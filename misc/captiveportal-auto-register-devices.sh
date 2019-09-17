#!/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt

PORTAL_IPS=/tmp/portal_ips
ALL_KNOWN_MACS=/tmp/all_known_macs
ALL_CONNECTED_MACS=/tmp/all_connected_macs

# get IPs of portal
/sbin/ifconfig | grep "inet " | awk '{print $2}' > $PORTAL_IPS

# get all existing users
/usr/local/bin/mysql --defaults-extra-file=<(printf "[client]\nuser = %s\npassword = %s" "$MYSQL_USER" "$MYSQL_PASSWD") -s radius -e "select ui.username from userinfo ui;" > $ALL_KNOWN_MACS

# get all currently connected macs
/usr/sbin/arp -n -a -i $LAN_INTERFACE > $ALL_CONNECTED_MACS

while read line
do
	IP=$(echo $line | awk '{print $2}' | tr '(' ' ' | tr ')' ' ')
	MAC=$(echo $line | awk '{print $4}') 
	
	# check for (incomplete) MAC address; unclear why this happens
	if [ "$MAC" != "(incomplete)" ]; then
	
	grep $IP $PORTAL_IPS
	if [ $? -ne 0 ]; then
		# not my own IP, so check if registered radius user
		grep $MAC $ALL_KNOWN_MACS
		if [ $? -ne 0 ]; then
			# radius user not found
			echo "$MAC - $IP - w - automatic auto reg of new devices - `date +%Y%m%d-%H%M%S`" >> /home/client_activities_log/status-`date +%Y%m%d`.log
			echo "$MAC with $IP to be registered"
			/home/marsPortal/freeradius-integration/self-registration/captive-portal-add_user_to_radius.sh $IP "" "" "" "" $DEFAULT_GROUP
			# auto login newly registered device
			# TODO: should only for groups with auto-login activated
			# doesn't appear to properly work in pfsense 2.3 ALIX
			/usr/local/bin/php -e $BASEDIR/misc/captiveportal-connect-user.php $IP $MAC
		fi
	fi

	fi
done < $ALL_CONNECTED_MACS
