


CREATE TABLE IF NOT EXISTS `user_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `field_id` int(11) DEFAULT NULL,
  `field_val` longtext,
  `field_name` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `user_files` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `original_name` varchar(512) DEFAULT NULL,
  `file_hash` varchar(512) DEFAULT NULL,
  `file_type` varchar(512) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `file_ext` varchar(12) DEFAULT NULL,
  `obj_type` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `user_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `t_type` varchar(512) NOT NULL DEFAULT 'text',
  `name` varchar(512) DEFAULT NULL,
  `placeholder` varchar(512) DEFAULT NULL,
  `value` varchar(2048) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '1',
  `hash` varchar(512) DEFAULT NULL,
  `for_client` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user_devices` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `device_token` varchar(2048) DEFAULT NULL,
  `dt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `calendar` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(2048) DEFAULT NULL,
  `dtStart` varchar(64) DEFAULT NULL,
  `dtStop` varchar(64) DEFAULT NULL,
  `allday` varchar(64) NOT NULL DEFAULT 'false',
  `backgroundColor` varchar(64) DEFAULT NULL,
  `borderColor` varchar(64) DEFAULT NULL,
  `description` varchar(1024) NOT NULL DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `uniq_hash` varchar(512) DEFAULT NULL,
  `visibility` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `sla_plans` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(512) DEFAULT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `sort_id` int(11) DEFAULT NULL,
  `uniq_id` varchar(1024) DEFAULT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  `reaction_time_def` int(11) NOT NULL DEFAULT '0',
  `reaction_time_low_prio` int(11) NOT NULL DEFAULT '0',
  `reaction_time_high_prio` int(11) NOT NULL DEFAULT '0',
  `work_time_def` int(11) NOT NULL DEFAULT '0',
  `work_time_low_prio` int(11) NOT NULL DEFAULT '0',
  `work_time_high_prio` int(11) NOT NULL DEFAULT '0',
  `deadline_time_def` int(11) NOT NULL DEFAULT '0',
  `deadline_time_low_prio` int(11) NOT NULL DEFAULT '0',
  `deadline_time_high_prio` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;




CREATE TABLE IF NOT EXISTS `portal_manual_cat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(512) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sort_id` int(11) DEFAULT NULL,
  `main` int(11) NOT NULL DEFAULT '0',
  `msg` longtext,
  `uniq_id` varchar(512) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  `dt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `portal_manual_qa` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(2048) DEFAULT NULL,
  `answer` longtext,
  `parent_id` int(11) DEFAULT NULL,
  `sort_id` int(11) DEFAULT NULL,
  `uniq_id` varchar(512) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `dt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `portal_news` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subj` varchar(2048) DEFAULT NULL,
  `msg` longtext,
  `title` varchar(2048) DEFAULT NULL,
  `author_id` int(11) NOT NULL DEFAULT '0',
  `dt` datetime DEFAULT NULL,
  `uniq_id` varchar(128) DEFAULT NULL,
  `rates` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `portal_posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subj` varchar(2048) DEFAULT NULL,
  `msg` longtext,
  `type` int(11) NOT NULL DEFAULT '1',
  `author_id` int(11) NOT NULL DEFAULT '0',
  `dt` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `uniq_id` varchar(128) DEFAULT NULL,
  `cat_id` int(11) NOT NULL DEFAULT '0',
  `rates` int(11) NOT NULL DEFAULT '0',
  `official` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `portal_todo` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(2048) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sort_id` int(11) DEFAULT NULL,
  `uniq_id` varchar(512) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `res_num` int(11) NOT NULL DEFAULT '0',
  `dt` datetime DEFAULT NULL,
  `is_success` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `portal_versions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subj` varchar(2048) DEFAULT NULL,
  `msg` longtext,
  `title` varchar(2048) DEFAULT NULL,
  `author_id` int(11) NOT NULL DEFAULT '0',
  `dt` datetime DEFAULT NULL,
  `uniq_id` varchar(128) DEFAULT NULL,
  `rates` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `post_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `p_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment_text` longtext,
  `dt` datetime DEFAULT NULL,
  `official` int(11) NOT NULL DEFAULT '0',
  `uniq_hash` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `post_files` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_hash` varchar(512) DEFAULT NULL,
  `original_name` varchar(512) DEFAULT NULL,
  `file_hash` varchar(512) DEFAULT NULL,
  `file_type` varchar(512) DEFAULT NULL,
  `file_size` varchar(512) DEFAULT NULL,
  `file_ext` varchar(512) DEFAULT NULL,
  `p_type` int(11) NOT NULL DEFAULT '0',
  `is_tmp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `post_likes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  `likes` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;





CREATE TABLE IF NOT EXISTS `users_notify` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `mail` varchar(2048) DEFAULT NULL,
  `pb` varchar(2048) DEFAULT NULL,
  `sms` varchar(2048) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;






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
  `for_client` int(11) NOT NULL DEFAULT '0',
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

INSERT INTO `perf` (`id`, `param`, `value`) VALUES (47, 'portal_status', 'false') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (48, 'portal_msg_type', 'info') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (49, 'portal_msg_title', 'Info') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (50, 'portal_msg_text', 'Some text') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (51, 'portal_msg_status', 'true') ON DUPLICATE KEY UPDATE `value` = `value`;

INSERT INTO `perf` (`id`, `param`, `value`) VALUES (52, 'portal_box_version_n', '2.x') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (53, 'portal_box_version_text', 'Some text') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (54, 'portal_box_version_icon', 'icon-svg') ON DUPLICATE KEY UPDATE `value` = `value`;

INSERT INTO `perf` (`id`, `param`, `value`) VALUES (55, 'mailers_subj', '') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (56, 'mailers_text', '') ON DUPLICATE KEY UPDATE `value` = `value`;

INSERT INTO `perf` (`id`, `param`, `value`) VALUES (57, 'allow_forgot', 'true') ON DUPLICATE KEY UPDATE `value` = `value`;

INSERT INTO `perf` (`id`, `param`, `value`) VALUES (58, 'sla_system', 'false') ON DUPLICATE KEY UPDATE `value` = `value`;

INSERT INTO `perf` (`id`, `param`, `value`) VALUES (59, 'portal_posts_mail_users', 'false') ON DUPLICATE KEY UPDATE `value` = `value`;

INSERT INTO `perf` (`id`, `param`, `value`) VALUES (60, 'email_gate_connect_param', '/imap/ssl') ON DUPLICATE KEY UPDATE `value` = `value`;

INSERT INTO `perf` (`id`, `param`, `value`) VALUES (61, 'smsc_login', '') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (62, 'smsc_pass', '') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (63, 'smsc_active', 'false') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (64, 'smsc_list_action', 'ticket_create,ticket_refer,ticket_comment,ticket_lock,ticket_unlock,ticket_ok,ticket_no_ok') ON DUPLICATE KEY UPDATE `value` = `value`;

INSERT INTO `perf` (`id`, `param`, `value`) VALUES (65, 'api_status', 'true') ON DUPLICATE KEY UPDATE `value` = `value`;

INSERT INTO `perf` (`id`, `param`, `value`) VALUES (66, 'twig_cache', 'false') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (67, 'pb_active', 'false') ON DUPLICATE KEY UPDATE `value` = `value`;

#######UPDATE perf.value####################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='perf' and column_name='value'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE perf MODIFY value longtext;"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
######################################################

  #`status` int(11) NOT NULL DEFAULT '1',
  #`main_user` int(11) DEFAULT NULL,

#######UPDATE perf.value####################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='units' and column_name='status'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE units ADD status INT(11) NOT NULL DEFAULT 1;"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
######################################################



#######UPDATE perf.value####################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='units' and column_name='main_user'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE units ADD main_user INT(11) DEFAULT NULL;"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
######################################################


#`client_request_status` int(11) NOT NULL DEFAULT '0',
#######UPDATE messages.client_request_status####################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='messages' and column_name='client_request_status'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE messages ADD client_request_status INT(11) NOT NULL DEFAULT 0;"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
######################################################



#######UPDATE notification_pool.delivers_id####################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='notification_pool' and column_name='delivers_id'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE notification_pool MODIFY delivers_id longtext;"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
######################################################

#######UPDATE notification_msg_pool.delivers_id####################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='notification_msg_pool' and column_name='delivers_id'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE notification_msg_pool MODIFY delivers_id longtext;"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
######################################################



#######UPDATE notification_msg_pool.session_id####################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='notification_msg_pool' and column_name='session_id'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE notification_msg_pool ADD session_id varchar(512) DEFAULT NULL;"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
######################################################


#######UPDATE notification_msg_pool.user_init####################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='notification_msg_pool' and column_name='user_init'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE notification_msg_pool ADD user_init varchar(512) DEFAULT NULL;"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
######################################################




#######UPDATE files.obj_type####################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='files' and column_name='obj_type'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE files ADD obj_type int(11) NOT NULL DEFAULT 0;"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
######################################################

#######UPDATE subj.uniq_id####################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='subj' and column_name='uniq_id'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE subj ADD uniq_id varchar(1024) DEFAULT NULL;"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
######################################################


#######UPDATE subj.type####################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='subj' and column_name='type'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE subj ADD type int(11) DEFAULT 0;"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
######################################################

#######UPDATE subj.sort_id####################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='subj' and column_name='sort_id'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE subj ADD sort_id int(11) DEFAULT 0;"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
######################################################

#######UPDATE subj.parent_id####################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='subj' and column_name='parent_id'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE subj ADD parent_id int(11) DEFAULT 0;"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
######################################################



#######UPDATE users.mob####################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='users' and column_name='mob'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE users ADD mob varchar(64) NOT NULL DEFAULT 0;"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
######################################################

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


#######UPDATE tickets.sla_plan_id####################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='tickets' and column_name='sla_plan_id'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE tickets ADD sla_plan_id int(11) NOT NULL DEFAULT 0;"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
######################################################




#######UPDATE ticket_fields.for_client####################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='ticket_fields' and column_name='for_client'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE ticket_fields ADD for_client int(11) NOT NULL DEFAULT 0;"
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
######################################################



#######UPDATE users.api_key####################
SET @sql = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        table_name='users' and column_name='api_key'
    ) > 0,
    "SELECT 0",
    "ALTER TABLE users ADD api_key varchar(1024) DEFAULT NULL;"
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
