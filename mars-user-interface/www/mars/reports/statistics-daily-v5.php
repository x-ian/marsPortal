<?php

  function top_upordown($startday, $endday, $topX, $upordown) {
	return "
		SELECT 
			daily_accounting_v5.username, 
			radusergroup.groupname as groupname, 
			CONCAT_WS(' ', userinfo.firstname, userinfo.lastname) as name, 
			userinfo.hostname as address, 
			userinfo.mac_vendor as city, 
			ROUND((0029_input+0059_input+0129_input+0159_input+0229_input+0259_input+0329_input+0359_input+0429_input+0459_input+0529_input+0559_input+0629_input+0659_input+0729_input+0759_input+0829_input+0859_input+0929_input+0959_input+1029_input+1059_input+1129_input+1159_input+1229_input+1259_input+1329_input+1359_input+1429_input+1459_input+1529_input+1559_input+1629_input+1659_input+1729_input+1759_input+1829_input+1859_input+1929_input+1959_input+2029_input+2059_input+2129_input+2159_input+2229_input+2259_input+2329_input+2359_input) / 1000000) as Upload,
			ROUND((0029_output+0059_output+0129_output+0159_output+0229_output+0259_output+0329_output+0359_output+0429_output+0459_output+0529_output+0559_output+0629_output+0659_output+0729_output+0759_output+0829_output+0859_output+0929_output+0959_output+1029_output+1059_output+1129_output+1159_output+1229_output+1259_output+1329_output+1359_output+1429_output+1459_output+1529_output+1559_output+1629_output+1659_output+1729_output+1759_output+1829_output+1859_output+1929_output+1959_output+2029_output+2059_output+2129_output+2159_output+2229_output+2259_output+2329_output+2359_output) / 1000000) as Download 
		FROM daily_accounting_v5
			LEFT JOIN radusergroup ON daily_accounting_v5.username=radusergroup.username 
			LEFT JOIN userinfo ON daily_accounting_v5.username=userinfo.username 
		WHERE daily_accounting_v5.day >= \"" . $startday . "\" AND daily_accounting_v5.day <= \"" . $endday . "\" 
		GROUP BY daily_accounting_v5.username ORDER BY " . $upordown . " DESC LIMIT " . $topX . ";";  
  }
  
  function total_upordown($startday, $endday, $upordown) {
	  return "SELECT 
		ROUND(SUM(0029_input+0059_input+0129_input+0159_input+0229_input+0259_input+0329_input+0359_input+0429_input+0459_input+0529_input+0559_input+0629_input+0659_input+0729_input+0759_input+0829_input+0859_input+0929_input+0959_input+1029_input+1059_input+1129_input+1159_input+1229_input+1259_input+1329_input+1359_input+1429_input+1459_input+1529_input+1559_input+1629_input+1659_input+1729_input+1759_input+1829_input+1859_input+1929_input+1959_input+2029_input+2059_input+2129_input+2159_input+2229_input+2259_input+2329_input+2359_input) / 1000000) as Upload,
		ROUND(SUM(0029_output+0059_output+0129_output+0159_output+0229_output+0259_output+0329_output+0359_output+0429_output+0459_output+0529_output+0559_output+0629_output+0659_output+0729_output+0759_output+0829_output+0859_output+0929_output+0959_output+1029_output+1059_output+1129_output+1159_output+1229_output+1259_output+1329_output+1359_output+1429_output+1459_output+1529_output+1559_output+1629_output+1659_output+1729_output+1759_output+1829_output+1859_output+1929_output+1959_output+2029_output+2059_output+2129_output+2159_output+2229_output+2259_output+2329_output+2359_output) / 1000000) as Download
	   FROM daily_accounting_v5 WHERE day >= \"" . $startday . "\" AND day <= \"" . $endday . "\";";  
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