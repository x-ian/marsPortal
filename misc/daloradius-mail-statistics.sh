#!/usr/local/bin/bash

BASEDIR=/home/marsPortal
source $BASEDIR/config.txt

FILE=/tmp/statistics-`date +%Y%m%d`.html
/usr/local/bin/curl --retry 2 -s -o $FILE `echo $DR_SERVER`/mars/statistics.php
echo $? > $FILE.exitcode

SUBJECT="`echo "pfSense: Users statistics: "` `date +%Y%m%d-%H%M`"
BODY=`echo "(mail generated by script pfSense:///home/marsPortal/misc/daloradius-mail-statistics.sh)"`

/usr/local/bin/perl -I /usr/local/lib/perl5/site_perl/5.10.1/ -I /usr/local/lib/perl5/site_perl/5.10.1/mach $BASEDIR/send_gmail_with_attachment.perl "$SUBJECT" $FILE $FILE application/html "$BODY" "$GMAIL_SENDER" "$GMAIL_SENDER_PASSWD" "$RECEIVER"
