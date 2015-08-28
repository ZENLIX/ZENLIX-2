# ************************************************************
# Sequel Pro SQL dump
# Версия 3408
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Адрес: 127.0.0.1 (MySQL 5.6.16)
# Схема: hd_prod
# Время создания: 2014-10-12 15:21:40 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Дамп таблицы approved_info
# ------------------------------------------------------------


DROP TABLE IF EXISTS `portal_manual_cat`;
CREATE TABLE `portal_manual_cat` (
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
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `user_files`;
CREATE TABLE `user_files` (
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


DROP TABLE IF EXISTS `portal_manual_qa`;
CREATE TABLE `portal_manual_qa` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(2048) DEFAULT NULL,
  `answer` longtext,
  `parent_id` int(11) DEFAULT NULL,
  `sort_id` int(11) DEFAULT NULL,
  `uniq_id` varchar(512) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `dt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `portal_news`;
CREATE TABLE `portal_news` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subj` varchar(2048) DEFAULT NULL,
  `msg` longtext,
  `title` varchar(2048) DEFAULT NULL,
  `author_id` int(11) NOT NULL DEFAULT '0',
  `dt` datetime DEFAULT NULL,
  `uniq_id` varchar(128) DEFAULT NULL,
  `rates` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `portal_posts`;
CREATE TABLE `portal_posts` (
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
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `portal_todo`;
CREATE TABLE `portal_todo` (
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
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `portal_versions`;
CREATE TABLE `portal_versions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subj` varchar(2048) DEFAULT NULL,
  `msg` longtext,
  `title` varchar(2048) DEFAULT NULL,
  `author_id` int(11) NOT NULL DEFAULT '0',
  `dt` datetime DEFAULT NULL,
  `uniq_id` varchar(128) DEFAULT NULL,
  `rates` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `post_comments`;
CREATE TABLE `post_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `p_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment_text` longtext,
  `dt` datetime DEFAULT NULL,
  `official` int(11) NOT NULL DEFAULT '0',
  `uniq_hash` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `post_files`;
CREATE TABLE `post_files` (
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
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `post_likes`;
CREATE TABLE `post_likes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  `likes` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `approved_info`;

CREATE TABLE `approved_info` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `fio` varchar(256) DEFAULT NULL,
  `login` varchar(256) DEFAULT NULL,
  `tel` varchar(256) DEFAULT NULL,
  `unit_desc` varchar(1024) DEFAULT NULL,
  `adr` varchar(256) DEFAULT NULL,
  `email` varchar(256) DEFAULT NULL,
  `posada` varchar(256) DEFAULT NULL,
  `user_from` int(11) DEFAULT NULL,
  `date_app` datetime DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `skype` varchar(256) DEFAULT NULL,
  `type_op` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `scheduler_ticket`;
CREATE TABLE `scheduler_ticket` (
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


DROP TABLE IF EXISTS `calendar`;
CREATE TABLE `calendar` (
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






DROP TABLE IF EXISTS `messages`;

CREATE TABLE `messages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_from` int(11) DEFAULT NULL,
  `user_to` int(11) DEFAULT NULL,
  `date_op` datetime DEFAULT NULL,
  `msg` varchar(4096) DEFAULT '',
  `type_msg` varchar(128) NOT NULL DEFAULT 'main',
  `is_read` int(11) NOT NULL DEFAULT '0',
  `client_request_status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ticket_data`;
CREATE TABLE `ticket_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_hash` varchar(512) DEFAULT NULL,
  `field_id` int(11) DEFAULT NULL,
  `field_val` longtext,
  `field_name` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ticket_fields`;
CREATE TABLE `ticket_fields` (
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

# Дамп таблицы comments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `comments`;

CREATE TABLE `comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `t_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment_text` varchar(2048) DEFAULT NULL,
  `dt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Дамп таблицы deps
# ------------------------------------------------------------

DROP TABLE IF EXISTS `deps`;

CREATE TABLE `deps` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(1024) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `deps` WRITE;
/*!40000 ALTER TABLE `deps` DISABLE KEYS */;

INSERT INTO `deps` (`id`, `name`, `status`)
VALUES
	(1,'Web-designers dep',1),
	(2,'Hosting dep',1),
	(3,'SEO',0),
	(4,'Network security',1),
	(5,'User support',1);

/*!40000 ALTER TABLE `deps` ENABLE KEYS */;
UNLOCK TABLES;


# Дамп таблицы files
# ------------------------------------------------------------

DROP TABLE IF EXISTS `files`;

CREATE TABLE `files` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_hash` varchar(512) DEFAULT NULL,
  `original_name` varchar(512) DEFAULT NULL,
  `file_hash` varchar(512) DEFAULT NULL,
  `file_type` varchar(512) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `file_ext` varchar(12) DEFAULT NULL,
  `obj_type` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Дамп таблицы helper
# ------------------------------------------------------------

DROP TABLE IF EXISTS `helper`;

CREATE TABLE `helper` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_init_id` int(128) DEFAULT NULL,
  `unit_to_id` varchar(11) DEFAULT NULL,
  `dt` datetime DEFAULT NULL,
  `title` varchar(1024) DEFAULT NULL,
  `message` longtext,
  `hashname` varchar(512) DEFAULT NULL,
  `client_flag` int(11) NOT NULL DEFAULT '0',
  `cat_id`  int(11) NOT NULL DEFAULT '1',
  `user_edit_id` int(128) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Дамп таблицы notes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notes`;

CREATE TABLE `notes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `hashname` varchar(512) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` longtext,
  `dt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_op` datetime DEFAULT NULL,
  `msg` varchar(512) CHARACTER SET latin1 DEFAULT NULL,
  `init_user_id` int(11) DEFAULT NULL,
  `target_user` varchar(128) DEFAULT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `notification_msg_pool`;

CREATE TABLE `notification_msg_pool` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `delivers_id` longtext,
  `type_op` varchar(512) DEFAULT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  `dt` datetime DEFAULT NULL,
  `chat_msg_id` int(11) DEFAULT NULL,
  `session_id` varchar(512) DEFAULT NULL,
  `user_init` varchar(512) DEFAULT NULL,  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



# Дамп таблицы notification_pool
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notification_pool`;

CREATE TABLE `notification_pool` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `delivers_id` longtext,
  `status` int(11) NOT NULL DEFAULT '0',
  `type_op` varchar(512) DEFAULT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  `dt` datetime DEFAULT NULL,
  `chat_msg_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Дамп таблицы perf
# ------------------------------------------------------------

DROP TABLE IF EXISTS `perf`;

CREATE TABLE `perf` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `param` varchar(512) NOT NULL DEFAULT '',
  `value` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `perf` WRITE;
/*!40000 ALTER TABLE `perf` DISABLE KEYS */;

INSERT INTO `perf` (`id`, `param`, `value`)
VALUES
	(1,'title_header','ZENLIX'),
	(2,'hostname','localhost/web/ZENLIX/'),
	(3,'mail','info@it.domain'),
	(4,'days2arch','3'),
	(5,'name_of_firm','ZENLIX'),
	(6,'fix_subj','true'),
	(7,'first_login','false'),
	(8,'file_uploads','true'),
	(9,'debug_mode','false'),
	(10,'mail_active','true'),
	(11,'mail_host','smtp.gmail.com'),
	(12,'mail_port','587'),
	(13,'mail_auth','true'),
	(14,'mail_auth_type','tls'),
	(15,'mail_username','it@mail.gmail'),
	(16,'mail_password','pass'),
	(17,'mail_from','it@mail.gmail'),
	(18,'mail_debug','false'),
	(19,'mail_type','sendmail'),
	(20,'file_types','gif|jpe?g|png|doc|xls|rtf|pdf|zip|rar|bmp|docx|xlsx|jpeg|jpg'),
	(21,'file_size','2097152'),
	(22,'pb_api','api'),
	(23,'ldap_ip','0.0.0.0'),
	(24,'ldap_domain','ldap.local'),
	(25,'version','2.95'),
	(26,'node_port','http://localhost:3001/'),
  (27,'time_zone','Europe/Kiev'),
  (28,'allow_register','true'),
  (29, 'logo_img', ''),
  (30, 'lang_def', 'en'),
  (31, 'global_msg_to', ''),
  (32, 'global_msg_type', 'info'),
  (33, 'global_msg_data', ''),
  (34, 'global_msg_status', '0'),
  (35, 'ticket_last_time', 'false'),
  (36, 'email_gate_status', 'false'),
  (37, 'email_gate_all', 'false'),
  (38, 'email_gate_unit_id', ''),
  (39, 'email_gate_user_id', ''),
  (40, 'email_gate_mailbox', ''),
  (41, 'email_gate_host', ''),
  (42, 'email_gate_port', ''),
  (43, 'email_gate_login', ''),
  (44, 'email_gate_pass', ''),
  (45, 'email_gate_filter', 'UNSEEN'),
  (46, 'email_gate_cat', 'INBOX'),
  (47, 'portal_status', 'false'),
  (48, 'portal_msg_type', 'info'),
  (49, 'portal_msg_title', 'Info'),
  (50, 'portal_msg_text', 'Some text'),
  (51, 'portal_msg_status', 'true'),
  (52, 'portal_box_version_n', '2.x'),
  (53, 'portal_box_version_text', 'Some Text'),
  (54, 'portal_box_version_icon', 'icon-svg'),
  (55, 'mailers_subj', ''),
  (56, 'mailers_text', ''),
  (57, 'allow_forgot', 'true'),
  (58, 'sla_system', 'false'),
  (59, 'portal_posts_mail_users', 'false'),
  (60, 'email_gate_connect_param', '/imap/ssl'),
  (61, 'smsc_login', ''),
  (62, 'smsc_pass', ''),
  (63, 'smsc_active', 'false'),
  (64, 'smsc_list_action', 'ticket_create,ticket_refer,ticket_comment,ticket_lock,ticket_unlock,ticket_ok,ticket_no_ok'),
  (65, 'api_status', 'true'),
  (66, 'twig_cache', 'false'),
  (67, 'pb_active', 'false');


/*!40000 ALTER TABLE `perf` ENABLE KEYS */;
UNLOCK TABLES;


DROP TABLE IF EXISTS `helper_cat`;
CREATE TABLE `helper_cat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(512) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sort_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
LOCK TABLES `helper_cat` WRITE;
INSERT INTO `helper_cat` (`id`, `name`, `parent_id`, `sort_id`) VALUES (1, 'First item', 0, 0 );
UNLOCK TABLES;
# Дамп таблицы posada
# ------------------------------------------------------------

DROP TABLE IF EXISTS `posada`;

CREATE TABLE `posada` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `posada` WRITE;
/*!40000 ALTER TABLE `posada` DISABLE KEYS */;

INSERT INTO `posada` (`id`, `name`)
VALUES
	(1,'administrator'),
	(2,'coordinator');

/*!40000 ALTER TABLE `posada` ENABLE KEYS */;
UNLOCK TABLES;


DROP TABLE IF EXISTS `ticket_info`;

CREATE TABLE `ticket_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) DEFAULT NULL,
  `ticket_source` varchar(128) NOT NULL DEFAULT 'web',
  `ip` varchar(64) DEFAULT NULL,
  `os` varchar(512) DEFAULT NULL,
  `browser` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




# Дамп таблицы subj
# ------------------------------------------------------------

DROP TABLE IF EXISTS `subj`;

CREATE TABLE `subj` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(512) DEFAULT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `sort_id` int(11) DEFAULT NULL,
  `uniq_id` varchar(1024) DEFAULT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `subj` WRITE;
/*!40000 ALTER TABLE `subj` DISABLE KEYS */;

INSERT INTO `subj` (`id`, `name`)
VALUES
	(1,'Systems'),
	(2,'Internet and Local Network'),
	(3,'IP-phone'),
	(4,'etc'),
	(5,'Computers'),
	(6,'Printers and Scanners'),
	(7,'Video cameras'),
	(8,'Software installing');

/*!40000 ALTER TABLE `subj` ENABLE KEYS */;
UNLOCK TABLES;



DROP TABLE IF EXISTS `user_data`;
CREATE TABLE `user_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `field_id` int(11) DEFAULT NULL,
  `field_val` longtext,
  `field_name` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `user_fields`;
CREATE TABLE `user_fields` (
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


# Дамп таблицы ticket_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ticket_log`;

CREATE TABLE `ticket_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_op` datetime DEFAULT NULL,
  `msg` varchar(512) CHARACTER SET latin1 DEFAULT NULL,
  `init_user_id` int(11) DEFAULT NULL,
  `to_user_id` varchar(128) DEFAULT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  `to_unit_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sla_plans`;

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

# Дамп таблицы tickets
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tickets`;

CREATE TABLE `tickets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_init_id` int(11) DEFAULT NULL,
  `user_to_id` varchar(512) DEFAULT NULL,
  `date_create` datetime DEFAULT NULL,
  `subj` varchar(512) DEFAULT NULL,
  `msg` longtext,
  `client_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `hash_name` varchar(512) DEFAULT NULL,
  `comment` varchar(1024) DEFAULT NULL,
  `arch` int(11) DEFAULT '0',
  `is_read` int(11) DEFAULT '0',
  `lock_by` int(11) DEFAULT '0',
  `last_edit` datetime DEFAULT NULL,
  `ok_by` int(11) DEFAULT '0',
  `prio` int(4) NOT NULL DEFAULT '0',
  `ok_date` datetime DEFAULT NULL,
  `last_update` datetime DEFAULT NULL,
  `deadline_time` datetime DEFAULT NULL,
  `sla_plan_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Дамп таблицы units
# ------------------------------------------------------------

DROP TABLE IF EXISTS `units`;

CREATE TABLE `units` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(512) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `main_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `units` WRITE;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;

INSERT INTO `units` (`id`, `name`)
VALUES
	(1,'Company A'),
	(2,'Company B');

/*!40000 ALTER TABLE `units` ENABLE KEYS */;
UNLOCK TABLES;


DROP TABLE IF EXISTS `users_notify`;
CREATE TABLE `users_notify` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `mail` varchar(2048) DEFAULT NULL,
  `pb` varchar(2048) DEFAULT NULL,
  `sms` varchar(2048) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Дамп таблицы users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_devices`;
CREATE TABLE `user_devices` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `device_token` varchar(2048) DEFAULT NULL,
  `dt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fio` varchar(512) DEFAULT NULL,
  `login` varchar(64) NOT NULL DEFAULT '',
  `pass` varchar(64) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '1',
  `priv` int(11) DEFAULT '0',
  `unit` varchar(512) NOT NULL DEFAULT '0',
  `is_admin` int(4) NOT NULL DEFAULT '0',
  `is_client` int(4) NOT NULL DEFAULT '0',
  `email` varchar(128) DEFAULT NULL,
  `messages` varchar(2048) NOT NULL DEFAULT '',
  `lang` varchar(11) NOT NULL DEFAULT 'ru',
  `priv_add_client` int(11) NOT NULL DEFAULT '1',
  `priv_edit_client` int(11) NOT NULL DEFAULT '1',
  `last_time` datetime DEFAULT NULL,
  `ldap_key` int(11) NOT NULL DEFAULT '0',
  `pb` varchar(512) DEFAULT NULL,
  `messages_title` varchar(2048) NOT NULL DEFAULT '',
  `usr_img` varchar(512) DEFAULT NULL,
  `uniq_id` varchar(512) DEFAULT NULL,
  `posada` varchar(512) DEFAULT NULL,
  `tel` varchar(512) DEFAULT NULL,
  `skype` varchar(512) DEFAULT NULL,
  `unit_desc` varchar(1024) DEFAULT NULL,
  `adr` varchar(1024) DEFAULT NULL,
  `messages_type` int(11) DEFAULT '0',
  `noty_layot` varchar(64) NOT NULL DEFAULT 'bottomRight',
  `def_unit_id` int(11) NOT NULL DEFAULT '0',
  `def_user_id` varchar(1024) NOT NULL DEFAULT '0',
  `api_key` varchar(1024) DEFAULT NULL,
  `mob` varchar(64) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `fio`, `login`, `pass`, `status`, `priv`, `unit`, `is_admin`, `is_client`, `email`, `messages`, `lang`, `priv_add_client`, `priv_edit_client`, `last_time`, `ldap_key`, `pb`, `messages_title`, `usr_img`, `uniq_id`, `posada`, `tel`, `skype`, `unit_desc`, `adr`)
VALUES
	(1, 'System Account', 'system', '81dc9bdb52d04dc20036dbd8313ed055', 1, 2, '1,2,3', 8, 0, '', 'It is necessary that to know who it serves and there is no duplicate perform the same task.', 'ru', 1, 1, '2014-10-23 15:23:49', 0, '', 'Please, dont forget to lock your tickets!', '', '7371a131b959f3527cbde59f0e5caf96', 'administrator', '', '', '', '');


/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
