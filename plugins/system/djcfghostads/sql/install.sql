
CREATE TABLE IF NOT EXISTS `#__djcf_ghostads` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `item_id` INT(11) NOT NULL,
  `cat_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `alias` VARCHAR(255) NOT NULL,
  `date_start` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_exp` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access_view` int(11) NOT NULL DEFAULT '0',
  `blocked` int(11) NOT NULL,
  `content` text NULL,
  `deleted` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_by` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;
