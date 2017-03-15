<html>
<head>
	<meta http-equiv="Refresh" content="0; url=http://www.google.com/" />
</head>

<body>
<span style="font-variant:small-caps; font-size:200%">
	<table align="center">
		<tr><td><img src="/captiveportal-mars.jpg" /></td><td>Portal</td></tr>
	</table>
</span> 

<hr/><br/>

<div align="center">

<div>
	Auto-detecting device...
</div>

	<p><?php
		$ip=$_SERVER['REMOTE_ADDR'];
		$name="";
		$email="";
		$owner="";
		$primary="";
	    echo "The registration process should be done for " . $ip;
		exec("/home/marsPortal/freeradius-integration/self-registration/captive-portal-add_user_to_radius.sh " 
		. $ip 
		. " \"" . $name . "\"" 
		. " \"" . $email . "\"" 
		. " \"" . $owner . "\"" 
		. " \"" . $primary . "\"" 
		. " \"" . "Users" . "\"",
		$output, $exitCode);
	?>
	</p>
	<p>If you are not able to access any webpages like <a href="http://www.google.com">Mars General</a>, please try it <a href="/">again</a> in a few minutes.</p>

</div>
</body>
</html>