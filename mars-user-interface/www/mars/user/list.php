<? 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 

include '../config.php'; 

echo "<table>"; 
echo "<tr>"; 
echo "<td><b>Name</b></td>"; 
echo "<td><b>Username (MAC)</b></td>"; 
echo "<td><b>Group</b></td>"; 
echo "<td><b>Mac Vendor</b></td>"; 
echo "<td><b>Hostname</b></td>"; 
echo "<td><b>Email</b></td>"; 
echo "<td><b>Department</b></td>"; 
echo "<td><b>Organisation</b></td>"; 
echo "<td><b>Registration Date</b></td>"; 
echo "<td><b>Initial Ip</b></td>"; 
echo "<td><b>Notes</b></td>"; 
echo "<td><b>Join</b></td>"; 
echo "</tr>"; 
echo "<form action='' method='POST'>";

$result = mysql_query("SELECT u.username AS username, u.firstname AS firstname, u.lastname AS lastname, u.mac_vendor AS mac_vendor, u.hostname AS hostname, u.email AS email, u.department AS department, u.organisation AS organisation, u.registration_date AS registration_date, u.initial_ip AS initial_ip, u.notes AS notes, radusergroup.groupname AS groupname FROM userinfo AS u LEFT JOIN radusergroup on u.username = radusergroup.username ORDER BY lastname, firstname ASC") or trigger_error(mysql_error()); 
while($row = mysql_fetch_array($result)){ 
foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
echo "<tr>";  
$name = nl2br( $row['firstname']) . " " . nl2br( $row['lastname']);
echo "<td>" . $name . "</td>";  
echo "<td>" . nl2br( $row['username']) . "</td>";  
echo "<td>" . nl2br( $row['groupname']) . "</td>";  
echo "<td>" . nl2br( $row['mac_vendor']) . "</td>";  
echo "<td>" . nl2br( $row['hostname']) . "</td>";  
echo "<td>" . nl2br( $row['email']) . "</td>";  
echo "<td>" . nl2br( $row['department']) . "</td>";  
echo "<td>" . nl2br( $row['organisation']) . "</td>";  
echo "<td>" . nl2br( $row['registration_date']) . "</td>";  
echo "<td>" . nl2br( $row['initial_ip']) . "</td>";  
echo "<td>" . nl2br( $row['notes']) . "</td>";  
echo "<td><input type='checkbox' name='$row['username']' value='$row['username']'></td> "; 
echo "</tr>"; 
} 
echo "</form></table>"; 
?>
<a href=edit.php?username={$row['username']}>Join</a>
</div>
</body>
