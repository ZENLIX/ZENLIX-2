
CREATE TABLE IF NOT EXISTS `scheduler_ticket` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_init_id` int(11) DEFAULT NULL,
  `user_to_id` varchar(512) DEFAULT NULL,
  `date_create` datetime DEFAULT NULL,
  `subj` varchar(512) DEFAULT NULL,
  `msg` longtext,
  `client_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `period` varchar(32) DEFAULT NULL,
  `period_arr` varchar(512) DEFAULT NULL,
  `action_time` time DEFAULT NULL,
  `dt_start` datetime DEFAULT NULL,
  `dt_stop` datetime DEFAULT NULL,
  `last_action_dt` datetime DEFAULT NULL,
  `prio` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ticket_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) DEFAULT NULL,
  `ticket_source` varchar(128) NOT NULL DEFAULT 'web',
  `ip` varchar(64) DEFAULT NULL,
  `os` varchar(512) DEFAULT NULL,
  `browser` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ticket_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `t_type` varchar(512) NOT NULL DEFAULT 'text',
  `name` varchar(512) DEFAULT NULL,
  `placeholder` varchar(512) DEFAULT NULL,
  `value` varchar(2048) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '1',
  `hash` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ticket_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_hash` varchar(512) DEFAULT NULL,
  `field_id` int(11) DEFAULT NULL,
  `field_val` longtext,
  `field_name` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `perf` (`id`, `param`, `value`) VALUES (29, 'logo_img', '') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (30, 'lang_def', 'en') ON DUPLICATE KEY UPDATE `value` = `value`;

INSERT INTO `perf` (`id`, `param`, `value`) VALUES (31, 'global_msg_to', '') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (32, 'global_msg_type', 'info') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (33, 'global_msg_data', '') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (34, 'global_msg_status', '0') ON DUPLICATE KEY UPDATE `value` = `value`;

INSERT INTO `perf` (`id`, `param`, `value`) VALUES (35, 'ticket_last_time', 'false') ON DUPLICATE KEY UPDATE `value` = `value`;

INSERT INTO `perf` (`id`, `param`, `value`) VALUES (36, 'email_gate_status', 'false') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (37, 'email_gate_all', 'false') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (38, 'email_gate_unit_id', '') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (39, 'email_gate_user_id', '') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (40, 'email_gate_mailbox', '') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (41, 'email_gate_host', '') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (42, 'email_gate_port', '') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (43, 'email_gate_login', '') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (44, 'email_gate_pass', '') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (45, 'email_gate_filter', 'UNSEEN') ON DUPLICATE KEY UPDATE `value` = `value`;

INSERT INTO `perf` (`id`, `param`, `value`) VALUES (46, 'email_gate_cat', 'INBOX') ON DUPLICATE KEY UPDATE `value` = `value`;

#######UPDATE users.messages_type####################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='users' and column_name='messages_type'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE users ADD messages_type int(11) DEFAULT 0;"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
######################################################


#######UPDATE users.noty_layot####################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='users' and column_name='noty_layot'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE users ADD noty_layot varchar(64) NOT NULL DEFAULT 'bottomRight';"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
######################################################



#######UPDATE users.def_to_unit/user##################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='users' and column_name='def_unit_id'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE users ADD def_unit_id int(11) NOT NULL DEFAULT '0';"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='users' and column_name='def_user_id'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE users ADD def_user_id varchar(1024) NOT NULL DEFAULT '0';"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;

######################################################





CREATE TABLE IF NOT EXISTS `helper_cat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(512) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sort_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=utf8;
INSERT INTO `helper_cat` (`id`, `name`, `parent_id`, `sort_id`) VALUES (1, 'First item', 0, 0 ) ON DUPLICATE KEY UPDATE `id` = `id`;



#######UPDATE helper.cat_id############################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='helper' and column_name='cat_id'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE helper ADD cat_id int(11) NOT NULL DEFAULT 1;"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
######################################################



#######UPDATE helper.cat_id############################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='helper' and column_name='user_edit_id'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE helper ADD user_edit_id int(128) NOT NULL DEFAULT 0;"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
######################################################


#######UPDATE tickets.deadline############################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='tickets' and column_name='deadline_time'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE tickets ADD deadline_time datetime DEFAULT NULL;"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
######################################################
