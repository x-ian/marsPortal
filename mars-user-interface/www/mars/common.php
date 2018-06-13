<? 

include('config.php'); 

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header("WWW-Authenticate: Basic realm=\"marsPortal Admin\"");
    header("HTTP/1.0 401 Unauthorized");
    print "Sorry, invalid credentials. Access denied! Reload page or close&reopen browser to try again.\n";
    exit;
} else {
    if (($_SERVER['PHP_AUTH_USER'] == $HTTP_AUTH_USER) && ($_SERVER['PHP_AUTH_PW'] == $HTTP_AUTH_PASSWD)) {
//            print "Welcome to the private area!";
    } else {
        header("WWW-Authenticate: Basic realm=\"marsPortal Admin\"");
        header("HTTP/1.0 401 Unauthorized");
        print "Sorry, invalid credentials. Access denied! Reload page or close&reopen browser to try again.\n";
        exit;
    }
}

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past to bypass browser caching

function dropdown_link_to_device($username) {
	
	$query = "SELECT radusergroup.groupname as groupname, userinfo.* from userinfo
LEFT JOIN radusergroup ON userinfo.username=radusergroup.username 
WHERE userinfo.username='" . $username . "';";

	$result = mysql_query($query) or trigger_error(mysql_error()); 

	while($row = mysql_fetch_array($result)){ 
		foreach($row AS $key => $value) { $row[$key] = stripslashes($value); }
		$name = "";
		if ($row['firstname'] !== '') {
			$name .= $row['firstname'] . " ";
		}
		if ($row['lastname'] !== '') {
			$name .= $row['lastname'] . " ";
		}
		if ($row['hostname'] !== '') {
			$name .= '(' . $row['hostname'] . ')';
		}
		
		return "<div class='dropdown'>
	  <a class='dropdown-toggle' type='button' id='dropdownMenu1' data-toggle='dropdown'>
	    {$name}
	    <span class='caret'></span>
	  </a>
	  <ul class='dropdown-menu' role='menu' aria-labelledby='dropdownMenu1'>
	    <li role='presentation'><a role='menuitem' tabindex='-1' href='/mars/device_with_volume.php?username={$row[username]}'>Traffic history</a></li>
	    <li role='presentation'><a role='menuitem' tabindex='-1' href='/mars/reports/device-activity.php?username={$row[username]}'>Activity history</a></li>
	    <li role='presentation' class='divider'></li>
	    <li role='presentation'><a role='menuitem' tabindex='-1' href='/mars/userinfo/edit.php?username=${row[username]}'>Edit device</a></li>
	    <li role='presentation' class='divider'></li>
	    <li role='presentation'>Username: {$row[firstname]} ${row[lastname]}</li>
	    <li role='presentation'>Hostname: {$row[hostname]}</li>
	    <li role='presentation'>Group: {$row[groupname]}</li>
	    <li role='presentation'>MAC Address: {$row[username]}</li>
	    <li role='presentation'>MAC Vendor: {$row[mac_vendor]}</li>
	  </ul>
	</div>";
	}
}

function link_to_device($row) {
	$name = "";
	if ($row['firstname'] !== '') {
		$name .= $row['firstname'] . " ";
	}
	if ($row['lastname'] !== '') {
		$name .= $row['lastname'] . " ";
	}
	if ($row['hostname'] !== '') {
		$name .= '(' . $row['hostname'] . ')';
	}
	$hoover = "";
	if ($row['groupname'] !== '') {
		$hoover .= $row['groupname'] . " - ";
	}
	if ($row['mac_vendor'] !== '') {
		$hoover .= $row['mac_vendor'];
	}
    return '<a href="/mars/userinfo/edit.php?username=' . $row['username'] . '" data-html="true" data-toggle="tooltip" title="' . $hoover . '">' . $name . '</a>';
}

function query($query) {
  $result = mysql_query($query);
	if (!$result) {
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Full query: ' . $query;
	  	die($message);
	} 
	return $result;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <link href="/mars/application.css" rel="stylesheet" type="text/css" />
  <title>marsPortal - <? echo $HEADLINE ?></title>
  <script src="/mars/application.js"></script>
<script>
$(document).ready(function() {
   $(".nav li.disabled a").click(function() {
     return false;
   });
});
</script>

</head>

<body class="visualize_text">
