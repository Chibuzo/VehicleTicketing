<?php
require_once "ticket.class.php";
require_once "fare.class.php";

class Vehicle extends Ticket {
	private $vehicle_no;

	function __construct() {
		parent::__construct();
	}


	public function addVehicleType($vehicle_name, $num_of_seat)
	{
		self::$db->query("SELECT id FROM vehicle_types WHERE name = :name", array('name' => $vehicle_name));
		if ($result = self::$db->fetch()) {
			self::$db->query("UPDATE vehicle_types SET removed = '0' WHERE name = :name", array('name' => $vehicle_name));
			return $result['id'];
		}

		$sql = "INSERT INTO vehicle_types (name, num_of_seats) VALUE (:name, :num_of_seat)";
		$param = array('name' => $vehicle_name, 'num_of_seat' => $num_of_seat);
		if (self::$db->query($sql, $param)) {
			return self::$db->getLastInsertId();
		}
	}


	public function getAllVehicleTypes()
	{
		self::$db->query("SELECT * FROM vehicle_types WHERE removed = '0' ORDER BY name");
		if ($vehicle_types = self::$db->fetchAll('obj')) {
			return $vehicle_types;
		} else {
			return false;
		}
	}


	public function getBoardingVehicles($travel_date, $route_id)
	{
		$sql = "SELECT bb.id, CONCAT(name, ' ', departure_order) vehicle_type FROM boarding_vehicle bb
				JOIN fares f ON bb.fare_id = f.id
				JOIN vehicle_types bt ON f.vehicle_type_id = bt.id
				WHERE travel_date = :travel_date AND route_id = :route_id ORDER BY departure_order";

		$param = array(
			'travel_date' => date('Y-m-d', strtotime($travel_date)),
			'route_id' => $route_id
		);

		self::$db->query($sql, $param);
		if ($result = self::$db->fetchAll('obj')) {
			return $result;
		} else {
			return false;
		}
	}


	public function updateVehicleType($vehicle_name, $num_of_seat, $id)
	{
		$sql = "UPDATE vehicle_types SET
					name = :name,
					num_of_seats = :num_of_seat
				WHERE id = :id";

		$param = array(
			'name' => $vehicle_name,
			'num_of_seat' => $num_of_seat,
			'id' => $id
		);
		if (self::$db->query($sql, $param)) {
			return true;
		}
	}


	public function removeVehicle($id)
	{
		$sql = "UPDATE vehicle_types SET removed = '1' WHERE id = :id";
		if (self::$db->query($sql, array('id' => $id))) {

			// remove from fares
			$sql = "DELETE FROM fares WHERE vehicle_type_id = :id";
			self::$db->query($sql, array('id' => $id));
			return true;
		}
	}
}

?>
