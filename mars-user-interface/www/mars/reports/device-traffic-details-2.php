<?php

function top_upordown($startday, $endday, $topX, $upordown) {
$a = "
	select 
		day, 
		mac, 
		remote_ip, 
		reverse_dns, 
		sum(outgoing) as Upload, 
		sum(incoming) as Download
	from traffic_details 
	left join ip_registry on traffic_details.remote_ip = ip_registry.ip
	where mac = \"{$username}\" and day >= \" {$startday}\" and day <= \"{$startday}\"
	group by mac, remote_ip order by {$upordown} desc limit {$topX}";

	//echo $a;
	return $a;
}

function total_upordown($startday, $endday, $upordown) {
  return "	select 
		day, 
		mac, 
		remote_ip, 
		reverse_dns, 
		sum(outgoing) as Upload, 
		sum(incoming) as Download
	from traffic_details WHERE day >= \"" . $startday . "\" AND day <= \"" . $endday . "\";";  
}

function generatedailytraffic($upordown, $today, $yesterday, $daysago7, $daysago30) {
	//echo "<hr/><p>Top " . $upordown . "s daily</p>";
	
	$upordown_today = query(top_upordown($today, $today, 10, $upordown));
	$upordown_yesterday = query(top_upordown($yesterday, $yesterday, 10, $upordown));
	$upordown_last7days = query(top_upordown($daysago7, $today, 10, $upordown));
	$upordown_last30days = query(top_upordown($daysago30, $today, 10, $upordown));

	$upordown_total_today = query(total_upordown($today, $today, $upordown));
	$upordown_total_yesterday = query(total_upordown($yesterday, $yesterday, $upordown));
	$upordown_total_last7days = query(total_upordown($daysago7, $today, $upordown));
	$upordown_total_last30days = query(total_upordown($daysago30, $today, $upordown));
?>

<table class='table table-striped'>
	<thead><tr>
		<th><?=$upordown?> (MB)</th>
		<th>Today</th>
		<th>Yesterday</th>
		<th>Last 7 days</th>
		<th>Last 30 days</th>
	</tr></thead>
	<tbody><tr>

		<td>Total</td>
		<td>
			<? if ($row = mysql_fetch_assoc($upordown_total_today)) {
				echo $row[$upordown];
			}?>	
		</td>
		<td>
			<? if ($row = mysql_fetch_assoc($upordown_total_yesterday)) {
				echo $row[$upordown];
			}?>	
		</td>
		<td>
			<? if ($row = mysql_fetch_assoc($upordown_total_last7days)) {
				echo $row[$upordown];
			}?>	
		</td>
		<td>
			<? if ($row = mysql_fetch_assoc($upordown_total_last30days)) {
				echo $row[$upordown];
			}?>	
		</td>
	</tr>

<?
		for ($i=1; $i<=10; $i++) {
			echo "<tr>";
			echo "<td>Top #" . $i . "</td>";
			echo "<td>";
			if ($row = mysql_fetch_assoc($upordown_today)) {
				deviceinfo($row, $upordown);
			}
			echo "</td>";
			echo "<td>";
			if ($row = mysql_fetch_assoc($upordown_yesterday)) {
				deviceinfo($row, $upordown);
			}
			echo "</td>";
			echo "<td>";
			if ($row = mysql_fetch_assoc($upordown_last7days)) {
				deviceinfo($row, $upordown);
			}
			echo "</td>";
			echo "<td>";
			if ($row = mysql_fetch_assoc($upordown_last30days)) {
				deviceinfo($row, $upordown);
			}
			echo "</td>";
			echo "</tr>";
		}
		echo "</tbody></table>";
		mysql_free_result($upordown_total_today);
		mysql_free_result($upordown_total_yesterday);
		mysql_free_result($upordown_total_last7days);
		mysql_free_result($upordown_total_last30days);
		mysql_free_result($upordown_today);
		mysql_free_result($upordown_yesterday);
		mysql_free_result($upordown_last7days);
		mysql_free_result($upordown_last30days);
	}

?>

<table class='table table-striped'>
	<thead><tr>
		<th><?=$upordown?> (MB)</th>
		<th>Today</th>
		<th>Yesterday</th>
		<th>Last 7 days</th>
		<th>Last 30 days</th>
	</tr></thead>
	<tbody><tr>

		<td>Total</td>
		<td>
			<? if ($row = mysql_fetch_assoc($upordown_total_today_gerry_mtl)) {
				echo $row[$upordown];
			}?>	
		</td>
		<td>
			<? if ($row = mysql_fetch_assoc($upordown_total_yesterday_gerry_mtl)) {
				echo $row[$upordown];
			}?>	
		</td>
		<td>
			<? if ($row = mysql_fetch_assoc($upordown_total_last7days_gerry_mtl)) {
				echo $row[$upordown];
			}?>	
		</td>
		<td>
			<? if ($row = mysql_fetch_assoc($upordown_total_last30days_gerry_mtl)) {
				echo $row[$upordown];
			}?>	
		</td>
	</tbody></tr>
</table>