#!/usr/local/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt

PUBLIC_IP="1.2.3.4" #"wget http://www.marsmonitoring.com/whatismyip"
FIRST_MAC=" `/sbin/ifconfig | grep ether | head -1`"
ALL_MACS=`/sbin/ifconfig | grep ether`
UPTIME=`/usr/bin/uptime`
INET=`ifconfig | grep "inet "`
LOAD=`top | grep averages`
MEM=`top | grep Mem`
SWAP=`top | grep Swap`

TEMP_CONFIG=`mktemp /tmp/ssmtp.config.XXXXXX`
echo "FromLineOverride=YES
mailhub=smtp.gmail.com:587
AuthUser=notification@marsgeneral.com
AuthPass=GoingToIbiza
UseSTARTTLS=YES" > $TEMP_CONFIG
TEMP_MAIL=`mktemp /tmp/ssmtp.mail.XXXXXX`
echo "From: notification@marsgeneral.com
To: cneumann@marsgeneral.com
Subject: pfSense heartbeat zone: $ZONE, public ip: $PUBLIC_IP, `date +%Y%m%d-%H%M`

uptime: 
	$UPTIME
all macs: 
$ALL_MACS
ipconfig: 
$INET
memory:
	$MEM
swap:
	$SWAP
load:
	$LOAD" > $TEMP_MAIL

/usr/local/sbin/ssmtp -C $TEMP_CONFIG cneumann@marsgeneral.com < $TEMP_MAIL

rm -f $TEMP_MAIL $TEMP_CONFIG
