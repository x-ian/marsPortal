<? 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
include('../config.php'); 
$order = $_GET['order']; 
?>

<a href="recent_top_X.php?order=output">Sort by Download</a> <a href="recent_top_X.php?order=input">Sort by Upload</a>

<br/><br/>

<table>
	<tr>
		<th>User</th>
		<th>Upload (in MB)</th>
		<th>Average rate (in bps)</th>
		<th>Download (in MB)</th>
		<th>Average rate (in bps)</th>
	</tr>

<?
function throughput_upordown($topX, $order) {
	return "
	select da.username, 
		snap.datetime, 
		ROUND((da.day_total_input - snap.input) / 1000000) as input, 
		ROUND((da.day_total_output - snap.output) / 1000000) as output, 
		ROUND((da.day_total_input - snap.input) / timestampdiff(SECOND, snap.datetime, now())) as input_rate,
		ROUND((da.day_total_output - snap.output) / timestampdiff(SECOND, snap.datetime, now())) as output_rate,
		CONCAT(ui.firstname, ' ', ui.lastname),
		ui.hostname,
		g.groupname
	from accounting_snapshot snap, daily_accounting_v2 da, userinfo ui, radusergroup g
	where da.username = snap.username and da.day = date_format(now(), '%Y-%m-%d') and date_format(snap.datetime, '%Y-%m-%d') = date_format(now(), '%Y-%m-%d') and ui.username = da.username and ui.username = g.username
	ORDER BY " . $order . " DESC LIMIT " . $topX;
}

$result = mysql_query(throughput_upordown(20, $order))  or trigger_error(mysql_error()); 

while($row = mysql_fetch_array($result)){ 
	foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
	echo "<tr>";  
	echo "<td>" .  $row[6] . " (" . $row[8] . " " . $row[7] . " <a href=/mars/userinfo/edit.php?username=$row[0]>$row[0]</a>)" . "</td>";
	echo "<td>" . nl2br( $row[2]) . "</td>";
	echo "<td>" . nl2br( $row[4]) . "</td>";
	echo "<td>" . nl2br( $row[3]) . "</td>";
	echo "<td>" . nl2br( $row[5]) . "</td>";
	echo "</tr>";  
}
?>
</table>

<br/>

<?
$result = mysql_query("select datetime from accounting_snapshot limit 1") or trigger_error(mysql_error()); 
if($row = mysql_fetch_array($result)){ 
	foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
	echo "Transfer volumes since " . $row[0] . "; upload/download values updated every 5 minutes";
}
?>

</div>
</body>
