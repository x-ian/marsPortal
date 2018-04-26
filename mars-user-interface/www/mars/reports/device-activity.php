<? 
$HEADLINE = 'Device activity'; 
include '../common.php'; 
include '../menu.php'; 
?>

<?php $username = $_GET['username']; ?>

<!-- begin page-specific content ########################################### -->
    <div id="main">
      <div class="page-header" align="center">
  	    <h1>Device activity at <?= date('Y-m-d H:i:s'); ?></h1>
		<?= dropdown_link_to_device($username) ?>
	  </div>

<?php
  function query($query) {
    $result = mysql_query($query);
	if (!$result) {
		$message  = 'Ungültige Abfrage: ' . mysql_error() . "\n";
		$message .= 'Gesamte Abfrage: ' . $query;
    	die($message);
	} 
	return $result;
  }
  
  echo "
  <table class='table table-striped table-bordered '>
  	<thead><tr>
	<th>Day</th>";
    
for ($i=0; $i<=23; $i++) {
	echo "<td colspan='2' align='left'>" . $i . ":00</td>";
}

	echo "</tr></thead><tbody>";
	//  bgcolor="#00FF00">
	
  function activity($device) {
	  $a = "
	SELECT 
	day,
	0029_input, 0059_input, 0129_input, 0159_input, 0229_input, 0259_input, 0329_input, 0359_input, 0429_input, 0459_input, 0529_input, 0559_input, 0629_input, 0659_input, 0729_input, 0759_input, 0829_input, 0859_input, 0929_input, 0959_input, 1029_input, 1059_input, 1129_input, 1159_input, 1229_input, 1259_input, 1329_input, 1359_input, 1429_input, 1459_input, 1529_input, 1559_input, 1629_input, 1659_input, 1729_input, 1759_input, 1829_input, 1859_input, 1929_input, 1959_input, 2029_input, 2059_input, 2129_input, 2159_input, 2229_input, 2259_input, 2329_input, 2359_input,
	0029_output, 0059_output, 0129_output, 0159_output, 0229_output, 0259_output, 0329_output, 0359_output, 0429_output, 0459_output, 0529_output, 0559_output, 0629_output, 0659_output, 0729_output, 0759_output, 0829_output, 0859_output, 0929_output, 0959_output, 1029_output, 1059_output, 1129_output, 1159_output, 1229_output, 1259_output, 1329_output, 1359_output, 1429_output, 1459_output, 1529_output, 1559_output, 1629_output, 1659_output, 1729_output, 1759_output, 1829_output, 1859_output, 1929_output, 1959_output, 2029_output, 2059_output, 2129_output, 2159_output, 2229_output, 2259_output, 2329_output, 2359_output
	from daily_accounting_v5 where username='" . $device . "' 
	order by day desc;";  
	//echo $a;
	return $a;
  }
  
$all_activities = query(activity($username));
$previous_day = date('Y-m-d');
$previous_day_date = date_create_from_format('Y-m-d', $previous_day);
while ($row = mysql_fetch_assoc($all_activities)) {
	$day = $row['day'];
/*	echo $day. ' + ' . $previous_day;
	echo "<br/>";
	date_sub($previous_day_date, date_interval_create_from_date_string('1 day'));
	$previous_day = date_format($previous_day_date, 'Y-m-d');
	echo $day. ' _ ' . $previous_day;
	echo "<br/>";
*/	
	while ($day < $previous_day) {
		
		// last activity in the past, fill out all days between then and now
		echo "<tr><td class='text-nowrap'>" . $previous_day . "</td><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/></tr>";
		date_sub($previous_day_date, date_interval_create_from_date_string('1 day'));
		$previous_day = date_format($previous_day_date, 'Y-m-d');
	}

	echo "<tr>";
    echo '<td class="text-nowrap">' . $row['day'] . '</td>';
	for ($i=0; $i<=23; $i++) {
		if ($row[sprintf('%02d',$i) . "29_input"] > 0 || $row[sprintf('%02d', $i) . '29_output'] > 0) {
			echo '<td bgcolor="#00FF00"/>';
		} else {
			echo '<td/>';
		}
		if ($row[sprintf('%02d', $i) . "59_input"] > 0 || $row[sprintf('%02d', $i) . '59_output'] > 0) {
			echo '<td bgcolor="#00FF00"/>';
		} else {
			echo '<td/>';
		}
	}
	$day_date = date_create_from_format('Y-m-d', $day);
	date_sub($day_date, date_interval_create_from_date_string('1 day'));
	$previous_day_date = $day_date;
	$previous_day = date_format($day_date, 'Y-m-d');
	echo "</tr>";
}
?>
</tbody></table>

<br/>

<p>Only times with actual data traffic are listed; (briefly) connected without any data (e.g. via WiFi) is ignored.</p>

<br/>
