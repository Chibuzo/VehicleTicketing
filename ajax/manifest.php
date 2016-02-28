<?php
session_start();
require "../classes/vehiclemodel.class.php";
require "../classes/manifest.class.php";
require_once "../classes/booking.class.php";
require_once "../classes/customer.class.php";

$vehicle = new VehicleModel();
$manifest = new Manifest();

if (isset($_REQUEST['op'])) {
	if ($_REQUEST['op'] == 'get-boarding-vehicles')
	{
		$vehicles = $vehicle->getBoardingVehicles($_POST['travel_date'], $_POST['park_map_id'], $_SESSION['travel_id']);
		$opt = '';
		if (count($vehicles) > 0) {
			foreach ($vehicles AS $b) {
				$opt .= "<option value='$b->id'>{$b->vehicle_type}</option>";
			}
			echo $opt;
		}
	}
	elseif ($_REQUEST['op'] == 'get-manifest')
	{
		$manifest_details = $manifest->getManifest($_POST['boarding_vehicle_id']);

		$html = '';
		if (isset($manifest_details[0]->vehicle_no)) {
			$html .= "<blockquote><p style='font: 11px Verdana; color:#999; line-height:17px;'>
				<button type='button id='audit-btn' data-target='#auditModal' data-toggle='modal' class='btn btn-danger pull-right'><i class='fa fa-credit-card'></i> Audit</button>
				Route: {$manifest_details[0]->route}<br />
				Driver's name: {$manifest_details[0]->driver_name}<br />
				Driver's phone number: {$manifest_details[0]->drivers_phone}<br />
				vehicle number: {$manifest_details[0]->vehicle_no}<br />
				Date of travel: " . date('D d M Y', strtotime($manifest_details[0]->travel_date)) . "<br />
				{$manifest_details[0]->vehicle_type} {$manifest_details[0]->num_of_seats} seater
			</blockquote></p>";
		}

		$html .= "<table class='table table-striped table-bordered' style='padding:0px'>
			<thead>
				<tr>
					<th style='width:45px'>Date</th>
					<th>Name</th>
					<th>Phone no</th>
					<th>Next of Kin</th>
					<th class='text-right'>Seat</th>
					<th>Ticket</th>
					<th class='text-right'>Fare (N)</th>
					<th style='text-align:center' colspan='3'>Action</th>
					<th>Sold by</th>
				</tr>
			</thead>
			<tbody id='tbl-ticket'>";

			foreach ($manifest_details AS $bk) {
				$html .= "<tr id='{$bk->customer_id}'>
							<td>" . date('d/m/Y', strtotime($bk->date_booked)) . "</td>
							<td>{$bk->c_name}</td>
							<td>{$bk->phone_no}</td>
							<td>{$bk->next_of_kin_phone}</td>
							<td class='text-right'>{$bk->seat_no}</td>
							<td>{$bk->ticket_no}</td>
							<td class='text-right'>" . number_format($bk->fare) . "</td>
							<td class='text-center'><a href='' id='{$bk->bd_id}' class='print-ticket' title='Print ticket' data-toggle='tooltip'><span class='glyphicon glyphicon-print'></span></a></td>
							<td class='text-center'><a href='' data-toggle='modal' data-target='#customerModal' id='{$bk->bd_id}' class='edit-ticket'><span class='glyphicon glyphicon-pencil'></span></a></td>
							<td class='text-center'><a href='' id='{$bk->bd_id}' class='cancel-ticket red'><span class='glyphicon glyphicon-remove'></span></a></td>
							<td>{$bk->username}</td>
						</tr>";
			}
			echo $html;
	}
	elseif ($_REQUEST['op'] == 'get-audit')
	{
		echo $manifest->getAudit($_POST['boarding_vehicle_id']);
	}
	elseif ($_REQUEST['op'] == 'print_manifest')
	{
		$manifest->generatePrintManifest($_GET['boarding_vehicle_id']);
	}
	elseif ($_REQUEST['op'] == 'balance-sheet')
	{
		$manifest->balanceSheet($_POST['boarding_vehicle_id'], $_POST['expenses'], $_POST['load_fare']);
	}
	elseif ($_REQUEST['op'] == 'cancel-ticket')
	{
		$booking = new Booking();
		if ($booking->cancelBooking($_POST['ticket_id']) === true) {
			echo "Done";
		} else {
			//echo "Ta";
		}
	}
	elseif ($_REQUEST['op'] == 'update_customer_info')
	{
		extract($_POST);
		$customer = new Customer();
		if ($customer->update($c_name, $c_phone, $next_of_kin, $customer_id) === true) {
			echo "Done";
		}
	}
	elseif ($_REQUEST['op'] == 'reopen-vehicle')
	{
		echo $manifest->reopenvehicle($_POST['boarding_vehicle_id']);
	}
}

?>
