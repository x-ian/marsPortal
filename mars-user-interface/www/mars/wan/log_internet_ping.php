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
$hour_ago_8 = date('Y-m-d H:i:s', strtotime('-8 hours'));
$hour_ago_12 = date('Y-m-d H:i:s', strtotime('-12 hours'));
$day_ago_1 = date('Y-m-d H:i:s', strtotime('-1 day'));
$day_ago_7 = date('Y-m-d H:i:s', strtotime('-7 days'));
?>

<table class='table table-striped'>
	<thead><tr>
		<td>Period</td><td>Internet Availability</td><td>Packets sent/received</td>
	</tr></thead>
	<tbody><tr>
		<th>Last 5 minutes</th>
		<td>
			<? $result = mysql_query(internet_ping(4, "interval 5 minute")) or trigger_error(mysql_error())?>
			<?=get_percent(mysql_fetch_array($result)[3]) ?> %
		</td>
		<td><?=get_percent(mysql_fetch_array($result)[1]) ?> / <?=get_percent(mysql_fetch_array($result)[2]) ?></td>
	</tr>
	<tr>
		<th>Last 15 minutes</th>
		<td>
			<? $result = mysql_query(internet_ping(14, "interval 15 minute")) or trigger_error(mysql_error())?>
			<?=get_percent(mysql_fetch_array($result)[3]) ?> %
		</td>
		<td><?=get_percent(mysql_fetch_array($result)[1]) ?> / <?=get_percent(mysql_fetch_array($result)[2]) ?></td>
	</tr>
	<tr>
		<th>Last hour</th>
		<td>
			<? $result = mysql_query(internet_ping(59, "interval 1 hour")) or trigger_error(mysql_error())?>
			<?=get_percent(mysql_fetch_array($result)[3]) ?> %
		</td>
		<td><?=get_percent(mysql_fetch_array($result)[1]) ?> / <?=get_percent(mysql_fetch_array($result)[2]) ?></td>
	</tr>
	<tr>
		<th>Last 4 hours</th>
		<td>
			<? $result = mysql_query(internet_ping(239, "interval 4 hour")) or trigger_error(mysql_error())?>
			<?=get_percent(mysql_fetch_array($result)[3]) ?> %
		</td>
		<td><?=get_percent(mysql_fetch_array($result)[1]) ?> / <?=get_percent(mysql_fetch_array($result)[2]) ?></td>
	</tr>
	<tr>
		<th>Last 8 hours</th>
		<td>
			<? $result = mysql_query(internet_ping(479, "interval 8 hour")) or trigger_error(mysql_error())?>
			<?=get_percent(mysql_fetch_array($result)[3]) ?> %
		</td>
		<td><?=get_percent(mysql_fetch_array($result)[1]) ?> / <?=get_percent(mysql_fetch_array($result)[2]) ?></td>
	</tr>
	<tr>
		<th>Last 12 hours</th>
		<td>
			<? $result = mysql_query(internet_ping(719, "interval 12 hour")) or trigger_error(mysql_error())?>
			<?=get_percent(mysql_fetch_array($result)[3]) ?> %
		</td>
		<td><?=get_percent(mysql_fetch_array($result)[1]) ?> / <?=get_percent(mysql_fetch_array($result)[2]) ?></td>
	</tr>
	<tr>
		<th>Last day</th>
		<td>
			<? $result = mysql_query(internet_ping(1439, "interval 24 hour")) or trigger_error(mysql_error())?>
			<?=get_percent(mysql_fetch_array($result)[3]) ?> %
		</td>
		<td><?=get_percent(mysql_fetch_array($result)[1]) ?> / <?=get_percent(mysql_fetch_array($result)[2]) ?></td>
	</tr>
	<tr>
		<th>Last 7 days</th>
		<td>
			<? $result = mysql_query(internet_ping(10079, "interval 7 day")) or trigger_error(mysql_error())?>
			<?=get_percent(mysql_fetch_array($result)[3]) ?> %
		</td>
		<td><?=get_percent(mysql_fetch_array($result)[1]) ?> / <?=get_percent(mysql_fetch_array($result)[2]) ?></td>
	</tr></tbody>

<?
function get_percent($number) {
	if ($number > 1)
		$number = 1;
	return number_format((float)($number / 1 * 100), 2, '.', '');
}

function internet_ping($expectedPings, $interval) {
	return "
		select count(*), 
			sum(transmitted), 
			sum(received), 
			(((count(*)) / " . $expectedPings . ") * (sum(received) / sum(transmitted))) 
		from log_internet_ping 
		where begin >= date_sub(now(), " . $interval . ");";
}
?>
</table>

<br/>

<p>Availability measured (via ping) every minute for a period of 1 minute. marsPortal downtime (switched off) is counted as not available.</p>

<br/>

</div>
</body>
