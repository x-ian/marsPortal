#!/bin/bash

BASEDIR=/home/marsPortal
source $BASEDIR/config.txt

IP=$1
REDIRURL=$2

MAC=$($BASEDIR/daloradius-integration/resolve_mac_address.sh $IP)

echo "<a href=`echo $DR_SERVER`mars/admin/add-user.php?mac=`echo $MAC`&redirurl=`echo $REDIRURL`>+</a>"
