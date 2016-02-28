<?php
require_once "ticket.class.php";

class Route extends Ticket {

	function __construct() {
		parent::__construct();
	}


	public function addRoute($origin, $destination)
	{
		$route_map = $origin . ' - ' . $destination;
		$sql = "INSERT INTO routes (origin, destination, route) VALUES (:origin, :destination, :route)";
		$param = array(
			'origin' =>$origin,
			'destination' => $destination,
			'route' => $route_map
		);
		if ($this->db->query($sql, $param)) {
			return true;
		}
	}


	function getRoutes()
	{
		$this->db->query("SELECT id, name AS route_name FROM states_towns");
		return $this->db->fetchAll();
	}


	public function getAllRoutes()
	{
		//return parent::getAll('routes', 'origin');
		$this->db->query("SELECT * FROM routes WHERE status = '1' ORDER BY origin");
		return $this->db->fetchAll('obj');
	}


	public function getDestination($origin)
	{
		//return parent::getManyById('routes', 'origin', $origin, 'destination');
		$sql = "SELECT * FROM routes WHERE origin = :origin AND status = '1'";
		$this->db->query($sql, array('origin' => $origin));
		return $this->db->fetchAll('obj');
	}


	function getRouteId($origin, $destination)
	{
		$sql = "SELECT id FROM routes WHERE origin = :origin AND destination = :destination";
		$param = array('origin' => $origin, 'destination' => $destination);
		self::$db->query($sql, $param);
		if ($result = self::$db->fetch('obj')) {
			return $result->id;
		} else {
			return false;
		}
	}


	public function getAllRouteIds()
	{
		$this->db->query("SELECT id FROM routes");
		return $this->db->stmt;
	}


	public function editRoute($origin, $destination, $id)
	{
		$route_map = $origin . ' - ' . $destination;
		$sql = "UPDATE routes SET
					origin = :origin,
					destination = :destination,
					route = :route
				WHERE id = :id";

		$param = array(
			'origin' => $origin,
			'destination' => $destination,
			'route' => $route_map,
			'id' => $id
		);
		if ($this->db->query($sql, $param)) {
			return true;
		}
	}


	/*function getRouteMap($route_id)
	{
		$sql = "SELECT route FROM routes WHERE id = :id";
		$param = array('id' => $route_id);
		$this->db->query($sql, $param);
		if ($result = $this->db->fetch()) {
			return $result;
		} else {
			return false;
		}
	}*/


	public function removeRoute($id)
	{
		$sql = "UPDATE routes SET status = '0' WHERE id = :id";
		if ($this->db->query($sql, array('id' => $id))) {
			$this->db->query("DELETE FROM fares WHERE route_id = :route_id", array('route_id' => $id));
			return true;
		}
	}
}

?>
