<?php
require_once "ticket.class.php";

class Printer extends Ticket
{
	public function __construct()
	{
		parent::__construct();
	}


	public function printTicket($ticket_id)
	{
		require_once "booking.class.php";
		$booking = new Booking();
		$details = $booking->getTicketDetails($ticket_id);
		if (is_array($details)) {
			extract($details);
			$vehicle_no = empty($vehicle_no) ? "-" : $vehicle_no;
			echo "<div class='line'><label class='ticket'>Ticket:</label> $ticket_no</div>
				<div class='line'><label class='ticket'>Customer name:</label> $c_name</div>
				<div class='line'><label class='ticket'>Next of kin no:</label> $next_of_kin_phone</div>
				<div class='line'><label class='ticket'>Route:</label> $route</div>
				<div class='line'><label class='ticket'>Seat number:</label> $seat_no</div>
				<div class='line'><label class='ticket'>Bus number:</label>$vehicle_no</div>
				<div class='line'><label class='ticket'>Departure order:</label>" . parent::ordinal($departure_order) . " " . $vehicle_type . "</div>
				<div class='line'><label class='ticket'>Date of Travel:</label>" . date('d-m-Y', strtotime($travel_date)) . "</div>
				<div class='line'><label class='ticket'>Departure time:</label> 5:30 AM</div>
				<div class='line'><label class='ticket'>Amount:</label> ₦" . number_format($fare) . "</div>
				<!--<div style='text-align:center; font-style:italic'>Free baggage allowance to 20kg</div>-->
				<div style='text-align:center; font-style:italic'>No refund of money after payment</div>";
		}
	}
}
?>
