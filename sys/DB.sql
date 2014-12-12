
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

ALTER TABLE `users` ADD messages_type int(11) DEFAULT '0';