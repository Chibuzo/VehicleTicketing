<?php
session_start();

require_once "classes/seatpicker.class.php";
require_once "classes/seating.class.php";
//require_once "classes/routemodel.class.php";
require_once "classes/trip.class.php";
require_once "classes/booking.class.php";
require_once "classes/vehiclemodel.class.php";
require_once "classes/customer.class.php";

if (isset($_REQUEST['op'])) {
	if ($_REQUEST['op'] == 'get_seating')
	{
		// get seatpicker
		$seatpicker = new SeatPicker($_POST['park_map_id'], $_POST['travel_date'], $_POST['num_of_seat'], $_POST['vehicle_type'], $_SESSION['travel_id'], $_POST['departure_order']);
		echo $seatpicker->getSeatChart();
	}
	elseif ($_REQUEST['op'] == 'complete-booking')
	{
		extract($_POST);
		$seating = new Seating();
		$seat_no = $seating->reserveSeat($boarding_vehicle_id, $seat_no);
		if (is_numeric($seat_no)) {
			$customer = new Customer();
			$customer_id = $customer->findCustomer('phone_no', $_POST['customer_phone']);
			if (is_numeric($customer_id) == false) {
				$param = array(
					'c_name' => $_POST['customer_name'],
					'phone_no' => $_POST['customer_phone'],
					'next_of_kin_phone' => $_POST['kin_phone']
				);
				$customer_id = $customer->addNew($param);
			}
			$booking = new Booking();
			$ticket_id = $booking->book($boarding_vehicle_id, $seat_no, 'offline', $customer_id);
			if (is_numeric($ticket_id)) {
				require_once "classes/printer.class.php";
				$printer = new Printer();
				$printer->printTicket($ticket_id);
			} else {
				echo "Error";
			}
		} else {
			echo "03";
		}
	}
	elseif ($_REQUEST['op'] == 'print-ticket')
	{
		if (is_numeric($_POST['ticket_id'])) {
			require_once "classes/printer.class.php";
			$printer = new Printer();
			$printer->printTicket($_POST['ticket_id']);
		}
	}
	elseif ($_REQUEST['op'] == 'cancel-ticket')        cancelTicket();
	//elseif ($_REQUEST['op'] == 'get-customer-details') echo json_encode(getCustomersDetails($_GET['bd_id']));
}
?>
