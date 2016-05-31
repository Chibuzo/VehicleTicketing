<?php
session_start();

require_once "includes/db_handle.php";
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Initial Setup</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />

  <body class="login-page">
    <div class="login-box">
      <div class="login-logo">
        <a href="">Initial Setup</a>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg">Enter company details</p>
        <form action="" method="post" id="form-setup">
          <div class="form-group">
            <div class="row">
              <div class="col-md-12">
                <select name="state" class="form-control" required>
                  <option value="" selected>-- State of Operation --</option>
                  <?php
                  $db->query("SELECT * FROM states ORDER BY state_name");
                  $_states = $db->fetchAll('obj');
                  foreach ($_states AS $st) {
                    echo "<option value='{$st->state_name}-{$st->id}'>$st->state_name</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="row">
              <div class="col-md-6">
                <input type="text" name="travel_id" class="form-control" placeholder="Travel ID" required />
              </div>
              <div class="col-md-6">
                <input type="text" name="park_id" class="form-control" placeholder="Park ID" required />
              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="row">
              <div class="col-md-6">
                <input type="text" name="phone1" class="form-control" placeholder="Phone one" required />
              </div>
              <div class="col-md-6">
                <input type="text" name="phone2" class="form-control" placeholder="Phone two" required />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-8">
              <div class="">

              </div>
            </div><!-- /.col -->
            <div class="col-xs-4">
              <button type="submit" name="login" class="btn btn-primary btn-block btn-flat">Install</button>
            </div><!-- /.col -->
          </div>
        </form>

        <!--<a href="#">I forgot my password</a><br>
        <a href="register.html" class="text-center">Register a new membership</a>-->

      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->
  </body>
</html>
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script>
$(document).ready(function() {
  $("#form-setup").submit(function(e) {
    e.preventDefault();
    $("button[type=submit]").text("Installing...").prop("disabled", true);
    $.post('ajax/setup.php', $(this).serialize(), function(d) {
      if (d.trim() == "Done") {
        alert("Setup successfull. Click OK to continue");
        location.replace("index.php");
      }
    });
  });
});
</script>
