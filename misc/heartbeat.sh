#!/usr/local/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt

SENDER=notification@marsgeneral.com
RECEIVER=cneumann@marsgeneral.com

FIRST_MAC=" `/sbin/ifconfig | grep ether | head -1`"
ALL_MACS=`/sbin/ifconfig | grep ether`
UPTIME=`/usr/bin/uptime`
INET=`ifconfig | grep "inet "`
VALUE="GoingToIbiza"
MEM=`top | grep Mem`
SWAP=`top | grep Swap`
LOAD=`top | grep averages`

SUBJECT="`echo "pfSense heartbeat"` $ZONE $FIRST_MAC `date +%Y%m%d-%H%M`"	
BODY="
$UPTIME
$ALL_MACS
$INET
$MEM
$SWAP
$LOAD"

/usr/bin/perl -I /usr/local/lib/perl5/site_perl/5.10.1/ -I /usr/local/lib/perl5/site_perl/5.10.1/mach $BASEDIR/send_gmail.perl "$SUBJECT" "$BODY" $SENDER $VALUE $RECEIVER
