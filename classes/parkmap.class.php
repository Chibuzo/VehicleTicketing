<?php
require_once "parkmodel.class.php";

class ParkMap extends ParkModel
{

    public $id, $travel_id, $route_id, $status;
    protected static $tbl = "travel_park_map";

    public function __construct()
    {
        parent::__construct();
    }


    public function addParkMap($origin, $destination, $travel_id)
    {
        $park_map_id = parent::addParkMap($origin, $destination);

        // check if the route has been added already
        if (is_numeric($this->verifyParkMap($park_map_id, $travel_id))) {
            return true;
        }

        $sql = "INSERT INTO " . self::$tbl . " (travel_id, park_map_id) VALUES (:travel_id, :park_map_id)";
        $param = array(
            'travel_id' =>$travel_id,
            'park_map_id' => $park_map_id
        );
        if (self::$db->query($sql, $param)) {
            return self::$db->getLastInsertId();
        }
    }


    /**
     * Needs review. Returns all the routes from a park
     *
     * @param $travel_id, $park_id
     */
    public function getRoutes($park_id)
    {
        $sql = "SELECT pm.id park_map_id, d_s.state_name as destination_state
                FROM park_map AS pm
                INNER JOIN parks AS d ON pm.destination = d.id
                INNER JOIN parks AS o ON pm.origin = o.id
                INNER JOIN states AS state ON o.state_id = state.id
                INNER JOIN states AS d_s ON d.state_id = d_s.id
                WHERE o.id = :park_id
                ORDER BY d_s.state_name";

        self::$db->query($sql, array('park_id'=> $park_id));
        return self::$db->fetchAll('obj');
    }


    /**
     * Returns all the park_maps for a route
     *
     * @param $origin - state_id for the originating state
     * @param $destination - state_id for the destination state
     * @return mixed
     */
    public function getParkMapByRoute($origin, $destination)
    {
        $sql = "SELECT pm.*, d.park AS destination_name, o.park AS origin_name, d_s.state_name AS destination_state, o_s.state_name AS origin_state
                FROM park_map AS pm
                INNER JOIN travel_park_map ON travel_park_map.park_map_id = pm.id
                INNER JOIN parks AS d ON pm.destination = d.id
                INNER JOIN parks AS o ON pm.origin = o.id
                INNER JOIN states AS o_s ON o.state_id = o_s.id
                INNER JOIN states AS d_s ON d.state_id = d_s.id
                WHERE o_s.id = :origin AND d_s.id = :destination";
        self::$db->query($sql, array('origin' => $origin, 'destination'=> $destination));
        return self::$db->fetchAll('obj');
    }

    /**
     * Returns the route for the given park_map
     *
     * @param $park_map_id
     * @return mixed
     */
    public function getRoute($park_map_id)
    {
        $sql = "SELECT routes.* FROM routes
                INNER JOIN parks po ON po.state_id = routes.origin
                INNER JOIN parks pd ON pd.state_id = routes.destination
                INNER JOIN park_map pm ON pm.origin = po.id AND pm.destination = pd.id
                WHERE pm.id = :park_map_id";
        self::$db->query($sql, array('park_map_id' => $park_map_id));
        return self::$db->fetch('obj');
    }

    public function verifyParkMap($park_map_id, $travel_id)
    {
        $sql = "SELECT id FROM " . self::$tbl . " WHERE park_map_id = :park_map_id AND travel_id = :travel_id";
        $param = array(
            'travel_id' => $travel_id,
            'park_map_id' => $park_map_id
        );
        self::$db->query($sql, $param);
        if ($id = self::$db->fetch('obj')) {
            return $id->id;
        }
    }


    public function disableRoute($route_id, $travel_id)
    {
        $sql = "UPDATE " . self::$tbl . " SET status = '0' WHERE route_id = :route_id AND travel_id = :travel_id";
        if (self::$db->query($sql, array('route_id' => $route_id, 'travel_id' => $travel_id))) {
            return true;
        }
    }


    public function removeRoute($route_id, $travel_id)
    {
        $sql = "UPDATE " . self::$tbl . " SET removed = '1' WHERE route_id = :route_id AND travel_id = :travel_id";
        if (self::$db->query($sql, array('route_id' => $route_id, 'travel_id' => $travel_id))) {
            return true;
        }
    }
}
?>
