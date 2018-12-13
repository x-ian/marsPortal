<? 
$HEADLINE = 'All devices';
include '../menu.php'; 
include '../common.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<table class='table table-striped'>
<thead><tr>
<th>Username (MAC)</th>
<th>Name</th>
<th>Group</th>
<th>Mac Vendor</th>
<th>Hostname</th>
<!--<th>Email</th>
<th>Department</th>
<th>Organisation</th>
<th>Registration Date</th>
<th>Initial Ip</th>
<th>Notes</th>-->
</tr></thead><tbody>

<? 
$result = mysql_query("SELECT u.username AS username, u.firstname AS firstname, u.lastname AS lastname, u.mac_vendor AS mac_vendor, u.hostname AS hostname, u.email AS email, u.department AS department, u.organisation AS organisation, u.registration_date AS registration_date, u.initial_ip AS initial_ip, u.notes AS notes, radusergroup.groupname AS groupname FROM userinfo AS u LEFT JOIN radusergroup on u.username = radusergroup.username ORDER BY registration_date DESC") or trigger_error(mysql_error()); 
while($row = mysql_fetch_array($result)){ 
foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
?>
<tr>  
<td><?=nl2br( $row['username'])?></td>  
<td><?=nl2br( $row['firstname'])?>&nbsp;<?=nl2br( $row['lastname'])?></td>  
<td><?=nl2br( $row['groupname'])?></td>  
<td><?=nl2br( $row['mac_vendor'])?></td>  
<td><?=nl2br( $row['hostname'])?></td>  
<!--<td><?=nl2br( $row['email'])?></td>  
<td><?=nl2br( $row['department'])?></td>  
<td><?=nl2br( $row['organisation'])?></td> 
<td><?=nl2br( $row['registration_date'])?></td>  
<td><?=nl2br( $row['initial_ip'])?></td>  
<td><?=nl2br( $row['notes'])?></td>  -->
<td>
	<a class="btn btn-default btn-xs"  href=edit.php?username=<?=$row['username']?>>Edit</a>&nbsp;
	<a class="btn btn-default btn-xs" href=/mars/reports/device-traffic-details.php?username=<?=$row['username']?>>Traffic&nbsp;Details</a>&nbsp;<a class="btn btn-default btn-xs" href=/mars/reports/device_with_volume.php?username=<?=$row['username']?>>Traffic&nbsp;History</a>&nbsp;<a class="btn btn-default btn-xs" href=/mars/reports/device-activity.php?username=<?=$row['username']?>>Activity&nbsp;History</a>&nbsp;
	<a data-confirm="Are you sure?" class="btn btn-danger btn-xs" rel="nofollow" data-method="post" href="delete.php?username=<?=$row['username']?>">Delete</a></td>
	
</tr>
<? } ?> 
</tbody></table>
<a class="btn btn-default btn-xs" href=new.php>New device</a>

</div>
</body>
