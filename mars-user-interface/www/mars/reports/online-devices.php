<? 
$HEADLINE = 'Devices online'; 
include '../common.php'; 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">
      <div class="page-header">
  	    <h1>Devices currently online <?php echo date('Y-m-d H:i:s'); ?></h1>
	  </div>

<? 

function online() {
	return "
		SELECT radacct.username AS username, userinfo.firstname AS firstname, userinfo.lastname AS lastname, userinfo.mac_vendor AS mac_vendor, userinfo.hostname AS hostname, userinfo.email AS email, userinfo.department AS department, userinfo.organisation AS organisation, userinfo.registration_date AS registration_date, radacct.FramedIPAddress as ipaddress, radacct.AcctStartTime as acctstart, radacct.AcctSessionTime, radacct.AcctInputOctets AS Upload, radacct.AcctOutputOctets AS Download, DATE_ADD(radacct.acctstarttime, INTERVAL radacct.acctsessiontime second) as last_contact 
		FROM radacct LEFT JOIN userinfo ON (radacct.Username = userinfo.Username) 
		WHERE (radacct.AcctStopTime IS NULL OR radacct.AcctStopTime = '0000-00-00 00:00:00') AND DATE_ADD(radacct.acctstarttime, INTERVAL radacct.acctsessiontime second) > DATE_ADD(NOW(), INTERVAL -10 MINUTE) AND (radacct.Username LIKE '%') 
		ORDER BY radacctid asc;";
}
?>

<table class='table table-striped'>
	<thead><tr>
		<th>Device</th>
		<th>IP address</th>
		<th>Session Start</th>
		<th>Last activity</th>
	</tr></thead>
	<tbody>

<?php
$result = mysqli_query(online()) or trigger_error(mysqli_error()); 
while($row = mysql_fetch_array($result)){ 
	foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
	echo "<tr>";
	echo "<td>" . dropdown_link_to_device($row['username']) . "</td>";
	echo "<td>" . $row['ipaddress'] . "</td>";
	echo "<td>" . $row['acctstart'] . "</td>";
	echo "<td>" . $row['last_contact'] . "</td>";
	echo "</tr>";
}
?>
</tbody></table>
