  <hr/>
  <p>Devices restricted/blocked</p>
  

<?php
	echo "<table><tr><th></th><th>Today ($today)</th><th>Yesterday ($yesterday)</th><th>Last 7 days (from $daysago7)</th><th>Last 30 days (from $daysago30)</th></tr>";
	
	exec("/home/marsPortal/misc/client_activity_logs.sh", $output, $exitCode);
	$output_a=implode(" ", $output);
	echo "$output_a";
	echo "</table>";
?>
<p>Same device can be counted mutliple times during 7 and 30 days periods.</p>