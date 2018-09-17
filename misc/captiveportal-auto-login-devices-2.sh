#!/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt

PORTAL_IPS=/tmp/portal_ips
ALL_POSSIBLE_MACS=/tmp/auto_login_all_possible_macs
ALL_PREVIOUS_MACS=/tmp/auto_login_all_previous_macs
ALL_CONNECTED_MACS=/tmp/auto_login_all_connected_macs

# make sure previous_macs exists, create if necessary
touch $ALL_PREVIOUS_MACS

# get IPs of portal
#/sbin/ifconfig | grep "inet " | awk '{print $2}' > $PORTAL_IPS

# get all currently connected macs
# | grep -Fv "$(cat /tmp/portal_ips)" | ...
/usr/sbin/arp -n -a -i $LAN_INTERFACE | awk '{print $4}' | sort | uniq > $ALL_CONNECTED_MACS

# get all new macs
ALL=`comm -13 $ALL_PREVIOUS_MACS $ALL_CONNECTED_MACS`

if [[ ! -z "$ALL" ]]; then
	
	while read line
	do
		echo $line
		MAC=$line

		# is auto_login user
		AUTO_LOGIN=`/usr/local/bin/mysql --defaults-extra-file=<(printf "[client]\nuser = %s\npassword = %s" "$MYSQL_USER" "$MYSQL_PASSWD") -s radius -b -e "select ui.username from userinfo ui, groupinfo gi, radusergroup rg where ui.username = \"$MAC\" and gi.groupname = rg.groupname and rg.username = ui.username and gi.auto_login=true;"`

		if [ ! -z "$AUTO_LOGIN" ]; then
			IP=$(/usr/sbin/arp -n -a -i $LAN_INTERFACE | grep $MAC | awk '{print $2}' | tr '(' ' ' | tr ')' ' ')
			echo "$MAC with $IP to be authed at `date`" >>/tmp/auto_login_new
			/usr/local/bin/php -e $BASEDIR/misc/captiveportal-connect-user.php $IP $MAC
		fi
	done <<< "$ALL"
fi
cp $ALL_CONNECTED_MACS $ALL_PREVIOUS_MACS

sleep 2
#/home/marsPortal/misc/captiveportal-auto-login-devices-2.sh &
