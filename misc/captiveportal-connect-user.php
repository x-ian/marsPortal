<?php 

require("captiveportal.inc");

include('/home/marsPortal/mars-user-interface/www/mars/config.php'); 

$cpzone = $ZONE;
$ip = $argv[1];
$mac = $argv[2];

echo "Auth " . $mac . " with " . $ip . " on " . $cpzone;

portal_mac_radius($mac, $ip, "first")

?>

