#!/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt

# remove all CP sessions
/usr/local/bin/php -q $BASEDIR/misc/captiveportal-disconnect-all-users.php

# clean all accounting info
/home/marsPortal/misc/clear_accounting.sh

# wipe database
/usr/local/bin/mysql -u `echo $MYSQL_USER` -p`echo $MYSQL_PASSWD` radius <<EOF
truncate radcheck;
truncate radusergroup;
truncate userinfo;
EOF

# clean DHCP leases
rm -f /var/dhcpd/var/db/dhcpd.leases
