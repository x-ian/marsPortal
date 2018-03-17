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
