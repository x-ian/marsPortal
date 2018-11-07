<? 
$HEADLINE = 'New group'; 
include '../common.php'; 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
if (isset($_POST['submitted'])) { 
foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 

// re-create all radgroupcheck entries
mysqli_query("DELETE FROM radgroupcheck WHERE groupname='{$_POST['groupname']}'") or die(mysqli_error());
mysqli_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-Max-Concurrent-Devices', '{$_POST['groupname']}', ':=', '{$_POST['concurrent_user']}')") or die(mysqli_error());
mysqli_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-Output-Megabytes-Daily-Total', '{$_POST['groupname']}', ':=', '{$_POST['day_total_output']}')") or die(mysqli_error());
mysqli_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-Input-Megabytes-Daily-Total', '{$_POST['groupname']}', ':=', '{$_POST['day_total_input']}')") or die(mysqli_error());
mysqli_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-Output-Megabytes-Daily-Work-Hours', '{$_POST['groupname']}', ':=', '{$_POST['work_total_output']}')") or die(mysqli_error());
mysqli_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-Input-Megabytes-Daily-Work-Hours', '{$_POST['groupname']}', ':=', '{$_POST['work_total_input']}')") or die(mysqli_error());
mysqli_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-User-Output-Megabytes-Daily-Total', '{$_POST['groupname']}', ':=', '{$_POST['user_day_total_output']}')") or die(mysqli_error());
mysqli_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-User-Input-Megabytes-Daily-Total', '{$_POST['groupname']}', ':=', '{$_POST['user_day_total_input']}')") or die(mysqli_error());
mysqli_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-User-Output-Megabytes-Daily-Work-Hours', '{$_POST['groupname']}', ':=', '{$_POST['user_work_total_output']}')") or die(mysqli_error());
mysqli_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-User-Input-Megabytes-Daily-Work-Hours', '{$_POST['groupname']}', ':=', '{$_POST['user_work_total_input']}')") or die(mysqli_error());
mysqli_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('Auth-Type', '{$_POST['groupname']}', ':=', '{$_POST['auth_type']}')") or die(mysqli_error());

// re-create all radgroupreply entries
mysqli_query("DELETE FROM radgroupreply WHERE groupname='{$_POST['groupname']}'") or die(mysqli_error());
mysqli_query("INSERT radgroupreply (attribute, groupname, op, value) VALUES ('Session-Timeout', '{$_POST['groupname']}', ':=', '{$_POST['session_timeout']}')") or die(mysqli_error());
mysqli_query("INSERT radgroupreply (attribute, groupname, op, value) VALUES ('WISPr-Bandwidth-Max-Up', '{$_POST['groupname']}', ':=', '{$_POST['bandwidth_up']}')") or die(mysqli_error());
mysqli_query("INSERT radgroupreply (attribute, groupname, op, value) VALUES ('WISPr-Bandwidth-Max-Down', '{$_POST['groupname']}', ':=', '{$_POST['bandwidth_down']}')") or die(mysqli_error());
mysqli_query("INSERT radgroupreply (attribute, groupname, op, value) VALUES ('Reply-Message', '{$_POST['groupname']}', ':=', '{$_POST['reply_message']}')") or die(mysqli_error());

// cleanup
mysqli_query("delete from radgroupcheck where value =''") or die(mysqli_error());
mysqli_query("delete from radgroupreply where value =''") or die(mysqli_error());

echo "Added row.<br />"; 
echo "<a href='list.php'>Back To Listing</a>"; 
} 
?>

<table>
<form action='' method='POST'> 
<tr><td><b>Groupname:</b></td><td><input size="40" type='text' name='groupname' value='<?= stripslashes($row['groupname']) ?>' /> (required) (only characters, digits, and dash; no spaces or symbols allowed)</td></tr>
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
<tr><td><b>Session Timeout:</b></td><td><input type='text' name='session_timeout' required value='<?= stripslashes($row['Session Timeout']) ?>' /> (required) (*) (in seconds)</td></tr>
<tr><td><b>Concurrent Users:</b></td><td><input type='text' name='concurrent_user' value='<?= stripslashes($row['Max Concurrent Users']) ?>' /> (*) (maximum number of concurrent connected users)</td></tr>
<tr><td><b>Auth Type:</b></td><td><input type='text' name='auth_type' value='<?= stripslashes($row['Auth Type']) ?>' /> (*) (empty by default, 'Reject' -without the quotes- to block users)</td></tr>
<tr><td><b>Reply Message:</b></td><td><input size="40" type='text' name='reply_message' value='<?= stripslashes($row['Reply Message']) ?>' /> (*) (empty by default, only used when Auth Type == Reject)</td></tr>
<tr><td><input type='submit' value='Create' /><input type='hidden' value='1' name='submitted' /></td></tr>
</form> 
</table>
<p>Notes:</p>
<b>Careful: If group with same name already exists, it will be overwritten.</b>
<p>(#) If at all, always specify Input and Output together. Don't leave one of them empty.</p>
<p>Use postfixes -non-work-hours and -open-for-today to name of group to define policies after work hours and when temporarily unblocked for the rest of the day after hitting the volume limits.</p>
<p>(*) Changing these settings will only be activated once a new session is created for a device (either through manually kicking out the session on the Captive Portal or by reaching the Session Timeout).</p>
</div>
</body>
