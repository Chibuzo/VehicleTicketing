<?php
require_once "ticket.class.php";

class Destination extends Ticket
{

    public $id, $park_map_id, $destination, $destination_park_id, $atatus;

    public function __construct()
    {
        parent::__construct();
    }


    public function addRoute($park_map_id, $destination, $destination_park_id)
    {
        $sql = "INSERT INTO destination (park_map_id, destination, destination_park_id) VALUES (:park_map_id, :destination, :destination_park_id)";
        $param = array('park_map_id' => $park_map_id, 'destination' => $destination, 'destination_park_id' => $destination_park_id);
        if (self::$db->query($sql, $param)) {
            return true;
        } else {
            throw new Exception ("Unable to add destinations");
        }
    }


    public function getRoutes()
    {
        self::$db->query("SELECT d.id, park_map_id, destination, park FROM destination d JOIN parks p ON d.destination_park_id = p.id ORDER by destination");
        if ($routes = self::$db->fetchAll('obj')) {
            return $routes;
        } else {
            throw new Exception ("Couldn't fetch routes");
        }
    }
}
?>
