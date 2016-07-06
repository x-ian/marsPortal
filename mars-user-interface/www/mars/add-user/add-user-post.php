<?php 
	include('../auth.php'); 

	$mac = $_POST['mac']; 
	$mac_vendor = $_POST['mac_vendor']; 
	$hostname = $_POST['hostname']; 
	$firstname = $_POST['firstname']; 
	$lastname = $_POST['lastname']; 
	$department = $_POST['department']; 
	$group= $_POST['group']; 
	$redirurl = $_POST['redirurl']; 
	
	$insert_userinfo =" INSERT INTO userinfo (username, firstname, lastname, email, department, organisation, initial_ip, hostname, registration_date, mac_vendor, notes) VALUES ('$mac', '$firstname', '$lastname', '', '$department', '', '', '$hostname', now(), '$mac_vendor', '')";	
	$insert_radcheck = "INSERT INTO radcheck (Username,Attribute,op,Value) VALUES ('$mac', 'Auth-Type', ':=', 'Accept')";
	$insert_group = " INSERT INTO radusergroup (UserName,GroupName,priority) VALUES ('$mac', '$group',0) ";
  
?>

<span style="font-variant:small-caps; font-size:200%">
	<table align="center">
		<tr><td><img src="../captiveportal-mars.jpg" /></td><td>Portal</td></tr>
	</table>
</span>

<hr/><br/>

<div align="center">

	<?php $result = mysql_query($insert_userinfo); ?>
	<?php $result = mysql_query($insert_radcheck); ?>
	<?php $result = mysql_query($insert_group); ?>

	<p><b>Device added. Try again to access <a href="<?php echo $redirurl; ?>"><?php echo $redirurl; ?></a></b></p>

</div>
