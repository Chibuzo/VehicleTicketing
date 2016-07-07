<?php
require_once "ticket.class.php";
require_once "vehiclemodel.class.php";

class SeatPicker extends Ticket {
	public $vehicle_type_id;
	public $fare;
	public $trip_id;
	public $travel_date;
	public $park_map_id;
	public $num_of_seats;
	public $boarding_vehicle_id = null;
	public $booked_seats;
	public $seat_status;
	public $departure_time = '';
	public $departure_order = 0;

	private $tbl = 'boarding_vehicle';

	function __construct($park_map_id, $travel_date, $num_of_seats, $vehicle_type_id, $travel_id, $departure_order = 0)
	{
		parent::__construct();

		$this->travel_id = $travel_id;
		$this->travel_date = $travel_date;
		$this->num_of_seats = $num_of_seats;
		$this->vehicle_type_id = $vehicle_type_id;
//		$this->fare = $fare;
//		$this->trip_id = $trip_id;
		$this->park_map_id = $park_map_id;
		$this->departure_order = $departure_order;
	}


	function getSeatChart()
	{
		$status = $this->getBoardingVehicleDetails();
		if ($status == "05") {
			return $status; // vehicle type wasn't setup for the selected route
		}

		switch ($this->vehicle_type_id) {
			case 3:
			case 5:
				return self::getLuxurySeats();
				break;
			case 8: // toyota hiace
				return self::getToyotaHiaceSeats();
				break;
			case 10:
			case 13:
				return self::getNissianUrvanSeats();
				break;
			case 15:
			case 19:
				return $this->getCoaterSeats();
				break;
		}
	}


	function getBoardingVehicleDetails()
	{
		$_vehicle = new VehicleModel();
		$vehicle = $_vehicle->findBoardingVehicle($this->park_map_id, $this->vehicle_type_id, $this->departure_order, $this->travel_date);

		if ($vehicle != false) {
			$this->boarding_vehicle_id = $vehicle->id;
			$this->fare = $vehicle->fare;
			$this->trip_id = $vehicle->trip_id;
			$this->booked_seats = explode(',', $vehicle->booked_seats);
			$this->seat_status =  $vehicle->seat_status;
		} else {
			$result = $this->insertVehicleForBoarding();
			if ($result == false) {
				return "05"; // vehicle type wasn't setup for the selected route
			}
			$this->boarding_vehicle_id = $result->boarding_vehicle_id;
			$this->fare = $result->fare;
			$this->trip_id = $result->trip_id;
			$this->booked_seats = array();
			$this->seat_status = 'Not full';
		}
	}


	public function insertVehicleForBoarding()
	{
		$vehicle = new VehicleModel();
		return $vehicle->fixBoardingVehicle($this->vehicle_type_id, $this->park_map_id, $this->travel_date, $this->departure_order, $this->travel_id);
	}


	/**
	 *  Toyota hiace with seat number 10
	 *  one seats at the front
	 *  then three seats on other rows
	 */
	function getToyotaHiaceSeats()
	{
		$booked_seats = $this->booked_seats;
		$seat_arrangement = $this->upperSittingDetails('mini-bus');

		$counter = 0;
		for ($i = 1; $i <= $this->num_of_seats; $i++) {
			$class = "class='seat'";

			$seat = $i;
			if (in_array($seat, $booked_seats)) $class = "class='booked_seat'";
			if ($counter == 0) $seat_arrangement .= $i <= 2 ? "<div class='front-row'>" : "<div class='seat-row'>";

			$seat_arrangement .= "\t<div {$class} data-hidden='no'  title='Seat {$seat}' id='{$seat}'></div>";
			$counter++;

			if ($i == 2) { // For one seat at the front
				$seat_arrangement .= "</div>"; // Close cols for the seat at the front
				$counter = 0;
			}

			if ($i == 5) {
				$seat_arrangement .= "\t<div class='th-hide'></div>";
				$counter++;
			} elseif ($i == 8) {
				$seat_arrangement .= "\t<div class='th-hide'></div>";
				$counter++;
			} elseif ($i == 11) {
				$seat_arrangement .= "\t<div class='th-hide'></div>";
				$counter++;
			}
			//if ($i == 2) continue;

			if ($counter == 4) {
				$counter = 0;
				$seat_arrangement .= "</div>"; // Close cols
			}
		}
		if ($counter == 1) $seat_arrangement .= "</div>";
		$seat_arrangement .= "\n</div>\n";

		return $seat_arrangement .= $this->lowerSittingDetails();
	}


	function getNissianUrvanSeats()
	{
		$booked_seats = $this->booked_seats;
		$seat_arrangement = $this->upperSittingDetails('mini-bus nissian-urvan');

		$counter = 0;
		for ($i = 1; $i <= $this->num_of_seats; $i++) {
			$class = "class='seat'";

			$seat = $i;
			if (in_array($seat, $booked_seats)) $class = "class='booked_seat'";
			if ($counter == 0) $seat_arrangement .= $i <= 2 ? "<div class='front-row'>" : "<div class='seat-row'>";

			$seat_arrangement .= "\t<div {$class} data-hidden='no'  title='Seat {$seat}' id='{$seat}'></div>";
			$counter++;

			if ($i == 2) { // For one seat at the front
				$seat_arrangement .= "</div>"; // Close cols for the seat at the front
				$counter = 0;
			}

			if ($counter == 3) {
				$counter = 0;
				$seat_arrangement .= "</div>"; // Close cols
			}
		}
		if ($counter == 3) $seat_arrangement .= "</div>";
		$seat_arrangement .= "\n</div>\n";

		return $seat_arrangement .= $this->lowerSittingDetails();
	}


	public function getLuxurySeats()
	{
		$booked_seats = $this->booked_seats;
		$seat_arrangement = $this->upperSittingDetails('luxury');

		$counter = $counter2 = 0;
		$seat_arrangement .= "<div id='right_seats'>";
		for ($i = 1; $i <= $this->num_of_seats; $i++) {
			$class = "class='seat'";

			if ($counter == 0) $seat_arrangement .= "<div class='cols'>";
			if ($counter < 2) {
				$seat = $i;
				/*** exchange arrays to match seating arrangement ***/
				/*if ($i % 2 == 1) $seat = $i + 1;
				else $seat = $i - 1;*/
				if (in_array($seat, $this->booked_seats)) $class = "class='booked_seat'";
				$seat_arrangement .= "\t<div {$class} data-hidden='no' title='Seat {$seat}' id='{$seat}'></div>";
				++$counter;
				if ($counter == 2) $seat_arrangement .= "</div>"; // Close cols
				if ($i != $this->num_of_seats) { continue; }
			} else {
				if ($counter2 < 2) $down_seats[] = $i;
				++$counter2;
				if ($counter2 == 2) $counter2 = $counter = 0;
				if ($i != $this->num_of_seats) { continue; }
			}

			$counter = 0;
			$seat_arrangement .= "\n</div>\n<div id='left_seats'>\n";

			foreach ($down_seats AS $seat) {
				$class = "class='seat'";

				//if ($this->num_of_seats == 59 && $seat == 60) $seat = 59;		// Fixes a bug that makes the last seat 60 instead of 59 due to the rearrangement
				if ($counter == 0) $seat_arrangement .= "<div class='cols'>";
				if (in_array($seat, $this->booked_seats)) $class = "class='booked_seat'";
				$seat_arrangement .= "\t<div {$class} data-hidden='no' title='Seat {$seat}' id='{$seat}'></div>";
				++$counter;
				if ($counter == 2) {
					// Close cols
					$seat_arrangement .= "</div>";
					$counter = 0;
				}
			}
			if ($counter == 1) $seat_arrangement .= "</div>";
			$seat_arrangement .= "\n</div>\n</div>";
		}

		return $seat_arrangement .= $this->lowerSittingDetails();
	}


	function getSiennaSeats() {
		$booked_seats = $this->booked_seats;
		$seat_arrangement = $this->upperSittingDetails('sienna');

		$counter = 0;
		for ($i = 1; $i <= $this->num_of_seats; $i++) {
			$seat = $i; $class = '';

			// rearrange seat number
			if ($i > 1) {
				if ($i % 2 == 0) $seat = $i + 1;
				else {
					$seat = $i - 1;
					$class .= ' push-seat';
				}
			}
			if (in_array($seat, $booked_seats)) $class .= " booked_seat";
			else $class .= " seat";

			if ($counter == 0) $seat_arrangement .= "<div class='cols'>";
			$seat_arrangement .= "\t<div class='{$class}' data-hidden='no' title='Seat {$seat}' id='{$seat}'></div>";
			++$counter;

			if ($i == 1) { // For one seat at the front
				$seat_arrangement .= "</div>"; // Close cols for the seat at the front
				$counter = 0;
			}

			if ($counter == 2) {
				//if ($i == 10) { $counter = 1; continue; }
				$counter = 0;
				$seat_arrangement .= "</div>"; // Close cols
			}
		}
		//if ($counter == 1) $seat_arrangement .= "</div>";
		$seat_arrangement .= "\n</div>\n";

		return $seat_arrangement .= $this->lowerSittingDetails();
	}



	/*** Seating arrangement for 59/60 seater vehicle ***/
	function getCoaterSeats() {
		$width = '470px';
		$style = "position:relative; top:4px; width:150px; right:6px; clear: both; display:block";
		$counter = 0; $counter2 = 0;

		$seat_arrangement = "<div class='seat_arrangement' style='width:{$width}' data-fare='{$this->fare}' data-route_id='$this->route_id'
		data-num_of_seats='{$this->num_of_seats}' data-boarding_vehicle_id='$this->boarding_vehicle_id' data-travel_date='{$this->travel_date}'>
		<span class='glyphicon glyphicon-remove pull-right'></span>
		<p>Click on an available seat to select it. Click again to de-select it.</p>
		<div class='seat_wrap' style='margin-left:30px; display:inline'>
		<div id='right_seats'>\n<div class='cols steering'></div>\n";

		for ($i = 1; $i <= $this->num_of_seats; $i++) {
			$class = "class='seat'";

			if ($counter == 0) $seat_arrangement .= "<div class='cols'>";
			if ($counter < 2) {
				/*** exchange arrays to match seating arrangement ***/
				if ($i % 2 == 1) $seat = $i + 1;
				else $seat = $i - 1;
				if (in_array($seat, $this->booked_seats)) $class = "class='booked_seat'";
				$seat_arrangement .= "\t<div {$class} data-vehicle_id='{$this->vehicle_id}' data-hidden='no' title='Seat {$seat}' id='{$seat}'></div>";
				++$counter;
				if ($counter == 2) $seat_arrangement .= "</div>"; // Close cols
				if ($i != $this->num_of_seats) { continue; }
			} else {
				if ($counter2 < 2) $down_seats[] = $i;
				++$counter2;
				if ($counter2 == 2) $counter2 = $counter = 0;
				if ($i != $this->num_of_seats) { continue; }
			}

			$counter = 0;
			$seat_arrangement .= "\n</div>\n<div id='left_seats' style='margin-top:10px'><div class='cols'></div>\n";

			foreach ($down_seats AS $seat) {
				$class = "class='seat'";

				if ($seat % 2 == 1) ++$seat;
				else --$seat;
				//if ($this->num_of_seats == 59 && $seat == 60) $seat = 59;		// Fixes a bug that makes the last seat 60 instead of 59 due to the rearrangement
				if ($counter == 0) $seat_arrangement .= "<div class='cols'>";
				if (in_array($seat, $this->booked_seats)) $class = "class='booked_seat'";
				$seat_arrangement .= "\t<div {$class} data-vehicle_id='{$this->vehicle_id}' data-hidden='no' title='Seat {$seat}' id='{$seat}'></div>";
				++$counter;
				if ($counter == 2) {
					// Close cols
					$seat_arrangement .= "</div>";
					$counter = 0;
				}
			}
			if ($counter == 1) $seat_arrangement .= "</div>";
			$seat_arrangement .= "\n</div>\n</div>";
		}

		return $seat_arrangement .= $this->lowerSittingDetails($style);
	}


	private function upperSittingDetails($vehicle_type)
	{
		return "<div class='seat_arrangement $vehicle_type' data-fare='" . number_format($this->fare) . "' data-park_map_id='{$this->park_map_id}' data-trip_id='{$this->trip_id}'
		data-departure_order='{$this->departure_order}' data-boarding_vehicle_id='{$this->boarding_vehicle_id}' data-vehicle_type_id='{$this->vehicle_type_id}' data-travel_date='{$this->travel_date}'>
		<div class='seat_wrap'>
			<div class='steering'></div>";
	}


	private function lowerSittingDetails()
	{
		return "<span id='seat_details' class='seat-details'>
			Seat number: <span class='picked_seat'></span><br />
			Fare: <span class='show_fare red'></span>
		</span>

		<div class='continue-btn pull-right hidden'>
			<a href='' class='continue btn btn-default btn-fill btn-sm pull-right'>Continue&nbsp;<i class='fa fa-angle-double-right'></i></a>
		</div></div>";
	}
}
?>
