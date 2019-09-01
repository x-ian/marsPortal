<head>
    <link rel="stylesheet" type="text/css" href="/mars/libs/chartist.min.css">
</head>

<style>
.ct-series-b .ct-bar, .ct-series-b .ct-line, .ct-series-b .ct-point, .ct-series-b .ct-area, .ct-series-b .ct-slice-donut {
    stroke: lightgreen;
    fill: lightgreen;
}
.ct-chart .ct-area {
  fill-opacity: 1
}
</style>

<? 
$HEADLINE = 'Internet Ping'; 
include '../menu.php'; 
include '../common.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

    <div class="ct-chart" id="chart"></div>

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
$day_ago_30 = date('Y-m-d H:i:s', strtotime('-30 days'));

$rates = [];
?>

<table class='table table-striped'>
	<thead><tr>
		<td>Period</td><td>Internet Availability</td><td>Packets sent/received (Intervals actual/expected)</td>
	</tr></thead>
	<tbody><tr>
		<th>Last 5 minutes</th>
		<td>
			<? $result = mysql_query(internet_ping(4, "interval 5 minute")) or trigger_error(mysql_error())?>
			<? $row = mysql_fetch_array($result)?>
			<?=get_percent($row[3]) ?> %
			<? $rates["last_5_min"] = get_percent($row[3]); ?>
		</td>
		<td><?=$row[1] ?> / <?=$row[2] ?> (<?=$row[0]?> / 4)</td>
	</tr>
	<tr>
		<th>Last 15 minutes</th>
		<td>
			<? $result = mysql_query(internet_ping(14, "interval 15 minute")) or trigger_error(mysql_error())?>
			<? $row = mysql_fetch_array($result)?>
			<?=get_percent($row[3]) ?> %
			<? $rates["last_15_min"] = get_percent($row[3]); ?>
		</td>
		<td><?=$row[1] ?> / <?=$row[2] ?> (<?=$row[0]?> / 14)</td>
	</tr>
	<tr>
		<th>Last hour</th>
		<td>
			<? $result = mysql_query(internet_ping(59, "interval 1 hour")) or trigger_error(mysql_error())?>
			<? $row = mysql_fetch_array($result)?>
			<?=get_percent($row[3]) ?> %
			<? $rates["last_hour"] = get_percent($row[3]); ?>
		</td>
		<td><?=$row[1] ?> / <?=$row[2] ?> (<?=$row[0]?> / 59)</td>
	</tr>
	<tr>
		<th>Last 4 hours</th>
		<td>
			<? $result = mysql_query(internet_ping(239, "interval 4 hour")) or trigger_error(mysql_error())?>
			<? $row = mysql_fetch_array($result)?>
			<?=get_percent($row[3]) ?> %
			<? $rates["last_4_hour"] = get_percent($row[3]); ?>
		</td>
		<td><?=$row[1] ?> / <?=$row[2] ?> (<?=$row[0]?> / 239)</td>
	</tr>
	<tr>
		<th>Last 8 hours</th>
		<td>
			<? $result = mysql_query(internet_ping(479, "interval 8 hour")) or trigger_error(mysql_error())?>
			<? $row = mysql_fetch_array($result)?>
			<? $rates["last_8_hour"] = get_percent($row[3]); ?>
			<?=get_percent($row[3]) ?> %
		</td>
		<td><?=$row[1] ?> / <?=$row[2] ?> (<?=$row[0]?> / 479)</td>
	</tr>
	<tr>
		<th>Last 12 hours</th>
		<td>
			<? $result = mysql_query(internet_ping(719, "interval 12 hour")) or trigger_error(mysql_error())?>
			<? $row = mysql_fetch_array($result)?>
			<?=get_percent($row[3]) ?> %
			<? $rates["last_12_hour"] = get_percent($row[3]); ?>
		</td>
		<td><?=$row[1] ?> / <?=$row[2] ?> (<?=$row[0]?> / 719)</td>
	</tr>
	<tr>
		<th>Last day</th>
		<td>
			<? $result = mysql_query(internet_ping(1439, "interval 24 hour")) or trigger_error(mysql_error())?>
			<? $row = mysql_fetch_array($result)?>
			<?=get_percent($row[3]) ?> %
			<? $rates["last_day"] = get_percent($row[3]); ?>
		</td>
		<td><?=$row[1] ?> / <?=$row[2] ?> (<?=$row[0]?> / 1439)</td>
	</tr>
	<tr>
		<th>Last 7 days</th>
		<td>
			<? $result = mysql_query(internet_ping(10079, "interval 7 day")) or trigger_error(mysql_error())?>
			<? $row = mysql_fetch_array($result)?>
			<?=get_percent($row[3]) ?> %
			<? $rates["last_7_day"] = get_percent($row[3]); ?>
		</td>
		<td><?=$row[1] ?> / <?=$row[2] ?> (<?=$row[0]?> / 10079)</td>
	</tr>
	<tr>
		<th>Last 30 days</th>
		<td>
			<? $result = mysql_query(internet_ping(43199, "interval 30 day")) or trigger_error(mysql_error())?>
			<? $row = mysql_fetch_array($result)?>
			<?=get_percent($row[3]) ?> %
			<? $rates["last_30_day"] = get_percent($row[3]); ?>
		</td>
		<td><?=$row[1] ?> / <?=$row[2] ?> (<?=$row[0]?> / 43199)</td>
	</tr></tbody>

<?
function get_percent($number) {
	if ($number > 1)
		$number = 1;
	return number_format((float)($number / 1 * 100), 2, '.', '');
}

function internet_ping($expectedPings, $interval) {
	$a = "
		select count(*), 
			sum(transmitted), 
			sum(received), 
			(((count(*)) / " . $expectedPings . ") * (sum(received) / sum(transmitted))) 
		from log_internet_ping 
		where begin >= date_sub(now(), " . $interval . ");";
		echo "<!-- " . $a . "-->";
	return $a;
}
?>
</table>

<br/>

<p>Availability measured (via ping) every minute for a period of 1 minute. marsPortal downtime (switched off) is counted as not available.</p>

<br/>

</div>

<script type="text/javascript" src="/mars/libs/chartist.js"></script>
<script>
new Chartist.Bar('.ct-chart', {
	  labels: ['Last 5 mins', 'Last 15 mins', 'Last hour', 'Last 4 hours', 'Last 8 hours', 'Last 12 hours', 'Last day', 'Last 7 days', 'Last 30 days'],
	  series: [
		  [ <?=100-$rates['last_5_min'] ?>, <?=100-$rates['last_15_min'] ?>, <?=100-$rates['last_hour'] ?>, <?=100-$rates['last_4_hour'] ?>, <?=100-$rates['last_8_hour'] ?>, <?=100-$rates['last_12_hour'] ?>, <?=100-$rates['last_day'] ?>, <?=100-$rates['last_7_day'] ?>, <?=100-$rates['last_30_day'] ?> ],
		  //[ 100, 100, 100, 100, 100, 100, 100, 100, 100 ],
		   [ <?=$rates['last_5_min'] ?>, <?=$rates['last_15_min'] ?>, <?=$rates['last_hour'] ?>, <?=$rates['last_4_hour'] ?>, <?=$rates['last_8_hour'] ?>, <?=$rates['last_12_hour'] ?>, <?=$rates['last_day'] ?>, <?=$rates['last_7_day'] ?>, <?=$rates['last_30_day'] ?> ],
	 	  ]
	}, 
	{
	  stackBars: true,
	height: '100px',
	high: 100,
	low: 0,
			chartPadding: {
				right: 40
			},
	fullWidth: true,
	  axisY: {
	    labelInterpolationFnc: function(value) {
	      return (value / 1000) + 'k';
	    }
	  }
	}).on('draw', function(data) {
	  if(data.type === 'bar') {
	    data.element.attr({
	      style: 'stroke-width: 30px'
	    });
	  }
	});	
	
	// {
	// 	height: '100px',
	// 	high: 100,
	// 	low: 0,
	// 	fullWidth: true,
	// 	showPoint: false,
	// 	showArea: true,
	// 	chartPadding: {
	// 		right: 40
	// 	},
	// });
</script>

</body>
