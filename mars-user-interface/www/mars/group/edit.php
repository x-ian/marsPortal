<? 
$HEADLINE = 'Edit group'; 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">
      <div class="page-header">
  	    <h1>Edit group</h1>
	  </div>

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

	// re-create groupinfo entry
	mysql_query("DELETE FROM groupinfo WHERE groupname='$groupname'") or die(mysql_error());
	mysql_query("INSERT groupinfo (groupname, auto_login) VALUES ('$groupname', '{$_POST['auto_login']}')") or die(mysql_error());
	
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
		(select value from radgroupreply rr12 where attribute ="Reply-Message" and rr12.groupname = rr1.groupname) "Reply Message",
		(select auto_login from groupinfo gi1 where gi1.groupname = rr1.groupname) "Auto Login"		
	from radgroupreply rr1 left join radgroupcheck r1 on rr1.groupname = r1.groupname 
	where rr1.groupname = "' . $groupname . '"
')); 
?>

<form class="form-horizontal value_type" action='' method='POST'> 
	
  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Groupname</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['groupname']) ?>" name="groupname" id="value_type_name" /> (only characters, digits, and dash; no spaces or symbols allowed)
    </div>
    <div class="col-lg-4">
		(only characters, digits, and dash; no spaces or symbols allowed)
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Work Total Input</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['Max Work Hours Up']) ?>" name="work_total_input" id="value_type_name" /> (#) (Upload, in MB)
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Work Total Output</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['Max Work Hours Down']) ?>" name="work_total_output" id="value_type_name" /> (#) (Download, in MB)
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Day Total Input</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['Max Daily Up']) ?>" name="day_total_input" id="value_type_name" /> (#) (Upload, in MB)
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Day Total Output</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['Max Daily Down']) ?>" name="day_total_output" id="value_type_name" /> (#) (Download, in MB)
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">User Work Total Input</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['User Max Work Hours Up']) ?>" name="user_work_total_input" id="value_type_name" /> (#) (Upload, in MB)
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">User Work Total Output</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['User Max Work Hours Down']) ?>" name="user_work_total_output" id="value_type_name" /> (#) (Download, in MB)
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">User Day Total Input</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['User Max Daily Up']) ?>" name="user_day_total_input" id="value_type_name" /> (#) (Upload, in MB)
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">User Day Total Output</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['User Max Daily Down']) ?>" name="user_day_total_output" id="value_type_name" /> (#) (Download, in MB)
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Bandwidth Up</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['WISPr-Bandwidth-Max-Up']) ?>" name="bandwidth_up" id="value_type_name" /> (*) (in bits/per second)
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Bandwidth Down</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['WISPr-Bandwidth-Max-Down']) ?>" name="bandwidth_down" id="value_type_name" /> (*) (in bits/per second)
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Session Timeout</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['Session Timeout']) ?>" name="session_timeout" id="value_type_name" /> (*) (in seconds; usually 43200)
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Concurrent Users</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['Max Concurrent Users']) ?>" name="concurrent_user" id="value_type_name" /> (*) (maximum number of concurrent connected users)
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Auth Type</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['Auth Type']) ?>" name="auth_type" id="value_type_name" /> (*) (empty by default, 'Reject' -without the quotes- to block users)
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Reply Message</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['Reply Message']) ?>" name="reply_message" id="value_type_name" /> (*) (empty by default, only used when Auth Type == Reject)
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Auto-login to Portal</label>
    <div class="col-lg-4">
       <input name="" type="hidden" value="0" />
	   <input class="form-control input-sm" type="checkbox" value="1" name="auto_login" id="auto_login" /> (when activated under Configuration - Change Settings)
    </div>
  </div>

  <div class="form-group">
    <div class="col-lg-offset-2 col-lg-4">
      <input type="submit" name="commit" value="Save" class="btn btn-primary" data-disable-with="Saving..." />
	  <input type='hidden' value='1' name='submitted' />
      <a class="btn btn-default" href="/mars/group/list.php">Cancel</a>
    </div>
  </div>
</form> 

<p>Notes:</p>
<b>Be careful when renaming a group; user/device entries referring to the old name will NOT be updated and point then to an invalid group.</b>
<p>Use postfixes -non-work-hours and -open-for-today to name of group to define policies after work hours and when temporarily unblocked for the rest of the day after hitting the volume limits.</p>
<p>(#) If at all, always specify Input and Output together. Don't leave one of them empty.</p>
<p>(*) Changing these settings will only be activated once a new session is created for a device (either through manually kicking out the session on the Captive Portal or by reaching the Session Timeout).</p>
<? } ?> 
</div>
</body>
