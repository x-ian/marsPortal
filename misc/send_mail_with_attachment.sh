#!/usr/local/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt
source $BASEDIR/ssmtp.config
 
SUBJECT=$1
BODY=$2
FILE=$3
FILENAME=$4
CONTENTTYPE=$5
SENDER=$AuthUser


TEMP_MAIL=`mktemp /tmp/ssmtp.mail.XXXXXX`
echo "From: $SENDER
To: $RECEIVER
Subject: $SUBJECT
MIME-Version: 1.0
Content-type: multipart/mixed;
	boundary=\"frontier\"

--frontier
Content-type: text/plain
Content-Disposition: quoted-printable

$BODY

--frontier
Content-Type: $CONTENTTYPE; name=\"$FILENAME\"
Content-Disposition: attachment; filename=\"$FILENAME\"

`cat $FILE`

--frontier--
" > $TEMP_MAIL

/usr/local/sbin/ssmtp -C $SSMTP_CONFIG $RECEIVER < $TEMP_MAIL

rm -f $TEMP_MAIL $TEMP_CONFIG
