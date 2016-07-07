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
		$vehicles = $vehicle->getBoardingVehicles($_POST['travel_date'], $_POST['park_map_id']);
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
				Route: {$_SESSION['state_name']} - {$manifest_details[0]->destination}<br />
				Driver's name: {$manifest_details[0]->driver_name}<br />
				Driver's phone number: {$manifest_details[0]->drivers_phone}<br />
				vehicle number: {$manifest_details[0]->vehicle_no}<br />
				Date of travel: " . date('D d M Y', strtotime($manifest_details[0]->travel_date)) . "<br />
				{$manifest_details[0]->vehicle_type} {$manifest_details[0]->num_of_seats} seater
			</blockquote></p>";
		}

		$html .= "<table class='table tablestriped table-bordered' style='padding:0px'>
			<thead>
				<tr>
					<th style='width:100px'>Date Booked</th>
					<th>Name</th>
					<th>Phone no</th>
					<th>Next of Kin</th>
					<th class='text-right'>Seat</th>
					<th>Ticket</th>
					<th class='text-right'>Fare (₦)</th>
					<th>Sold by</th>
					<th style='text-align:center' colspan='3'>Action</th>
				</tr>
			</thead>
			<tbody id='tbl-ticket'>";

			foreach ($manifest_details AS $bk) {
				$bg = '';
				//if ($bk->offline == '0') $bg = 'warning';
				$html .= "<tr id='{$bk->customer_id}' class='$bg'>
							<td>" . date('d/m/Y', strtotime($bk->date_booked)) . "</td>
							<td>{$bk->c_name}</td>
							<td>{$bk->phone_no}</td>
							<td>{$bk->next_of_kin_phone}</td>
							<td class='text-right'>{$bk->seat_no}</td>
							<td>{$bk->ticket_no}</td>
							<td class='text-right'>" . number_format($bk->fare) . "</td>
							<td>{$bk->username}</td>
							<td class='text-center'><a href='' id='{$bk->bd_id}' class='print-ticket' title='Print ticket' data-toggle='tooltip'><span class='glyphicon glyphicon-print'></span></a></td>
							<td class='text-center'><a href='' data-toggle='modal' data-target='#customerModal' id='{$bk->bd_id}' class='edit-ticket'><span class='glyphicon glyphicon-pencil'></span></a></td>
							<td class='text-center'><a href='' id='{$bk->bd_id}' class='cancel-ticket red'><span class='glyphicon glyphicon-remove'></span></a></td>
							<!--<td>{$_SESSION['username']}</td>-->
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
		$manifest->balanceSheet($_POST['boarding_vehicle_id'], $_POST['feeding'], $_POST['fuel'], $_POST['scouters'], $_POST['expenses'], $_POST['load']);
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
	elseif ($_REQUEST['op'] == 'print-waybill') {
		$details = $manifest->getWayBillDetails($_GET['boarding_vehicle_id']);
		printWaybill($details);
	}
}


function printWaybill($details) {
	echo "<div class='head'>{$_SESSION['travel']}<br />
		<span>DRIVER'S WAYBILL</span></div><br><br>";

	$html = "<table style='bordercollapse:collapse; width:100%; text-align: left;' border='0'>
			<tr><td width='100'>Depot</td><td class='uline' width='200'>{$_SESSION['park_name']}</td><td width='100'>Date</td><td class='uline' wdth='220'>" . date('d/m/Y') . "</td></tr>
			<tr><td>Route</td><td class='uline'>Nsk</td><td>Fare</td><td class='uline'>₦" . number_format($details->fare) . "</td></tr>
			<tr><td>Fleet/Reg No</td><td colspan='3' class='uline'>PMT $details->vehicle_no</td></tr>
			<tr><td>Driver's name</td><td colspan='3' class='uline'>$details->driver_name</td></tr>
			<tr><td>Passengers</td><td class='uline'>$details->seats</td><td>Scouters charges</td><td class='uline'>₦" . number_format($details->scouters_charge) . "</td></tr>
			<tr><td>Fuel</td><td class='uline'>₦" . number_format($details->fuel) . "</td><td>Feeding</td><td class='uline'>₦" . number_format($details->drivers_feeding) . "</td></tr>
			<tr><td>Net/Income</td><td class='uline'>₦" . number_format($details->income) . "</td><td>Time</td><td class='uline'>$details->departure_time</td></tr>
			<tr><td>Remarks</td><td colspan='3' class='uline'>{$_SESSION['username']}</td></tr>";

	echo $html;
}

?>
