<?php
require_once "ticket.class.php";

class Report extends Ticket {

	public function __construct()
	{
		parent::__construct();
	}

	public function getDailyReport($date)
	{
		$sql = "SELECT bb.booked_seats, seat_status, departure_order, vehicle_name bus_type, f.fare, destination, (scouters_charge + drivers_feeding + fuel) expenses
				FROM boarding_vehicle bb
				JOIN trips f ON bb.trip_id = f.id
				JOIN vehicle_types bt ON f.vehicle_type_id = bt.id
				JOIN destination d ON f.park_map_id = d.park_map_id
				LEFT JOIN manifest_audit ma ON bb.id = ma.boarding_vehicle_id
				WHERE travel_date = :travel_date AND seat_status = 'Full' AND booked_seats <> ''
				GROUP BY bb.id
				ORDER BY destination, vehicle_name, departure_order";

		self::$db->query($sql, array('travel_date' => date('Y-m-d', strtotime($date))));
		if ($reports = self::$db->fetchAll()) {
			return $reports;
		} else {
			return array();
		}
	}
}
?>
