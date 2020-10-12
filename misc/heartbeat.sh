#!/usr/local/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt

TIMESTAMP=`date +%Y%m%d-%H%M%S`
 
PUBLIC_IP="`curl https://wtfismyip.com/text`"
# alterntively: dig TXT +short o-o.myaddr.l.google.com @ns1.google.com | awk -F'"' '{ print $2}'
# or: dig +short @resolver1.opendns.com myip.opendns.com
FIRST_MAC="`/sbin/ifconfig | grep ether | head -1`"
ALL_MACS=`/sbin/ifconfig | grep ether`
UPTIME=`/usr/bin/uptime`
INET=`/sbin/ifconfig | grep "inet "`
LOAD=`top | grep averages`
MEM=`top | grep Mem`
TOTAL_MEM_TEMP=`sysctl hw.physmem | awk '{print $2}'`
TOTAL_MEM=`echo "scale=2 ; $TOTAL_MEM_TEMP / 1000000" | bc`
DISK=`df -H /`
SWAP=`top | grep Swap`
PFSENSE_VERSION="`cat /etc/version`-p`cat /etc/version.patch`"

TEMP_MAIL=`mktemp /home/mail_backlog/$TIMESTAMP-XXXXXX`.sh
echo "FromLineOverride=YES
mailhub=smtp.gmail.com:587
AuthUser=notification@marsgeneral.com
AuthPass=CHANGE_ME
UseSTARTTLS=YES" > $TEMP_MAIL.config
echo "From: notification@marsgeneral.com
To: cneumann@marsgeneral.com
Subject: marsPortal heartbeat at `date +%Y%m%d-%H%M` ($DEVICE_NAME,$SSH_TUNNEL_PORT,$NETGATE_ID)

device name:
	$DEVICE_NAME
zone:
	$ZONE
public ip:
	$PUBLIC_IP
ssh tunnel port:
	$SSH_TUNNEL_PORT
netgate id:
	$NETGATE_ID
pfsense version:
	$PFSENSE_VERSION
uptime: 
	$UPTIME
all macs: 
$ALL_MACS
ifconfig: 
$INET
total memory:
	$TOTAL_MEM
memory:
	$MEM
disk:
	$DISK
swap:
	$SWAP
load:
	$LOAD

(mail generated by script marsPortal:///home/marsPortal/misc/heartbeat.sh)" > $TEMP_MAIL.mail

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
