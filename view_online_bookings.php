<?php
session_start();
require_once("includes/fns.php");
//define ("TRAVEL_ID", "1");
if(isset($_GET['count']) && intval($_GET['count'] != 0)){
	$count = $_GET['count'];
	}
	else{
		header("Location: sell_ticket.php");
		exit;
		}
docType();
printBanner();

	$result = $DB_CONNECTION->query("SELECT * FROM booking_details WHERE online = 'yes' AND status = 0 ORDER BY date_booked DESC LIMIT {$count}") or die (mysqli_error($DB_CONNECTION));
if ($result->num_rows == 0){
	header("Location: sell_ticket.php");
	}
?>
<div id="content">
<a href="util/local_update.php?count=<?php echo $count; ?>" class="btn btn-primary">Clear</a>
<br/><br/>
<style>
th, td {font: 11px Verdana}
</style>
<table class="table table-striped table-bordered">
<thead>
<tr>
	<th>Ticket no</th>
	<th>Route</th>
	<th>Date booked</th>
	<th>Travel date</th>
	<th>Name</th>
    <th>Seat no</th>
	<th>Phone no</th>
	<!--<th>Delivery Address</th>-->
	
	<th>Transaction</th>
	<th>Amount</th>
	<!--<th>Action</th>-->
</thead>
<tbody>
	
<?php
// Insert into offline database and display
if ($result->num_rows > 0) {
	while ($row = $result->fetch_object()) {
	if(!empty($row->route_id)){	
	$route_name = $DB_CONNECTION->query("SELECT * FROM routes WHERE id =".$row->route_id) or die (mysqli_error($DB_CONNECTION));
	if($route_name->num_rows == 1){
	$route_row = $route_name->fetch_object();
	}
	}
		echo "<tr><td>{$row->ticket_no}</td>
				  <td>"; ?>
				  <?php if(!empty($route_row)){ echo $route_row->route;} ?>
                  <?php echo"</td>
				  <td>" . date('M, d D', strtotime($row->date_booked)) . "</td>
				  <td>" . date('M, d D', strtotime($row->travel_date)) . "</td>
				  <td>{$row->c_name}</td>
				  <td>{$row->seat_no}</td>
				  <td>{$row->phone_no}</td>
				  
				
				  <td>{$row->payment_status}</td>
				  <td>₦ {$row->fare}</td>
			  </tr>";
	}
}
echo "</tbody></table>";

?>