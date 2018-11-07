<? 
$HEADLINE = 'WAN Throughput'; 
include '../menu.php'; 
include '../common.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<table class='table table-striped'>
	<thead><tr>
		<th>Last hour</th><th>Upload</th><th>Download</th>
	</tr></thead>
	<tbody>
		
<?

function wan_throughput() {
	return "
		select * 
		from log_wan_throughput 
		where at >= date_sub(now(), interval 1 hour)
		order by at DESC;";
}

	$result = mysqli_query(wan_throughput()) or trigger_error(mysql_error()); 

	echo "<tbody>";
while($row = mysql_fetch_array($result)){ 
	foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
?>	
	<tr>
		<td><?=$row["at"]?></td>
		<td><?=$row["tx"]?> (<?=$row["tx_unit"]?>)</td>
		<td><?=$row["rx"]?> (<?=$row["rx_unit"]?>)</td>
	</tr>
<? } ?>
</tbody></table>

<br/>

<p>WAN interface throughput measured (via netstat) every 10 seconds for a period of 10 seconds.</p>

<br/>

</div>
</body>
