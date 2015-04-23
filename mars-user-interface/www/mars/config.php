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
$link = mysql_connect('localhost', $MYSQL_USER, $MYSQL_PASSWD);
if (!$link) {
    die('Not connected : ' . mysql_error());
}

if (! mysql_select_db('radius') ) {
    die ('Can\'t use foo : ' . mysql_error());
}
?>
