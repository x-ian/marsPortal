#!/usr/local/bin/bash

IP=$1
NAME=$2
EMAIL=$(echo $3 | awk '{print tolower($0)}')
OWNER=$4
PRIMARY_DEVICE=$5

DATE=`date +%Y%m%d-%H%M`

BASEDIR=`dirname $0`
PORTALDIR=/home/marsPortal
source $PORTALDIR/config.txt

STATUS_LOG=/home/client_activities_log/status-`date +%Y%m%d`.log

MAC=$($PORTALDIR/misc/resolve_mac_address.sh $IP)
MAC_FIRST_DIGITS=$(echo $MAC | cut -c 1-6 | awk '{print toupper($0)}')
# check in the local copy of the IEEE OUI database
# once in a while get an update from http://standards.ieee.org/develop/regauth/oui/public.html
MAC_VENDOR=$(grep "(base 16)" $BASEDIR/ieee_oui.txt | grep $MAC_FIRST_DIGITS | awk -F"\t" '{ print $3 }' | sed -e 's/ /_/g')

# netbios doesn't seem as reliable as dhcp hostname
#NETBIOS=$($BASEDIR/resolve_netbios_name.sh $IP)
DHCPHOSTNAME=$($BASEDIR/resolve_hostname.sh $IP)

GROUP=Guests
# auto elevate all @pih.org and partners.org users
echo $EMAIL | grep "@pih.org"
if [ $? -eq 0 ]; then
	GROUP=Users
fi
echo $EMAIL | grep "@partners.org"
if [ $? -eq 0 ]; then
	GROUP=Users
fi
# auto elevate all APZU users
#echo $OWNER | grep "apzu"
#if [ $? -eq 0 ]; then
#	GROUP=APZUnet-user
#fi
# auto elevate all APZU- and PIH computers
echo $DHCPHOSTNAME | grep "apzu" --ignore-case
if [ $? -eq 0 ]; then
	GROUP=Users
fi
echo $DHCPHOSTNAME | grep "pih" --ignore-case
if [ $? -eq 0 ]; then
	GROUP=Users
fi
echo $DHCPHOSTNAME | grep "tbc" --ignore-case
if [ $? -eq 0 ]; then
	GROUP=Users
fi

$BASEDIR/new-user-with-mac-auth.sh $MAC "" "$NAME" "$EMAIL" "$OWNER" "$GROUP" "$IP" "$DHCPHOSTNAME" "$MAC_VENDOR" "$PRIMARY_DEVICE"

echo "$MAC - $IP - x - newly registered - `date +%Y%m%d-%H%M%S`" >> $STATUS_LOG

SUBJECT="pfSense: New user: $OWNER $NAME $EMAIL"
BODY="$OWNER
$MAC
$NAME
$EMAIL
$IP
$DHCPHOSTNAME
$DATE
$MAC_VENDOR
$PRIMARY_DEVICE
$DR_SERVER/daloradius/mng-edit.php?username=$MAC

(mail generated by script pfSense:///home/marsPortal/freeradius-integration/self-registration/captive-portal-add_user_to_radius.sh)"

# send mail in the background
$PORTALDIR/misc/send_mail.sh "$SUBJECT" "$BODY" &
