<?php
echo "<hr/><p>Top uploads total</p>";

  function top_upload($startday, $endday, $topX) {
return 'SELECT distinct(radacct.UserName) as username, radusergroup.groupname as groupname, userinfo.lastname as name, userinfo.email as email, userinfo.company as company, userinfo.address as address, userinfo.city as city, ROUND((sum(radacct.AcctInputOctets)/1000000)) as upload FROM radacct     LEFT JOIN radusergroup ON radacct.username=radusergroup.username LEFT JOIN userinfo ON radacct.username=userinfo.username    WHERE (AcctStopTime > "0000-00-00 00:00:01" AND AcctStartTime>"' . $startday . '" AND AcctStartTime<date(date_add("' . $endday . '", INTERVAL +1 DAY))) OR ((radacct.AcctStopTime IS NULL OR radacct.AcctStopTime = "0000-00-00 00:00:00") AND AcctStartTime<date(date_add("' . $endday . '", INTERVAL +1 DAY))) group by UserName order by upload desc limit ' . $topX . ';';  
  }
$up_today = query(top_upload($today, $today, 10));
$up_yesterday = query(top_upload($yesterday, $yesterday, 10));
$up_last7days = query(top_upload($daysago7, $today, 10));
$up_last30days = query(top_upload($daysago30, $today, 10));
echo "<table><tr><th>Upload (MB)</th><th>Today</th><th>Yesterday</th><th>Last 7 days</th><th>Last 30 days</th></tr>";

  function total_upload($startday, $endday) {
return 'SELECT ROUND((sum(radacct.AcctInputOctets)/1000000)) as upload FROM radacct WHERE (AcctStopTime > "0000-00-00 00:00:01" AND AcctStartTime>"' . $startday . '" AND AcctStartTime<date(date_add("' . $endday . '", INTERVAL +1 DAY))) OR ((radacct.AcctStopTime IS NULL OR radacct.AcctStopTime = "0000-00-00 00:00:00") AND AcctStartTime<date(date_add("' . $endday . '", INTERVAL +1 DAY))) ;';  
  }
$up_total_today = query(total_upload($today, $today));
$up_total_yesterday = query(total_upload($yesterday, $yesterday));
$up_total_last7days = query(total_upload($daysago7, $today));
$up_total_last30days = query(total_upload($daysago30, $today));
echo '<tr><td>Total</td><td>';
if ($row = mysql_fetch_assoc($up_total_today)) {
	echo $row['upload'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($up_total_yesterday)) {
	echo $row['upload'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($up_total_last7days)) {
	echo $row['upload'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($up_total_last30days)) {
	echo $row['upload'];
}	
echo '</td></tr>';
mysql_free_result($up_total_today);
mysql_free_result($up_total_yesterday);
mysql_free_result($up_total_last7days);
mysql_free_result($up_total_last30days);

for ($i=1; $i<=10; $i++) {
	echo "<tr>";
	echo "<td>Top #" . $i . "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($up_today)) {
	    echo uservolumelink($row['username'], $row['upload']) . " (" . userdetailslink($row['username'], $row['name']). " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($up_yesterday)) {
	    echo uservolumelink($row['username'], $row['upload']) . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($up_last7days)) {
	    echo uservolumelink($row['username'], $row['upload']) . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($up_last30days)) {
	    echo uservolumelink($row['username'], $row['upload']) . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "</tr>";
}
echo "</table>";
mysql_free_result($up_today);
mysql_free_result($up_yesterday);
mysql_free_result($up_last7days);
mysql_free_result($up_last30days);
?>
