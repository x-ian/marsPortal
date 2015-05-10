<? 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
include('../config.php'); 

function online() {
	return "
		SELECT radacct.username AS username, userinfo.firstname AS firstname, userinfo.lastname AS lastname, userinfo.mac_vendor AS mac_vendor, userinfo.hostname AS hostname, userinfo.email AS email, userinfo.department AS department, userinfo.organisation AS organisation, userinfo.registration_date AS registration_date, radacct.FramedIPAddress, radacct.AcctStartTime, radacct.AcctSessionTime, radacct.AcctInputOctets AS Upload, radacct.AcctOutputOctets AS Download 
		FROM radacct LEFT JOIN userinfo ON (radacct.Username = userinfo.Username) 
		WHERE (radacct.AcctStopTime IS NULL OR radacct.AcctStopTime = '0000-00-00 00:00:00') AND DATE_ADD(radacct.acctstarttime, INTERVAL radacct.acctsessiontime second) > DATE_ADD(NOW(), INTERVAL -10 MINUTE) AND (radacct.Username LIKE '%') 
		ORDER BY radacctid asc;";
}
?>

<span style="font-variant:small-caps; font-size:200%">
	<p align="center">
		marsPortal Users currently online (<?php echo date('Y-m-d H:i:s'); ?>)
	</p>
</span>


<table>
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
	echo "<tr><td>" . $row['username'] . "</td><td>" . $row['lastname'] . "</td><td>" . $row['mac_vendor'] . "</td></tr>";
}
?>
</table>
