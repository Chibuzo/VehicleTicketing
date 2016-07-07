<?php
require "includes/head.php";
require "includes/side-bar.php";
require_once "classes/destination.class.php";
require_once "classes/vehiclemodel.class.php";
require_once "classes/trip.class.php";
require_once "classes/utility.class.php";

$destination = new Destination();
$vehicle = new VehicleModel();
$trip = new Trip();

$vehicle_types = $vehicle->getAllVehicleTypes($_SESSION['travel_id']);

if (isset($_POST['op'])) {
    if ($_POST['op'] == 'create') {
        extract($_POST);
        $amenities = implode($amenities, ">");
        //$_route = $travel_park_map->getRoute($route);

        $rslt = $trip_model->addTrip($route, $departure, $_SESSION['travel_id'], $_SESSION['state_id'], $_route->id, $vehicle_type, $amenities, $departure_time, $fare);
    } elseif ($_POST['op'] == "edit") {
        extract($_POST);
        $amenities = implode($edit_amenities, ">");

        $rslt = $trip_model->updateTrip($edit_trip_id, $amenities, $edit_fare);
    }
}

$trips = $trip->getTrips();
?>
<link href="css/multi-select.css" rel="stylesheet" type="text/css" />
<link href="css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
<div class="content-wrapper">
    <section class="content-header">
      <h1><i class="fa fa-road"></i>
        Manage Trips
        <small>Control panel</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Manage Trips</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-6">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h2 style='font-size: 18px' class="box-title"><i class="fa fa-car"></i> &nbsp;New Trip</h2>
                    </div>
                    <div class="box-body">
                        <div>
                            <form class="form-vertical form-trip" action="" method="post" id="form-add-trip">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="hidden col-sm-2 control-label" for="route">Route</label>

                                        <div clas="col-sm-12">
                                            <select name="park_map_id" id="park_map_id" class="form-control" required>
                                                <option value="" selected>-- Select Route --</option>
                                                <?php
                                                foreach ($destination->getRoutes() as $row) {
                                                    printf("<option value='%s'>%s &#09; to %s (%s)</option>", $row->park_map_id, $_SESSION['park_name'], $row->destination, $row->park);
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="hidden col-sm-2 control-label" for="vehicle_types">Vehicle Type</label>
                                        <div class="col-sm-6">
                                            <select name="vehicle_type" id="vehicle_types" class="form-control" required>
                                                <option value="" selected>-- Vehicle Type --</option>
                                                <?php
                                                foreach ($vehicle_types as $vehicle) {
                                                    printf("<option value='%s'>%s</option>", $vehicle->vehicle_type_id, $vehicle->vehicle_name);
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="number" min="100" step="100" max="100000" name="fare" id="fare" placeholder="Fare" />
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="hidden col-sm-2 control-label" for="departure">Departure Order</label>
                                        <div class="col-sm-6">
                                            <input class="form-control" type="number" name="departure" id="departure" placeholder="Departure Order" required/>
                                        </div>
                                        <label class="hidden col-sm-2 control-label" for="departure_time">Departure Time</label>
                                        <div class="col-sm-6">
                                             <div class="input-group bootstrap-timepicker timepicker col-sm-12">
                                                <input id="departure_time" name="departure_time" readonly type="text" class="form-control input-small">
                                             </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label" for="amenities">Vehicle Amenities</label>
                                        <div class="">
                                            <select name="amenities[]" id="amenities" multiple="multiple" class="form-control">
                                                <option value="A/C">A/C</option>
                                                <option value="Food">Food</option>
                                                <option value="TV">TV</option>
                                                <option value="Refreshment">Refreshment</option>
                                                <option value="Restrooms">Restrooms</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                              <!-- /.box-body -->
                                <div class="box-footer">
                                    <input type="hidden" name="op" value="add-trip">
                                    <button class="btn btn-info" type="submit"><i class='fa fa-save'></i> &nbsp;Save</button>
                                </div>
                              <!-- /.box-footer -->
                            </form>
                        </div>
                    </div>
                    <!--<div class="box-footer">

                    </div>-->
                </div>
            </div>
            <div class="col-md-6">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h2 style='font-size: 18px' class="box-title"><i class="fa fa-money"></i> &nbsp;Manage Fares</h2>
                    </div>
                    <div class="box-body">
                        <div>
                            <table class='table table-striped table-bordered'>
                                <thead>
                                    <tr>
                                        <th style="width: 10px">S/no</th>
                                        <th>Route</th>
                                        <th>Order</th>
                                        <th>Amenities</th>
                                        <th>Fare</th>
                                        <th class="text-center">Edit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $i = 1;
                                foreach ($trips as $trip) {
                                    $_amenities = preg_replace("/>/", ",", $trip->amenities);
                                    $table_amenities = preg_replace("/>/", ", ", $trip->amenities);
                                    echo "<tr>";
                                    printf("<td>%s</td>", $i);
                                    printf("<td>%s</td>", $_SESSION['park_name'] . " - " . $trip->destination);
                                    printf("<td>%s %s</td>", Utility::ordinal($trip->departure), $trip->vehicle_name);
                                    printf("<td>%s</td>", $table_amenities);
                                    printf("<td>%s</td>", number_format($trip->fare));
                                    ?>
                                    <td class='text-center'>
                                        <a href='#' class='edit-trip' data-id="<?php echo $trip->id; ?>" data-trip-id="<?php echo $trip->trip_id; ?>" data-amenities="<?php echo $_amenities; ?>" data-fare="<?php echo $trip->fare; ?>" data-toggle="modal" data-target="#tripModal" >
                                            <i class='fa fa-pencil' data-toggle='tooltip' title='' data-original-title='Edit fare'></i>
                                        </a>
                                    </td>
                                    <?php
                                    echo "</tr>";
                                    $i++;
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--<div class="box-footer">

                    </div>-->
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Modal -->
<div class="modal fade" id="tripModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Edit Trip</h4>
            </div>
            <form action="" method="post" class="form-trip">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Amenities</label>
                        <select name="amenities[]" id="edit_amenities" multiple="multiple" class="form-control">
                            <option value="A/C">A/C</option>
                            <option value="Food">Food</option>
                            <option value="TV">TV</option>
                            <option value="Refreshment">Refreshment</option>
                            <option value="Restrooms">Restrooms</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Fare</label>
                        <input type="number" min="100" step="100" max="100000" name="fare" class="form-control" id="edit_fare" placeholder="Fare" />
                    </div>
                </div>
                <input type="hidden" name="trip_id" id="trip_id" />
                <input type="hidden" name="op" value="edit-trip">
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>

        </div>
    </div>
</div>
<?php include_once "includes/footer.html"; ?>
<script type="text/javascript" src="js/jquery.multi-select.js"></script>
<script type="text/javascript" src="plugins/timepicker/bootstrap-timepicker.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('.edit-trip').on('click', function(e) {
        e.preventDefault();
        var fare = $(this).data('fare');
        var amenities = $(this).data('amenities');
        var trip_id = $(this).data('trip-id');
        var id = $(this).data('id');
        $('#edit_fare').val(fare);
        $("#id").val(id);
        $('#trip_id').val(trip_id);
        var data_amenities = amenities.split(",");
        $("#edit_amenities").val(data_amenities);
        $("#edit_amenities").multiSelect("refresh");
    });


    // add/update trip
    $(".form-trip").on('submit', function (e) {
        e.preventDefault();

        $.post('ajax/synch.php', $(this).serialize(), function(d) {
            if (d.trim() == 'Done') {
                location.reload();
            }
        });
    });


    $('#amenities').multiSelect();
    $('#edit_amenities').multiSelect();
    $('#departure_time').timepicker();
});
</script>
