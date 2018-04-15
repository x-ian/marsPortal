<? 
$HEADLINE = 'All users'; 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<?
if (isset($_POST['submitted'])) { 
	$mac1 = $_POST['mac'][0]; 
	$mac2 = $_POST['mac'][1]; 
	
	$mac_source = mysql_query("select * from userinfo where username = '" . $mac1 . "';");
	$firstname = "";
	$lastname = "";
	$department = "";
	if ($row = mysql_fetch_assoc($mac_source)) {
		$firstname = $row['firstname'];
		$lastname = $row['lastname'];
		$department = $row['department'];
	}
	$mac_source_group = mysql_query("select * from radusergroup where username = '" . $mac1 . "'");
	$group = "";
	if ($row_group = mysql_fetch_assoc($mac_source_group)) {
		$group = $row_group['groupname'];
	}
		
	$update_mac_target = mysql_query(" UPDATE userinfo set firstname = '$firstname', lastname = '$lastname', department = '$department' where username = '$mac2' ");
	$update_mac_target_group = mysql_query(" UPDATE radusergroup set groupname = '$group' where username = '$mac2' ");

	echo "Devices joined.<br />"; 
}
?>

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
			document.getElementById('submitted').disabled=false;
		} else {
			document.getElementById('submitted').disabled=true;			
		}
	}
</script>
	
<table class='table table-striped'>
<thead><tr>
<td>Name</td>
<td>Group</td>
<td>Department</td>
<td>MAC address</td>
<td>Join</td>
<td>Mac Vendor</td>
<td>Hostname</td>
<td>Email</td>
<td>Organisation</td>
<td>Notes</td>
</tr></thead>
<tbody>
<?
echo "<form action='' method='POST'>";

$result = mysql_query("SELECT u.username AS username, u.firstname AS firstname, u.lastname AS lastname, u.mac_vendor AS mac_vendor, u.hostname AS hostname, u.email AS email, u.department AS department, u.organisation AS organisation, u.registration_date AS registration_date, u.initial_ip AS initial_ip, u.notes AS notes, radusergroup.groupname AS groupname FROM userinfo AS u LEFT JOIN radusergroup on u.username = radusergroup.username ORDER BY lastname, firstname ASC") or trigger_error(mysql_error()); 
$previous_name = "";
while($row = mysql_fetch_array($result)){ 
foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
echo "<tr>";  
$name = nl2br( $row['firstname']) . "&nbsp;" . nl2br( $row['lastname']);
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
echo "<td>" . link_to_device($row) . "</td>";  
echo "<td><input type='checkbox' name='mac[]'" . "' value='" . $row['username'] . "' onchange='ensureTwoCheckboxes();'></td> "; 
echo "<td>" . nl2br( $row['mac_vendor']) . "</td>";  
echo "<td>" . nl2br( $row['hostname']) . "</td>";  
echo "<td>" . nl2br( $row['email']) . "</td>";  
echo "<td>" . nl2br( $row['organisation']) . "</td>";  
echo "<td>" . nl2br( $row['notes']) . "</td>";  
echo "</tr>"; 
} 
echo "<tr><td><br/></td></tr><tr><td colspan='10'><input id='submitted' name='submitted' type='submit' value='Join device entries' disabled/> (unifies name, department & group; only active when two devices are selected)</td></tr>";
echo "</form></tbody></table>"; 
?>
</div>
</body>
