<?php 

require("captiveportal.inc");

if (!is_array($config['captiveportal'])) {
	$config['captiveportal'] = array();
}
	
$a_cp =& $config['captiveportal'];
foreach ($a_cp as $captiveportalzone) {
	// set $cp_zone so the correct database will use used
	$cpzone = $captiveportalzone['zone'];
	// also surface the global $cpzoneid
	$cpzoneid = $captiveportalzone['zoneid'];
	// Read the corresponding database
	$cpdb = captiveportal_read_db();
	foreach ($cpdb as $cpent) {
		if ($cpent["ip"] == $argv[1]) {
			captiveportal_disconnect_client($cpent[5]);
		}
	}
	unset($cpdb);
}
unset($a_cp);

?>

