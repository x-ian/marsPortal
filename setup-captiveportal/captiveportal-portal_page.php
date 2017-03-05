
<span style="font-variant:small-caps; font-size:200%">
	<table align="center">
		<tr><td><img src="/captiveportal-mars.jpg" /></td><td>Portal</td></tr>
	</table>
</span> 

<hr/><br/>

<div align="center">
	<?php
		$ip=$_SERVER['REMOTE_ADDR'];
		exec("/home/marsPortal/freeradius-integration/captive-portal-check_device_status.sh " . $ip, $output, $exitCode);				
		
		switch ($exitCode) {
			case 0:
				// device enabled and no restrictions apply. should not happen as the captive portal should have automattically logged it in before this check
				echo "<p>Oops. Captive Portal device check with exit code 0. Shouldn't happen, but apparently did...</p>";
				break;
			case 1:
				// not yet registered
				
				// give password-protected way to directly add device entry to radius
				//$redir = $_GET['redirurl']; 
				//exec("/home/marsPortal/freeradius-integration/echo-add-user-link.sh " . $ip . " " . $redir, $out, $exit);
				//echo "<b>Unknown device. Please consult the IT team" . implode(" ", $out) . ".</b>";
				//echo "<p>Once the IT team has given access, please try again: <a href=$PORTAL_REDIRURL$>$PORTAL_REDIRURL$</a></p>";
				//echo "<br/><br/><p>Exit code: $exitCode</p>";
				//echo "<p>   (Reason: " . implode(" ", $output) . ")</p>";
				
				
				// provide self registration capabilty
				include '/usr/local/captiveportal/captiveportal-device_registration.html';
				
				break;
			case 2:
				// too many users
				echo "<p><b>Too many users. Please try again later.</b></p>";
				echo "<br/><br/><p>Exit code: $exitCode</p>";
				echo "<p>   (Reason: " . implode(" ", $output) . ")</p>";
				break;
			case 3:
				// access denied with additional restrictions
				echo "<p><b>Your device has used up your available data volume. Either check back tomorrow or next week.</b></p>";
				echo "<br/><br/><p>Exit code: $exitCode</p>";
				echo "<p>   (Reason: " . implode(" ", $output) . ")</p>";
				exec("/home/marsPortal/freeradius-integration/echo-user-data-statistics-link.sh " . $ip, $out, $exit);
				echo "<p>In doubt, check your data usage of the last 7 days: " . implode(" ", $out) . "</p>";
				break;
			case 4:
				// device disabled
				echo "<b>Your device is disabled. Please see the IT team for further explanation.</b>";
				echo "<br/><br/><p>Exit code: $exitCode</p>";
				echo "<p>   (Reason: " . implode(" ", $output) . ")</p>";
				break;
			case 5:
				// data bundle during business hours exceeded
				echo "<p><b>Your device has reached the maximum daily data bundle during working hours (Monday to Friday from 07:30-12:00 and 13:30-17:00). Please try again tomorrow.</b></p>";
				echo "<p>Exit code: $exitCode - (Reason: " . implode(" ", $output) . ")</p>";
				exec("/home/marsPortal/freeradius-integration/echo-user-data-statistics-link.sh " . $ip, $out, $exit);
				echo "<p>In doubt, check your data usage of the last 7 days: " . implode(" ", $out) . "</p>";
				break;
			case 6:
				// rejected with reply message
				echo "<p><b>Network access was rejected with below error message</b></p>";
				echo "<p>Exit code: $exitCode - (Reason: " . implode(" ", $output) . ")</p>";
				break;
			case 7:
				// access denied with additional restrictions
				echo "<p><b>All your devices have used up your available data volume. Please try again tomorrow.</b></p>";
				echo "<br/><br/><p>Exit code: $exitCode</p>";
				echo "<p>   (Reason: " . implode(" ", $output) . ")</p>";
				exec("/home/marsPortal/freeradius-integration/echo-user-data-statistics-link.sh " . $ip, $out, $exit);
				echo "<p>In doubt, check your data usage of the last 7 days: " . implode(" ", $out) . " (Careful: Only your current device is listed)</p>";
				break;
			case 8:
				// data bundle during business hours exceeded
				echo "<p><b>All your devices have reached the maximum daily data bundle during working hours (Monday to Friday from 07:30-12:00 and 13:30-17:00). Please try again tomorrow.</b></p>";
				echo "<p>Exit code: $exitCode - (Reason: " . implode(" ", $output) . ")</p>";
				exec("/home/marsPortal/freeradius-integration/echo-user-data-statistics-link.sh " . $ip, $out, $exit);
				echo "<p>In doubt, check your data usage of the last 7 days: " . implode(" ", $out) . " (Careful: Only your current device is listed)</p>";
				break;
			default:
				// unknown response or server down
				echo "<p><b>Network not available. Please see the IT team.</b></p>";
				echo "<br/><br/><p>Exit code: $exitCode</p>";
				echo "<p>   (Reason: " . implode(" ", $output) . ")</p>";
		}  
	?>
</div>