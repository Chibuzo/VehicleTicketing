<?php
require_once '../apicaller.php';

$apicaller = new ApiCaller('APP001', '28e336ac6c9423d946ba02d19c6a2632', 'http://localhost/travelhub/api/');

if (isset($_REQUEST['op'])) {
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
}