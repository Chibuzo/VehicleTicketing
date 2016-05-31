<?php
require_once "ticket.class.php";

class Booking extends Ticket {
	private static $db_tbl = 'booking_details';

	function __construct()
	{
		parent::__construct();
	}


	function book($boarding_vehicle_id, $seat_no, $customer_id)
	{
		$ticket_no = $this->generateRefNo();

		$sql = "INSERT INTO " . self::$db_tbl . "
		(ticket_no, payment_status, channel, boarding_vehicle_id, seat_no, customer_id)
		VALUES
		('$ticket_no', 'Paid', 'offline', :boarding_vehicle_id, :seat_no, '$customer_id')";

		$param = array(
				'boarding_vehicle_id' => $boarding_vehicle_id,
				'seat_no' => $seat_no
		);

		if (self::$db->query($sql, $param)) {
			return self::$db->getLastInsertId();
		} else {
			return "03"; // Booking wasn't successful
		}
	}


	public function getTicketDetails($id)
	{
		$sql = "SELECT ticket_no, c_name, next_of_kin_phone, tr.fare, seat_no, vehicle_no, bv.travel_date, destination, bv.departure_order, vt.vehicle_name vehicle_type
				FROM " . self::$db_tbl . " bd
				JOIN boarding_vehicle bv ON bd.boarding_vehicle_id = bv.id
				LEFT JOIN booked_vehicles bbv ON bv.booked_vehicle_id = bbv.id
				JOIN trips tr ON bv.trip_id = tr.trip_id
				JOIN destination d ON bv.park_map_id = d.park_map_id
				JOIN customers c ON bd.customer_id = c.id
				JOIN vehicle_types vt ON tr.vehicle_type_id = vt.id
				WHERE bd.id = :id";

		self::$db->query($sql, array('id' => $id));
		if ($ticket = self::$db->fetch()) {
			return $ticket;
		}
	}


	function cancelBooking($ticket_id)
	{
		$sql = "SELECT bv.booked_seats, bv.id bb_id, seat_no FROM " . self::$db_tbl . " bd
				JOIN boarding_vehicle bv ON bd.boarding_vehicle_id = bv.id
				WHERE bd.id = :ticket_id";

		self::$db->query($sql, array('ticket_id' => $ticket_id));
		if ($details = self::$db->fetch('obj')) {
			$seats = explode(",", $details->booked_seats);
			foreach ($seats AS $key => $value) if ($seats[$key] == $details->seat_no) unset($seats[$key]);
			$remaining_seats = implode(',', $seats);

			// lets do some transactions
			$query_check = true;
			self::$db->beginDbTransaction();
			$sql = "UPDATE boarding_vehicle SET booked_seats = '$remaining_seats', seat_status = 'Not full'
					WHERE id = '$details->bb_id'";

			self::$db->query($sql) ? null : $query_check = false;

			self::$db->query("DELETE FROM booking_details WHERE id = :id", array('id' => $ticket_id)) ? null : $query_check = false;

			if ($query_check == true) {
				self::$db->commitTransaction();
				return true;
			} else {
				self::$db->rollBackTransaction();
				return false;
			}
		} else {
			echo "Not Found";
		}
	}


	function generateRefNo()
	{
		$find    = array('/', '+', '=', "\\", '|', '-');
		$replace = array('X', 'Y', 'Z', 'U', 'O', '4');
		return strtoupper(str_replace($find, $replace, base64_encode(mcrypt_create_iv(6, MCRYPT_RAND))));
	}


	function countUserTickets($customer_id) {
		$sql = "SELECT COUNT(*) AS num_row FROM {self::$db_tbl} WHERE customer_id = :customer_id AND payment_status = 'Paid'";
		self::$db->query($sql, array('customer_id' => $customer_id));
		$count = self::$db->fetch('obj');
		return $count->num_rows;
	}
}

?>
