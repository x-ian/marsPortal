#!/usr/local/bin/bash

/bin/sleep 30

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt

/usr/bin/killall ssh
/usr/local/bin/autossh -M 0 -o "ServerAliveInterval 60" -o "ServerAliveCountMax 3" -o BatchMode=yes -R $SSH_TUNNEL_PORT:localhost:22 -N ssh-tunnel@marsgeneral.com

