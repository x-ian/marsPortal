#!/usr/local/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt
source $BASEDIR/ssmtp.config

TIMESTAMP=`date +%Y%m%d-%H%M%S`

SUBJECT="marsPortal $1 ($SSH_TUNNEL_PORT,$NETGATE_ID)"
BODY=$2
SENDER=$AuthUser
#RECEIVER=$RECEIVER already part of config.txt

TEMP_MAIL=`mktemp /home/mail_backlog/$TIMESTAMP-XXXXXX.sh`
echo "From: $SENDER
To: $RECEIVER
Subject: $SUBJECT

$BODY" > $TEMP_MAIL.mail

# place mail job in backlog of mails
echo "#!/usr/local/bin/bash
/usr/local/sbin/ssmtp -C $SSMTP_CONFIG $RECEIVER < $TEMP_MAIL.mail > $TEMP_MAIL.exit 2>&1
# if [ $? -eq 0 ]; then # used to work, doesnt anymore...
if [ ! -s "$TEMP_MAIL.exit" ]; then
	rm -f $TEMP_MAIL*
fi
" > $TEMP_MAIL

# try to send it once right away
/usr/local/bin/bash -x $TEMP_MAIL
