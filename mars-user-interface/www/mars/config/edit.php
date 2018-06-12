<? 
$HEADLINE = 'Configuration'; 
include '../common.php'; 
include '../menu.php'; 
include './crontab.php'; 

define("CRONJOB_AUTOLOGIN", '*/5 * * * * /home/marsPortal/misc/captiveportal-auto-login-devices.sh');

?>


<!-- begin page-specific content ########################################### -->
    <div id="main">
      <div class="page-header">
  	    <h1>Change settings</h1>
	  </div>

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
    <label class="control-label col-lg-2" for="value_type_name">Auto-login to Portal for all connected devices (every 5 mins)</label>
    <div class="col-lg-4">
       <input name="" type="hidden" value="0" />
	   <? if (doesJobExist(CRONJOB_AUTOLOGIN)) { ?>
	   <input checked class="form-control input-sm" type="checkbox" value="1" name="autologin" id="autologin" />
	   <? } else { ?>
	   <input class="form-control input-sm" type="checkbox" value="1" name="autologin" id="autologin" />
		<? } ?>
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

</div>
</body>

