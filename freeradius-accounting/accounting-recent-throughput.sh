#!/usr/local/bin/bash

BASEDIR=`dirname $0`
source $BASEDIR/../config.txt

SNAPSHOT_TABLE=$1

/usr/local/bin/mysql -u `echo $MYSQL_USER` -p`echo $MYSQL_PASSWD` radius <<EOF
	truncate $1;
	insert into $1 (username, datetime, output, input)   
	select username, now(), day_total_output, day_total_input from daily_accounting_v2 where day=date_format(now(), '%Y-%m-%d') ;
EOF

