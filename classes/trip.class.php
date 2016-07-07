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
        $tripid = $this->verifyTrip($park_map_id, $vehicle_type_id, $departure);
        if (is_numeric($tripid)) {
            return $tripid;
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


    public function getTrips()
    {
        $sql = "SELECT t.id, trip_id, vehicle_name, vt.vehicle_type_id, amenities, departure, fare, destination FROM trips t
                JOIN destination d ON t.park_map_id = d.park_map_id
                JOIN vehicle_types vt ON t.vehicle_type_id = vt.vehicle_type_id";

        self::$db->query($sql);
        return self::$db->fetchAll('obj');
    }


    public function getTrip($trip_id)
    {
        $sql = "SELECT vehicle_type_id, park_map_id FROM trips WHERE trip_id = :trip_id";
        self::$db->query($sql, array('trip_id' => $trip_id));
        if ($trip = self::$db->fetch('obj')) {
            return $trip;
        } else {
            throw new Exception ("Trip details not found.");
        }
    }


    public function getTripDetails($vehicle_type_id, $park_map_id, $departure_order)
    {
        $sql = "SELECT trip_id, fare, departure FROM trips
                WHERE vehicle_type_id = :vehicle_type AND park_map_id = :park_map_id AND departure = '$departure_order'";

        $param = array(
            'vehicle_type' => $vehicle_type_id,
            'park_map_id' => $park_map_id
        );

        // find trip details using the given departure order
        self::$db->query($sql, $param);
        if ($data = self::$db->fetch('obj')) {
            return $data;
        } else {
            // find trip details using the highest departure order
            $sql = "SELECT trip_id, fare, departure FROM trips
                    WHERE vehicle_type_id = :vehicle_type AND park_map_id = :park_map_id
                    ORDER BY departure ASC LIMIT 0, 1";

            self::$db->query($sql, $param);
            if ($data = self::$db->fetch('obj')) {
                return $data;
            } else {
                false;
            }
        }
        return false;
    }


    public function updateTrip($trip_id, $amenities, $fare)
    {
        $sql = "UPDATE trips SET amenities = :amenities, fare = :fare WHERE trip_id = :id";
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
