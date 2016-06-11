#!/usr/local/bin/bash

BASEDIR=/home/marsPortal
MAC=$1

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

# loop over users and terminate session for matching MAC address
# should only be one, but I cloned the disconnect-all-users script
cat $TMP_ALL | grep -A5 `echo $MAC` | tail -1 | cut -d "\"" -f2 | sed 's/amp;//g' | while read -r url
do
  /usr/local/bin/wget --keep-session-cookies --load-cookies $TMP_COOKIES --no-check-certificate \
    --output-document=$TMP_ALL2 \
    "$PF_SERVER/status_captiveportal.php$url"
done

rm $TMP_ALL
rm $TMP_ALL2
rm $TMP_COOKIES
rm $TMP_CSRF
rm $TMP_CSRF2
