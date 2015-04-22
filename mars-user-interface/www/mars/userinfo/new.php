<? 
include '/mars/menu.php'; 
?>

<? 
include('/mars/config.php'); 
if (isset($_POST['submitted'])) { 
foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 
$sql = "INSERT INTO `userinfo` ( `username` ,  `firstname` ,  `lastname` ,  `email` ,  `department` ,  `organisation` ,  `initial_ip` ,  `hostname` ,  `registration_date` ,  `mac_vendor` ,  `notes`  ) VALUES(  '{$_POST['username']}' ,  '{$_POST['firstname']}' ,  '{$_POST['lastname']}' ,  '{$_POST['email']}' ,  '{$_POST['department']}' ,  '{$_POST['organisation']}' ,  '{$_POST['initial_ip']}' ,  '{$_POST['hostname']}' ,  '{$_POST['registration_date']}' ,  '{$_POST['mac_vendor']}' ,  '{$_POST['notes']}'  ) "; 
mysql_query($sql) or die(mysql_error()); 
echo "Added row.<br />"; 
echo "<a href='list.php'>Back To Listing</a>"; 
} 
?>

<form action='' method='POST'> 
<p><b>Username:</b><br /><input type='text' name='username'/> 
<p><b>Firstname:</b><br /><input type='text' name='firstname'/> 
<p><b>Lastname:</b><br /><input type='text' name='lastname'/> 
<p><b>Email:</b><br /><input type='text' name='email'/> 
<p><b>Department:</b><br /><input type='text' name='department'/> 
<p><b>Organisation:</b><br /><input type='text' name='organisation'/> 
<p><b>Initial Ip:</b><br /><input type='text' name='initial_ip'/> 
<p><b>Hostname:</b><br /><input type='text' name='hostname'/> 
<p><b>Registration Date:</b><br /><input type='text' name='registration_date'/> 
<p><b>Mac Vendor:</b><br /><input type='text' name='mac_vendor'/> 
<p><b>Notes:</b><br /><input type='text' name='notes'/> 
<p><input type='submit' value='Add Row' /><input type='hidden' value='1' name='submitted' /> 
</form> 

