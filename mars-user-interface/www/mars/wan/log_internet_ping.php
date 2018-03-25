<? 
$HEADLINE = 'Internet Ping'; 
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
		<th>Last 5 minutes</th>
		<td>
			<?=$result = mysql_fetch_array(mysql_query(internet_ping(5, "interval 5 minute")) or trigger_error(mysql_error()))[log_internet_ping] ?>
		</td>
	</tr>
	<tr>
		<th>Last 15 minutes</th>
		<td></td>
	</tr>
	<tr>
		<th>Last hour</th>
		<td></td>
	</tr>
	<tr>
		<th>Last 4 hours</th>
		<td></td>
	</tr></thead>

<?
function snapshottime($table) {
	$result = mysql_query("select datetime from $table limit 1") or trigger_error(mysql_error()); 
	if($row = mysql_fetch_array($result)) { 
		foreach($row AS $key => $value) { $row[$key] = stripslashes($value); }
	} 
	return $row[0];
}

function internet_ping($expectedPings, $interval) {
	return "
		select count(*), 
			sum(transmitted), 
			sum(received), 
			(((count(*)) / " . $expectedPings . ") * (sum(received) / sum(transmitted))) 
		from log_internet_ping 
		where begin >= date_sub(now(), '" . $interval . "');"
}

$start = $now;
if ($period == 'min_ago_5')
else if ($period == 'min_ago_15')
	$result = mysql_fetch_array(mysql_query(internet_ping(59, "interval 15 minute")) or trigger_error(mysql_error())); 
else if ($period == "hour_ago_1")
	$result = mysql_fetch_array(mysql_query(internet_ping(59, "interval 1 hour")) or trigger_error(mysql_error())); 
else if ($period == "hour_ago_4")
	$result = mysql_fetch_array(mysql_query(internet_ping(59, "interval 4 hour")) or trigger_error(mysql_error())); 


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

<p>Throughput in kbit/sec (plus total size in MB). Data updated every minute.</p>

<br/>

</div>
</body>
