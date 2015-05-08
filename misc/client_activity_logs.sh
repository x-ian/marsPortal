#!/usr/local/bin/bash

# summary of client connectivity based on client_activity_log 

cd /home/client_activities_log

collect_stats() {
	PATTERN=$1
	FILES=$2
	MATCHES=`grep -h "$PATTERN" $FILES | awk -F" - " '{ print $1 }' | sort | uniq | wc -l`
	echo $MATCHES
	return $MATCHES
}

# possible statuses
# x - newly registered
# 2 - Too many users
# 3 - Data volume reached
# 4 - Device permanently disabled
# 5 - Data bundle during business hours exceeded
# 6 - Access rejected with message
# 9 - RADIUS offline or unknown response

# relevant files
TODAY=status-`date +%Y%m%d`.log
YESTERDAY=status-`date -v -1d +%Y%m%d`.log
LAST_7_DAYS="$TODAY $YESTERDAY status-`date -v -2d +%Y%m%d`.log status-`date -v -3d +%Y%m%d`.log status-`date -v -4d +%Y%m%d`.log status-`date -v -5d +%Y%m%d`.log status-`date -v -6d +%Y%m%d`.log"
LAST_30_DAYS=$LAST_7_DAYS

print_stats_line() {
	STATUS_PATTERN=$1
	STATUS_DESCRIPTION=$2
	cat <<EOF
"<tr><td>$STATUS_DESCRIPTION</td><td>$(collect_stats "$STATUS_PATTERN" "$TODAY")</td><td>$(collect_stats "$STATUS_PATTERN" "$YESTERDAY")</td><td>$(collect_stats "$STATUS_PATTERN" "$LAST_7_DAYS")</td><td>$(collect_stats "$STATUS_PATTERN" "$LAST_30_DAYS")</td></tr>"
EOF
}

print_stats_line "\- x \-" "Registered"
print_stats_line "\- 2 \-" "Too many users per group"
print_stats_line "\- 3 \-" "Data volume reached"
print_stats_line "\- 4 \-" "Disabled"
print_stats_line "\- 5 \-" "Data volume during work reached"
print_stats_line "\- 6 \-" "Genernic network access rejected"
print_stats_line "\- 9 \-" "RADIUS offline"
echo "</table>"
