#!/usr/local/bin/bash

BASEDIR=/home/marsPortal

MAC=$1

MAC_FIRST_DIGITS=$(echo $MAC | sed -e 's/://g' | cut -c 1-6 | awk '{print toupper($0)}')
MAC_VENDOR=$(grep "(base 16)" $BASEDIR/misc/ieee_oui.txt | grep $MAC_FIRST_DIGITS | awk -F"\t" '{ print $3 }' | sed -e 's/ /_/g')

echo $MAC_VENDOR