<?php 
	$mac = $_GET['mac']; 
	$redirurl = $_GET['redirurl']; 
	
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

	$all_groups = mysql_query('select distinct groupname from radgroupreply union select distinct groupname from radgroupcheck order by groupname;');  
  
?>

<span style="font-variant:small-caps; font-size:200%">
	<table align="center">
		<tr><td><img src="../captiveportal-mars.jpg" /></td><td>Portal</td></tr>
	</table>
</span>

<hr/>

<div align="center">

	<p><b>Add new device entry to RADIUS server.</b></p>
	
	<form method="post" action="add-user-post.php">
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
				<td>Firstname:</td>
				<td><input name="firstname" type="text"/></td>
			</tr>
			<tr>
				<td>Lastname:</td>
				<td><input name="lastname" type="text"/></td>
			</tr>
			<tr>
				<td>Department:</td>
				<td><input name="department" type="text"/></td>
			</tr>
			<tr>
				<td>Group:</td>
				<td>
					<select name="group">
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
				<td/><td/>
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