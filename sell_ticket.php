<?php
require "includes/head.php";
require "includes/side-bar.php";
require "classes/vehiclemodel.class.php";
require "classes/destination.class.php";
//require "classes/routemodel.class.php";

$vehicle = new VehicleModel();
$destination = new Destination();
//$route = new RouteModel();
?>
<link rel="stylesheet" type="text/css" href='css/seats.css' />
<link href="css/datepicker.css" rel="stylesheet" />
<link href="css/datepicker3.css" rel="stylesheet" />

<style>
#pick_seat {margin-left:30px; height: 230px; margin-top: 8px;}
#bus_details {display:none}
iframe#receipt {clear:both; width:280px; display:none; height:300px; border:#ccc solid; }
label {font: bold 11px Verdana}
select {paddng:0px; heght:23px; width:173px}
.alert-error, #div-add-customer { display: none; }
</style>

<div class="content-wrapper">
  	<section class="content-header">
		<h1>
			Sell Ticket
			<small>Reservations</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="active">Reservations</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">
		<div class="row">
			<div id='main_bus_search' class="col-md-7">
				<div class="box box-warning" style="height:480px">
					<div class="box-header with-border hidden">
						<h2 style='font-size: 18px' class="box-title"><i class="fa fa-money"></i> &nbsp;Sell ticket</h2>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-md-5">
								<div class="alert alert-error">
								</div>
								<form action="" method="post" id="book">
									<div class="form-group col-md-12">
										<label>Select Vehicle Type</label>
										<select name="vehicle_type" class="form-control" id="vehicle_type">
											<option value="">-- Select Vehicle type --</option>
											<?php
												foreach ($vehicle->getAllVehicleTypes() AS $b) {
													echo "\t<option value='{$b->vehicle_type_id}' data-num_of_seat='{$b->num_of_seats}'>{$b->vehicle_name} ($b->num_of_seats Seats)</option>\n";
												}
											?>
										</select>
									</div>

									<div class="form-group col-md-12">
										<label>To</label>
										<select name="park_map_id" id="park_map_id" class="form-control">
											<option value="">-- Pick destination --</option>
											<?php
												foreach($destination->getRoutes() AS $dest) {
													echo "\t<option value='{$dest->park_map_id}'>{$dest->destination} [ $dest->park ]</option>\n";
												}
												?>
										</select>
									</div>

									<div class="form-group col-md-12">
										<label>Pick Vehicle</label>
										<select name="departure_order" class="form-control">
											<option value="">-- Auto select --</option>
											<option value="1">First Vehicle</option>
											<option value="2">Second Vehicle</option>
											<option value="3">Third Vehicle</option>
											<option value="4">Fourth Vehicle</option>
											<option value="5">Fifth Vehicle</option>
											<option value="6">Sixth Vehicle</option>
											<option value="7">Seventh Vehicle</option>
											<option value="8">Eight Vehicle</option>
											<option value="9">Ninth Vehicle</option>
											<option value="10">Tenth Vehicle</option>
										</select>
									</div>

									<div class="form-group col-md-12">
										<label>Date of travel</label>
										<input name="travel_date" id="t_date" type="text" value="<?php echo date('Y-m-d'); ?>" class="form-control date" />
									</div>

									<div class="form-group col-md-12">
										<label>&nbsp;</label><br />
										<div class="row">
											<div class="col-md-6">
												<button type="submit" name="search" type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-th-list icon-white"></span> Show seats</button>
											</div>
											<div class="col-md-6">
												<button type='button' id='reset-form' class='btn btn-default btn-block'><span class="glyphicon glyphicon-remove"></span> Cancel</button>
											</div>
										</div>
									</div>

									<p id="alert-show"><div id='loading_bus_details' class='alert hide'></div></p>
								</form>
							</div>

							<div class="col-md-7">
								<div id="pick_seat"></div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-4 pull-right">
				<div class="box box-success">
					<div class="box-header with-border">
						<h2 style='font-size: 18px' class="box-title"><i class="fa fa-user"></i> &nbsp;Customer details</h2>
					</div>
					<div class="box-body">
						<div>
							<form method="post" id="customer_info" class="">
								<input type="hidden" name="c_id" id="c_id" value="" />
								<!--<div class="input-group form-group">
									<input type="text" name="customer_phone" id="customer-phone" class="form-control" placeholder="Customer's phone number" />
									<span class="input-group-btn">
										<button type="submit" class="btn btn-primary" id="find-customer">Find & Print</button>
									</span>
								</div>-->
								<div class="form-group">
									<input type="text" name="customer_phone" id="customer-phone" class="form-control" placeholder="Customer's phone number" />
								</div>
								<div id="div-add-customer">
									<div class="form-group">
										<input type="text" name="customer_name" class="form-control" placeholder="Customer's name" />
									</div>
									<div class="form-group">
										<input type="text" name="address" class="form-control hidden" placeholder="Customer's address" />
									</div>

									<div class="form-group">
										<input type="text" name="kin_phone" class="form-control" placeholder="Next of kin's phone number" />
									</div>
								</div>
								<div class="form-group">
									<button type='submit' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-print'></span> Print ticket</button>
								</div>
								<input type="reset" class="hide clearfix" />
								<div class="alert alert-error">Please fill in all the required details</div>
							</form>
						</div>
					</div>
				</div>
			</div>

			<iframe id='receipt' name='receipt' src='ticket.php'></iframe>

		</div>
	</section>
</div>

<?php include_once "includes/footer.html"; ?>
<script type="text/javascript" src="js/socket.js"></script>
<script type="text/javascript" src="js/sell_ticket.js"></script>
<script type="text/javascript" src="js/bootstrap-datepicker.js"></script>
<script>
$(document).ready(function() {
	$('.date').datepicker({
		format: 'yyyy-mm-dd',
		keyboardNavigation: false,
		forceParse: false,
		todayHighlight: true,
		autoclose: true
	});
});


/*** Prevent backspace button from navigating the page backwards ***/
$(function(){
    /*
     * this swallows backspace keys on any non-input element.
     * stops backspace -> back
     */
    var rx = /INPUT|SELECT|TEXTAREA/i;

    $(document).bind("keydown keypress", function(e){
        if( e.which == 8 ){ // 8 == backspace
            if(!rx.test(e.target.tagName) || e.target.disabled || e.target.readOnly ){
                e.preventDefault();
            }
        }
    });
});
</script>
