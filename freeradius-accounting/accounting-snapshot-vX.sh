#!/usr/local/bin/bash

BASEDIR=`dirname $0`
source /home/marsPortal/config.txt

/usr/local/bin/mysql -u `echo $MYSQL_USER` -p`echo $MYSQL_PASSWD` radius <<EOF
-- find and set all entries that have not yet set the offsets for today
INSERT INTO daily_accounting_vX (username, day, offset_input, offset_output, offset)
  SELECT * from 
    (SELECT DISTINCT(ra.username), 
      date_format(now(), '%Y-%m-%d') as day, 
      SUM(ra.acctinputoctets) as input_octets, 
      SUM(ra.acctoutputoctets) as output_octets, 
      from_unixtime(max(UNIX_TIMESTAMP(ra.AcctStartTime) + ra.AcctSessionTime)) as last_acct_update
    FROM radacct ra 
    WHERE UNIX_TIMESTAMP(ra.AcctStartTime) + ra.AcctSessionTime > UNIX_TIMESTAMP(date_format(now(), '%Y-%m-%d')) 
    GROUP BY ra.username) as ra 
  WHERE ra.username not in 
    (select ra.username from daily_accounting_vX where day = date_format(now(), '%Y-%m-%d'))
EOF

update_accounting() {
	NEXT_INTERVAL_COLUMN=$1
	
	/usr/local/bin/mysql -u `echo $MYSQL_USER` -p`echo $MYSQL_PASSWD` radius <<EOF
UPDATE daily_accounting_vX da INNER JOIN 
  (SELECT DISTINCT(ra.username),
    date_format(now(), '%Y-%m-%d') as day,
    SUM(ra.acctinputoctets) as input_octets,
    SUM(ra.acctoutputoctets) as output_octets
  FROM radacct ra
  WHERE UNIX_TIMESTAMP(ra.AcctStartTime) + ra.AcctSessionTime > UNIX_TIMESTAMP(date_format(now(), '%Y-%m-%d'))
  GROUP BY ra.username) as ra ON ra.username = da.username
SET `echo $NEXT_INTERVAL_COLUMN`_input = ra.input_octets, `echo $NEXT_INTERVAL_COLUMN`_output = ra.output_octets
WHERE da.day = date_format(now(), '%Y-%m-%d');
EOF
}

# current time and day in week
CLOSEST_HALF_HOUR_TMP=`echo "$(date "+%s") - ($(date +%s)%1800)" | bc`
CLOSEST_HALF_HOUR_TMP2=`echo "$CLOSEST_HALF_HOUR_TMP + 1800 - 60" | bc`
CLOSEST_HALF_HOUR=`date -r $CLOSEST_HALF_HOUR_TMP2 "+%H%M"`

update_accounting $CLOSEST_HALF_HOUR
