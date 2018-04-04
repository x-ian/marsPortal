<? 
$HEADLINE = 'WAN Traffic Volume'; 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<table class='table table-striped'>
	<thead><tr>
		<th>Last 24 Hours</th><th>Upload</th><th>Download</th>
	</tr></thead>
	<tbody>
		
<?

function wan_traffic() {
	return "
		select * 
		from log_wan_traffic 
		where when2 >= date_sub(now(), interval 24 hour)
		order by at DESC;";
}

	$result = mysql_query(wan_traffic()) or trigger_error(mysql_error()); 

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

<p>WAN traffic volume recorded every 10 Mins (via netstat).</p>

<br/>

</div>
</body>
