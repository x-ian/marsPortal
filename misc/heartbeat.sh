#!/usr/local/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt

FIRST_MAC=" `/sbin/ifconfig | grep ether | head -1`"
ALL_MACS=`/sbin/ifconfig | grep ether`
UPTIME=`/usr/bin/uptime`
INET=`ifconfig | grep "inet "`
VALUE="GoingToIbiza"

SUBJECT="`echo "pfSense heartbeat"` $FIRST_MAC `date +%Y%m%d-%H%M`"	
BODY="
$UPTIME
$ALL_MACS
$INET"

/usr/bin/perl -I /usr/local/lib/perl5/site_perl/5.10.1/ -I /usr/local/lib/perl5/site_perl/5.10.1/mach $BASEDIR/send_gmail.perl "$SUBJECT" "$BODY" notification@marsgeneral.com $VALUE cneumann@marsgeneral.com
