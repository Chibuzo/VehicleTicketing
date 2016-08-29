<?php
require_once "ticket.class.php";

class Synch extends Ticket
{
    //public $id, $trip_id, $travel_date, $seat_no, $departure_order, $cust_name, $cust_phone, $next_of_kin_phone;

    public function __construct()
    {
        parent::__construct();
    }


    public function postBooking($apicaller, $trip_id, $travel_date, $seat_no, $departure_order, $cust_name, $cust_phone, $next_of_kin_phone)
    {
        try {
            return $apicaller->sendRequest(array(
                'controller' => 'booking',
                'action' => 'save_booking',
                'trip_id' => $trip_id,
                'travel_date' => $travel_date,
                'seat_no' => $seat_no,
                'departure_order' => $departure_order,
                'customer_name' => $cust_name,
                'customer_phone' => $cust_phone,
                'next_of_kin_phone' => $next_of_kin_phone,
                'channel' => 'offline'
            ));
        } catch (Exception $e) {
            return $e->getCode();
        }
    }


    function logFailedSynch($trip_id, $travel_date, $seat_no, $departure_order, $cust_name, $cust_phone, $next_of_kin_phone)
    {
        $sql = "INSERT INTO booking_synch
                (trip_id, travel_date, seat_no, departure_order, cust_name, cust_phone, next_of_kin_phone)
                VALUES
                (:trip_id, :travel_date, :seat_no, :departure_order, :cust_name, :cust_phone, :next_of_kin_phone)";

        $param = array(
            'trip_id' => $trip_id,
            'travel_date' => $travel_date,
            'seat_no' => $seat_no,
            'departure_order' => $departure_order,
            'cust_name' => $cust_name,
            'cust_phone' => $cust_phone,
            'next_of_kin_phone' => $next_of_kin_phone
        );

        self::$db->query($sql, $param);
        $_SESSION['booking_synch'] = "Outdated";
    }


    public function postFailedSynch($apicaller)
    {
        self::$db->query("SELECT * FROM booking_synch");

        if ($data = self::$db->fetchAll('obj')) {
            try {
                $result = $apicaller->sendRequest(array(
                    'controller' => 'booking',
                    'action' => 'fix_failed_booking',
                    'data' => $data
                ));
            } catch (Exception $e) {
                return $e->getMessage();
            }
            if ($result == 'Done') {
                $_SESSION['booking_synch'] == 'Updated';
                self::$db->query("DELETE FROM booking_synch");
            }
        } else {
            return false;
        }
    }


    public function cancelTicket($apicaller, $ticket_id)
    {
        $sql = "SELECT seat_no, trip_id, departure_order, bv.travel_date FROM boarding_vehicle bv
                JOIN booking_details bd ON bv.id = bd.boarding_vehicle_id
                WHERE bd.id = :ticket_id";

        self::$db->query($sql, array('ticket_id' => $ticket_id));
        if ($d = self::$db->fetch('obj')) {
            try {
                $result = $apicaller->sendRequest(array(
                    'controller' => 'booking',
                    'action' => 'cancel_ticket',
                    'seat_no' => $d->seat_no,
                    'trip_id' => $d->trip_id,
                    'departure_order' => $d->departure_order,
                    'travel_date' => $d->travel_date
                ));
            } catch (Exception $e) {
                return $e->getMessage();
            }
            var_dump($result);
        } else {
            return false;
        }
    }


    public function synchManifestBalance($apicaller, $trip_id, $travel_date, $departure_order, $feeding, $fuel, $scouters, $expenses, $load)
    {
        try {
            $result = $apicaller->sendRequest(array(
                'controller' => 'report',
                'action' => 'save_manifest_account',
                'trip_id' => $trip_id,
                'travel_date' => $travel_date,
                'departure_order' => $departure_order,
                'feeding' => $feeding,
                'fuel' => $fuel,
                'expenses' => $expenses,
                'scouters' => $scouters,
                'load' => $load
            ));
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }


    public function reopenVehicle($apicaller, $trip_id, $travel_date, $departure_order)
    {
        try {
            $result = $apicaller->sendRequest(array(
                'controller' => 'booking',
                'action' => 'reopen_vehicle',
                'trip_id' => $trip_id,
                'travel_date' => $travel_date,
                'departure_order' => $departure_order
            ));
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }
}