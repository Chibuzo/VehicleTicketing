<?php
require "includes/head.php";
require "includes/side-bar.php";
require "classes/vehiclemodel.class.php";
require "classes/destination.class.php";

$destination = new Destination();
$vehicle = new VehicleModel();
$vehicles = array();
foreach ($vehicle->getAllRegisteredVehicles() AS $v) {
	$vehicles[] = $v->vehicle_no;
}
?>
<link href="css/datepicker.css" rel="stylesheet" />
<link href="css/datepicker3.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="css/jquery-ui.min.css" media="all" />
<style>
iframe#receipt {
	clear:both;
	width:280px;
	display:none;
	height:300px;
	border:#ccc solid;
}

#book_vehicle_controls, .alert {
	display: none;
}

#report { margin-top: 12px; }
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
			<div class="col-md-3">
				<div class="box box-warning">
					<div class="box-header with-border">
						<h2 style='font-size: 18px' class="box-title"><i class="fa fa-vehicle"></i> &nbsp;Add Vehicle</h2>
					</div>
					<div class="box-body">
						<div id="b_side">
							<div class="alert alert-error">
								Form not completely filled.
							</div>

							<form method="post" id="book_vehicle" class=''>
								<div class="input-group form-group">
									<input type="text" id="vehicle_no" name="vehicle_no" placeholder="vehicle Number" class="form-control" />
									<span class="input-group-btn">
										<button type="submit" class="btn btn-primary" id="verify">Verify</button>
									</span>
								</div>

								<div id="book_vehicle_controls">
									<div class="form-group">
										<select name='route_id' class="form-control">
											<option value="">-- Destination --</option>
											<?php
												// get destination
												foreach($destination->getRoutes() AS $dest) {
													echo "\t<option value='{$dest->park_map_id}'>{$dest->destination}</option>\n";
												}
											?>
										</select>
									</div>

									<div class="form-group">
										<input name="travel_date" id="t_date" type="text" class="date form-control" placeholder="Pick travel date" />
									</div>

									<div class="form-group">
										<select name="departure_order" class="form-control">
											<option value="">Auto select [ vehicle position ]</option>
											<option value="1">First vehicle</option>
											<option value="2">Second vehicle</option>
											<option value="3">Third vehicle</option>
											<option value="4">Fourth vehicle</option>
											<option value="5">Fifth vehicle</option>
											<option value="6">Sixth vehicle</option>
											<option value="7">Seventh vehicle</option>
											<option value="8">Eight vehicle</option>
											<option value="9">Ninth vehicle</option>
											<option value="10">Tenth vehicle</option>
										</select>
									</div>
									<!--<input type="hidden" name="vehicle_id" id="vehicle_id" />-->

									<input type="submit" value="Book vehicle" class="btn btn-primary btn-block" />
									<input type="reset" class="hidden" id="reset" />
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-9">
				<div class="box box-warning">
					<div class="box-header with-border">
						<h2 style='font-size: 18px' class="box-title"><i class="fa fa-vehicle"></i> &nbsp;Booked Vehicle</h2>
					</div>
					<div class="box-body">
						<div id="d_side">
							<form action="" method="post" class="form-horizontal">
								<div class="input-groupform-group">
									<label class="pull-left">Travel date</label>
									<div class="col-sm-3">
										<input name="traveldate" class="form-control date" id="tdate" type="text" value="<?php echo isset($_POST['traveldate']) ? $_POST['traveldate'] : ''; ?>" />
									</div>
									<span class="input-group-btn">
										<input type="submit" class="btn btn-primary" name="submit" value="Display" />
									</span>
								</div>

								<!-- Clean table -->
								<button class="btn btn-danger btn-large hidden" id="clean-db" style="float:right"><i class="icon-exclamation-sign icon-white"></i> Clean Database</button>
							</form>

							<div id="report">
								<table class='table table-striped table-bordered' style="font: 11px Verdana">
									<thead>
										<tr>
											<th>Route</th>
											<th>vehicle type</th>
											<th>Driver's name</th>
											<th style="width:100px">Driver's no</th>
											<th style="width:80px">vehicle no</th>
											<th style="width:70px">Status</th>
											<th style='text-align:center' colspan="3">Option</th>
										</tr>
									</thead>
									<tbody id='tb'>
							<?php
								if (isset($_POST['submit'])) $date = $_POST['traveldate'];
								else $date = date('Y-m-d');
								// get booked vehicle

								$booked_vehicles = $vehicle->getBookedVehicles($date);
								if (count($booked_vehicles) > 0) {
									foreach ($booked_vehicles AS $row) {
										if (empty($row['seat_status'])) $status = "Not full";
										else $status = $row['seat_status'];
										echo "<tr id='{$row['bbv_id']}' data-vehicle_id='{$row['vehicle_id']}' data-boarding-vehicle-id='{$row['boarding_vehicle_id']}' data-departure_order='{$row['departure_order']}' data-route_id='{$row['route_id']}' data-vehicle_type_id='{$row['vehicle_type_id']}'>
												<td>{$_SESSION['state_name']} - {$row['destination']}</td>
												<td>{$row['vehicle_type']}</td>
												<td>{$row['driver_name']}</td>
												<td>{$row['drivers_phone']}</td>
												<td>{$row['vehicle_no']}</td>
												<td>{$status}</td>
												<td style='text-align:center; width:19px'><a href='#' title='Edit' data-target='#vehicleModal' data-toggle='modal' class='edit-vehicle'><img src='images/pencil.png' /></a></td>
												<td style='width:15px'><a href='' class='remove-vehicle'><img src='images/cross.png' /></a></td>
												<!--<td style='text-align:center; width:19px'><a href='#' class='up-vehicle' data-vehicle_id='{$row['id']}' data-num_of_seats='' data-departure_order='{$row['departure_order']}' data-date='$date'><img src='images/arrow_up.png' /></a></td>-->";

												echo "</tr>";
									}
								}
							?>
								</tbody>
							</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>



	<iframe id='receipt' name='receipt' src='ticket_template.htm'></iframe>
</div>
	<!-- Edit vehicle details modal -->
<div class="modal fade" id="vehicleModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title">Add Vehicle</h4>
			</div>

			<form action="" id="vehicle_details" method="post">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>vehicle Number</label>
								<input type="text" name="vehicle_no" id="_vehicle_no" class="form-control" Placeholder="vehicle Number" />
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>Vehicle Type</label>
								<select name="vehicle_type_id" id="_vehicle_type_id" class="form-control">
									<option value="">-- Vehicle type --</option>
									<?php
										foreach ($vehicle->getAllvehicleTypes() AS $b) {
											echo "<option value='{$b->id}'>{$b->vehicle_name} ($b->num_of_seats Seats)</option>";
										}
									?>
								</select>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label>Driver's name</label>
						<input type="text" name="driver_name" id="driver_name" class="form-control" Placeholder="Driver's name" />
					</div>

					<div class="form-group">
						<label>Driver's phone</label>
						<input type="text" name="driver_phone" id="driver_phone" class="form-control" Placeholder="Driver's phone" />
					</div>
					<input type="hidden" name="action" id="action" value="add-vehicle" />
					<input type="hidden" name="vehicle_id" id="vehicle_id" value="0" />
					<input type="hidden" name="bbv_id" id="bbv_id" value="0" />
					<div class="alert alert-warning"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" id="close-modal" data-dismiss="modal">Cancel</button>
					<button type="submit" name="submit" class="btn btn-primary" id="submit-btn">Add Vehicle</button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php include_once "includes/footer.html"; ?>
<script type="text/javascript" src="js/bootstrap-datepicker.js"></script>
<script src="js/jquery-ui.min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/book_vehicle.js"></script>
<script>
$(document).ready(function() {
	/*** Autocomplete for selecting destination ***/
	var vehicles = ['<?php echo implode("', '", $vehicles); ?>'];

	$( "#vehicle_no" ).autocomplete({
		source: vehicles
	});


	$('.date').datepicker({
		format: 'dd-mm-yyyy',
		keyboardNavigation: false,
		forceParse: false,
		todayHighlight: true,
		autoclose: true
	});
});
</script>
