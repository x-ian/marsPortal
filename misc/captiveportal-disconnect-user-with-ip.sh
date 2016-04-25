#!/usr/local/bin/bash

BASEDIR=/home/marsPortal
IP=$1

MAC=`$BASEDIR/misc/resolve_mac_address.sh $IP`

$BASEDIR/misc/captiveportal-disconnect-user.sh $MAC