<?php
require_once "ticket.class.php";

class Manifest extends Ticket {

	public function __construct()
	{
		parent::__construct();
	}


	public function getManifest($boarding_vehicle_id, $limit = null)
	{
		if ($limit != null) {
			$limit = "LIMIT 0, 5";
		}
		$sql = "SELECT bd.id bd_id, ticket_no, date_booked, c_name, next_of_kin_phone, phone_no, customer_id, seat_no, bv.fare, bv.travel_date, name vehicle_type, num_of_seats, route, username, vi.*
				FROM booking_details bd
				JOIN boarding_vehicle bv ON bd.boarding_vehicle_id = bv.id
				JOIN trips tr ON bv.trip_id = tr.id
				JOIN routes r ON tr.route_id = r.id
				JOIN vehicle_types vt ON tr.vehicle_type_id = vt.id
				LEFT JOIN booked_vehicles bbv ON bv.booked_vehicle_id = bbv.id
				LEFT JOIN vehicle_info vi ON bv.booked_vehicle_id = vi.id
				JOIN customers c ON bd.customer_id = c.id
				LEFT JOIN users u ON bd.user_id = u.id
				WHERE bd.status = '1' AND bd.boarding_vehicle_id = :boarding_vehicle_id
				ORDER BY travel_date DESC
				{$limit}";

		self::$db->query($sql, array('boarding_vehicle_id' => $boarding_vehicle_id));
		return self::$db->fetchAll('obj');
	}


	public function getAudit($boarding_vehicle_id)
	{
		$sql = "SELECT m.*, fare, booked_seats FROM manifest_audit m
				JOIN boarding_vehicle bv ON m.boarding_vehicle_id = bv.id
				WHERE boarding_vehicle_id = :boarding_vehicle_id";

		self::$db->query($sql, array('boarding_vehicle_id' => $boarding_vehicle_id));
		if ($audit = self::$db->fetch('obj')) {
			$seats = count(explode(",", $audit->booked_seats));
			$income = $seats * $audit->fare;

			echo "<div class='audit_pane'><div><b>Balance Sheet</b></div><hr id='line' style='margin:8px 0px' />

					Tickets sold: $seats<br />
					Transport income: ₦" . number_format($income) . "<br />
					Load: ₦" . number_format($audit->load_cost) . "<br />
					Expenses/Driver: ₦" . number_format($audit->drivers_expenses) . "<br />
					<!--Service charge: ₦<hr style='margin:8px 0px' />-->
					Balance: ₦" . number_format(($income  + (int)$audit->load_cost) - (int)$audit->drivers_expenses) . "</div>
					<div class='auditpane' style='border:0px'><br />
						<button id='reopen' class='btn btn-default btn-large btn-block' data-boarding_vehicle_id='$boarding_vehicle_id'>Reopen this vehicle</button>
					</div>";
		} else {
			echo "<div class='audit_pane'>No details found</div>";
		}
	}


	function generatePrintManifest($boarding_vehicle_id) {

		$details = $this->getManifest($boarding_vehicle_id);

		# Get manifest Serial number
		//$serial_no = '';
		//$result = $DB_CONNECTION->query("SELECT serial_no FROM manifest_serial_no WHERE booked_id = '{$_GET['booked_id']}'");
		//if ($result->num_rows > 0)
		//	$serial_no = $result->fetch_object()->serial_no;
		$serial_no = '';

		echo "<div class='head'>{$_SESSION['travel']}<br />
		<span>Tel: {$_SESSION['phone']}</span><span style='float:right;font-size:16px; display:none'>E - {$serial_no} &nbsp;</span></div><br />";

		$html = '';
		if (isset($details[0]->vehicle_no)) {
			$html .= "<p>
				Route: {$details[0]->route}<br />
				Driver's name: {$details[0]->driver_name}<br />
				Driver's phone number: {$details[0]->drivers_phone}<br />
				vehicle number: {$details[0]->vehicle_no}<br />
				Date of travel: " . date('D d M Y', strtotime($details[0]->travel_date)) . "<br />
			</p>";
		}

		$html .= "<table cellpadding='10' cellspacing='10' style='border-collapse:collapse; width:100%; float:left; font-size:12px; text-align: left' border='1'>
				<thead>
					<tr>
						<th>S/NO</th>
						<th>Name</th>
						<th>Phone</th>
						<th>Next of Kin no</th>
						<th>Seat</th>
						<th>Ticket No</th>
						<th>Fare (N)</th>
					</tr>
				</thead>
				<tbody>";

		$n = 0;
		foreach ($details AS $bk) {
			$n++;
			$html .= "<tr>
					<td>$n</td>
					<td>{$bk->c_name}</td>
					<td>{$bk->phone_no}</td>
					<td>{$bk->next_of_kin_phone}</td>
					<td class='text-right'>{$bk->seat_no}</td>
					<td>{$bk->ticket_no}</td>
					<td class='text-right'>" . number_format($bk->fare) . "</td>
				</tr>";
		}
		$html .= "<tbody></table>";
		// Get manifest's balance sheet
		$this->getAudit($boarding_vehicle_id);

		$html .= "<div id='signature'><span><hr />Driver's Signature</span><span style='float:right'><hr />Manager's Signature</span></div>";
		echo $html;
	}


	public function balanceSheet($boarding_vehicle_id, $expenses, $load)
	{
		self::$db->query("SELECT boarding_vehicle_id FROM manifest_audit WHERE boarding_vehicle_id = :boarding_vehicle_id", array('boarding_vehicle_id' => $boarding_vehicle_id));

		$param = array(
			'load' => $load,
			'expenses' => $expenses,
			'boarding_vehicle_id' => $boarding_vehicle_id
		);

		if ($result = self::$db->fetch()) {
			$sql = "UPDATE manifest_audit
					SET    load_cost = :load, drivers_expenses = :expenses
					WHERE  boarding_vehicle_id = :boarding_vehicle_id";

			self::$db->query($sql, $param); // ? null : $query_check = false;
		} else {
			$sql = "INSERT INTO manifest_audit (load_cost, drivers_expenses, boarding_vehicle_id)
					VALUES (:load, :expenses, :boarding_vehicle_id)";
			self::$db->query($sql, $param);
		}

		# Mark the closed vehicle as full
		self::$db->query("UPDATE boarding_vehicle SET seat_status = 'Full' WHERE id = :id",  array('id' => $boarding_vehicle_id));
	}


	public function reopenvehicle($boarding_vehicle_id)
	{
		$sql = "SELECT booked_seats, num_of_seats FROM boarding_vehicle bv
				JOIN trips tr ON bv.trip_id = tr.id
				JOIN vehicle_types vt ON tr.vehicle_type_id = vt.id
				WHERE bv.id = :boarding_vehicle_id";

		self::$db->query($sql, array('boarding_vehicle_id' => $boarding_vehicle_id));
		if ($data = self::$db->fetch('obj')) {
			$num_of_seats = count(explode(",", $data->booked_seats));
			if ($num_of_seats == $data->num_of_seats) {
				return "This vehicle is full, you cannot reopen it";
			} else {
				$query_check = true;
				self::$db->beginDbTransaction();
				$sql = "UPDATE boarding_vehicle SET seat_status = 'Not full' WHERE id = :id";
				self::$db->query($sql, array('id' => $boarding_vehicle_id)) ? null : $query_check = false;
				self::$db->query("DELETE FROM manifest_audit WHERE boarding_vehicle_id = :boarding_vehicle_id", array('boarding_vehicle_id' => $boarding_vehicle_id)) ? null : $query_check = false;

				# If this vehicle was merged, then mark it as not full in the merge table
				//$sql = "UPDATE merged_routes SET seat_status = 'Not full' WHERE going_booked_id = '{$_POST['booked_id']}'";
				//$DB_CONNECTION->query($sql) ? null : $query_check = false;

				# Remove manifest seria number
				#$sql = "DELETE FROM manifest_serial_no WHERE booked_id = '{$_POST['booked_id']}'";
				#$DB_CONNECTION->query($sql) ? null : $query_check = false;

				if ($query_check == true) {
					self::$db->commitTransaction();
					return "Done";
				} else {
					self::$db->rollBackTransaction();
					return "Something went wrong";
				}
			}
		}
	}
}

?>
