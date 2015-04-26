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
$sql = "UPDATE `group` SET  `groupname` =  '{$_POST['groupname']}' ,  `work_total_input` =  '{$_POST['work_total_input']}' ,  `work_total_output` =  '{$_POST['work_total_output']}' ,  `day_total_input` =  '{$_POST['day_total_input']}' ,  `day_total_output` =  '{$_POST['day_total_output']}' ,  `bandwidth_up` =  '{$_POST['bandwidth_up']}' ,  `bandwidth_down` =  '{$_POST['bandwidth_down']}' ,  `session_timeout` =  '{$_POST['session_timeout']}' ,  `concurrent_user` =  '{$_POST['concurrent_user']}' ,  `auth_type` =  '{$_POST['auth_type']}' ,  `reply_message` =  '{$_POST['reply_message']}'   WHERE `id` = '$id' "; 
mysql_query($sql) or die(mysql_error()); 
echo (mysql_affected_rows()) ? "Edited row.<br />" : "Nothing changed. <br />"; 
echo "<a href='list.php'>Back To Listing</a>"; 
} 
$row = mysql_fetch_array ( mysql_query("SELECT * FROM `group` WHERE `id` = '$id' ")); 
?>

<form action='' method='POST'> 
<p><b>Groupname:</b><br /><input type='text' name='groupname' value='<?= stripslashes($row['groupname']) ?>' /> 
<p><b>Work Total Input:</b><br /><input type='text' name='work_total_input' value='<?= stripslashes($row['work_total_input']) ?>' /> 
<p><b>Work Total Output:</b><br /><input type='text' name='work_total_output' value='<?= stripslashes($row['work_total_output']) ?>' /> 
<p><b>Day Total Input:</b><br /><input type='text' name='day_total_input' value='<?= stripslashes($row['day_total_input']) ?>' /> 
<p><b>Day Total Output:</b><br /><input type='text' name='day_total_output' value='<?= stripslashes($row['day_total_output']) ?>' /> 
<p><b>Bandwidth Up:</b><br /><input type='text' name='bandwidth_up' value='<?= stripslashes($row['bandwidth_up']) ?>' /> 
<p><b>Bandwidth Down:</b><br /><input type='text' name='bandwidth_down' value='<?= stripslashes($row['bandwidth_down']) ?>' /> 
<p><b>Session Timeout:</b><br /><input type='text' name='session_timeout' value='<?= stripslashes($row['session_timeout']) ?>' /> 
<p><b>Concurrent User:</b><br /><input type='text' name='concurrent_user' value='<?= stripslashes($row['concurrent_user']) ?>' /> 
<p><b>Auth Type:</b><br /><input type='text' name='auth_type' value='<?= stripslashes($row['auth_type']) ?>' /> 
<p><b>Reply Message:</b><br /><input type='text' name='reply_message' value='<?= stripslashes($row['reply_message']) ?>' /> 
<p><input type='submit' value='Edit Row' /><input type='hidden' value='1' name='submitted' /> 
</form> 
<? } ?> 
</div>
<b>TODO: When changing a group, ask to disconnect all users to activate new settings</b>
</body>
