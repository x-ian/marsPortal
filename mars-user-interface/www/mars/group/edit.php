<? 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
include('../config.php'); 
if (isset($_GET['groupname']) ) { 
$groupname = $_GET['groupname']; 
if (isset($_POST['submitted'])) { 
foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 
$sql = "UPDATE `group` SET  `groupname` =  '{$_POST['groupname']}' ,  `work_total_input` =  '{$_POST['work_total_input']}' ,  `work_total_output` =  '{$_POST['work_total_output']}' ,  `day_total_input` =  '{$_POST['day_total_input']}' ,  `day_total_output` =  '{$_POST['day_total_output']}' ,  `bandwidth_up` =  '{$_POST['bandwidth_up']}' ,  `bandwidth_down` =  '{$_POST['bandwidth_down']}' ,  `session_timeout` =  '{$_POST['session_timeout']}' ,  `concurrent_user` =  '{$_POST['concurrent_user']}' ,  `auth_type` =  '{$_POST['auth_type']}' ,  `reply_message` =  '{$_POST['reply_message']}'   WHERE `groupname` = '$groupname' "; 
mysql_query($sql) or die(mysql_error()); 
echo (mysql_affected_rows()) ? "Edited group.<br />" : "Nothing changed. <br />"; 
echo "<a href='list.php'>Back To Listing</a>"; 
} 
$row = mysql_fetch_array ( mysql_query('
	
	select rr1.groupname "groupname", 
		(select value from radgroupcheck r2 where attribute="mars-Max-Concurrent-Devices" and r2.groupname = r1.groupname)  "Max Concurrent Users", 
		(select value from radgroupcheck r5 where attribute="mars-Output-Megabytes-Daily-Total" and r5.groupname = r1.groupname)  "Max Daily Down", 
		(select value from radgroupcheck r6 where attribute="mars-Input-Megabytes-Daily-Total" and r6.groupname = r1.groupname)  "Max Daily Up", 
		(select value from radgroupcheck r9 where attribute="mars-Output-Megabytes-Daily-Work-Hours" and r9.groupname = r1.groupname)  "Max Work Hours Down", 
		(select value from radgroupcheck r10 where attribute="mars-Input-Megabytes-Daily-Work-Hours" and r10.groupname = r1.groupname)  "Max Work Hours Up", 
		(select value from radgroupreply rr2 where attribute ="Session-Timeout" and rr2.groupname = rr1.groupname) "Session Timeout", 
		(select value from radgroupreply rr3 where attribute ="WISPr-Bandwidth-Max-Up" and rr3.groupname = rr1.groupname) "WISPr-Bandwidth-Max-Up", 
		(select value from radgroupreply rr4 where attribute ="WISPr-Bandwidth-Max-Down" and rr4.groupname = rr1.groupname) "WISPr-Bandwidth-Max-Down",
		(select value from radgroupcheck r11 where attribute="Auth-Type" and r11.groupname = r1.groupname)  "Auth Type", 
		(select value from radgroupreply rr12 where attribute ="Reply-Message" and rr12.groupname = rr1.groupname) "Reply Message"
	from radgroupreply rr1 left join radgroupcheck r1 on rr1.groupname = r1.groupname 
	where rr1.groupname = "' . $groupname . '"
')); 
?>

<table>
<form action='' method='POST'> 
<tr><td><b>Groupname:</b></td><td><input readonly size="40" type='text' name='groupname' value='<?= stripslashes($row['groupname']) ?>' /> (only characters, digits, and dash; no spaces or symbols allowed)</td></tr>
<tr><td><b>Work Total Input:</b></td><td><input type='text' name='work_total_input' value='<?= stripslashes($row['Max Work Hours Up']) ?>' /> (in MB)</td></tr>
<tr><td><b>Work Total Output:</b></td><td><input type='text' name='work_total_output' value='<?= stripslashes($row['Max Work Hours Down']) ?>' /> (in MB)</td></tr>
<tr><td><b>Day Total Input:</b></td><td><input type='text' name='day_total_input' value='<?= stripslashes($row['Max Daily Up']) ?>' /> (in MB)</td></tr>
<tr><td><b>Day Total Output:</b></td><td><input type='text' name='day_total_output' value='<?= stripslashes($row['Max Daily Down']) ?>' /> (in MB)</td></tr>
<tr><td><b>Bandwidth Up:</b></td><td><input type='text' name='bandwidth_up' value='<?= stripslashes($row['WISPr-Bandwidth-Max-Up']) ?>' /> (*) (in bits/per second)</td></tr>
<tr><td><b>Bandwidth Down:</b></td><td><input type='text' name='bandwidth_down' value='<?= stripslashes($row['WISPr-Bandwidth-Max-Down']) ?>' /> (*) (in bits/per second)</td></tr>
<tr><td><b>Session Timeout:</b></td><td><input type='text' name='session_timeout' value='<?= stripslashes($row['Session Timeout']) ?>' /> (*) (in seconds)</td></tr>
<tr><td><b>Concurrent Users:</b></td><td><input type='text' name='concurrent_user' value='<?= stripslashes($row['Max Concurrent Users']) ?>' /> (*) (maximum number of concurrent connected users)</td></tr>
<tr><td><b>Auth Type:</b></td><td><input type='text' name='auth_type' value='<?= stripslashes($row['auth_type']) ?>' /> (*) (empty by default, 'Reject' -without the quotes- to block users)</td></tr>
<tr><td><b>Reply Message:</b></td><td><input size="40" type='text' name='reply_message' value='<?= stripslashes($row['Reply Message']) ?>' /> (*) (empty by default, only used when Auth Type == Reject)</td></tr>
<tr><td><input type='submit' value='Edit Row' /><input type='hidden' value='1' name='submitted' /></td></tr>
</form> 
</table>
<p>Notes:</p>
<p>Use postfixes -non-work-hours and -open-for-today to name of group to define policies after work hours and when temporarily unblocked for the rest of the day after hitting the volume limits.</p>
<p>(*) Changing these settings will only be activated once a new session is created for a device (maximum amount of Session Timeout).</p>
<? } ?> 
</div>
<b>TODO: When changing a group, ask to disconnect all users to activate new settings</b>
</body>
