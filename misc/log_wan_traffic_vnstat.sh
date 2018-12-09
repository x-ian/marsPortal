#!/bin/bash

BASEDIR=/home/marsPortal
source $BASEDIR/config.txt

LOG=/tmp/log_wan_traffic.log

	/usr/local/bin/vnstat -i igb0 -s > $LOG
	
	TX=$(grep "today" $LOG | awk '{print $5}')
	if [ -z "$TX" ]; then
		TX=-1
	fi
	TX_UNIT=$(grep "today" $LOG | awk '{print $6}')
	RX=$(grep "today" $LOG | awk '{print $2}')
	if [ -z "$RX" ]; then
		RX=-1
	fi
	RX_UNIT=$(grep "today" $LOG | awk '{print $3}')

	START=`date +%s`

	/usr/local/bin/mysql -u `echo $MYSQL_USER` -p`echo $MYSQL_PASSWD` radius <<EOF
INSERT INTO log_wan_traffic (when2,rx,rx_unit,tx,tx_unit) VALUES
(FROM_UNIXTIME($START),$RX,"$RX_UNIT",$TX,"$TX_UNIT");
EOF

