<?php
  // up or download work
  function top_upordown_work($startday, $endday, $topX, $upordown) {
	return "
	SELECT 
		daily_accounting.username, 
		radusergroup.groupname as groupname, 
		userinfo.lastname as name, 
		userinfo.email as email, 
		userinfo.company as company, 
		userinfo.address as address, 
		userinfo.city as city, 
		ROUND((SUM(inputoctets_work_end) - SUM(inputoctets_work_beg)) / 1000000) as Upload, 
		ROUND((SUM(outputoctets_work_end) - SUM(outputoctets_work_beg)) / 1000000) as Download 
	FROM daily_accounting 
		LEFT JOIN radusergroup ON daily_accounting.username=radusergroup.username 
		LEFT JOIN userinfo ON daily_accounting.username=userinfo.username 
	WHERE daily_accounting.day >= \"" . $startday . "\" AND daily_accounting.day <= \"" . $endday . "\" 
	GROUP BY daily_accounting.username ORDER BY " . $upordown . " DESC LIMIT " . $topX . ";";  
  }
  
  // total up or download work
  function total_upordown_work($startday, $endday, $upordown) {
	  return "SELECT 
		  ROUND((SUM(inputoctets_work_end) - SUM(inputoctets_work_beg)) / 1000000) as Upload,
		  ROUND((SUM(outputoctets_work_end) - SUM(outputoctets_work_beg)) / 1000000) as Download
	   FROM daily_accounting WHERE day >= \"" . $startday . "\" AND day <= \"" . $endday . "\";";  
  }

	function generateworktraffic($upordown, $today, $yesterday, $daysago7, $daysago30) {
		echo "<hr/><p>Top " . $upordown . "s during working hours (Mo-Fr 8:00 to 16:30)</p>";
		
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