<?php
require_once("../includes/DB_CONNECT.php");
require "../classes/customer.class.php";

$DB_CONNECTION = db_connect();
$customer = new Customer();

if ($_REQUEST['op'] == 'get_customer')
{
	echo json_encode($customer->getCustomer('cid', $_POST['cid']));
}
elseif ($_REQUEST['op'] == 'get_details')
{
	if ($_REQUEST['cid'] != '0')
		echo json_encode($customer->getCustomer($_POST['cid']));
	else {
		$result = $DB_CONNECTION->query("SELECT c_name, address, next_of_kin_phone FROM booking_details WHERE id = '{$_REQUEST['bd_id']}'");
		echo json_encode($result->fetch_assoc());
	}
}
?>