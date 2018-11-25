#!/usr/local/bin/bash

BASEDIR=`dirname $0`
source $BASEDIR/../config.txt

# update config.txt to set new default user group
/usr/bin/sed  -i '' '/DEFAULT_GROUP=/s/$/-non-work-hours/' $BASEDIR/../config.txt

/usr/local/bin/mysql -u `echo $MYSQL_USER` -p`echo $MYSQL_PASSWD` radius <<EOF
set @non_work_groups_postfix = '-non-work-hours';

UPDATE radusergroup
SET groupname = CONCAT(groupname, @non_work_groups_postfix)
WHERE
  (SELECT COUNT(*) > 0 FROM radgroupreply WHERE groupname = CONCAT(radusergroup.groupname, @non_work_groups_postfix))
EOF

# get all users of group
MACS=`/usr/local/bin/mysql -u $(echo $MYSQL_USER) -p$(echo $MYSQL_PASSWD) radius -N -B -e "select username from radusergroup where groupname like ('%-non-work-hours');"`

#/usr/local/bin/php -q $BASEDIR/../misc/captiveportal-disconnect-all-users.php
while read line           
do
	echo "disconnect $line"
	/usr/bin/sed -i '' -n '/'"$line"'/!p' /tmp/auto_login_all_previous_macs
	/usr/local/bin/php -q $BASEDIR/../misc/captiveportal-disconnect-user.php $line
done <<< $MACS

# restart unbound to load new block files
# kind of sucks, unbound-control didn't work?
/usr/bin/killall unbound
sleep 5
/usr/local/sbin/unbound -c /var/unbound/unbound.conf
