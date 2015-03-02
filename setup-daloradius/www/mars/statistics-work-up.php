<?php
echo "<hr/><p>Top uploads during working hours (Mo-Fr 7:00 to 18:00)</p>";
	
  // upload work
  function top_upload_work($startday, $endday, $topX) {
return 'SELECT daily_accounting.username, radusergroup.groupname as groupname, userinfo.lastname as name, userinfo.email as email, userinfo.company as company, userinfo.address as address, userinfo.city as city, ROUND((SUM(inputoctets_work_end) - SUM(inputoctets_work_beg)) / 1000000) as upload, ROUND((SUM(outputoctets_work_end) - SUM(outputoctets_work_beg)) / 1000000) as download FROM daily_accounting LEFT JOIN radusergroup ON daily_accounting.username=radusergroup.username LEFT JOIN userinfo ON daily_accounting.username=userinfo.username WHERE daily_accounting.day >= "' . $startday . '" AND daily_accounting.day <= "' . $endday . '" GROUP BY daily_accounting.username ORDER BY upload DESC LIMIT ' . $topX . ';';  
  }
$up_work_today = query(top_upload_work($today, $today, 10));
$up_work_yesterday = query(top_upload_work($yesterday, $yesterday, 10));
$up_work_last7days = query(top_upload_work($daysago7, $today, 10));
$up_work_last30days = query(top_upload_work($daysago30, $today, 10));

// upload work
  function total_upload_work($startday, $endday) {
return 'SELECT ROUND((SUM(inputoctets_work_end) - SUM(inputoctets_work_beg)) / 1000000) as upload FROM daily_accounting WHERE day >= "' . $startday . '" AND day <= "' . $endday . '";';  
  }
$up_work_total_today = query(total_upload_work($today, $today));
$up_work_total_yesterday = query(total_upload_work($yesterday, $yesterday));
$up_work_total_last7days = query(total_upload_work($daysago7, $today));
$up_work_total_last30days = query(total_upload_work($daysago30, $today));
echo "<table><tr><th>Upload (MB)</th><th>Today</th><th>Yesterday</th><th>Last 7 days</th><th>Last 30 days</th></tr>";
echo '<tr><td>Total</td><td>';
if ($row = mysql_fetch_assoc($up_work_total_today)) {
	echo $row['upload'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($up_work_total_yesterday)) {
	echo $row['upload'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($up_work_total_last7days)) {
	echo $row['upload'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($up_work_total_last30days)) {
	echo $row['upload'];
}	
echo '</td></tr>';
mysql_free_result($up_work_total_today);
mysql_free_result($up_work_total_yesterday);
mysql_free_result($up_work_total_last7days);
mysql_free_result($up_work_total_last30days);

for ($i=1; $i<=10; $i++) {
	echo "<tr>";
	echo "<td>Top #" . $i . "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($up_work_today)) {
	    echo uservolumelink($row['username'], $row['upload']) . " (" . userdetailslink($row['username'], $row['name']). " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($up_work_yesterday)) {
	    echo uservolumelink($row['username'], $row['upload']) . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($up_work_last7days)) {
	    echo uservolumelink($row['username'], $row['upload']) . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($up_work_last30days)) {
	    echo uservolumelink($row['username'], $row['upload']) . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "</tr>";
}
echo "</table>";
mysql_free_result($up_work_today);
mysql_free_result($up_work_yesterday);
mysql_free_result($up_work_last7days);
mysql_free_result($up_work_last30days);
?>