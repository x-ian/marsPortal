<? 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
include('../config.php'); 
echo "<table>"; 
echo "<tr>"; 
echo "<td><b>Groupname</b></td>"; 
echo "<td><b>Work Total Input</b></td>"; 
echo "<td><b>Work Total Output</b></td>"; 
echo "<td><b>Bandwidth Up</b></td>"; 
echo "<td><b>Bandwidth Down</b></td>"; 
echo "<td><b>Session Timeout</b></td>"; 
echo "<td><b>Concurrent User</b></td>"; 
echo "<td><b>Day Total Input</b></td>"; 
echo "<td><b>Day Total Output</b></td>"; 
echo "<td><b>Auth Type</b></td>"; 
echo "<td><b>Reply Message</b></td>"; 
echo "</tr>"; 
$result = mysql_query('
select rr1.groupname, 
	(select value from radgroupcheck r2 where attribute="mars-Max-Concurrent-Devices" and r2.groupname = r1.groupname)  "Max Concurrent Users", 
	(select value from radgroupcheck r5 where attribute="mars-Output-Megabytes-Daily-Total" and r5.groupname = r1.groupname)  "Max Daily Down", 
	(select value from radgroupcheck r6 where attribute="mars-Input-Megabytes-Daily-Total" and r6.groupname = r1.groupname)  "Max Daily Up", 
	(select value from radgroupcheck r9 where attribute="mars-Output-Megabytes-Daily-Work-Hours" and r9.groupname = r1.groupname)  "Max Work Hours Down", 
	(select value from radgroupcheck r10 where attribute="mars-Input-Megabytes-Daily-Work-Hours" and r10.groupname = r1.groupname)  "Max Work Hours Up", 
	(select value from radgroupreply rr2 where attribute ="Session-Timeout" and rr2.groupname = rr1.groupname) "Session Timeout", 
	(select value from radgroupreply rr3 where attribute ="WISPr-Bandwidth-Max-Up" and rr3.groupname = rr1.groupname) "WISPr-Bandwidth-Max-Up", 
	(select value from radgroupreply rr4 where attribute ="WISPr-Bandwidth-Max-Down" and rr4.groupname = rr1.groupname) "WISPr-Bandwidth-Max-Down",
	(select value from radgroupcheck r11 where attribute="Auth-Type" and r11.groupname = r1.groupname)  "Auth Type", 
	(select value from radgroupreply rr12 where attribute ="Reply-Message" and rr12.groupname = rr1.groupname) "Reply Message"
from radgroupreply rr1 left join radgroupcheck r1 on rr1.groupname = r1.groupname 
group by rr1.groupname;') or trigger_error(mysql_error()); 
while($row = mysql_fetch_array($result)){ 
foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
echo "<tr>";  

echo "<td>" . nl2br( $row['groupname']) . "</td>";
echo "<td>" . nl2br( $row['Max Work Hours Up']) . "</td>";
echo "<td>" . nl2br( $row['Max Work Hours Down']) . "</td>";
echo "<td>" . nl2br( $row['WISPr-Bandwidth-Max-Up']) . "</td>";
echo "<td>" . nl2br( $row['WISPr-Bandwidth-Max-Down']) . "</td>";
echo "<td>" . nl2br( $row['Session Timeout']) . "</td>";
echo "<td>" . nl2br( $row['Max Concurrent Users']) . "</td>";
echo "<td>" . nl2br( $row['Max Daily Up']) . "</td>";
echo "<td>" . nl2br( $row['Max Daily Down']) . "</td>";
echo "<td>" . nl2br( $row['Auth Type']) . "</td>";  
echo "<td>" . nl2br( $row['Reply Message']) . "</td>";  
echo "<td><a href=edit.php?id={$row['id']}>Edit</a> <a href=duplicate.php?id={$row['id']}>Duplicate</a> <a href=delete.php?id={$row['id']}>Delete</a></td> "; 
echo "</tr>"; 
} 
echo "</table>"; 
echo "<a href=new.php>New Group</a>"; 
?>

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
