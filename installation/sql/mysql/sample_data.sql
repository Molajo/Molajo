# @version		$Id: sample_data.sql 21061 2011-04-03 16:50:11Z dextercowley $
#
# IMPORTANT - THIS FILE MUST BE SAVED WITH UTF-8 ENCODING ONLY. BEWARE IF EDITING!
#

#
# CONTENT
#

INSERT INTO `#__categories` ( `id`, `parent_id`, `asset_id`,  `lft`, `rgt`, `level`, `path`, `extension`, `title`, `subtitle`, `alias`, `note`,
  `description`, `published`, `checked_out`, `checked_out_time`, `params`, `metadesc`, `metakey`,
  `metadata`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hits`, `language`)
  VALUES
    (1, 0, 8000, 0, 3, 0, '', 'system', 'ROOT', '', 'root', '', '', 1, 0, '0000-00-00 00:00:00', '{}', '', '', '', 0, '2010-03-18 16:07:09', 0, '0000-00-00 00:00:00', 0, '*'),
    (2, 1, 8005, 1, 2, 1, 'articles', 'com_articles', 'Articles', 'Category for Articles', 'articles', '', '', 1, 0, '0000-00-00 00:00:00', '{"category_layout":"","image":""}', '', '', '{"author":"","robots":""}', 42, '2010-06-28 13:26:37', 42, '2011-06-03 16:52:26', 0, '*');

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
    (1, 8000, '__categories', 'com_categories', '', 'index.php?option=com_categories&view=category&id=1', 1),
    (2, 8005, '__categories', 'com_categories', 'articles', 'index.php?option=com_categories&view=category&id=2', 1);

INSERT INTO `#__articles` (
  `id`, `catid`, `asset_id`, `title`, `subtitle`, `alias`, `content_type`, `content_text`,
  `content_link`, `content_email_address`,
  `content_numeric_value`, `content_file`, `featured`, `stickied`, `user_default`, `category_default`,
  `language`, `ordering`, `state`, `publish_up`, `publish_down`, `version`, `version_of_id`, `state_prior_to_version`,
  `created`, `created_by`, `created_by_alias`, `created_by_email`, `created_by_website`, `created_by_ip_address`,
  `created_by_referer`, `modified`, `modified_by`, `checked_out`, `checked_out_time`,
  `component_option`, `component_id`, `parent_id`, `lft`, `rgt`, `level`, `metakey`, `metadesc`, `metadata`,
  `attribs`, `params`)
  VALUES
    (1, 2, 8210, 'My First Article', 'Subtitle for My First Article', 'my-first-article', 10, '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>', NULL, NULL, NULL, '', 1, 0, 1, 1, '*', 1, 1, '2011-05-06 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-05-06 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-05-27 13:26:26', 42, 0, '0000-00-00 00:00:00', 'com_articles', 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', ''),
    (2, 2, 8220, 'My Second Article', 'Subtitle for My Second Article', 'my-second-article', 10, '<h1>HTML Ipsum Presents</h1>\r\n	       \r\n<p><strong>Pellentesque habitant morbi tristique</strong> senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. <em>Aenean ultricies mi vitae est.</em> Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, <code>commodo vitae</code>, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. <a href="#">Donec non enim</a> in turpis pulvinar facilisis. Ut felis.</p>\r\n\r\n<h2>Header Level 2</h2>\r\n	       \r\n<ol>\r\n   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>\r\n   <li>Aliquam tincidunt mauris eu risus.</li>\r\n</ol>\r\n\r\n<blockquote><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus magna. Cras in mi at felis aliquet congue. Ut a est eget ligula molestie gravida. Curabitur massa. Donec eleifend, libero at sagittis mollis, tellus est malesuada tellus, at luctus turpis elit sit amet quam. Vivamus pretium ornare est.</p></blockquote>\r\n\r\n<h3>Header Level 3</h3>\r\n\r\n<ul>\r\n   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>\r\n   <li>Aliquam tincidunt mauris eu risus.</li>\r\n</ul>\r\n\r\n<pre><code>\r\n#header h1 a { \r\n	display: block; \r\n	width: 300px; \r\n	height: 80px; \r\n}\r\n</code></pre>', NULL, NULL, NULL, '', 0, 0, 0, 0, '*', 2, 1, '2011-06-06 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-06-06 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-05-27 13:26:26', 42, 42, '0000-00-00 00:00:00', 'com_articles', 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', ''),
    (3, 2, 8230, 'My Third Article', 'Subtitle for My Third Article', 'my-third-article', 10, '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\r\n\r\n<ol>\r\n   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>\r\n   <li>Aliquam tincidunt mauris eu risus.</li>\r\n   <li>Vestibulum auctor dapibus neque.</li>\r\n</ol>\r\n\r\n<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\r\n	       ', NULL, NULL, NULL, '', 0, 0, 0, 0, '*', 3, 1, '2011-06-10 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-06-10 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-06-10 00:00:00', 42, 42, '0000-00-00 00:00:00', 'com_articles', 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', ''),
    (4, 2, 8240, 'My Fourth Article', 'Subtitle for My Fourth Article', 'my-fourth-article', 10, '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>', NULL, NULL, NULL, '', 0, 0, 0, 0, '*', 4, 1, '2011-06-11 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-06-11 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-06-11 00:00:00', 42, 42, '0000-00-00 00:00:00', 'com_articles', 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', ''),
    (5, 2, 8250, 'My Fifth Article', 'Subtitle for My Fifth Article', 'my-fifth-article', 10, '<dl> <dt>Definition list</dt>\r\n   <dd>Consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna \r\naliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea \r\ncommodo consequat.</dd>\r\n   <dt>Lorem ipsum dolor sit amet</dt>\r\n   <dd>Consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna \r\naliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea \r\ncommodo consequat.</dd>\r\n</dl>', NULL, NULL, NULL, '', 0, 0, 0, 0, '*', 5, 1, '2011-06-27 13:26:26', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-06-27 13:26:26', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-06-27 13:26:26', 42, 42, '0000-00-00 00:00:00', 'com_articles', 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '');

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
    (1, 8210, '__articles', 'com_articles', 'articles/my-first-article', 'index.php?option=com_articles&view=article&id=1', 1),
    (2, 8220, '__articles', 'com_articles', 'articles/my-second-article', 'index.php?option=com_articles&view=article&id=2', 1),
    (3, 8230, '__articles', 'com_articles', 'articles/my-third-article', 'index.php?option=com_articles&view=article&id=3', 1),
    (4, 8240, '__articles', 'com_articles', 'articles/my-fourth-article', 'index.php?option=com_articles&view=article&id=4', 1),
    (5, 8250, '__articles', 'com_articles', 'articles/my-fifth-article', 'index.php?option=com_articles&view=article&id=5', 1);

#
# Actions
#

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

#
# 3-View
#
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`)
  SELECT DISTINCT c.group_id as group_id, b.id as asset_id, 3 as `action_id`
    FROM `#__groups`  a,
      `#__assets`     b,
      `#__group_to_groupings` c
    WHERE a.id = c.group_id
      AND b.access = c.grouping_id

#
# 4-Edit, 5-Publish, 6-Delete
#
INSERT INTO `#__temp_permissions` (`group_id`, `asset_id`, `action_id`)
  SELECT DISTINCT a.id as group_id, b.id as asset_id, c.id as action_id
    FROM `#__groups`  a,
      `#__assets`     b,
      `#__actions`    c
    WHERE a.id = 4
      AND c.id IN (4, 5, 6)

#
# 2-Create, 7-Admin for Components
#
INSERT INTO `#__temp_permissions` (`group_id`, `asset_id`, `action_id`)
  SELECT DISTINCT a.id as group_id, b.asset_id, c.id as action_id
    FROM `#__groups`  a,
      `#__extensions` b,
      `#__actions`    c
    WHERE a.id = 4
      AND c.id IN (2, 7)
      AND b.type = 'component'
      AND b.application_id = 1

#
# 1-Login in Site Application
#
 SELECT DISTINCT a.id as group_id, b.asset_id, c.id as action_id
    FROM `#__groups`  a,
      `#__applications` b,
      `#__actions`    c
    WHERE a.id IN (3, 4)
      AND c.id IN (1)
      AND b.application_id = 0

#
# 1-Login in Administrator
#
 SELECT DISTINCT a.id as group_id, b.asset_id, c.id as action_id
    FROM `#__groups`  a,
      `#__applications` b,
      `#__actions`    c
    WHERE a.id = 4
      AND c.id IN (1)
      AND b.application_id = 1

/** aggregate permissions */
TRUNCATE TABLE `#__permissions_groups`;
INSERT INTO `#__permissions_groups` (`group_id`,`asset_id`,`action_id`)
  SELECT DISTINCT `group_id`,`asset_id`,`action_id`
    FROM `#__temp_permissions`;

TRUNCATE TABLE `#__permissions_groupings`;
INSERT INTO `#__permissions_groupings` ( `grouping_id`, `asset_id`, `action_id`)
  SELECT DISTINCT b.grouping_id, a.asset_id, a.action_id
  FROM #__temp_permissions a,
    #__group_to_groupings b
  WHERE a.group_id = b.group_id;

DROP TABLE `#__temp_permissions`;


