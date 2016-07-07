<?php

  function top_upordown($startday, $endday, $topX, $upordown) {
	return "
		SELECT 
			GROUP_CONCAT('', daily_accounting_v2.username) as usernames, 
			radusergroup.groupname as groupname, 
			CONCAT_WS(' ', userinfo.firstname, userinfo.lastname) as name, 
			userinfo.department as department, 
			userinfo.email as email, 
			userinfo.organisation as company, 
			userinfo.hostname as address, 
			userinfo.mac_vendor as city, 
			ROUND((SUM(day_total_input) - SUM(day_offset_input)) / 1000000) as Upload, 
			ROUND((SUM(day_total_output) - SUM(day_offset_output)) / 1000000) as Download 
		FROM daily_accounting_v2
			LEFT JOIN radusergroup ON daily_accounting_v2.username=radusergroup.username 
			LEFT JOIN userinfo ON daily_accounting_v2.username=userinfo.username 
		WHERE daily_accounting_v2.day >= \"" . $startday . "\" AND daily_accounting_v2.day <= \"" . $endday . "\" 
		GROUP BY CONCAT_WS(' ', userinfo.firstname, userinfo.lastname) ORDER BY " . $upordown . " DESC LIMIT " . $topX . ";";  
  }
  
  function total_upordown($startday, $endday, $upordown) {
	  return "SELECT 
		  ROUND((SUM(day_total_input) - SUM(day_offset_input)) / 1000000) as Upload,
		  ROUND((SUM(day_total_output) - SUM(day_offset_output)) / 1000000) as Download
	   FROM daily_accounting_v2 WHERE day >= \"" . $startday . "\" AND day <= \"" . $endday . "\";";  
  }

	function generatedailytraffic($upordown, $today, $yesterday, $daysago7, $daysago30) {
		echo "<hr/><p>Top " . $upordown . "s daily</p>";
		
		$upordown_today = query(top_upordown($today, $today, 10, $upordown));
		$upordown_yesterday = query(top_upordown($yesterday, $yesterday, 10, $upordown));
		$upordown_last7days = query(top_upordown($daysago7, $today, 10, $upordown));
		$upordown_last30days = query(top_upordown($daysago30, $today, 10, $upordown));

		$upordown_total_today = query(total_upordown($today, $today, $upordown));
		$upordown_total_yesterday = query(total_upordown($yesterday, $yesterday, $upordown));
		$upordown_total_last7days = query(total_upordown($daysago7, $today, $upordown));
		$upordown_total_last30days = query(total_upordown($daysago30, $today, $upordown));


		echo "<table class='listtable'>
			<tr>
				<th>" . $upordown . " (MB)</th>
				<th>Today</th>
				<th>Yesterday</th>
				<th>Last 7 days</th>
				<th>Last 30 days</th>
			</tr>
			<tr>
				<td>Total</td>
				<td>";
				if ($row = mysql_fetch_assoc($upordown_total_today)) {
					echo $row[$upordown];
				}	
				echo '</td>';
				echo '<td>';
				if ($row = mysql_fetch_assoc($upordown_total_yesterday)) {
					echo $row[$upordown];
				}	
				echo '</td>';
				echo '<td>';
				if ($row = mysql_fetch_assoc($upordown_total_last7days)) {
					echo $row[$upordown];
				}	
				echo '</td>';
				echo '<td>';
				if ($row = mysql_fetch_assoc($upordown_total_last30days)) {
					echo $row[$upordown];
				}	
				echo '</td></tr>';

		for ($i=1; $i<=10; $i++) {
			echo "<tr>";
			echo "<td>Top #" . $i . "</td>";
			echo "<td>";
			if ($row = mysql_fetch_assoc($upordown_today)) {
				userinfo($row, $upordown);
			}
			echo "</td>";
			echo "<td>";
			if ($row = mysql_fetch_assoc($upordown_yesterday)) {
				userinfo($row, $upordown);
			}
			echo "</td>";
			echo "<td>";
			if ($row = mysql_fetch_assoc($upordown_last7days)) {
				userinfo($row, $upordown);
			}
			echo "</td>";
			echo "<td>";
			if ($row = mysql_fetch_assoc($upordown_last30days)) {
				userinfo($row, $upordown);
			}
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
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