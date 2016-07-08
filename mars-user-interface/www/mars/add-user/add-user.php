<?php 
	include('../auth.php'); 

	$mac = $_GET['mac']; 
	$mac_vendor = $_GET['mac_vendor']; 
	$hostname = $_GET['hostname']; 
	
	$redirurl = $_GET['redirurl']; 

	$all_groups = mysql_query('select distinct groupname from radgroupreply union select distinct groupname from radgroupcheck order by groupname;');  
  
	$all_users = mysql_query('select firstname, lastname, department from userinfo group by firstname, lastname order by firstname ASC, lastname ASC;');  
  
?>

<script>

var d = {};
<?php
	// very stupid, very inefficient
	$all_users2 = mysql_query('select userinfo.firstname, userinfo.lastname, userinfo.department, radusergroup.groupname from userinfo, radusergroup where userinfo.username = radusergroup.username group by userinfo.firstname, userinfo.lastname order by userinfo.firstname ASC, userinfo.lastname ASC;');  
	while($r = mysql_fetch_assoc($all_users2)) {
		echo "d['" . $r['firstname'] . ' ' . $r['lastname']  . "'] = { firstname : '" . $r['firstname'] . "', lastname : '" . $r['lastname'] . "', department : '" . $r['department'] . "', group : '" . $r['groupname'] . "'};";
	}
?>

function onChangeUser() {
	if (document.getElementById('existinguser').value === '') {
		document.getElementById('firstname').readOnly = false;
		document.getElementById('lastname').readOnly = false;
		document.getElementById('department').readOnly = false;
		document.getElementById('group').disabled = false;
		document.getElementById('additional_mac').readOnly = false;
		document.getElementById('firstname').value = "";
		document.getElementById('lastname').value = "";
		document.getElementById('department').value = "";
		document.getElementById('group').value = "Users";
		document.getElementById('additional_mac').value = "";
	} else {
		document.getElementById('firstname').readOnly = true;
		document.getElementById('lastname').readOnly = true;
		document.getElementById('department').readOnly = true;
		document.getElementById('group').disabled = true;
		document.getElementById('firstname').value = d[document.getElementById('existinguser').value]['firstname'];
		document.getElementById('lastname').value = d[document.getElementById('existinguser').value]['lastname'];
		document.getElementById('department').value = d[document.getElementById('existinguser').value]['department'];
		document.getElementById('group').value = d[document.getElementById('existinguser').value]['group'];
		document.getElementById('additional_mac').value = "";
		document.getElementById('additional_mac').readOnly = true;
	}
}
</script>

<span style="font-variant:small-caps; font-size:200%">
	<table align="center">
		<tr><td><img src="../captiveportal-mars.jpg" /></td><td>Portal</td></tr>
	</table>
</span>

<hr/>

<div align="center">

	<p><b>Add new device entry to RADIUS server.</b></p>
	
	<form method="post" action="add-user-post.php" onsubmit="document.getElementById('group').disabled = false;">
		<input name="redirurl" value="<?php echo $redirurl; ?>" type="hidden"/>
		<table>
			<tr>
				<td>MAC Address:</td>
				<?php if (isset($mac)) { ?>
					<td><input name="mac" value="<?php echo $mac; ?>" type="text" readonly="true"/></td>
				<?php } else { ?>
					<td><input name="mac" type="text"/></td>
				<?php } ?>
			</tr>
			<tr>
				<td>Hostname:</td>
				<?php if (isset($hostname)) { ?>
					<td><input name="hostname" value="<?php echo $hostname; ?>" type="text" readonly="true"/></td>
				<?php } else { ?>
					<td><input name="hostname" type="text"/></td>
				<?php } ?>
			</tr>
			<tr>
				<td>MAC Vendor:</td>
				<?php if (isset($mac_vendor)) { ?>
					<td><input name="mac_vendor" value="<?php echo $mac_vendor; ?>" type="text" readonly="true"/></td>
				<?php } else { ?>
					<td><input name="mac_vendor" type="text"/></td>
				<?php } ?>				
			<tr>
				<td colspan="2"><hr/></td>
			</tr>
			<tr>
				<td>Existing user:</td>
				<td>
					<select name="existing_user" onchange="onChangeUser();" id="existinguser">
						<option value=''></option>
						<?php
							while ($row = mysql_fetch_assoc($all_users)) {
								$user = $row['firstname'] . " " . $row['lastname'];
								echo "<option value='" . $user . "'>" . $user . "</option>";
							}
						?>						
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2"><hr/></td>
			</tr>
			<tr>
				<td>Firstname:</td>
				<td><input name="firstname" type="text" id="firstname"/></td>
			</tr>
			<tr>
				<td>Lastname:</td>
				<td><input name="lastname" type="text" id="lastname"/></td>
			</tr>
			<tr>
				<td>Department:</td>
				<td><input name="department" type="text" id="department"/></td>
			</tr>
			<tr>
				<td>Group:</td>
				<td>
					<select name="group" id="group">
						<?php
							while ($row = mysql_fetch_assoc($all_groups)) {
								$groupname = $row['groupname'];
								if ($groupname == "Users") {
									echo "<option value=" . $groupname . " selected>" . $groupname . "</option>";
								} else {
									echo "<option value=" . $groupname . ">" . $groupname . "</option>";
								}
							}
						?>						
					</select>
				<td/>
			</tr>
			<tr>
				<td>Additional MAC Address:</td>
				<td><input name="additional_mac" type="text" id="additional_mac" pattern="^([0-9A-Fa-f]{2}[:]){5}([0-9A-Fa-f]{2})$"/> (xx:xx:xx:xx:xx:xx)</td>
			</tr>
			<tr>
				<td></td>
			</tr>
			<tr>
				<td />
				<td>
					<input name="accept" type="submit" value="Create device"/>
				</td>
			</tr>
		</table>
	</form>
</div>
