  <hr/>
  <p>Registered devices overview</p>
  

<?php
	echo "<table class='listtable'><tr><th></th><th>Today ($today)</th><th>Yesterday ($yesterday)</th><th>Last 7 days (from/at $daysago7)</th><th>Last 30 days (from/at $daysago30)</th></tr>";
	
  // active
  function active($startday, $endday) {
	return 'select radusergroup.groupname as groupname, count(distinct(radacct.username)) as count from radacct left join  radusergroup ON radacct.username=radusergroup.username where  ((acctstarttime < date(date_add("' . $endday . '", INTERVAL +1 DAY)) and acctstoptime > "' . $startday . '") or (acctstarttime < date(date_add("' . $endday . '", INTERVAL +1 DAY)) and acctstoptime is null)) group by groupname;';
  }
echo "<tr>";
echo "<td>Active</td>";
echo "<td>";
$active_today = query(active($today, $today));
while ($row = mysql_fetch_assoc($active_today)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "<td>";
$active_yesterday = query(active($yesterday, $yesterday));
while ($row = mysql_fetch_assoc($active_yesterday)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "<td>";
$active_7daysago = query(active($daysago7, $today));
while ($row = mysql_fetch_assoc($active_7daysago)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "<td>";
$active_30daysago = query(active($daysago30, $today));
while ($row = mysql_fetch_assoc($active_30daysago)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "</tr>";
mysql_free_result($active_today);
mysql_free_result($active_yesterday);
mysql_free_result($active_7daysago);
mysql_free_result($active_30daysago);



// denied access
//echo "<tr>";
//echo "<td>todo: devices denied access</td>";
//echo "</tr>";


  // newly registered
  function registered($startday, $endday) {
	return '
		SELECT radusergroup.groupname as groupname, count(distinct(radcheck.username)) as count 
		FROM radcheck LEFT JOIN radusergroup ON radcheck.username=radusergroup.username 
			LEFT JOIN userinfo ON radcheck.username=userinfo.username 
				WHERE registration_date > "' . $startday . '" and registration_date <  date(date_add("' . $endday . '", INTERVAL +1 DAY)) 
		GROUP by groupname;';
  }
echo "<tr>";
echo "<td>Registered</td>";
echo "<td>";
$registered_today = query(registered($today, $today));
while ($row = mysql_fetch_assoc($registered_today)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "<td>";
$registered_yesterday = query(registered($yesterday, $yesterday));
while ($row = mysql_fetch_assoc($registered_yesterday)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "<td>";
$registered_7daysago = query(registered($daysago7, $today));
while ($row = mysql_fetch_assoc($registered_7daysago)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "<td>";
$registered_30daysago = query(registered($daysago30, $today));
while ($row = mysql_fetch_assoc($registered_30daysago)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "</tr>";
mysql_free_result($registered_today);
mysql_free_result($registered_yesterday);
mysql_free_result($registered_7daysago);
mysql_free_result($registered_30daysago);


// ever registered as of ...
  function ever($endday) {
	return 'SELECT radusergroup.groupname as groupname, count(distinct(radcheck.username)) as count FROM radcheck LEFT JOIN radusergroup ON radcheck.username=radusergroup.username LEFT JOIN userinfo ON radcheck.username=userinfo.username where registration_date < date_add("' . $endday . '", INTERVAL +1 DAY) GROUP by radusergroup.groupname order by groupname;';
  }
  
  
echo "<tr>";
echo "<td>Ever reg. (cumul.)</td>";
echo "<td>";
$ever_today = query(ever($today));
while ($row = mysql_fetch_assoc($ever_today)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "<td>";
$ever_yesterday = query(ever($yesterday));
while ($row = mysql_fetch_assoc($ever_yesterday)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "<td>";
$ever_7daysago = query(ever($daysago7));
while ($row = mysql_fetch_assoc($ever_7daysago)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "<td>";
$ever_30daysago = query(ever($daysago30));
while ($row = mysql_fetch_assoc($ever_30daysago)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "</tr>";
echo "</table>";
echo "<p>Statistics for groups -open-for-today and -non-work-hours are only accurate for today. Additionally if a device changed groups, only the most recent group assignment is taken (and also used for the periods before).</p>";
mysql_free_result($ever_today);
mysql_free_result($ever_yesterday);
mysql_free_result($ever_7daysago);
mysql_free_result($ever_30daysago);

?>