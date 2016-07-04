<?php 
	// javascript on previous page ensures that only and lways 2 macs are included
	$mac1 = $_POST['mac'][0]; 
	$mac2 = $_POST['mac'][1]; 

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

	$mac_source = query("select * from userinfo where username = '" . $mac1 . "';");
	$firstname = "";
	$lastname = "";
	$department = "";
	if ($row = mysql_fetch_assoc($mac_source)) {
		$firstname = $row['firstname'];
		$lastname = $row['lastname'];
		$department = $row['department'];
	}
	$mac_source_group = query("select * from radusergroup where username = '" . $mac1 . "'");
	$group = "";
	if ($row_group = mysql_fetch_assoc($mac_source_group)) {
		$group = $row_group['groupname'];
	}
		
	$update_mac_target = query(" UPDATE userinfo set firstname = '$firstname', lastname = '$lastname', department = '$department' where username = '$mac2' ");
	$update_mac_target_group = query(" UPDATE radusergroup set groupname = '$group' where username = '$mac2' ");
  
?>

<span style="font-variant:small-caps; font-size:200%">
	<table align="center">
		<tr><td><img src="../captiveportal-mars.jpg" /></td><td>Portal</td></tr>
	</table>
</span>

<hr/><br/>

<div align="center">

<br/>
	<p><b>Device entries merged. Back to listing <a href="<?php echo $redirurl; ?>"><?php echo $redirurl; ?></a></b></p>

</div>
