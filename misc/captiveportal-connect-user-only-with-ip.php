<?php 

require("captiveportal.inc");

include('/home/marsPortal/mars-user-interface/www/mars/config.php'); 

$cpzone = $ZONE;
$ip = $argv[1];
$mac = `arp $ip | cut -d " " -f4 | tr -d '\n'`;

if (in_array($mac, array_column(captiveportal_read_db(), 'mac'))) {
	echo "Already auth'd " . $mac . " with " . $ip . " on " . $cpzone;
	`echo "$mac - $ip - w - prevent another automatic login during cp detection - $(date +%Y%m%d-%H%M%S)" >> /home/client_activities_log/status-$(date +%Y%m%d).log`;
} else {
	$zeit = `date`;

	echo "Auth " . $mac . " with " . $ip . " on " . $cpzone . " at " . $zeit;

	`echo "$mac - $ip - z - automatic login during cp detection - $(date +%Y%m%d-%H%M%S)" >> /home/client_activities_log/status-$(date +%Y%m%d).log`;
	`echo "Auth $mac with $ip on $cpzone at $zeit" >> /tmp/abc`;

	portal_mac_radius($mac, $ip, "first");
}

?>

