<?php
require_once '../apicaller.php';
require_once "../classes/ticket.class.php";
require_once "../classes/vehiclemodel.class.php";
require_once "../classes/trip.class.php";
require_once "../classes/destination.class.php";
require_once "../classes/parkmap.class.php";

$apicaller = new ApiCaller('APP001', '28e336ac6c9423d946ba02d19c6a2632', 'http://localhost/travelhub/api/');

try {
    $setup_data = $apicaller->sendRequest(array(
        'controller' => 'setup',
        'action'     => 'init',
        'travel_id'  => $_POST['travel_id'],
        'park_id'    => $_POST['park_id'],
    ));
    if (empty($setup_data)) {
        echo "No data";
        exit;
    }
    $phone = $_POST['phone1'] . ", " . $_POST['phone2'];

    // let's be useful
    $travel = $setup_data->travel;
    $vehicles       = $setup_data->data->vehicles;
    $trips          = $setup_data->data->trips;
    $destinations   = $setup_data->data->destinations;

    Ticket::addTravelDetails($travel->company_name, $travel->id, $travel->abbr, $_POST['state'], $travel->park, $_POST['park_id'], $travel->online_charge, $travel->offline_charge, $phone);

    $vehicle = new VehicleModel();
    foreach ($vehicles AS $veh) {
        $vehicle->addVehicleType($veh->vehicle_name, $veh->type_name, $veh->num_of_seats, $veh->vehicle_type_id);
    }

    $trip = new Trip();
    foreach ($trips AS $tr) {
        $trip->addTrip($tr->id, $tr->park_map_id, $tr->departure, $tr->route_id, $tr->vehicle_type_id, $tr->amenities, $tr->departure_time, $tr->fare);
    }

    $destination = new Destination();
    foreach ($destinations AS $dest) {
        $destination->addRoute($dest->park_map_id, $dest->destination_state, $dest->destination);
    }

    echo "Done";
} catch (Exception $e) {
    echo $e->getMessage();
}