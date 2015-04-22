<? 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
// generated with http://www.phpscaffold.com/

include '../config.php'; 

echo "<table>"; 
echo "<tr>"; 
echo "<td><b>Username (MAC)</b></td>"; 
echo "<td><b>Name</b></td>"; 
echo "<td><b>Group</b></td>"; 
echo "<td><b>Mac Vendor</b></td>"; 
echo "<td><b>Email</b></td>"; 
echo "<td><b>Department</b></td>"; 
echo "<td><b>Organisation</b></td>"; 
echo "<td><b>Registration Date</b></td>"; 
echo "<td><b>Initial Ip</b></td>"; 
echo "<td><b>Hostname</b></td>"; 
echo "<td><b>Notes</b></td>"; 
echo "</tr>"; 

$result = mysql_query("SELECT * FROM `userinfo`") or trigger_error(mysql_error()); 
while($row = mysql_fetch_array($result)){ 
foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
echo "<tr>";  
echo "<td>" . nl2br( $row['username']) . "</td>";  
echo "<td>" . nl2br( $row['firstname']) . " " . nl2br( $row['lastname']) . "</td>";  
echo "<td>TODO</td>";  
echo "<td>" . nl2br( $row['mac_vendor']) . "</td>";  
echo "<td>" . nl2br( $row['email']) . "</td>";  
echo "<td>" . nl2br( $row['department']) . "</td>";  
echo "<td>" . nl2br( $row['organisation']) . "</td>";  
echo "<td>" . nl2br( $row['registration_date']) . "</td>";  
echo "<td>" . nl2br( $row['initial_ip']) . "</td>";  
echo "<td>" . nl2br( $row['hostname']) . "</td>";  
echo "<td>" . nl2br( $row['notes']) . "</td>";  
echo "<td><a href=edit.php?id={$row['id']}>Edit</a></td><td><a href=../user_with_volume.php?username={$row['username']}>Transfer History</a></td><td><a href=delete.php?id={$row['id']}>Delete</a></td> "; 
echo "</tr>"; 
} 
echo "</table>"; 
echo "<a href=new.php>New User/device</a>"; 
?>
</div>
</body>
