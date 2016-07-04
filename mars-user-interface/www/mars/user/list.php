<? 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<script>
	function ensureTwoCheckboxes() {
		var nodeList = document.getElementsByName('mac[]')
		var checked = 0;
		for (var i = 0; i < nodeList.length; ++i) {
		    if (nodeList[i].checked) {
		    	checked++;
		    }
		}
		if (checked == 2) {
			document.getElementById('accept').disabled=false;
		} else {
			document.getElementById('accept').disabled=true;			
		}
	}
</script>
<? 

include '../config.php'; 

echo "<table>"; 
echo "<tr>"; 
echo "<td><b>Name</b></td>"; 
echo "<td><b>Group</b></td>"; 
echo "<td><b>Department</b></td>"; 
echo "<td><b>MAC address</b></td>"; 
echo "<td><b>Join</b></td>"; 
echo "<td><b>Mac Vendor</b></td>"; 
echo "<td><b>Hostname</b></td>"; 
echo "<td><b>Email</b></td>"; 
echo "<td><b>Organisation</b></td>"; 
echo "<td><b>Notes</b></td>"; 
echo "</tr>"; 
echo "<form action='join-devices-post.php' method='POST'>";

$result = mysql_query("SELECT u.username AS username, u.firstname AS firstname, u.lastname AS lastname, u.mac_vendor AS mac_vendor, u.hostname AS hostname, u.email AS email, u.department AS department, u.organisation AS organisation, u.registration_date AS registration_date, u.initial_ip AS initial_ip, u.notes AS notes, radusergroup.groupname AS groupname FROM userinfo AS u LEFT JOIN radusergroup on u.username = radusergroup.username ORDER BY lastname, firstname ASC") or trigger_error(mysql_error()); 
$previous_name = "";
while($row = mysql_fetch_array($result)){ 
foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
echo "<tr>";  
$name = nl2br( $row['firstname']) . " " . nl2br( $row['lastname']);
if ($previous_name == $name) {
	echo "<td></td>";	
	echo "<td></td>";	
	echo "<td></td>";	
} else {
	echo "<td>" . $name . "</td>";
	$previous_name = $name;
	echo "<td>" . nl2br( $row['groupname']) . "</td>";  
	echo "<td>" . nl2br( $row['department']) . "</td>";  
}
echo "<td><a href=/mars/userinfo/edit.php?username=" . nl2br($row['username']) . ">" . nl2br( $row['username']) . "</a></td>";  
echo "<td><input type='checkbox' name='mac[]'" . "' value='" . $row['username'] . "' onchange='ensureTwoCheckboxes();'></td> "; 
echo "<td>" . nl2br( $row['mac_vendor']) . "</td>";  
echo "<td>" . nl2br( $row['hostname']) . "</td>";  
echo "<td>" . nl2br( $row['email']) . "</td>";  
echo "<td>" . nl2br( $row['organisation']) . "</td>";  
echo "<td>" . nl2br( $row['notes']) . "</td>";  
echo "</tr>"; 
} 
echo "<tr><td><br/></td></tr><tr><td colspan='10'><input id='accept' name='accept' type='submit' value='Join device entries' disabled/> (unifies name, department & group; only active when two devices are selected)</td></tr>";
echo "</form></table>"; 
?>
</div>
</body>
