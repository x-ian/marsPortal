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
</head>

<body>

  <div id="banner" style="text-align: center;">
	<table align="center">
		<tr><td><a href="index.html"><img src="/mars/captiveportal-mars.jpg" width="75"/></a></td><td style="font-variant:small-caps; font-size:200%">portal - <? echo $HEADLINE ?></td></tr>
	</table>
  </div>
  
  <div id="columns">
    <div id="side">
      <ul id="ul1">
        <li>Manage</li>
      	<ul id="ul2">
	        <li><a href="/mars/userinfo/list.php">Devices</a></li>
	        <li><a href="/mars/user/list.php">Users</a></li>
	        <li><a href="/mars/group/list.php">Groups<a></li>
		</ul>
        <li>Reports (Devices)</li>
      	<ul id="ul2">
       		<li><a href="/mars/reports/recent_top_X.php?order=output_rate1">Most active devices</a></li>
       		<li><a href="/mars/reports/statistics.php">Statistics</a></li>
       		<li><a href="/mars/reports/devices_with_volume.php">Devices (7d history)</a></li>
       		<li><a href="/mars/reports/online-devices.php">Devices currently online</a></li>
		</ul>
        <li>Reports (Users & Groups)</li>
      	<ul id="ul2">
       		<li><a href="/mars/reports/users-statistics.php">Statistics</a></li>
       		<li><a href="/mars/reports/groups.php">Groups</a></li>
		</ul>
<!--        <li><a href="/mars/admin/admin.php">Admin<a></li> -->
        <li>Additional links</li>
      	<ul id="ul2">
       		<li><a href="/index.php">pfSense Dashboard</a></li>
       		<li><a href="/status_captiveportal.php">Captive portal sessions</a></li>
       		<li><a href="/status_graph.php?if=wan">Traffic graphs</a></li>
<!--
       		<li><?php echo '<a href=http://' . $PF_IP . ':3000/sortDataThpt.html?showH=1&showL=2&col=1>nTop - Top Downloaders</a>'; ?></li>
       		<li><?php echo '<a href=http://' . $PF_IP . ':3000/sortDataThpt.html?col=1&showH=1&showL=1>nTop - Top Uploaders</a>'; ?></li>
-->
       		<li><a href="/lightsquid/index.cgi">Squid Proxy report (if installed)</a></li>
		</ul>
  	  </ul>
	</div>
