<? 
$HEADLINE = 'Group statistics'; 
include '../common.php'; 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">
      <div class="page-header">
  	    <h1>Group statistics <?php echo date('Y-m-d H:i:s'); ?></h1>
	  </div>

<? 

function query($query) {
  $result = mysql_query($query);
	if (!$result) {
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Full query: ' . $query;
	  	die($message);
	} 
	return $result;
}

$today = date('Y-m-d', strtotime('-0 day'));
$yesterday = date('Y-m-d', strtotime('-1 day'));
$daysago7 = date('Y-m-d', strtotime('-6 days'));
$daysago30 = date('Y-m-d', strtotime('-29 days'));	

function total($from_date, $to_date, $upordown) {
	$a= "
		SELECT 
		groupname, 
		ROUND(SUM(0029_input+0059_input+0129_input+0159_input+0229_input+0259_input+0329_input+0359_input+0429_input+0459_input+0529_input+0559_input+0629_input+0659_input+0729_input+0759_input+0829_input+0859_input+0929_input+0959_input+1029_input+1059_input+1129_input+1159_input+1229_input+1259_input+1329_input+1359_input+1429_input+1459_input+1529_input+1559_input+1629_input+1659_input+1729_input+1759_input+1829_input+1859_input+1929_input+1959_input+2029_input+2059_input+2129_input+2159_input+2229_input+2259_input+2329_input+2359_input) / 1000000) as upload,
		ROUND(SUM(0029_output+0059_output+0129_output+0159_output+0229_output+0259_output+0329_output+0359_output+0429_output+0459_output+0529_output+0559_output+0629_output+0659_output+0729_output+0759_output+0829_output+0859_output+0929_output+0959_output+1029_output+1059_output+1129_output+1159_output+1229_output+1259_output+1329_output+1359_output+1429_output+1459_output+1529_output+1559_output+1629_output+1659_output+1729_output+1759_output+1829_output+1859_output+1929_output+1959_output+2029_output+2059_output+2129_output+2159_output+2229_output+2259_output+2329_output+2359_output) / 1000000) as download
		from daily_accounting_v5, radusergroup where daily_accounting_v5.username = radusergroup.username
		AND daily_accounting_v5.day >= '" . $from_date . "' AND daily_accounting_v5.day <= '" . $to_date . "'
		GROUP BY groupname
		ORDER BY " . $upordown . " DESC;";
		//echo $a;
		return $a;
}

$down_today = query(total($today, $today, "download"));
$down_yesterday = query(total($yesterday, $yesterday, "download"));
$down_last7days = query(total($daysago7, $today, "download"));
$down_last30days = query(total($daysago30, $today, "download"));

$up_today = query(total($today, $today, "upload"));
$up_yesterday = query(total($yesterday, $yesterday, "upload"));
$up_last7days = query(total($daysago7, $today, "upload"));
$up_last30days = query(total($daysago30, $today, "upload"));

?>

<div class="page-header">
	<h1>Upload</h1>
</div>
<table class='table table-striped'>
	<thead><tr>
		<th>Today</th>
		<th>Yesterday</th>
		<th>Last 7 days</th>
		<th>Last 30 days</th>
	</tr></thead>
	<tbody>
<?php
	while ($row_30 = mysql_fetch_assoc($up_last30days)) {
		echo "<tr>";
		echo "<td>";
		if ($row_today = mysql_fetch_assoc($up_today)) {
			echo $row_today['upload'] . "&nbsp;" . $row_today['groupname'];
		}
		echo "</td><td>";
		if ($row_yesterday = mysql_fetch_assoc($up_yesterday)) {
			echo $row_yesterday['upload'] . "&nbsp;" . $row_yesterday['groupname'];
		}
		echo "</td><td>";
		if ($row_7 = mysql_fetch_assoc($up_last7days)) {
			echo $row_7['upload'] . "&nbsp;" . $row_7['groupname'];
		}
		echo "</td><td>";
		echo $row_30['upload'] . "&nbsp;" . $row_30['groupname'];
		echo "</td></tr>";
	}
?>
</tbody></table>

<div class="page-header">
	<h1>Download</h1>
</div>
<table class='table table-striped'>
	<thead><tr>
		<th>Today</th>
		<th>Yesterday</th>
		<th>Last 7 days</th>
		<th>Last 30 days</th>
	</tr></thead>
	<tbody>

<?php
	while ($row_30 = mysql_fetch_assoc($down_last30days)) {
		echo "<tr>";
		echo "<td>";
		if ($row_today = mysql_fetch_assoc($down_today)) {
			echo $row_today['download'] . "&nbsp;" . $row_today['groupname'];
		}
		echo "</td><td>";
		if ($row_yesterday = mysql_fetch_assoc($down_yesterday)) {
			echo $row_yesterday['download'] . "&nbsp;" . $row_yesterday['groupname'];
		}
		echo "</td><td>";
		if ($row_7 = mysql_fetch_assoc($down_last7days)) {
			echo $row_7['download'] . "&nbsp;" . $row_7['groupname'];
		}
		echo "</td><td>";
		echo $row_30['download'] . "&nbsp;" . $row_30['groupname'];
		echo "</td></tr>";
	}
?>
</tbody></table>