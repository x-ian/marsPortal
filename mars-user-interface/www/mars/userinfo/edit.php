<? 
include '../menu.php'; 
?>


<!-- begin page-specific content ########################################### -->
    <div id="main">

<?
include('../config.php'); 
if (isset($_GET['username']) ) { 
$username = $_GET['username']; 
if (isset($_POST['submitted'])) { 
foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 
$sql = "UPDATE `userinfo` SET  `username` =  '{$_POST['username']}' ,  `firstname` =  '{$_POST['firstname']}' ,  `lastname` =  '{$_POST['lastname']}' ,  `email` =  '{$_POST['email']}' ,  `department` =  '{$_POST['department']}' ,  `organisation` =  '{$_POST['organisation']}' ,  `initial_ip` =  '{$_POST['initial_ip']}' ,  `hostname` =  '{$_POST['hostname']}' ,  `registration_date` =  '{$_POST['registration_date']}' ,  `mac_vendor` =  '{$_POST['mac_vendor']}' ,  `notes` =  '{$_POST['notes']}'   WHERE `username` = '$username' "; 
mysql_query($sql) or die(mysql_error()); 
echo (mysql_affected_rows()) ? "Edited row.<br />" : "Nothing changed. <br />"; 
echo "<a href='list.php'>Back To Listing</a>"; 
} 
$row = mysql_fetch_array ( mysql_query("SELECT * FROM `userinfo` WHERE `username` = '$username' ")); 
?>

<table>
<form action='' method='POST'> 
<tr><td><b>Username:</b></td><td><input type='text' name='username' value='<?= stripslashes($row['username']) ?>' /> </td></tr>
<td><b>Firstname:</b></td><td><input type='text' name='firstname' value='<?= stripslashes($row['firstname']) ?>' /> </td></tr>
<td><b>Lastname:</b></td><td><input type='text' name='lastname' value='<?= stripslashes($row['lastname']) ?>' /> </td></tr>
<tr><td><b>Group:</b></td><td><input type='text' name='groupname' value='TODO' /> </td></tr>
<tr><td><b>Email:</b></td><td><input type='text' name='email' value='<?= stripslashes($row['email']) ?>' /> </td></tr>
<tr><td><b>Department:</b></td><td><input type='text' name='department' value='<?= stripslashes($row['department']) ?>' /> </td></tr>
<tr><td><b>Organisation:</b></td><td><input type='text' name='organisation' value='<?= stripslashes($row['organisation']) ?>' /> </td></tr>
<tr><td><b>Initial Ip:</b></td><td><input type='text' name='initial_ip' value='<?= stripslashes($row['initial_ip']) ?>' /> </td></tr>
<tr><td><b>Hostname:</b></td><td><input type='text' name='hostname' value='<?= stripslashes($row['hostname']) ?>' /> </td></tr>
<tr><td><b>Registration Date:</b></td><td><input type='text' name='registration_date' value='<?= stripslashes($row['registration_date']) ?>' /> (format: YYYY-MM-DD HH:mm:ss)</td></tr>
<tr><td><b>Mac Vendor:</b></td><td><input type='text' name='mac_vendor' value='<?= stripslashes($row['mac_vendor']) ?>' /> </td></tr>
<tr><td><b>Notes:</b></td><td><input type='text' name='notes' value='<?= stripslashes($row['notes']) ?>' /> </td></tr>
<tr><td><input type='submit' value='Edit Row' /><input type='hidden' value='1' name='submitted' /> </td></tr>
</form> 
<? } ?> 
</div>
</body>

