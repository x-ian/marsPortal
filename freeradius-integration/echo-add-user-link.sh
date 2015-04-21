#!/bin/bash

BASEDIR=/home/marsPortal
source $BASEDIR/config.txt

IP=$1
REDIRURL=$2

MAC=$($BASEDIR/freeradius-integration/resolve_mac_address.sh $IP)

echo "<a href=http://localhost/mars/add-user/add-user.php?mac=`echo $MAC`&redirurl=`echo $REDIRURL`>+</a>"
