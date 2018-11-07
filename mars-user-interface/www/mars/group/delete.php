<? 
include '../common.php'; 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
$groupname = $_GET['groupname']; 
mysqli_query($link, "DELETE FROM radgroupcheck WHERE groupname = '" . $groupname . "' ") or die(mysqli_error());
mysqli_query($link, "DELETE FROM radgroupreply WHERE groupname = '" . $groupname . "' ") or die(mysqli_error());
mysqli_query($link, "DELETE FROM radusergroup WHERE groupname = '" . $groupname . "' ") or die(mysqli_error());
?> 

<a href='list.php'>Back To Listing</a>
</div>
</body>
