#!/usr/local/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt

# checking for system crashes
# for some reasons it seems as at least in the logs the systems gets signal 15 twice, but at least
# not for normal reboots
# check for the last kernel boot message and see if directly before a signal 15 was invoked
/usr/local/sbin/clog /var/log/system.log | /usr/bin/grep "kernel boot file is /boot/kernel/kernel" -B 1 | /usr/bin/tail -2 | /usr/bin/grep 'exiting on signal 15'
CRASH=$?

if [ $CRASH -eq 0 ]; then
	SUBJECT="`echo "startup at "` `date +%Y%m%d-%H%M`"
else
	SUBJECT="`echo "startup after crash at "` `date +%Y%m%d-%H%M`"	
fi
BODY=`echo "(mail generated by script marsPortal:///home/marsPortal/misc/mail_after_startup.sh)"`

$BASEDIR/misc/send_mail.sh "$SUBJECT" "$BODY"
