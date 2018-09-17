<?php 

require("captiveportal.inc");

include('/home/marsPortal/mars-user-interface/www/mars/config.php'); 

$cpzone = $ZONE;
$ip = $argv[1];
$mac = `arp $ip | cut -d " " -f4 | tr -d '\n'`;

echo "Auth " . $mac . " with " . $ip . " on " . $cpzone;

`echo "Auth " . $mac . " with " . $ip . " on " . $cpzone >> /tmp/abc`;

portal_mac_radius($mac, $ip, "first")

?>

