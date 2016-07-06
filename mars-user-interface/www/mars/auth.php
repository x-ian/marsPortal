<?php
	include 'config.php';
//if (false) {
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        header("WWW-Authenticate: Basic realm=\"marsPortal Admin\"");
        header("HTTP/1.0 401 Unauthorized");
        print "Sorry, browser doesn't seem to enforce authentication. Try again with another browser!\n";
        exit;
    } else {
        if (($_SERVER['PHP_AUTH_USER'] == $HTTP_AUTH_USER) && ($_SERVER['PHP_AUTH_PW'] == $HTTP_AUTH_PASSWD)) {
//            print "Welcome to the private area!";
        } else {
            header("WWW-Authenticate: Basic realm=\"marsPortal Admin\"");
            header("HTTP/1.0 401 Unauthorized");
            print "Sorry, invalid credentials. Access denied!\n";
            exit;
        }
    }
	//}
?>