<?php

  function top_upordown($startday, $endday, $topX, $upordown) {
	return "
		SELECT 
	distinct(radacct.UserName) as username, 
	radusergroup.groupname as groupname, 
	CONCAT_WS(' ', userinfo.firstname, userinfo.lastname) as name, 
	userinfo.department as department, 
	userinfo.email as email, 
	userinfo.company as company, 
	userinfo.address as address, 
	userinfo.city as city,
	ROUND((sum(radacct.AcctOutputOctets)/1000000)) as Download,
	ROUND((sum(radacct.AcctInputOctets)/1000000)) as Upload
	FROM radacct     
	LEFT JOIN radusergroup ON radacct.username=radusergroup.username 
	LEFT JOIN userinfo ON radacct.username=userinfo.username    
	WHERE (AcctStopTime > \"0000-00-00 00:00:01\" AND AcctStartTime>\"" . $startday . "\" AND AcctStartTime<date(date_add(\"" . $endday . "\", INTERVAL +1 DAY))) 
	OR 
	((radacct.AcctStopTime IS NULL OR radacct.AcctStopTime = \"0000-00-00 00:00:00\") AND AcctStartTime<date(date_add(\"" . $endday . "\", INTERVAL +1 DAY))) 
	group by UserName order by " . $upordown . " desc limit " . $topX . ";";  
  }
  
  function total_upordown($startday, $endday, $upordown) {
	  return "
	  	SELECT ROUND((sum(radacct.AcctOutputOctets)/1000000)) as Download, 
	  	 ROUND((sum(radacct.AcctInputOctets)/1000000)) as Upload 
	  	 FROM radacct 
	  WHERE (AcctStopTime > \"0000-00-00 00:00:01\" AND AcctStartTime>\"" . $startday . "\" AND AcctStartTime<date(date_add(\"" . $endday . "\", INTERVAL +1 DAY))) 
	  OR 
	  ((radacct.AcctStopTime IS NULL OR radacct.AcctStopTime = \"0000-00-00 00:00:00\") AND AcctStartTime<date(date_add(\"" . $endday . "\", INTERVAL +1 DAY))) ;";
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