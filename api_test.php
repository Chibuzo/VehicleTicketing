<?php
include_once 'apicaller.php';

$apicaller = new ApiCaller('APP001', '28e336ac6c9423d946ba02d19c6a2632', 'http://localhost/travelhub/api/');

try {
	$todo_items = $apicaller->sendRequest(array(
		'controller' => 'route',
		'action' => 'get_routes'
	));
	
	var_dump($todo_items);
} catch (Exception $e) {
	echo $e->getMessage();
}

?>