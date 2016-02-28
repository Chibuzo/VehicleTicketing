<?php

require_once "ticket.class.php";

class Trip extends Ticket
{

    public function __construct()
    {
        parent::__construct();
    }

    public function addTrip($park_map_id, $departure, $travel_id, $state_id, $route_id, $vehicle_type_id, $amenities, $departure_time, $fare)
    {
        if (is_numeric($this->verifyTrip($park_map_id, $vehicle_type_id, $departure))) {
            return true;
        }

        $sql = "INSERT INTO trips (park_map_id, travel_id, state_id, departure, vehicle_type_id, route_id, amenities, departure_time, fare)
            VALUES (:park_map_id, :travel_id, :state_id, :departure, :vehicle_type_id, :route_id, :amenities, :departure_time, :fare)";

        $param = array(
            'park_map_id' => $park_map_id,
            'travel_id' => $travel_id,
            'state_id' => $state_id,
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


    /* for ticketing office */
    /*public function getDailyTrips($vehicle_type_id, $park_map_id, $travel_id)
    {
        $sql = "SELECT id trip_id, fare, departure FROM trips
				WHERE vehicle_type_id = :vehicle_type AND park_map_id = :park_map_id AND travel_id = :travel_id";

        $param = array(
            'vehicle_type' => $vehicle_type_id,
            'park_map_id' => $park_map_id,
            'travel_id' => $travel_id
        );

        self::$db->query($sql, $param);
        return self::$db->fetchAll('obj');
    }*/


    public function getTripDetails($vehicle_type_id, $park_map_id, $departure_order, $travel_id)
    {
        $query = "";
        if (is_numeric($departure_order) && $departure_order > 0) {
            $query = "AND departure_order = '$departure_order'";
        }
        $sql = "SELECT id trip_id, fare, departure FROM trips
				WHERE vehicle_type_id = :vehicle_type AND park_map_id = :park_map_id AND travel_id = :travel_id $query";

        $param = array(
            'vehicle_type' => $vehicle_type_id,
            'park_map_id' => $park_map_id,
            'travel_id' => $travel_id
        );

        self::$db->query($sql, $param);
        if ($data = self::$db->fetch('obj')) {
            return $data;
        } else {
            // fetch details of last added vehicle type for the route
            $sql = "SELECT id trip_id, fare, departure FROM trips
				WHERE vehicle_type_id = :vehicle_type AND park_map_id = :park_map_id AND travel_id = :travel_id
				ORDER BY departure ASC LIMIT 0, 1";

            $param = array(
                'vehicle_type' => $vehicle_type_id,
                'park_map_id' => $park_map_id,
                'travel_id' => $travel_id
            );
            self::$db->query($sql, $param);
            if ($data = self::$db->fetch('obj')) {
                return $data;
            }
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


    public function getTripsByRoute($route_id)
    {
        $sql = "SELECT tr.id trip_id, vt.id vehicle_type_id, num_of_seats, name, fare, amenities, departure_time, company_name, po.park origin_park, pd.park destination_park, travel_id FROM trips tr
				JOIN vehicle_types vt ON tr.vehicle_type_id = vt.id
				JOIN park_map pm ON tr.park_map_id = pm.id
				JOIN parks po ON pm.origin = po.id
				JOIN parks pd ON pm.destination = pd.id
				JOIN travels t ON tr.travel_id = t.id
				WHERE tr.route_id = :route_id AND fare > 0 ";

        self::$db->query($sql, array('route_id' => $route_id));
        return self::$db->fetchAll();
    }


    public function getByState($state_id)
    {
        $sql = "SELECT trips.*, po.park AS origin_name, pd.park AS destination_name
                FROM trips 
                JOIN park_map AS pm ON pm.id = trips.park_map_id
                JOIN parks AS po ON po.id = pm.origin
                JOIN parks AS pd ON pd.id = pm.destination
                WHERE trips.state_id = :state_id";
        self::$db->query($sql, array('state_id' => $state_id));
        return self::$db->fetchAll('obj');
    }

    public function getByStateTravel($state_id, $travel_id)
    {
        $sql = "SELECT trips.*, po.park AS origin_name, pd.park AS destination_name, vt.name AS vehicle_name
                FROM trips 
                JOIN park_map AS pm ON pm.id = trips.park_map_id
                JOIN parks AS po ON po.id = pm.origin
                JOIN parks AS pd ON pd.id = pm.destination
                JOIN vehicle_types vt ON vt.id = trips.vehicle_type_id
                WHERE trips.state_id = :state_id AND travel_id = :travel_id
                ORDER BY vehicle_name";
        self::$db->query($sql, array('state_id' => $state_id, 'travel_id' => $travel_id));
        return self::$db->fetchAll('obj');
    }

    public function getByStateTravelParkMap($state_id, $travel_id, $park_map_id)
    {
        $sql = "SELECT trips.*, po.park AS origin_name, pd.park AS destination_name, vt.name AS vehicle_name
                FROM trips
                JOIN park_map AS pm ON pm.id = trips.park_map_id
                JOIN parks AS po ON po.id = pm.origin
                JOIN parks AS pd ON pd.id = pm.destination
                JOIN vehicle_types vt ON vt.id = trips.vehicle_type_id
                WHERE trips.state_id = :state_id AND travel_id = :travel_id AND pm.id = :park_map_id
                ORDER BY vehicle_name";
        self::$db->query($sql, array('state_id' => $state_id, 'travel_id' => $travel_id, 'park_map_id' => $park_map_id));
        return self::$db->fetchAll('obj');
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
