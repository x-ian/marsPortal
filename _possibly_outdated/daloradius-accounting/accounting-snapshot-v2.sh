#!/usr/local/bin/bash

BASEDIR=`dirname $0`
source /home/marsPortal/config.txt

# current time and day in week
NOW=`date +%-H%M`
NOW_DAY=`date +%u`

DAY_OFFSET_ALREADY_SET=$(/usr/local/bin/mysql -u $MYSQL_USER -p$MYSQL_PASSWD radius -se 'select id from daily_accounting_v2 where day_offset is not null and day=current_date();' | wc -l)
WORK_OFFSET_ALREADY_SET=$(/usr/local/bin/mysql -u $MYSQL_USER -p$MYSQL_PASSWD radius -se 'select id from daily_accounting_v2 where work_offset is not null and day=current_date();' | wc -l)
LUNCH_OFFSET_ALREADY_SET=$(/usr/local/bin/mysql -u $MYSQL_USER -p$MYSQL_PASSWD radius -se 'select id from daily_accounting_v2 where lunch_offset is not null and day=current_date();' | wc -l)

update_accounting() {
	TIME_COL=$1
	INPUT_COL=$2
	OUTPUT_COL=$3
	
	/usr/local/bin/mysql -u `echo $MYSQL_USER` -p`echo $MYSQL_PASSWD` radius <<EOF
INSERT INTO daily_accounting_v2 (username, day, $TIME_COL, $INPUT_COL, $OUTPUT_COL)
SELECT DISTINCT(radacct.username), date_format(now(), '%Y-%m-%d'), now(), SUM(radacct.acctinputoctets), SUM(radacct.acctoutputoctets)
  FROM radacct 
  WHERE UNIX_TIMESTAMP(AcctStartTime) + AcctSessionTime > UNIX_TIMESTAMP(date_format(now(), '%Y-%m-%d')) 
  GROUP BY username

  ON DUPLICATE KEY UPDATE username=VALUES(username), day=VALUES(day), $TIME_COL=now(), $INPUT_COL=VALUES($INPUT_COL), $OUTPUT_COL=VALUES($OUTPUT_COL);
EOF
}

if [ "$DAY_START" -le "$NOW" ] && [ "$DAY_OFFSET_ALREADY_SET" -eq "0" ]; then
	update_accounting "day_offset" "day_offset_input" "day_offset_output"
fi
if [[ $WORK_DAYS == *"$NOW_DAY"* ]] && [ "$WORK_START" -le "$NOW" ] && [ "$WORK_OFFSET_ALREADY_SET" -eq "0" ]; then
	update_accounting "work_offset" "work_offset_input" "work_offset_output"
fi
if [[ $WORK_DAYS == *"$NOW_DAY"* ]] && [ "$LUNCH_START" -le "$NOW" ] && [ "$LUNCH_OFFSET_ALREADY_SET" -eq "0" ]; then
	update_accounting "lunch_offset" "lunch_offset_input" "lunch_offset_output"
fi
if [ "$DAY_OFFSET_ALREADY_SET" -ne "0" ]; then
	update_accounting "day_total" "day_total_input" "day_total_output"
fi
if [[ $WORK_DAYS == *"$NOW_DAY"* ]] && [ "$WORK_OFFSET_ALREADY_SET" -ne "0" ] && [ "$WORK_END" -ge "$NOW" ]; then # (&& not lunchtime?)
	update_accounting "work_total" "work_total_input" "work_total_output"
fi
if [[ $WORK_DAYS == *"$NOW_DAY"* ]] && [ "$LUNCH_OFFSET_ALREADY_SET" -ne "0" ] && [ "$LUNCH_END" -ge "$NOW" ]; then
	update_accounting "lunch_total" "lunch_total_input" "lunch_total_output"
fi

