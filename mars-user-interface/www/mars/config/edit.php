<? 
$HEADLINE = 'Configuration'; 
include '../common.php'; 
include '../menu.php'; 
?>


<!-- begin page-specific content ########################################### -->
    <div id="main">
      <div class="page-header">
  	    <h1>Change settings</h1>
	  </div>

<?
$ip=$_SERVER['REMOTE_ADDR'];
//if (isset($_GET['username']) ) { 
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

<form class="form-horizontal value_type" action='' method='POST'> 
	
  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Email recipients</label>
    <div class="col-lg-4">
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['']) ?>" name="" id="value_type_name" />
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Work hours</label>
    <div class="col-lg-4">
       <input name="" type="hidden" value="0" />
	   <input class="form-control input-sm" type="checkbox" value="1" name="" id="" />
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['']) ?>" name="" id="value_type_name" />
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Non-HTTP Portal auto-registration</label>
    <div class="col-lg-4">
       <input name="" type="hidden" value="0" />
	   <input class="form-control input-sm" type="checkbox" value="1" name="" id="" />
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Activate -open-for-today groups</label>
    <div class="col-lg-4">
       <input name="" type="hidden" value="0" />
	   <input class="form-control input-sm" type="checkbox" value="1" name="" id="" />
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Auto-login to Portal for all connected devices</label>
    <div class="col-lg-4">
       <input name="" type="hidden" value="0" />
	   <input class="form-control input-sm" type="checkbox" value="1" name="" id="" />
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Monitor additional network infrastructure</label>
    <div class="col-lg-4">
       <input name="" type="hidden" value="0" />
	   <input class="form-control input-sm" type="checkbox" value="1" name="" id="" />
      <input class="form-control input-sm" type="text" value="<?= stripslashes($row['']) ?>" name="" id="value_type_name" />
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

<? //} ?> 
</div>
</body>

