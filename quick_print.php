<?php
session_start();
require_once("includes/DB_CONNECT.php");
$DB_CONNECTION = db_connect();

$sql = "SELECT bb.id, bi.bus_no, bb.route_code, bi.num_of_seats, bb.departure_order, bb.travel_date, booked_id
		FROM booked_buses AS bb
		JOIN bus_info bi ON bi.id = bb.bus_id
		JOIN seat_booking AS sb ON sb.bb_id = bb.id
		WHERE bb.id = '{$_REQUEST['bb_id']}'";
$result = $DB_CONNECTION->query($sql) or die (mysqli_error($DB_CONNECTION));
$bus = $result->fetch_object();

if ($bus->num_of_seats == 6) $fare_field = 'sienna_fare';
elseif ($bus->num_of_seats == 11) $fare_field = 'executive_fare';
elseif ($bus->num_of_seats < 20) $fare_field = 'hiace_fare';
else $fare_field = 'luxury_fare';

$fare  = getFare('1', $bus->route_code, $fare_field);
$state = splitRouteMap($bus->route_code);
$route = "{$state['origin']} to {$state['destination']}";

$date = date('Y-m-d');
$seats = '';

// Check if tickets has been printed
$result1 = $DB_CONNECTION->query("SELECT seat_status, booked_seats FROM seat_booking WHERE booked_id = '$bus->booked_id'");
extract($result1->fetch_assoc());
if ($seat_status == "Full") break;

$booked_seats = explode(',', $booked_seats);
# Get free (unbooked) seats
$booked = '';
for ($i = 1; $i < $bus->num_of_seats + 1; $i++) {
	if (in_array($i, $booked_seats)) continue;
	$free_seats[] = $i;
}
$num_of_free_seats = count($free_seats);
if ($num_of_free_seats > 15) {
	$num_to_print = 15;
} else {
	$num_to_print = $num_of_free_seats + 1;
}

for ($i = 0; $i < $num_to_print; $i++) {	
	/*** Generate ticket ref number ***/
	$find      = array('/', '+', '=', "\\", '|', '-');
	$replace   = array('X', 'Y', 'Z', 'U', 'O', '4');
	$ticket_no = strtoupper(str_replace($find, $replace, base64_encode(mcrypt_create_iv(6, MCRYPT_RAND))));
	
	$sql = "INSERT INTO booking_details (ticket_no, route_code, booked_id, fare, address, bus_no, date_booked, travel_date, staff_id, travel_id) VALUES
	('$ticket_no', '$bus->route_code', '{$_REQUEST['booked_id']}', '$fare', '{$state['destination']}', '$bus->bus_no', '$date', '$bus->travel_date', '{$_SESSION['staff_id']}', '1')";
	$DB_CONNECTION->query($sql);
	
	if (empty($seats))
		$seats .= $free_seats[$i]; // The first seat;
	else
		$seats .= "," . $free_seats[$i];
}

$seats = ',' . $seats;
$sql = "UPDATE seat_booking SET booked_seats = CONCAT(booked_seats, '$seats'), seat_status = '$seat_status' WHERE booked_id = '$bus->booked_id'";
$DB_CONNECTION->query($sql);

$DB_CONNECTION->query("UPDATE booked_buses SET seat_status = '$seat_status' WHERE id = '$bus->id'");

//echo json_encode($tickets);

function getTicket($ticket_no, $name, $next_of_kin, $route, $seat_no, $bus_no, $travel_date, $fare) {
	return "<div class='line'><label class='ticket'>Ticket:</label> $ticket_no</div>
		<div class='line'><label class='ticket'>Customer name:</label> $name</div>
		<div class='line'><label class='ticket'>Next of kin no:</label> $next_of_kin</div>
		<div class='line'><label class='ticket'>Route:</label> $route</div>
		<div class='line'><label class='ticket'>Seat number:</label> $seat_no</div>
		<div class='line'><label class='ticket'>Bus number:</label>$bus_no</div>
		<div class='line'><label class='ticket'>Date of Travel:</label>$travel_date</div>
		<div class='line'><label class='ticket'>Amount:</label> $fare NGN</div>
		<br /><p><div class='line'><br />&nbsp;</div></p>";
}

function splitRouteMap($route_code) {
	global $DB_CONNECTION;
	$result = $DB_CONNECTION->query("SELECT route from routes WHERE route_code = '$route_code' ORDER BY route");
	$route_map = $result->fetch_assoc();
	$route = explode(" - ", $route_map['route']);
	return array('origin' => $route[0], 'destination' => $route[1]);
}

function getFare($travel_id, $route_code, $fare_field) {
	global $DB_CONNECTION;
	
	$result = $DB_CONNECTION->query("SELECT {$fare_field} FROM fares WHERE travel_id = '$travel_id' AND route_code = '$route_code'");
	return $result->fetch_object()->$fare_field;
}
?>