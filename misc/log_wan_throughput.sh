#!/bin/bash

BASEDIR=/home/marsPortal
source $BASEDIR/config.txt

while true
do
	sleep 9.75

	START=`date +%s`
	
	TRAFFIC=`netstat -w 10  -q 1 -b -d -I $WAN_INTERFACE | tail -1`

	# abbove netstat reports in bytes per 10 seconds. so / 1000 / 10 to get kbytes/sec
	RX=$(echo $(echo $TRAFFIC | awk '{print $4}') / 10000 | bc)
	if [ -z "$RX" ]; then
		RX=-1
	fi
	TX=$(echo $(echo $TRAFFIC | awk '{print $7}') / 10000 | bc)
	if [ -z "$TX" ]; then
		TX=-1
	fi
	/usr/local/bin/mysql -u `echo $MYSQL_USER` -p`echo $MYSQL_PASSWD` radius <<EOF
INSERT INTO log_wan_throughput (at,rx,rx_unit,tx,tx_unit) VALUES
(FROM_UNIXTIME($START),$RX,"kbytes/sec",$TX,"kbytes/sec");
EOF
done
