#!/usr/local/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt

ENTRIES=`/usr/local/bin/mysql -u $MYSQL_USER -p$MYSQL_PASSWD radius -e "select username, count(*) from radusergroup group by username having count(*) <> 1;"`
RET=`echo "$ENTRIES" | /usr/bin/wc -l`

if [ "$ENTRIES" != ""  ]; then
  echo "Device with more than one assigned groups detected"
  echo $ENTRIES
fi

ENTRIES=`/usr/local/bin/mysql -u $MYSQL_USER -p$MYSQL_PASSWD radius -e "select username from userinfo where username not in (select username from radusergroup);"`
RET=`echo "$ENTRIES" | /usr/bin/wc -l`

if [ "$ENTRIES" != ""  ]; then
  echo "Devices without any group assignment detected."
  echo $ENTRIES
fi

# TODO
# Devices with multiple userinfo detected
