<? 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
include('../config.php'); 
if (isset($_GET['id']) ) { 
$id = (int) $_GET['id']; 
if (isset($_POST['submitted'])) { 
foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 
$sql = "UPDATE `userinfo` SET  `username` =  '{$_POST['username']}' ,  `firstname` =  '{$_POST['firstname']}' ,  `lastname` =  '{$_POST['lastname']}' ,  `email` =  '{$_POST['email']}' ,  `department` =  '{$_POST['department']}' ,  `organisation` =  '{$_POST['organisation']}' ,  `initial_ip` =  '{$_POST['initial_ip']}' ,  `hostname` =  '{$_POST['hostname']}' ,  `registration_date` =  '{$_POST['registration_date']}' ,  `mac_vendor` =  '{$_POST['mac_vendor']}' ,  `notes` =  '{$_POST['notes']}'   WHERE `id` = '$id' "; 
mysql_query($sql) or die(mysql_error()); 
echo (mysql_affected_rows()) ? "Edited row.<br />" : "Nothing changed. <br />"; 
echo "<a href='list.php'>Back To Listing</a>"; 
} 
$row = mysql_fetch_array ( mysql_query("SELECT * FROM `userinfo` WHERE `id` = '$id' ")); 
?>

<form action='' method='POST'> 
<p><b>Username:</b><br /><input type='text' name='username' value='<?= stripslashes($row['username']) ?>' /> 
<p><b>Firstname:</b><br /><input type='text' name='firstname' value='<?= stripslashes($row['firstname']) ?>' /> 
<p><b>Lastname:</b><br /><input type='text' name='lastname' value='<?= stripslashes($row['lastname']) ?>' /> 
<p><b>Email:</b><br /><input type='text' name='email' value='<?= stripslashes($row['email']) ?>' /> 
<p><b>Department:</b><br /><input type='text' name='department' value='<?= stripslashes($row['department']) ?>' /> 
<p><b>Organisation:</b><br /><input type='text' name='organisation' value='<?= stripslashes($row['organisation']) ?>' /> 
<p><b>Initial Ip:</b><br /><input type='text' name='initial_ip' value='<?= stripslashes($row['initial_ip']) ?>' /> 
<p><b>Hostname:</b><br /><input type='text' name='hostname' value='<?= stripslashes($row['hostname']) ?>' /> 
<p><b>Registration Date (YYYY-MM-DD HH:mm:ss):</b><br /><input type='text' name='registration_date' value='<?= stripslashes($row['registration_date']) ?>' /> 
<p><b>Mac Vendor:</b><br /><input type='text' name='mac_vendor' value='<?= stripslashes($row['mac_vendor']) ?>' /> 
<p><b>Notes:</b><br /><input type='text' name='notes' value='<?= stripslashes($row['notes']) ?>' /> 
<p><input type='submit' value='Edit Row' /><input type='hidden' value='1' name='submitted' /> 
</form> 
<? } ?> 
</div>
</body>

