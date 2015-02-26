INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('iphone', 'iPhone API', 'iPhone API', '4.0.0', 1, 'extra');

CREATE TABLE `engine4_iphone_points` (
  `id` int(10) NOT NULL auto_increment,
  `type` int(5) NOT NULL,
  `user_id` int(10) NOT NULL,
  `lat` varchar(16) character set utf8 collate utf8_unicode_ci NOT NULL,
  `lng` varchar(16) character set utf8 collate utf8_unicode_ci NOT NULL,
  `title` text character set utf8 collate utf8_unicode_ci NOT NULL,
  `description` text character set utf8 collate utf8_unicode_ci NOT NULL,
  `image` text character set utf8 collate utf8_unicode_ci NOT NULL,
  `datecreated` int(14) NOT NULL,
  `dateupdated` int(14) NOT NULL,
  `alert` tinyint(2) NOT NULL,
  PRIMARY KEY  (`id`)
);


CREATE TABLE `engine4_iphone_token` (
  `id` int(10) NOT NULL auto_increment,
  `user_id` int(10) NOT NULL,
  `token` varchar(128) character set utf8 collate utf8_unicode_ci NOT NULL,
  `lat` varchar(16) character set utf8 collate utf8_unicode_ci NOT NULL,
  `lng` varchar(16) character set utf8 collate utf8_unicode_ci NOT NULL,
  `update` int(14) NOT NULL,
  `radius` varchar(20) character set utf8 collate utf8_unicode_ci NOT NULL,
  `types` varchar(128) character set utf8 collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE `engine4_iphone_types` (
  `id` int(5) NOT NULL auto_increment,
  `title` text character set utf8 collate utf8_unicode_ci NOT NULL,
  `email` text character set utf8 collate utf8_unicode_ci NOT NULL,
  `icon` text character set utf8 collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
);

INSERT IGNORE INTO `engine4_core_menuitems`
	(`id`, `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`)
	VALUES (NULL, 'core_admin_main_plugins_iphone', 'iphone', 'iPhone', NULL, '{"route":"admin_default","module":"iphone","controller":"manage"}', 'core_admin_main_plugins', NULL, '1', '0', '999');

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('iphone.gmap_api_key', '');
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('iphone.defaultloc', '23,90');
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('iphone.defaultrad', '10');

INSERT IGNORE INTO `engine4_core_menuitems` (`id`, `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
(null, 'iphone_admin_main_points', 'iphone', 'Points', '', '{"route":"admin_default","module":"iphone", "controller":"manage"}', 'iphone_admin_main', '', 1, 0, 1),
(null, 'iphone_admin_main_type', 'iphone', 'Point Types', '', '{"route":"admin_default","module":"iphone","controller":"manage", "action":"type"}', 'iphone_admin_main', '', 1, 0, 3),
(null, 'iphone_admin_main_settings', 'iphone', 'Settings', '', '{"route":"admin_default","module":"iphone","controller":"manage", "action":"settings"}', 'iphone_admin_main', '', 1, 0, 4);


