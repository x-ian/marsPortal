#!/usr/local/bin/bash

# there might be a clevererer way, maybe use mac straight from portal

IP=$1

# skip the ping and see if we can speed it up and still have it working reliably
#MAC=$(ping -t 2 $IP > /dev/null; arp $IP | awk '{print $4}' | sed s/://g)
#MAC=$(arp $IP | awk '{print $4}' | sed s/://g) # for unformatted MACs
#MAC_RAW=$(/usr/sbin/arp $IP)
#/sbin/ping -t 1 $IP > /dev/null
MAC=$(/usr/sbin/arp $IP | /usr/bin/awk '{print $4}') # for MAC with colons

#echo "$IP $MAC_RAW" >> /home/resolve_mac_Address

echo $MAC
