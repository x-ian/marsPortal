<?php
  // up or download work
  function top_upordown_work($startday, $endday, $topX, $upordown) {
	return "
	SELECT 
		daily_accounting_v2.username, 
		radusergroup.groupname as groupname, 
		CONCAT_WS(' ', userinfo.firstname, userinfo.lastname) as name, 
		userinfo.department as department, 
		userinfo.email as email, 
		userinfo.organisation as company, 
		userinfo.hostname as address, 
		userinfo.mac_vendor as city, 
		ROUND((SUM(work_total_input) - SUM(work_offset_input) - SUM(lunch_total_input) + SUM(lunch_offset_input)) / 1000000) as Upload, 
		ROUND((SUM(work_total_output) - SUM(work_offset_output) - SUM(lunch_total_output) + SUM(lunch_offset_output)) / 1000000) as Download 
	FROM daily_accounting_v2
		LEFT JOIN radusergroup ON daily_accounting_v2.username=radusergroup.username 
		LEFT JOIN userinfo ON daily_accounting_v2.username=userinfo.username 
	WHERE daily_accounting_v2.day >= \"" . $startday . "\" AND daily_accounting_v2.day <= \"" . $endday . "\" 
	GROUP BY daily_accounting_v2.username ORDER BY " . $upordown . " DESC LIMIT " . $topX . ";";  
  }
  
  // total up or download work
  function total_upordown_work($startday, $endday, $upordown) {
	  return "SELECT 
		  ROUND((SUM(work_total_input) - SUM(work_offset_input) - SUM(lunch_total_input) + SUM(lunch_offset_input)) / 1000000) as Upload,
		  ROUND((SUM(work_total_output) - SUM(work_offset_output) - SUM(lunch_total_output) + SUM(lunch_offset_output)) / 1000000) as Download
	   FROM daily_accounting_v2 WHERE day >= \"" . $startday . "\" AND day <= \"" . $endday . "\";";  
  }

	function generateworktraffic($upordown, $today, $yesterday, $daysago7, $daysago30) {
		echo "<hr/><p>Top " . $upordown . "s during working hours (Mo-Fr 8:00-12:00 and 13:30-17:00)</p>";
		
		$upordown_work_today = query(top_upordown_work($today, $today, 10, $upordown));
		$upordown_work_yesterday = query(top_upordown_work($yesterday, $yesterday, 10, $upordown));
		$upordown_work_last7days = query(top_upordown_work($daysago7, $today, 10, $upordown));
		$upordown_work_last30days = query(top_upordown_work($daysago30, $today, 10, $upordown));

		$upordown_work_total_today = query(total_upordown_work($today, $today, $upordown));
		$upordown_work_total_yesterday = query(total_upordown_work($yesterday, $yesterday, $upordown));
		$upordown_work_total_last7days = query(total_upordown_work($daysago7, $today, $upordown));
		$upordown_work_total_last30days = query(total_upordown_work($daysago30, $today, $upordown));


		echo "<table>
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
				if ($row = mysql_fetch_assoc($upordown_work_total_today)) {
					echo $row[$upordown];
				}	
				echo '</td>';
				echo '<td>';
				if ($row = mysql_fetch_assoc($upordown_work_total_yesterday)) {
					echo $row[$upordown];
				}	
				echo '</td>';
				echo '<td>';
				if ($row = mysql_fetch_assoc($upordown_work_total_last7days)) {
					echo $row[$upordown];
				}	
				echo '</td>';
				echo '<td>';
				if ($row = mysql_fetch_assoc($upordown_work_total_last30days)) {
					echo $row[$upordown];
				}	
				echo '</td></tr>';

		for ($i=1; $i<=10; $i++) {
			echo "<tr>";
			echo "<td>Top #" . $i . "</td>";
			echo "<td>";
			if ($row = mysql_fetch_assoc($upordown_work_today)) {
				deviceinfo($row, $upordown);
			}
			echo "</td>";
			echo "<td>";
			if ($row = mysql_fetch_assoc($upordown_work_yesterday)) {
				deviceinfo($row, $upordown);
			}
			echo "</td>";
			echo "<td>";
			if ($row = mysql_fetch_assoc($upordown_work_last7days)) {
				deviceinfo($row, $upordown);
			}
			echo "</td>";
			echo "<td>";
			if ($row = mysql_fetch_assoc($upordown_work_last30days)) {
				deviceinfo($row, $upordown);
			}
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
		mysql_free_result($upordown_work_total_today);
		mysql_free_result($upordown_work_total_yesterday);
		mysql_free_result($upordown_work_total_last7days);
		mysql_free_result($upordown_work_total_last30days);
		mysql_free_result($upordown_work_today);
		mysql_free_result($upordown_work_yesterday);
		mysql_free_result($upordown_work_last7days);
		mysql_free_result($upordown_work_last30days);
	}
?>