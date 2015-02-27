#!/usr/local/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt

SENDER=notification@marsgeneral.com
RECEIVER=cneumann@marsgeneral.com

PUBLIC_IP="1.2.3.4" #"wget http://www.marsmonitoring.com/whatismyip"
FIRST_MAC=" `/sbin/ifconfig | grep ether | head -1`"
ALL_MACS=`/sbin/ifconfig | grep ether`
UPTIME=`/usr/bin/uptime`
INET=`ifconfig | grep "inet "`
VALUE=`echo "GoingToIbiza"`
LOAD=`top | grep averages`
MEM=`top | grep Mem`
SWAP=`top | grep Swap`

SUBJECT="`echo "pfSense heartbeat"` $ZONE $PUBLIC_IP `date +%Y%m%d-%H%M`"	
BODY="
$UPTIME
$ALL_MACS
$INET
$MEM
$SWAP
$LOAD"

/usr/bin/perl -I /usr/local/lib/perl5/site_perl/5.10.1/ -I /usr/local/lib/perl5/site_perl/5.10.1/mach $BASEDIR/send_gmail.perl "$SUBJECT" "$BODY" $SENDER $VALUE $RECEIVER
