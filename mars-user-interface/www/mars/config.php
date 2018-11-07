<?php
// parse marsPortal config file
$fh=fopen("/home/marsPortal/config.txt", "r");
while ($line=fgets($fh, 80)) {
  if (!preg_match('/^#/', $line) && preg_match('/=/', $line)) {
    $line_a=explode("=", $line);
	$param = preg_replace( "/\r|\n/", "", $line_a[0] );
	$value = preg_replace( "/\r|\n/", "", $line_a[1] );
    ${$param}=$value;
  }
}

// connect to db
$link = mysqli_connect('localhost', $MYSQL_USER, $MYSQL_PASSWD);
global $link;
if (!$link) {
    die('Not connected to MySQL DB with error: ' . mysqli_error());
}

if (! mysqli_select_db($link, 'radius') ) {
    die ('Can\'t use MySQL instance radius with error: ' . mysqli_error());
}
?>
