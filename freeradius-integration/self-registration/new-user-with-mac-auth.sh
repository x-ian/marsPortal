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

#INSERT INTO radcheck (id,Username,Attribute,op,Value) VALUES (0, '111', 'Auth-Type', ':=', 'Accept') 
#INSERT INTO radusergroup (UserName,GroupName,priority) VALUES ('111', 'Users',0) 
#INSERT INTO userinfo (id, username, firstname, lastname, email, department, company, workphone, homephone, mobilephone, address, city, state, country, zip, notes, changeuserinfo, portalloginpassword, enableportallogin, creationdate, creationby, updatedate, updateby) VALUES (0, '111', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '', '0', '2015-04-21 02:27:14', 'administrator', NULL, NULL) 

/usr/local/bin/mysql -u $MYSQL_USER -p$MYSQL_PASSWD radius <<EOF
	INSERT INTO radcheck (Username,Attribute,op,Value) VALUES ("$MAC", "Auth-Type", ":=", "Accept");
	INSERT INTO radusergroup (UserName,GroupName,priority) VALUES ("$MAC", "$GROUP",0);
EOF
