#!/bin/bash

BASEDIR=`dirname $0`
source /home/marsPortal/config.txt

if [ "$1" == "day_offset" ]; then
	TIME_COL=day_offset
	INPUT_COL=day_offset_input
	OUTPUT_COL=day_offset_output
elif [ "$1" == "work_offset" ]; then
	TIME_COL=work_offset
	INPUT_COL=work_offset_input
	OUTPUT_COL=work_offset_output
elif [ "$1" == "lunch_offset" ]; then
	TIME_COL=lunch_offset
	INPUT_COL=lunch_offset_input
	OUTPUT_COL=lunch_offset_output
elif [ "$1" == "lunch_total" ]; then
	TIME_COL=lunch_total
	INPUT_COL=lunch_total_input
	OUTPUT_COL=lunch_total_output
elif [ "$1" == "work_total" ]; then
	TIME_COL=work_total
	INPUT_COL=work_total_input
	OUTPUT_COL=work_total_output
elif [ "$1" == "day_total" ]; then
	TIME_COL=day_total
	INPUT_COL=day_total_input
	OUTPUT_COL=day_total_output
else
	echo "Unknown value for parameter #1 ($1). Exiting."
	exit 1
fi

/usr/bin/mysql -u `echo $DR_MYSQL_USER` -p`echo $DR_MYSQL_PASSWD` radius <<EOF
INSERT INTO daily_accounting2 (username, day, $TIME_COL, $INPUT_COL, $OUTPUT_COL)
SELECT DISTINCT(radacct.username), date_format(now(), '%Y-%m-%d'), now(), SUM(radacct.acctinputoctets), SUM(radacct.acctoutputoctets)
  FROM radacct 
  WHERE UNIX_TIMESTAMP(AcctStartTime) + AcctSessionTime > UNIX_TIMESTAMP(date_format(now(), '%Y-%m-%d')) 
  GROUP BY username
  ON DUPLICATE KEY UPDATE username=VALUES(username), day=VALUES(day), $TIME_COL=now(), $INPUT_COL=VALUES($INPUT_COL), $OUTPUT_COL=VALUES($OUTPUT_COL);
EOF
