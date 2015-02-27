#!/bin/bash

BASEDIR=`dirname $0`
source $BASEDIR/../config.txt

/usr/bin/mysql -u `echo $DR_MYSQL_USER` -p`echo $DR_MYSQL_PASSWD` radius <<EOF
set @non_work_groups_postfix = '-non-work-hours';

UPDATE radusergroup 
  SET groupname = SUBSTRING(groupname,1,LENGTH(groupname)-LENGTH(@non_work_groups_postfix)) 
  WHERE groupname LIKE CONCAT('%', @non_work_groups_postfix) COLLATE utf8_unicode_ci;
EOF

$BASEDIR/../misc/captiveportal-disconnect-all-users.sh
