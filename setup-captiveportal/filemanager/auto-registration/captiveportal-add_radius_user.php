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
	<p>If you are not able to access any webpages like <a href="http://www.marsgeneral.com">Mars General</a>, please try it <a href="/">again</a> in a few minutes.</p>

</div>
</body>
</html>