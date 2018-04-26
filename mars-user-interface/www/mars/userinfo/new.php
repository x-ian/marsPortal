<?
$HEADLINE = 'New device'; 
include '../menu.php'; 
include '../common.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
if (isset($_POST['submitted'])) { 
foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 

mysql_query("DELETE FROM radusergroup WHERE username = '{$_POST['username']}'") or die(mysql_error()); 
mysql_query("INSERT INTO radusergroup (groupname, username) VALUES ('{$_POST['groupname']}', '{$_POST['username']}')") or die(mysql_error()); 

mysql_query("DELETE FROM radcheck WHERE username = '{$_POST['username']}'") or die(mysql_error()); 
mysql_query("INSERT INTO radcheck (Username,Attribute,op,Value) VALUES ('{$_POST['username']}', 'Auth-Type', ':=', 'Accept')") or die(mysql_error()); 

$sql = "INSERT INTO `userinfo` ( `username` ,  `firstname` ,  `lastname` ,  `email` ,  `department` ,  `organisation` ,  `initial_ip` ,  `hostname` ,  `registration_date` ,  `mac_vendor` ,  `notes`  ) VALUES(  '{$_POST['username']}' ,  '{$_POST['firstname']}' ,  '{$_POST['lastname']}' ,  '{$_POST['email']}' ,  '{$_POST['department']}' ,  '{$_POST['organisation']}' ,  '{$_POST['initial_ip']}' ,  '{$_POST['hostname']}' ,  '{$_POST['registration_date']}' ,  '{$_POST['mac_vendor']}' ,  '{$_POST['notes']}'  ) "; 
mysql_query($sql) or die(mysql_error()); 

echo "Added row.</td><td>"; 
echo "<a href='list.php'>Back To Listing</a>"; 
} 
?>

<table>
<form action='' method='POST'> 
<tr><td><b>Username:</b></td><td><input type='text' name='username'/></td></tr>
<tr><td><b>Firstname:</b></td><td><input type='text' name='firstname'/> </td></tr>
<tr><td><b>Lastname:</b></td><td><input type='text' name='lastname'/> </td></tr>
<tr><td><b>Group:</b></td><td>
	<? 
	$query = "select groupname from radusergroup where username = '" . $row['username'] . "'";
	$res = mysql_query($query);
	if (($row2 = mysql_fetch_row($res)) != null) {
		$groupname = $row2[0];
	}

	$query = "(select distinct(groupname) from radgroupreply) union (select distinct(groupname) from radgroupcheck) order by groupname";
	$res = mysql_query($query);
	echo "<select name = 'groupname'>";
	while (($row3 = mysql_fetch_row($res)) != null)
	{
	    echo "<option value = " . $row3[0];
	    if ($groupname == $row3[0]) {
	        echo " selected = 'selected'";
		}
	    echo ">{$row3[0]}</option>";
	}
	echo "</select>";
	?>
	(mandatory)</td></tr>
<tr><td><b>Email:</b></td><td><input type='text' name='email'/> </td></tr>
<tr><td><b>Department:</b></td><td><input type='text' name='department'/> </td></tr>
<tr><td><b>Organisation:</b></td><td><input type='text' name='organisation'/> </td></tr>
<tr><td><b>Initial Ip:</b></td><td><input type='text' name='initial_ip'/> </td></tr>
<tr><td><b>Hostname:</b></td><td><input type='text' name='hostname'/> </td></tr>
<tr><td><b>Registration Date:</b></td><td><input type='text' name='registration_date'/> (mandatory; format YYYY-MM-DD HH:mm:ss)</td></tr>
<tr><td><b>Mac Vendor:</b></td><td><input type='text' name='mac_vendor'/> </td></tr>
<tr><td><b>Notes:</b></td><td><input type='text' name='notes'/> </td></tr>
<tr><td><input type='submit' value='Create' /><input type='hidden' value='1' name='submitted' /> </td></tr>
</form> 
</table>
</div>
</body>
