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


TEMP_MAIL=`mktemp /home/mail_backlog/$TIMESTAMP-XXXXXX.sh`
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
" > $TEMP_MAIL.mail

# place mail job in backlog of mails
echo "#!/usr/local/bin/bash
/usr/local/sbin/ssmtp -C $SSMTP_CONFIG $RECEIVER < $TEMP_MAIL.mail
if [ $? -eq 0 ]; then
	rm -f $TEMP_MAIL*
fi
" > $TEMP_MAIL

# try to send it once right away
/usr/local/bin/bash -x $TEMP_MAIL
