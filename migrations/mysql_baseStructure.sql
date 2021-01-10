SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));

CREATE TABLE IF NOT EXISTS `podcaster_episodes` (
  `podcaster_id` VARCHAR(255) NOT NULL DEFAULT '',
  `podcast_slug` varchar(255) DEFAULT NULL,
  `podcast_name` varchar(255) DEFAULT NULL,
  `episode_slug` varchar(255) DEFAULT NULL,
  `episode_name` varchar(255) DEFAULT NULL,
  `log_date` date NOT NULL,
  `downloads` int(11) DEFAULT NULL,
  PRIMARY KEY (`podcaster_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `podcaster_feeds` (
  `podcaster_id` VARCHAR(255) NOT NULL DEFAULT '',
  `podcast_name` varchar(255) DEFAULT NULL,
  `podcast_slug` varchar(255) DEFAULT NULL,
  `log_date` date NOT NULL,
  `downloads` int(11) DEFAULT NULL,
  PRIMARY KEY (`podcaster_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `podcaster_devices` (
  `podcaster_id` VARCHAR(255) NOT NULL DEFAULT '',
  `device` varchar(255) DEFAULT NULL,
  `podcast_slug` varchar(255) DEFAULT NULL,
  `log_date` VARCHAR(255) NOT NULL,
  `downloads` int(11) DEFAULT NULL,
  PRIMARY KEY (`podcaster_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `podcaster_os` (
  `podcaster_id` VARCHAR(255) NOT NULL DEFAULT '',
  `os` varchar(255) DEFAULT NULL,
  `podcast_slug` varchar(255) DEFAULT NULL,
  `log_date` VARCHAR(255) NOT NULL,
  `downloads` int(11) DEFAULT NULL,
  PRIMARY KEY (`podcaster_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `podcaster_useragents` (
  `podcaster_id` VARCHAR(255) NOT NULL DEFAULT '',
  `useragent` varchar(255) DEFAULT NULL,
  `podcast_slug` varchar(255) DEFAULT NULL,
  `log_date` VARCHAR(255) NOT NULL,
  `downloads` int(11) DEFAULT NULL,
  PRIMARY KEY (`podcaster_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `podcaster_settings` (
  `podcaster_version` VARCHAR(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


ALTER TABLE `podcaster_episodes` CHANGE `podcast` `podcast_slug` VARCHAR(255);
ALTER TABLE `podcaster_episodes` ADD `podcast_name` VARCHAR(255) NULL DEFAULT NULL AFTER `podcast_slug`;
UPDATE `podcaster_episodes` SET `podcast_name` = `podcast_slug` WHERE `id`=`id`;

ALTER TABLE `podcaster_episodes` CHANGE `episode` `episode_slug` VARCHAR(255);
ALTER TABLE `podcaster_episodes` ADD `episode_name` VARCHAR(255) NULL DEFAULT NULL AFTER `episode_slug`;
UPDATE `podcaster_episodes` SET `episode_name` = `episode_slug` WHERE `id`=`id`;

ALTER TABLE `podcaster_episodes` CHANGE `day` `log_date` DATE;
ALTER TABLE `podcaster_episodes` ADD `podcaster_id` VARCHAR(255) NOT NULL DEFAULT '';
UPDATE `podcaster_episodes` SET `podcaster_id` = `podcast_slug` WHERE `id`=`id`;

ALTER TABLE `podcaster_feeds` CHANGE `podcast` `podcast_name` VARCHAR(255);
ALTER TABLE `podcaster_feeds` CHANGE `feed` `podcast_slug` VARCHAR(255);
ALTER TABLE `podcaster_feeds` CHANGE `day` `log_date` DATE;
ALTER TABLE `podcaster_feeds` ADD `podcaster_id` VARCHAR(255) NOT NULL DEFAULT '';
UPDATE `podcaster_feeds` SET `podcaster_id` = `podcast_slug` WHERE `id`=`id`;

ALTER TABLE `podcaster_devices` CHANGE `podcast` `podcast_slug` VARCHAR(255);

