<? 
$HEADLINE = 'User usage statistics (' . date('Y-m-d H:i:s') . ')';
include '../common.php'; 
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

function userdetailslinks($usernames) {
	$links = 'Devices:';
	$names = array_unique(explode(',', $usernames)); // danger, dont know why sometimes macs appear multipel times
	for ($i = 0; $i < count($names); $i++) {
		$links = $links . ' <a href="/mars/userinfo/edit.php?username=' . $names[$i] . '">' . $i . '</a>';
	}
	return $links;
}

function uservolumelinks($usernames) {
	$links = 'Data:';
	$names = array_unique(explode(',', $usernames)); // danger, dont know why sometimes macs appear multipel times
	for ($i = 0; $i < count($names); $i++) {
		$links = $links . ' <a href="/mars/device_with_volume.php?username=' . $names[$i] . '">' . $i . '</a>';
	}
	return $links;
}

function userinfo($row, $upordown) {
	echo $row[$upordown] . ", " . uservolumelinks($row['usernames']) . "<br/>" . $row['name'] . ", " . userdetailslinks($row['usernames']) . "<br/>" . $row['department']. " " . $row['email'] . " " . $row['groupname'] . " " . $row['company'];	
}

?>


<?php
require dirname(__FILE__)."/users-statistics-work.php";

generateworktraffic('Download', $today, $yesterday, $daysago7, $daysago30);
generateworktraffic('Upload', $today, $yesterday, $daysago7, $daysago30);
 
require dirname(__FILE__)."/users-statistics-daily.php";
	
generatedailytraffic('Download', $today, $yesterday, $daysago7, $daysago30);
generatedailytraffic('Upload', $today, $yesterday, $daysago7, $daysago30);

?>

<hr/>
<span class="headline"><p>User usage statistics (<?php echo date('Y-m-d H:i:s')?>)</p></span>
