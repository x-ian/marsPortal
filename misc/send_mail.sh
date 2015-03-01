#!/usr/local/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt
source $BASEDIR/ssmtp.config
 
SUBJECT=$1;
BODY=$2;
SENDER=$AuthUser;
#RECEIVER=$RECEIVER already part of config.txt

TEMP_MAIL=`mktemp /tmp/ssmtp.mail.XXXXXX`
echo "From: $SENDER
To: $RECEIVER
Subject: $SUBJECT

$BODY" > $TEMP_MAIL

/usr/local/sbin/ssmtp -C $SSMTP_CONFIG $RECEIVER < $TEMP_MAIL

rm -f $TEMP_MAIL $TEMP_CONFIG
