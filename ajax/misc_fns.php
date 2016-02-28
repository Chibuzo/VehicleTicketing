<?php
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
	elseif ($_POST['op'] == 'delete-vehicle_type') {
		require_once "../classes/bus.class.php";
		$bus = new Bus();
		if ($bus->removeVehicle($_POST['id']) === true) {
			echo "Done";
		}
	}
	elseif ($_POST['op'] == "remove-route") {
		require_once "../classes/route.class.php";
		$route = new Route();
		if ($route->removeRoute($_POST['id']) === true) {
			echo "Done";
		}
	}
	elseif ($_POST['op'] == 'update-route')
	{
		require_once "../classes/route.class.php";
		$route = new Route();
		if ($route->editRoute($_POST['origin'], $_POST['destination'], $_POST['id']) === true) {
			echo "Done";
		}
	}
	elseif ($_POST['op'] == 'update-bus')
	{
		require_once "../classes/bus.class.php";
		$bus = new Bus();
		extract($_POST);
		if ($bus->updateBusType($name, $num_of_seat, $id) === true) {
			echo "Done";
		}
	}
}

?>
