#!/usr/local/bin/bash

BASEDIR=/home/marsPortal

TMP_COOKIES=`mktemp`-cookies.txt
TMP_CSRF=`mktemp`-csrf.txt
TMP_CSRF2=`mktemp`-csrf2.txt
TMP_ALL=`mktemp`-all.html

# get pfSense login details
source $BASEDIR/config.txt

# login and CSRF handling
/usr/local/bin/wget -qO- --keep-session-cookies --save-cookies $TMP_COOKIES --no-check-certificate \
  $PF_SERVER/diag_backup.php \
  | grep "name='__csrf_magic'" | sed 's/.*value="\(.*\)".*/\1/' > $TMP_CSRF
/usr/local/bin/wget -qO- --keep-session-cookies --load-cookies $TMP_COOKIES \
  --save-cookies $TMP_COOKIES --no-check-certificate \
  --post-data "login=Login&usernamefld=`echo $USER`&passwordfld=`echo $PASSWD`&__csrf_magic=`cat $TMP_CSRF`" \
  $PF_SERVER/diag_backup.php | grep "name='__csrf_magic'" \
  | sed 's/.*value="\(.*\)".*/\1/' > $TMP_CSRF2

# get all active users
/usr/local/bin/wget --keep-session-cookies --load-cookies $TMP_COOKIES --no-check-certificate \
  --post-data "__csrf_magic=`cat $TMP_CSRF`&descr=weekly&frequency=daily&dayofweek=0&dayofmonth=&monthofquarter=&monthofyear=&timeofday=0&Submit=Send+Now&id=0" \
  --output-document=$TMP_ALL \
  "$PF_SERVER/status_mail_report_edit.php?id=0"

rm $TMP_ALL
rm $TMP_COOKIES
rm $TMP_CSRF
rm $TMP_CSRF2

