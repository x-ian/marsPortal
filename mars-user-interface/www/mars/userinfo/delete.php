<? 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
include('../config.php'); 
$username = (int) $_GET['username']; 
mysql_query("DELETE FROM `userinfo` WHERE `username` = '$username' ") ; 
echo (mysql_affected_rows()) ? "Row deleted.<br /> " : "Nothing deleted.<br /> "; 
?> 

<a href='list.php'>Back To Listing</a>

</div>
</body>
