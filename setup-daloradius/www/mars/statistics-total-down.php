<?php
echo "<hr/><p>Top downloads total</p>";

  // download total
  function top_download($startday, $endday, $topX) {
return 'SELECT distinct(radacct.UserName) as username, radusergroup.groupname as groupname, userinfo.lastname as name, userinfo.email as email, userinfo.company as company, userinfo.address as address, userinfo.city as city, ROUND((sum(radacct.AcctOutputOctets)/1000000)) as download FROM radacct     LEFT JOIN radusergroup ON radacct.username=radusergroup.username LEFT JOIN userinfo ON radacct.username=userinfo.username    WHERE (AcctStopTime > "0000-00-00 00:00:01" AND AcctStartTime>"' . $startday . '" AND AcctStartTime<date(date_add("' . $endday . '", INTERVAL +1 DAY))) OR ((radacct.AcctStopTime IS NULL OR radacct.AcctStopTime = "0000-00-00 00:00:00") AND AcctStartTime<date(date_add("' . $endday . '", INTERVAL +1 DAY))) group by UserName order by download desc limit ' . $topX . ';';  
  }
$down_today = query(top_download($today, $today, 10));
$down_yesterday = query(top_download($yesterday, $yesterday, 10));
$down_last7days = query(top_download($daysago7, $today, 10));
$down_last30days = query(top_download($daysago30, $today, 10));

  function total_download($startday, $endday) {
return 'SELECT ROUND((sum(radacct.AcctOutputOctets)/1000000)) as download FROM radacct WHERE (AcctStopTime > "0000-00-00 00:00:01" AND AcctStartTime>"' . $startday . '" AND AcctStartTime<date(date_add("' . $endday . '", INTERVAL +1 DAY))) OR ((radacct.AcctStopTime IS NULL OR radacct.AcctStopTime = "0000-00-00 00:00:00") AND AcctStartTime<date(date_add("' . $endday . '", INTERVAL +1 DAY))) ;';  
  }
$down_total_today = query(total_download($today, $today));
$down_total_yesterday = query(total_download($yesterday, $yesterday));
$down_total_last7days = query(total_download($daysago7, $today));
$down_total_last30days = query(total_download($daysago30, $today));
echo "<table><tr><th>Download (MB)</th><th>Today</th><th>Yesterday</th><th>Last 7 days</th><th>Last 30 days</th></tr>";
echo '<tr><td>Total</td><td>';
if ($row = mysql_fetch_assoc($down_total_today)) {
	echo $row['download'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($down_total_yesterday)) {
	echo $row['download'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($down_total_last7days)) {
	echo $row['download'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($down_total_last30days)) {
	echo $row['download'];
}	
echo '</td></tr>';
mysql_free_result($down_total_today);
mysql_free_result($down_total_yesterday);
mysql_free_result($down_total_last7days);
mysql_free_result($down_total_last30days);

for ($i=1; $i<=10; $i++) {
	echo "<tr>";
	echo "<td>Top #" . $i . "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($down_today)) {
	    echo uservolumelink($row['username'], $row['download']) . " (" . userdetailslink($row['username'], $row['name']). " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($down_yesterday)) {
	    echo uservolumelink($row['username'], $row['download']) . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($down_last7days)) {
	    echo uservolumelink($row['username'], $row['download']) . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($down_last30days)) {
	    echo uservolumelink($row['username'], $row['download']) . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "</tr>";
}
echo "</table>";
mysql_free_result($down_today);
mysql_free_result($down_yesterday);
mysql_free_result($down_last7days);
mysql_free_result($down_last30days);
?>
