<? 
$HEADLINE = 'Edit device'; 
include '../menu.php'; 
?>

<?
$ip=$_SERVER['REMOTE_ADDR'];
if (isset($_GET['username']) ) { 
$username = $_GET['username']; 
if (isset($_POST['submitted'])) { 
foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 
mysql_query("DELETE FROM radusergroup WHERE username = '{$_POST['username']}'") or die(mysql_error()); 
mysql_query("INSERT INTO radusergroup (groupname, username) VALUES ('{$_POST['groupname']}', '{$_POST['username']}')") or die(mysql_error()); 

$sql = "UPDATE `userinfo` SET  `username` =  '{$_POST['username']}' ,  `firstname` =  '{$_POST['firstname']}' ,  `lastname` =  '{$_POST['lastname']}' ,  `email` =  '{$_POST['email']}' ,  `department` =  '{$_POST['department']}' ,  `organisation` =  '{$_POST['organisation']}' ,  `initial_ip` =  '{$_POST['initial_ip']}' ,  `hostname` =  '{$_POST['hostname']}' ,  `registration_date` =  '{$_POST['registration_date']}' ,  `mac_vendor` =  '{$_POST['mac_vendor']}' ,  `notes` =  '{$_POST['notes']}'   WHERE `username` = '$username' "; 
mysql_query($sql) or die(mysql_error()); 
echo (mysql_affected_rows()) ? "Values saved. " : "Nothing changed. "; 
echo "<a href='list.php'>Back To Listing</a><br />"; 
exec("/usr/local/bin/php -q /home/marsPortal/misc/captiveportal-disconnect-user.php " . $_POST['username'], $out, $exit);
} 
$row = mysql_fetch_array ( mysql_query("SELECT * FROM `userinfo` WHERE `username` = '$username' ")); 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">
      <div class="page-header" align="center">
  	    <h1>Edit Device</h1>
		<?= dropdown_link_to_device($username) ?>
	  </div>


<form class="form-horizontal value_type" action='' method='POST'> 
	
  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Username</label>
    <div class="col-lg-4">
      <input readonly class="form-control input-sm" type="text" value="<?= stripslashes($row['username']) ?>" name="username" id="value_type_name" /> (mandatory, format xx:xx:xx:xx:xx:xx)
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">First Name</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['firstname']) ?>" name="firstname" id="value_type_name" />
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Last Name</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['lastname']) ?>" name="lastname" id="value_type_name" />
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="message_device_time">Group</label>
    <div class="col-lg-4">
	<? 
	$query = "select groupname from radusergroup where username = '" . $row['username'] . "'";
	$res = mysql_query($query);
	if (($row2 = mysql_fetch_row($res)) != null) {
		$groupname = $row2[0];
	}

	$query = "(select distinct(groupname) from radgroupreply) union (select distinct(groupname) from radgroupcheck) order by groupname";
	$res = mysql_query($query);
	echo "<select class='form-control input-sm' name = 'groupname'>";
	while (($row3 = mysql_fetch_row($res)) != null)
	{
	    echo "<option value = " . $row3[0];
	    if ($groupname == $row3[0]) {
	        echo " selected = 'selected'";
		}
	    echo ">{$row3[0]}</option>";
	}
	echo "</select>";
	?>
	(mandatory)
	</div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Email</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['email']) ?>" name="email" id="value_type_name" />
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Department</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['department']) ?>" name="department" id="value_type_name" />
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Organisation</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['organisation']) ?>" name="organisation" id="value_type_name" />
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Initial IP</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['initial_ip']) ?>" name="initial_ip" id="value_type_name" />
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Hostname</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['hostname']) ?>" name="hostname" id="value_type_name" />
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Registration Date</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['registration_date']) ?>" name="registration_date" id="value_type_name" /> (mandatory; format: YYYY-MM-DD HH:mm:ss)
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">MAC Vendor</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['mac_vendor']) ?>" name="mac_vendor" id="value_type_name" />
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Notes</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['notes']) ?>" name="notes" id="value_type_name" />
    </div>
  </div>

  <div class="form-group">
    <div class="col-lg-offset-2 col-lg-4">
      <input type="submit" name="commit" value="Save" class="btn btn-primary" data-disable-with="Saving..." />
	  <input type='hidden' value='1' name='submitted' />
      <a class="btn btn-default" href="/mars/userinfo/list.php">Cancel</a>
    </div>
  </div>
</form> 

<? } ?> 
<b>Note: Saving changes will automatically close the session of this device on the Captive Portal.</b>
</div>
</body>

