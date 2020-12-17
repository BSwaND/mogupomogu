CREATE TABLE IF NOT EXISTS `#__djcf_days_groups` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `day_id` INT(11) NOT NULL,
    `group_id` INT(11) NOT NULL,
    PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

ALTER TABLE `#__djcf_fields` ADD `class` text NOT NULL;

ALTER TABLE `#__djcf_items` ADD `last_view` timestamp NOT NULL default '0000-00-00 00:00:00';