<? 
$username = $_GET['username'];
$HEADLINE = 'Traffic details for ' . $username . ' (' . date('Y-m-d H:i:s') . ')';
include '../common.php'; 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">
      <div class="page-header">
  	    <h1>Traffic details <?php echo date('Y-m-d H:i:s'); ?></h1>
	  </div>

<? 
$today = date('Y-m-d', strtotime('-0 day'));
$yesterday = date('Y-m-d', strtotime('-1 day'));
$daysago7 = date('Y-m-d', strtotime('-6 days'));
$daysago30 = date('Y-m-d', strtotime('-29 days'));	

function deviceinfo($row, $upordown) {
//	$number = "<a href='/mars/device_with_volume.php?username={$row[username]}'> {$row[$upordown]} </a>";
//	$link = dropdown_link_to_device($row['username']);	
//    echo "{$number}: {$link}";
	echo "{$row} {$upordown}";
}

require dirname(__FILE__)."/device-traffic-details-2.php";

echo "<div class=\"page-header\"><h1>Top downloads</h1></div>";
generatedailytraffic('Download', $today, $yesterday, $daysago7, $daysago30);

echo "<div class=\"page-header\"><h1>Top uploads</h1></div>";
generatedailytraffic('Upload', $today, $yesterday, $daysago7, $daysago30);

?>

<br/>

<p>Updated every 5 minutes; up to first 5 minutes may not be counted.</p>

<br/>

</div>
</body>
