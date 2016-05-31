<?php
session_start();

require_once "classes/seatpicker.class.php";
require_once "classes/seating.class.php";
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
	elseif ($_REQUEST['op'] == 'verify-customer')
	{
		$customer = new Customer();
		$_customer = $customer->getCustomer('phone_no', $_POST['customer_phone']);
		if ($_customer == false && empty($_POST['customer_name'])) {
			echo '{"id": "00"}'; // prompt for new customer details
			return;
		} elseif ($_customer == false && empty($_POST['customer_name']) == false) {
			$customer->customer_name = $_POST['customer_name'];
			$customer->phone_no = $_POST['customer_phone'];
			$customer->next_of_kin_phone = $_POST['kin_phone'];
			$customer_id = $customer->addNew($customer);

			$_customer['c_name'] = $customer->customer_name;
			$_customer['phone_no'] = $customer->phone_no;
			$_customer['next_of_kin_phone'] = $customer->next_of_kin_phone;
			$_customer['id'] = $customer_id;
		}
		echo json_encode($_customer);
	}
	elseif ($_REQUEST['op'] == 'complete-booking')
	{
		extract($_POST);

		$seating = new Seating();
		try {
			$seat_no = $seating->reserveSeat($boarding_vehicle_id, $seat_no);
		} catch (Exception $e) {
			echo $e->getCode();
			return false; // seat no longer available
		}

		$booking = new Booking();
		$ticket_id = $booking->book($boarding_vehicle_id, $seat_no, $customer_id);
		if (is_numeric($ticket_id)) {
			require_once "classes/printer.class.php";
			$printer = new Printer();
			$printer->printTicket($ticket_id);
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
