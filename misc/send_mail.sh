#!/usr/local/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt
source $BASEDIR/ssmtp.config

TIMESTAMP=`date +%Y%m%d-%H%M%S`
 
SUBJECT=$1
BODY=$2
SENDER=$AuthUser
#RECEIVER=$RECEIVER already part of config.txt

TEMP_MAIL=`mktemp /home/mail_backlog/$TIMESTAMP.XXXXXX`
echo "From: $SENDER
To: $RECEIVER
Subject: $SUBJECT

$BODY" > $TEMP_MAIL

echo '/usr/local/sbin/ssmtp -C $SSMTP_CONFIG $RECEIVER < $TEMP_MAIL' > /home/mail_backlog/$TIMESTAMP.sh
