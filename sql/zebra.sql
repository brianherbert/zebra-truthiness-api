CREATE TABLE `lies` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `hash` char(32) NOT NULL DEFAULT '',
  `url` varchar(2083) NOT NULL DEFAULT '',
  `domain` varchar(300) NOT NULL DEFAULT '',
  `title` varchar(300) DEFAULT NULL,
  `lie` text NOT NULL,
  `context` text,
  `source` varchar(2083) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;