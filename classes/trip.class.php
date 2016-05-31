<?php

require_once "ticket.class.php";

class Trip extends Ticket
{

    public function __construct()
    {
        parent::__construct();
    }

    public function addTrip($trip_id, $park_map_id, $departure, $route_id, $vehicle_type_id, $amenities, $departure_time, $fare)
    {
        if (is_numeric($this->verifyTrip($park_map_id, $vehicle_type_id, $departure))) {
            return true;
        }

        $sql = "INSERT INTO trips (trip_id, park_map_id, departure, vehicle_type_id, route_id, amenities, departure_time, fare)
            VALUES (:trip_id, :park_map_id, :departure, :vehicle_type_id, :route_id, :amenities, :departure_time, :fare)";

        $param = array(
            'trip_id' => $trip_id,
            'park_map_id' => $park_map_id,
            'departure' => $departure,
            'route_id' => $route_id,
            'vehicle_type_id' => $vehicle_type_id,
            'amenities' => $amenities,
            'departure_time' => $departure_time,
            'fare' => $fare
        );

        self::$db->query($sql, $param);
        return self::$db->getLastInsertId();
    }


    public function getTripDetails($vehicle_type_id, $park_map_id, $departure_order)
    {
        $query = "";
        if (is_numeric($departure_order) && $departure_order > 0) {
            $query = "AND departure = '$departure_order'";
        }
        // fetch details of last added vehicle type for the route
        $sql = "SELECT trip_id, fare, departure FROM trips
            WHERE vehicle_type_id = :vehicle_type AND park_map_id = :park_map_id $query
            ORDER BY departure ASC LIMIT 0, 1";

        $param = array(
            'vehicle_type' => $vehicle_type_id,
            'park_map_id' => $park_map_id
        );
        self::$db->query($sql, $param);
        if ($data = self::$db->fetch('obj')) {
            return $data;
        }
        return false;
    }


    public function updateTrip($trip_id, $amenities, $fare)
    {
        $sql = "UPDATE trips SET amenities = :amenities, fare = :fare WHERE id = :id";
        $result = self::$db->query($sql, array('amenities' => $amenities, 'fare' => $fare, 'id' => $trip_id));
        if ($result !== false) {
            return true;
        }
        return false;
    }


    public function getByPark($park_id)
    {
        $sql = "";
        self::$db->query($sql, array('park_id' => $park_id));
        return self::$db->fetchAll('obj');
    }

    private function verifyTrip($park_map_id, $vehicle_type_id, $departure)
    {
        $sql = "SELECT id FROM trips WHERE park_map_id = :park_map_id AND vehicle_type_id = :vehicle_type_id AND departure = :departure";
        $param = array(
            'park_map_id' => $park_map_id,
            'vehicle_type_id' => $vehicle_type_id,
            'departure' => $departure
        );
        self::$db->query($sql, $param);
        if ($id = self::$db->fetch('obj')) {
            return $id->id;
        }
    }
}
