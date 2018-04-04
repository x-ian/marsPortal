#!/usr/local/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt

DHCP_MACS=/tmp/dhcp_leases.log

# all mac addresses of dhcp leases
grep -o '^[^#]*' /var/dhcpd/var/db/dhcpd.leases | egrep "lease|hostname|hardware ethernet|starts|}" | sed 's/lease/\'$'\nlease/g' | tr '\n' ' ' | tr '}', '\n' | awk '{print $10}' | tr -d ';' > $DHCP_MACS

while read mac
do
	if [ -z "${mac// }" ]; then	
		:
	else
		RESULT=$(/usr/local/bin/mysql --defaults-extra-file=<(printf "[client]\nuser = %s\npassword = %s" "$MYSQL_USER" "$MYSQL_PASSWD") -s radius -e "select '1' from userinfo where username='$mac'")
	
		if [ "$RESULT" != "1" ]; then
			#echo "$mac not registered"
			grep -o '^[^#]*' /var/dhcpd/var/db/dhcpd.leases | egrep "lease|hostname|hardware ethernet|starts|}" | sed 's/lease/\'$'\nlease/g' | tr '\n' ' ' | tr '}', '\n' | grep "$mac"
		fi
	fi
done < $DHCP_MACS
