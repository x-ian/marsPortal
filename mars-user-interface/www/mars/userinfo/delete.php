<? 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
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
echo (mysql_affected_rows()) ? "Device including accounting history deleted.<br /> " : "Nothing deleted.<br /> "; 
exec("/usr/local/bin/php -q /home/marsPortal/misc/captiveportal-disconnect-user.php  " . $username, $out, $exit);
?> 
<b>Note: Deleting a device will automatically close its session on the Captive Portal.</b>

<a href='list.php'>Back To Listing</a>

</div>
</body>
