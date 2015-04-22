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
echo "<td><b>Day Total Input</b></td>"; 
echo "<td><b>Day Total Output</b></td>"; 
echo "<td><b>Bandwidth Up</b></td>"; 
echo "<td><b>Bandwidth Down</b></td>"; 
echo "<td><b>Session Timeout</b></td>"; 
echo "<td><b>Concurrent User</b></td>"; 
echo "<td><b>Auth Type</b></td>"; 
echo "<td><b>Reply Message</b></td>"; 
echo "</tr>"; 
$result = mysql_query("SELECT * FROM `group`") or trigger_error(mysql_error()); 
while($row = mysql_fetch_array($result)){ 
foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
echo "<tr>";  
echo "<td>" . nl2br( $row['groupname']) . "</td>";  
echo "<td>" . nl2br( $row['work_total_input']) . "</td>";  
echo "<td>" . nl2br( $row['work_total_output']) . "</td>";  
echo "<td>" . nl2br( $row['day_total_input']) . "</td>";  
echo "<td>" . nl2br( $row['day_total_output']) . "</td>";  
echo "<td>" . nl2br( $row['bandwidth_up']) . "</td>";  
echo "<td>" . nl2br( $row['bandwidth_down']) . "</td>";  
echo "<td>" . nl2br( $row['session_timeout']) . "</td>";  
echo "<td>" . nl2br( $row['concurrent_user']) . "</td>";  
echo "<td>" . nl2br( $row['auth_type']) . "</td>";  
echo "<td>" . nl2br( $row['reply_message']) . "</td>";  
echo "<td><a href=edit.php?id={$row['id']}>Edit</a></td><td><a href=duplicate.php?id={$row['id']}>Duplicate</a></td><td><a href=delete.php?id={$row['id']}>Delete</a></td> "; 
echo "</tr>"; 
} 
echo "</table>"; 
echo "<a href=new.php>New Group</a>"; 
?>
</div>
</body>
