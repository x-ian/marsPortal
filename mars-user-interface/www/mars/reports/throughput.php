<? 
$HEADLINE = 'Throughput'; 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
$order = $_GET['order']; 
$period = $_GET['period']; 

$now = date('Y-m-d H:i:s');
$min_ago_5 = date('Y-m-d H:i:s', strtotime('-5 minutes'));
$min_ago_15 = date('Y-m-d H:i:s', strtotime('-15 minutes'));
$hour_ago_1 = date('Y-m-d H:i:s', strtotime('-1 hour'));
$hour_ago_4 = date('Y-m-d H:i:s', strtotime('-4 hours'));
?>

<table class='table table-striped'>
	<thead><tr>
		<th rowspan="3">Device</th>
		<td colspan="2" align="center">Last 5 minutes</td>
		<td colspan="2"  align="center"style='border-left: 1px solid #000;'>Last 15 minutes</td>
		<td colspan="2"  align="center"style='border-left: 1px solid #000;'>Last hour</td>
		<td colspan="2"  align="center"style='border-left: 1px solid #000;'>Last 4 hours</td>
	</tr>
	<tr>
		<th><a href="throughput.php?order=input_rate&period=min_ago_5">Upload</a></th>
		<th><a href="throughput.php?order=output_rate&period=min_ago_5">Download</a></th>
		<th style='border-left: 1px solid #000;'><a href="throughput.php?order=input_rate&period=min_ago_15">Upload</a></th>
		<th><a href="throughput.php?order=output_rate&period=min_ago_15">Download</a></th>
		<th style='border-left: 1px solid #000;'><a href="throughput.php?order=input_rate&period=hour_ago_1">Upload</a></th>
		<th><a href="throughput.php?order=output_rate&period=hour_ago_1">Download</a></th>
		<th style='border-left: 1px solid #000;'><a href="throughput.php?order=input_rate&period=hour_ago_4">Upload</a></th>
		<th><a href="throughput.php?order=output_rate&period=hour_ago_4">Download</a></th>
	</tr></thead>

<?
function snapshottime($table) {
	$result = mysql_query("select datetime from $table limit 1") or trigger_error(mysql_error()); 
	if($row = mysql_fetch_array($result)) { 
		foreach($row AS $key => $value) { $row[$key] = stripslashes($value); }
	} 
	return $row[0];
}

function throughput_upordown($topX, $order, $start, $end) {
	return "
		select distinct(t.username) as username, 
			CONCAT(ui.firstname, ' ', ui.lastname) as name, 
			ui.hostname, g.groupname,
			ROUND((max(offset_input) - min(offset_input)) / 1000000) as input, 
			ROUND((max(offset_output) - min(offset_output)) / 1000000) as output, 
			ROUND(ROUND((max(offset_input) - min(offset_input)) / timestampdiff(SECOND, '$start', '$end')) / 1000) as input_rate, 
			ROUND(ROUND((max(offset_output) - min(offset_output)) / timestampdiff(SECOND, '$start', '$end')) / 1000) as output_rate, 
			max(time_of_day) as max, 
			min(time_of_day) as min 
		from throughput t, userinfo ui, radusergroup g
		where t.username = ui.username and g.username = t.username and 
			time_of_day >= '" . $start . "' and time_of_day <= '" . $end . "' 
		group by t.username order by " . $order . " desc
		LIMIT " . $topX;
}

function throughput_device_upordown($device, $start, $end) {
	return "
		select 
			ROUND((max(offset_input) - min(offset_input)) / 1000000) as input, 
			ROUND((max(offset_output) - min(offset_output)) / 1000000) as output, 
			ROUND(ROUND((max(offset_input) - min(offset_input)) / timestampdiff(SECOND, '$start', '$end')) / 1000) as input_rate, 
			ROUND(ROUND((max(offset_output) - min(offset_output)) / timestampdiff(SECOND, '$start', '$end')) / 1000) as output_rate
		from throughput t
		where t.username = '" . $device . "' and 
			time_of_day >= '" . $start . "' and time_of_day <= '" . $end . "'";
}

$start = $now;
if ($period == 'min_ago_5')
	$start =$min_ago_5;
else if ($period == 'min_ago_15')
	$start = $min_ago_15;
else if ($period == "hour_ago_1")
	$start = $hour_ago_1;
else if ($period == "hour_ago_4")
	$start = $hour_ago_4;

	$result = mysql_query(throughput_upordown(20, $order, $start, $now))  or trigger_error(mysql_error()); 

	echo "<tbody>";
while($row = mysql_fetch_array($result)){ 
	foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
?>
	<tr>
	<td><?=$row["name"]?> (<?=$row["groupname"]?> <?=$row["hostname"]?>)<a href=/mars/userinfo/edit.php?username=<?=$row["username"]?>> <?=$row["username"]?></a></td>
<? if ($period == "min_ago_5") { ?>
	<td><?=nl2br( $row["input_rate"])?> (<?=nl2br( $row["input"])?>)</td>
	<td><?=nl2br( $row["output_rate"])?> (<?=nl2br( $row["output"])?>)</td>
<? } else {
	$result2 = mysql_query(throughput_device_upordown($row["username"], $min_ago_5, $now))  or trigger_error(mysql_error()); 
	$result3 = mysql_fetch_array($result2); ?>
	<td><?=nl2br( $result3["input_rate"])?> (<?=nl2br( $result3["input"])?>)</td>
	<td><?=nl2br( $result3["output_rate"])?> (<?=nl2br( $result3["output"])?>)</td>
<? } ?>
<? if ($period == "min_ago_15") { ?>
	<td><?=nl2br( $row["input_rate"])?> (<?=nl2br( $row["input"])?>)</td>
	<td><?=nl2br( $row["output_rate"])?> (<?=nl2br( $row["output"])?>)</td>
<? } else {
	$result2 = mysql_query(throughput_device_upordown($row["username"], $min_ago_15, $now))  or trigger_error(mysql_error()); 
	$result3 = mysql_fetch_array($result2); ?>
	<td><?=nl2br( $result3["input_rate"])?> (<?=nl2br( $result3["input"])?>)</td>
	<td><?=nl2br( $result3["output_rate"])?> (<?=nl2br( $result3["output"])?>)</td>
<? } ?>
<? if ($period == "hour_ago_1") { ?>
	<td><?=nl2br( $row["input_rate"])?> (<?=nl2br( $row["input"])?>)</td>
	<td><?=nl2br( $row["output_rate"])?> (<?=nl2br( $row["output"])?>)</td>
<? } else {
	$result2 = mysql_query(throughput_device_upordown($row["username"], $hour_ago_1, $now))  or trigger_error(mysql_error()); 
	$result3 = mysql_fetch_array($result2); ?>
	<td><?=nl2br( $result3["input_rate"])?> (<?=nl2br( $result3["input"])?>)</td>
	<td><?=nl2br( $result3["output_rate"])?> (<?=nl2br( $result3["output"])?>)</td>
<? } ?>
<? if ($period == "hour_ago_4") { ?>
	<td><?=nl2br( $row["input_rate"])?> (<?=nl2br( $row["input"])?>)</td>
	<td><?=nl2br( $row["output_rate"])?> (<?=nl2br( $row["output"])?>)</td>
<? } else {
	$result2 = mysql_query(throughput_device_upordown($row["username"], $hour_ago_4, $now))  or trigger_error(mysql_error()); 
	$result3 = mysql_fetch_array($result2); ?>
	<td><?=nl2br( $result3["input_rate"])?> (<?=nl2br( $result3["input"])?>)</td>
	<td><?=nl2br( $result3["output_rate"])?> (<?=nl2br( $result3["output"])?>)</td>
<? } ?>
	</tr>
	<? } ?>
</tbody></table>

<br/>

<p>(Transfer volume (MB) updated every 5 minutes; devices only included if active/online before start of the relevant period)</p>

<br/>

</div>
</body>
