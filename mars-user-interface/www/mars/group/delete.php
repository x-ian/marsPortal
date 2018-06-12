<? 
include '../common.php'; 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
$groupname = $_GET['groupname']; 
mysql_query("DELETE FROM radgroupcheck WHERE groupname = '" . $groupname . "' ") or die(mysql_error());
mysql_query("DELETE FROM radgroupreply WHERE groupname = '" . $groupname . "' ") or die(mysql_error());
mysql_query("DELETE FROM radusergroup WHERE groupname = '" . $groupname . "' ") or die(mysql_error());
?> 

<a href='list.php'>Back To Listing</a>
</div>
</body>
