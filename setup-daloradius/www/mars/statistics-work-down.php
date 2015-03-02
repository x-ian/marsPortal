<?php
  
echo "<hr/><p>Top downloads during working hours (Mo-Fr 7:00 to 18:00)</p>";
	
  // download work
  function top_download_work($startday, $endday, $topX) {
return 'SELECT daily_accounting.username, radusergroup.groupname as groupname, userinfo.lastname as name, userinfo.email as email, userinfo.company as company, userinfo.address as address, userinfo.city as city, ROUND((SUM(inputoctets_work_end) - SUM(inputoctets_work_beg)) / 1000000) as upload, ROUND((SUM(outputoctets_work_end) - SUM(outputoctets_work_beg)) / 1000000) as download FROM daily_accounting LEFT JOIN radusergroup ON daily_accounting.username=radusergroup.username LEFT JOIN userinfo ON daily_accounting.username=userinfo.username WHERE daily_accounting.day >= "' . $startday . '" AND daily_accounting.day <= "' . $endday . '" GROUP BY daily_accounting.username ORDER BY download DESC LIMIT ' . $topX . ';';  
  }
$down_work_today = query(top_download_work($today, $today, 10));
$down_work_yesterday = query(top_download_work($yesterday, $yesterday, 10));
$down_work_last7days = query(top_download_work($daysago7, $today, 10));
$down_work_last30days = query(top_download_work($daysago30, $today, 10));

// download work
  function total_download_work($startday, $endday) {
return 'SELECT ROUND((SUM(outputoctets_work_end) - SUM(outputoctets_work_beg)) / 1000000) as download FROM daily_accounting WHERE day >= "' . $startday . '" AND day <= "' . $endday . '";';  
  }
$down_work_total_today = query(total_download_work($today, $today));
$down_work_total_yesterday = query(total_download_work($yesterday, $yesterday));
$down_work_total_last7days = query(total_download_work($daysago7, $today));
$down_work_total_last30days = query(total_download_work($daysago30, $today));
echo "<table><tr><th>Download (MB)</th><th>Today</th><th>Yesterday</th><th>Last 7 days</th><th>Last 30 days</th></tr>";
echo '<tr><td>Total</td><td>';
if ($row = mysql_fetch_assoc($down_work_total_today)) {
	echo $row['download'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($down_work_total_yesterday)) {
	echo $row['download'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($down_work_total_last7days)) {
	echo $row['download'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($down_work_total_last30days)) {
	echo $row['download'];
}	
echo '</td></tr>';
mysql_free_result($down_work_total_today);
mysql_free_result($down_work_total_yesterday);
mysql_free_result($down_work_total_last7days);
mysql_free_result($down_work_total_last30days);

for ($i=1; $i<=10; $i++) {
	echo "<tr>";
	echo "<td>Top #" . $i . "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($down_work_today)) {
	    echo uservolumelink($row['username'], $row['download']) . " (" . userdetailslink($row['username'], $row['name']). " " . $row['email'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($down_work_yesterday)) {
	    echo uservolumelink($row['username'], $row['download']) . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($down_work_last7days)) {
	    echo uservolumelink($row['username'], $row['download']) . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($down_work_last30days)) {
	    echo uservolumelink($row['username'], $row['download']) . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "</tr>";
}
echo "</table>";
mysql_free_result($down_work_today);
mysql_free_result($down_work_yesterday);
mysql_free_result($down_work_last7days);
mysql_free_result($down_work_last30days);
?>
