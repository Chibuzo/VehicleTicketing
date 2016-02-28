<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/terminal/classes/db.class.php";

class Ticket
{
	//protected $db;
	protected static $db;
	protected static $offline_db;

	protected function __construct()
	{
		self::$db = new db(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD);
		self::$offline_db = new db(OFFLINE_DB_HOST, OFFLINE_DB_NAME, OFFLINE_DB_USERNAME, OFFLINE_DB_PASSWORD);
	}


	public static function loadTravelDetails()
	{
		self::$offline_db = new db(OFFLINE_DB_HOST, OFFLINE_DB_NAME, OFFLINE_DB_USERNAME, OFFLINE_DB_PASSWORD);

		self::$offline_db->query("SELECT * FROM ticket.travels");
		if ($travel = self::$offline_db->fetch('obj')) {
			$state = explode("-", $travel->state);
			$park = explode("-", $travel->park);

			$_SESSION['travel'] = $travel->travel_name;
			$_SESSION['abbr'] = $travel->abbr;
			$_SESSION['travel_id'] = $travel->travel_id;
			$_SESSION['state_name'] = $state[0];
			$_SESSION['state_id'] = $state[1];
			$_SESSION['park_name'] = $park[0];
			$_SESSION['park_id'] = $park[1];
			$_SESSION['phone'] = $travel->phone_nos;
		} else {
			return false; // not installed yet;
		}
	}


	public function getOneById($tbl, $id)
	{
		$sql = "SELECT * FROM {$tbl} WHERE id = :id";
		self::$db->query($sql, array('id' => $id));
		return self::$db->fetch('obj');
	}


	public function getAll($tbl, $orderby = null)
	{
		if ($orderby != null) {
			$orderby = "ORDER BY " . $orderby;
		}
		//if ($where != null) {
		//	$where = "WHERE "
		//}
		$sql = "SELECT * FROM {$tbl} {$orderby}";
		self::$db->query($sql);
		return self::$db->fetchAll('obj');
	}


	public function getManyById($tbl, $id_field, $id)
	{
		$sql = "SELECT * FROM {$tbl} WHERE {$id_field} = :id";
		self::$db->query($sql, array('id' => $id));
		return self::$db->fetchAll('obj');
	}


	public function getNumRows($tbl_name, $where = null)
	{
		if ($where === null) $where = "";

		self::$db->query("SELECT COUNT(*) AS num_rows FROM {$tbl_name} {$where}");
		$result = self::$db->fetch('obj');
		return $result->num_rows;
	}


	public static function ordinal($number) {
		$ends = array('th','st','nd','rd','th','th','th','th','th','th');
		if ((($number % 100) >= 11) && (($number%100) <= 13))
			return $number. 'th';
		else
			return $number. $ends[$number % 10];
	}
}

?>
