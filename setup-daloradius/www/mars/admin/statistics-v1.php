<?php

$today = date('Y-m-d', strtotime('-0 day'));
$yesterday = date('Y-m-d', strtotime('-1 day'));
$daysago7 = date('Y-m-d', strtotime('-6 days'));
$daysago30 = date('Y-m-d', strtotime('-29 days'));	

mysql_connect('localhost','radius','radius') or die('Could not connect to mysql server.');
mysql_select_db('radius');

function query($query) {
  $result = mysql_query($query);
	if (!$result) {
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Full query: ' . $query;
	  	die($message);
	} 
	return $result;
}

function userdetailslink($mac, $name) {
  return '<a href="/daloradius/mng-edit.php?username=' . $mac . '">' . $name . '</a>';
}

function uservolumelink($mac, $linktext) {
  return '<a href="/mars/user_with_volume-v1?username=' . $mac . '">' . $linktext . '</a>';
}

function deviceinfo($row, $upordown) {
    echo uservolumelink($row['username'], $row[$upordown]) . " (" . userdetailslink($row['username'], $row['name']). " " . $row['department']. " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";	
}

?>

<span style="font-variant:small-caps; font-size:200%">
	<p align="center">
		marsPortal Usage Statistics (<?php echo $today; ?>)
	</p>
</span>


<?php
require dirname(__FILE__)."/statistics-registration.php";

require dirname(__FILE__)."/statistics-work-v1.php";

generateworktraffic('Download', $today, $yesterday, $daysago7, $daysago30);
generateworktraffic('Upload', $today, $yesterday, $daysago7, $daysago30);
 
require dirname(__FILE__)."/statistics-daily-v1.php";
	
generatedailytraffic('Download', $today, $yesterday, $daysago7, $daysago30);
generatedailytraffic('Upload', $today, $yesterday, $daysago7, $daysago30);

require dirname(__FILE__)."/statistics-current-groups.php";
?>