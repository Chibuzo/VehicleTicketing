<?php
require "includes/head.php";
require "includes/side-bar.php";
require_once "classes/report.class.php";

$report = new Report();
?>
<link href="css/datepicker.css" rel="stylesheet" />
<link href="css/datepicker3.css" rel="stylesheet" />

<div class="content-wrapper">
  	<section class="content-header">
	  <h1>
		Reports
		<small>Control panel</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Reports</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-warning">
					<div class="box-header with-border">
						<h2 style='font-size: 18px' class="box-title"><i class="fa fa-book"></i> &nbsp;Reports</h2>
						<a href="generate_report.php?date=<?php echo isset($_POST['travel_date']) ? $_POST['travel_date'] : date('Y-m-d'); ?>" class="btn btn-primary pull-right"><i class="fa fa-book"></i> Generate Report Sheet</a>
					    <a href="generate_report.php?op=upload-report&date=<?php echo isset($_POST['travel_date']) ? $_POST['travel_date'] : date('Y-m-d'); ?>" class="btn btn-info pull-right hidden" style="margin-right: 5px"><i class="fa fa-upload"></i> Upload Report</a>
					</div>

					<div class="box-body">
						<div>
							<div style="margin-bottom: 8px">
								<form action="" method="post" class="form-horizontal">
									<div class="row">
										<div class="col-md-3">
											<select class="form-control" name="report_type">
												<!--<option value="">-- Report Type --</option>-->
												<option value="Daily">Daily Report</option>
												<!--<option value="Monthly">Monthly Report</option>-->
											</select>
										</div>

										<div class="col-md-5">
											<div class="input-groupform-group">
												<div class="col-sm-5">
													<input name="travel_date" class="form-control date" id="tdate" type="text" value="<?php echo isset($_POST['travel_date']) ? $_POST['travel_date'] : ''; ?>" placeholder="Pick date..." />
												</div>
												<span class="input-group-btn">
													<input type="submit" class="btn btn-primary" name="submit" value="Display" />
												</span>
											</div>
										</div>

										<!--<div class="col-md-5">
											<input type="submit" name="submit" value="Get Report" class="btn btn"
										</div>-->
									</div>
								</form>
							</div>

							<table class='table table-striped table-bordered'>
								<thead>
									<tr>
										<th style="width: 10px">S/no</th>
										<th>Route</th>
										<th>Vehicle Type</th>
										<th>Tickets sold</th>
										<th>Fare ( ₦ )</th>
										<th>Revenue ( ₦ )</th>
										<th>Expenses ( ₦ )</th>
										<th>Profit ( ₦ )</th>
									</tr>
								</thead>
								<tbody>
								<?php
									$date = isset($_POST['travel_date']) ? $_POST['travel_date'] : date('Y-m-d');
									$reports = $report->getDailyReport($date);

									$html = ''; $n = 0; $total_revenue = $total_expenses = $total_profit = 0;
									foreach ($reports AS $rep) {
										$n++;
										$departure_order = Report::ordinal($rep['departure_order']);
										$num_of_tickets = count(explode(",", $rep['booked_seats']));
										$total_expenses += $rep['expenses'];
										$total_revenue += $revenue = $num_of_tickets * $rep['fare'];
										$total_profit += $profit = $revenue - $rep['expenses'];
										$html .= "<tr>
													<td class='text-right'>$n</td>
													<td>{$_SESSION['state_name']} - {$rep['destination']}</td>
													<td>{$rep['bus_type']} ( $departure_order )</td>
													<td class='text-right'>{$num_of_tickets}</td>
													<td class='text-right'>" . number_format($rep['fare']) . "</td>
													<td class='text-right'>" . number_format($revenue) . "</td>
													<td class='text-right'>" . number_format($rep['expenses']) . "</td>
													<td class='text-right'>" . number_format($profit) . "</td>
												</tr>";
									}
									$html .= "<tr style='font-size:16px; font-weight: bold; text-align: right'><td colspan='5'>Totals:</td>
													<td>".number_format($total_revenue)."</td><td>".number_format($total_expenses)."</td><td>".number_format($total_profit)."</td></tr>";
									echo $html;
								?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<?php include_once "includes/footer.html"; ?>
<script type="text/javascript" src="js/bootstrap-datepicker.js"></script>
<script>
$(document).ready(function() {
	$('#tdate').datepicker({
		format: 'dd-M-yyyy',
		keyboardNavigation: false,
		forceParse: false,
		todayHighlight: true,
		autoclose: true
	});

	//$("#upload-report").click(function(e) {
	//	e.preventDefault();
	//	var $this = $(this);
	//	$(this).html("<i class='fa fa-cog fa-spin'></i> &nbsp; Uploading...").prop("disabled", true);
	//	var date = $(this).data("date");
	//	$.post("ajax/report.php", {"op": "upload-report", "report_date": date}, function(d) {
	//		if (d.trim() == "Done") {
	//			$this.html("<i class='fa fa-upload'></i> Upload Report");
	//		}
	//	});
	//});
});
</script>
