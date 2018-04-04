#!/bin/bash

BASEDIR=/home/marsPortal
source $BASEDIR/config.txt

LOG=/tmp/log_wan_traffic_2.log

/usr/bin/netstat -i -b -n -I igb0 > $LOG
#/usr/bin/netstat -i -b -n -I bridge0 >> $LOG

TX=$(grep "igb0" $LOG | grep "Link" | awk '{print $11}')
if [ -z "$TX" ]; then
	TX=-1
fi
TX_UNIT="bytes" #$(grep "today" $LOG | awk '{print $6}')
RX=$(grep "igb0" $LOG | grep "Link" | awk '{print $8}')
if [ -z "$RX" ]; then
	RX=-1
fi
RX_UNIT="bytes" #$(grep "today" $LOG | awk '{print $3}')

START=`date +%s`

/usr/local/bin/mysql -u `echo $MYSQL_USER` -p`echo $MYSQL_PASSWD` radius <<EOF
INSERT INTO log_wan_traffic (when2,rx,rx_unit,tx,tx_unit) VALUES
(FROM_UNIXTIME($START),$RX,"$RX_UNIT",$TX,"$TX_UNIT");
EOF

