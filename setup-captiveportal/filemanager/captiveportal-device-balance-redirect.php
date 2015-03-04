<html>
<head>
	<?php
		$ip=$_SERVER['REMOTE_ADDR'];
		exec("/home/marsPortal/daloradius-integration/echo-user-data-statistics-url.sh " . $ip, $output, $exitCode);
	?>
	
	<meta http-equiv="Refresh" content="0; url=<?php echo implode(" ", $output);?>" />
</head>
<body>
<span style="font-variant:small-caps; font-size:200%">
	<table align="center">
		<tr><td><img src="/captiveportal-mars.jpg" /></td><td>Portal</td></tr>
	</table>
</span> 

<hr/><br/>

<div align="center">
	<?php
		exec("/home/marsPortal/daloradius-integration/echo-user-data-statistics-link.sh " . $ip, $output, $exitCode);				
   	 echo implode(" ", $output);
	?>
</div>
</body>
</html>