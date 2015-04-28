  <hr/>
  <p>Devices restricted/blocked</p>
  

<?php
	echo "<table><tr><th></th><th>Today ($today)</th><th>Yesterday ($yesterday)</th><th>Last 7 days (from/at $daysago7)</th><th>Last 30 days (from/at $daysago30)</th></tr>";
	
	exec("/home/marsPortal/misc/client_activity_logs.sh", $output, $exitCode);				
	echo "$output";
	echo "</table>";
?>