<?php
session_start();
require_once '../apicaller.php';

if (isset($_REQUEST['op'])) {
    $apicaller = new ApiCaller('APP001', '28e336ac6c9423d946ba02d19c6a2632', 'http://localhost/travelhub/api/');
    if ($_REQUEST['op'] == 'update-seat')
    {
        try {
            $status = $apicaller->sendRequest(array(
                'controller'        => 'booking',
                'action'            => 'save_booking',
                'trip_id'           => $_POST['trip_id'],
                'travel_date'       => $_POST['travel_date'],
                'seat_no'           => $_POST['seat_no'],
                'departure_order'   => $_POST['departure_order'],
                'customer_name'     => $_POST['customer_name'],
                'customer_phone'    => $_POST['customer_phone'],
                'next_of_kin_phone' => $_POST['next_of_kin_phone'],
                'channel'           => 'offline'
            ));
        } catch (Exception $e) {
            echo $e->getCode();
        }
    }
    elseif ($_REQUEST['op'] == 'online-synch') // receive online booking through socket and save
    {
        require_once '../classes/booking.class.php';
        require_once '../classes/vehiclemodel.class.php';
        require_once '../classes/customer.class.php';

        $data = json_decode($_POST['data'], true);
        // handle customer
        $customer = new Customer();
        $_customer = $customer->getCustomer('phone_no', $data['customer_phone']);
        $customer_id = $_customer['id'];
        if ($_customer == false) {
            $customer->customer_name     = $data['customer_name'];
            $customer->phone_no          = $data['customer_phone'];
            $customer->next_of_kin_phone = $data['next_of_kin_phone'];
            $customer_id                 = $customer->addNew($customer);
        }
        try {
            $booking = new Booking();
            $booking->saveOnlineBooking($data['trip_id'], $data['travel_date'], $data['departure_order'], $data['seat_no'], $customer_id, $data['channel']);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    elseif ($_REQUEST['op'] == 'add-park-map')
    {
        try {
            $park_map_id = $apicaller->sendRequest(array(
                'controller'  => 'setup',
                'action'      => 'add_park_map',
                'origin'      => $_POST['origin'],
                'destination' => $_POST['destination'],
                'travel_id'   => $_SESSION['travel_id']
            ));
            if (is_numeric($park_map_id)) {
                require_once "../classes/destination.class.php";
                $destination = new Destination();
                $result = $destination->addRoute($park_map_id, $_POST['state'], $_POST['destination']);
                if ($result == true) {
                    echo "Done";
                }
            } else {
                echo "Err";
            }
        } catch (Exception $e) {
            echo $e->getCode();
        }
    }
    elseif ($_REQUEST['op'] == 'add-trip')
    {
        $departuretime = $_POST['hour'] . ':' . $_POST['minute'];
        $_amenities = implode($_POST['amenities'], ">");
        try {
            $status = $apicaller->sendRequest(array(
                'controller'  => 'setup',
                'action'      => 'add_trip',
                'park_map_id' => $_POST['park_map_id'],
                'departure'   => $_POST['departure'],
                'vehicle_type_id' => $_POST['vehicle_type'],
                'amenities'   => $_amenities,
                'departure_time' => $departuretime,
                'fare'        => $_POST['fare'],
                'travel_id'   => $_SESSION['travel_id'],
                'state_id'    => $_SESSION['state_id']
            ));
            if (is_numeric($status->trip_id)) {
                extract($_POST);
                require_once "../classes/trip.class.php";
                $trip = new Trip();
                $result = $trip->addTrip($status->trip_id, $park_map_id, $departure, $status->route_id, $vehicle_type, $_amenities, $departuretime, $fare);
                if (is_numeric($result)) echo "Done";
                else echo $result;
            }
        } catch (Exception $e) {
            echo $e->getCode();
        }
    }
    elseif ($_REQUEST['op'] == 'edit-trip')
    {
        $amenities = implode($_POST['amenities'], ">");
        try {
            $status = $apicaller->sendRequest(array(
                'controller'  => 'setup',
                'action'      => 'update_trip',
                'trip_id'     => $_POST['trip_id'],
                'amenities'   => $amenities,
                'fare'        => $_POST['fare']
            ));
        } catch (Exception $e) {
            echo $e->getCode();
        }
        require_once "../classes/trip.class.php";
        $trip = new Trip();
        if ($trip->updateTrip($_POST['trip_id'], $amenities, $_POST['fare'])) {
            echo "Done";
        }
    }
    elseif ($_REQUEST['op'] == 'travel-events')
    {
        $data = json_decode($_POST['data'], true);
        switch ($data['push_type']) {
            case 'add-vehicle-type':
                require_once "../classes/vehiclemodel.class.php";
                $vehicle = new VehicleModel();
                $vehicle->addVehicleType($data['vehicle_name'], '', $data['num_of_seats'], $data['vehicle_type_id']);
                break;
        }
    }
}