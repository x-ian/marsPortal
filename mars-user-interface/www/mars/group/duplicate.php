<? 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
if (isset($_POST['submitted'])) { 
foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 

// re-create all radgroupcheck entries
mysql_query("DELETE FROM radgroupcheck WHERE groupname='{$_POST['groupname']}'") or die(mysql_error());
mysql_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-Max-Concurrent-Devices', '{$_POST['groupname']}', ':=', '{$_POST['concurrent_user']}')") or die(mysql_error());
mysql_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-Output-Megabytes-Daily-Total', '{$_POST['groupname']}', ':=', '{$_POST['day_total_output']}')") or die(mysql_error());
mysql_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-Input-Megabytes-Daily-Total', '{$_POST['groupname']}', ':=', '{$_POST['day_total_input']}')") or die(mysql_error());
mysql_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-Output-Megabytes-Daily-Work-Hours', '{$_POST['groupname']}', ':=', '{$_POST['work_total_output']}')") or die(mysql_error());
mysql_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('mars-Input-Megabytes-Daily-Work-Hours', '{$_POST['groupname']}', ':=', '{$_POST['work_total_input']}')") or die(mysql_error());
mysql_query("INSERT radgroupcheck (attribute, groupname, op, value) VALUES ('Auth-Type', '{$_POST['groupname']}', ':=', '{$_POST['auth_type']}')") or die(mysql_error());

// re-create all radgroupreply entries
mysql_query("DELETE FROM radgroupreply WHERE groupname='{$_POST['groupname']}'") or die(mysql_error());
mysql_query("INSERT radgroupreply (attribute, groupname, op, value) VALUES ('Session-Timeout', '{$_POST['groupname']}', ':=', '{$_POST['session_timeout']}')") or die(mysql_error());
mysql_query("INSERT radgroupreply (attribute, groupname, op, value) VALUES ('WISPr-Bandwidth-Max-Up', '{$_POST['groupname']}', ':=', '{$_POST['bandwidth_up']}')") or die(mysql_error());
mysql_query("INSERT radgroupreply (attribute, groupname, op, value) VALUES ('WISPr-Bandwidth-Max-Down', '{$_POST['groupname']}', ':=', '{$_POST['bandwidth_down']}')") or die(mysql_error());
mysql_query("INSERT radgroupreply (attribute, groupname, op, value) VALUES ('Reply-Message', '{$_POST['groupname']}', ':=', '{$_POST['reply_message']}')") or die(mysql_error());

// cleanup
mysql_query("delete from radgroupcheck where value =''") or die(mysql_error());
mysql_query("delete from radgroupreply where value =''") or die(mysql_error());

echo "Added row.<br />"; 
echo "<a href='list.php'>Back To Listing</a>"; 

} else {
	$groupname = $_GET['groupname'];
	$work_total_input = $_GET['work_total_input'];
	$work_total_output = $_GET['work_total_output'];
	$day_total_input = $_GET['day_total_input'];
	$day_total_output = $_GET['day_total_output'];
	$bandwidth_up = $_GET['bandwidth_up'];
	$bandwidth_down = $_GET['bandwidth_down'];
	$session_timeout = $_GET['session_timeout'];
	$auth_type = $_GET['auth_type'];
	$reply_message = $_GET['reply_message'];
	$concurrent_user = $_GET['concurrent_user'];
}
?>

<table>
<form action='' method='POST'> 
<tr><td><b>Groupname:</b></td><td><input size="40" type='text' name='groupname' value='<?= $groupname ?>' /> (only characters, digits, and dash; no spaces or symbols allowed)</td></tr>
<tr><td><b>Work Total Input:</b></td><td><input type='text' name='work_total_input' value='<?= $work_total_input ?>' /> (Upload, in MB)</td></tr>
<tr><td><b>Work Total Output:</b></td><td><input type='text' name='work_total_output' value='<?= $work_total_output ?>' /> (Download, in MB)</td></tr>
<tr><td><b>Day Total Input:</b></td><td><input type='text' name='day_total_input' value='<?= $day_total_input ?>' /> (Upload, in MB)</td></tr>
<tr><td><b>Day Total Output:</b></td><td><input type='text' name='day_total_output' value='<?= $day_total_output ?>' /> (Download, in MB)</td></tr>
<tr><td><b>Bandwidth Up:</b></td><td><input type='text' name='bandwidth_up' value='<?= $bandwidth_up ?>' /> (*) (in bits/per second)</td></tr>
<tr><td><b>Bandwidth Down:</b></td><td><input type='text' name='bandwidth_down' value='<?= $bandwidth_down ?>' /> (*) (in bits/per second)</td></tr>
<tr><td><b>Session Timeout:</b></td><td><input type='text' name='session_timeout' value='<?= $session_timeout ?>' /> (*) (in seconds)</td></tr>
<tr><td><b>Concurrent Users:</b></td><td><input type='text' name='concurrent_user' value='<?= $concurrent_user ?>' /> (*) (maximum number of concurrent connected users)</td></tr>
<tr><td><b>Auth Type:</b></td><td><input type='text' name='auth_type' value='<?= $auth_type ?>' /> (*) (empty by default, 'Reject' -without the quotes- to block users)</td></tr>
<tr><td><b>Reply Message:</b></td><td><input size="40" type='text' name='reply_message' value='<?= $reply_message ?>' /> (*) (empty by default, only used when Auth Type == Reject)</td></tr>
<tr><td><input type='submit' value='New Group' /><input type='hidden' value='1' name='submitted' /></td></tr>
</form> 
</table>
<p>Notes:</p>
<b>Careful: If group with same name already exists, it will be overwritten.</b>
<p>Use postfixes -non-work-hours and -open-for-today to name of group to define policies after work hours and when temporarily unblocked for the rest of the day after hitting the volume limits.</p>
</div>
</body>
