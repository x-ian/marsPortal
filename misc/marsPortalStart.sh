#!/usr/local/bin/bash

# start script to bring back mysqld and radiusd after reboot
# easiest to add as a @reboot cronjob for root

/usr/local/bin/mysqld_safe &
sleep 10

echo 'radiusd_enable="YES"' >> /etc/rc.conf
mkdir /var/run/radiusd/
/usr/local/etc/rc.d/radiusd start

