<?php
// connect to db
$link = mysql_connect('localhost', 'radius', 'radpass');
if (!$link) {
    die('Not connected : ' . mysql_error());
}

if (! mysql_select_db('radius') ) {
    die ('Can\'t use foo : ' . mysql_error());
}
?>
