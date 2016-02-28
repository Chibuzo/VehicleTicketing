<?php
require_once "ticket.class.php";

class Fare extends Ticket
{
	private static $db_tbl = "fares";

	public function __construct()
	{
		parent::__construct();
	}


	private function addFare($form)
	{
		$sql = "INSERT INTO " . self::$db_tbl . "
				(bus_type_id, fare, route_id) VALUE (:bus_type_id, :fare, :route_id)";

		self::$db->prepare($sql);

		foreach ($form AS $key => $val) {
			if (is_numeric($key)) {
				$param = array(
					'bus_type_id' => $key,
					'fare' => $val,
					'route_id' => $form['route_id']
				);
				self::$db->execute($param);
			}
		}
	}


	public function addSingleFare($route_id, $bus_type_id, $fare)
	{
		$sql = "INSERT INTO " . self::$db_tbl . "
				(bus_type_id, fare, route_id) VALUE (:bus_type_id, :fare, :route_id)";

		$param = array(
			'bus_type_id' => $bus_type_id,
			'fare' => $fare,
			'route_id' => $route_id
		);
		if (self::$db->query($sql, $param)) {
			return true;
		}
	}


	public function editFare($form)
	{
		$sql = "UPDATE " . self::$db_tbl . " SET
					fare = :fare
				WHERE bus_type_id = :bus_type_id AND route_id = :route_id";

		$bus_types = $this->getAllBusTypesId($form['route_id']);

		self::$db->prepare($sql);

		$new_vehicle_types = array();
		foreach ($form AS $key => $val) {
			if (is_numeric($key) && in_array($key, $bus_types)) { // update existing vehicle type
				$param = array(
					'fare' => $val,
					'bus_type_id' => $key,
					'route_id' => $form['route_id']
				);
				self::$db->execute($param);
			} else { // add fare for new vehicle type
				if (is_numeric($key)) {
					$new_vehicle_types['bus_type_id'][] = $key;
					$new_vehicle_types['fare'][] = $val;
				}
			}
		}

		$sql = "INSERT INTO " . self::$db_tbl . "
				(bus_type_id, fare, route_id) VALUE (:bus_type_id, :fare, :route_id)";
		self::$db->prepare($sql);

		for ($i = 0; $i < count($new_vehicle_types['fare']); $i++) {
			$param = array(
				'fare' => $new_vehicle_types['fare'][$i],
				'bus_type_id' => $new_vehicle_types['bus_type_id'][$i],
				'route_id' => $form['route_id']
			);
			self::$db->execute($param);
		}
	}


	private function checkRouteFares($route_id)
	{
		$sql = "SELECT COUNT(*) num FROM " . self::$db_tbl . " WHERE route_id = :route_id";
		self::$db->query($sql, array('route_id' => $route_id));
		if ($result = self::$db->fetch('obj')) {
			return $result->num;
		} else {
			return 0;
		}
	}


	protected function getAllBusTypesId($route_id)
	{
		$sql = "SELECT bus_type_id FROM " . self::$db_tbl . " WHERE route_id = :route_id";
		self::$db->query($sql, array('route_id' => $route_id));
		$bus_types = array();
		foreach (self::$db->stmt AS $row) {
			$bus_types[] = $row['bus_type_id'];
		}
		return $bus_types;
	}


	public function getAllFares()
	{
		return parent::getAll(self::$db_tbl);
	}


	public function getFareById($id)
	{
		return parent::getOneById(self::$db_tbl, $id);
	}


	public function getFareByRouteId($route_id)
	{
		$sql = "SELECT f.id, bt.id bus_type_id, fare, name AS bus_type FROM " . self::$db_tbl . " f
				JOIN bus_types bt ON f.bus_type_id = bt.id
				WHERE route_id = :route_id
				ORDER BY name";

		$param = array('route_id' => $route_id);

		if (self::$db->query($sql, $param)) {
			return self::$db->fetchAll('obj');
		}
	}


	public function getFareByBusType($bus_type_id, $route_id)
	{
		$sql = "SELECT fare, id FROM " . self::$db_tbl . "
				WHERE bus_type_id = :bus_type_id AND route_id = :route_id";

		$param = array(
			'bus_type_id' => $bus_type_id,
			'route_id' => $route_id
		);

		self::$db->query($sql, $param);
		if ($result = self::$db->fetch('obj')) {
			return $result;
		} else {
			return false;
		}
	}


	private function deleteAllRouteFares($route_id)
	{
		self::$db->query("DELETE FROM " . self::$db_tbl . " WHERE route_id = :route_id", array('route_id' => $route_id));
	}
}
?>
