
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



INSERT INTO `perf` (`id`, `param`, `value`) VALUES (29, 'logo_img', '') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (30, 'lang_def', 'en') ON DUPLICATE KEY UPDATE `value` = `value`;

INSERT INTO `perf` (`id`, `param`, `value`) VALUES (31, 'global_msg_to', '') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (32, 'global_msg_type', 'info') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (33, 'global_msg_data', '') ON DUPLICATE KEY UPDATE `value` = `value`;
INSERT INTO `perf` (`id`, `param`, `value`) VALUES (34, 'global_msg_status', '0') ON DUPLICATE KEY UPDATE `value` = `value`;

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

