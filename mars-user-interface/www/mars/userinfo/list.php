<? 
$HEADLINE = 'All devices';
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
// generated with http://www.phpscaffold.com/

echo "<table class='listtable'>"; 
echo "<tr>"; 
echo "<td><b>Username (MAC)</b></td>"; 
echo "<td><b>Name</b></td>"; 
echo "<td><b>Group</b></td>"; 
echo "<td><b>Mac Vendor</b></td>"; 
echo "<td><b>Hostname</b></td>"; 
echo "<td><b>Email</b></td>"; 
echo "<td><b>Department</b></td>"; 
echo "<td><b>Organisation</b></td>"; 
echo "<td><b>Registration Date</b></td>"; 
echo "<td><b>Initial Ip</b></td>"; 
echo "<td><b>Notes</b></td>"; 
echo "</tr>"; 

$result = mysql_query("SELECT u.username AS username, u.firstname AS firstname, u.lastname AS lastname, u.mac_vendor AS mac_vendor, u.hostname AS hostname, u.email AS email, u.department AS department, u.organisation AS organisation, u.registration_date AS registration_date, u.initial_ip AS initial_ip, u.notes AS notes, radusergroup.groupname AS groupname FROM userinfo AS u LEFT JOIN radusergroup on u.username = radusergroup.username ORDER BY registration_date DESC") or trigger_error(mysql_error()); 
while($row = mysql_fetch_array($result)){ 
foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
echo "<tr>";  
echo "<td>" . nl2br( $row['username']) . "</td>";  
echo "<td>" . nl2br( $row['firstname']) . "&nbsp;" . nl2br( $row['lastname']) . "</td>";  
echo "<td>" . nl2br( $row['groupname']) . "</td>";  
echo "<td>" . nl2br( $row['mac_vendor']) . "</td>";  
echo "<td>" . nl2br( $row['hostname']) . "</td>";  
echo "<td>" . nl2br( $row['email']) . "</td>";  
echo "<td>" . nl2br( $row['department']) . "</td>";  
echo "<td>" . nl2br( $row['organisation']) . "</td>";  
echo "<td>" . nl2br( $row['registration_date']) . "</td>";  
echo "<td>" . nl2br( $row['initial_ip']) . "</td>";  
echo "<td>" . nl2br( $row['notes']) . "</td>";  
echo "<td><a href=edit.php?username={$row['username']}>Edit</a><br/><a href=../device_with_volume.php?username={$row['username']}>Transfer&nbsp;History</a><br/><a href=delete.php?username={$row['username']}>Delete</a></td> "; 
echo "</tr>"; 
} 
echo "</table>"; 
echo "<a href=new.php>New device</a>"; 
?>
</div>
</body>
