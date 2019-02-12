DROP TABLE IF EXISTS `podcaster_episodes`;

CREATE TABLE `podcaster_episodes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `podcast` varchar(255) DEFAULT NULL,
  `episode` varchar(255) DEFAULT NULL,
  `day` date NOT NULL,
  `downloads` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `podcaster_feeds`;

CREATE TABLE `podcaster_feeds` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `podcast` varchar(255) DEFAULT NULL,
  `feed` varchar(255) DEFAULT NULL,
  `day` date NOT NULL,
  `downloads` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
