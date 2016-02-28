<?php
require "includes/head.php";
require "includes/side-bar.php";
require "classes/route.class.php";
require "classes/travelparkmap.class.php";

$parkMap = new TravelParkMap();
?>
<link href="css/datepicker.css" rel="stylesheet" />
<link href="css/datepicker3.css" rel="stylesheet" />
<style>
#receipt {clear:both; width:280px; display:none; height:300px; border:#ccc solid; }
</style>
<div class="content-wrapper">
  	<section class="content-header">
		<h1>
			Manifest
			<small>Reservations</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="active">Manifest</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<form>
					<div class="form-group row">
						<div class="col-md-2">
							<label>Date of travel</label>
							<input name="travel_date" id="t_date" type="text" value="<?php echo date('Y-m-d'); ?>" class="date form-control" />
						</div>

						<div class="col-md-3">
							<label>Choose destination</label>
							<select name="park_map_id" class="form-control">
								<option value="" >-- Select Destination --</option>
								<?php
								foreach($parkMap->getParkMap($_SESSION['travel_id'], $_SESSION['park_id']) AS $dest) {
									echo "\t<option value='{$dest->park_map_id}'>{$dest->destination_state}</option>\n";
								}
								?>
							</select>
						</div>

						<div class="col-md-2">
							<label>Select Bus</label>
							<select name='vehicle' class="form-control">
								<option value="">-- Select vehicle --</option>
							</select>
						</div>

						<div class="col-md-2">
							<label>&nbsp;</label>
							<button type='button' class="btn btn-info btn-block" id='view'>View manifest</button>
						</div>
						<div class="col-md-3">
							<label>&nbsp;</label>
							<button type='button' class='btn btn-block btn-danger' id='print'><span class='glyphicon glyphicon-print'></span> Print Manifest</button>
						</div>

				</form>
			</div>
		</div>

		<div>
			<div class="col-md-9">
				<div class="box box-warning">
					<div class="box-body">
						<div id="report" >
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="box box-warning">
					<div class="box-body">
						<div id="audit_pane">

						</div>
					</div>
				</div>
			</div>
			<iframe id='receipt' name='receipt' src='ticket.php'></iframe>
		</div>
		<div>
			<iframe id='manifest' name='manifest' style='width:100%; display:none' src='manifest.htm'></iframe>
		</div>
	</section>
</div>



<div class="modal fade" id="auditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title">Vehicle's Financial Audit</h4>
			</div>
			<div class="alert alert-success" style="display: none"></div>
			<form method="post" id='form-audit'>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Expenses</label>
								<input type="text" name="expenses" class="form-control" Placeholder="Expenses" />
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>Load (if any)</label>
								<input type="text" name="load_fare" class="form-control" placeholder="Load charges" />
							</div>
						</div>
					</div>
				</div>
				<input type="hidden" name="boarding_vehicle_id" id="boarding_vehicle_id" />

				<div class="modal-footer">
				  <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				  <button type="submit" class="btn btn-primary"> Save </button>
				</div>
			</form>
		</div>
	</div>
</div>


	<!-- Edit customer details modal -->
<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title">Edit Customer Details</h4>
			</div>
			<div class="alert alert-success" style="display: none"></div>
			<form method="post" id='customer_details'>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Customer's name</label>
								<input type="text" name="c_name" id="c_name" class="form-control" Placeholder="Customer's name" />
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>Customer's phone number</label>
								<input type="text" name="c_phone" id="c_phone" class="form-control" placeholder="Customer number" />
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Next of kin phone</label>
								<input type="text" name="next_of_kin" id="next_of_kin" class="form-control" placeholder="Next of kin number" />
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>&nbsp;</label>
								<input type="submit" value="Save" class="btn btn-primary btn-block" />
							</div>
						</div>
					</div>
				</div>
				<input type="hidden" name="customer_id" id="customer_id" />

				<div class="modal-footer">
				  <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				  <button type="submit" class="btn btn-primary hidden"> Save </button>
				</div>
			</form>
		</div>
	</div>
</div>

</div>
<?php include_once "includes/footer.html"; ?>
<script type="text/javascript" src="js/manifest.js"></script>
<script type="text/javascript" src="js/bootstrap-datepicker.js"></script>
<script>
$('.date').datepicker({
	keyboardNavigation: false,
	format: 'yyyy-mm-dd',
	forceParse: false,
	todayHighlight: true,
	autoclose: true
});
</script>
