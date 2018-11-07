<? 
include '../menu.php'; 
include '../common.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
$username = $_GET['username']; 
$ip=$_SERVER['REMOTE_ADDR'];
mysqli_query($link, "DELETE FROM `radusergroup` WHERE `username` = '$username' ") ; 
mysqli_query($link, "DELETE FROM `radcheck` WHERE `username` = '$username' ") ; 
mysqli_query($link, "DELETE FROM `radacct` WHERE `username` = '$username' ") ; 
mysqli_query($link, "DELETE FROM `radreply` WHERE `username` = '$username' ") ; 
mysqli_query($link, "DELETE FROM `radpostauth` WHERE `username` = '$username' ") ; 
mysqli_query($link, "DELETE FROM `daily_accounting_v5` WHERE `username` = '$username' ") ; 
mysqli_query($link, "DELETE FROM `throughput` WHERE `username` = '$username' ") ; 
mysqli_query($link, "DELETE FROM `userinfo` WHERE `username` = '$username' ") ; 
echo (mysqli_affected_rows()) ? "Device including accounting history deleted.<br /> " : "Nothing deleted.<br /> "; 
exec("/usr/local/bin/php -q /home/marsPortal/misc/captiveportal-disconnect-user.php  " . $username, $out, $exit);
?> 
<b>Note: Deleting a device will automatically close its session on the Captive Portal.</b>

<a href='list.php'>Back To Listing</a>

</div>
</body>
