#!/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt

PORTAL_IPS=/tmp/portal_ips
ALL_POSSIBLE_MACS=/tmp/all_possible_macs
ALL_CONNECTED_MACS=/tmp/all_connected_macs

# get IPs of portal
/sbin/ifconfig | grep "inet " | awk '{print $2}' > $PORTAL_IPS

# get all auto_login users
/usr/local/bin/mysql --defaults-extra-file=<(printf "[client]\nuser = %s\npassword = %s" "$MYSQL_USER" "$MYSQL_PASSWD") -s radius -e "select ui.username from userinfo ui, groupinfo gi, radusergroup rg where gi.groupname = rg.groupname and rg.username = ui.username and gi.auto_login=true;" > $ALL_POSSIBLE_MACS

# get all currently connected macs
/usr/sbin/arp -n -a -i $LAN_INTERFACE > $ALL_CONNECTED_MACS

while read line
do
	IP=$(echo $line | awk '{print $2}' | tr '(' ' ' | tr ')' ' ')
	MAC=$(echo $line | awk '{print $4}') 
	
	grep $IP $PORTAL_IPS
	if [ $? -ne 0 ]; then
		# not my own IP, so check if registered radius user in auto-login group
		grep $MAC $ALL_POSSIBLE_MACS
		if [ $? -eq 0 ]; then
			# radius user with auto login found, check if already auth
			/usr/local/bin/php -e $BASEDIR/misc/captiveportal-device-connected.php $MAC
			if [ $? -eq 0 ]; then
				echo "$MAC with $IP to be authed"
				/home/marsPortal/freeradius-integration/self-registration/captive-portal-add_user_to_radius.sh $IP "" "" "" "" "Users"
			fi
		fi
	fi
done < $ALL_CONNECTED_MACS
