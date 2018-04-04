DELETE FROM radgroupcheck WHERE groupname = 'marsPortal-Template';
INSERT INTO radgroupcheck (groupname, attribute, op, value) VALUES
('marsPortal-Template', 'mars-Input-Megabytes-Daily-Work-Hours', ':=', '250'),
('marsPortal-Template', 'mars-Output-Megabytes-Daily-Work-Hours', ':=', '250'),
('marsPortal-Template', 'mars-Input-Megabytes-Daily-Total', ':=', '500'),
('marsPortal-Template', 'mars-Output-Megabytes-Daily-Total', ':=', '500'),
('marsPortal-Template', 'mars-User-Input-Megabytes-Daily-Work-Hours', ':=', '250'),
('marsPortal-Template', 'mars-User-Output-Megabytes-Daily-Work-Hours', ':=', '250'),
('marsPortal-Template', 'mars-User-Input-Megabytes-Daily-Total', ':=', '500'),
('marsPortal-Template', 'mars-User-Output-Megabytes-Daily-Total', ':=', '500'),
('marsPortal-Template', 'mars-Max-Concurrent-Devices', ':=', '100');
DELETE FROM radgroupreply WHERE groupname = 'marsPortal-Template';
INSERT INTO radgroupreply (groupname, attribute, op, value) VALUES
('marsPortal-Template', 'Session-Timeout', ':=', '43200'),
('marsPortal-Template', 'WISPr-Bandwidth-Max-Up', ':=', '512000'),
('marsPortal-Template', 'WISPr-Bandwidth-Max-Down', ':=', '512000');

DELETE FROM radgroupreply WHERE groupname = 'marsPortal-Template-open-for-today';
INSERT INTO radgroupreply (groupname, attribute, op, value) VALUES
('marsPortal-Template-open-for-today', 'Session-Timeout', ':=', '43200'),
('marsPortal-Template-open-for-today', 'WISPr-Bandwidth-Max-Up', ':=', '512000'),
('marsPortal-Template-open-for-today', 'WISPr-Bandwidth-Max-Down', ':=', '512000');

DELETE FROM radgroupreply WHERE groupname = 'marsPortal-Template-non-work-hours';
INSERT INTO radgroupreply (groupname, attribute, op, value) VALUES
('marsPortal-Template-non-work-hours', 'Session-Timeout', ':=', '43200');

DELETE FROM radgroupcheck WHERE groupname = 'marsPortal-Template-restricted';
INSERT INTO radgroupcheck (groupname, attribute, op, value) VALUES
('marsPortal-Template-restricted', 'Auth-Type', ':=', 'Reject');
DELETE FROM radgroupreply WHERE groupname = 'marsPortal-Template-restricted';
INSERT INTO radgroupreply (groupname, attribute, op, value) VALUES
('marsPortal-Template-restricted', 'Reply-Message', ':=', 'Network currently in maintenance mode. Please try again later.');

DELETE FROM radgroupcheck WHERE groupname = 'No-Internet-Access';
INSERT INTO radgroupcheck (groupname, attribute, op, value) VALUES
('No-Internet-Access', 'Auth-Type', ':=', 'Reject');
DELETE FROM radgroupreply WHERE groupname = 'No-Internet-Access';
INSERT INTO radgroupreply (groupname, attribute, op, value) VALUES
('No-Internet-Access', 'Reply-Message', ':=', 'Your device is permanently disabled.');

DELETE FROM radgroupcheck WHERE groupname = 'Users';
DELETE FROM radgroupreply WHERE groupname = 'Users';
INSERT INTO radgroupreply (groupname, attribute, op, value) VALUES
('Users', 'Session-Timeout', ':=', '604800');


DROP TABLE IF EXISTS daily_accounting_v2;
CREATE TABLE daily_accounting_v2 (
  id int(32) NOT NULL AUTO_INCREMENT,
  username varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  day date NOT NULL,

  day_offset datetime,
  day_offset_input bigint(20) DEFAULT 0,
  day_offset_output bigint(20) DEFAULT 0,

  work_offset datetime,
  work_offset_input bigint(20) DEFAULT 0,
  work_offset_output bigint(20) DEFAULT 0,

  lunch_offset datetime,
  lunch_offset_input bigint(20) DEFAULT 0,
  lunch_offset_output bigint(20) DEFAULT 0,

  lunch_total datetime,
  lunch_total_input bigint(20) DEFAULT 0,
  lunch_total_output bigint(20) DEFAULT 0,

  work_total datetime,
  work_total_input bigint(20) DEFAULT 0,
  work_total_output bigint(20) DEFAULT 0,

  day_total datetime,
  day_total_input bigint(20) DEFAULT 0,
  day_total_output bigint(20) DEFAULT 0,

  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE daily_accounting_v2 ADD UNIQUE INDEX (username, day);
ALTER TABLE daily_accounting_v2 ADD INDEX (username);
ALTER TABLE daily_accounting_v2 ADD INDEX (day);

DROP TABLE IF EXISTS log_internet_ping;
CREATE TABLE log_internet_ping (
  id int(32) NOT NULL AUTO_INCREMENT,
  begin datetime NOT NULL,
  end datetime NOT NULL,
  
  transmitted smallint,
  received smallint,
  packet_loss varchar(10),
  rtt_avg DECIMAL(10,3) DEFAULT 0,
  
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE log_internet_ping ADD UNIQUE INDEX (begin, end);

DROP TABLE IF EXISTS log_wan_traffic;
CREATE TABLE log_wan_traffic (
  id int(32) NOT NULL AUTO_INCREMENT,
  when2 datetime NOT NULL,
  
--  rx DECIMAL(5,2),
  rx DECIMAL(12,0),
  rx_unit varchar(10),
--  tx DECIMAL(5,2),
  tx DECIMAL(12,0),
  tx_unit varchar(10),
  
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE log_wan_traffic ADD UNIQUE INDEX (when2);

DROP TABLE IF EXISTS log_wan_throughput;
CREATE TABLE log_wan_throughput (
  id int(32) NOT NULL AUTO_INCREMENT,
  at datetime NOT NULL,
  
  rx int(32),
  rx_unit varchar(10),
  tx int(32),
  tx_unit varchar(10),
  
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE log_wan_throughput ADD UNIQUE INDEX (at);

DROP TABLE IF EXISTS throughput;
CREATE TABLE throughput (
  id int(32) NOT NULL AUTO_INCREMENT,
  username varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  day date NOT NULL,
  minute_of_day smallint NOT NULL,
  time_of_day time NOT NULL,
  
  offset_input bigint(20) DEFAULT 0,
  offset_output bigint(20) DEFAULT 0,
  
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE throughput ADD UNIQUE INDEX (username, minute_of_day);
ALTER TABLE throughput ADD INDEX (time_of_day);

DROP TABLE IF EXISTS daily_accounting_v5;
CREATE TABLE daily_accounting_v5 (
  id int(32) NOT NULL AUTO_INCREMENT,
  username varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  day date NOT NULL,

  offset_input bigint(20) DEFAULT 0,
  offset_output bigint(20) DEFAULT 0,
  offset datetime,
  
  0029_input bigint(20) DEFAULT 0,
  0029_output bigint(20) DEFAULT 0,
  0059_input bigint(20) DEFAULT 0,
  0059_output bigint(20) DEFAULT 0,

  0129_input bigint(20) DEFAULT 0,
  0129_output bigint(20) DEFAULT 0,
  0159_input bigint(20) DEFAULT 0,
  0159_output bigint(20) DEFAULT 0,

  0229_input bigint(20) DEFAULT 0,
  0229_output bigint(20) DEFAULT 0,
  0259_input bigint(20) DEFAULT 0,
  0259_output bigint(20) DEFAULT 0,

  0329_input bigint(20) DEFAULT 0,
  0329_output bigint(20) DEFAULT 0,
  0359_input bigint(20) DEFAULT 0,
  0359_output bigint(20) DEFAULT 0,

  0429_input bigint(20) DEFAULT 0,
  0429_output bigint(20) DEFAULT 0,
  0459_input bigint(20) DEFAULT 0,
  0459_output bigint(20) DEFAULT 0,

  0529_input bigint(20) DEFAULT 0,
  0529_output bigint(20) DEFAULT 0,
  0559_input bigint(20) DEFAULT 0,
  0559_output bigint(20) DEFAULT 0,

  0629_input bigint(20) DEFAULT 0,
  0629_output bigint(20) DEFAULT 0,
  0659_input bigint(20) DEFAULT 0,
  0659_output bigint(20) DEFAULT 0,

  0729_input bigint(20) DEFAULT 0,
  0729_output bigint(20) DEFAULT 0,
  0759_input bigint(20) DEFAULT 0,
  0759_output bigint(20) DEFAULT 0,

  0829_input bigint(20) DEFAULT 0,
  0829_output bigint(20) DEFAULT 0,
  0859_input bigint(20) DEFAULT 0,
  0859_output bigint(20) DEFAULT 0,

  0929_input bigint(20) DEFAULT 0,
  0929_output bigint(20) DEFAULT 0,
  0959_input bigint(20) DEFAULT 0,
  0959_output bigint(20) DEFAULT 0,

  1029_input bigint(20) DEFAULT 0,
  1029_output bigint(20) DEFAULT 0,
  1059_input bigint(20) DEFAULT 0,
  1059_output bigint(20) DEFAULT 0,

  1129_input bigint(20) DEFAULT 0,
  1129_output bigint(20) DEFAULT 0,
  1159_input bigint(20) DEFAULT 0,
  1159_output bigint(20) DEFAULT 0,

  1229_input bigint(20) DEFAULT 0,
  1229_output bigint(20) DEFAULT 0,
  1259_input bigint(20) DEFAULT 0,
  1259_output bigint(20) DEFAULT 0,

  1329_input bigint(20) DEFAULT 0,
  1329_output bigint(20) DEFAULT 0,
  1359_input bigint(20) DEFAULT 0,
  1359_output bigint(20) DEFAULT 0,

  1429_input bigint(20) DEFAULT 0,
  1429_output bigint(20) DEFAULT 0,
  1459_input bigint(20) DEFAULT 0,
  1459_output bigint(20) DEFAULT 0,

  1529_input bigint(20) DEFAULT 0,
  1529_output bigint(20) DEFAULT 0,
  1559_input bigint(20) DEFAULT 0,
  1559_output bigint(20) DEFAULT 0,

  1629_input bigint(20) DEFAULT 0,
  1629_output bigint(20) DEFAULT 0,
  1659_input bigint(20) DEFAULT 0,
  1659_output bigint(20) DEFAULT 0,

  1729_input bigint(20) DEFAULT 0,
  1729_output bigint(20) DEFAULT 0,
  1759_input bigint(20) DEFAULT 0,
  1759_output bigint(20) DEFAULT 0,

  1829_input bigint(20) DEFAULT 0,
  1829_output bigint(20) DEFAULT 0,
  1859_input bigint(20) DEFAULT 0,
  1859_output bigint(20) DEFAULT 0,

  1929_input bigint(20) DEFAULT 0,
  1929_output bigint(20) DEFAULT 0,
  1959_input bigint(20) DEFAULT 0,
  1959_output bigint(20) DEFAULT 0,

  2029_input bigint(20) DEFAULT 0,
  2029_output bigint(20) DEFAULT 0,
  2059_input bigint(20) DEFAULT 0,
  2059_output bigint(20) DEFAULT 0,

  2129_input bigint(20) DEFAULT 0,
  2129_output bigint(20) DEFAULT 0,
  2159_input bigint(20) DEFAULT 0,
  2159_output bigint(20) DEFAULT 0,

  2229_input bigint(20) DEFAULT 0,
  2229_output bigint(20) DEFAULT 0,
  2259_input bigint(20) DEFAULT 0,
  2259_output bigint(20) DEFAULT 0,

  2329_input bigint(20) DEFAULT 0,
  2329_output bigint(20) DEFAULT 0,
  2359_input bigint(20) DEFAULT 0,
  2359_output bigint(20) DEFAULT 0,

  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE daily_accounting_v5 ADD UNIQUE INDEX (username, day);
ALTER TABLE daily_accounting_v5 ADD INDEX (username);
ALTER TABLE daily_accounting_v5 ADD INDEX (day);

DROP TABLE IF EXISTS userinfo;
CREATE TABLE userinfo (
  id int(32) NOT NULL AUTO_INCREMENT,
  username varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  firstname varchar(64),
  lastname varchar(64),
  email varchar(64),
  department varchar(64),
  organisation varchar(64),
  initial_ip varchar(64),
  hostname varchar(64),
  registration_date datetime,
  mac_vendor varchar(64),
  notes varchar(64),
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE userinfo ADD UNIQUE INDEX (username);

DROP TABLE IF EXISTS accounting_snapshot_1;
CREATE TABLE accounting_snapshot_1 (
  id int(32) NOT NULL AUTO_INCREMENT,
  username varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  datetime timestamp NOT NULL,
  output bigint(20) NOT NULL DEFAULT 0,
  input bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS accounting_snapshot_2;
CREATE TABLE accounting_snapshot_2 (
  id int(32) NOT NULL AUTO_INCREMENT,
  username varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  datetime timestamp NOT NULL,
  output bigint(20) NOT NULL DEFAULT 0,
  input bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS accounting_snapshot_3;
CREATE TABLE accounting_snapshot_3 (
  id int(32) NOT NULL AUTO_INCREMENT,
  username varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  datetime timestamp NOT NULL,
  output bigint(20) NOT NULL DEFAULT 0,
  input bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
