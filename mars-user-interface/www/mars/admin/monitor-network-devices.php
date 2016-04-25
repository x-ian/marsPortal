<? 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<?
if($_POST['Submit']){
	$open = fopen("/home/marsPortal/monitor_network_devices.txt","w+");
	$text = $_POST['update'];
	fwrite($open, $text);
	fclose($open);
	echo "File updated.<br />"; 
	echo "File:<br />";
	$file = file("/home/marsPortal/monitor_network_devices.txt");
	foreach($file as $text) {
		echo $text."<br />";
	}
	}else{
		$file = file("/home/marsPortal/monitor_network_devices.txt");
		echo "<form action=\"".$PHP_SELF."\" method=\"post\">";
		echo "<textarea Name=\"update\" cols=\"25\" rows=\"20\">";
		foreach($file as $text) {
		echo $text;
	} 
	echo "</textarea>";
	echo "<input name=\"Submit\" type=\"submit\" value=\"Update\" />\n
	</form>";
}
?>

</div>
</body>
