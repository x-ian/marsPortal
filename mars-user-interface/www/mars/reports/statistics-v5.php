<? 
$HEADLINE = 'Usage Statistics (' . date('Y-m-d H:i:s') . ')';
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">
      <div class="page-header">
  	    <h1>Traffic volme <?php echo date('Y-m-d H:i:s'); ?></h1>
	  </div>

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
	$name = "";
	if ($row['firstname'] !== '') {
		$name .= $row['firstname'] . " ";
	}
	if ($row['lastname'] !== '') {
		$name .= $row['lastname'] . " ";
	}
	if ($name !== '') {
		$name .= ' - ';
	}	
	if ($row['hostname'] !== '') {
		$name .= $row['hostname'];
	}
	$hoover = "";
	if ($row['groupname'] !== '') {
		$hoover .= $row['groupname'] . " - ";
	}
	if ($row['mac_vendor'] !== '') {
		$hoover .= $row['mac_vendor'];
	}

    echo uservolumelink($row['username'], $row[$upordown]) . " (" . '<a href="/mars/userinfo/edit.php?username=' . $row['username'] . '" data-html="true" data-toggle="tooltip" title="' . $hoover . '">' . $name . '</a>)';

//    echo uservolumelink($row['username'], $row[$upordown]) . " (" . userdetailslink($row['username'], $row['name']). " " . $row['department']. " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";	
}

?>

<?php
//require dirname(__FILE__)."/statistics-registration.php";

//require dirname(__FILE__)."/statistics-blocked.php";

//require dirname(__FILE__)."/statistics-work.php";

//generateworktraffic('Download', $today, $yesterday, $daysago7, $daysago30);
//generateworktraffic('Upload', $today, $yesterday, $daysago7, $daysago30);
 
require dirname(__FILE__)."/statistics-daily-v5.php";

echo "<div class=\"page-header\"><h1>Top downloads</h1></div>";
generatedailytraffic('Download', $today, $yesterday, $daysago7, $daysago30);

echo "<div class=\"page-header\"><h1>Top uploads</h1></div>";
generatedailytraffic('Upload', $today, $yesterday, $daysago7, $daysago30);

//require dirname(__FILE__)."/statistics-current-groups.php";
?>

<hr/>
<span class="headline"><p>Usage Statistics (<?php echo date('Y-m-d H:i:s')?>)</p></span>
