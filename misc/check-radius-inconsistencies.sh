#!/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt

ENTRIES=`/usr/local/bin/mysql -u $MYSQL_USER -p$MYSQL_PASSWD radius -e "select username, count(*) from radusergroup group by username having count(*) <> 1;"`
RET=`echo "$ENTRIES" | /usr/bin/wc -l`

ERRORS=0

if [ "$ENTRIES" != ""  ]; then
	ERRORS=1
  echo "Device(s) with more than one assigned groups detected."
  echo $ENTRIES
fi

ENTRIES=`/usr/local/bin/mysql -u $MYSQL_USER -p$MYSQL_PASSWD radius -e "select username from userinfo where username not in (select username from radusergroup);"`
RET=`echo "$ENTRIES" | /usr/bin/wc -l`

if [ "$ENTRIES" != ""  ]; then
	ERRORS=1
  echo "Device(s) without any group assignment detected."
  echo $ENTRIES
fi

ENTRIES=`/usr/local/bin/mysql -u $MYSQL_USER -p$MYSQL_PASSWD radius -e "select username, count(*) from userinfo group by username having count(*) <> 1;"`
RET=`echo "$ENTRIES" | /usr/bin/wc -l`

if [ "$ENTRIES" != ""  ]; then
	ERRORS=1
  echo "Device(s) with more than one userinfo entry detected."
  echo $ENTRIES
fi

if [ $ERRORS == 0 ]; then
	echo "No inconsistencies detected."
fi
