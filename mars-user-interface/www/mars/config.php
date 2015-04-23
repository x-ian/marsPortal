<?php
// parse marsPortal config file
$fh=fopen("/home/marsPortal/config.txt", "r");
while ($line=fgets($fh, 80)) {
  if (preg_match('/^[a-z]+=("[a-z0-9]+"|[0-9]+)$/', $line)) {
    $line_a=explode("=", $line);
    $line_a[0]=$line_a[1];
  }
}

// connect to db
$link = mysql_connect('localhost', $MYSQL_USER, $MYSQL_PASS);
if (!$link) {
    die('Not connected : ' . mysql_error());
}

if (! mysql_select_db('radius') ) {
    die ('Can\'t use foo : ' . mysql_error());
}
?>
