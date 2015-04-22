<? 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
// generated with http://www.phpscaffold.com/

include 'config.php'; 

echo "<table>"; 
echo "<tr>"; 
echo "<td><b>Id</b></td>"; 
echo "<td><b>Username</b></td>"; 
echo "<td><b>Firstname</b></td>"; 
echo "<td><b>Lastname</b></td>"; 
echo "<td><b>Email</b></td>"; 
echo "<td><b>Department</b></td>"; 
echo "<td><b>Organisation</b></td>"; 
echo "<td><b>Initial Ip</b></td>"; 
echo "<td><b>Hostname</b></td>"; 
echo "<td><b>Registration Date</b></td>"; 
echo "<td><b>Mac Vendor</b></td>"; 
echo "<td><b>Notes</b></td>"; 
echo "<td><b>Group</b></td>"; 
echo "</tr>"; 

$result = mysql_query("SELECT * FROM `userinfo`") or trigger_error(mysql_error()); 
while($row = mysql_fetch_array($result)){ 
foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
echo "<tr>";  
echo "<td valign='top'>" . nl2br( $row['id']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['username']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['firstname']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['lastname']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['email']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['department']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['organisation']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['initial_ip']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['hostname']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['registration_date']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['mac_vendor']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['notes']) . "</td>";  
echo "<td valign='top'>TODO</td>";  
echo "<td valign='top'><a href=edit.php?id={$row['id']}>Edit</a></td><td><a href=delete.php?id={$row['id']}>Delete</a></td> "; 
echo "</tr>"; 
} 
echo "</table>"; 
echo "<a href=new.php>New Entry</a>"; 
?>
</div>
</body>