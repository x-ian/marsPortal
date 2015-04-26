<? 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
include('../config.php'); 
$groupname = $_GET['groupname']; 
mysql_query("DELETE FROM `radgroupcheck` WHERE `groupname` = '$groupname' ") ; 
mysql_query("DELETE FROM `radgroupreply` WHERE `groupname` = '$groupname' ") ; 
mysql_query("DELETE FROM `radusergroup` WHERE `groupname` = '$groupname' ") ; 
echo (mysql_affected_rows()) ? "Group deleted.<br /> " : "Nothing deleted.<br /> "; 
?> 

<a href='list.php'>Back To Listing</a>
</div>
</body>
