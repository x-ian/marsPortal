#!/bin/bash

BASEDIR=/home/marsPortal
source $BASEDIR/config.txt

IP=$1
REDIRURL=$2

MAC=$($BASEDIR/misc/resolve_mac_address.sh $IP)
MAC_VENDOR=$($BASEDIR/misc/resolve_mac_vendor.sh $MAC)
HOSTNAME=$($BASEDIR/misc/resolve_hostname.sh $IP)

echo "<a href=http://$PF_IP/mars/add-user/add-user.php?mac=`echo $MAC`&redirurl=`echo $REDIRURL`>+</a>"
