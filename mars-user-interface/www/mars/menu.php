<? 

include('config.php'); 

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header("WWW-Authenticate: Basic realm=\"marsPortal Admin\"");
    header("HTTP/1.0 401 Unauthorized");
    print "Sorry, invalid credentials. Access denied! Reload page or close&reopen browser to try again.\n";
    exit;
} else {
    if (($_SERVER['PHP_AUTH_USER'] == $HTTP_AUTH_USER) && ($_SERVER['PHP_AUTH_PW'] == $HTTP_AUTH_PASSWD)) {
//            print "Welcome to the private area!";
    } else {
        header("WWW-Authenticate: Basic realm=\"marsPortal Admin\"");
        header("HTTP/1.0 401 Unauthorized");
        print "Sorry, invalid credentials. Access denied! Reload page or close&reopen browser to try again.\n";
        exit;
    }
}

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past to bypass browser caching
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <link href="/mars/application.css" rel="stylesheet" type="text/css" />
  <title>marsPortal - <? echo $HEADLINE ?></title>
  <script src="/mars/application.js"></script>
</head>

<body class="visualize_text">

<nav class="navbar navbar-default"> <!-- navbar-fixed-top -->
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
	  <a class="navbar-brand" href="/mars/index.php"><img src="/mars/captiveportal-mars.jpg" width="33"/></a>
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
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">WAN Stats<span class="caret"></span></a>
          <ul class="dropdown-menu">
					<li><a href="/mars/wan/log_internet_ping.php">Availability (ping)</a> </li>
					<li><a href="/mars/wan/log_wan_throughput.php">Throughput (netstat)</a> </li>
		   			<li><a href="/mars/wan/log_wan_traffic.php">Statistics</a></li>
            <!--<li role="separator" class="divider"></li>-->
          </ul>
        </li>
		
		<li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Traffic Stats (Devices)<span class="caret"></span></a>
          <ul class="dropdown-menu">
					<li><a href="/mars/reports/recent_top_X.php?order=output_rate1">Most active devices (v2)</a> </li>
					<li><a href="/mars/reports/throughput.php?order=output_rate&period=min_ago_5">Throughput (most active devices)</a> </li>
		   			<li><a href="/mars/reports/statistics.php">Statistics (v2)</a></li>
		   			<li><a href="/mars/reports/statistics-v5.php">Statistics</a></li>
					<li><a href="/mars/reports/devices_with_volume.php">Devices history</a></li>
					<li><a href="/mars/reports/online-devices.php">Devices currently online</a></li>
            <!--<li role="separator" class="divider"></li>-->
          </ul>
        </li>
		
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Traffic Stats (Users & Groups)<span class="caret"></span></a>
          <ul class="dropdown-menu">
	       		<li><a href="/mars/reports/users-statistics.php">Statistics</a></li>
	       		<li><a href="/mars/reports/groups.php">Groups</a></li>
          </ul>
        </li>

        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin<span class="caret"></span></a>
          <ul class="dropdown-menu">
	       		<li><a href="/probe_configurations">Work hours</a></li>
	       		<li><a href="/probe_configurations">Change password</a></li>
	       		<li><a href="/probe_configurations">Edit email recipients</a></li>
	       		<li><a href="/probe_configurations">View Logs</a></li>
	       		<li><a href="/probe_configurations">Backups</a></li>
	       		<li><a href="/probe_configurations">Activate remote administration</a></li>
	       		<li><a href="/probe_configurations">Observe network devices</a></li>
	       		<li><a href="/probe_configurations">Monitor additional network infrastructure</a></li>
	       		<li><a href="/probe_configurations">Activate VIP/emergency mode</a></li>
	       		<li><a href="/mars/admin/licenses.php">License notes</a></li>
	       		<li><a href="/probe_configurations">Useful links</a></li>
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

      <form class="navbar-form navbar-right">
		  <button type="button" class="btn btn-default" aria-label="Contact">
			<a href="mailto:sales@marsgeneral.com"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span></a>
		  </button>		
      </form>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

