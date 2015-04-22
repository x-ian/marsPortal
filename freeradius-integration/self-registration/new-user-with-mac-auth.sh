#!/usr/local/bin/bash

# Add new user with MAC Auth to DR

BASEDIR=`dirname $0`
PORTALDIR=/home/marsPortal
source $PORTALDIR/config.txt

MAC=$1
FIRSTNAME=$2
LASTNAME=$3
EMAIL=$4
COMPANY=$5
GROUP="$6"
IP=$7
HOSTNAME=$8
MAC_VENDOR=$9
PRIMARY_DEVICE=${10}

/usr/local/bin/mysql -u $MYSQL_USER -p$MYSQL_PASSWD radius <<EOF
	-- TODO ensure uniqueness
	INSERT INTO radcheck (Username, Attribute, op, Value) VALUES ("$MAC", "Auth-Type", ":=", "Accept");
	INSERT INTO radusergroup (UserName, GroupName, priority) VALUES ("$MAC", "$GROUP",0);
	INSERT INTO userinfo (username, firstname, lastname, email, department, organisation, initial_ip, hostname, registration_date, mac_vendor, notes) VALUES ("$MAC", $FIRSTNAME, "$LASTNAME", "$EMAIL", "", "$ORGANISATION", "$IP", "$HOSTNAME", "$MAC_VENDOR", "$NOTES");
EOF
