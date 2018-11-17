<?php 

require("captiveportal.inc");

include('/home/marsPortal/mars-user-interface/www/mars/config.php'); 

$cpzone = $ZONE;
$ip = $argv[1];
$mac = $argv[2];

if (in_array($mac, array_column(captiveportal_read_db(), 'mac'))) {
	echo "Already auth'd " . $mac . " with " . $ip . " on " . $cpzone;
} else {
	// no session yet, login
	echo "Auth " . $mac . " with " . $ip . " on " . $cpzone;
	portal_mac_radius($mac, $ip, "first");
}

?>

