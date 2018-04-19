#!/usr/local/bin/bash

# Add new user with MAC Auth to radius

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
	# get rid of complete history of this device
	DELETE FROM radacct WHERE username = "$MAC";
	DELETE FROM radreply WHERE username = "$MAC";
	DELETE FROM radpostauth WHERE username = "$MAC";
	DELETE FROM daily_accounting_v2 WHERE username = "$MAC";

	DELETE FROM radcheck WHERE username = "$MAC";
	INSERT INTO radcheck (Username, Attribute, op, Value) VALUES ("$MAC", "Auth-Type", ":=", "Accept");
	
	DELETE FROM radusergroup WHERE username = "$MAC";
	INSERT INTO radusergroup (UserName, GroupName, priority) VALUES ("$MAC", "$GROUP",0);
	
	DELETE FROM userinfo WHERE username = "$MAC";
	INSERT INTO userinfo (username, firstname, lastname, email, department, organisation, initial_ip, hostname, registration_date, mac_vendor, notes) VALUES ("$MAC", "$FIRSTNAME", "$LASTNAME", "$EMAIL", NULL, "$COMPANY", "$IP", "$HOSTNAME", current_timestamp(), "$MAC_VENDOR", "Primary device: $PRIMARY_DEVICE");
EOF
