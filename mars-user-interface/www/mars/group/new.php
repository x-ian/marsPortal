<? 
include '/mars/menu.php'; 
?>

<? 
include('/mars/config.php'); 
if (isset($_POST['submitted'])) { 
foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 
$sql = "INSERT INTO `group` ( `groupname` ,  `work_total_input` ,  `work_total_output` ,  `day_total_input` ,  `day_total_output` ,  `bandwidth_up` ,  `bandwidth_down` ,  `session_timeout` ,  `concurrent_user` ,  `auth_type` ,  `reply_message`  ) VALUES(  '{$_POST['groupname']}' ,  '{$_POST['work_total_input']}' ,  '{$_POST['work_total_output']}' ,  '{$_POST['day_total_input']}' ,  '{$_POST['day_total_output']}' ,  '{$_POST['bandwidth_up']}' ,  '{$_POST['bandwidth_down']}' ,  '{$_POST['session_timeout']}' ,  '{$_POST['concurrent_user']}' ,  '{$_POST['auth_type']}' ,  '{$_POST['reply_message']}'  ) "; 
mysql_query($sql) or die(mysql_error()); 
echo "Added row.<br />"; 
echo "<a href='list.php'>Back To Listing</a>"; 
} 
?>

<form action='' method='POST'> 
<p><b>Groupname:</b><br /><input type='text' name='groupname'/> 
<p><b>Work Total Input:</b><br /><input type='text' name='work_total_input'/> 
<p><b>Work Total Output:</b><br /><input type='text' name='work_total_output'/> 
<p><b>Day Total Input:</b><br /><input type='text' name='day_total_input'/> 
<p><b>Day Total Output:</b><br /><input type='text' name='day_total_output'/> 
<p><b>Bandwidth Up:</b><br /><input type='text' name='bandwidth_up'/> 
<p><b>Bandwidth Down:</b><br /><input type='text' name='bandwidth_down'/> 
<p><b>Session Timeout:</b><br /><input type='text' name='session_timeout'/> 
<p><b>Concurrent User:</b><br /><input type='text' name='concurrent_user'/> 
<p><b>Auth Type:</b><br /><input type='text' name='auth_type'/> 
<p><b>Reply Message:</b><br /><input type='text' name='reply_message'/> 
<p><input type='submit' value='Add Row' /><input type='hidden' value='1' name='submitted' /> 
</form> 
