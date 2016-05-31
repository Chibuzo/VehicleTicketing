<?php
require_once "ticket.class.php";

class Destination extends Ticket
{

    public $id, $park_map_id, $destination, $atatus;

    public function __construct()
    {
        parent::__construct();
    }


    public function addRoutes($park_map_id, $destination)
    {
        $sql = "INSERT INTO destination (park_map_id, destination) VALUES (:park_map_id, :destination)";
        $param = array('park_map_id' => $park_map_id, 'destination' => $destination);
        if (self::$db->query($sql, $param)) {
            return true;
        } else {
            throw new Exception ("Unable to add destinations");
        }
    }


    public function getRoutes()
    {
        self::$db->query("SELECT park_map_id, destination FROM destination ORDER by destination");
        if ($routes = self::$db->fetchAll('obj')) {
            return $routes;
        } else {
            throw new Exception ("Couldn't fetch routes");
        }
    }
}
?>
