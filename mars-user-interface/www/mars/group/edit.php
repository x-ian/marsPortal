<? 
$HEADLINE = 'Edit group'; 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
if (isset($_GET['groupname']) ) { 
$groupname = $_GET['groupname']; 
if (isset($_POST['submitted'])) { 
	foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 

	// re-create all radgroupcheck entries
	mysql_query("DELETE FROM radgroupcheck WHERE groupname='$groupname'") or die(mysql_error());
	mysql_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-Max-Concurrent-Devices', '$groupname', ':=', '{$_POST['concurrent_user']}')") or die(mysql_error());
	mysql_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-Output-Megabytes-Daily-Total', '$groupname', ':=', '{$_POST['day_total_output']}')") or die(mysql_error());
	mysql_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-Input-Megabytes-Daily-Total', '$groupname', ':=', '{$_POST['day_total_input']}')") or die(mysql_error());
	mysql_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-Output-Megabytes-Daily-Work-Hours', '$groupname', ':=', '{$_POST['work_total_output']}')") or die(mysql_error());
	mysql_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-Input-Megabytes-Daily-Work-Hours', '$groupname', ':=', '{$_POST['work_total_input']}')") or die(mysql_error());
	mysql_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-User-Output-Megabytes-Daily-Total', '$groupname', ':=', '{$_POST['user_day_total_output']}')") or die(mysql_error());
	mysql_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-User-Input-Megabytes-Daily-Total', '$groupname', ':=', '{$_POST['user_day_total_input']}')") or die(mysql_error());
	mysql_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-User-Output-Megabytes-Daily-Work-Hours', '$groupname', ':=', '{$_POST['user_work_total_output']}')") or die(mysql_error());
	mysql_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-User-Input-Megabytes-Daily-Work-Hours', '$groupname', ':=', '{$_POST['user_work_total_input']}')") or die(mysql_error());
	mysql_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('Auth-Type', '$groupname', ':=', '{$_POST['auth_type']}')") or die(mysql_error());

	// re-create all radgroupreply entries
	mysql_query("DELETE FROM radgroupreply WHERE groupname='$groupname'") or die(mysql_error());
	mysql_query("INSERT radgroupreply (attribute, groupname, op, value) VALUES ('Session-Timeout', '$groupname', ':=', '{$_POST['session_timeout']}')") or die(mysql_error());
	mysql_query("INSERT radgroupreply (attribute, groupname, op, value) VALUES ('WISPr-Bandwidth-Max-Up', '$groupname', ':=', '{$_POST['bandwidth_up']}')") or die(mysql_error());
	mysql_query("INSERT radgroupreply (attribute, groupname, op, value) VALUES ('WISPr-Bandwidth-Max-Down', '$groupname', ':=', '{$_POST['bandwidth_down']}')") or die(mysql_error());
	mysql_query("INSERT radgroupreply (attribute, groupname, op, value) VALUES ('Reply-Message', '$groupname', ':=', '{$_POST['reply_message']}')") or die(mysql_error());

	// cleanup
	mysql_query("delete from radgroupcheck where value =''") or die(mysql_error());
	mysql_query("delete from radgroupreply where value =''") or die(mysql_error());
	
	echo "<a href='list.php'>Back To Listing</a>"; 
} 
$row = mysql_fetch_array ( mysql_query('
	
	select rr1.groupname "groupname", 
		(select value from radgroupcheck r2 where attribute="mars-Max-Concurrent-Devices" and r2.groupname = r1.groupname)  "Max Concurrent Users", 
		(select value from radgroupcheck r5 where attribute="mars-Output-Megabytes-Daily-Total" and r5.groupname = r1.groupname)  "Max Daily Down", 
		(select value from radgroupcheck r6 where attribute="mars-Input-Megabytes-Daily-Total" and r6.groupname = r1.groupname)  "Max Daily Up", 
		(select value from radgroupcheck r9 where attribute="mars-Output-Megabytes-Daily-Work-Hours" and r9.groupname = r1.groupname)  "Max Work Hours Down", 
		(select value from radgroupcheck r10 where attribute="mars-Input-Megabytes-Daily-Work-Hours" and r10.groupname = r1.groupname)  "Max Work Hours Up", 
		(select value from radgroupcheck r5 where attribute="mars-User-Output-Megabytes-Daily-Total" and r5.groupname = r1.groupname)  "User Max Daily Down", 
		(select value from radgroupcheck r6 where attribute="mars-User-Input-Megabytes-Daily-Total" and r6.groupname = r1.groupname)  "User Max Daily Up", 
		(select value from radgroupcheck r9 where attribute="mars-User-Output-Megabytes-Daily-Work-Hours" and r9.groupname = r1.groupname)  "User Max Work Hours Down", 
		(select value from radgroupcheck r10 where attribute="mars-User-Input-Megabytes-Daily-Work-Hours" and r10.groupname = r1.groupname)  "User Max Work Hours Up", 
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
<tr><td><b>Work Total Input:</b></td><td><input type='text' name='work_total_input' value='<?= stripslashes($row['Max Work Hours Up']) ?>' /> (#) (Upload, in MB)</td></tr>
<tr><td><b>Work Total Output:</b></td><td><input type='text' name='work_total_output' value='<?= stripslashes($row['Max Work Hours Down']) ?>' /> (#) (Download, in MB)</td></tr>
<tr><td><b>Day Total Input:</b></td><td><input type='text' name='day_total_input' value='<?= stripslashes($row['Max Daily Up']) ?>' /> (#) (Upload, in MB)</td></tr>
<tr><td><b>Day Total Output:</b></td><td><input type='text' name='day_total_output' value='<?= stripslashes($row['Max Daily Down']) ?>' /> (#) (Download, in MB)</td></tr>
<tr><td><b>User Work Total Input:</b></td><td><input type='text' name='user_work_total_input' value='<?= stripslashes($row['User Max Work Hours Up']) ?>' /> (#) (Upload, in MB)</td></tr>
<tr><td><b>User Work Total Output:</b></td><td><input type='text' name='user_work_total_output' value='<?= stripslashes($row['User Max Work Hours Down']) ?>' /> (#) (Download, in MB)</td></tr>
<tr><td><b>User Day Total Input:</b></td><td><input type='text' name='user_day_total_input' value='<?= stripslashes($row['User Max Daily Up']) ?>' /> (#) (Upload, in MB)</td></tr>
<tr><td><b>User Day Total Output:</b></td><td><input type='text' name='user_day_total_output' value='<?= stripslashes($row['User Max Daily Down']) ?>' /> (#) (Download, in MB)</td></tr>
<tr><td><b>Bandwidth Up:</b></td><td><input type='text' name='bandwidth_up' value='<?= stripslashes($row['WISPr-Bandwidth-Max-Up']) ?>' /> (*) (in bits/per second)</td></tr>
<tr><td><b>Bandwidth Down:</b></td><td><input type='text' name='bandwidth_down' value='<?= stripslashes($row['WISPr-Bandwidth-Max-Down']) ?>' /> (*) (in bits/per second)</td></tr>
<tr><td><b>Session Timeout:</b></td><td><input type='text' name='session_timeout' required value='<?= stripslashes($row['Session Timeout']) ?>' /> (*) (in seconds; usually 43200)</td></tr>
<tr><td><b>Concurrent Users:</b></td><td><input type='text' name='concurrent_user' value='<?= stripslashes($row['Max Concurrent Users']) ?>' /> (*) (maximum number of concurrent connected users)</td></tr>
<tr><td><b>Auth Type:</b></td><td><input type='text' name='auth_type' value='<?= stripslashes($row['Auth Type']) ?>' /> (*) (empty by default, 'Reject' -without the quotes- to block users)</td></tr>
<tr><td><b>Reply Message:</b></td><td><input size="40" type='text' name='reply_message' value='<?= stripslashes($row['Reply Message']) ?>' /> (*) (empty by default, only used when Auth Type == Reject)</td></tr>
<tr><td><input type='submit' value='Save' /><input type='hidden' value='1' name='submitted' /></td></tr>
</form> 
</table>
<p>Notes:</p>
<b>Be careful when renaming a group; user/device entries refering to the old name will NOT be updated and point then to an invalid group.</b>
<p>Use postfixes -non-work-hours and -open-for-today to name of group to define policies after work hours and when temporarily unblocked for the rest of the day after hitting the volume limits.</p>
<p>(#) If at all, always specify Input and Output together. Don't leave one of them empty.</p>
<p>(*) Changing these settings will only be activated once a new session is created for a device (either through manually kicking out the session on the Captive Portal or by reaching the Session Timeout).</p>
<? } ?> 
</div>
</body>
