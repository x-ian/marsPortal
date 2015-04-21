#!/bin/bash

BASEDIR=/home/marsPortal
source $BASEDIR/config.txt

IP=$1

MAC=$($BASEDIR/daloradius-integration/resolve_mac_address.sh $IP)

echo "http://localhost/mars/user_with_volume.php?username=`echo $MAC`"
