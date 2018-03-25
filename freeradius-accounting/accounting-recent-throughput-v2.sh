#!/usr/local/bin/bash

BASEDIR=`dirname $0`
source $BASEDIR/../config.txt

/usr/local/bin/mysql -u `echo $MYSQL_USER` -p`echo $MYSQL_PASSWD` radius <<EOF
-- if new day, wipe out everything and start from scratch
DELETE FROM throughput WHERE day < date_format(now(), '%Y-%m-%d');

INSERT INTO throughput (username, day, minute_of_day, time_of_day, offset_input, offset_output)
  SELECT * from 
    (SELECT DISTINCT(ra.username), 
      date_format(now(), '%Y-%m-%d'),
      ((hour(now()) * 60) + minute(now())), 
      curtime(), 
      SUM(ra.acctinputoctets) as input_octets, 
      SUM(ra.acctoutputoctets) as output_octets
    FROM radacct ra 
    WHERE UNIX_TIMESTAMP(ra.AcctStartTime) + ra.AcctSessionTime > UNIX_TIMESTAMP(date_format(now(), '%Y-%m-%d')) 
    GROUP BY ra.username) as ra 
EOF

