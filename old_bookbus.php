<?php
require "includes/head.php";
require "includes/side-bar.php";
require "classes/route.class.php";
require "classes/bus.class.php";

$bus = new Bus();
$route = new Route();
?>
<link href="css/datepicker.css" rel="stylesheet" />
<link href="css/datepicker3.css" rel="stylesheet" />
<style>
.alert { display: none; }
iframe#receipt {clear:both; width:280px; dsplay:none; height:300px; border:#ccc solid; }
</style>
<div class="content-wrapper">
  	<section class="content-header">
		<h1>
			Book Vehicle
			<small>Assign Vehicle for next trip</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="active">Book Vehicle</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-warning">
					<div class="box-header with-border">
						<h2 style='font-size: 18px' class="box-title"><i class="fa fa-bus"></i> &nbsp;Add Vehicle</h2>
					</div>
					<div class="box-body">
						<div id="b_side">
							<div class="alert alert-error">
								<button type="button" class="close" data-dismiss="alert">X</button>
								Form not completely filled out.
							</div>
							<form action="" method="get" id="book_bus">
								<div class="control-group">
									<label class="control-label">Select Bus Type</label>
									<select name="num_of_seats" class="form-control">
										<option value="">-- Select bus type --</option>
									<?php
										/*** Auto select bus position onloading ***/

										foreach ($bus->getAllBusTypes() AS $b) {
											echo "\t<option value='{$b->num_of_seats}'>{$b->name} - {$b->num_of_seats} Seater</option>\n";
										}
										echo "</select></div>";

										// get destination
										$destination = '';
										foreach ($route->getDestination('Lagos') AS $r) {
											$destination .= "<option value='{$r->id}'>{$r->destination}</option>";
										}
										echo "<div class='form-group'><label class='control-label'>Going to...[ tomorrow ]</label>
											<select name='route_code' class='form-control'>\n
											\t<option value=''>-- Going to --</option>\n";

										echo $destination;
									?>
								</select></div>
							</form>
						</div>

	<div class='side' id="d_side" style='width:520px; float:right'>
		<form action="" method="post">
			<label style='float:left; display:inline'>Date of travel</label>
			<input name="travel_date" id="tdate" type="text" value="<?php echo @$_POST['travel_date']; ?>" style='width:110px; margin-top:-5px; margin-left:5px' />
			<input type="submit" class="btn btn-primary" name="submit" value="Show" style="margin-top:-14px" />
		</form>
		<div class="alert alert-success hide">Tickets successfully generated</div>
		<table class='table table-striped table-bordered' style="font: 11px Verdana">
			<thead>
				<tr>
					<th style="text-align:center; width:40px">Route</th>
					<th style="text-align:center">Bus type</th>
					<th style="text-align:center">Driver's name</th>
					<th style="text-align:center">Driver's no</th>
					<th style="text-align:center">Bus no</th>
					<th style="text-align:center">Status</th>
					<th style='text-align:center' colspan="3">Option</th>
				</tr>
			</thead>
			<tbody>
		<?php
			if (isset($_POST['submit'])) $date = $_POST['travel_date'];
			else $date = date('Y-m-d');
			$result = $DB_CONNECTION->query("SELECT bb.id, bb.no_of_seats, bb.drivers_name, bb.drivers_phone_no, bb.bus_no, bb.seat_status, booked_id, route FROM booked_buses AS bb JOIN seat_booking AS sb ON bb.id = sb.bus_id JOIN routes ON bb.route_code = routes.route_code WHERE bb.travel_date = '$date'");
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					// Check if tickets has been printed
					$result1 = $DB_CONNECTION->query("SELECT seat_status FROM seat_booking WHERE booked_id = '{$row['booked_id']}'");
					$seat_status = $result1->fetch_object()->seat_status;
					echo "<tr><td>{$row['route']}</td>
							<td>{$row['no_of_seats']} seater</td>
							<td>{$row['drivers_name']}</td>
							<td>{$row['drivers_phone_no']}</td>
							<td>{$row['bus_no']}</td>
							<td>{$row['seat_status']}</td>
							<td style='width:15px'><a href='' class='remove-bus' data-seating_arrangement='{$row['no_of_seats']}' data-bus_id='{$row['id']}'><img src='../images/cross.png' /></a></td>";
							if ($seat_status == "Not full") {
								echo "<td style='text-align:center'><a href='#' title='Print tickets' class='print-tickets' id='{$row['id']}' data-booked_id='{$row['booked_id']}'>Tickets</a></td>";
							} else echo "<td></td>";
							echo "<td style='text-align:center'><a href='quick_manifest.php?id={$row['id']}' title='Print manifest' class='write-manifest'>Manifest</a></td>
						</tr>";
				}
			}
		?>
			</tbody>
		</table>
	</div>
</div>


<script type="text/javascript" src="js/book_bus.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	/*** Book bus ***/
	$('#book_bus').submit(function(e) {
		e.preventDefault();
		var bln_validate = true;
		var data = $(this).serialize();
		var route_code = $('select[name=route_code] option:selected').data('route_code');

		// Validate form inputs
		$.each($(this).serializeArray(), function(i, val) {
			if (val.value.length == 0) {
				$('.alert').fadeIn();
				$('[name=' + val.name + ']').focus();
				bln_validate = false;
				return false;
			}
		});

		if (bln_validate === false) return false;

		$.post('ajax.php', data + '&op=book_bus&route_code=' + route_code, function(d) { //alert(d);
			if (d.trim() == "done") {
				$('#b_side > .alert').removeClass('alert-error').addClass('alert-success')
				.html('Bus booking successful.').fadeIn('fast').fadeOut(5000);
				$('button[type=reset]').click();
			}
			else
				$('.alert').removeClass('alert-success').addClass('alert-error')
				.html('<button type="button" class="close" data-dismiss="alert">X</button>' + d).fadeIn('fast');
		});
	});

	/*** Remove a booked bus ***/
	$('.remove-bus').click(function(e) {
		e.preventDefault();
		var bus_id              = $(this).data('bus_id');
		var seating_arrangement = $(this).data('seating_arrangement');
		var seat_status = '';
		if (seating_arrangement == 15 || seating_arrangement == 60) {
			--seating_arrangement;
			seat_status = "Not full";
		} else seat_status = "Full";
		if (confirm("Are you sure you want to remove this bus?")) {
			$.post('ajax.php', {'op':'remove_bus', 'bus_id':bus_id, 'seating_arrangement':seating_arrangement, 'seat_status':seat_status}, function(d) {
				location.href = location.href;
			});
		}
	});
});
</script>
