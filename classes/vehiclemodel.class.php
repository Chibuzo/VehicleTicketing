<?php
require_once "ticket.class.php";
require_once "trip.class.php";

class VehicleModel extends Ticket {
	private $trip = null;

	function __construct()
	{
		parent::__construct();

		$this->trip = new Trip();
	}


	function findVehicles($route_id)
	{
		return $this->trip->getTripsByRoute($route_id);
	}


	public function addVehicleType($vehicle_name, $vehicle_type, $num_of_seat, $vehicle_type_id)
	{
		$sql = "INSERT INTO vehicle_types (vehicle_name, vehicle_type, vehicle_type_id, num_of_seats) VALUES (:vehicle_name, :vehicle_type, :vehicle_type_id, :num_of_seat)";
		$param = array(
			'vehicle_name' => $vehicle_name,
			'vehicle_type' => $vehicle_type,
			'vehicle_type_id' => $vehicle_type_id,
			'num_of_seat' => $num_of_seat
		);
		if (self::$db->query($sql, $param)) {
			return true;
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


	public function findBoardingVehicle($park_map_id, $vehicle_type_id, $departure_order = 0, $travel_date)
	{
		$query = "";
		if ($departure_order > 0) {
			$query = "AND departure_order = '$departure_order'";
		}
		$sql = "SELECT id, booked_seats, fare, trip_id, seat_status, departure_order FROM boarding_vehicle
				WHERE park_map_id = :park_map_id AND vehicle_type_id = :vehicle_type_id
				AND travel_date = :travel_date AND seat_status = 'Not full' $query
				ORDER BY departure_order ASC LIMIT 0, 1";

		$param = array(
			'park_map_id' => $park_map_id,
			'vehicle_type_id' => $vehicle_type_id,
			'travel_date' => $travel_date
		);

		self::$db->query($sql, $param);
		if ($vehicle = self::$db->fetch('obj')) {
			return $vehicle;
		} else {
			return false;
		}
	}


	public function fixBoardingVehicles($vehicle_type_id, $park_map_id, $travel_date, $departure_order)
	{
		$bv = $this->trip->getTripDetails($vehicle_type_id, $park_map_id, $departure_order);

		if ($bv == false) {
			return false; // vehicle type wasn't setup for the selected route
		}

		$sql = "INSERT INTO boarding_vehicle (trip_id, park_map_id, vehicle_type_id, fare, departure_order, travel_date)
				VALUES (:trip_id, :park_map_id, :vehicle_type_id, :fare, :departure_order, :travel_date)";

		self::$db->prepare($sql);
		$stmt = self::$db->stmt;

		$boarding_vehicle = $this->findBoardingVehicle($park_map_id, $vehicle_type_id, $departure_order, $travel_date);
		//die(var_dump($boarding_vehicle));
		if ($boarding_vehicle != false) {
			$count = $boarding_vehicle->departure_order + 1;
		} elseif ($departure_order > 0) {
			$count = $departure_order;
		} else {
			$count = 1;
		}
		for ($i = 1; $i <= $count; $i++) {
			if ($this->findBoardingVehicle($park_map_id, $vehicle_type_id, $i, $travel_date) != false) continue;
			$param = array(
				'trip_id' => $bv->trip_id,
				'park_map_id' => $park_map_id,
				'vehicle_type_id' => $vehicle_type_id,
				'fare' => $bv->fare,
				'departure_order' => $i,
				'travel_date' => $travel_date
			);
			$stmt->execute($param);
		}
		$bv->boarding_vehicle_id = self::$db->getLastInsertId();
		return $bv;
	}


	/**
	 * for manifest page
	 * @param $travel_date
	 * @param $park_map_id
	 * @return bool
	 */
	public function getBoardingVehicles($travel_date, $park_map_id)
	{
		$sql = "SELECT bv.id, CONCAT(vehicle_name, ' ', departure_order) vehicle_type FROM boarding_vehicle bv
				LEFT JOIN vehicle_types vt ON bv.vehicle_type_id = vt.id
				WHERE travel_date = :travel_date AND bv.park_map_id = :park_map_id
				ORDER BY departure_order";

		$param = array(
				'travel_date' => date('Y-m-d', strtotime($travel_date)),
				'park_map_id' => $park_map_id
		);

		self::$db->query($sql, $param);
		if ($result = self::$db->fetchAll('obj')) {
			return $result;
		} else {
			return false;
		}
	}


	public function getBookedVehicles($travel_date)
	{
		$sql = "SELECT bbv.*, bbv.id bbv_id, seat_status, vehicle_name vehicle_type, destination, vi.*, vi.id vehicle_id, vi.id vehicle_id, bv.id boarding_vehicle_id
				FROM booked_vehicles bbv
				JOIN boarding_vehicle bv ON bbv.id = bv.booked_vehicle_id AND bbv.park_map_id = bv.park_map_id
				JOIN trips tr ON bbv.park_map_id = tr.park_map_id AND bbv.vehicle_type_id = tr.vehicle_type_id AND bv.trip_id = tr.trip_id
				JOIN vehicle_types vt ON bbv.vehicle_type_id = vt.id
				JOIN vehicle_info vi ON bbv.vehicle_no = vi.vehicle_no
				JOIN destination d ON tr.park_map_id = d.park_map_id
				WHERE bbv.travel_date = :travel_date";

		self::$db->query($sql, array('travel_date' => date('Y-m-d', strtotime($travel_date))));
		return self::$db->fetchAll();
	}


	function checkVehicleBookStatus($travel_date, $_vehicle_no)
	{
		$travel_date = date('Y-m-d', strtotime($travel_date));

		$sql = "SELECT bbv.id FROM booked_vehicles bbv
				WHERE bbv.travel_date = :travel_date AND bbv.vehicle_no = :vehicle_no";

		$param = array(
			'travel_date' => $travel_date,
			'vehicle_no' => $_vehicle_no
		);
		self::$db->query($sql, $param);
		if ($result = self::$db->fetch()) return true;
	}


	function bookVehicle($_vehicle_no, $park_map_id, $travel_date, $departure_order)
	{
		$travel_date = date('Y-m-d', strtotime($travel_date));

		/*** Get the vehicle details ***/
		$sql = "SELECT vt.id vehicle_type_id, num_of_seats, vehicle_name vehicle_type, vi.id vi_id FROM vehicle_info vi
				JOIN vehicle_types vt ON vi.vehicle_type_id = vt.id
				WHERE vehicle_no = :vehicle_no";

		self::$db->query($sql, array('vehicle_no' => $_vehicle_no));
		$vehicle = self::$db->fetch('obj');
		if ($vehicle == false) {
			throw new Exception ("vehicle details wasn't found.");
		}
		# Get trip details
		$trip = $this->trip->getTripDetails($vehicle->vehicle_type_id, $park_map_id, $departure_order);

		if ($trip == false) {
			throw new Exception("The selected vehicle is not setup for this route. Please contact the park manager.");
		}

		// Determine vehicle departure order
		if (is_numeric($departure_order) && $departure_order > 0) {
			$vehicle_order = $departure_order;
		} else {
			// Get the position of the boarding vehicle, if any
			$sql = "SELECT departure_order FROM booked_vehicles bbv
					JOIN trips tr ON bbv.park_map_id = tr.park_map_id AND bbv.vehicle_type_id = tr.vehicle_type_id
					WHERE travel_date = :travel_date AND bbv.park_map_id = :park_map_id AND bbv.vehicle_type_id = :vehicle_type_id
					ORDER BY departure_order DESC LIMIT 0, 1";

			$param = array(
				'travel_date' => $travel_date,
				'park_map_id' => $park_map_id,
				'vehicle_type_id' => $vehicle->vehicle_type_id
			);

			self::$db->query($sql, $param);
			if ($boarding_vehicle = self::$db->fetch('obj')) {
				$vehicle_order = $boarding_vehicle->departure_order + 1;
			} else {
				$vehicle_order = 1;
			}
		}

		/*** Book the vehicle ***/
		$sql = "INSERT INTO booked_vehicles (vehicle_type_id, vehicle_info_id, vehicle_no, park_map_id, departure_order, travel_date) VALUES
				('$vehicle->vehicle_type_id', '$vehicle->vi_id', :vehicle_no, :park_map_id, :vehicle_order, :travel_date)";

		$param = array(
				'vehicle_no' => $_vehicle_no,
				'park_map_id' => $park_map_id,
				'vehicle_order' => $vehicle_order,
				'travel_date' => date('Y-m-d', strtotime($travel_date))
		);
		if (!self::$db->query($sql, $param)) {
			throw new Exception ("Error booking vehicle");
		}
		$bbv_id = self::$db->getLastInsertId();

		// find matching boarding vehicle details
		$sql = "SELECT id FROM boarding_vehicle
				WHERE trip_id = '$trip->trip_id' AND travel_date = :travel_date AND departure_order = :vehicle_order";

		self::$db->query($sql, array('travel_date' => $travel_date, 'vehicle_order' => $vehicle_order));
		if ($result = self::$db->fetch('obj')) {
			// update boarding vehicle with booked vehicle id
			self::$db->query("UPDATE boarding_vehicle SET booked_vehicle_id = '$bbv_id' WHERE id = '$result->id'");
		} else {
			$sql = "INSERT INTO boarding_vehicle (trip_id, park_map_id, booked_vehicle_id, vehicle_type_id, departure_order, travel_date)
					VALUES (:trip_id, :park_map_id, :booked_vehicle_id, :vehicle_type_id, :departure_order, :travel_date)";

			$param = array(
				'trip_id' => $trip->trip_id,
				'park_map_id' => $park_map_id,
				'booked_vehicle_id' => $bbv_id,
				'vehicle_type_id' => $vehicle->vehicle_type_id,
				'departure_order' => $vehicle_order,
				'travel_date' => $travel_date
			);

			self::$db->query($sql, $param);
		}
		return true;
	}


	function verifyVehicle($_vehicle_no)
	{
		$result = self::$db->query("SELECT id FROM vehicle_info WHERE vehicle_no = :vehicle_no", array('vehicle_no' => $_vehicle_no));
		if ($result = self::$db->fetch('obj')) {
			return true;
		} else {
			return false;
		}
	}


	function getVehicleDetails($vehicle_id)
	{
		$sql = "SELECT id AS vehicle_id, vehicle_no, driver_name, drivers_phone_no, vehicle_name, num_of_seats FROM vehicle_info
				WHERE id = '$vehicle_id'";
		$result = self::$dbh->query($sql);
		return $result->fetch_assoc();
	}


	function addVehicle($vehicle_no, $vehicle_type_id, $driver_name, $driver_phone)
	{
		$sql = "INSERT INTO vehicle_info
			(vehicle_no, driver_name, drivers_phone, vehicle_type_id)
				VALUES
			(:vehicle_no, :driver_name, :driver_phone, :vehicle_type_id)";

		$param = array(
				'vehicle_no' => $vehicle_no,
				'driver_name' => $driver_name,
				'driver_phone' => $driver_phone,
				'vehicle_type_id' => $vehicle_type_id
		);
		if (self::$db->query($sql, $param)) {
			return true;
		}
	}


	public function getAllVehicleTypes()
	{
		$sql = "SELECT * FROM vehicle_types ORDER BY vehicle_name";
		self::$db->query($sql);
		return self::$db->fetchAll('obj');
	}


	public function getAllRegisteredVehicles()
	{
		self::$db->query("SELECT * FROM vehicle_info");
		return self::$db->fetchAll('obj');
	}


	function charterVehicle()
	{
		$sql = "INSERT INTO vehicle_charter
				(customer_name, customer_phone, next_of_kin, email, departure_location, destination, date_of_travel, date_chartered)
				VALUES
				(:customer_name, :customer_phone, :next_of_kin, :email, :departure_location, :destination, :travel_date, NOW())";

		$param = array(
				'customer_name' => $_POST['customer_name'],
				'customer_phone' => $_POST['customer_phone'],
				'next_of_kin' => $_POST['email'],
				'email' => $_POST['email'],
				'departure_location' => $_POST['departure_location'],
				'destination' => $_POST['destination'],
				'travel_date' => $_POST['travel_date']
		);

		if (self::$db->query($sql, $param))
			return true;
	}


	function editVehicle($vehicle_id, $vehicle_no, $vehicle_type_id, $driver_name, $driver_phone, $bbv_id)
	{
		$sql = "UPDATE vehicle_info SET
					vehicle_no = '$vehicle_no',
					vehicle_type_id = '$vehicle_type_id',
					driver_name = '$driver_name',
					drivers_phone = '$driver_phone'
				WHERE id = '$vehicle_id'";

		if (self::$db->query($sql)) {
			// update booked vehicles
			self::$db->query("UPDATE booked_vehicles SET vehicle_no = '$vehicle_no' WHERE id = '$bbv_id'");
			return true;
		}
	}


	public function removeBookedVehicle($bbv_id, $bv_id)
	{
		self::$db->beginDbTransaction();
		$query_check = true;
		self::$db->query("DELETE FROM booked_vehicles WHERE id = :bbv_id", array('bbv_id' => $bbv_id)) ? null : $query_check = false;
		self::$db->query("UPDATE boarding_vehicle SET booked_vehicle_id = '0' WHERE id = :bv_id", array('bv_id' => $bv_id)) ? null : $query_check = false;

		if ($query_check === true) {
			self::$db->commitTransaction();
			return true;
		} else {
			self::$db->rollBackTransaction();
		}

	}
}

?>