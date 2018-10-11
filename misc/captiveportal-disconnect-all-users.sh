#!/usr/local/bin/bash

BASEDIR=/home/marsPortal

TMP_COOKIES=`mktemp`-cookies.txt
TMP_CSRF=`mktemp`-csrf.txt
TMP_CSRF2=`mktemp`-csrf2.txt
TMP_ALL=`mktemp`-all.html
TMP_ALL2=`mktemp`-all2.html

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
  --output-document=$TMP_ALL \
  "$PF_SERVER/status_captiveportal.php?zone=$ZONE"

# delete cache of known MACs
rm -f /tmp/auto_login_all_previous_macs

# loop over users and terminate all sessions
cat $TMP_ALL | grep '&amp;order=&amp;showact=&amp;act=del&amp;id' | cut -d "\"" -f2 | sed 's/amp;//g' | while read -r url
do
  sleep 2
  /usr/local/bin/wget --keep-session-cookies --load-cookies $TMP_COOKIES --no-check-certificate \
    --output-document=$TMP_ALL2 \
    "$PF_SERVER/status_captiveportal.php$url"
done

rm $TMP_ALL
rm $TMP_ALL2
rm $TMP_COOKIES
rm $TMP_CSRF
rm $TMP_CSRF2
