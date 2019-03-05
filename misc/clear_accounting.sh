#!/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt

# remove all CP sessions
/usr/local/bin/php -q $BASEDIR/misc/captiveportal-disconnect-all-users.php

# wipe database
/usr/local/bin/mysql -u `echo $MYSQL_USER` -p`echo $MYSQL_PASSWD` radius <<EOF
truncate accounting_snapshot_1;
truncate accounting_snapshot_2;
truncate accounting_snapshot_3;
truncate daily_accounting_v2;
truncate daily_accounting_v5;

truncate log_internet;
truncate log_internet_ping;
truncate log_wan_throughput;
truncate log_wan_traffic;

truncate radacct;
truncate throughput;
truncate radpostauth;

truncate traffic_details;
truncate ip_registry;
EOF
