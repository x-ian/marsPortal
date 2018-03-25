#!/bin/bash

HOST=8.8.8.8
LOG=/tmp/log_internet_ping.log
#LOG=internet_monitor.log

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt

# allow some time after restart to recover network connection

sleep 60
while true
do
	rm -f $LOG
	START=`date +%s`
	ping -q -t 60 -i 5 $HOST >> $LOG
	STOP=`date +%s`
	TRANSMITTED=`grep "transmitted" $LOG | awk '{print $1}'`
	if [ -z "$TRANSMITTED" ]; then
		TRANSMITTED=-1
	fi
	RECEIVED=`grep "transmitted" $LOG | awk '{print $4}'`
	if [ -z "$RECEIVED" ]; then
		RECEIVED=-1
	fi
	PACKET_LOSS=`grep "transmitted" $LOG | awk '{print $7}'`
	if [ -z "$PACKET_LOSS" ]; then
		PACKET_LOSS=-1
	fi
	RTT_AVG=`grep "round-trip" $LOG | awk '{print $4}' | awk -F'/' '{print $2}'`
	if [ -z "$RTT_AVG" ]; then
		RTT_AVG=-1
	fi
	/usr/local/bin/mysql -u `echo $MYSQL_USER` -p`echo $MYSQL_PASSWD` radius <<EOF
INSERT INTO log_internet_ping (begin,end,transmitted,received,packet_loss,rtt_avg) VALUES
(FROM_UNIXTIME($START),FROM_UNIXTIME($STOP),$TRANSMITTED,$RECEIVED,"$PACKET_LOSS",$RTT_AVG);
EOF
done
