<?php
require_once("../classes/bus.class.php");
require_once ("../includes/helper_fns.php");

$bus = new Bus();

if ($_REQUEST['op'] == 'bus-auto-complete')
{
	$db = new Db('ticket');
	autoComplete($db);
}
elseif ($_REQUEST['op'] == 'verify-bus')
{
	if ($bus->verifyBus($_POST['bus_no']) === true) {
		echo "Found";
	}
}
elseif ($_REQUEST['op'] == 'book-bus')
{
	if (!$bus->checkBusBookStatus($_POST['travel_date'], $_POST['bus_no'])) {
		try {
			$bus->bookBus($_POST['bus_no'], $_POST['route_id'], $_POST['travel_date'], $_POST['departure_order']);
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	} else {
		echo "06"; // Bus already booked
	}
}
elseif ($_REQUEST['op'] == 'add-bus')
{
	if ($bus->addBus($_POST['bus_no'], $_POST['bus_type_id'], $_POST['driver_name'], $_POST['driver_phone'])) echo "Saved";
}
elseif ($_REQUEST['op'] == 'edit-bus')
{
	if ($bus->editBus($_POST['bus_id'], $_POST['bus_no'], $_POST['bus_type_id'], $_POST['driver_name'], $_POST['driver_phone'])) echo "Saved";
}
elseif ($_REQUEST['op'] == 'remove-bus')
{
	extract($_POST);
	//require_once "../classes/fare.class.php";
	//$fare = new Fare();
	//$fare_id = $fare->getFareByBusType($bus_type_id, $route_id);
	if ($bus->removeBookedBus($bb_id, $boarding_bus_id) === true) {
		echo "Done";
	}
}
elseif ($_REQUEST['op'] == 'get-bus-details')
{
	echo json_encode($bus->getBusDetails($_REQUEST['bus_id']));
}

function autoComplete($db) {
	$q = $_GET['q'];
	$sql = "SELECT bus_no FROM bus_info WHERE bus_no LIKE '%$q%'";
	foreach ($db->query($sql) AS $row) {
		echo $row['bus_no']."\n";
	}
}
?>
