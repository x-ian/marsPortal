<?php 
	$mac = $_POST['mac']; 
	$firstname = $_POST['firstname']; 
	$lastname = $_POST['lastname']; 
	$department = $_POST['department']; 
	$group= $_POST['group']; 
	$redirurl = $_POST['redirurl']; 
	
	mysql_connect('localhost','radius','radius') or die('Could not connect to mysql server.');
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

	$insert_userinfo =" INSERT INTO userinfo (username, firstname, lastname, email, department, company, workphone, homephone, mobilephone, address, city, state, country, zip, notes, changeuserinfo, portalloginpassword, enableportallogin, creationdate, creationby, updatedate, updateby) VALUES ('$mac', '$firstname', '$lastname', '', '$department', '', '', '', '', '', '', '', '', '', '', '0', '', '0', '2015-03-03 08:01:14', 'administrator', NULL, NULL)";	
	$insert_radcheck = "INSERT INTO radcheck (Username,Attribute,op,Value) VALUES ('$mac', 'Auth-Type', ':=', 'Accept')";
	$insert_billinfo = "INSERT INTO userbillinfo (username, contactperson, company, email, phone, address, city, state, country, zip, paymentmethod, cash, creditcardname, creditcardnumber, creditcardverification, creditcardtype, creditcardexp, notes, changeuserbillinfo, lead, coupon, ordertaker, billstatus, lastbill, nextbill, nextinvoicedue, billdue, postalinvoice, faxinvoice, emailinvoice, creationdate, creationby, updatedate, updateby) VALUES ('$mac', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', '', '', '', '', '', '', '', '2015-03-03 08:01:14', 'administrator', NULL, NULL)";
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
	<?php $result = query($insert_billinfo); ?>
	<?php $result = query($insert_group); ?>

	<p><b>Device added. Try again to access <a href="<?php echo $redirurl; ?>"><?php echo $redirurl; ?></a></b></p>

</div>