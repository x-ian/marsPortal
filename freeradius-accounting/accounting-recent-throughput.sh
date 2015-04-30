#!/usr/local/bin/bash

BASEDIR=`dirname $0`
source $BASEDIR/../config.txt

/usr/local/bin/mysql -u `echo $MYSQL_USER` -p`echo $MYSQL_PASSWD` radius <<EOF
	truncate accounting_snapshot;
	insert into accounting_snapshot (username, datetime, output, input)   
	select username, now(), day_total_output, day_total_input from daily_accounting_v2 where day ='2015-04-30';
EOF
