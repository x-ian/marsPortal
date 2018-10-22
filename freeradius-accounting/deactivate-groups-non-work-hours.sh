#!/usr/local/bin/bash

BASEDIR=`dirname $0`
source $BASEDIR/../config.txt

# update config.txt to set new default group to work hours
/usr/bin/sed  -i '' 's/-non-work-hours//g' $BASEDIR/../config.txt

# get all users of group
MACS=`/usr/local/bin/mysql -u $(echo $MYSQL_USER) -p$(echo $MYSQL_PASSWD) radius -N -B -e "select username from radusergroup where groupname like ('%-non-work-hours');"`

# change device/group relation
/usr/local/bin/mysql -u `echo $MYSQL_USER` -p`echo $MYSQL_PASSWD` radius <<EOF
set @non_work_groups_postfix = '-non-work-hours';

UPDATE radusergroup 
  SET groupname = SUBSTRING(groupname,1,LENGTH(groupname)-LENGTH(@non_work_groups_postfix)) 
  WHERE groupname LIKE CONCAT('%', @non_work_groups_postfix);
EOF

#/usr/local/bin/php -q $BASEDIR/../misc/captiveportal-disconnect-all-users.php
while read line           
do
	echo "disconnect $line"
	/usr/bin/sed -i '' -n '/'"$line"'/!p' /tmp/auto_login_all_previous_macs
	/usr/local/bin/php -q $BASEDIR/../misc/captiveportal-disconnect-user.php $line
done <<< $MACS
