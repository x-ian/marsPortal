#!/usr/local/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt

# checking for system crashes
# for some reasons it seems as at least in the logs the systems gets signal 15 twice
# check for the last kernel boot message and see if directly before a signal 15 was invoked
/usr/local/sbin/clog /var/log/system.log | /usr/bin/grep "kernel boot file is /boot/kernel/kernel" -B 1 | /usr/bin/tail -5 | /usr/bin/head -2 | /usr/bin/grep 'exiting on signal 15'
CRASH=$?

if [ $CRASH -eq 0 ]; then
	SUBJECT="`echo "pfSense: System startup: "` `date +%Y%m%d-%H%M`"
else
	SUBJECT="`echo "pfSense: System startup after crash: "` `date +%Y%m%d-%H%M`"	
fi
BODY=`echo "(mail generated by script pfSense:///home/marsPortal/misc/send_gmail_after_startup.sh)"`

/usr/bin/perl -I /usr/local/lib/perl5/site_perl/5.10.1/ -I /usr/local/lib/perl5/site_perl/5.10.1/mach $BASEDIR/send_gmail.perl "$SUBJECT" "$BODY" "$GMAIL_SENDER" "$GMAIL_SENDER_PASSWD" "$RECEIVER"
