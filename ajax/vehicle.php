<?php
session_start();
require_once("../classes/vehiclemodel.class.php");
//require_once ("../includes/db_handle.php");

$vehicle = new VehicleModel();

if ($_REQUEST['op'] == 'verify-vehicle')
{
	if ($vehicle->verifyvehicle($_POST['vehicle_no']) === true) {
		echo "Found";
	}
}
elseif ($_REQUEST['op'] == 'book-vehicle')
{
	if (!$vehicle->checkvehicleBookStatus($_POST['travel_date'], $_POST['vehicle_no'], $_SESSION['travel_id'])) {
		try {
			$status = $vehicle->bookvehicle($_POST['vehicle_no'], $_POST['route_id'], $_POST['travel_date'], $_POST['departure_order'], $_SESSION['travel_id']);
			if ($status === true) {
				echo "Done";
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	} else {
		echo "06"; // vehicle already booked
	}
}
elseif ($_REQUEST['op'] == 'add-vehicle')
{
	if ($vehicle->addvehicle($_POST['vehicle_no'], $_POST['vehicle_type_id'], $_POST['driver_name'], $_POST['driver_phone'], $_SESSION['travel_id'])) echo "Saved";
}
elseif ($_REQUEST['op'] == 'edit-vehicle')
{
	if ($vehicle->editvehicle($_POST['vehicle_id'], $_POST['vehicle_no'], $_POST['vehicle_type_id'], $_POST['driver_name'], $_POST['driver_phone'], $_POST['bbv_id'])) echo "Saved";
}
elseif ($_REQUEST['op'] == 'remove-vehicle')
{
	extract($_POST);
	if ($vehicle->removeBookedvehicle($bbv_id, $boarding_vehicle_id) === true) {
		echo "Done";
	}
}
elseif ($_REQUEST['op'] == 'get-vehicle-details')
{
	echo json_encode($vehicle->getvehicleDetails($_REQUEST['vehicle_id']));
}
?>
