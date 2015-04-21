/usr/local/bin/mysqld_safe &
sleep 10

echo 'radiusd_enable="YES"' >> /etc/rc.conf
mkdir /var/run/radiusd/
/usr/local/etc/rc.d/radiusd start

