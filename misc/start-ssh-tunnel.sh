#!/usr/local/bin/bash

BASEDIR=/home/marsPortal

source $BASEDIR/config.txt

echo "Stopping all tunnels"
/usr/bin/killall ssh
/bin/sleep 120

echo "Starting new tunnel, abort with CTRL-C"
/usr/local/bin/autossh -M 0 -o "ExitOnForwardFailure=yes" -o "ConnectTimeout=10" -o "UserKnownHostsFile /dev/null" -o "StrictHostKeyChecking no" -o "ServerAliveInterval 60" -o "ServerAliveCountMax 3" -o "BatchMode=yes" -R $SSH_TUNNEL_PORT:localhost:22 -N ssh-tunnel@marsgeneral.com &
