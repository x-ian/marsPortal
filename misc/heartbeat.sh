#!/usr/local/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt

TIMESTAMP=`date +%Y%m%d-%H%M%S`
 
PUBLIC_IP="`wget http://www.marsmonitoring.com/whatismyip`"
FIRST_MAC=" `/sbin/ifconfig | grep ether | head -1`"
ALL_MACS=`/sbin/ifconfig | grep ether`
UPTIME=`/usr/bin/uptime`
INET=`ifconfig | grep "inet "`
LOAD=`top | grep averages`
MEM=`top | grep Mem`
DISK=`df -H /`
SWAP=`top | grep Swap`

TEMP_MAIL=`mktemp /home/mail_backlog/$TIMESTAMP-XXXXXX.sh`
echo "FromLineOverride=YES
mailhub=smtp.gmail.com:587
AuthUser=notification@marsgeneral.com
AuthPass=GoingToIbiza
UseSTARTTLS=YES" > $TEMP_MAIL.config
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
disk:
	$DISK
swap:
	$SWAP
load:
	$LOAD" > $TEMP_MAIL.mail

# place mail job in backlog of mails
echo "#!/usr/local/bin/bash
/usr/local/sbin/ssmtp -C $TEMP_MAIL.config $RECEIVER < $TEMP_MAIL.mail
if [ $? -eq 0 ]; then
	rm -f $TEMP_MAIL*
fi
" > $TEMP_MAIL

# try to send it once right away
/usr/local/bin/bash -x $TEMP_MAIL

rm -f $TEMP_MAIL $TEMP_CONFIG
