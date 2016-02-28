<?php
require_once("includes/DB_CONNECT.php");
require_once("includes/fns.php");


//if     ($_REQUEST['op'] == 'reports') 	       generateManifest();
//elseif ($_REQUEST['op'] == 'get_loading_bus')  getLoadingBus();
//elseif ($_REQUEST['op'] == 'get_booked_buses') getBookedBuses();
elseif ($_REQUEST['op'] == 'print_manifest')   generatePrintManifest();
elseif ($_REQUEST['op'] == 're_print_ticket')  rePrintTicket();
elseif ($_REQUEST['op'] == 'remove_route')     removeRoute();
elseif ($_REQUEST['op'] == 'balance-sheet')    balanceManifest();
elseif ($_REQUEST['op'] == 'get-chart')	       getChart();
elseif ($_REQUEST['op'] == 'merge-route')      mergeRoute();
elseif ($_REQUEST['op'] == 'make-free-ticket') makeFreeTicket();
elseif ($_REQUEST['op'] == 'reopen-bus')       reopenBus();
elseif ($_REQUEST['op'] == 'get-merged-routes')displayMergedRoutes();
elseif ($_REQUEST['op'] == 'get-fare')	  echo getFare(TRAVEL_ID, $_GET['route'], $_GET['seating']);
elseif ($_REQUEST['op'] == 'update_customer_info') updateCustomerInfo();


function generateManifest() {
	global $DB_CONNECTION;

	$sql = "SELECT driver_name, bi.bus_no, num_of_seats, bb.travel_date, online_charge, offline_charge, drivers_phone_no
			FROM booked_buses AS bb
			JOIN seat_booking AS sb ON sb.bb_id = bb.id
			JOIN bus_info AS bi ON bi.id = bb.bus_id AND bb.travel_date = sb.travel_date
			WHERE bb.id = '{$_POST['bus_id']}'";
	$result = $DB_CONNECTION->query($sql) or die (mysqli_error($DB_CONNECTION));

	$details = $result->fetch_assoc();

	if (isset($details['bus_no'])) {
		echo "<blockquote><p style='font: 11px Verdana; color:#999; line-height:17px;'>
			Route: Lagos to " . getStateName($_POST['state_id']) . "<br />
			Driver's name: {$details['driver_name']}<br />
			Driver's phone number: {$details['drivers_phone_no']}<br />
			Bus number: {$details['bus_no']}<br />
			Date of travel: " . date('D d M Y', strtotime($details['travel_date'])) . "<br />
			{$details['num_of_seats']} seater bus
			<button type='button' style='float:right' data-target='#myModal' data-toggle='modal' class='btn'>Audit</button>
		</blockquote></p>";
	} else {
		echo "<p>No bus details yet</p>";

	}
	echo	"<table class='table table-striped table-bordered' style='width:75%; float:left; padding:0px'>
			<thead>
				<tr>
					<th style='width:45px'>Date</th>
					<th>Name</th>
					<th>Address</th>
					<th>Phone no</th>
					<th>Next of Kin</th>
					<th>Seat</th>
					<th>Ticket</th>
					<th>Cost</th>
					<th style='text-align:center' colspan='3'>Action</th>
					<th>Sold by</th>
				</tr>
			</thead>
			<tbody>";

			$result = $DB_CONNECTION->query("SELECT * FROM booking_details WHERE booked_id = '{$_POST['booked_id']}' ORDER BY seat_no");
			$num_of_tickets = $result->num_rows;
			$fare = 0; $service_charge = 0;
			while ($row = $result->fetch_assoc()) {
				#$customer = getCustomerDetails($row['cid']);
				#$c_name = !isset($customer['c_name']) ? $customer['c_name'] : $row['c_name'];
				#$next_of_kin_phone = !isset($customer['next_of_kin_phone']) ? $customer['next_of_kin_phone'] : $row['next_of_kin_phone'];
				$staff    = getStaffUsername($row['staff_id']);
				$_fare    = ($row['fare'] == 0) ? "Free" : $row['fare'];
				echo "<tr id='row_{$row['id']}'><td>" . date('d M', strtotime($row['date_booked'])) . "</td>
					<td>{$row['c_name']}</td>
					<td>{$row['address']}</td>
					<td>{$row['phone_no']}</td>
					<td>{$row['next_of_kin_phone']}</td>
					<td>{$row['seat_no']}</td>
					<td>{$row['ticket_no']}</td>
					<td data-target='#myTicketModal' class='free-ticket' data-route='{$row['route_code']}' data-seating_arrangement='{$details['num_of_seats']}' data-toggle='modal' id='{$row['id']}'>{$_fare}</td>
					<td style='text-align:center; width:19px'><a href='#' title='Print ticket' class='print-ticket' id='{$row['id']}'><img src='images/print.png' /></a></td>
					<td style='text-align:center; width:19px'><a href='#customerModal' title='Edit' data-toggle='modal' data-cid='{$row['cid']}' data-bd_id='{$row['id']}' class='edit-ticket'><img src='images/pencil.png' /></a></td>
					<td style='text-align:center; width:19px'><a href='#' title='Cancel ticket' class='cancel-ticket' id='{$row['id']}'><img src='images/cross.png' /></a></td>
					<td>$staff</td>
					</tr>";
				$fare += $row['fare'];
				if ($row['online'] == "Yes") {
					$service_charge += $row['fare'] * ($details['online_charge'] / 100);
				} else {
					$service_charge += $row['fare'] * ($details['offline_charge'] / 100);
				}
				//$ticket_cost = $row['fare'] != 0 ? $row['fare'] : continue;
			}
	echo "</tbody>\n</table>";

	/*** Get manifest's balance sheet ***/
	$result = $DB_CONNECTION->query("SELECT * FROM manifest_audit WHERE booked_id = '{$_POST['booked_id']}'");
	if ($result->num_rows > 0) {
		$audit = $result->fetch_object();
		$income = $fare;

		echo "<div class='audit_pane'><div><b>Balance Sheet</b></div><hr style='margin:8px 0px' />

				Tickets sold: {$num_of_tickets}<br />
				Transport income: ₦" . number_format($income) . "<br />
				Load: ₦" . number_format($audit->load_cost) . "<br />
				Expenses/Driver: ₦" . number_format($audit->drivers_expenses) . "<br />
				<!--Service charge: ₦" . number_format($service_charge) . "<hr style='margin:8px 0px' />-->
				Balance: ₦" . number_format(($income  + (int)$audit->load_cost) - (int)$audit->drivers_expenses) . "</div>
				<div class='audit_pane' style='border:0px'>
					<button id='reopen' class='btn btn-primary btn-large btn-block' data-booked_id='{$_POST['booked_id']}'>Reopen this bus</button>
				</div>";
	} else {
		echo "<div class='audit_pane'>No details found</div>";
	}
}

function generatePrintManifest() {
	global $DB_CONNECTION;

	$sql = "SELECT bi.id, driver_name, bi.bus_no, num_of_seats, bb.travel_date, online_charge, offline_charge, drivers_phone_no
			FROM booked_buses AS bb
			JOIN seat_booking AS sb ON sb.bb_id = bb.id
			JOIN bus_info AS bi ON bi.id = bb.bus_id AND bb.travel_date = sb.travel_date
			WHERE bb.id = '{$_REQUEST['bus_id']}'";
	$result = $DB_CONNECTION->query($sql);

	$details = $result->fetch_assoc();

	# Get manifest Serial number
	$serial_no = '';
	$result = $DB_CONNECTION->query("SELECT serial_no FROM manifest_serial_no WHERE booked_id = '{$_GET['booked_id']}'");
	if ($result->num_rows > 0)
		$serial_no = $result->fetch_object()->serial_no;

	echo "<div class='head'>" . TRAVEL_NAME . "<br />
	<span>Tel: 08070591840 - 895</span><span style='float:right;font-size:16px'>E - {$serial_no} &nbsp;</span></div><br />";

	if (isset($details['id'])) {
		echo "<p>
			Route: Lagos to " . getStateName($_GET['state_id']) . "<br />
			Driver's name: {$details['driver_name']}<br />
			Driver's phone number: {$details['drivers_phone_no']}<br />
			Bus number: {$details['bus_no']}<br />
			Date of travel: " . date('D d M Y', strtotime($details['travel_date'])) . "<br />

		</p>";
	}

	echo	"<table cellpadding='10' cellspacing='10' style='border-collapse:collapse; width:73%; float:left; font-size:12px' border='1'>
			<thead>
				<tr>
					<th>S/NO</th>
					<th>Customer's name</th>
					<th>Address</th>
					<th>Phone number</th>
					<th>Next of Kin no</th>
					<th>Seat no</th>
					<!--<th>Ticket Number</th>-->
					<th>Cost</th>
				</tr>
			</thead>
			<tbody>";

			$result = $DB_CONNECTION->query("SELECT * FROM booking_details WHERE booked_id = '{$_GET['booked_id']}'");
			$num_of_tickets = $result->num_rows;
			$n = 1; $fare = 0; $service_charge = 0;
			while ($row = $result->fetch_assoc()) {
				$customer = getCustomerDetails($row['cid']);
				$staff = getStaffUsername($row['staff_id']);
				if ($row['online'] == "Yes") {
					$_fare = $row['fare'];
					$service_charge += $row['fare'] * ($details['online_charge'] / 100);
				} else {
					$_fare = $row['fare'];
					$service_charge += $row['fare'] * ($details['offline_charge'] / 100);
				}
				echo "<tr><td>{$n}</td>
					<td>{$row['c_name']}</td>
					<td>{$row['address']}</td>
					<td>{$row['phone_no']}</td>
					<td>{$row['next_of_kin_phone']}</td>
					<td style='text-align:center'>{$row['seat_no']}</td>
					<!--<td>{$row['ticket_no']}</td>-->
					<td>$_fare</td>
					</tr>";
				$fare += $row['fare'];
				//$ticket_cost = $row['fare'];
				$n++;
			}
	echo "</tbody>\n</table>";

	/*** Get manifest's balance sheet ***/
	$result = $DB_CONNECTION->query("SELECT * FROM manifest_audit WHERE booked_id = '{$_GET['booked_id']}'");
	if ($result->num_rows > 0) {
		$audit = $result->fetch_object();
		$income = $fare;

		echo "<div class='audit-pane'><div><b>Balance Sheet</b></div><hr style='margin:8px 0px' />

				Tickets sold: {$num_of_tickets}<br />
				Transport income: ₦" . number_format($income) . "<br />
				Load: ₦" . number_format($audit->load_cost) . "<br />
				Expenses/Driver: ₦" . number_format($audit->drivers_expenses) . "<br />
				<!--Service Charge: ₦" . number_format($service_charge) . "<hr style='margin:8px 0px' />-->
				Balance: ₦" . number_format(($income  + (int)$audit->load_cost) - (int)$audit->drivers_expenses) . "</div>";
	} else {
		echo "<div class='audit-pane'>No details found</div>";
	}

	echo "<div id='signature'><span><hr />Driver's Signature</span><span style='float:right'><hr />Manager's Signature</span></div>";
}


function getCustomerDetails($cid) {
	global $DB_CONNECTION;

	$result = $DB_CONNECTION->query("SELECT * FROM customers WHERE cid = '$cid'");
	return $result->fetch_assoc();
}

//  Close bus
function balanceManifest() {
	global $DB_CONNECTION;
	$query_check = true;

	$result = $DB_CONNECTION->query("SELECT booked_id FROM manifest_audit WHERE booked_id = '{$_POST['booked_id']}'");
	$DB_CONNECTION->query("START TRANSACTION");
	if ($result->num_rows > 0) {
		$sql = "UPDATE manifest_audit
				SET    load_cost = '{$_POST['load']}', drivers_expenses = '{$_POST['drivers_expenses']}'
				WHERE  booked_id = '{$_POST['booked_id']}'";
		$DB_CONNECTION->query($sql) ? null : $query_check = false;
	} else {
		$sql = "INSERT INTO manifest_audit (booked_id, load_cost, drivers_expenses, travel_id)
				VALUES ('{$_POST['booked_id']}', '{$_POST['load']}', '{$_POST['drivers_expenses']}', '" . TRAVEL_ID . "')";
		$DB_CONNECTION->query($sql) ? null : $query_check = false;
	}

	# Mark the closed bus as full
	$DB_CONNECTION->query("UPDATE seat_booking SET seat_status = 'Full' WHERE booked_id = '{$_POST['booked_id']}'") ? null : $query_check = false;

	# If this bus was merged, then mark it as full in the merge table
	$DB_CONNECTION->query("UPDATE merged_routes SET seat_status = 'Full' WHERE going_booked_id = '{$_POST['booked_id']}'");

	# Generate manifest seria number
	$result = $DB_CONNECTION->query("SELECT serial_no FROM manifest_serial_no WHERE booked_id = '{$_POST['booked_id']}'");
	if ($result->num_rows == 0) { // If this bus has no serial number, then assign
		$result = $DB_CONNECTION->query("SELECT serial_no FROM manifest_serial_no ORDER BY serial_no DESC LIMIT 1");
		if ($result->num_rows > 0) {
			$serial_no = (int) $result->fetch_object()->serial_no;
			++$serial_no;
			$serial_no = str_pad($serial_no, 6, '0', STR_PAD_LEFT);
			$sql = "INSERT INTO manifest_serial_no (booked_id, serial_no) VALUES ('{$_POST['booked_id']}', '$serial_no')";
			$DB_CONNECTION->query($sql) or die (mysqli_error($DB_CONNECTION));// ? null : $query_check = false;
		}
	}

	if ($query_check == true) {
		$DB_CONNECTION->query("COMMIT");
	} else {
		$DB_CONNECTION->query("ROLLBACK");
	}
	return;
}

function getChart() {
	global $DB_CONNECTION;

/*** Get all the booked buses for this date ***/
	$sql = "SELECT * FROM seat_booking
			WHERE  booked_seats <> '' AND travel_date = '{$_POST['_date']}'
			ORDER BY route_code";
	$result = $DB_CONNECTION->query($sql);

	# Get merging details [ if there was a merge ]
	$merge_details = getMergedDetails($_POST['_date']);

	$html = ''; $merged_html_details = '';
	while ($row = $result->fetch_assoc()) {
		$bln_merged = false;
		for ($i = 0; $i < count(@$merge_details['merging_booked_id']); $i++) {
			if ($merge_details['merging_booked_id'][$i] == $row['booked_id']) {
				$bln_merged = true;
				break;
			}
		}

		//$merged_html_details .= "$destination is merged with $going_bus_route<hr />";
		if ($bln_merged === true) continue;

		$state = splitRouteMap($row['route_code']);
		$booked_seats         = explode(",", $row['booked_seats']);
		$available_seats      = $row['seating_arrangement'] - count($booked_seats);

		$html .= "<tr><td>{$state['destination']}</td>
			<td>{$row['seating_arrangement']} seater</td>
			<td> Bus {$row['departure_order']}</td>
			<td>" . count($booked_seats) . "</td>
			<td>{$available_seats}</td>
			<td style='text-align:center'>
			  <input type='checkbox' class='merge' data-bus_no='{$row['bus_no']}' data-seating_arrangement='{$row['seating_arrangement']}' data-destination='{$state['destination']}' value='{$row['booked_id']}' />
			</td></tr>";
	}
	echo $html;
}


function getMergedDetails($date) {
	global $DB_CONNECTION;

	$sql = "SELECT booked_ids, going_route, merged_route FROM merged_routes WHERE travel_date = '$date'";
	$merged = $DB_CONNECTION->query($sql);
	$merge_details = array();

	if ($merged->num_rows > 0) {
		while ($merge = $merged->fetch_object()) {
			$booked_ids         = explode(',', $merge->booked_ids);
			$merge_details['going_route'][]       = $merge->going_route;
			$merge_details['destination'][]       = $merge->merged_route;
			$merge_details['merging_booked_id'][] = $booked_ids[1];
			$merge_details['going_booked_id'][]   = $booked_ids[0];
		}
	}
	return $merge_details;
}


function displayMergedRoutes() {
	$html = '<ul>';
	$details = getMergedDetails($_GET['_date']);
	for ($i = 0; $i < count(@$details['going_route']); $i++) {
		$html .= "<li><b>{$details['going_route'][$i]}</b> is merged with <b>{$details['destination'][$i]}</b></li><br />";
	}
	echo $html . "</ul>";
}

#########################################################################################
#
#	$_POST['going_booked_id'] is the booked id of the going bus
#   $_POST['merging_booked_id'] is the booked id of the merge added/merged to the going bus
#
function mergeRoute() {
	global $DB_CONNECTION;

	if (!isset($_POST['going_booked_id'], $_POST['merging_booked_id'])) die ("Incomplete merging details...");

/*** Get booked seats ***/
	$result = $DB_CONNECTION->query("SELECT booked_seats, travel_date FROM seat_booking WHERE booked_id = '{$_POST['going_booked_id']}'");
	$booked_seats1 = $result->fetch_object();
	$seats1 = explode(',', $booked_seats1->booked_seats);
	$num_of_going_booked_seats = count($seats1);
	$travel_date = $booked_seats1->travel_date;

	$result = $DB_CONNECTION->query("SELECT booked_seats FROM seat_booking WHERE booked_id = '{$_POST['merging_booked_id']}'");
	$booked_seats2 = $result->fetch_object();
	$seats2 = explode(',', $booked_seats2->booked_seats);
	$num_of_merging_booked_seats = count($seats2);

/*** Merge the seats and re-assign seats for intersected seats [ ie, where a seat number was picked in both buses ] if any ***/
	$total_num_of_merged_seats = $num_of_going_booked_seats + $num_of_merging_booked_seats;

	# Find number of intersects (ie where the same seat number was booked on both buses)
	$num_of_intercepts = count(array_intersect($seats1, $seats2));

	# Merge the seats, and remove duplicates
	$booked_seats = array_unique(array_merge((array)$seats1, (array)$seats2));

	# START DATABASE TRANSACTION HERE
	$DB_CONNECTION->query("START TRANSACTION");
	$query_check = true;
	if ($num_of_intercepts > 0) {
		# Get free (unbooked) seats
		for ($i = 1; $i < $_POST['seating_arrangement'] + 1; $i++) {
			if (in_array($i, $booked_seats)) continue;
			$free_seats[] = $i;
		}

		# Randomly pick new seat number for the intercepted ones
		$new_seats = array_rand($free_seats, $num_of_intercepts);
		if (is_array($new_seats)) {
			foreach ($new_seats AS $i) {
				$re_picked_seats[] = $free_seats[$i];
			}
		} else {
			$re_picked_seats = $free_seats[$new_seats];
		}
		$merged_seats  = array_merge((array)$booked_seats, (array)$re_picked_seats);
		$_merged_seats = implode(',', $merged_seats);
		$sql = "UPDATE seat_booking SET booked_seats = '$_merged_seats' WHERE booked_id = '{$_POST['going_booked_id']}'";
		$DB_CONNECTION->query($sql) ? null : $query_check = false;
	} else {
		$_booked_seats = implode(',', $booked_seats);
		$sql = "UPDATE seat_booking SET booked_seats = '$_booked_seats' WHERE booked_id = '{$_POST['going_booked_id']}'";
		$DB_CONNECTION->query($sql) ? null : $query_check = false;
	}

	# Remove the merged bus from record
	$DB_CONNECTION->query("DELETE FROM seat_booking WHERE booked_id = '{$_POST['merging_booked_id']}'") ? null : $query_check = false;

	$sql = "UPDATE booking_details
			SET    booked_id = '{$_POST['going_booked_id']}',
				   bus_no    = '{$_POST['bus_no']}'
			WHERE  booked_id = '{$_POST['merging_booked_id']}'";
	$DB_CONNECTION->query($sql) ? null : $query_check = false;

/*** Record merging details ***/
	if ($_POST['seating_arrangement'] > $total_num_of_merged_seats) {
		$seat_status = "Not Full";
	} elseif ($num_of_going_booked_seats >= $_POST['seating_arrangement']) {
		$left_over_seats = $total_num_of_merged_seats - $_POST['seating_arrangement'];
		$seat_status = "Full";
	}

	$booked_ids = $_POST['going_booked_id'] . ',' . $_POST['merging_booked_id'];
	$sql = "INSERT INTO merged_routes (booked_ids, seat_status, seating_arrangement, going_booked_id, going_route, merged_route, travel_date)
			VALUES ('$booked_ids', '$seat_status', '{$_POST['seating_arrangement']}', '{$_POST['going_booked_id']}', '{$_POST['going_route']}', '{$_POST['merged_route']}', '$travel_date')";
	$DB_CONNECTION->query($sql) ? null : $query_check = false;

	if ($query_check == true) {
		$DB_CONNECTION->query("COMMIT");
	} else {
		$DB_CONNECTION->query("ROLLBACK");
	}
	echo "done";
	return;
}


function reopenBus() {
	global $DB_CONNECTION;

	$result = $DB_CONNECTION->query("SELECT booked_seats, seating_arrangement FROM seat_booking WHERE booked_id = '{$_POST['booked_id']}'");
	if ($result->num_rows > 0) {
		$data = $result->fetch_object();
		$num_of_seats = count(explode(",", $data->booked_seats));
		if ($num_of_seats == $data->seating_arrangement) {
			echo "This bus is full, you cannot reopen it";
			return;
		} else {
			$query_check = true;
			$DB_CONNECTION->query("START TRANSACTION");
			$sql = "UPDATE seat_booking SET seat_status = 'Not full' WHERE booked_id = '{$_POST['booked_id']}'";
			$DB_CONNECTION->query($sql) ? null : $query_check = false;
			$DB_CONNECTION->query("DELETE FROM manifest_audit WHERE booked_id = '{$_POST['booked_id']}'") ? null : $query_check = false;

			# If this bus was merged, then mark it as not full in the merge table
			$sql = "UPDATE merged_routes SET seat_status = 'Not full' WHERE going_booked_id = '{$_POST['booked_id']}'";
			$DB_CONNECTION->query($sql) ? null : $query_check = false;

			# Remove manifest seria number
			#$sql = "DELETE FROM manifest_serial_no WHERE booked_id = '{$_POST['booked_id']}'";
			#$DB_CONNECTION->query($sql) ? null : $query_check = false;

			if ($query_check == true) {
				$DB_CONNECTION->query("COMMIT");
				echo "Done";
			} else {
				$DB_CONNECTION->query("ROLLBACK");
			}
			return;
		}
	}
}


function updateCustomerInfo() {
	global $DB_CONNECTION;

	$sql = "UPDATE booking_details SET
				c_name = '{$_POST['c_name']}',
				next_of_kin_phone = '{$_POST['next_of_kin_no']}'
			WHERE id = '{$_POST['bd_id']}'";
	$DB_CONNECTION->query($sql);

	$sql = "UPDATE customers SET
				c_name = '{$_POST['c_name']}',
				next_of_kin_phone = '{$_POST['next_of_kin_no']}',
				address = '{$_POST['address']}'
			WHERE id = '{$_POST['cid']}'";
	$DB_CONNECTION->query($sql);
}


function getBookedBusDetails($id) {
	global $DB_CONNECTION;

	$sql = "SELECT bi.num_of_seats, bi.driver_name, bi.drivers_phone_no, bi.bus_no, bi.bus_name, bb.route_code, bb.travel_date, bb.departure_order
			FROM booked_buses AS bb
			JOIN bus_info AS bi ON bb.bus_id = bi.id WHERE bb.id = '$id'";
	$result = $DB_CONNECTION->query($sql);
	$details = $result->fetch_assoc();
	$route = splitRouteMap($details['route_code']);
	return array_merge($details, array('destination' => $route['destination']));
}

function makeFreeTicket() {
	global $DB_CONNECTION;

	$DB_CONNECTION->query("UPDATE booking_details SET fare = '{$_POST['free_ticket']}' WHERE id = '{$_POST['booking_details_id']}'");
	return;
}

?>
