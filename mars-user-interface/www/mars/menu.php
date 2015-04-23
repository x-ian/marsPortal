<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <link href="/mars/application.css" rel="stylesheet" type="text/css" />
  <title>mars portal</title>
</head>

<? include 'config.php'; ?>

<body>

  <div id="banner" style="text-align: center;">
	<table align="center">
		<tr><td><a href="index.html"><img src="/mars/captiveportal-mars.jpg" width="75"/></a></td><td style="font-variant:small-caps; font-size:200%">portal</td></tr>
	</table>
  </div>
  
  <div id="columns">
    <div id="side">
      <ul id="ul1">
        <li><a href="/mars/userinfo/list.php">Manage users/devices</a></li>
        <li><a href="/mars/group/list.php">Manage groups<a></li>
        <li>Reports</li>
      	<ul id="ul2">
       		<li><a href="/mars/reports/statistics.php">Statistics</a></li>
       		<li><a href="/mars/reports/users_with_volume.php">Users (7d history)</a></li>
		</ul>
        <li><a href="/mars/admin.php">Admin<a></li>
        <li>Additional links</li>
      	<ul id="ul2">
       		<li><a href="/index.php">pfSense Dashboard</a></li>
       		<li><a href="/status_captiveportal.php">Captive portal sessions</a></li>
       		<li><a href="/status_graph.php?if=wan">Traffic graphs</a></li>
       		<li><?php echo '<a href=http://' . $PF_IP . ':3000>nTop</a>'; ?></li>
       		<li><a href="/lightsquid/index.cgi">Squid Proxy report</a></li>
		</ul>
  	  </ul>
	</div>

