SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));

CREATE TABLE IF NOT EXISTS `podcaster_devices` (
  `podcaster_id` varchar(255) NOT NULL DEFAULT '',
  `device` varchar(255) DEFAULT NULL,
  `podcast_slug` varchar(255) DEFAULT NULL,
  `log_date` varchar(255) NOT NULL,
  `downloads` int(11) DEFAULT NULL,
  PRIMARY KEY (`podcaster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `podcaster_episodes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `podcast_slug` varchar(255) DEFAULT NULL,
  `podcast_name` varchar(255) DEFAULT NULL,
  `episode_slug` varchar(255) DEFAULT NULL,
  `episode_name` varchar(255) DEFAULT NULL,
  `log_date` date DEFAULT NULL,
  `downloads` int(11) DEFAULT NULL,
  `podcaster_id` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15265 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `podcaster_feeds` (
  `podcaster_id` varchar(255) NOT NULL DEFAULT '',
  `podcast_name` varchar(255) DEFAULT NULL,
  `podcast_slug` varchar(255) DEFAULT NULL,
  `log_date` date NOT NULL,
  `downloads` int(11) DEFAULT NULL,
  PRIMARY KEY (`podcaster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `podcaster_os` (
  `podcaster_id` varchar(255) NOT NULL DEFAULT '',
  `os` varchar(255) DEFAULT NULL,
  `podcast_slug` varchar(255) DEFAULT NULL,
  `log_date` varchar(255) NOT NULL,
  `downloads` int(11) DEFAULT NULL,
  PRIMARY KEY (`podcaster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `podcaster_settings` (
  `podcaster_version` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `podcaster_useragents` (
  `podcaster_id` varchar(255) NOT NULL DEFAULT '',
  `useragent` varchar(255) DEFAULT NULL,
  `podcast_slug` varchar(255) DEFAULT NULL,
  `log_date` varchar(255) NOT NULL,
  `downloads` int(11) DEFAULT NULL,
  PRIMARY KEY (`podcaster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS migrations (
    version varchar(100) DEFAULT NULL
);