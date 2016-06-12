<?php 
	$mac = $_POST['mac']; 
	$mac_vendor = $_POST['mac_vendor']; 
	$hostname = $_POST['hostname']; 
	$firstname = $_POST['firstname']; 
	$lastname = $_POST['lastname']; 
	$department = $_POST['department']; 
	$group= $_POST['group']; 
	$redirurl = $_POST['redirurl']; 
	
        require '/home/marsPortal/config.php';
        mysql_connect('localhost',$user,$pw) or die('Could not connect to mysql server.');
	mysql_select_db('radius');

	function query($query) {
	  $result = mysql_query($query);
		if (!$result) {
			$message  = 'Invalid query: ' . mysql_error() . "\n";
			$message .= 'Full query: ' . $query;
		  	die($message);
		} 
		return $result;
	}

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

	<?php $result = query($insert_userinfo); ?>
	<?php $result = query($insert_radcheck); ?>
	<?php $result = query($insert_group); ?>

	<p><b>Device added. Try again to access <a href="<?php echo $redirurl; ?>"><?php echo $redirurl; ?></a></b></p>

</div>
