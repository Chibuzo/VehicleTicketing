<!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          <!-- Sidebar user panel -->
          <div class="user-panel">
            <div class="pull-left image">
              <a href='#'><i class="fa fa-user fa-2x"></i></a>
            </div>
            <div class="pull-left info">
              <p><?php echo $_SESSION['username']; ?></p>

              <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
          </div>

          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
            <li class="hidden">
              <a href="dashboard.php">
                <i class="fa fa-dashboard"></i> <span>Dashboard</span>
              </a>
            </li>

            <li id="sell">
              <a href="sell_ticket.php#sell">
                <i class="fa fa-credit-card"></i> <span>Sell ticket</span>
              </a>
            </li>

            <li id="manifest">
              <a href="manifest.php#manifest">
                <i class="fa fa-tasks"></i>
                <span>Manifest</span>
              </a>
            </li>

			<?php if ($_SESSION['user_type'] == 'admin') { ?>

				<li id="bookvehicle">
				  <a href="bookbus.php#bookvehicle">
					<i class="fa fa-bus"></i>
					<span>Book Vehicle</span>
				  </a>
				</li>

				<li id="route">
				  <a href="manage_routes.php#route">
					<i class="fa fa-cogs"></i> <span>Manage Routes</span>
				  </a>
				</li>

                <li id="trip">
                  <a href="manage_trips.php#trip">
                    <i class="fa fa-road"></i> <span>Manage Trips</span>
                  </a>
                </li>

				<li id="fare">
				  <a href="manage_fares.php#fare">
					<i class="fa fa-money"></i>
					<span>Manage Fares</span>
				  </a>
				</li>

				<li id="report">
				  <a href="report.php#report">
					<i class="fa fa-book"></i>
					<span>Reports</span>
				  </a>
				</li>

				<li id="user">
				  <a href="users.php#user">
					<i class="fa fa-group"></i>
					<span>Manage Users</span>
				  </a>
				</li>
			<?php } ?>

			<li>
              <a href="logout.php">
                <i class="fa fa-sign-out"></i>
                <span>Logout</span>
              </a>
            </li>
		  </ul>
        </section>
        <!-- /.sidebar -->
      </aside>
