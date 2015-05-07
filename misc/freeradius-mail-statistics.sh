#!/usr/local/bin/bash

BASEDIR=/home/marsPortal
source $BASEDIR/config.txt

FILE=/tmp/statistics-`date +%Y%m%d`.html
#/usr/local/bin/curl -u $DR_HTTP_USER_PASSWD --retry 2 -s -o $FILE `echo $PF_SERVER`/mars/reports/statistics.php
/usr/local/bin/curl --retry 2 -s -o $FILE `echo $PF_SERVER`/mars/reports/statistics.php
echo $? > $FILE.exitcode

SUBJECT="`echo "marsPortal $ZONE: Users statistics: "` `date +%Y%m%d-%H%M`"
BODY=`echo "(mail generated by script marsPortal:///home/marsPortal/misc/freeradius-mail-statistics.sh)"`

$BASEDIR/misc/send_mail_with_attachment.sh "$SUBJECT" "$BODY" $FILE $FILE application/html
