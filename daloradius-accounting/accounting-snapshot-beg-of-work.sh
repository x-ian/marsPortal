#!/bin/bash

BASEDIR=`dirname $0`
source $BASEDIR/../config.txt

/usr/bin/mysql -u `echo $DR_MYSQL_USER` -p`echo $DR_MYSQL_PASSWD` radius <<EOF
INSERT INTO daily_accounting (username, day, work_beg, inputoctets_work_beg, outputoctets_work_beg)
SELECT DISTINCT(radacct.username), date_format(now(), '%Y-%m-%d'), now(), SUM(radacct.acctinputoctets), SUM(radacct.acctoutputoctets)
FROM radacct 
WHERE UNIX_TIMESTAMP(AcctStartTime) + AcctSessionTime > UNIX_TIMESTAMP(date_format(now(), '%Y-%m-%d')) 
GROUP BY username
ON DUPLICATE KEY UPDATE username=VALUES(username), day=VALUES(day), work_beg=now(), inputoctets_work_beg=VALUES(inputoctets_work_beg), outputoctets_work_beg=VALUES(outputoctets_work_beg);
EOF
