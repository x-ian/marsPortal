#!/usr/local/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt

#MAC="00:25:00:48:60:10"
MAC="b8:e8:56:03:99:d4"

if [ $? -eq 0 ]; then
  BASEDIR=/home/marsPortal
  SUBJECT="MAC found: $MAC"
  BODY="SYSTEM ALIVE"
  $BASEDIR/misc/send_mail.sh "$SUBJECT" "$BODY"

  echo "system active "
else
  echo "not active"
fi