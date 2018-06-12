<? 
$HEADLINE = 'Device history'; 
include './menu.php'; 
?>

<?php $username = $_GET['username']; ?>

<!-- begin page-specific content ########################################### -->
    <div id="main">
      <div class="page-header" align="center">
  	    <h1>Traffic volume history at <?=date('Y-m-d H:i:s'); ?></h1>
		<?= dropdown_link_to_device($username) ?>
	  </div>

<!-- begin page-specific content ########################################### 
    <div id="main">

<? 

//include 'config.php'; 

?>

<span style="font-variant:small-caps; font-size:200%">
	<table align="center">
		<tr><td><img src="captiveportal-mars.jpg" /></td><td>Portal</td></tr>
	</table>
</span> 

<hr/><br/>
-->
	  
<?php
  function query($query) {
    $result = mysql_query($query);
	if (!$result) {
		$message  = 'Ungültige Abfrage: ' . mysql_error() . "\n";
		$message .= 'Gesamte Abfrage: ' . $query;
    	die($message);
	} 
	return $result;
  }
  
  $today = date('Y-m-d', strtotime('-0 day'));
  $yesterday = date('Y-m-d', strtotime('-1 day'));
  $daysago7 = date('Y-m-d', strtotime('-6 days'));
  $daysago30 = date('Y-m-d', strtotime('-29 days'));
  
echo "<table class='table table-striped'>
	<thead><tr>
		<th>Day</th>
		<th>Total up</th>
		<th>Total down</th>
	</tr></thead><tbody>";

  function user($username, $start) {
	  $a = '
		  select 
		  username, 
		  day, 
		  ROUND(SUM(0029_input+0059_input+0129_input+0159_input+0229_input+0259_input+0329_input+0359_input+0429_input+0459_input+0529_input+0559_input+0629_input+0659_input+0729_input+0759_input+0829_input+0859_input+0929_input+0959_input+1029_input+1059_input+1129_input+1159_input+1229_input+1259_input+1329_input+1359_input+1429_input+1459_input+1529_input+1559_input+1629_input+1659_input+1729_input+1759_input+1829_input+1859_input+1929_input+1959_input+2029_input+2059_input+2129_input+2159_input+2229_input+2259_input+2329_input+2359_input) / 1000000) as upload,
		  ROUND(SUM(0029_output+0059_output+0129_output+0159_output+0229_output+0259_output+0329_output+0359_output+0429_output+0459_output+0529_output+0559_output+0629_output+0659_output+0729_output+0759_output+0829_output+0859_output+0929_output+0959_output+1029_output+1059_output+1129_output+1159_output+1229_output+1259_output+1329_output+1359_output+1429_output+1459_output+1529_output+1559_output+1629_output+1659_output+1729_output+1759_output+1829_output+1859_output+1929_output+1959_output+2029_output+2059_output+2129_output+2159_output+2229_output+2259_output+2329_output+2359_output) / 1000000) as download 
		  FROM daily_accounting_v5
		  where username = "' . $username . '" and day > "' . $start . '" 
		  group by username, day order by day desc;';  
		//echo $a;
		return $a;
	}
    
$all_traffic = query(user($username, $daysago30));
while ($row = mysql_fetch_assoc($all_traffic)) {
	echo "<tr>";
	echo '<td>' . $row['day'] . '</td>';
    echo '<td>' . $row['upload'] . '</td>';
    echo '<td>' . $row['download'] . '</td>';
	echo "</tr>";
}
mysql_free_result($all_traffic);
?>
</tbody></table>
