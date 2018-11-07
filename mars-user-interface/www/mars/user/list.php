<? 
$HEADLINE = 'All users'; 
include '../common.php'; 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<?
if (isset($_POST['submitted'])) { 
	$mac1 = $_POST['mac'][0]; 
	$mac2 = $_POST['mac'][1]; 
	
	$mac_source = mysqli_query($link, "select * from userinfo where username = '" . $mac1 . "';");
	$firstname = "";
	$lastname = "";
	$department = "";
	if ($row = mysqli_fetch_assoc($mac_source)) {
		$firstname = $row['firstname'];
		$lastname = $row['lastname'];
		$department = $row['department'];
	}
	$mac_source_group = mysqli_query($link, "select * from radusergroup where username = '" . $mac1 . "'");
	$group = "";
	if ($row_group = mysqli_fetch_assoc($mac_source_group)) {
		$group = $row_group['groupname'];
	}
		
	$update_mac_target = mysqli_query($link, " UPDATE userinfo set firstname = '$firstname', lastname = '$lastname', department = '$department' where username = '$mac2' ");
	$update_mac_target_group = mysqli_query($link, " UPDATE radusergroup set groupname = '$group' where username = '$mac2' ");

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
<th>Name</th>
<th>Group</th>
<th>Device</th>
<th>Join</th>
<th>Mac Vendor</th>
</tr></thead>
<tbody>
<?
echo "<form action='' method='POST'>";

$result = mysqli_query($link, "SELECT u.username AS username, u.firstname AS firstname, u.lastname AS lastname, u.mac_vendor AS mac_vendor, u.hostname AS hostname, u.email AS email, u.department AS department, u.organisation AS organisation, u.registration_date AS registration_date, u.initial_ip AS initial_ip, u.notes AS notes, radusergroup.groupname AS groupname FROM userinfo AS u LEFT JOIN radusergroup on u.username = radusergroup.username ORDER BY lastname, firstname ASC") or trigger_error(mysqli_error()); 
$previous_name = "";
while($row = mysqli_fetch_array($result)){ 
foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
echo "<tr>";  
$name = nl2br( $row['firstname']) . "&nbsp;" . nl2br( $row['lastname']);
if ($name == "&nbsp;") {
	$name = "(not available)";
}
if ($previous_name == $name) {
	echo "<td></td>";	
	echo "<td></td>";	
} else {
	echo "<td>" . $name . "</td>";
	$previous_name = $name;
	echo "<td>" . nl2br( $row['groupname']) . "</td>";  
}
echo "<td>" . link_to_device($row) . "</td>";  
echo "<td><input type='checkbox' name='mac[]'" . "' value='" . $row['username'] . "' onchange='ensureTwoCheckboxes();'></td> "; 
echo "<td>" . nl2br( $row['mac_vendor']) . "</td>";  
echo "</tr>"; 
} 
echo "<tr><td colspan='10'><input id='submitted' name='submitted' type='submit' value='Join device entries' disabled/> (unifies name, department & group; only active when two devices are selected)</td></tr>";
echo "</form></tbody></table>"; 
?>
</div>
</body>
