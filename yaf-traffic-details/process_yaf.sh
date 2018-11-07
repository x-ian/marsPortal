#!/bin/bash

#mv /home/yaf/yaf-* /home/yaf_work
cd /home/yaf
/usr/bin/find . -name 'yaf-*' ! -empty -type f -exec mv {} /home/yaf_work \;
yafscii --in '/home/yaf_work/*' --out - --mac --tabular --print-header >/tmp/traffic_details_yaf.tsv
/home/marsPortal/yaf-traffic-details/ipfix-parse.py /tmp/traffic_details_yaf.tsv /var/db/mysql_secure/traffic_details.csv
mv /home/yaf_work/* /home/yaf_done
mysql -u root -pradius radius --show-warnings --execute="LOAD DATA INFILE '/var/db/mysql_secure/traffic_details.csv' IGNORE INTO TABLE traffic_details FIELDS TERMINATED BY '\t' (day,mac,remote_ip, outgoing,incoming);"
#rm -f /tmp/traffic_details_yaf.tsv /var/db/mysql_secure/traffic_details.csv

cd /tmp
# clog -f /var/log/resolver.log | grep dnsmasq | grep -i 'reply\|cached' | grep -v "NXDOMAIN" | grep -Eo 'reply.*[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}|cached.*[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}' | grep "is" | sed 's/reply //g' | sed 's/cached //g' | sed 's/is //g' | tr " " "\t" > ip_registry
unbound-control -c /var/unbound/unbound.conf dump_cache > /tmp/dns
grep 'IN'$'\t''A'$'\t' dns | cut -f1 -f5 > /var/db/mysql_secure/ip_registry
mysql -u root -pradius radius --show-warnings --execute="LOAD DATA INFILE '/var/db/mysql_secure/ip_registry' IGNORE INTO TABLE ip_registry FIELDS TERMINATED BY '\t' (reverse_dns,ip);"
#rm -f /var/db/mysql_secure/ip_registry /tmp/dns 

#https://zonefiles.io/all-registered-domains/
