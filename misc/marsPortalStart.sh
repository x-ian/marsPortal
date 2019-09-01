#!/usr/local/bin/bash

# start script to bring back mysqld and radiusd after reboot
# easiest to add as a @reboot cronjob for root

BASEDIR=/home/marsPortal
source $BASEDIR/config.txt

# for now manage unbound outside of pfSense
# stop service in pfSense and manually adjust config
/usr/local/sbin/unbound -c /var/unbound/unbound.conf

/usr/local/bin/mysqld_safe &
sleep 10

echo 'radiusd_enable="YES"' >> /etc/rc.conf
mkdir /var/run/radiusd/
chmod go+w /var/run/radiusd
/usr/local/etc/rc.d/radiusd start

# wipe out all radacct sessions
# NO, not a good idea
#/usr/local/bin/mysql -u `echo $MYSQL_USER` -p`echo $MYSQL_PASSWD` radius <<EOF
#truncate radacct;
#EOF

/usr/local/bin/yaf --mac --in bridge0 --live pcap --out /home/yaf/yaf --log /home/yaf/yaf.log --verbose --rotate 300 &

