<? 
$HEADLINE = 'Most active devices'; 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
$order = $_GET['order']; 
?>


<table style='border-collapse: collapse;'>
	<tr>
		<th rowspan="3">Device</th>
		<td colspan="4"><? echo snapshottime("accounting_snapshot_1"); ?></td>
		<td colspan="4" style='border-left: 1px solid #000;'><? echo snapshottime("accounting_snapshot_2"); ?></td>
		<td colspan="4" style='border-left: 1px solid #000;'><? echo snapshottime("accounting_snapshot_3"); ?></td>
	</tr>
	<tr>
		<th colspan="2"><a href="recent_top_X.php?order=input_rate1">Upload</a></th>
		<th colspan="2"><a href="recent_top_X.php?order=output_rate1">Download</a></th>
		<th colspan="2" style='border-left: 1px solid #000;'><a href="recent_top_X.php?order=input_rate2">Upload</a></th>
		<th colspan="2"><a href="recent_top_X.php?order=output_rate2">Download</a></th>
		<th colspan="2" style='border-left: 1px solid #000;'><a href="recent_top_X.php?order=input_rate3">Upload</a></th>
		<th colspan="2"><a href="recent_top_X.php?order=output_rate3">Download</a></th>
	</tr>
	<tr>
		<th>MB</th>
		<th>bits/s</th>
		<th>MB</th>
		<th>bits/s</th>
		<th style='border-left: 1px solid #000;'>MB</th>
		<th>bits/s</th>
		<th>MB</th>
		<th>bits/s</th>
		<th style='border-left: 1px solid #000;'>MB</th>
		<th>bits/s</th>
		<th>MB</th>
		<th>bits/s</th>
	</tr>

<?
function snapshottime($table) {
	$result = mysql_query("select datetime from $table limit 1") or trigger_error(mysql_error()); 
	if($row = mysql_fetch_array($result)) { 
		foreach($row AS $key => $value) { $row[$key] = stripslashes($value); }
	} 
	return $row[0];
}


function throughput_upordown_old($topX, $order) {
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
	from accounting_snapshot snap, daily_accounting_v2 da, userinfo ui, radusergroup g, left join accounting_snapshot_2 snap2 on snap2.username = snap.username
	where da.username = snap.username and da.day = date_format(now(), '%Y-%m-%d') and date_format(snap.datetime, '%Y-%m-%d') = date_format(now(), '%Y-%m-%d') and ui.username = da.username and ui.username = g.username
	ORDER BY " . $order . " DESC LIMIT " . $topX;
}

function throughput_upordown($topX, $order, $table1, $table2, $table3, $columnidentifier1, $columnidentifier2, $columnidentifier3) {
	return "
		select da.username,
		snap1.datetime as time" . $columnidentifier1 . ", 
		ROUND((da.day_total_input - snap1.input) / 1000000) as input" . $columnidentifier1 . ", 
		ROUND((da.day_total_output - snap1.output) / 1000000) as output" . $columnidentifier1 . ", 
		ROUND((da.day_total_input - snap1.input) / timestampdiff(SECOND, snap1.datetime, now())) as input_rate" . $columnidentifier1 . ", 
		ROUND((da.day_total_output - snap1.output) / timestampdiff(SECOND, snap1.datetime, now())) as output_rate" . $columnidentifier1 . ", 

	    CONCAT(ui.firstname, ' ', ui.lastname), ui.hostname, g.groupname,

		snap2.datetime as time" . $columnidentifier2 . ", 
		ROUND((da.day_total_input - snap2.input) / 1000000) as input" . $columnidentifier2 . ", 
		ROUND((da.day_total_output - snap2.output) / 1000000) as output" . $columnidentifier2 . ", 
		ROUND((da.day_total_input - snap2.input) / timestampdiff(SECOND, snap2.datetime, now())) as input_rate" . $columnidentifier2 . ", 
		ROUND((da.day_total_output - snap2.output) / timestampdiff(SECOND, snap2.datetime, now())) as output_rate" . $columnidentifier2 . ", 

		snap3.datetime as time" . $columnidentifier3 . ", 
		ROUND((da.day_total_input - snap3.input) / 1000000) as input" . $columnidentifier3 . ", 
		ROUND((da.day_total_output - snap3.output) / 1000000) as output" . $columnidentifier3 . ", 
		ROUND((da.day_total_input - snap3.input) / timestampdiff(SECOND, snap3.datetime, now())) as input_rate" . $columnidentifier3 . ", 
		ROUND((da.day_total_output - snap3.output) / timestampdiff(SECOND, snap3.datetime, now())) as output_rate" . $columnidentifier3 . " 

	from " . $table1 . " snap1 
		left join " . $table2 . "  snap2 on snap2.username = snap1.username
		left join " . $table3 . "  snap3 on snap3.username = snap1.username, 
		daily_accounting_v2 da, userinfo ui, radusergroup g
	where da.username = snap1.username and da.day = date_format(now(), '%Y-%m-%d') and date_format(snap1.datetime, '%Y-%m-%d') = date_format(now(), '%Y-%m-%d') and ui.username = da.username and ui.username = g.username
	ORDER BY " . $order . " DESC LIMIT " . $topX;
}

if (strpos($order, '1') !== FALSE) {
	$result = mysql_query(throughput_upordown(20, $order, "accounting_snapshot_1", "accounting_snapshot_2", "accounting_snapshot_3", "1", "2", "3"))  or trigger_error(mysql_error()); 
} else if (strpos($order, '2') !== FALSE) {
	$result = mysql_query(throughput_upordown(20, $order, "accounting_snapshot_2", "accounting_snapshot_1", "accounting_snapshot_3", "2", "1", "3"))  or trigger_error(mysql_error()); 
} else if (strpos($order, '3') !== FALSE) {
	$result = mysql_query(throughput_upordown(20, $order, "accounting_snapshot_3", "accounting_snapshot_2", "accounting_snapshot_1", "3", "2", "1"))  or trigger_error(mysql_error()); 
}

while($row = mysql_fetch_array($result)){ 
	foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
	echo "<tr>";  
	echo "<td>" .  $row[6] . " (" . $row[8] . " " . $row[7] . " <a href=/mars/userinfo/edit.php?username=$row[0]>$row[0]</a>)" . "</td>";
	echo "<td>" . nl2br( $row["input1"]) . "</td>";
	echo "<td>" . nl2br( $row["input_rate1"]) . "</td>";
	echo "<td>" . nl2br( $row["output1"]) . "</td>";
	echo "<td>" . nl2br( $row["output_rate1"]) . "</td>";
	echo "<td style='border-left: 1px solid #000;'>" . nl2br( $row["input2"]) . "</td>";
	echo "<td>" . nl2br( $row["input_rate2"]) . "</td>";
	echo "<td>" . nl2br( $row["output2"]) . "</td>";
	echo "<td>" . nl2br( $row["output_rate2"]) . "</td>";
	echo "<td style='border-left: 1px solid #000;'>" . nl2br( $row["input3"]) . "</td>";
	echo "<td>" . nl2br( $row["input_rate3"]) . "</td>";
	echo "<td>" . nl2br( $row["output3"]) . "</td>";
	echo "<td>" . nl2br( $row["output_rate3"]) . "</td>";
	echo "</tr>";  
}
?>
</table>

<br/>

<p>(Transfer volume (MB) updated every 5 minutes; devices only included if active/online before start of the relevant period)</p>

<br/>

</div>
</body>
