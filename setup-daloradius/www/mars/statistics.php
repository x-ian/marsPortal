<span style="font-variant:small-caps; font-size:200%">
	<p align="center">
		Usage Statistics (only accurate when accounting is active)
	</p>
</span>

<hr/>
<p>Registered devices overview</p>

<?php
  mysql_connect('localhost','radius','radius') or die('Could not connect to mysql server.');
  mysql_select_db('radius');

  function query($query) {
    $result = mysql_query($query);
	if (!$result) {
		$message  = 'Ungültige Abfrage: ' . mysql_error() . "\n";
		$message .= 'Gesamte Abfrage: ' . $query;
    	die($message);
	} 
	return $result;
  }
  
  function userdetailslink($mac, $name) {
	  return '<a href="/daloradius/mng-edit.php?username=' . $mac . '">' . $name . '</a>';
  }
  
  function uservolumelink($mac, $linktext) {
	  return '<a href="/mars/user_with_volume?username=' . $mac . '">' . $linktext . '</a>';
  }
  
//  $today = '2014-10-29';
$today = date('Y-m-d', strtotime('-0 day'));
  $yesterday = date('Y-m-d', strtotime('-1 day'));
  $daysago7 = date('Y-m-d', strtotime('-6 days'));
  $daysago30 = date('Y-m-d', strtotime('-29 days'));
  
  
echo "<table><tr><th></th><th>Today ($today)</th><th>Yesterday ($yesterday)</th><th>Last 7 days (from $daysago7)</th><th>Last 30 days (from $daysago30)</th></tr>";
  // active
  function active($startday, $endday) {
	return 'select radusergroup.groupname as groupname, count(distinct(radacct.username)) as count from radacct left join  radusergroup ON radacct.username=radusergroup.username where  ((acctstarttime < date(date_add("' . $endday . '", INTERVAL +1 DAY)) and acctstoptime > "' . $startday . '") or (acctstarttime < date(date_add("' . $endday . '", INTERVAL +1 DAY)) and acctstoptime is null)) group by groupname;';
  }
echo "<tr>";
echo "<td>devices active</td>";
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
echo "<tr>";
echo "<td>todo: devices denied access</td>";
echo "</tr>";


  // newly registered
  function registered($startday, $endday) {
	return 'SELECT radusergroup.groupname as groupname, count(distinct(radcheck.username)) as count FROM radcheck LEFT JOIN radusergroup ON radcheck.username=radusergroup.username LEFT JOIN userinfo ON radcheck.username=userinfo.username where creationdate > "' . $startday . '" and creationdate <  date(date_add("' . $endday . '", INTERVAL +1 DAY)) GROUP by groupname;';
  }
echo "<tr>";
echo "<td>devices registered</td>";
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
	return 'SELECT radusergroup.groupname as groupname, count(distinct(radcheck.username)) as count FROM radcheck LEFT JOIN radusergroup ON radcheck.username=radusergroup.username LEFT JOIN userinfo ON radcheck.username=userinfo.username where creationdate < date_add("' . $endday . '", INTERVAL +1 DAY) GROUP by radusergroup.groupname order by groupname;';
  }
echo "<tr>";
echo "<td>devices ever reg.</td>";
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
mysql_free_result($ever_today);
mysql_free_result($ever_yesterday);
mysql_free_result($ever_7daysago);
mysql_free_result($ever_30daysago);
 
require dirname(__FILE__)."/statistics-work-down.php";
	
require dirname(__FILE__)."/statistics-work-up.php";

require dirname(__FILE__)."/statistics-total-down.php";
	
require dirname(__FILE__)."/statistics-total-up.php";

require dirname(__FILE__)."/statistics-current-groups.php";
?>