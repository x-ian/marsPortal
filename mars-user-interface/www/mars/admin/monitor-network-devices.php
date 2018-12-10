<? 
include '../common.php'; 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">
      <div class="page-header">
  	    <h1>Edit network devices to monitor</h1>
	  </div>

<? 
// configuration
$url = 'http://mars/editor.php';
$file = '/home/config/monitor_network_devices.txt';
	
# weird line ending behaviour
$dns_filter1 = file_get_contents($file);
$dns_filter = str_replace(array("\\r\\n"), "\n", $dns_filter1);

?>

<?
//if($_POST['Submit']){
if (isset($_POST['submitted'])) { 
	foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 
	
	# weird line ending behaviour
    file_put_contents($file, str_replace(array("\\r\\n"), "\n", $_POST['dns_filter']));
	
	echo "<a href='list.php'>Back To Listing</a>"; 
} 

//
// if($_POST['Submit']){
// 	$open = fopen("/home/marsPortal/monitor_network_devices.txt","w+");
// 	$text = $_POST['update'];
// 	fwrite($open, $text);
// 	fclose($open);
// 	echo "File updated.<br />";
// 	echo "File:<br />";
// 	$file = file("/home/marsPortal/monitor_network_devices.txt");
// 	foreach($file as $text) {
// 		echo $text."<br />";
// 	}
// 	}else{
// 		$file = file("/home/marsPortal/monitor_network_devices.txt");
// 		echo "<form action=\"".$PHP_SELF."\" method=\"post\">";
// 		echo "<textarea Name=\"update\" cols=\"25\" rows=\"20\">";
// 		foreach($file as $text) {
// 		echo $text;
// 	}
// 	echo "</textarea>";
// 	echo "<input name=\"Submit\" type=\"submit\" value=\"Update\" />\n
// 	</form>";
// }
?>

<form class="form-horizontal value_type" action='' method='POST'> 
	
  <div class="form-group">
    <label class="control-label col-lg-2" for="value_type_name">Network devices</label>
    <div class="col-lg-2">
	   	<textarea class="form-control input-sm" type="textarea" name="dns_filter" id="dns_filter" rows="5"><?= htmlspecialchars($dns_filter) ?></textarea>
    </div>
    <div class="col-lg-4 input-sm"><br/>one entry per line,<br/>Checked every morning at 7 am<br/>In case one or more devices are down, send email notification<br/>Requires an empty new line at the end</div>
  </div>

  <div class="form-group">
    <div class="col-lg-offset-2 col-lg-4">
      <input type="submit" name="commit" value="Save" class="btn btn-primary" data-disable-with="Saving..." />
	  <input type='hidden' value='1' name='submitted' />
      <a class="btn btn-default" href="/mars/admin/monitor_network_devices.php">Cancel</a>
    </div>
  </div>
</form> 

</div>
</body>
