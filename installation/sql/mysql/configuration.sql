# $Id: configuration.sql

#
# CLIENTS
#

INSERT INTO `#__clients` (`id`, `client_id`, `name`, `path`, `access`, `asset_id`) VALUES (1, 0, 'Site', 'JPATH_SITE', 1, 1);
INSERT INTO `#__clients` (`id`, `client_id`, `name`, `path`, `access`, `asset_id`) VALUES (2, 1, 'Administrator', 'JPATH_ADMINISTRATOR', 5, 2);
INSERT INTO `#__clients` (`id`, `client_id`, `name`, `path`, `access`, `asset_id`) VALUES (3, 2, 'Installation', 'JPATH_INSTALLATION', 0, 3);

#
# USERS AND GROUPS
#

INSERT INTO `#__groups` (`id` ,`parent_id` ,`lft` ,`rgt` ,`title`, `access`, `protected`, `asset_id`) VALUES (1, 0, 0, 1, 'Administrator', 4, 1, 50);
INSERT INTO `#__groups` (`id` ,`parent_id` ,`lft` ,`rgt` ,`title`, `access`, `protected`, `asset_id`) VALUES (2, 0, 2, 3, 'Registered',    4, 1, 60);
INSERT INTO `#__groups` (`id` ,`parent_id` ,`lft` ,`rgt` ,`title`, `access`, `protected`, `asset_id`) VALUES (3, 0, 4, 5, 'Guest',         4, 1, 70);
INSERT INTO `#__groups` (`id` ,`parent_id` ,`lft` ,`rgt` ,`title`, `access`, `protected`, `asset_id`) VALUES (4, 0, 6, 7, 'Public',        4, 1, 80);

INSERT INTO `#__groupings` (`id`, `group_name_list`, `group_id_list` ) VALUES
    (1, 'Administrator', '1'),
    (2, 'Registered', '2'),
    (3, 'Guest', '3'),
    (4, 'Public', '4'),
    (5, 'Registered, Administrator', '1,2');

INSERT INTO `#__group_to_groupings` ( `group_id` ,`grouping_id` ) SELECT 1, 1;
INSERT INTO `#__group_to_groupings` ( `group_id` ,`grouping_id` ) SELECT 2, 2;
INSERT INTO `#__group_to_groupings` ( `group_id` ,`grouping_id` ) SELECT 3, 3;
INSERT INTO `#__group_to_groupings` ( `group_id` ,`grouping_id` ) SELECT 4, 4;
INSERT INTO `#__group_to_groupings` ( `group_id` ,`grouping_id` ) SELECT 1, 5;
INSERT INTO `#__group_to_groupings` ( `group_id` ,`grouping_id` ) SELECT 2, 5;

#
# CONTENT
#

INSERT INTO `#__categories` ( `id`, `parent_id`, `lft`, `rgt`, `level`, `path`, `extension`, `title`, `alias`, `note`,
  `description`, `published`, `checked_out`, `checked_out_time`, `params`, `metadesc`, `metakey`,
  `metadata`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hits`, `language`,
  `access`, `asset_id` )
  VALUES
    (1, 0, 0, 3, 0, '', 'system', 'ROOT', 'root', '', '', 1, 0, '0000-00-00 00:00:00', '{}', '', '', '', 0, '2010-03-18 16:07:09', 0, '0000-00-00 00:00:00', 0, '*', 0, 100),
    (2, 1, 1, 2, 1, 'uncategorised', 'com_articles', 'Articles', 'articles', '', '', 1, 0, '0000-00-00 00:00:00', '{"category_layout":"","image":""}', '', '', '{"author":"","robots":""}', 42, '2010-06-28 13:26:37', 42, '2011-06-03 16:52:26', 0, '*', 1, 101);

INSERT INTO `#__articles` (
  `id`, `catid`, `title`, `alias`, `content_type`, `content_text`, `content_link`, `content_email_address`,
  `content_numeric_value`, `content_file`, `featured`, `stickied`, `user_default`, `category_default`,
  `language`, `ordering`, `state`, `publish_up`, `publish_down`, `version`, `version_of_id`, `state_prior_to_version`,
  `created`, `created_by`, `created_by_alias`, `created_by_email`, `created_by_website`, `created_by_ip_address`,
  `created_by_referer`, `modified`, `modified_by`, `checked_out`, `checked_out_time`,
  `content_table`, `component_id`, `parent_id`, `lft`, `rgt`, `level`, `metakey`, `metadesc`, `metadata`,
  `attribs`, `params`, `access`, `asset_id` )
  VALUES
    (1, 2, 'My First Article', 'my-first-article', 10, '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>', NULL, NULL, NULL, '', 1, 0, 1, 1, '*', 1, 1, '2011-05-06 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-05-06 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-05-27 13:26:26', 42, 0, '0000-00-00 00:00:00', 'com_articles', 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 1, 200),
    (2, 2, 'My Second Article', 'my-second-article', 10, '<h1>HTML Ipsum Presents</h1>\r\n	       \r\n<p><strong>Pellentesque habitant morbi tristique</strong> senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. <em>Aenean ultricies mi vitae est.</em> Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, <code>commodo vitae</code>, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. <a href="#">Donec non enim</a> in turpis pulvinar facilisis. Ut felis.</p>\r\n\r\n<h2>Header Level 2</h2>\r\n	       \r\n<ol>\r\n   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>\r\n   <li>Aliquam tincidunt mauris eu risus.</li>\r\n</ol>\r\n\r\n<blockquote><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus magna. Cras in mi at felis aliquet congue. Ut a est eget ligula molestie gravida. Curabitur massa. Donec eleifend, libero at sagittis mollis, tellus est malesuada tellus, at luctus turpis elit sit amet quam. Vivamus pretium ornare est.</p></blockquote>\r\n\r\n<h3>Header Level 3</h3>\r\n\r\n<ul>\r\n   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>\r\n   <li>Aliquam tincidunt mauris eu risus.</li>\r\n</ul>\r\n\r\n<pre><code>\r\n#header h1 a { \r\n	display: block; \r\n	width: 300px; \r\n	height: 80px; \r\n}\r\n</code></pre>', NULL, NULL, NULL, '', 0, 0, 0, 0, '*', 2, 1, '2011-06-06 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-06-06 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-05-27 13:26:26', 42, 42, '0000-00-00 00:00:00', 'com_articles', 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 1, 205),
    (3, 2, 'My Third Article', 'my-third-article', 10, '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\r\n\r\n<ol>\r\n   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>\r\n   <li>Aliquam tincidunt mauris eu risus.</li>\r\n   <li>Vestibulum auctor dapibus neque.</li>\r\n</ol>\r\n\r\n<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\r\n	       ', NULL, NULL, NULL, '', 0, 0, 0, 0, '*', 3, 1, '2011-06-10 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-06-10 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-06-10 00:00:00', 42, 42, '0000-00-00 00:00:00', 'com_articles', 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 1, 210),
    (4, 2, 'My Fourth Article', 'my-fourth-article', 10, '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>', NULL, NULL, NULL, '', 0, 0, 0, 0, '*', 4, 1, '2011-06-11 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-06-11 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-06-11 00:00:00', 42, 42, '0000-00-00 00:00:00', 'com_articles', 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 1, 215),
    (5, 2, 'My Fifth Article', 'my-fifth-article', 10, '<dl> <dt>Definition list</dt>\r\n   <dd>Consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna \r\naliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea \r\ncommodo consequat.</dd>\r\n   <dt>Lorem ipsum dolor sit amet</dt>\r\n   <dd>Consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna \r\naliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea \r\ncommodo consequat.</dd>\r\n</dl>', NULL, NULL, NULL, '', 0, 0, 0, 0, '*', 5, 1, '2011-06-27 13:26:26', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-06-27 13:26:26', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-06-27 13:26:26', 42, 42, '0000-00-00 00:00:00', 'com_articles', 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 1, 220);

#
# EXTENSIONS
#

# Components - Administrator
INSERT INTO `#__extensions` (
  `id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (1, 'com_admin', 'component', 'com_admin', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 1, 5, 1000),
    (2, 'com_articles', 'component', 'com_articles', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 2, 1, 5, 1005),
    (3, 'com_cache', 'component', 'com_cache', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 3, 1, 4, 1010),
    (4, 'com_categories', 'component', 'com_categories', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 4, 1, 5, 1015),
    (5, 'com_checkin', 'component', 'com_checkin', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 5, 1, 4, 1020),
    (6, 'com_config', 'component', 'com_config', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 6, 1, 4, 1025),
    (7, 'com_cpanel', 'component', 'com_cpanel', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 7, 1, 5, 1030),
    (8, 'com_installer', 'component', 'com_installer', '', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 8, 2, 4, 1035),
    (9, 'com_languages', 'component', 'com_languages', '', 1, 1, 1, '', '{"administrator":"en-GB","site":"en-GB"}', '', '', 0, '0000-00-00 00:00:00', 9, 1, 4, 1040),
    (10, 'com_login', 'component', 'com_login', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 10, 1, 1, 1045),
    (11, 'com_media', 'component', 'com_media', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 11, 1, 5, 1050),
    (12, 'com_menus', 'component', 'com_menus', '', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 12, 1, 4, 1055),
    (13, 'com_messages', 'component', 'com_messages', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 13, 1, 5, 1060),
    (14, 'com_modules', 'component', 'com_modules', '', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 14, 1, 4, 1065),
    (15, 'com_plugins', 'component', 'com_plugins', '', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 15, 1, 4, 1070),
    (16, 'com_redirect', 'component', 'com_redirect', '', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 16, 1, 4, 1075),
    (17, 'com_search', 'component', 'com_search', '', 1, 1, 1, '', '{"enabled":"0","show_date":"1"}', '', '', 0, '0000-00-00 00:00:00', 17, 1, 4, 1080),
    (18, 'com_templates', 'component', 'com_templates', '', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 18, 1, 4, 1085),
    (19, 'com_users', 'component', 'com_users', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 19, 1, 4, 1090);

# Components - Site
INSERT INTO `#__extensions` (
  `id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (210, 'com_articles', 'component', 'com_articles', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 2, 1, 1, 1210),
    (220, 'com_search', 'component', 'com_search', '', 1, 1, 1, '', '{"enabled":"0","show_date":"1"}', '', '', 0, '0000-00-00 00:00:00', 17, 1, 1, 1220),
    (230, 'com_users', 'component', 'com_users', '', 1, 1, 1, '', '{"allowUserRegistration":"1","useractivation":"1","frontend_userparams":"1","mailSubjectPrefix":"","mailBodySuffix":""}', '', '', 0, '0000-00-00 00:00:00', 19, 1, 1, 1230);

# Layouts
INSERT INTO `#__extensions` (
  `id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (310, 'contact', 'layout', 'layout', 'contact', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1, 1, 1310),
    (315, 'edit', 'layout', 'layout', 'edit', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1, 1, 1315),
    (320, 'faq', 'layout', 'layout', 'faq', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1, 1, 1320),
    (325, 'include', 'layout', 'layout', 'include', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1, 1, 1325),
    (330, 'item', 'layout', 'layout', 'item', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1, 1, 1330),
    (335, 'items', 'layout', 'layout', 'items', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1, 1, 1335),
    (340, 'list', 'layout', 'layout', 'list', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1, 1, 1340),
    (345, 'manager', 'layout', 'layout', 'manager', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1, 1, 1345),
    (350, 'modal', 'layout', 'layout', 'modal', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1, 1, 1350),
    (355, 'pagination', 'layout', 'layout', 'pagination', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1, 1, 1355),
    (360, 'system', 'layout', 'layout', 'system', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1, 1, 1360),
    (365, 'table', 'layout', 'layout', 'table', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1, 1, 1365),
    (370, 'toolbar', 'layout', 'layout', 'toolbar', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1, 1, 1370),
    (375, 'tree', 'layout', 'layout', 'tree', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1, 1, 1375),
    (380, 'twig_example', 'layout', 'layout', 'twig_example', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1, 1, 1380);

# Libraries
INSERT INTO `#__extensions` (
  `id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (401, 'Akismet', 'library', 'akismet', 'akismet', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 1, 4, 1400),
    (402, 'Curl', 'library', 'curl', 'curl', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 2, 1, 5, 1402),
    (403, 'Joomla Framework', 'library', 'joomla', 'joomla', 1, 1, 1, '{"legacy":false,"name":"Joomla! Web Application Framework","type":"library","creationDate":"2008","author":"Joomla","copyright":"Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.","authorEmail":"admin@joomla.org","authorUrl":"http:\\/\\/www.joomla.org","version":"1.6.0","description":"The Joomla! Web Application Framework","group":""}', '{}', '', '', 0, '0000-00-00 00:00:00', 3, 1, 4, 1404),
    (404, 'Molajo Application', 'library', 'molajo', 'molajo', 1, 1, 1, '{"legacy":false,"name":"Molajo Application","type":"library","creationDate":"2011","author":"Molajo Project Team","copyright":"Copyright (C) 2011 Individual Molajo Contributors. All rights reserved.","authorEmail":"collaborate@molajo.org","authorUrl":"http:\\/\\/molajo.org","version":"1.0.0","description":"Molajo is a web development environment useful for crafting custom solutions from simple to complex custom data architecture, presentation output, and access control.","group":""}\r\n', '', '', '', 0, '0000-00-00 00:00:00', 4, 1, 4, 1406),
    (405, 'Mollom', 'library', 'mollom', 'mollom', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 5, 1, 4, 1410),
    (406, 'PHPMailer', 'library', 'phpmailer', 'phpmailer', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 6, 1, 4, 1415),
    (407, 'phputf8', 'library', 'phputf8', 'phputf8', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 7, 1, 4, 1420),
    (408, 'Recaptcha', 'library', 'recaptcha', 'recaptcha', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 8, 1, 4, 1425),
    (409, 'Secureimage', 'library', 'secureimage', 'secureimage', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 9, 1, 4, 1430),
    (410, 'SimplePie', 'library', 'simplepie', 'simplepie', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 10, 1, 4, 1435),
    (411, 'Twig', 'library', 'twig', 'twig', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 11, 1, 4, 1440),
    (412, 'WideImage', 'library', 'wideimage', 'wideimage', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 12, 1, 4, 1450);

# Modules - Administrator
INSERT INTO `#__extensions` (
  `id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (501, 'mod_custom', 'module', 'mod_custom', 'mod_custom', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 0, 5, 1501),
    (502, 'mod_feed', 'module', 'mod_feed', 'mod_feed', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 2, 0, 5, 1502),
    (503, 'mod_latest', 'module', 'mod_latest', 'mod_latest', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 3, 0, 5, 1503),
    (504, 'mod_logged', 'module', 'mod_logged', 'mod_logged', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 4, 0, 5, 1504),
    (505, 'mod_login', 'module', 'mod_login', 'mod_login', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 5, 0, 5, 1505),
    (506, 'mod_menu', 'module', 'mod_menu', 'mod_menu', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 6, 0, 5, 1506),
    (507, 'mod_mypanel', 'module', 'mod_mypanel', 'mod_mypanel', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 7, 0, 5, 1507),
    (508, 'mod_myshortcuts', 'module', 'mod_myshortcuts', 'mod_myshortcuts', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 8, 0, 5, 1508),
    (509, 'mod_online', 'module', 'mod_online', 'mod_online', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 9, 0, 5, 1509),
    (510, 'mod_popular', 'module', 'mod_popular', 'mod_popular', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 10, 0, 5, 1510),
    (511, 'mod_quickicon', 'module', 'mod_quickicon', 'mod_quickicon', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 11, 0, 5, 1511),
    (512, 'mod_status', 'module', 'mod_status', 'mod_status', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 12, 0, 5, 1512),
    (513, 'mod_submenu', 'module', 'mod_submenu', 'mod_submenu', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 13, 0, 5, 1513),
    (514, 'mod_title', 'module', 'mod_title', 'mod_title', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 14, 0, 5, 1514),
    (515, 'mod_toolbar', 'module', 'mod_toolbar', 'mod_toolbar', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 15, 0, 5, 1515),
    (516, 'mod_unread', 'module', 'mod_unread', 'mod_unread', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 16, 0, 5, 1516);

# Modules - Site
INSERT INTO `#__extensions` (
  `id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (601, 'mod_articles', 'module', 'mod_articles', 'mod_articles', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 1, 5, 1601),
    (602, 'mod_breadcrumbs', 'module', 'mod_breadcrumbs', 'mod_breadcrumbs', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 2, 1, 5, 1602),
    (603, 'mod_custom', 'module', 'mod_custom', 'mod_custom', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 3, 1, 5, 1603),
    (604, 'mod_feed', 'module', 'mod_feed', 'mod_feed', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 4, 1, 5, 1604),
    (605, 'mod_footer', 'module', 'mod_footer', 'mod_footer', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 5, 1, 5, 1605),
    (606, 'mod_languages', 'module', 'mod_languages', 'mod_languages', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 6, 1, 5, 1606),
    (607, 'mod_login', 'module', 'mod_login', 'mod_login', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 7, 1, 5, 1607),
    (608, 'mod_media', 'module', 'mod_media', 'mod_media', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 8, 1, 5, 1608),
    (609, 'mod_menu', 'module', 'mod_menu', 'mod_menu', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 9, 1, 5, 1609),
    (610, 'mod_related_items', 'module', 'mod_related_items', 'mod_related_items', 0, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 10, 1, 5, 1610),
    (611, 'mod_search', 'module', 'mod_search', 'mod_search', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 11, 1, 5, 1611),
    (612, 'mod_syndicate', 'module', 'mod_syndicate', 'mod_syndicate', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 12, 1, 5, 1612),
    (613, 'mod_users_latest', 'module', 'mod_users_latest', 'mod_users_latest', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 13, 1, 5, 1613),
    (614, 'mod_whosonline', 'module', 'mod_whosonline', 'mod_whosonline', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 14, 1, 5, 1614);

#
# Plugins
#

## Authentication
INSERT INTO `#__extensions` (
  `id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (615, 'plg_authentication_joomla', 'plugin', 'joomla', 'authentication', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1, 4, 616);

## Content
INSERT INTO `#__extensions` (
  `id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (701, 'plg_content_emailcloak', 'plugin', 'emailcloak', 'content', 1, 1, 0, '', '{"mode":"1"}', '', '', 0, '0000-00-00 00:00:00', 1, 2, 4, 1700),
    (705, 'plg_content_loadmodule', 'plugin', 'loadmodule', 'content', 1, 1, 0, '', '{"style":"none"}', '', '', 0, '0000-00-00 00:00:00', 3, 0, 4, 1705),
    (710, 'plg_content_molajosample', 'plugin', 'molajosample', 'content', 1, 1, 0, '', '{"enable_molajosample_feature":"1"}', '', '', 0, '0000-00-00 00:00:00', 4, 0, 4, 1710);

## Editors
INSERT INTO `#__extensions` (
  `id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (800, 'plg_editors_aloha', 'plugin', 'aloha', 'editors', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 5, 0, 4, 1800),
    (805, 'plg_editors_codemirror', 'plugin', 'codemirror', 'editors', 1, 1, 1, '', '{"linenumbers":"0","tabmode":"indent"}', '', '', 0, '0000-00-00 00:00:00', 6, 0, 4, 1805),
    (810, 'plg_editors_none', 'plugin', 'none', 'editors', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 7, 0, 4, 1810);

## Extended Editor
INSERT INTO `#__extensions` (
  `id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (900, 'plg_editors-xtd_article', 'plugin', 'article', 'editors-xtd', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 8, 0, 1, 1900),
    (905, 'plg_editors-xtd_audio', 'plugin', 'audio', 'editors-xtd', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 8, 0, 1, 1905),
    (910, 'plg_editors-xtd_file', 'plugin', 'file', 'editors-xtd', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 9, 0, 1, 1910),
    (915, 'plg_editors-xtd_pagebreak', 'plugin', 'pagebreak', 'editors-xtd', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 10, 0, 1, 1915),
    (920, 'plg_editors-xtd_readmore', 'plugin', 'readmore', 'editors-xtd', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 11, 0, 1, 1920),
    (925, 'plg_editors-xtd_video', 'plugin', 'image', 'editors-xtd', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 12, 0, 1, 1925);

## Extension
INSERT INTO `#__extensions` (
  `id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (926, 'plg_extension_joomla', 'plugin', 'joomla', 'extension', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 13, 0, 1, 926);

## Language
INSERT INTO `#__extensions` (
  `id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUE
    (950, 'English (United Kingdom)', 'language', 'en-GB', '', 0, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 44, 1, 4, 1950),
    (951, 'English (United Kingdom)', 'language', 'en-GB', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 45, 1, 4, 1951);

## Molajo
INSERT INTO `#__extensions` (
  `id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (1005, 'plg_molajo_broadcast', 'plugin', 'broadcast', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_BROADCAST_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved.","authorEmail":"SpecificMaintainerTeam@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_BROADCAST_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 14, -1, 4, 10005),
    (1010, 'plg_molajo_compress', 'plugin', 'compress', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_COMPRESS_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Individual Molajo Contributors. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"SpecificMaintainerTeam@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_COMPRESS_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 15, -1, 4, 10010),
    (1015, 'plg_molajo_categorization', 'plugin', 'categorization', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_CATEGORIZATION_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Individual Molajo Contributors. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"SpecificMaintainerTeam@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_CATEGORIZATION_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 16, -1, 4, 10015),
    (1020, 'plg_molajo_content', 'plugin', 'content', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_CONTENT_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Individual Molajo Contributors. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"SpecificMaintainerTeam@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_CONTENT_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 17, -1, 4, 10020),
    (1025, 'plg_molajo_extend', 'plugin', 'extend', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_SYSTEM_EXTEND_NAME","type":"plugin","creationDate":"May 2011","author":"Amy Stephen","copyright":"(C) 2011 Amy Stephen. All rights reserved.","authorEmail":"AmyStephen@gmail.com","authorUrl":"Molajo.org","version":"1.6.0","description":"PLG_SYSTEM_EXTEND_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 0, -1, 4, 10025),
    (1030, 'plg_molajo_links', 'plugin', 'links', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_LINKS_NAME","type":"plugin","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Individual Molajo Contributors. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"SpecificMaintainerTeam@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_LINKS_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 18, -1, 4, 10030),
    (1035, 'plg_molajo_media', 'plugin', 'media', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_MEDIA_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Individual Molajo Contributors. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"SpecificMaintainerTeam@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_MEDIA_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 19, -1, 4, 10035),
    (1040, 'plg_molajo_protect', 'plugin', 'protect', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_PROTECT_NAME","type":"plugin","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Individual Molajo Contributors. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"SpecificMaintainerTeam@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_PROTECT_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 20, -1, 4, 10040),
    (1045, 'plg_molajo_responses', 'plugin', 'responses', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_RESPONSES_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Individual Molajo Contributors. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"SpecificMaintainerTeam@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_RESPONSES_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 21, -1, 4, 10045),
    (1050, 'plg_molajo_search', 'plugin', 'search', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_SEARCH_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Individual Molajo Contributors. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"SpecificMaintainerTeam@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_SEARCH_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 23, -1, 4, 10050),
    (1055, 'plg_molajo_system', 'plugin', 'system', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_SYSTEM_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Individual Molajo Contributors. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"SpecificMaintainerTeam@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_SYSTEM_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 24, -1, 4, 10055),
    (1060, 'plg_molajo_urls', 'plugin', 'urls', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_URLS_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Individual Molajo Contributors. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"SpecificMaintainerTeam@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_URLS_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 25, -1, 4, 10060),
    (1065, 'plg_molajo_webservices', 'plugin', 'webservices', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_WEBSERVICES_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Individual Molajo Contributors. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"SpecificMaintainerTeam@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_WEBSERVICES_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 26, -1, 4, 10065);

## Search
INSERT INTO `#__extensions` (
  `id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (1100, 'plg_search_categories', 'plugin', 'categories', 'search', 1, 1, 0, '', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 27, 0, 4, 11000),
    (1105, 'plg_search_articles', 'plugin', 'articles', 'search', 1, 1, 0, '', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 28, 0, 4, 11005),
    (1110, 'plg_search_media', 'plugin', 'media', 'search', 1, 1, 0, '', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 29, 0, 4, 11100);

## System
INSERT INTO `#__extensions` (
  `id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (1120, 'plg_system_cache', 'plugin', 'cache', 'system', 1, 0, 1, '', '{"browsercache":"0","cachetime":"15"}', '', '', 0, '0000-00-00 00:00:00', 30, 0, 4, 11200),
    (1125, 'plg_system_debug', 'plugin', 'debug', 'system', 1, 1, 0, '', '{"profile":"1","queries":"1","memory":"1","language_files":"1","language_strings":"1","strip-first":"1","strip-prefix":"","strip-suffix":""}', '', '', 0, '0000-00-00 00:00:00', 31, 0, 4, 11250),
    (1130, 'plg_system_languagefilter', 'plugin', 'languagefilter', 'system', 1, 0, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 32, 0, 4, 11300),
    (1135, 'plg_system_log', 'plugin', 'log', 'system', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 33, 0, 4, 11350),
    (1140, 'plg_system_logout', 'plugin', 'logout', 'system', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 34, 0, 4, 11400),
    (1145, 'plg_system_molajo', 'plugin', 'molajo', 'system', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 35, 0, 4, 11450),
    (1150, 'plg_system_p3p', 'plugin', 'p3p', 'system', 1, 1, 0, '', '{"headers":"NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"}', '', '', 0, '0000-00-00 00:00:00', 36, 0, 4, 11500),
    (1155, 'plg_system_redirect', 'plugin', 'redirect', 'system', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 37, 0, 4, 11550),
    (1160, 'plg_system_remember', 'plugin', 'remember', 'system', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 38, 0, 4, 11600),
    (1165, 'plg_system_sef', 'plugin', 'sef', 'system', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 39, 0, 4, 11650);

## Query
INSERT INTO `#__extensions` (
  `id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (1200, 'plg_query_molajosample', 'plugin', 'molajosample', 'query', 1, 1, 0, '', '{"enable_molajosample_feature":"1"}', '', '', 0, '0000-00-00 00:00:00', 41, 0, 4, 12000);

## Template
INSERT INTO `#__extensions` (
  `id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUE
    (1300, 'molajo-construct', 'template', 'molajo-construct', '', 0, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 42, 1, 4, 13000),
    (1305, 'bluestork', 'template', 'bluestork', '', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 43, 1, 4, 13050),
    (1310, 'minima', 'template', 'minima', '', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 43, 1, 4, 13100);

## Users
INSERT INTO `#__extensions` (
  `id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (1400, 'plg_user_joomla', 'plugin', 'joomla', 'user', 1, 1, 0, '', '{"autoregister":"1"}', '', '', 0, '0000-00-00 00:00:00', 40, 0, 4, 14000);

#
# Configuration
#

/* 001 MOLAJO_CONFIG_OPTION_ID_FIELDS */
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1, '', '', 0),
('core', 1, 'access', 'MOLAJO_FIELD_ACCESS_LABEL', 1),
('core', 1, 'alias', 'MOLAJO_FIELD_ALIAS_LABEL', 2),
('core', 1, 'asset_id', 'MOLAJO_FIELD_ASSET_ID_LABEL', 3),
('core', 1, 'attribs', 'MOLAJO_FIELD_ATTRIBS_LABEL', 4),
('core', 1, 'catid', 'MOLAJO_FIELD_CATID_LABEL', 5),
('core', 1, 'checked_out', 'MOLAJO_FIELD_CHECKED_OUT_LABEL', 6),
('core', 1, 'checked_out_time', 'MOLAJO_FIELD_CHECKED_OUT_TIME_LABEL', 7),
('core', 1, 'component_id', 'MOLAJO_FIELD_COMPONENT_ID_LABEL', 8),
('core', 1, 'content_table', 'MOLAJO_FIELD_content_table_LABEL', 9),
('core', 1, 'content_email_address', 'MOLAJO_FIELD_CONTENT_EMAIL_ADDRESS_LABEL', 10),
('core', 1, 'content_file', 'MOLAJO_FIELD_CONTENT_FILE_LABEL', 11),
('core', 1, 'content_link', 'MOLAJO_FIELD_CONTENT_LINK_LABEL', 12),
('core', 1, 'content_numeric_value', 'MOLAJO_FIELD_CONTENT_NUMERIC_VALUE_LABEL', 13),
('core', 1, 'content_text', 'MOLAJO_FIELD_CONTENT_TEXT_LABEL', 14),
('core', 1, 'content_type', 'MOLAJO_FIELD_CONTENT_TYPE_LABEL', 15),
('core', 1, 'created', 'MOLAJO_FIELD_CREATED_LABEL', 16),
('core', 1, 'created_by', 'MOLAJO_FIELD_CREATED_BY_LABEL', 17),
('core', 1, 'created_by_alias', 'MOLAJO_FIELD_CREATED_BY_ALIAS_LABEL', 18),
('core', 1, 'created_by_email', 'MOLAJO_FIELD_CREATED_BY_EMAIL_LABEL', 19),
('core', 1, 'created_by_ip_address', 'MOLAJO_FIELD_CREATED_BY_IP_ADDRESS_LABEL', 20),
('core', 1, 'created_by_referer', 'MOLAJO_FIELD_CREATED_BY_REFERER_LABEL', 21),
('core', 1, 'created_by_website', 'MOLAJO_FIELD_CREATED_BY_WEBSITE_LABEL', 22),
('core', 1, 'featured', 'MOLAJO_FIELD_FEATURED_LABEL', 23),
('core', 1, 'id', 'MOLAJO_FIELD_ID_LABEL', 24),
('core', 1, 'language', 'MOLAJO_FIELD_LANGUAGE_LABEL', 25),
('core', 1, 'level', 'MOLAJO_FIELD_LEVEL_LABEL', 26),
('core', 1, 'lft', 'MOLAJO_FIELD_LFT_LABEL', 27),
('core', 1, 'metadata', 'MOLAJO_FIELD_METADATA_LABEL', 28),
('core', 1, 'metadesc', 'MOLAJO_FIELD_METADESC_LABEL', 29),
('core', 1, 'metakey', 'MOLAJO_FIELD_METAKEY_LABEL', 30),
('core', 1, 'meta_author', 'MOLAJO_FIELD_META_AUTHOR_LABEL', 31),
('core', 1, 'meta_rights', 'MOLAJO_FIELD_META_RIGHTS_LABEL', 32),
('core', 1, 'meta_robots', 'MOLAJO_FIELD_META_ROBOTS_LABEL', 33),
('core', 1, 'modified', 'MOLAJO_FIELD_MODIFIED_LABEL', 34),
('core', 1, 'modified_by', 'MOLAJO_FIELD_MODIFIED_BY_LABEL', 35),
('core', 1, 'ordering', 'MOLAJO_FIELD_ORDERING_LABEL', 36),
('core', 1, 'publish_down', 'MOLAJO_FIELD_PUBLISH_DOWN_LABEL', 37),
('core', 1, 'publish_up', 'MOLAJO_FIELD_PUBLISH_UP_LABEL', 38),
('core', 1, 'rgt', 'MOLAJO_FIELD_RGT_LABEL', 39),
('core', 1, 'state', 'MOLAJO_FIELD_STATE_LABEL', 40),
('core', 1, 'state_prior_to_version', 'MOLAJO_FIELD_STATE_PRIOR_TO_VERSION_LABEL', 41),
('core', 1, 'stickied', 'MOLAJO_FIELD_STICKIED_LABEL', 42),
('core', 1, 'user_default', 'MOLAJO_FIELD_user_default_LABEL', 43),
('core', 1, 'category_default', 'MOLAJO_FIELD_category_default_LABEL', 43),
('core', 1, 'title', 'MOLAJO_FIELD_TITLE_LABEL', 43),
('core', 1, 'version', 'MOLAJO_FIELD_VERSION_LABEL', 44),
('core', 1, 'version_of_id', 'MOLAJO_FIELD_VERSION_OF_ID_LABEL', 45);

/* 002 MOLAJO_CONFIG_OPTION_ID_EDITSTATE_FIELDS */
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 2, '', '', 0),
('core', 2, 'access', 'MOLAJO_FIELD_ACCESS_LABEL', 1),
('core', 2, 'featured', 'MOLAJO_FIELD_FEATURED_LABEL', 2),
('core', 2, 'ordering', 'MOLAJO_FIELD_ORDERING_LABEL', 3),
('core', 2, 'publish_down', 'MOLAJO_FIELD_PUBLISH_DOWN_LABEL', 4),
('core', 2, 'publish_up', 'MOLAJO_FIELD_PUBLISH_UP_LABEL', 5),
('core', 2, 'state', 'MOLAJO_FIELD_STATE_LABEL', 6),
('core', 2, 'stickied', 'MOLAJO_FIELD_STICKIED_LABEL', 7);

/* 003 MOLAJO_CONFIG_OPTION_ID_JSON_FIELDS */
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3, '', '', 0),
('core', 3, 'attribs', 'MOLAJO_FIELD_ATTRIBS_LABEL', 1),
('core', 3, 'metadata', 'MOLAJO_FIELD_METADATA_LABEL', 2),
('core', 3, 'params', 'MOLAJO_FIELD_PARAMETERS_LABEL', 3);

/* 010 MOLAJO_CONFIG_OPTION_ID_CONTENT_TYPES */
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 10, '', '', 0),
('core', 10, 'content_type', 'Content Type', 1);

/* VIEWS */

/* 020 MOLAJO_CONFIG_OPTION_ID_VIEW_PAIRS */
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 20, '', '', 0),
('core', 20, 'single', 'multiple', 1);

/* TABLE */

/* 045 MOLAJO_CONFIG_OPTION_ID_TABLE */;
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 45, '', '', 0),
('core', 45, '__multiple', '__multiple', 1);

/* FORMAT */

/* 075 MOLAJO_CONFIG_OPTION_ID_FORMAT */
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 75, '', '', 0),
('core', 75, 'html', 'html', 1),
('core', 75, 'raw', 'raw', 2),
('core', 75, 'feed', 'feed', 3);

/* TASKS */

/* 080 MOLAJO_CONFIG_OPTION_ID_DISPLAY_CONTROLLER_TASKS */;
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 80, '', '', 0),
('core', 80, 'add', 'add', 1),
('core', 80, 'edit', 'edit', 2),
('core', 80, 'display', 'display', 3);

/** 085 MOLAJO_CONFIG_OPTION_ID_SINGLE_CONTROLLER_TASKS */
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 85, '', '', 0),
('core', 85, 'apply', 'apply', 1),
('core', 85, 'cancel', 'cancel', 2),
('core', 85, 'create', 'create', 3),
('core', 85, 'save', 'save', 4),
('core', 85, 'save2copy', 'save2copy', 5),
('core', 85, 'save2new', 'save2new', 6),
('core', 85, 'restore', 'restore', 7);

/** 090 MOLAJO_CONFIG_OPTION_ID_MULTIPLE_CONTROLLER_TASKS **/
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 90, '', '', 0),
('core', 90, 'archive', 'archive', 1),
('core', 90, 'publish', 'publish', 2),
('core', 90, 'unpublish', 'unpublish', 3),
('core', 90, 'spam', 'spam', 4),
('core', 90, 'trash', 'trash', 5),
('core', 90, 'feature', 'feature', 6),
('core', 90, 'unfeature', 'unfeature', 7),
('core', 90, 'sticky', 'sticky', 8),
('core', 90, 'unsticky', 'unsticky', 9),
('core', 90, 'checkin', 'checkin', 10),
('core', 90, 'reorder', 'reorder', 11),
('core', 90, 'orderup', 'orderup', 12),
('core', 90, 'orderdown', 'orderdown', 13),
('core', 90, 'saveorder', 'saveorder', 14),
('core', 90, 'delete', 'delete', 15),
('core', 90, 'copy', 'copy', 16),
('core', 90, 'move', 'move', 17);

/** 100 MOLAJO_CONFIG_OPTION_ID_TASK_ACL_METHODS **/
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 100, '', '', 0),
('core', 100, 'add', 'create', 1),
('core', 100, 'admin', 'admin', 2),
('core', 100, 'apply', 'edit', 3),
('core', 100, 'archive', 'delete', 4),
('core', 100, 'cancel', '', 5),
('core', 100, 'checkin', 'admin', 6),
('core', 100, 'close', '', 7),
('core', 100, 'copy', 'create', 8),
('core', 100, 'create', 'create', 9),
('core', 100, 'delete', 'delete', 10),
('core', 100, 'display', 'view', 11),
('core', 100, 'edit', 'edit', 12),
('core', 100, 'editstate', 'delete', 13),
('core', 100, 'feature', 'delete', 14),
('core', 100, 'manage', '', 15),
('core', 100, 'move', 'edit', 16),
('core', 100, 'orderdown', 'delete', 18),
('core', 100, 'orderup', 'delete', 19),
('core', 100, 'publish', 'delete', 20),
('core', 100, 'reorder', 'delete', 21),
('core', 100, 'restore', 'delete', 22),
('core', 100, 'save', 'edit', 23),
('core', 100, 'save2copy', 'edit', 24),
('core', 100, 'save2new', 'edit', 25),
('core', 100, 'saveorder', 'delete', 26),
('core', 100, 'search', 'view', 27),
('core', 100, 'spam', 'delete', 28),
('core', 100, 'state', 'delete', 29),
('core', 100, 'sticky', 'delete', 30),
('core', 100, 'trash', 'delete', 31),
('core', 100, 'unfeature', 'delete', 32),
('core', 100, 'unpublish', 'delete', 33),
('core', 100, 'unsticky', 'delete', 34);

/** 110 MOLAJO_CONFIG_OPTION_ID_ACL_IMPLEMENTATION **/
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 110, '', '', 0),
('core', 110, 'simple', 'Simple ACL Implementation', 1);

/** 120 MOLAJO_CONFIG_OPTION_ID_ACL_ITEM_TESTS **/
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 120, '', '', 0),
('core', 120, 'display', 'display', 1),
('core', 120, 'edit', 'edit', 2),
('core', 120, 'editstate', 'editstate', 3),
('core', 120, 'trash', 'trash', 4),
('core', 120, 'delete', 'delete', 5),
('core', 120, 'restore', 'restore', 6);

/* 200 MOLAJO_CONFIG_OPTION_ID_LIST_TOOLBAR_BUTTONS */;
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 200, '', '', 0),
('core', 200, 'archive', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_ARCHIVE', 1),
('core', 200, 'checkin', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_CHECKIN', 2),
('core', 200, 'delete', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_DELETE', 3),
('core', 200, 'edit', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_EDIT', 4),
('core', 200, 'feature', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_FEATURE', 5),
('core', 200, 'help', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_HELP', 6),
('core', 200, 'new', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_NEW', 7),
('core', 200, 'options', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_OPTIONS', 8),
('core', 200, 'publish', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_PUBLISH', 9),
('core', 200, 'restore', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_RESTORE', 10),
('core', 200, 'separator', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SEPARATOR', 11),
('core', 200, 'spam', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SPAM', 12),
('core', 200, 'sticky', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_STICKY', 13),
('core', 200, 'trash', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_TRASH', 14),
('core', 200, 'unpublish', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_UNPUBLISH', 15);

/* 210 MOLAJO_CONFIG_OPTION_ID_EDIT_TOOLBAR_BUTTONS */
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 210, '', '', 0),
('core', 210, 'apply', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_APPLY', 1),
('core', 210, 'close', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_CLOSE', 2),
('core', 210, 'help', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_HELP', 3),
('core', 210, 'restore', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_RESTORE', 4),
('core', 210, 'save', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SAVE', 5),
('core', 210, 'save2new', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SAVE_AND_NEW', 6),
('core', 210, 'save2copy', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SAVE_AS_COPY', 7),
('core', 210, 'separator', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SEPARATOR', 8);

/* 220 MOLAJO_CONFIG_OPTION_ID_TOOLBAR_SUBMENU_LINKS */;
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 220, '', '', 0),
('core', 220, 'category', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_CATEGORY', 1),
('core', 220, 'default', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_DEFAULT', 2),
('core', 220, 'featured', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_FEATURED', 3),
('core', 220, 'revisions', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_REVISIONS', 4),
('core', 220, 'stickied', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_STICKIED', 5),
('core', 220, 'unpublished', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_UNPUBLISHED', 6);

/* 230 MOLAJO_CONFIG_OPTION_ID_LISTBOX_FILTER */;
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 230, '', '', 0),
('core', 230, 'access', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_ACCESS', 1),
('core', 230, 'alias', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_ALIAS', 2),
('core', 230, 'created_by', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_AUTHOR', 3),
('core', 230, 'catid', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_CATEGORY', 4),
('core', 230, 'content_type', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_CONTENT_TYPE', 5),
('core', 230, 'created', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_CREATE_DATE', 6),
('core', 230, 'featured', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_FEATURED', 7),
('core', 230, 'language', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_LANGUAGE', 9),
('core', 230, 'modified', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_UPDATE_DATE', 10),
('core', 230, 'publish_up', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_PUBLISH_DATE', 11),
('core', 230, 'state', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_STATE', 12),
('core', 230, 'stickied', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_STICKIED', 13),
('core', 230, 'title', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_TITLE', 14);

/* 240 MOLAJO_CONFIG_OPTION_ID_EDITOR_BUTTONS */;
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 240, '', '', 0),
('core', 240, 'article', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_ARTICLE', 1),
('core', 240, 'audio', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_AUDIO', 2),
('core', 240, 'file', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_FILE', 3),
('core', 240, 'gallery', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_GALLERY', 4),
('core', 240, 'image', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_IMAGE', 5),
('core', 240, 'pagebreak', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_PAGEBREAK', 6),
('core', 240, 'readmore', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_READMORE', 7),
('core', 240, 'video', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_VIDEO', 8);

/* 250 MOLAJO_CONFIG_OPTION_ID_STATE */;
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 250, '', '', 0),
('core', 250, '2', 'MOLAJO_OPTION_ARCHIVED', 1),
('core', 250, '1', 'MOLAJO_OPTION_PUBLISHED', 2),
('core', 250, '0', 'MOLAJO_OPTION_UNPUBLISHED', 3),
('core', 250, '-1', 'MOLAJO_OPTION_TRASHED', 4),
('core', 250, '-2', 'MOLAJO_OPTION_SPAMMED', 5),
('core', 250, '-10', 'MOLAJO_OPTION_VERSION', 6);

/* 500 MOLAJO_CONFIG_OPTION_ID_PARAMETERS_LAYOUTS */;
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 500, '', '', 0),
('core', 500, 'article', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_ARTICLE', 1),
('core', 500, 'banner', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_BANNER', 2),
('core', 500, 'contact', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_CONTACT', 3),
('core', 500, 'contact_form', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_CONTACT_FORM', 4),
('core', 500, 'media', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_MEDIA', 5),
('core', 500, 'newsfeed', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_NEWSFEED', 6),
('core', 500, 'item', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_ITEM', 7),
('core', 500, 'user', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_USER', 8),
('core', 500, 'weblink', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_WEBLINK', 9),
('core', 500, 'category', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_CATEGORY', 10),
('core', 500, 'blog', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_BLOG', 11),
('core', 500, 'integration', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_INTEGRATION', 12),
('core', 500, 'list', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_LIST', 13),
('core', 500, 'manager', 'MOLAJO_CONFIG_ITEM_LAYOUT_PARAMETER_MANAGER', 14);

/* ARTICLE CONFIGURATION FIELDS */

/* 010 MOLAJO_CONFIG_OPTION_ID_CONTENT_TYPES */
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 10, '', '', 0),
('com_articles', 10, 'articles', 'Articles', 1);

/* VIEWS */

/* 020 MOLAJO_CONFIG_OPTION_ID_VIEW_PAIRS */
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 20, '', '', 0),
('com_articles', 20, 'article', 'articles', 1);

/* TABLE */

/* 045 MOLAJO_CONFIG_OPTION_ID_TABLE */;
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 45, '', '', 0),
('com_articles', 45, '__articles', '__articles', 1);

/* 050 MOLAJO_CONFIG_OPTION_ID_EDIT_LAYOUTS */;
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 50, '', '', 0),
('com_articles', 50, 'article', 'article', 1);

/* 060 MOLAJO_CONFIG_OPTION_ID_DEFAULT_LAYOUT */;
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 60, '', '', 0),
('com_articles', 60, 'articles', 'articles', 1);


#
# LANGUAGES
#
INSERT INTO `#__languages` (`lang_id`,`lang_code`,`title`,`title_native`,`sef`,`image`,`description`,`metakey`,`metadesc`,`published`)
  VALUES
    (1, 'en-GB', 'English (UK)', 'English (UK)', 'en', 'en', '', '', '', 1);

#
# MENUS
#

INSERT INTO `#__menu_types` VALUES (1, 'mainmenu', 'Main Menu', 'The main menu for the site');

# Administrator
INSERT INTO `#__menu` VALUES (1, '', 'Menu_Item_Root', 'root', '', '', '', '', 1, 0, 0, 0, 1, 0, '0000-00-00 00:00:00', 0, 121, 5, '', 0, '', 0, 49, 0, '*', 0);
INSERT INTO `#__menu` VALUES (2, 'menu', 'com_messages', 'Messaging', '', 'Messaging', 'index.php?option=com_messages', 'component', 1, 1, 1, 13, 2, 0, '0000-00-00 00:00:00', 0, 122, 5, 'class:messages', 0, '', 17, 22, 0, '*', 1);
INSERT INTO `#__menu` VALUES (3, 'menu', 'com_messages_add', 'New Private Message', '', 'list/New Private Message', 'index.php?option=com_messages&task=message.add', 'component', 1, 2, 2, 13, 3, 0, '0000-00-00 00:00:00', 0, 123, 5, 'class:messages-add', 0, '', 46, 49, 0, '*', 1);
INSERT INTO `#__menu` VALUES (4, 'menu', 'com_messages_read', 'Read Private Message', '', 'list/Read Private Message', 'index.php?option=com_messages', 'component', 1, 2, 2, 13, 4, 0, '0000-00-00 00:00:00', 0, 124, 5, 'class:messages-read', 0, '', 50, 51, 0, '*', 1);
INSERT INTO `#__menu` VALUES (5, 'menu', 'com_redirect', 'Redirect', '', 'Redirect', 'index.php?option=com_redirect', 'component', 1, 1, 1, 16, 5, 0, '0000-00-00 00:00:00', 0, 125, 5, 'class:redirect', 0, '', 37, 38, 0, '*', 1);
INSERT INTO `#__menu` VALUES (6, 'menu', 'com_search', 'Search', '', 'Search', 'index.php?option=com_search', 'component', 1, 1, 1, 17, 6, 0, '0000-00-00 00:00:00', 0, 126, 5, 'class:search', 0, '', 29, 30, 0, '*', 1);

# Client
INSERT INTO `#__menu` VALUES (7, 'mainmenu', 'Home', 'home', '', 'home', 'index.php?option=com_users&view=login', 'component', 1, 1, 1, 19, 1, 0, '0000-00-00 00:00:00', 0, 127, 1, '', 0, '{"login_redirect_url":"","logindescription_show":"1","login_description":"","login_image":"","logout_redirect_url":"","logoutdescription_show":"1","logout_description":"","logout_image":"","menu-anchor_title":"","menu-anchor_css":"","menu_image":"","menu_text":1,"page_title":"","show_page_heading":0,"page_heading":"","pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0}', 39, 40, 1, '*', 0);
INSERT INTO `#__menu` VALUES (8, 'mainmenu', 'Edit Article', 'edit', '', 'edit', 'index.php?option=com_articles&view=article&layout=edit', 'component', 1, 1, 1, 2, 2, 0, '0000-00-00 00:00:00', 0, 128, 5, '', 0, '{"menu-anchor_title":"","menu-anchor_css":"","menu_image":"","menu_text":1,"page_title":"","show_page_heading":0,"page_heading":"","pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0}', 41, 42, 0, '*', 0);
INSERT INTO `#__menu` VALUES (9, 'mainmenu', 'Display Article', 'item', '', 'item', 'index.php?option=com_articles&view=articles&layout=item&id=5', 'component', 1, 1, 1, 2, 2, 0, '0000-00-00 00:00:00', 0, 129, 1, '', 0, '{"menu-anchor_title":"","menu-anchor_css":"","menu_image":"","menu_text":1,"page_title":"","show_page_heading":0,"page_heading":"","pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0}', 41, 42, 0, '*', 0);
INSERT INTO `#__menu` VALUES (10, 'mainmenu', 'Article Blog', 'items', '', 'items', 'index.php?option=com_articles&view=articles&layout=items&catid=2', 'component', 1, 1, 1, 2, 3, 0, '0000-00-00 00:00:00', 0, 130, 1, '', 0, '{"menu-anchor_title":"","menu-anchor_css":"","menu_image":"","menu_text":1,"page_title":"","show_page_heading":0,"page_heading":"","pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0}', 43, 44, 0, '*', 0);
INSERT INTO `#__menu` VALUES (11, 'mainmenu', 'Article List', 'list', '', 'list', 'index.php?option=com_articles&view=articles&catid=2', 'component', 1, 1, 1, 2, 4, 0, '0000-00-00 00:00:00', 0, 131, 1, '', 0, '{"menu-anchor_title":"","menu-anchor_css":"","menu_image":"","menu_text":1,"page_title":"","show_page_heading":0,"page_heading":"","pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0}', 45, 52, 0, '*', 0);
INSERT INTO `#__menu` VALUES (12, 'mainmenu', 'Article Table', 'table', '', 'table', 'index.php?option=com_articles&view=articles&layout=table&catid=2', 'component', 1, 1, 1, 5, 5, 0, '0000-00-00 00:00:00', 0, 132, 1, '', 0, '{"menu-anchor_title":"","menu-anchor_css":"","menu_image":"","menu_text":1,"page_title":"","show_page_heading":0,"page_heading":"","pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0}', 47, 48, 0, '*', 0);

INSERT INTO `#__modules_menu` VALUES
(1,0),
(2,0),
(3,0),
(4,0),
(6,0),
(7,0),
(8,0),
(9,0),
(10,0),
(12,0),
(13,0),
(14,0),
(15,0),
(16,0),
(17,0);

#
# MODULES
#

# admin modules
INSERT INTO `#__modules` (`id`, `title`,  `note`, `content`, `ordering`, `position`, `checked_out`,
  `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`,`showtitle`, `params`,
  `client_id`, `language`, `access`,  `asset_id`)
    VALUES
    (1, 'Login', '', '', 1, 'login', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_login', 1, '', 1, '*', 5, 5001),
    (2, 'Popular Articles', '', '', 1, 'cpanel', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_popular', 1, '{"count":"5","catid":"","user_id":"0","layout":"_:DEFAULT","moduleclass_sfx":"","cache":"0","automatic_title":"1"}', 1, '*', 5, 5008),
    (3, 'Recently Added Articles', '', '', 2, 'cpanel', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_latest', 1, '{"count":"5","ordering":"c_dsc","catid":"","user_id":"0","layout":"_:DEFAULT","moduleclass_sfx":"","cache":"0","automatic_title":"1"}', 1, '*', 5, 5010),
    (4, 'Unread Messages', '', '', 1, 'header', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_unread', 1, '', 1, '*', 5, 5011),
    (5, 'Online Users', '', '', 2, 'header', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_online', 1, '', 1, '*', 5, 5015),
    (6, 'Toolbar', '', '', 1, 'toolbar', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_toolbar', 1, '', 1, '*', 5, 5020),
    (7, 'Quick Icons', '', '', 1, 'icon', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_quickicon', 1, '', 1, '*', 5, 5030),
    (8, 'Logged-in Users', '', '', 2, 'cpanel', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_logged', 1, '{"count":"5","name":"1","layout":"_:DEFAULT","moduleclass_sfx":"","cache":"0","automatic_title":"1"}', 1, '*', 5, 5035),
    (9, 'Admin Menu', '', '', 1, 'menu', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_menu', 1, '{"layout":"","moduleclass_sfx":"","shownew":"1","showhelp":"1","cache":"0"}', 1, '*', 5, 5040),
    (10, 'Admin Submenu', '', '', 1, 'submenu', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_submenu', 1, '', 1, '*', 5, 5050),
    (11, 'User Status', '', '', 1, 'status', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_status', 1, '', 1, '*', 5, 5055),
    (12, 'Title', '', '', 1, 'title', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_title', 1, '', 1, '*', 5, 5060),
    (13, 'My Panel', '', '', 1, 'widgets-first', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_mypanel', 1, '', 1, '*', 5, 5062),
    (14, 'My Shortcuts', '', '', 2, 'widgets-last', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_myshortcuts', 1, '{"show_add_link":"1"}', 1, '*', 5, 5063);

# site modules
INSERT INTO `#__modules` (`id`, `title`,  `note`, `content`, `ordering`, `position`, `checked_out`,
  `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`,`showtitle`, `params`,
  `client_id`, `language`, `access`,  `asset_id`)
    VALUES
    (15, 'Main Menu', '', '', 1, 'nav', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_menu', 1, '{"menutype":"mainmenu","startLevel":"1","endLevel":"0","showAllChildren":"0","tag_id":"","class_sfx":"","window_open":"","layout":"_:DEFAULT","moduleclass_sfx":"_menu","cache":"1","cache_time":"900","cachemode":"itemid"}', 0, '*', 5, 5070),
    (16, 'Login Form', '', '', 7, 'content-above-1', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_login', 1, '{"greeting":"1","name":"0"}', 0, '*', 5, 5085),
    (17, 'Breadcrumbs', '', '', 1, 'breadcrumbs', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_breadcrumbs', 1, '{"moduleclass_sfx":"","showHome":"1","homeText":"Home","showComponent":"1","separator":"","cache":"1","cache_time":"900","cachemode":"itemid"}', 0, '*', 5, 5080);

#
# TEMPLATES
#
INSERT INTO `#__template_styles` VALUES (1, 'molajo-construct', '0', '1', 'Molajo Construct', '{}');
INSERT INTO `#__template_styles` VALUES (2, 'Blank Slate', '0', '1', 'Molajo Blankslate - DEFAULT', '{}');
INSERT INTO `#__template_styles` VALUES (3, 'bluestork', '1', '0', 'Bluestork', '{"useRoundedCorners":"1","showSiteName":"0"}');
INSERT INTO `#__template_styles` VALUES (4, 'minima', '1', '1', 'Minima - DEFAULT', '{}');

#
# UPDATES
#
INSERT INTO `#__update_sites` VALUES
(1, 'Molajo Core', 'collection', 'http://update.molajo.org/core/list.xml', 1),
(2, 'Molajo Directory', 'collection', 'http://update.molajo.org/directory/list.xml', 1);

INSERT INTO `#__update_sites_extensions` VALUES (1, 700), (2, 700);
