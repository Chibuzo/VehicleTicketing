<?php
require_once "ticket.class.php";

class Report extends Ticket {

	public function __construct()
	{
		parent::__construct();
	}

	public function getDailyReport($date)
	{
		$sql = "SELECT bb.booked_seats, seat_status, departure_order, name bus_type, fare, route, (load_cost + drivers_expenses) expenses
				FROM boarding_bus bb
				JOIN fares f ON bb.fare_id = f.id
				JOIN bus_types bt ON f.bus_type_id = bt.id
				JOIN routes r ON f.route_id = r.id
				LEFT JOIN manifest_audit ma ON bb.id = ma.booked_bus_id
				WHERE travel_date = :travel_date AND seat_status = 'Full' AND booked_seats <> ''
				GROUP BY bb.id
				ORDER BY route, name, departure_order";

		$this->db->query($sql, array('travel_date' => date('Y-m-d', strtotime($date))));
		if ($reports = $this->db->fetchAll()) {
			return $reports;
		} else {
			return array();
		}
	}
}
?>
