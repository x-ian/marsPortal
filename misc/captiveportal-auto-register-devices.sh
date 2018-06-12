#!/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt

PORTAL_IPS=/tmp/portal_ips
ALL_KNOWN_MACS=/tmp/all_known_macs
ALL_CONNECTED_MACS=/tmp/all_connected_macs

# get IPs of portal
/sbin/ifconfig | grep "inet " | awk '{print $2}' > $PORTAL_IPS

# get all existing users
/usr/local/bin/mysql --defaults-extra-file=/home/marsPortal/mysql.txt -s radius -e "select ui.username from userinfo ui;" > $ALL_KNOWN_MACS

# get all currently connected macs
/usr/sbin/arp -n -a -i $LAN_INTERFACE > $ALL_CONNECTED_MACS

while read line
do
	IP=$(echo $line | awk '{print $2}' | tr '(' ' ' | tr ')' ' ')
	MAC=$(echo $line | awk '{print $4}') 
	
	grep $IP $PORTAL_IPS
	if [ $? -ne 0 ]; then
		# not my own IP, so check if registered radius user
		grep $MAC $ALL_KNOWN_MACS
		if [ $? -ne 0 ]; then
			# radius user not found
			echo "$MAC with $IP to be registered"
			/home/marsPortal/freeradius-integration/self-registration/captive-portal-add_user_to_radius.sh $IP "" "" "" "" "Users"
		fi
	fi
done < $ALL_CONNECTED_MACS
