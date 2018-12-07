<? 
$HEADLINE = 'All groups'; 
include '../common.php'; 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 

function empty2($value, $postfix) {
	if ($value !== '') 
		return $value . $postfix;
	else
		return '';
	
}

echo "<table  class='table table-striped'>"; 
echo "<thead><tr>"; 
echo "<th>Groupname</th>"; 
echo "<th>Work Total</th>"; 
// echo "<th>User Work Total</th>";
echo "<th>Bandwidth</th>"; 
echo "<th>Session Timeout</th>"; 
echo "<th>Max Concurrent Users</th>"; 
echo "<th>Day Total</th>"; 
// echo "<th>User Day Total</th>";
echo "<th>Auth Type</th>"; 
//echo "<th>Reply Message</th>"; 
echo "</tr></thead><tbody>"; 
$result = mysql_query('
select rr1.groupname, 
	(select value from radgroupcheck r2 where attribute="mars-Max-Concurrent-Devices" and r2.groupname = r1.groupname)  "Max Concurrent Users", 
	(select value from radgroupcheck r5 where attribute="mars-Output-Megabytes-Daily-Total" and r5.groupname = r1.groupname)  "Max Daily Down", 
	(select value from radgroupcheck r6 where attribute="mars-Input-Megabytes-Daily-Total" and r6.groupname = r1.groupname)  "Max Daily Up", 
	(select value from radgroupcheck r9 where attribute="mars-Output-Megabytes-Daily-Work-Hours" and r9.groupname = r1.groupname)  "Max Work Hours Down", 
	(select value from radgroupcheck r10 where attribute="mars-Input-Megabytes-Daily-Work-Hours" and r10.groupname = r1.groupname)  "Max Work Hours Up", 
	(select value from radgroupcheck r3 where attribute="mars-User-Output-Megabytes-Daily-Total" and r3.groupname = r1.groupname)  "User Max Daily Down", 
	(select value from radgroupcheck r4 where attribute="mars-User-Input-Megabytes-Daily-Total" and r4.groupname = r1.groupname)  "User Max Daily Up", 
	(select value from radgroupcheck r7 where attribute="mars-User-Output-Megabytes-Daily-Work-Hours" and r7.groupname = r1.groupname)  "User Max Work Hours Down", 
	(select value from radgroupcheck r8 where attribute="mars-User-Input-Megabytes-Daily-Work-Hours" and r8.groupname = r1.groupname)  "User Max Work Hours Up", 
	(select value from radgroupreply rr2 where attribute ="Session-Timeout" and rr2.groupname = rr1.groupname) "Session Timeout", 
	(select value from radgroupreply rr3 where attribute ="WISPr-Bandwidth-Max-Up" and rr3.groupname = rr1.groupname) "WISPr-Bandwidth-Max-Up", 
	(select value from radgroupreply rr4 where attribute ="WISPr-Bandwidth-Max-Down" and rr4.groupname = rr1.groupname) "WISPr-Bandwidth-Max-Down",
	(select value from radgroupcheck r11 where attribute="Auth-Type" and r11.groupname = r1.groupname)  "Auth Type", 
	(select value from radgroupreply rr12 where attribute ="Reply-Message" and rr12.groupname = rr1.groupname) "Reply Message"
from radgroupreply rr1 left join radgroupcheck r1 on rr1.groupname = r1.groupname 
group by rr1.groupname;') or trigger_error(mysql_error()); 
while($row = mysql_fetch_array($result)){ 
foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } ?>
<tr>

<td><?=nl2br( $row['groupname'])?></td>

<td><?=nl2br( empty2($row['Max Work Hours Up'], '&nbsp;&#8593;'))?><br/>
<?=nl2br( empty2($row['Max Work Hours Down'], '&nbsp;&#8595;'))?></td>

<!-- <td><?=nl2br( empty2($row['User Max Work Hours Up'], '&nbsp;&#8593;'))?><br/>
<?=nl2br( empty2($row['User Max Work Hours Down'], '&nbsp;&#8595;'))?></td> -->

<td><?=nl2br( empty2($row['WISPr-Bandwidth-Max-Up'], '&nbsp;&#8593;'))?><br/>
<?=nl2br( empty2($row['WISPr-Bandwidth-Max-Down'], '&nbsp;&#8595;'))?></td>

<td><?=nl2br( $row['Session Timeout'])?></td>
<td><?=nl2br( $row['Max Concurrent Users'])?></td>

<td><?=nl2br( empty2($row['Max Daily Up'], '&nbsp;&#8593;'))?><br/>
<?=nl2br( empty2($row['Max Daily Down'], '&nbsp;&#8595;'))?></td>

<!-- <td><?=nl2br( empty2($row['User Max Daily Up'], '&nbsp;&#8593;'))?><br/>
<?=nl2br( empty2($row['User Max Daily Down'], '&nbsp;&#8595;'))?></td> -->

<td><?=nl2br( $row['Auth Type'])?></td>  
<!--<td><?=nl2br( $row['Reply Message'])?></td>-->

<td>
	<a class="btn btn-default btn-xs"  href=edit.php?groupname=<?=$row['groupname']?>>Edit</a>&nbsp;
	<a class="btn btn-default btn-xs"  href=duplicate.php?&work_total_input=<?=$row['Max Work Hours Up']?>&work_total_output=<?=$row['Max Work Hours Down']?>&day_total_input=<?=$row['Max Daily Up']?>&day_total_output=<?=$row['Max Daily Down']?>&user_work_total_input=<?=$row['User Max Work Hours Up']?>&user_work_total_output=<?=$row['User Max Work Hours Down']?>&user_day_total_input=<?=$row['User Max Daily Up']?>&user_day_total_output=<?=$row['User Max Daily Down']?>&bandwidth_up=<?=$row['WISPr-Bandwidth-Max-Up']?>&bandwidth_down=<?=$row['WISPr-Bandwidth-Max-Down']?>&session_timeout=<?=$row['Session Timeout']?>&reply_message=<?=urlencode($row['Reply Message'])?>&auth_type=<?=$row['Auth Type']?>&concurrent_user=<?=$row['Max Concurrent Users']?>>Duplicate</a>&nbsp;
	<a data-confirm="Are you sure?" class="btn btn-danger btn-xs" rel="nofollow" data-method="post" href="delete.php?groupname=<?=$row['groupname']?>">Delete</a></td>
  </tr>
  <? } ?> 
</tbody></table>
<a class="btn btn-default btn-xs" href=new.php>New Group</a>

<hr/>

<pre>
Input/Output volumes in Megabytes
Bandwidth limits in bits/second
Session Timeout in s

25000 bits/second = 11.25 megabytes/hour
50000 bits/second = 22.5 megabytes/hour
150000 bits/second = 67.5 megabytes/hour
400000 bits/second = 180 megabytes/hour
2000000 bits/second = 900 megabytes/hour

with 1 megabyte = 1000000 bytes
</pre>

</div>
</body>
