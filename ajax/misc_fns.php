<?php
session_start();
require_once "../classes/route.class.php";
$route = new Route();

if (isset($_REQUEST['op'])) {
	if ($_REQUEST['op'] == 'get-destination')
	{
		$html = '';
		foreach($route->getDestination('Enugu') AS $dest) {
			$html .= "<option value='{$dest->destination}' data-route_id='{$dest->id}'>{$dest->destination}</option>";
		}
		echo $html;
	}
	elseif ($_REQUEST['op'] == 'delete-vehicle_type') {
		require_once "../classes/bus.class.php";
		$bus = new Bus();
		if ($bus->removeVehicle($REQUEST['id']) === true) {
			echo "Done";
		}
	}
	elseif ($_REQUEST['op'] == "remove-route") {
		require_once "../classes/route.class.php";
		$route = new Route();
		if ($route->removeRoute($REQUEST['id']) === true) {
			echo "Done";
		}
	}
	elseif ($_REQUEST['op'] == 'update-route')
	{
		require_once "../classes/route.class.php";
		$route = new Route();
		if ($route->editRoute($REQUEST['origin'], $REQUEST['destination'], $REQUEST['id']) === true) {
			echo "Done";
		}
	}
	elseif ($_REQUEST['op'] == 'update-bus')
	{
		require_once "../classes/bus.class.php";
		$bus = new Bus();
		extract($_POST);
		if ($bus->updateBusType($name, $num_of_seat, $id) === true) {
			echo "Done";
		}
	}
	elseif ($_REQUEST['op'] == "get-state-parks")
	{
		$id = $_POST['state_id'];
		require_once "../classes/parkmodel.class.php";
		$park_model = new ParkModel();

		$parks =  $park_model->getParksByState($id);

		echo json_encode($parks);
	}
	elseif ($_REQUEST['op'] == 'get-terminal-details')
	{
		require_once "../classes/ticket.class.php";
		$details = implode("-", explode(" ", Ticket::loadTravelDetails()));
		$park = implode("-", explode(" ", $_SESSION['park_name']));
		echo '{"abbr": "' . $_SESSION['abbr'] . '", "park": "' . $park . '"}';
	}
}

?>
