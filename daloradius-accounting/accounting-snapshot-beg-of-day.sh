#!/bin/bash

BASEDIR=`dirname $0`
source $BASEDIR/../config.txt

# make sure every session is properly closed to start basically from 0 for up- and downloads
#$BASEDIR/../misc/captiveportal-disconnect-all-users.sh

/usr/bin/mysql -u `echo $DR_MYSQL_USER` -p`echo $DR_MYSQL_PASSWD` radius <<EOF
INSERT INTO daily_accounting (username, day, day_beg, inputoctets_day_beg, outputoctets_day_beg)
SELECT DISTINCT(radacct.username), date_format(now(), '%Y-%m-%d'), now(), SUM(radacct.acctinputoctets), SUM(radacct.acctoutputoctets)
FROM radacct 
WHERE UNIX_TIMESTAMP(AcctStartTime) + AcctSessionTime > UNIX_TIMESTAMP(date_format(now(), '%Y-%m-%d')) 
GROUP BY username;
EOF
