<?php
require "includes/head.php";
require "includes/side-bar.php";
require_once "includes/db_handle.php";

//require_once "../../api/models/user.class.php";
require_once "classes/destination.class.php";
require_once "classes/parkmodel.class.php";

$destination = new Destination();

?>
<style>
.opt-icons .fa { color: #666; font-size: 17px; margin-left: 6px; }
</style>
<!-- Right side column. Contains the navbar and content of the page -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-road"></i> Routes
            <small>Control panel</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Routes</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
		<div class="row">

			<div class="col-md-7 col-xs-12">
				<div class="box box-danger">
					<div class="box-header with-border">
						<h2 style='font-size: 20px' class="box-title"><i class="fa fa-bus"></i> &nbsp; Routes</h2>
					</div>
					<div class="box-body">
                        <form method="post" id="new_park_map">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select name="origin" id="origin" class="form-control" required>
                                           <option value="<?php echo $_SESSION['park_id']; ?>"><?php echo $_SESSION['park_name']; ?></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select name="destination" id="destination" class="form-control" required>
                                            <option value="" selected>- Destination (State) -</option>
                                            <?php
                                            $db->query("SELECT * FROM states ORDER BY state_name");
                                            $_states = $db->fetchAll('obj');
                                            foreach ($_states AS $st) {
                                                printf("<option value='%d'>%s</option>", $st->id, $st->state_name);
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select name="destination_park" id="destination_park" class="form-control" required>
                                            <option value="">-- Destination (Park) --</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name="park_map" value="yes" />

                                <div class="col-md-1">
                                    <button type="submit" name="addParkMap" class="btn bg-olive"><i class='fa fa-plus'></i> Add</button>
                                </div>
                            </div>
                        </form>
						<div id="detail-div">
							<table class="table tablebordered table-striped">
								<thead>
									<tr>
										<th width='30'>S/No</th>
										<th>Origin</th>
										<th>Destination</th>
										<th class='text-center'>Option</th>
									</tr>
								</thead>
								<tbody id="vehicle">
								<?php
									$html = ""; $n = 0;
									foreach ($destination->getRoutes() as $row) {
										$n++;
										$html .= "<tr>
													<td class='text-right'>$n</td>
													<td>{$_SESSION['park_name']}</td>
													<td>{$row->destination} ($row->park)</td>
													<td class='opt-icons text-center' id='{$row->id}'>
														<a href='' class='edit-vehicle' title='Edit' data-toggle='tooltip'><i class='fa fa-pencil'></i></a>
														<a href='' class='delete' title='Remove' data-toggle='tooltip'><i class='fa fa-trash-o'></i></a>
													</td>
												</tr>";
									}
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
<script>
$(document).ready(function() {

    $('#destination').on('change', function() {
        var id = $(this).val();
        $.post("ajax/misc_fns.php", {"op": "get-state-parks", "state_id": id}, function(d) {
            var parks = JSON.parse(d);
            var _select = $("#destination_park");
            _select.html('<option value="">-- Destination (Park) --</option>');
            $.each(parks, function(i, val) {
                _select.append($('<option />', { value: val.id, text: val.park }));
            });
        });
    });

    $('#new_park_map').on('submit', function(e) {
        e.preventDefault();
        var origin = $('#origin').val();
        var destination = $('#destination_park').val();
        var state = $("#destination option:selected").text();
        if (destination == origin) {
            alert("Destination must be different from origin");
        }

        $.post('ajax/synch.php', {'op': 'add-park-map', 'origin': origin, 'destination': destination, 'state': state}, function(d) {
            if (d.trim() != 'Done') {
                location.reload();
            }
        });
    });
});
</script>
