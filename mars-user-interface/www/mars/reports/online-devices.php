<? 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 

function online() {
	return "
		SELECT radacct.username AS username, userinfo.firstname AS firstname, userinfo.lastname AS lastname, userinfo.mac_vendor AS mac_vendor, userinfo.hostname AS hostname, userinfo.email AS email, userinfo.department AS department, userinfo.organisation AS organisation, userinfo.registration_date AS registration_date, radacct.FramedIPAddress as ipaddress, radacct.AcctStartTime as acctstart, radacct.AcctSessionTime, radacct.AcctInputOctets AS Upload, radacct.AcctOutputOctets AS Download 
		FROM radacct LEFT JOIN userinfo ON (radacct.Username = userinfo.Username) 
		WHERE (radacct.AcctStopTime IS NULL OR radacct.AcctStopTime = '0000-00-00 00:00:00') AND DATE_ADD(radacct.acctstarttime, INTERVAL radacct.acctsessiontime second) > DATE_ADD(NOW(), INTERVAL -10 MINUTE) AND (radacct.Username LIKE '%') 
		ORDER BY radacctid asc;";
}
?>

<span style="font-variant:small-caps; font-size:200%">
	<p align="center">
		marsPortal Devices currently online (<?php echo date('Y-m-d H:i:s'); ?>)
	</p>
</span>


<table class='listtable'>
	<tr>
		<th>MAC address</th>
		<th>Name</th>
		<th>Email</th>
		<th>MAC vendor</th>
		<th>Hostname</th>
		<th>Organisation</th>
		<th>IP address</th>
		<th>Session Start</th>
	</tr>
<?php
$result = mysql_query(online()) or trigger_error(mysql_error()); 
while($row = mysql_fetch_array($result)){ 
	foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
	echo "<tr>";
	echo "<td><a href=/mars/userinfo/edit.php?username=" . $row['username'] . ">" . $row['username'] . "</a></td>";
	echo "<td>" . $row['firstname'] . " " . $row['lastname'] . "</td>";
	echo "<td>" . $row['email'] . "</td>";
	echo "<td>" . $row['mac_vendor'] . "</td>";
	echo "<td>" . $row['hostname'] . "</td>";
	echo "<td>" . $row['organisation'] . "</td>";
	echo "<td>" . $row['ipaddress'] . "</td>";
	echo "<td>" . $row['acctstart'] . "</td>";
	echo "</tr>";
}
?>
</table>
