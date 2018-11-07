<?php
echo "<hr/><p>Current group settings</p>";

$result = mysqli_query('
select rr1.groupname, 
	(select value from radgroupcheck r2 where attribute="mars-Max-Concurrent-Devices" and r2.groupname = r1.groupname)  "Max Concurrent Users", 
	(select value from radgroupcheck r5 where attribute="mars-Output-Megabytes-Daily-Total" and r5.groupname = r1.groupname)  "Max Daily Down", 
	(select value from radgroupcheck r6 where attribute="mars-Input-Megabytes-Daily-Total" and r6.groupname = r1.groupname)  "Max Daily Up", 
	(select value from radgroupcheck r9 where attribute="mars-Output-Megabytes-Daily-Work-Hours" and r9.groupname = r1.groupname)  "Max Business Hours Down", 
	(select value from radgroupcheck r10 where attribute="mars-Input-Megabytes-Daily-Work-Hours" and r10.groupname = r1.groupname)  "Max Business Hours Up", 
	(select value from radgroupcheck r3 where attribute="mars-User-Output-Megabytes-Daily-Total" and r3.groupname = r1.groupname)  "User Max Daily Down", 
	(select value from radgroupcheck r4 where attribute="mars-User-Input-Megabytes-Daily-Total" and r4.groupname = r1.groupname)  "User Max Daily Up", 
	(select value from radgroupcheck r7 where attribute="mars-User-Output-Megabytes-Daily-Work-Hours" and r7.groupname = r1.groupname)  "User Max Business Hours Down", 
	(select value from radgroupcheck r8 where attribute="mars-User-Input-Megabytes-Daily-Work-Hours" and r8.groupname = r1.groupname)  "User Max Business Hours Up", 
	(select value from radgroupreply rr2 where attribute ="Session-Timeout" and rr2.groupname = rr1.groupname) "Session Timeout", 
	(select value from radgroupreply rr3 where attribute ="WISPr-Bandwidth-Max-Up" and rr3.groupname = rr1.groupname) "WISPr-Bandwidth-Max-Up", 
	(select value from radgroupreply rr4 where attribute ="WISPr-Bandwidth-Max-Down" and rr4.groupname = rr1.groupname) "WISPr-Bandwidth-Max-Down" 
from radgroupreply rr1 left join radgroupcheck r1 on rr1.groupname = r1.groupname 
group by rr1.groupname;');  
  
echo "<table class='listtable'><tr><th>Group</th><th>Work&nbsp;&#8593;</th><th>Work&nbsp;&#8595;</th><th>User&nbsp;Work&nbsp;&#8593;</th><th>User&nbsp;Work&nbsp;&#8595;</th><th>Rate&nbsp;&#8593;</th><th>Rate&nbsp;&#8595;</th><th>Timeout</th><th>Max. Devices</th><th>Day&nbsp;&#8593;</th><th>Day&nbsp;&#8595;</th><th>User&nbsp;Day&nbsp;&#8593;</th><th>User&nbsp;Day&nbsp;&#8595;</th></tr>";
while ($row = mysqli_fetch_assoc($result)) {
	echo "<tr>";
	echo "<td>" . $row['groupname'] . "</td>";
	echo "<td>" . $row['Max Business Hours Up'] . "</td>";
	echo "<td>" . $row['Max Business Hours Down'] . "</td>";
	echo "<td>" . $row['User Max Business Hours Up'] . "</td>";
	echo "<td>" . $row['User Max Business Hours Down'] . "</td>";
	echo "<td>" . $row['WISPr-Bandwidth-Max-Up'] . "</td>";
	echo "<td>" . $row['WISPr-Bandwidth-Max-Down'] . "</td>";
	echo "<td>" . $row['Session Timeout'] . "</td>";
	echo "<td>" . $row['Max Concurrent Users'] . "</td>";
	echo "<td>" . $row['Max Daily Up'] . "</td>";
	echo "<td>" . $row['Max Daily Down'] . "</td>";
	echo "<td>" . $row['User Max Daily Up'] . "</td>";
	echo "<td>" . $row['User Max Daily Down'] . "</td>";
	echo "</tr>";
}
echo "</table>";
?>

<pre>
Up- and Download volumes in Megabytes
Bandwidth limits in bits/second
Session Timeout in s

25000 bits/second = 11.25 megabytes/hour
50000 bits/second = 22.5 megabytes/hour
150000 bits/second = 67.5 megabytes/hour
400000 bits/second = 180 megabytes/hour
2000000 bits/second = 900 megabytes/hour

with 1 megabyte = 1000000 bytes
</pre>
