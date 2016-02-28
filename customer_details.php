<?php
require "includes/DB_CONNECT.php";

$db = db_connect();

$sql = "SELECT DISTINCT next_of_kin_phone FROM booking_details
		WHERE c_name <> '' AND next_of_kin_phone <> '' AND next_of_kin_phone <> '0000' LIMIT 60001, 10000";
		
$result = $db->query($sql);


while($info = $result->fetch_assoc()) {
	echo $info['next_of_kin_phone']."<br />";
}


?>