<? 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
include('../config.php'); 

$today = date('Y-m-d', strtotime('-0 day'));
$yesterday = date('Y-m-d', strtotime('-1 day'));
$daysago7 = date('Y-m-d', strtotime('-6 days'));
$daysago30 = date('Y-m-d', strtotime('-29 days'));	

function total($startday, $endday) {
	return "
		select 
		groupname,
		ROUND((SUM(day_total_input) - SUM(day_offset_input)) / 1000000) as Upload, 
		ROUND((SUM(day_total_output) - SUM(day_offset_output)) / 1000000) as Download 
		FROM radusergroup 
		LEFT JOIN daily_accounting_v2 ON radusergroup.username = daily_accounting_v2.username
		WHERE daily_accounting_v2.day >= \"" . $startday . "\" AND daily_accounting_v2.day <= \"" . $endday . "\" 
		GROUP BY radusergroup.groupname;";
}
?>

<span style="font-variant:small-caps; font-size:200%">
	<p align="center">
		marsPortal Group Statistics (<?php echo $today; ?>)
	</p>
</span>


Today:
<table><tr><th>Group</th><th>Download</th><th>Upload</th></tr>
<?php
$result = mysql_query(total($today, $today)) or trigger_error(mysql_error()); 
while($row = mysql_fetch_array($result)){ 
	foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
	echo "<tr><td>" . $row[0] . "</td><td>" . $row[2] . "</td><td>" . $row[1] . "</td></tr>";
}
?>
</table>
<hr/>
Yesterday:
<table><tr><th>Group</th><th>Download</th><th>Upload</th></tr>
<?php
$result = mysql_query(total($yesterday, $yesterday)) or trigger_error(mysql_error()); 
while($row = mysql_fetch_array($result)){ 
	foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
	echo "<tr><td>" . $row[0] . "</td><td>" . $row[2] . "</td><td>" . $row[1] . "</td></tr>";
}
?>
</table>
<hr/>

Last 7 days:
<table><tr><th>Group</th><th>Download</th><th>Upload</th></tr>
<?php
$result = mysql_query(total($daysago7, $today)) or trigger_error(mysql_error()); 
while($row = mysql_fetch_array($result)){ 
	foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
	echo "<tr><td>" . $row[0] . "</td><td>" . $row[2] . "</td><td>" . $row[1] . "</td></tr>";
}
?>
</table>
<hr/>

Last 30 days:
<table><tr><th>Group</th><th>Download</th><th>Upload</th></tr>
<?php
$result = mysql_query(total($daysago30, $today)) or trigger_error(mysql_error()); 
while($row = mysql_fetch_array($result)){ 
	foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
	echo "<tr><td>" . $row[0] . "</td><td>" . $row[2] . "</td><td>" . $row[1] . "</td></tr>";
}
?>
</table>