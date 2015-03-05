
<span style="font-variant:small-caps; font-size:200%">
	<table align="center">
		<tr><td><img src="/captiveportal-mars.jpg" /></td><td>Portal</td></tr>
	</table>
</span> 

<hr/><br/>

<div align="center">
	<?php
		$ip=$_SERVER['REMOTE_ADDR'];
		exec("/home/marsPortal/daloradius-integration/captive-portal-check_device_status.sh " . $ip, $output, $exitCode);				
		
		switch ($exitCode) {
			case 0:
				// device enabled and no restrictions apply. should not happen as the captive portal should have automattically logged it in before this check
				break;
			case 1:
				// not yet registered
				$redir = $_GET['redirurl']; 
				exec("/home/marsPortal/daloradius-integration/echo-add-user-link.sh " . $ip . " $redir", $out, $exit);
				echo "<b>Unknown device. Please consult the IT team" . implode(" ", $out) . ".</b>";
				echo "<p>Once the IT team has given access, please try again: <a href=$PORTAL_REDIRURL$>$PORTAL_REDIRURL$</a></p>";
				echo "<br/><br/><p>Exit code: $exitCode</p>";
				echo "<p>   (Reason: " . implode(" ", $output) . ")</p>";
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
				break;
			case 4:
				// device disabled
				echo "<b>Your device is disabled. Please see the IT team for further explanation.</b>";
				echo "<br/><br/><p>Exit code: $exitCode</p>";
				echo "<p>   (Reason: " . implode(" ", $output) . ")</p>";
				break;
			case 5:
				// data bundle during business hours exceeded
				echo "<p><b>Your device has reached the maximum daily data bundle during working hours (Monday to Friday from 08:00-12:30 and 13:30-16:30). Please try again tomorrow.</b></p>";
				echo "<p>Exit code: $exitCode - (Reason: " . implode(" ", $output) . ")</p>";
				exec("/home/marsPortal/daloradius-integration/echo-user-data-statistics-link.sh " . $ip, $out, $exit);
				echo "<p>In doubt, check your data usage of the last 7 days: " . implode(" ", $out) . "</p>";
				break;
			default:
				// unknown response or server down
				echo "<p><b>Network not available. Please see the IT team.</b></p>";
				echo "<br/><br/><p>Exit code: $exitCode</p>";
				echo "<p>   (Reason: " . implode(" ", $output) . ")</p>";
		}  
	?>
</div>