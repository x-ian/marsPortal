<? 
$HEADLINE = 'Traffic volume history'; 
include '../common.php'; 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">
      <div class="page-header">
  	    <h1>Traffic volume for all registered devices <?php echo date('Y-m-d H:i:s'); ?></h1>
	  </div>

<?php
  function userdetailslink($mac, $id) {
	  return '<a href="/mars/userinfo/edit.php?username=' . $mac . '">' . $mac . '</a>';
  }
    
echo "
<table class='table table-striped'>
	<thead><tr>
		<th>Username</th>
		<th>Group</th>
		<th>Name</th>
		<th>Computername</th>
		<th>Vendor</th>
		<th>Up today</th>
		<th>Down today</th>
		<th>Up yesterday</th>
		<th>Down yesterday</th>
		<th>Up last 7 days</th>
		<th>Down last 7 days</th>
		<th>Up last 30 days</th>
		<th>Down last 30 days</th>
		<th>Up ever</th>
		<th>Down ever</th>
	</tr></thead><tbody>";

  function users() {
return '
	SELECT 
	da.username, 
	radusergroup.groupname as groupname, 
	CONCAT(userinfo.firstname, " ", userinfo.lastname) as name, 
	userinfo.hostname, 
	userinfo.mac_vendor, 
	ROUND(SUM(0029_input+0059_input+0129_input+0159_input+0229_input+0259_input+0329_input+0359_input+0429_input+0459_input+0529_input+0559_input+0629_input+0659_input+0729_input+0759_input+0829_input+0859_input+0929_input+0959_input+1029_input+1059_input+1129_input+1159_input+1229_input+1259_input+1329_input+1359_input+1429_input+1459_input+1529_input+1559_input+1629_input+1659_input+1729_input+1759_input+1829_input+1859_input+1929_input+1959_input+2029_input+2059_input+2129_input+2159_input+2229_input+2259_input+2329_input+2359_input) / 1000000) as upload_ever,
	ROUND(SUM(0029_output+0059_output+0129_output+0159_output+0229_output+0259_output+0329_output+0359_output+0429_output+0459_output+0529_output+0559_output+0629_output+0659_output+0729_output+0759_output+0829_output+0859_output+0929_output+0959_output+1029_output+1059_output+1129_output+1159_output+1229_output+1259_output+1329_output+1359_output+1429_output+1459_output+1529_output+1559_output+1629_output+1659_output+1729_output+1759_output+1829_output+1859_output+1929_output+1959_output+2029_output+2059_output+2129_output+2159_output+2229_output+2259_output+2329_output+2359_output) / 1000000) as download_ever, 
	(select ROUND(SUM(0029_input+0059_input+0129_input+0159_input+0229_input+0259_input+0329_input+0359_input+0429_input+0459_input+0529_input+0559_input+0629_input+0659_input+0729_input+0759_input+0829_input+0859_input+0929_input+0959_input+1029_input+1059_input+1129_input+1159_input+1229_input+1259_input+1329_input+1359_input+1429_input+1459_input+1529_input+1559_input+1629_input+1659_input+1729_input+1759_input+1829_input+1859_input+1929_input+1959_input+2029_input+2059_input+2129_input+2159_input+2229_input+2259_input+2329_input+2359_input) / 1000000) as Upload_7 from daily_accounting_v5 da5_2 where da5_2.username = da.username and da5_2.day >= curdate()) as upload_today,
	(select ROUND(SUM(0029_output+0059_output+0129_output+0159_output+0229_output+0259_output+0329_output+0359_output+0429_output+0459_output+0529_output+0559_output+0629_output+0659_output+0729_output+0759_output+0829_output+0859_output+0929_output+0959_output+1029_output+1059_output+1129_output+1159_output+1229_output+1259_output+1329_output+1359_output+1429_output+1459_output+1529_output+1559_output+1629_output+1659_output+1729_output+1759_output+1829_output+1859_output+1929_output+1959_output+2029_output+2059_output+2129_output+2159_output+2229_output+2259_output+2329_output+2359_output) / 1000000) as Download from daily_accounting_v5 da5_2 where da5_2.username = da.username and da5_2.day >= curdate()) as download_today,
	(select ROUND(SUM(0029_input+0059_input+0129_input+0159_input+0229_input+0259_input+0329_input+0359_input+0429_input+0459_input+0529_input+0559_input+0629_input+0659_input+0729_input+0759_input+0829_input+0859_input+0929_input+0959_input+1029_input+1059_input+1129_input+1159_input+1229_input+1259_input+1329_input+1359_input+1429_input+1459_input+1529_input+1559_input+1629_input+1659_input+1729_input+1759_input+1829_input+1859_input+1929_input+1959_input+2029_input+2059_input+2129_input+2159_input+2229_input+2259_input+2329_input+2359_input) / 1000000) as Upload_7 from daily_accounting_v5 da5_2 where da5_2.username = da.username and da5_2.day >= date_sub(curdate(), interval 1 day) AND da5_2.day < curdate()) as upload_yesterday,
	(select ROUND(SUM(0029_output+0059_output+0129_output+0159_output+0229_output+0259_output+0329_output+0359_output+0429_output+0459_output+0529_output+0559_output+0629_output+0659_output+0729_output+0759_output+0829_output+0859_output+0929_output+0959_output+1029_output+1059_output+1129_output+1159_output+1229_output+1259_output+1329_output+1359_output+1429_output+1459_output+1529_output+1559_output+1629_output+1659_output+1729_output+1759_output+1829_output+1859_output+1929_output+1959_output+2029_output+2059_output+2129_output+2159_output+2229_output+2259_output+2329_output+2359_output) / 1000000) as Download from daily_accounting_v5 da5_2 where da5_2.username = da.username and da5_2.day >= date_sub(curdate(), interval 1 day) AND da5_2.day < curdate()) as download_yesterday,
	(select ROUND(SUM(0029_input+0059_input+0129_input+0159_input+0229_input+0259_input+0329_input+0359_input+0429_input+0459_input+0529_input+0559_input+0629_input+0659_input+0729_input+0759_input+0829_input+0859_input+0929_input+0959_input+1029_input+1059_input+1129_input+1159_input+1229_input+1259_input+1329_input+1359_input+1429_input+1459_input+1529_input+1559_input+1629_input+1659_input+1729_input+1759_input+1829_input+1859_input+1929_input+1959_input+2029_input+2059_input+2129_input+2159_input+2229_input+2259_input+2329_input+2359_input) / 1000000) as Upload_7 from daily_accounting_v5 da5_2 where da5_2.username = da.username and da5_2.day >= date_sub(curdate(), interval 7 day) AND da5_2.day <= curdate()) as upload_last7days,
	(select ROUND(SUM(0029_output+0059_output+0129_output+0159_output+0229_output+0259_output+0329_output+0359_output+0429_output+0459_output+0529_output+0559_output+0629_output+0659_output+0729_output+0759_output+0829_output+0859_output+0929_output+0959_output+1029_output+1059_output+1129_output+1159_output+1229_output+1259_output+1329_output+1359_output+1429_output+1459_output+1529_output+1559_output+1629_output+1659_output+1729_output+1759_output+1829_output+1859_output+1929_output+1959_output+2029_output+2059_output+2129_output+2159_output+2229_output+2259_output+2329_output+2359_output) / 1000000) as Download from daily_accounting_v5 da5_2 where da5_2.username = da.username and da5_2.day >= date_sub(curdate(), interval 7 day) AND da5_2.day <= curdate()) as download_last7days,
	(select ROUND(SUM(0029_input+0059_input+0129_input+0159_input+0229_input+0259_input+0329_input+0359_input+0429_input+0459_input+0529_input+0559_input+0629_input+0659_input+0729_input+0759_input+0829_input+0859_input+0929_input+0959_input+1029_input+1059_input+1129_input+1159_input+1229_input+1259_input+1329_input+1359_input+1429_input+1459_input+1529_input+1559_input+1629_input+1659_input+1729_input+1759_input+1829_input+1859_input+1929_input+1959_input+2029_input+2059_input+2129_input+2159_input+2229_input+2259_input+2329_input+2359_input) / 1000000) as Upload_7 from daily_accounting_v5 da5_2 where da5_2.username = da.username and da5_2.day >= date_sub(curdate(), interval 30 day) AND da5_2.day <= curdate()) as upload_last30days,
	(select ROUND(SUM(0029_output+0059_output+0129_output+0159_output+0229_output+0259_output+0329_output+0359_output+0429_output+0459_output+0529_output+0559_output+0629_output+0659_output+0729_output+0759_output+0829_output+0859_output+0929_output+0959_output+1029_output+1059_output+1129_output+1159_output+1229_output+1259_output+1329_output+1359_output+1429_output+1459_output+1529_output+1559_output+1629_output+1659_output+1729_output+1759_output+1829_output+1859_output+1929_output+1959_output+2029_output+2059_output+2129_output+2159_output+2229_output+2259_output+2329_output+2359_output) / 1000000) as Download from daily_accounting_v5 da5_2 where da5_2.username = da.username and da5_2.day >= date_sub(curdate(), interval 30 day) AND da5_2.day <= curdate()) as download_last30days
	FROM daily_accounting_v5 da
	LEFT JOIN radusergroup ON da.username=radusergroup.username 
	LEFT JOIN userinfo ON da.username=userinfo.username 
	WHERE da.day >= date_sub(curdate(), interval 1000 day)
	GROUP BY da.username;';  
  }
  
$all_users = query(users());
while ($row = mysqli_fetch_assoc($all_users)) {
	echo "<tr>";
    echo '<td>' . userdetailslink($row['username'], $row['id']) . '</a></td>';
    echo '<td>' . $row['groupname'] . '</td>';
    echo '<td>' . $row['name'] . '</td>';
    echo '<td>' . $row['hostname'] . '</td>';
    echo '<td>' . $row['mac_vendor'] . '</td>';
    echo '<td>' . $row['upload_today'] . '</td>';
    echo '<td>' . $row['download_today'] . '</td>';
    echo '<td>' . $row['upload_yesterday'] . '</td>';
    echo '<td>' . $row['download_yesterday'] . '</td>';
    echo '<td>' . $row['upload_last7days'] . '</td>';
    echo '<td>' . $row['download_last7days'] . '</td>';
    echo '<td>' . $row['upload_last30days'] . '</td>';
    echo '<td>' . $row['download_last30days'] . '</td>';
    echo '<td>' . $row['upload_ever'] . '</td>';
    echo '<td>' . $row['download_ever'] . '</td>';
	echo "</tr>";
}
mysqli_free_result($all_users);
?>
</tbody></table>