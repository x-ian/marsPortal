<? 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
include('../config.php'); 
$username = $_GET['username']; 
$ip=$_SERVER['REMOTE_ADDR'];
mysql_query("DELETE FROM `radusergroup` WHERE `username` = '$username' ") ; 
mysql_query("DELETE FROM `radcheck` WHERE `username` = '$username' ") ; 
mysql_query("DELETE FROM `radacct` WHERE `username` = '$username' ") ; 
mysql_query("DELETE FROM `radreply` WHERE `username` = '$username' ") ; 
mysql_query("DELETE FROM `radpostauth` WHERE `username` = '$username' ") ; 
mysql_query("DELETE FROM `daily_accounting_v2` WHERE `username` = '$username' ") ; 
mysql_query("DELETE FROM `accounting_snapshot` WHERE `username` = '$username' ") ; 
mysql_query("DELETE FROM `userinfo` WHERE `username` = '$username' ") ; 
echo (mysql_affected_rows()) ? "User/device including accounting history deleted.<br /> " : "Nothing deleted.<br /> "; 
exec("/home/marsPortal/misc/captiveportal-disconnect-user-with-ip.sh " . $ip, $out, $exit);
?> 
<b>Note: Deleting a user will automatically close the session of this user on the Captive Portal.</b>

<a href='list.php'>Back To Listing</a>

</div>
</body>
