#!/usr/local/bin/bash

BASEDIR=`dirname $0`
source /home/marsPortal/config.txt

/usr/local/bin/mysql -u `echo $MYSQL_USER` -p`echo $MYSQL_PASSWD` radius <<EOF
-- find and set all entries that have not yet set the offsets for today
INSERT INTO daily_accounting_v5 (username, day, offset_input, offset_output, offset)
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
    (select username from daily_accounting_v5 where day = date_format(now(), '%Y-%m-%d'))
EOF

update_accounting() {
	NEXT_INTERVAL_COLUMN=$2
	PREVIOUS_INTERVAL_COLUMN=$1

	cat <<EOF
UPDATE daily_accounting_v5 da INNER JOIN 
  (SELECT DISTINCT(ra.username),
    date_format(now(), '%Y-%m-%d') as day,
    SUM(ra.acctinputoctets) as input_octets,
    SUM(ra.acctoutputoctets) as output_octets
  FROM radacct ra
  WHERE UNIX_TIMESTAMP(ra.AcctStartTime) + ra.AcctSessionTime > UNIX_TIMESTAMP(date_format(now(), '%Y-%m-%d'))
  GROUP BY ra.username) as ra ON ra.username = da.username
SET `echo $NEXT_INTERVAL_COLUMN`_input = (ra.input_octets - offset_input - (0029_input+0059_input+0129_input+0159_input+0229_input+0259_input+0329_input+0359_input+0429_input+0459_input+0529_input+0559_input+0629_input+0659_input+0729_input+0759_input+0829_input+0859_input+0929_input+0959_input+1029_input+1059_input+1129_input+1159_input+1229_input+1259_input+1329_input+1359_input+1429_input+1459_input+1529_input+1559_input+1629_input+1659_input+1729_input+1759_input+1829_input+1859_input+1929_input+1959_input+2029_input+2059_input+2129_input+2159_input+2229_input+2259_input+2329_input+2359_input) + `echo $NEXT_INTERVAL_COLUMN`_input), `echo $NEXT_INTERVAL_COLUMN`_output = (ra.output_octets - offset_output - (0029_output+0059_output+0129_output+0159_output+0229_output+0259_output+0329_output+0359_output+0429_output+0459_output+0529_output+0559_output+0629_output+0659_output+0729_output+0759_output+0829_output+0859_output+0929_output+0959_output+1029_output+1059_output+1129_output+1159_output+1229_output+1259_output+1329_output+1359_output+1429_output+1459_output+1529_output+1559_output+1629_output+1659_output+1729_output+1759_output+1829_output+1859_output+1929_output+1959_output+2029_output+2059_output+2129_output+2159_output+2229_output+2259_output+2329_output+2359_output) + `echo $NEXT_INTERVAL_COLUMN`_output)
WHERE da.day = date_format(now(), '%Y-%m-%d')  AND da.username = ra.username;
EOF

	/usr/local/bin/mysql -u `echo $MYSQL_USER` -p`echo $MYSQL_PASSWD` radius <<EOF
UPDATE daily_accounting_v5 da INNER JOIN 
  (SELECT DISTINCT(ra.username),
    date_format(now(), '%Y-%m-%d') as day,
    SUM(ra.acctinputoctets) as input_octets,
    SUM(ra.acctoutputoctets) as output_octets
  FROM radacct ra
  WHERE UNIX_TIMESTAMP(ra.AcctStartTime) + ra.AcctSessionTime > UNIX_TIMESTAMP(date_format(now(), '%Y-%m-%d'))
  GROUP BY ra.username) as ra ON ra.username = da.username
SET `echo $NEXT_INTERVAL_COLUMN`_input = (ra.input_octets - offset_input - (0029_input+0059_input+0129_input+0159_input+0229_input+0259_input+0329_input+0359_input+0429_input+0459_input+0529_input+0559_input+0629_input+0659_input+0729_input+0759_input+0829_input+0859_input+0929_input+0959_input+1029_input+1059_input+1129_input+1159_input+1229_input+1259_input+1329_input+1359_input+1429_input+1459_input+1529_input+1559_input+1629_input+1659_input+1729_input+1759_input+1829_input+1859_input+1929_input+1959_input+2029_input+2059_input+2129_input+2159_input+2229_input+2259_input+2329_input+2359_input) + `echo $NEXT_INTERVAL_COLUMN`_input), `echo $NEXT_INTERVAL_COLUMN`_output = (ra.output_octets - offset_output - (0029_output+0059_output+0129_output+0159_output+0229_output+0259_output+0329_output+0359_output+0429_output+0459_output+0529_output+0559_output+0629_output+0659_output+0729_output+0759_output+0829_output+0859_output+0929_output+0959_output+1029_output+1059_output+1129_output+1159_output+1229_output+1259_output+1329_output+1359_output+1429_output+1459_output+1529_output+1559_output+1629_output+1659_output+1729_output+1759_output+1829_output+1859_output+1929_output+1959_output+2029_output+2059_output+2129_output+2159_output+2229_output+2259_output+2329_output+2359_output) + `echo $NEXT_INTERVAL_COLUMN`_output)
WHERE da.day = date_format(now(), '%Y-%m-%d')  AND da.username = ra.username;
EOF
}

# current time and day in week
CLOSEST_HALF_HOUR_TMP=`echo "$(date "+%s") - ($(date +%s)%1800) - 60" | /usr/bin/bc`
CLOSEST_HALF_HOUR_DOWN=`date -r $CLOSEST_HALF_HOUR_TMP "+%H%M"`
CLOSEST_HALF_HOUR_TMP1=`echo "$(date "+%s") - ($(date +%s)%1800)" | /usr/bin/bc`
CLOSEST_HALF_HOUR_TMP2=`echo "$CLOSEST_HALF_HOUR_TMP1 + 1800 - 60" | /usr/bin/bc`
CLOSEST_HALF_HOUR_UP=`date -r $CLOSEST_HALF_HOUR_TMP2 "+%H%M"`

update_accounting $CLOSEST_HALF_HOUR_DOWN $CLOSEST_HALF_HOUR_UP


