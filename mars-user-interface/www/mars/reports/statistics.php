<? 
$HEADLINE = 'Usage Statistics (' . date('Y-m-d H:i:s') . ')';
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
$today = date('Y-m-d', strtotime('-0 day'));
$yesterday = date('Y-m-d', strtotime('-1 day'));
$daysago7 = date('Y-m-d', strtotime('-6 days'));
$daysago30 = date('Y-m-d', strtotime('-29 days'));	

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
  return '<a href="/mars/userinfo/edit.php?username=' . $mac . '">' . $name . '</a>';
}

function uservolumelink($mac, $linktext) {
  return '<a href="/mars/device_with_volume.php?username=' . $mac . '">' . $linktext . '</a>';
}

function deviceinfo($row, $upordown) {
    echo uservolumelink($row['username'], $row[$upordown]) . " (" . userdetailslink($row['username'], $row['name']). " " . $row['department']. " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";	
}

?>


<?php
require dirname(__FILE__)."/statistics-registration.php";

require dirname(__FILE__)."/statistics-blocked.php";

require dirname(__FILE__)."/statistics-work.php";

generateworktraffic('Download', $today, $yesterday, $daysago7, $daysago30);
generateworktraffic('Upload', $today, $yesterday, $daysago7, $daysago30);
 
require dirname(__FILE__)."/statistics-daily.php";
	
generatedailytraffic('Download', $today, $yesterday, $daysago7, $daysago30);
generatedailytraffic('Upload', $today, $yesterday, $daysago7, $daysago30);

require dirname(__FILE__)."/statistics-current-groups.php";
?>

<hr/>
<span class="headline"><p>Usage Statistics (<?php echo date('Y-m-d H:i:s')?>)</p></span>
