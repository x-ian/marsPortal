#!/bin/bash

LOG=/tmp/log_wan_throughput.log
LOG2=/tmp/log_wan_throughput_2.log
#LOG=internet_monitor.log

BASEDIR=/home/marsPortal
source $BASEDIR/config.txt

while true
do
	sleep 9

	START=`date +%s`
	
	TRAFFIC=`netstat -w 10  -q 1 -b -d -I igb0 | tail -1`

	RX=$(echo $(echo $TRAFFIC | awk '{print $4}') / 1000 | bc)
	if [ -z "$RX" ]; then
		RX=-1
	fi
	TX=$(echo $(echo $TRAFFIC | awk '{print $7}') / 1000 | bc)
	if [ -z "$TX" ]; then
		TX=-1
	fi
	/usr/local/bin/mysql -u `echo $MYSQL_USER` -p`echo $MYSQL_PASSWD` radius <<EOF
INSERT INTO log_wan_throughput (at,rx,rx_unit,tx,tx_unit) VALUES
(FROM_UNIXTIME($START),$RX,"kbytes/sec",$TX,"kbytes/sec");
EOF
done

exit 0

while true
do
	
	sed -re $'s/\b[\b ]+\b/\\n/g' $LOG | tail -1 > $LOG2
	RX=$(grep "rx" $LOG2 | awk '{print $4}')
	echo "RX $RX"
	if [ -z "$RX" ]; then
		RX=-1
	fi
	RX_UNIT=`grep "rx" $LOG2 | awk '{print $5}'`
	if [ -z "$RX_UNIT" ]; then
		RECEIVED=-1
	fi
	TX=`grep "tx" $LOG2 | awk '{print $9}'`
	if [ -z "$TX" ]; then
		TX=-1
	fi
	TX_UNIT=`grep "tx" $LOG2 | awk '{print $10}'`
	if [ -z "$TX_UNIT" ]; then
		TX_UNIT=-1
	fi

	cat  <<EOF
INSERT INTO log_wan_throughput (at,rx,rx_unit,tx,tx_unit) VALUES
(FROM_UNIXTIME($START),$RX,"$RX_UNIT",$TX,"$TX_UNIT");
EOF

	/usr/local/bin/mysql -u `echo $MYSQL_USER` -p`echo $MYSQL_PASSWD` radius <<EOF
INSERT INTO log_wan_throughput (at,rx,rx_unit,tx,tx_unit) VALUES
(FROM_UNIXTIME($START),$RX,"$RX_UNIT",$TX,"$TX_UNIT");
EOF
done
