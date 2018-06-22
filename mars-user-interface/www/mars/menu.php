<? include './auth.php'; ?>

<nav class="navbar navbar-default"> <!-- navbar-fixed-top -->
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
	  <a class="navbar-brand" href="/mars/index.php"><img src="/mars/logo-mars-cleaned-medium-150.png" width="33"/></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
		  
        <!-- <li class="active"><a href="#">Dashboard <span class="sr-only">(current)</span></a></li> -->
        
		<li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Manage<span class="caret"></span></a>
          <ul class="dropdown-menu">
					<li><a href="/mars/userinfo/list.php">Devices</a> </li>
		   			<li><a href="/mars/user/list.php">Users</a></li>
					<li><a href="/mars/group/list.php">Groups</a></li>
            <!--<li role="separator" class="divider"></li>-->
          </ul>
        </li>
		
		<li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">WAN Statistics<span class="caret"></span></a>
          <ul class="dropdown-menu">
					<li><a href="/mars/wan/log_internet_ping.php">Availability (ping)</a> </li>
					<li><a href="/mars/wan/log_wan_throughput.php">Throughput (netstat)</a> </li>
		   			<li><a href="/mars/wan/log_wan_traffic.php">Traffic volume</a></li>
            <!--<li role="separator" class="divider"></li>-->
          </ul>
        </li>
		
		<li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Traffic Statistics (Devices)<span class="caret"></span></a>
          <ul class="dropdown-menu">
					<li><a href="/mars/reports/throughput.php?order=output_rate&period=min_ago_5">Throughput / Most active devices</a> </li>
		   			<li><a href="/mars/reports/statistics-v5.php">Traffic volume</a></li>
					<li><a href="/mars/reports/devices_with_volume.php">Devices history</a></li>
					<li><a href="/mars/reports/online-devices.php">Devices currently online</a></li>
          </ul>
        </li>
		
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Traffic Statistics (Users & Groups)<span class="caret"></span></a>
          <ul class="dropdown-menu">
	       		<li>Statistics (disabled)></li>
	       		<li><a href="/mars/reports/groups.php">Groups</a></li>
          </ul>
        </li>

        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Administration<span class="caret"></span></a>
          <ul class="dropdown-menu">
	       		<li><a href="">View Logs</a></li>
	       		<li><a href="">Activate remote administration</a></li>
	       		<li><a href="">Notify when device comes online</a></li>
	       		<li><a href="">Activate VIP/emergency mode</a></li>
	       		<li><a href="/mars/admin/devices_not_yet_registered.php">Devices without Portal Registration</a></li>
				<li role="separator" class="divider"></li>
	       		<li><a href="/mars/admin/check-radius-inconsistencies.php">RADIUS inconsistencies</a></li>
	       		<li><a href="">Cleanup devices not seen for last x months</a></li>
	       		<li><a href="">Reset all accounting data</a></li>
				<li role="separator" class="divider"></li>
	       		<li><a href="">Useful links</a></li>
	       		<li><a href="/mars/admin/licenses.php">License notes</a></li>
          </ul>
        </li>

        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Configuration<span class="caret"></span></a>
          <ul class="dropdown-menu">
	       		<li><a href="">Invoke Backup</a></li>
	       		<li><a href="/mars/config/edit.php">Change Settings</a></li>
	       		<li><a href="">Change password</a></li>
          </ul>
        </li>
      </ul>

      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Account <span class="caret"></span></a>
          <ul class="dropdown-menu">
			<li><a href="/users/edit">Edit account</a></li>
			<li><a rel="nofollow" data-method="delete" href="/users/sign_out">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

