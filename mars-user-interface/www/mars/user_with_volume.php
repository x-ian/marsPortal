<? 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 

include '../config.php'; 

?>

<span style="font-variant:small-caps; font-size:200%">
	<table align="center">
		<tr><td><img src="captiveportal-mars.jpg" /></td><td>Portal</td></tr>
	</table>
</span> 

<hr/><br/>


<?php $username = $_GET['username']; ?>

<span style="font-variant:small-caps; font-size:200%">
	<p align="center">
		Data volume for device <?php echo $username . ' at ' . date('Y-m-d H:i:s')?>
	</p>
	<br/>
</span>

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
  
echo "<table>
	<tr>
		<th/>
		<th>Working down</th>
		<th>Working up</th>
		<th>Non-Working down</th>
		<th>Non-Working up</th>
		<th>Total down</th>
		<th>Total up</th>
	</tr>";

  function user($username, $start) {
	  return '
select 
	username, 
	day, 
	ROUND((day_total_input - day_offset_input) / 1000000) as total_input, 
	ROUND((day_total_output - day_offset_output) / 1000000) as total_output, 
	ROUND((work_total_input - work_offset_input - lunch_total_input + lunch_offset_input) / 1000000) as work_input,
	ROUND((work_total_output - work_offset_output - lunch_total_output + lunch_offset_output) / 1000000) as work_output,
	ROUND((day_total_input - day_offset_input - work_total_input + work_offset_input + lunch_total_input - lunch_offset_input) / 1000000) as non_work_input, 
	ROUND((day_total_output - day_offset_output - work_total_output + work_offset_output + lunch_total_output - lunch_offset_output) / 1000000) as non_work_output
from daily_accounting_v2 
where username = "' . $username . '" and day > "' . $start . '" 
group by username, day order by day desc;';  
  }
    
$all_traffic = query(user($username, $daysago30));
while ($row = mysql_fetch_assoc($all_traffic)) {
	echo "<tr>";
	echo '<td>' . $row['day'] . '</td>';
    echo '<td>' . $row['work_output'] . '</td>';
    echo '<td>' . $row['work_input'] . '</td>';
    echo '<td>' . $row['non_work_output'] . '</td>';
    echo '<td>' . $row['non_work_input'] . '</td>';
    echo '<td>' . $row['total_output'] . '</td>';
    echo '<td>' . $row['total_input'] . '</td>';
	echo "</tr>";
}
mysql_free_result($all_traffic);
?>
</table>
<br/>
(Numbers updated every 10 minutes)