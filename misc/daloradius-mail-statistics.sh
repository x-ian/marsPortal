#!/usr/local/bin/bash

BASEDIR=/home/marsPortal
source $BASEDIR/config.txt

FILE=/tmp/statistics-`date +%Y%m%d`.html
/usr/local/bin/curl -u $DR_HTTP_USER_PASSWD --retry 2 -s -o $FILE `echo $DR_SERVER`/mars/admin/statistics.php
echo $? > $FILE.exitcode

SUBJECT="`echo "pfSense $ZONE: Users statistics: "` `date +%Y%m%d-%H%M`"
BODY=`echo "(mail generated by script pfSense:///home/marsPortal/misc/daloradius-mail-statistics.sh)"`

$BASEDIR/misc/send_mail_with_attachment.sh "$SUBJECT" "$BODY" $FILE $FILE application/html

