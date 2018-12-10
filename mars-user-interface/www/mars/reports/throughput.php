<head>
    <link rel="stylesheet" type="text/css" href="/mars/libs/chartist.min.css">
    <link rel="stylesheet" type="text/css" href="/mars/libs/chartist-plugin-tooltip.css">
</head>

<style>
.ct-legend {
     position: relative;
     z-index: 10;
     list-style: none;
     text-align: center;
 }
 .ct-legend li {
     position: relative;
     padding-left: 23px;
     margin-right: 10px;
     margin-bottom: 3px;
     cursor: pointer;
     display: inline-block;
 }
 .ct-legend li:before {
     width: 12px;
     height: 12px;
     position: absolute;
     left: 0;
     content: '';
     border: 3px solid transparent;
     border-radius: 2px;
 }
 .ct-legend li.inactive:before {
     background: transparent;
 }
 .ct-legend.ct-legend-inside {
     position: absolute;
     top: 0;
     right: 0;
 }
 .ct-legend.ct-legend-inside li{
     display: block;
     margin: 0;
 }
 
.ct-legend .ct-series-0:before {
     background-color: #d70206;
     border-color: #d70206;
 }
 .ct-legend .ct-series-1:before {
     background-color: #f05b4f;
     border-color: #f05b4f;
 }
 .ct-legend .ct-series-2:before {
     background-color: #f4c63d;
     border-color: #f4c63d;
 }
 .ct-legend .ct-series-3:before {
     background-color: #d17905;
     border-color: #d17905;
 }
 .ct-legend .ct-series-4:before {
     background-color: #453d3f;
     border-color: #453d3f;
 }

 .ct-legend .ct-series-5:before {
     background-color: #59922b;
     border-color: #59922b;
 }
 .ct-legend .ct-series-6:before {
     background-color: #0544d3;
     border-color: #0544d3;
 }
 .ct-legend .ct-series-7:before {
     background-color: #6b0392;
     border-color: #6b0392;
 }
 .ct-legend .ct-series-8:before {
     background-color: #f05b4f;
     border-color: #f05b4f;
 }
 .ct-legend .ct-series-9:before {
     background-color: #dda458;
     border-color: #dda458;
 }
 .ct-legend .ct-series-10:before {
     background-color: #eacf7d;
     border-color: #eacf7d;
 }
 .ct-legend .ct-series-11:before {
     background-color: #86797d;
     border-color: #86797d;
 }
 .ct-legend .ct-series-12:before {
     background-color: #b2c326;
     border-color: #b2c326;
 }
 .ct-legend .ct-series-13:before {
     background-color: #6188e2;
     border-color: #6188e2;
 }
 .ct-legend .ct-series-14:before {
     background-color: #a748ca;
     border-color: #a748ca;
 }

/* .ct-chart-line-multipleseries .ct-legend .ct-series-0:before {
    background-color: #d70206;
    border-color: #d70206;
 }

 .ct-chart-line-multipleseries .ct-legend .ct-series-1:before {
    background-color: #f4c63d;
    border-color: #f4c63d;
 }

 .ct-chart-line-multipleseries .ct-legend li.inactive:before {
    background: transparent;
  }
*/
 
 
 
 #mychartcontainer {
   display: flex;
 }
 #legend-div-left {
   width: 330px;
/*   background: lightblue;*/
 }
 #mychart {
   flex: 1;
   /* Grow to rest of container */
/*   background: lightgreen;*/
}
 
</style>
<? 
$HEADLINE = 'Throughput'; 
include '../common.php'; 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">
      <div class="page-header">
  	    <h1>Throughput at <?= date('Y-m-d H:i:s'); ?></h1>
	  </div>

<? 
$order = $_GET['order']; 
$period = $_GET['period']; 

$now = date('H:i:s');
$min_ago_5 = date('H:i:s', strtotime('-6 minutes'));
$min_ago_15 = date('H:i:s', strtotime('-15 minutes'));
$hour_ago_1 = date('H:i:s', strtotime('-1 hour'));
$hour_ago_4 = date('H:i:s', strtotime('-4 hours'));
?>

	<div id="mychartcontainer">
          <div id="legend-div-left"></div>
		<div id="mychart">
		    <div class="chart-container">
	          <div class="ct-chart" id="chart"></div>
		    </div>
		</div>
	</div>

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
	$a = "
		select distinct(t.username) as username, 
			ui.firstname as firstname,
			ui.lastname as lastname, 
			ui.hostname as hostname,
			g.groupname as groupname,
			ui.mac_vendor, 
			ROUND((max(offset_input) - min(offset_input)) / 1000000) as input, 
			ROUND((max(offset_output) - min(offset_output)) / 1000000) as output, 
			ROUND(((max(offset_input) - min(offset_input)) / TIME_TO_SEC(timediff('$end', '$start'))) * 1) as input_rate, 
			ROUND(((max(offset_output) - min(offset_output)) / TIME_TO_SEC(timediff('$end', '$start'))) * 1) as output_rate, 
			max(time_of_day) as max, 
			min(time_of_day) as min 
		from throughput t, userinfo ui, radusergroup g
		where t.username = ui.username and g.username = t.username and 
			time_of_day >= '" . $start . "' and time_of_day <= '" . $end . "' and day = curdate()
		group by t.username order by " . $order . " desc
		LIMIT " . $topX;
		// echo $a;
		return $a;
}

function throughput_device_upordown($device, $start, $end) {
	return "
		select 
			ROUND((max(offset_input) - min(offset_input)) / 1000000) as input, 
			ROUND((max(offset_output) - min(offset_output)) / 1000000) as output, 
			ROUND(ROUND((max(offset_input) - min(offset_input)) / TIME_TO_SEC(timediff('$end', '$start'))) * 1) as input_rate, 
			ROUND(ROUND((max(offset_output) - min(offset_output)) / TIME_TO_SEC(timediff('$end', '$start'))) * 1) as output_rate
		from throughput t
		where t.username = '" . $device . "' and 
			time_of_day >= '" . $start . "' and time_of_day <= '" . $end . "' and day = curdate()";
}

function throughput_total_upordown($start, $end) {
	$aa = "
	select sum(tt.input) as input, sum(tt.input_rate) as input_rate, sum(tt.output) as output, sum(tt.output_rate) as output_rate from (
		select 
			ROUND((max(offset_input) - min(offset_input)) / 1000000) as input, 
			ROUND((max(offset_output) - min(offset_output)) / 1000000) as output, 
			ROUND(ROUND((max(offset_input) - min(offset_input)) / TIME_TO_SEC(timediff('$end', '$start'))) * 1) as input_rate, 
			ROUND(ROUND((max(offset_output) - min(offset_output)) / TIME_TO_SEC(timediff('$end', '$start'))) * 1) as output_rate
		from throughput t
		where time_of_day >= '" . $start . "' and time_of_day <= '" . $end . "' and day=curdate() GROUP BY username
		) as tt";
		//echo $aa;
		return $aa;
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

	$rates = [];
	$rates["total"] = [];

	$result = mysql_query(throughput_upordown(14, $order, $start, $now))  or trigger_error(mysql_error()); 

	echo "<tbody><tr><td>Total</td>";
	if ($row = mysql_fetch_assoc(mysql_query(throughput_total_upordown($min_ago_5, $now)))) {
		echo "<td>" . $row["input_rate"] . " (" . $row["input"] . ")</td>";
		echo "<td>" . $row["output_rate"] . " (" . $row["output"] . ")</td>";
		$rates["total"]["min_ago_5"] = $row["output_rate"];
	}	
	if ($row = mysql_fetch_assoc(mysql_query(throughput_total_upordown($min_ago_15, $now)))) {
		echo "<td>" . $row["input_rate"] . " (" . $row["input"] . ")</td>";
		echo "<td>" . $row["output_rate"] . " (" . $row["output"] . ")</td>";
		$rates["total"]["min_ago_15"] = $row["output_rate"];
	}	
	if ($row = mysql_fetch_assoc(mysql_query(throughput_total_upordown($hour_ago_1, $now)))) {
		echo "<td>" . $row["input_rate"] . " (" . $row["input"] . ")</td>";
		echo "<td>" . $row["output_rate"] . " (" . $row["output"] . ")</td>";
		$rates["total"]["hour_ago_1"] = $row["output_rate"];
	}	
	if ($row = mysql_fetch_assoc(mysql_query(throughput_total_upordown($hour_ago_4, $now)))) {
		echo "<td>" . $row["input_rate"] . " (" . $row["input"] . ")</td>";
		echo "<td>" . $row["output_rate"] . " (" . $row["output"] . ")</td>";
		$rates["total"]["hour_ago_4"] = $row["output_rate"];
	}	
	echo "</tr>";


while($row = mysql_fetch_array($result)){ 
	foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
	
	if ($rates[$row["username"]] == null) {
		$rates[$row["username"]] = [];
	}
?>
	<tr>
	<td><?=dropdown_link_to_device($row["username"])?></td>
<? if ($period == "min_ago_5") { ?>
	<td><?=nl2br( $row["input_rate"])?> (<?=nl2br( $row["input"])?>)</td>
	<td><?=nl2br( $row["output_rate"])?> (<?=nl2br( $row["output"])?>)</td>
	<? $rates[$row["username"]]["min_ago_5"] = $row["output_rate"]; ?>
<? } else {
	$result2 = mysql_query(throughput_device_upordown($row["username"], $min_ago_5, $now))  or trigger_error(mysql_error()); 
	$result3 = mysql_fetch_array($result2); ?>
	<td><?=nl2br( $result3["input_rate"])?> (<?=nl2br( $result3["input"])?>)</td>
	<td><?=nl2br( $result3["output_rate"])?> (<?=nl2br( $result3["output"])?>)</td>
	<? $rates[$row["username"]]["min_ago_5"] = $result3["output_rate"]; ?>
<? } ?>
<? if ($period == "min_ago_15") { ?>
	<td><?=nl2br( $row["input_rate"])?> (<?=nl2br( $row["input"])?>)</td>
	<td><?=nl2br( $row["output_rate"])?> (<?=nl2br( $row["output"])?>)</td>
	<? $rates[$row["username"]]["min_ago_15"] = $row["output_rate"]; ?>
<? } else {
	$result2 = mysql_query(throughput_device_upordown($row["username"], $min_ago_15, $now))  or trigger_error(mysql_error()); 
	$result3 = mysql_fetch_array($result2); ?>
	<td><?=nl2br( $result3["input_rate"])?> (<?=nl2br( $result3["input"])?>)</td>
	<td><?=nl2br( $result3["output_rate"])?> (<?=nl2br( $result3["output"])?>)</td>
	<? $rates[$row["username"]]["min_ago_15"] = $result3["output_rate"]; ?>
<? } ?>
<? if ($period == "hour_ago_1") { ?>
	<td><?=nl2br( $row["input_rate"])?> (<?=nl2br( $row["input"])?>)</td>
	<td><?=nl2br( $row["output_rate"])?> (<?=nl2br( $row["output"])?>)</td>
	<? $rates[$row["username"]]["hour_ago_1"] = $row["output_rate"]; ?>
<? } else {
	$result2 = mysql_query(throughput_device_upordown($row["username"], $hour_ago_1, $now))  or trigger_error(mysql_error()); 
	$result3 = mysql_fetch_array($result2); ?>
	<td><?=nl2br( $result3["input_rate"])?> (<?=nl2br( $result3["input"])?>)</td>
	<td><?=nl2br( $result3["output_rate"])?> (<?=nl2br( $result3["output"])?>)</td>
	<? $rates[$row["username"]]["hour_ago_1"] = $result3["output_rate"]; ?>
<? } ?>
<? if ($period == "hour_ago_4") { ?>
	<td><?=nl2br( $row["input_rate"])?> (<?=nl2br( $row["input"])?>)</td>
	<td><?=nl2br( $row["output_rate"])?> (<?=nl2br( $row["output"])?>)</td>
	<? $rates[$row["username"]]["hour_ago_4"] = $row["output_rate"]; ?>
<? } else {
	$result2 = mysql_query(throughput_device_upordown($row["username"], $hour_ago_4, $now))  or trigger_error(mysql_error()); 
	$result3 = mysql_fetch_array($result2); ?>
	<td><?=nl2br( $result3["input_rate"])?> (<?=nl2br( $result3["input"])?>)</td>
	<td><?=nl2br( $result3["output_rate"])?> (<?=nl2br( $result3["output"])?>)</td>
	<? $rates[$row["username"]]["hour_ago_4"] = $result3["output_rate"]; ?>
<? } ?>
	</tr>
	<? } ?>
</tbody></table>

<br/>

<p>Throughput in bits/sec (and total size in MB). Data updated every minute.</p>

<br/>

</div>

<script type="text/javascript" src="/mars/libs/chartist.js"></script>
<script type="text/javascript" src="/mars/libs/chartist-plugin-legend.js"></script>
<script type="text/javascript" src="/mars/libs/chartist-plugin-tooltip.min.js"></script>
				
    <script>
		var legendDivLeft = document.getElementById('legend-div-left');
		
		new Chartist.Line('.ct-chart', {
		  labels: ['Last 5 mins', 'Last 15 mins', 'Last hour', 'Last 4 hours'],
		  series: [
			  <?
			  foreach($rates as $item => $value) {
//			      echo "[ " . $item['min_ago_5'] . ", " . $item['min_ago_15'] . ", " . $item['hour_ago_1'] . ", " . $item['hour_ago_4'] . " ],";
				echo "[ ";
				echo "{ meta: '" . name_to_device($item) . "', value: " . $value['min_ago_5'] . "}, ";
				echo "{ meta: '" . name_to_device($item) . "', value: " . $value['min_ago_15'] . "}, ";
				echo "{ meta: '" . name_to_device($item) . "', value: " . $value['hour_ago_1'] . "}, ";
				echo "{ meta: '" . name_to_device($item) . "', value: " . $value['hour_ago_4'] . "} ";
				echo " ],";
			  }
			  
			  
			  ?>
		  ]
		}, {
//  width: '800px',
  height: '250px',
			high: 1250000,
			low: 0,
		  fullWidth: true,
		  chartPadding: {
		    right: 40
		  },
	      plugins: [
			  Chartist.plugins.tooltip(),
	          Chartist.plugins.legend({
				  position: legendDivLeft,
	              legendNames: [
		<? 
			foreach($rates as $item => $value) {
				echo '"' . name_to_device($item) . '", ';
			}
		?>
					  ],
	          })
	      ]

		});

        </script>

</body>
