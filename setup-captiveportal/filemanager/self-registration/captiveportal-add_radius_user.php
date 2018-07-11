<? include '/home/marsPortal/mars-user-interface/www/mars/config.php'; ?>
<html>
<head>
	<meta http-equiv="Refresh" content="3; url=http://www.marsgeneral.com/" />
</head>

<body>
<span style="font-variant:small-caps; font-size:200%">
	<table align="center">
		<tr><td><img src="/captiveportal-mars.jpg" /></td><td>Portal</td></tr>
	</table>
</span> 

<hr/><br/>

<div align="center">

	<pEverybody gets connected - Hopefully you by now as well!</p>
	<p><?php
		$ip=$_SERVER['REMOTE_ADDR'];
		$name=$_REQUEST['name'];
		$email="";
		$owner="";
		$primary="";
	    echo "The registration process should be done for " . $name . " (" . $email . ") from " . $ip;
		/* echo "/home/marsPortal/freeradius-integration/self-registration/captive-portal-add_user_to_radius.sh " 
		. $ip 
		. " \"" . $name . "\"" 
		. " \"" . $email . "\"" 
		. " \"" . $owner . "\"" 
		. " \"" . $primary . "\"" 
		. " \"" . $DEFAULT_GROUP . "\""; */
		exec("/home/marsPortal/freeradius-integration/self-registration/captive-portal-add_user_to_radius.sh " 
		. $ip 
		. " \"" . $name . "\"" 
		. " \"" . $email . "\"" 
		. " \"" . $owner . "\"" 
		. " \"" . $primary . "\"" 
		. " \"" . $DEFAULT_GROUP . "\"",
		$output, $exitCode);
	?>
	</p>
	<p>If you are not able to access any webpages like <a href="http://www.marsgeneral.com">Mars General</a>, please try it <a href="/">again</a> in a few minutes.</p>

</div>
</body>
</html>