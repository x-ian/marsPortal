<? 
include '/mars/menu.php'; 
?>

<? 
include('/mars/config.php'); 
$id = (int) $_GET['id']; 
mysql_query("DELETE FROM `userinfo` WHERE `id` = '$id' ") ; 
echo (mysql_affected_rows()) ? "Row deleted.<br /> " : "Nothing deleted.<br /> "; 
?> 

<a href='list.php'>Back To Listing</a>

