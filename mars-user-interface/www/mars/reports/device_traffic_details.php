<? 
$HEADLINE = 'Device Traffic Details'; 
include '../common.php'; 
include '../menu.php'; 
?>

<?php $username = $_GET['username']; ?>

<!-- begin page-specific content ########################################### -->
    <div id="main">
      <div class="page-header" align="center">
  	    <h1>Traffic Details at <?=date('Y-m-d H:i:s'); ?></h1>
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

<div class="page-header"><h1>Top 10 downloads</h1></div>

<?php
  
  $today = date('Y-m-d', strtotime('-0 day'));
  $yesterday = date('Y-m-d', strtotime('-1 day'));
  $daysago7 = date('Y-m-d', strtotime('-6 days'));
  $daysago30 = date('Y-m-d', strtotime('-29 days'));
  
echo "<table class='table table-striped'>
	<thead><tr>
		<th>Day</th>
		<th>IP</th>
		<th>Site</th>
		<th>Total down</th>
	</tr></thead><tbody>";

  function user($username, $start) {
	  $a = '
		  select day, mac, remote_ip, reverse_dns, sum(outgoing) as sum_outgoing, sum(incoming) as sum_incoming
		  from traffic_details 
		  left join ip_registry on traffic_details.remote_ip = ip_registry.ip
  		  where mac = "' . $username . '" and day >= "' . $start . '" and day <= "' . $start . '"
		  group by day, mac, remote_ip order by sum(incoming) desc limit 10
		';
		//echo $a;
		return $a;
	}
    
$all_traffic = query(user($username, $today));
while ($row = mysql_fetch_assoc($all_traffic)) {
	echo '<tr>';
	echo '<td>' . $row['day'] . '</td>';
    echo '<td>' . $row['remote_ip'] . '</td>';
    echo '<td>' . $row['remote_dns'] . '</td>';
    echo '<td>' . $row['sum_incoming'] . '</td>';
	echo "</tr>";
}
mysql_free_result($all_traffic);
?>
</tbody></table>

<hr/>

<div class="page-header"><h1>Top 10 uploads</h1></div>
<?php
echo "<table class='table table-striped'>
	<thead><tr>
		<th>Day</th>
		<th>IP</th>
		<th>Site</th>
		<th>Total up</th>
	</tr></thead><tbody>";

  function user2($username, $start) {
	  $a = '
		  select day, mac, remote_ip, reverse_dns, sum(outgoing) as sum_outgoing, sum(incoming) as sum_incoming
		  from traffic_details 
		  left join ip_registry on traffic_details.remote_ip = ip_registry.ip
  		  where mac = "' . $username . '" and day >= "' . $start . '" and day <= "' . $start . '"
		  group by day, mac, remote_ip order by sum(outgoing) desc limit 10
		';
		//echo $a;
		return $a;
	}
    
$all_traffic = query(user2($username, $today));
while ($row = mysql_fetch_assoc($all_traffic)) {
	echo '<tr>';
	echo '<td>' . $row['day'] . '</td>';
    echo '<td>' . $row['remote_ip'] . '</td>';
    echo '<td>' . $row['remote_dns'] . '</td>';
    echo '<td>' . $row['sum_outgoing'] . '</td>';
	echo "</tr>";
}
mysql_free_result($all_traffic);
?>
</tbody></table>
