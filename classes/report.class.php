<?php
require_once "ticket.class.php";

class Report extends Ticket {

	public function __construct()
	{
		parent::__construct();
	}

	public function getDailyReport($date)
	{
		$sql = "SELECT bv.booked_seats, seat_status, bv.departure_order, vehicle_name bus_type, vehicle_no, t.fare, destination, (scouters_charge + drivers_feeding + fuel) expenses
				FROM boarding_vehicle bv
				LEFT JOIN booked_vehicles bbv ON bv.booked_vehicle_id = bbv.id
				JOIN trips t ON bv.trip_id = t.trip_id
				JOIN vehicle_types vt ON t.vehicle_type_id = vt.vehicle_type_id
				JOIN destination d ON t.park_map_id = d.park_map_id
				LEFT JOIN manifest_audit ma ON bv.id = ma.boarding_vehicle_id
				WHERE bv.travel_date = :travel_date AND seat_status = 'Full' AND booked_seats <> ''
				GROUP BY bv.id
				ORDER BY destination, vehicle_name, bv.departure_order";

		self::$db->query($sql, array('travel_date' => date('Y-m-d', strtotime($date))));
		if ($reports = self::$db->fetchAll()) {
			return $reports;
		} else {
			return array();
		}
	}
}
?>
