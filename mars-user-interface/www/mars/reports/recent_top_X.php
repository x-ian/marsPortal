<? 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
include('../config.php'); 
$order = $_GET['order']; 
?>

<a href="recent_top_X.php?order=input">Sort by Download</a> <a href="recent_top_X.php?order=output">Sort by Upload</a>

<br/><br/>

<table>
	<tr>
		<th>Username</th>
		<th>Data In (download)</th>
		<th>Data Out (upload)</th>
	</tr>

<?
function throughput_upordown($topX, $order) {
	return "select da.username, ROUND((da.day_total_input - snap.input) / 1000000) as input, ROUND((da.day_total_output - snap.output) / 1000000) as output
	from accounting_snapshot snap, daily_accounting_v2 da
	where da.username = snap.username and da.day = date_format(now(), '%Y-%m-%d') and date_format(snap.datetime, '%Y-%m-%d') = date_format(now(), '%Y-%m-%d')
	ORDER BY " . $order . " DESC LIMIT " . $topX;
}

$result = mysql_query(throughput_upordown(10, $order))  or trigger_error(mysql_error()); 

while($row = mysql_fetch_array($result)){ 
	foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
	echo "<tr>";  
	echo "<td>" . nl2br( $row[0]) . "</td>";
	echo "<td>" . nl2br( $row[1]) . "</td>";
	echo "<td>" . nl2br( $row[2]) . "</td>";
	echo "</tr>";  
}
?>
</table>

<br/>

<?
$result = mysql_query("select datetime from accounting_snapshot limit 1") or trigger_error(mysql_error()); 
if($row = mysql_fetch_array($result)){ 
	foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
	echo "Transfer volumes since " . $row[0];  
}
?>

</div>
</body>
