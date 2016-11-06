#!/bin/bash

BASEDIR=/home/marsPortal
source $BASEDIR/config.txt

IP=$1

MAC=$($BASEDIR/misc/resolve_mac_address.sh $IP)

echo "<a href=$PF_SERVER/mars/device_with_volume.php?username=`echo $MAC`>Data usage</a>"
