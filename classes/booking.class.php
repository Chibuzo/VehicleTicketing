<?php
require_once "ticket.class.php";

class Booking extends Ticket {
	private static $db_tbl = 'booking_details';

	function __construct()
	{
		parent::__construct();
	}


	function book($boarding_vehicle_id, $seat_no, $channel, $customer_id)
	{
		$ticket_no = $this->generateRefNo();

		$sql = "INSERT INTO " . self::$db_tbl . "
		(ticket_no, payment_status, channel, boarding_vehicle_id, seat_no, customer_id, user_id)
		VALUES
		('$ticket_no', 'Paid', :channel, :boarding_vehicle_id, :seat_no, '$customer_id', '{$_SESSION['user_id']}')";

		$param = array(
				'channel' => $channel,
				'boarding_vehicle_id' => $boarding_vehicle_id,
				'seat_no' => $seat_no
		);

		if (self::$db->query($sql, $param)) {
			return self::$db->getLastInsertId();
		} else {
			return "03"; // Booking wasn't successful
		}
	}


	function reserveSeat($boarding_vehicle_id, $seat_no) {
		// get the booked seats
		$sql = "SELECT booked_seats, num_of_seats FROM boarding_vehicle bv
				JOIN vehicle_types vt ON bv.vehicle_type_id = vt.vehicle_type_id
				WHERE bv.id = :id";

		self::$db->query($sql, array('id' => $boarding_vehicle_id));
		$seat_details = self::$db->fetch('obj');

		if (!empty($seat_details->booked_seats)) {
			$booked_seats = explode(",", $seat_details->booked_seats);

			/*** Make sure no seat number repeats itself, and that the selected seat ($seat_no) has not already been booked ***/
			$booked_seats = array_unique($booked_seats);
			if (in_array($seat_no, $booked_seats)) {
				throw new Exception ("The selected seat is no longer available", "02");
			}

			# If there is any empty seat/array element, remove it
			foreach ($booked_seats AS $key => $value) if (empty($value)) unset($booked_seats[$key]);
			$booked_seats = array_values($booked_seats);
			$num_of_seats_booked = count($booked_seats);

			$booked_seats = implode(",", $booked_seats);
			$booked_seats .= ',' . $seat_no;
		} else {
			$booked_seats = $seat_no;
			$num_of_seats_booked = 1;
		}

		# START TRANSACTION
		self::$db->beginDbTransaction();
		$query_check = true;

		// Check if the seats are filled
		$status = 'Not full';
		if ($num_of_seats_booked + 1 == $seat_details->num_of_seats)
			$status = 'Full';

		$sql = "UPDATE boarding_vehicle SET
					booked_seats = '$booked_seats',
					seat_status = '$status'
				WHERE id = :boarding_vehicle_id";

		self::$db->query($sql, array('boarding_vehicle_id' => $boarding_vehicle_id)) ? null : $query_check = false;

		if ($query_check == false) {
			self::$db->rollBackTransaction();
			return false;
		} else
			self::$db->commitTransaction();
		return $seat_no;
	}


	public function saveOnlineBooking($trip_id, $travel_date, $departure_order, $seat_no, $customer_id, $channel)
	{
		// first, get boarding vehicle ID
		$vehicle = new VehicleModel();
		$boarding_vehicle_id = $vehicle->getBoardingVehicleId($trip_id, $departure_order, $travel_date);
		if ($boarding_vehicle_id == false) {
			$trip = new Trip();
			$_trip = $trip->getTrip($trip_id);

			$vehicle->fixBoardingVehicle($_trip->vehicle_type_id, $_trip->park_map_id, $travel_date, $departure_order);
			$boarding_vehicle_id = $vehicle->getBoardingVehicleId($trip_id, $departure_order, $travel_date);
		}

		// reserve seat, haha
		$result = $this->reserveSeat($boarding_vehicle_id, $seat_no);
		if ($result != $seat_no) {
			throw new Exception ("Couldn't reserve seat. Please try again", "01");
		}

		// complete booking
		$result = $this->book($boarding_vehicle_id, $seat_no, $channel, $customer_id);
		if ($result != true) {
			throw new Exception ("Booking not successful", "02");
		}
		return true;
	}


	public function getTicketDetails($id)
	{
		$sql = "SELECT ticket_no, c_name, next_of_kin_phone, tr.fare, seat_no, vehicle_no, bv.travel_date, destination, bv.departure_order, vt.vehicle_name vehicle_type
				FROM " . self::$db_tbl . " bd
				JOIN boarding_vehicle bv ON bd.boarding_vehicle_id = bv.id
				LEFT JOIN booked_vehicles bbv ON bv.booked_vehicle_id = bbv.id
				JOIN trips tr ON bv.trip_id = tr.trip_id
				JOIN destination d ON bv.park_map_id = d.park_map_id
				JOIN customers c ON bd.customer_id = c.id
				JOIN vehicle_types vt ON tr.vehicle_type_id = vt.vehicle_type_id
				WHERE bd.id = :id";

		self::$db->query($sql, array('id' => $id));
		if ($ticket = self::$db->fetch()) {
			return $ticket;
		}
	}


	function cancelBooking($ticket_id)
	{
		$sql = "SELECT bv.booked_seats, bv.id bb_id, seat_no FROM " . self::$db_tbl . " bd
				JOIN boarding_vehicle bv ON bd.boarding_vehicle_id = bv.id
				WHERE bd.id = :ticket_id";

		self::$db->query($sql, array('ticket_id' => $ticket_id));
		if ($details = self::$db->fetch('obj')) {
			$seats = explode(",", $details->booked_seats);
			foreach ($seats AS $key => $value) if ($seats[$key] == $details->seat_no) unset($seats[$key]);
			$remaining_seats = implode(',', $seats);

			// lets do some transactions
			$query_check = true;
			self::$db->beginDbTransaction();
			$sql = "UPDATE boarding_vehicle SET booked_seats = '$remaining_seats', seat_status = 'Not full'
					WHERE id = '$details->bb_id'";

			self::$db->query($sql) ? null : $query_check = false;

			self::$db->query("DELETE FROM booking_details WHERE id = :id", array('id' => $ticket_id)) ? null : $query_check = false;

			if ($query_check == true) {
				self::$db->commitTransaction();
				return true;
			} else {
				self::$db->rollBackTransaction();
				return false;
			}
		} else {
			echo "Not Found";
		}
	}


	function generateRefNo()
	{
		$find    = array('/', '+', '=', "\\", '|', '-');
		$replace = array('X', 'Y', 'Z', 'U', 'O', '4');
		return strtoupper(str_replace($find, $replace, base64_encode(mcrypt_create_iv(6, MCRYPT_RAND))));
	}


	function countUserTickets($customer_id) {
		$sql = "SELECT COUNT(*) AS num_row FROM {self::$db_tbl} WHERE customer_id = :customer_id AND payment_status = 'Paid'";
		self::$db->query($sql, array('customer_id' => $customer_id));
		$count = self::$db->fetch('obj');
		return $count->num_rows;
	}
}

?>
