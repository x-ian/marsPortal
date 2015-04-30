#!/usr/local/bin/bash

# all the housekeeping stuff that I want to be done sunday nights
# let's say right before midnight
# 55 23 * * Sun ...

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt
 
# reset ntop traffic stats
/usr/local/bin/wget --user `echo $NTOP_USER` --password `echo $NTOP_PASSWD` http://`echo $PF_IP`:3000/resetStats.html

# compacting squid cache (http://doc.pfsense.org/index.php/Squid_Package_Tuning)
#/usr/local/sbin/squid -k rotate

# clearing out and recreating the whole squid cache dir
#/usr/local/sbin/squid -k shutdown
#/bin/sleep 10
#/bin/rm -rf /var/squid/cache/*
#/usr/local/sbin/squid -z

# some internal backup
#/home/marsPortal/misc/do-backup.sh

# clean up DHCP leases as they seem to be never removed. ideally this should maybe be done monthly or quarterly
/bin/rm -f /var/dhcpd/var/db/dhcpd.leases
/bin/rm -f /var/dhcpd/var/db/dhcpd.leases~
/bin/rm -f /var/dhcpd/var/db/dhcpd6.leases
/bin/rm -f /var/dhcpd/var/db/dhcpd6.leases~

# delete outstanding mails older than 14 days
/usr/bin/find /home/mail_backlog -mtime +14 -delete

# RADIUS ACCOUNTING
/usr/local/bin/mysql -u $MYSQL_USER -p$MYSQL_PASSWD radius <<EOF
	delete from radpostauth where authdate < DATE_ADD(CURDATE(),INTERVAL -3 MONTH);
	delete from radacct where acctstarttime < DATE_ADD(CURDATE(),INTERVAL -3 MONTH);
EOF
	
# update MAC vendor list, http://standards.ieee.org/develop/regauth/oui/public.html
/usr/local/bin/wget http://standards.ieee.org/develop/regauth/oui/oui.txt -O /tmp/ieee_oui.txt
if [ $? -eq 0 ]; then
	mv /tmp/ieee_oui.txt $BASEDIR/freeradius-integration/self-registration
fi

# just in case, restart once in a while
/sbin/reboot

