# @version		$Id: sample_data.sql 21061 2011-04-03 16:50:11Z dextercowley $
#
# IMPORTANT - THIS FILE MUST BE SAVED WITH UTF-8 ENCODING ONLY. BEWARE IF EDITING!
#

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
# Actions
#
TRUNCATE TABLE `#__actions`;
INSERT INTO `#__actions` (`id` ,`title`) VALUES (1, 'login'), (2, 'create'), (3, 'view'), (4, 'edit'), (5, 'delete'), (6, 'admin');

#
# Table structure for table `#__temp_permissions`
#   Calculate assigned actions by asset id for groups
#
CREATE TABLE IF NOT EXISTS `#__temp_permissions` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Permissions Primary Key',
  `group_id` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #_groups.id',
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__assets.id',
  `action_id` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__actions.id',
  PRIMARY KEY  (`id`)
)  DEFAULT CHARSET=utf8;

# groups
INSERT INTO `#__assets` SELECT DISTINCT `asset_id`, '#__groups' FROM `#__groups`;
# administrator has full control of groups (no 1=login needed)
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT 1, a.asset_id, b.id FROM `#__groups` a, `#__actions` b WHERE b.id > 1;

# applications
INSERT INTO `#__assets` SELECT DISTINCT `asset_id`, '#__applications' FROM `#__applications`;
# administrator has full control
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT 1, a.asset_id, b.id FROM `#__applications` a, `#__actions` b where b.id <> 3;
# load access (View Level Access) permissions
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT a.access, a.asset_id, b.id FROM `#__applications` a, `#__actions` b where b.id = 3;

# categories
INSERT INTO `#__assets` SELECT DISTINCT `asset_id`, '#__categories' FROM `#__categories`;
# administrator has full control
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT 1, a.asset_id, b.id FROM `#__categories` a, `#__actions` b where b.id NOT IN (1, 3);
# load access (View Level Access) permissions
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT a.access, a.asset_id, b.id FROM `#__categories` a, `#__actions` b where b.id = 3;

# articles
INSERT INTO `#__assets` SELECT DISTINCT `asset_id`, '#__articles' FROM `#__articles`;
# administrator has full control
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT 1, a.asset_id, b.id FROM `#__articles` a, `#__actions` b where b.id <> 1;
# load access (View Level Access) permissions
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT a.access, a.asset_id, b.id FROM `#__articles` a, `#__actions` b where b.id = 3;

# menus
INSERT INTO `#__assets` SELECT DISTINCT `asset_id`, '#__menu' FROM `#__menu`;
# administrator has full control
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT 1, a.asset_id, b.id FROM `#__menu` a, `#__actions` b where b.id <> 1;
# load access (View Level Access) permissions
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT a.access, a.asset_id, b.id FROM `#__menu` a, `#__actions` b where b.id = 3;

# extensions
INSERT INTO `#__assets` SELECT DISTINCT `asset_id`, '#__extensions' FROM `#__extensions`;
# administrator has full control
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT 1, a.asset_id, b.id FROM `#__extensions` a, `#__actions` b where b.id <> 1;
# load access (View Level Access) permissions
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT a.access, a.asset_id, b.id FROM `#__extensions` a, `#__actions` b where b.id = 3;

# modules
INSERT INTO `#__assets` SELECT DISTINCT asset_id, '#__modules' FROM `#__modules`;
# administrator has full control
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT 1, a.asset_id, b.id FROM `#__modules` a, `#__actions` b where b.id NOT IN (1, 3);
# load access (View Level Access) permissions
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT a.access, a.asset_id, b.id FROM `#__modules` a, `#__actions` b where b.id = 3;

# users
INSERT INTO `#__assets` SELECT DISTINCT asset_id, '#__users' FROM `#__users`;
# administrator has full control
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT 1, a.asset_id, b.id FROM `#__users` a, `#__actions` b where b.id NOT IN (1, 3);
# load access (View Level Access) permissions
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT a.access, a.asset_id, b.id FROM `#__users` a, `#__actions` b where b.id = 3;

/** aggregate permissions */
TRUNCATE TABLE `#__permissions_groups`
INSERT INTO `#__permissions_groups` (`group_id`,`asset_id`,`action_id`)
  SELECT DISTINCT `group_id`,`asset_id`,`action_id`
    FROM `#__temp_permissions`;

TRUNCATE TABLE `#__permissions_groupings`
INSERT INTO `#__permissions_groupings` ( `grouping_id`, `asset_id`, `action_id`)
  SELECT DISTINCT b.grouping_id, a.asset_id, a.action_id
  FROM #__temp_permissions a,
    #__group_to_groupings b
  WHERE a.group_id = b.group_id;

DROP TABLE `#__temp_permissions`;


