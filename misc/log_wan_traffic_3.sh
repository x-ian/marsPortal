#!/bin/bash

BASEDIR=/home/marsPortal
source $BASEDIR/config.txt

/usr/local/bin/mysql -u `echo $MYSQL_USER` -p`echo $MYSQL_PASSWD` radius <<EOF
INSERT INTO log_wan_traffic (when2,tx,tx_unit,rx,rx_unit)
select now(), round(sum(t.input_octets) / 1000000), "MB", round(sum(t.output_octets)/1000000), "MB" from (SELECT * from (SELECT DISTINCT(ra.username),  date_format(now(), '%Y-%m-%d') as day,  SUM(ra.acctinputoctets) as input_octets,  SUM(ra.acctoutputoctets) as output_octets,  from_unixtime(max(UNIX_TIMESTAMP(ra.AcctStartTime) + ra.AcctSessionTime)) as last_acct_update FROM radacct ra  WHERE UNIX_TIMESTAMP(ra.AcctStartTime) + ra.AcctSessionTime > UNIX_TIMESTAMP(date_format(now(), '%Y-%m-%d'))  GROUP BY ra.username) as ra) as t;
EOF

