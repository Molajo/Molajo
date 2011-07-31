/* primary component table */;

CREATE TABLE IF NOT EXISTS `#__articles` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `catid` INT (11) UNSIGNED NOT NULL DEFAULT  0 COMMENT 'Category ID associated with the Primary Key',

  `title` VARCHAR (255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `alias` VARCHAR (255) NOT NULL DEFAULT ' ' COMMENT 'URL Alias',

  `content_type` tinyint(3) NOT NULL DEFAULT '0' COMMENT 'Content Type: Links to #__configuration.option_id = 10 and component_option values matching ',

  `content_text` MEDIUMTEXT NULL COMMENT 'Content Primary Text Field, can include break to designate Introductory and Full text',
  `content_link` VARCHAR (2083) NULL COMMENT 'Content Link for Weblink or Newsfeed Field',
  `content_email_address` VARCHAR (255) NULL COMMENT 'Content Email Field',
  `content_numeric_value` TINYINT (3) NULL COMMENT 'Content Numeric Value, ex. vote on poll',
  `content_file` VARCHAR (255) NOT NULL DEFAULT '' COMMENT 'Content Network Path to File',

  `featured` boolean NOT NULL DEFAULT '0' COMMENT 'Featured 1-Featured 0-Not Featured',
  `stickied` boolean NOT NULL DEFAULT '0' COMMENT 'Stickied 1-Stickied 0-Not Stickied',
  `user_default` boolean NOT NULL DEFAULT '0' COMMENT 'User DEFAULT 1-DEFAULT 0-Not DEFAULT',
  `category_default` boolean NOT NULL DEFAULT '0' COMMENT 'Category DEFAULT 1-DEFAULT 0-Not DEFAULT',
  `language` CHAR (7) NOT NULL DEFAULT '' COMMENT 'Language',
  `ordering` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Ordering',

  `state` TINYINT (3) NOT NULL DEFAULT '0' COMMENT 'Published State 2: Archived 1: Published 0: Unpublished -1: Trashed -2: Spam -10 Version',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish Begin Date and Time',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish End Date and Time',
  `version` INT (11) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Version Number',
  `version_of_id` INT (11) NULL COMMENT 'Category ID associated with the Primary Key',
  `state_prior_to_version` INT (11) UNSIGNED NULL COMMENT 'State value prior to creating this version copy and changing the state to Version',

  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'  COMMENT 'Created Date and Time',
  `created_by` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Created by User ID',
  `created_by_alias` VARCHAR (255) NOT NULL DEFAULT '' COMMENT 'Created by Alias',
  `created_by_email` VARCHAR (255) NULL COMMENT 'Created By Email Address',
  `created_by_website` VARCHAR (255) NULL COMMENT 'Created By Website',
  `created_by_ip_address` CHAR(15) NULL COMMENT 'Created By IP Address',
  `created_by_referer` VARCHAR (255) NULL COMMENT 'Created By Referer',

  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modified Date',
  `modified_by` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Modified By User ID',

  `checked_out` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Checked out by User Id',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Checked out Date and Time',

  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `access` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__groupings table',

  `component_option` VARCHAR(50) NOT NULL DEFAULT ' ' COMMENT 'Component Option Value',
  `component_id` INT (11) UNSIGNED NOT NULL DEFAULT  0 COMMENT 'Primary Key for Component Content',
  `parent_id` INT (11) NULL COMMENT 'Nested set parent',
  `lft` INT (11) NULL COMMENT 'Nested set lft',
  `rgt` INT (11) NULL COMMENT 'Nested set rgt',
  `level` INT (11) NULL DEFAULT '0' COMMENT 'The cached level in the nested tree',
  `metakey` TEXT NULL COMMENT 'Meta Key',
  `metadesc` TEXT NULL COMMENT 'Meta Description',
  `metadata` TEXT NULL COMMENT 'Meta Data',
  `attribs` TEXT NULL COMMENT 'Attributes (Custom Fields)',
  `params` MEDIUMTEXT COMMENT 'Configurable Parameter Values',

  PRIMARY KEY  (`id`),

  KEY `idx_component_component_id_id` (`component_option`, `component_id`, `id`),
  KEY `idx_access` (`access`),
  UNIQUE KEY `idx_asset_id` (`asset_id`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`state`),
  KEY `idx_catid` (`catid`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_featured_catid` (`featured`,`catid`),
  KEY `idx_stickied_catid` (`stickied`,`catid`),
  KEY `idx_language` (`language`)

) DEFAULT CHARSET=utf8;

#
# CONTENT
#
SET @DefaultView_access = 1;
SET @max_asset_id = (SELECT MAX(id) FROM `#__assets`);

INSERT INTO `#__categories` ( `parent_id`, `lft`, `rgt`, `level`, `path`, `extension`, `title`, `alias`, `note`,
  `description`, `published`, `checked_out`, `checked_out_time`, `params`, `metadesc`, `metakey`,
  `metadata`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hits`, `language`,
  `access`, `asset_id` )
  VALUES
    (1, 1, 2, 1, 'uncategorised', 'com_articles', 'Articles', 'articles', '', '', 1, 0, '0000-00-00 00:00:00', '{"category_layout":"","image":""}', '', '', '{"author":"","robots":""}', 42, '2010-06-28 13:26:37', 42, '2011-06-03 16:52:26', 0, '*', @DefaultView_access, @max_asset_id);

SET @catid = (SELECT MAX(id) FROM `#__categories` WHERE `extension` = 'com_articles');

INSERT INTO `#__articles` (
  `id`, `catid`, `title`, `alias`, `content_type`, `content_text`, `content_link`, `content_email_address`,
  `content_numeric_value`, `content_file`, `featured`, `stickied`, `user_default`, `category_default`,
  `language`, `ordering`, `state`, `publish_up`, `publish_down`, `version`, `version_of_id`, `state_prior_to_version`,
  `created`, `created_by`, `created_by_alias`, `created_by_email`, `created_by_website`, `created_by_ip_address`,
  `created_by_referer`, `modified`, `modified_by`, `checked_out`, `checked_out_time`,
  `component_option`, `component_id`, `parent_id`, `lft`, `rgt`, `level`, `metakey`, `metadesc`, `metadata`,
  `attribs`, `params`, `access`, `asset_id` )
  VALUES
    (1, @catid, 'My First Article', 'my-first-article', 10, '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>', NULL, NULL, NULL, '', 1, 0, 1, 1, '*', 1, 1, '2011-05-06 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-05-06 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-05-27 13:26:26', 42, 0, '0000-00-00 00:00:00', 'com_articles', 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', @DefaultView_access, @max_asset_id++),
    (2, @catid, 'My Second Article', 'my-second-article', 10, '<h1>HTML Ipsum Presents</h1>\r\n	       \r\n<p><strong>Pellentesque habitant morbi tristique</strong> senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. <em>Aenean ultricies mi vitae est.</em> Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, <code>commodo vitae</code>, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. <a href="#">Donec non enim</a> in turpis pulvinar facilisis. Ut felis.</p>\r\n\r\n<h2>Header Level 2</h2>\r\n	       \r\n<ol>\r\n   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>\r\n   <li>Aliquam tincidunt mauris eu risus.</li>\r\n</ol>\r\n\r\n<blockquote><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus magna. Cras in mi at felis aliquet congue. Ut a est eget ligula molestie gravida. Curabitur massa. Donec eleifend, libero at sagittis mollis, tellus est malesuada tellus, at luctus turpis elit sit amet quam. Vivamus pretium ornare est.</p></blockquote>\r\n\r\n<h3>Header Level 3</h3>\r\n\r\n<ul>\r\n   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>\r\n   <li>Aliquam tincidunt mauris eu risus.</li>\r\n</ul>\r\n\r\n<pre><code>\r\n#header h1 a { \r\n	display: block; \r\n	width: 300px; \r\n	height: 80px; \r\n}\r\n</code></pre>', NULL, NULL, NULL, '', 0, 0, 0, 0, '*', 2, 1, '2011-06-06 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-06-06 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-05-27 13:26:26', 42, 42, '0000-00-00 00:00:00', 'com_articles', 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', @DefaultView_access, @max_asset_id++),
    (3, @catid, 'My Third Article', 'my-third-article', 10, '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\r\n\r\n<ol>\r\n   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>\r\n   <li>Aliquam tincidunt mauris eu risus.</li>\r\n   <li>Vestibulum auctor dapibus neque.</li>\r\n</ol>\r\n\r\n<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\r\n	       ', NULL, NULL, NULL, '', 0, 0, 0, 0, '*', 3, 1, '2011-06-10 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-06-10 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-06-10 00:00:00', 42, 42, '0000-00-00 00:00:00', 'com_articles', 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', @DefaultView_access, @max_asset_id++),
    (4, @catid, 'My Fourth Article', 'my-fourth-article', 10, '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>', NULL, NULL, NULL, '', 0, 0, 0, 0, '*', 4, 1, '2011-06-11 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-06-11 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-06-11 00:00:00', 42, 42, '0000-00-00 00:00:00', 'com_articles', 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', @DefaultView_access, @max_asset_id++),
    (5, @catid, 'My Fifth Article', 'my-fifth-article', 10, '<dl> <dt>Definition list</dt>\r\n   <dd>Consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna \r\naliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea \r\ncommodo consequat.</dd>\r\n   <dt>Lorem ipsum dolor sit amet</dt>\r\n   <dd>Consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna \r\naliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea \r\ncommodo consequat.</dd>\r\n</dl>', NULL, NULL, NULL, '', 0, 0, 0, 0, '*', 5, 1, '2011-06-27 13:26:26', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-06-27 13:26:26', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-06-27 13:26:26', 42, 42, '0000-00-00 00:00:00', 'com_articles', 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', @DefaultView_access, @max_asset_id++);


/** aggregate permissions */

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

# articles
INSERT INTO `#__assets` SELECT DISTINCT `asset_id`, '#__articles' FROM `#__articles`;
# administrator has full control
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT 1, a.asset_id, b.id FROM `#__articles` a, `#__actions` b where b.id <> 1;
# load access (View Level Access) permissions
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT a.access, a.asset_id, b.id FROM `#__articles` a, `#__actions` b where b.id = 3;

INSERT INTO `#__permissions_groups` (`group_id`,`asset_id`,`action_id`)
  SELECT DISTINCT `group_id`,`asset_id`,`action_id`
    FROM `#__temp_permissions`;

INSERT INTO `#__permissions_groupings` ( `grouping_id`, `asset_id`, `action_id`)
  SELECT DISTINCT b.grouping_id, a.asset_id, a.action_id
  FROM #__temp_permissions a,
    #__group_to_groupings b
  WHERE a.group_id = b.group_id;

DROP TABLE `#__temp_permissions`;

/* FIELDS */

/* 010 MOLAJO_CONFIG_OPTION_ID_CONTENT_TYPES */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 10, '', '', 0),
('com_articles', 10, 'articles', 'Articles', 1);

/* VIEWS */

/* 020 MOLAJO_CONFIG_OPTION_ID_VIEW_PAIRS */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 20, '', '', 0),
('com_articles', 20, 'article', 'articles', 1);

/* TABLE */

/* 045 MOLAJO_CONFIG_OPTION_ID_TABLE */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 45, '', '', 0),
('com_articles', 45, '__articles', '__articles', 1);

/* 050 MOLAJO_CONFIG_OPTION_ID_EDIT_LAYOUTS */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 50, '', '', 0),
('com_articles', 50, 'article', 'article', 1);

/* 060 MOLAJO_CONFIG_OPTION_ID_DEFAULT_LAYOUT_APPLICATION1 */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 60, '', '', 0),
('com_articles', 60, 'articles', 'articles', 1);

/* initial configuration */
UPDATE `#__extensions`
SET params = '{"config_component_tags":"1","config_component_tag_categories":"1","config_component_state_spam":"0","config_component_enable_comments":"1","config_component_version_management":"1","config_component_maintain_version_count":5,"config_component_retain_versions_after_delete":"1","config_component_uninstall":"1","config_component_single_item_parameter1":"item","config_component_single_item_parameter2":"0","config_component_single_item_parameter3":"0","config_component_single_item_parameter4":"0","config_component_single_item_parameter5":"0","config_component_candy_editor_parameter1":"item","config_component_candy_editor_parameter2":"0","config_component_candy_editor_parameter3":"0","config_component_candy_editor_parameter4":"0","config_component_candy_editor_parameter5":"0","config_component_candy_default_parameter1":"item","config_component_candy_default_parameter2":"0","config_component_candy_default_parameter3":"0","config_component_candy_default_parameter4":"0","config_component_candy_default_parameter5":"0","config_component_land_blog_parameter1":"category","config_component_land_blog_parameter2":"blog","config_component_land_blog_parameter3":"item","config_component_land_blog_parameter4":"integration","config_component_land_blog_parameter5":"0","config_component_land_default_parameter1":"category","config_component_land_default_parameter2":"list","config_component_land_default_parameter3":"item","config_component_land_default_parameter4":"integration","config_component_land_default_parameter5":"0","config_manager_title":"1","config_manager_button_bar_option1":"new","config_manager_button_bar_option2":"edit","config_manager_button_bar_option3":"checkin","config_manager_button_bar_option4":"separator","config_manager_button_bar_option5":"publish","config_manager_button_bar_option6":"unpublish","config_manager_button_bar_option7":"feature","config_manager_button_bar_option8":"sticky","config_manager_button_bar_option9":"archive","config_manager_button_bar_option10":"separator","config_manager_button_bar_option11":"spam","config_manager_button_bar_option12":"trash","config_manager_button_bar_option13":"delete","config_manager_button_bar_option14":"restore","config_manager_button_bar_option15":"separator","config_manager_button_bar_option16":"options","config_manager_button_bar_option17":"separator","config_manager_button_bar_option18":"help","config_manager_button_bar_option19":"0","config_manager_button_bar_option20":"0","config_manager_sub_menu_for_content_types":"0","config_manager_sub_menu1":"default","config_manager_sub_menu2":"category","config_manager_sub_menu3":"featured","config_manager_sub_menu4":"revisions","config_manager_sub_menu5":"0","config_manager_list_search":"1","config_manager_list_filters1":"catid","config_manager_list_filters2":"state","config_manager_list_filters3":"featured","config_manager_list_filters4":"created_by","config_manager_list_filters5":"access","config_manager_list_filters6":"0","config_manager_list_filters7":"0","config_manager_list_filters8":"0","config_manager_list_filters9":"0","config_manager_list_filters10":"0","config_manager_list_filters_query_filters1":"access","config_manager_list_filters_query_filters2":"catid","config_manager_list_filters_query_filters3":"created_by","config_manager_list_filters_query_filters4":"0","config_manager_list_filters_query_filters5":"0","config_manager_grid_column_display_alias":"1","config_manager_grid_column1":"id","config_manager_grid_column2":"title","config_manager_grid_column3":"created_by","config_manager_grid_column4":"state","config_manager_grid_column5":"publish_up","config_manager_grid_column6":"publish_down","config_manager_grid_column7":"featured","config_manager_grid_column8":"stickied","config_manager_grid_column9":"catid","config_manager_grid_column10":"ordering","config_manager_grid_column11":"0","config_manager_grid_column12":"0","config_manager_grid_column13":"0","config_manager_grid_column14":"0","config_manager_grid_column15":"0","config_manager_editor_button_bar_new_option1":"apply","config_manager_editor_button_bar_new_option2":"save","config_manager_editor_button_bar_new_option3":"save2new","config_manager_editor_button_bar_new_option4":"close","config_manager_editor_button_bar_new_option5":"help","config_manager_editor_button_bar_new_option6":"0","config_manager_editor_button_bar_new_option7":"0","config_manager_editor_button_bar_new_option8":"0","config_manager_editor_button_bar_new_option9":"0","config_manager_editor_button_bar_new_option10":"0","config_manager_editor_button_bar_edit_option1":"save","config_manager_editor_button_bar_edit_option2":"0","config_manager_editor_button_bar_edit_option3":"save2new","config_manager_editor_button_bar_edit_option4":"save2copy","config_manager_editor_button_bar_edit_option5":"close","config_manager_editor_button_bar_edit_option6":"help","config_manager_editor_button_bar_edit_option7":"0","config_manager_editor_button_bar_edit_option8":"0","config_manager_editor_button_bar_edit_option9":"0","config_manager_editor_button_bar_edit_option10":"0","config_manager_editor_buttons1":"article","config_manager_editor_buttons2":"image","config_manager_editor_buttons3":"pagebreak","config_manager_editor_buttons4":"readmore","config_manager_editor_buttons5":"audio","config_manager_editor_buttons6":"video","config_manager_editor_buttons7":"file","config_manager_editor_buttons8":"gallery","config_manager_editor_buttons9":"0","config_manager_editor_buttons10":"0","config_manager_editor_left_top_column1":"title","config_manager_editor_left_top_column2":"alias","config_manager_editor_left_top_column3":"id","config_manager_editor_left_top_column4":"0","config_manager_editor_left_top_column5":"0","config_manager_editor_left_top_column6":"0","config_manager_editor_left_top_column7":"0","config_manager_editor_left_top_column8":"0","config_manager_editor_left_top_column9":"0","config_manager_editor_left_top_column10":"0","config_manager_editor_primary_column1":"content_text","config_manager_editor_left_bottom_column1":"catid","config_manager_editor_left_bottom_column2":"featured","config_manager_editor_left_bottom_column3":"stickied","config_manager_editor_left_bottom_column4":"language","config_manager_editor_left_bottom_column5":"0","config_manager_editor_right_publishing_column1":"state","config_manager_editor_right_publishing_column2":"created_by","config_manager_editor_right_publishing_column3":"created_by_alias","config_manager_editor_right_publishing_column4":"created","config_manager_editor_right_publishing_column5":"publish_up","config_manager_editor_right_publishing_column6":"publish_down","config_manager_editor_right_publishing_column7":"0","config_manager_editor_right_publishing_column8":"0","config_manager_editor_right_publishing_column9":"0","config_manager_editor_right_publishing_column10":"0","config_manager_editor_attribs":"1","config_manager_editor_params":"1","config_manager_editor_metadata":"1","params":{"show_title":"","link_titles":"","show_intro":"","show_category":"","link_category":"","show_parent_category":"","link_parent_category":"","show_author":"","link_author":"","show_create_date":"","show_modify_date":"","show_publish_date":"","show_item_navigation":"","show_vote":"","show_icons":"","show_print_icon":"","show_email_icon":"","show_hits":"","show_noauth":"","show_category_title":"","show_description":"","show_description_image":"","maxLevel":"","show_empty_categories":"","show_no_articles":"","show_subcat_desc":"","show_cat_num_articles":"","num_leading_articles":"1","num_intro_articles":"4","num_columns":"2","num_links":"3","multi_column_order":"1","show_subcategory_content":"","orderby_pri":"","orderby_sec":"","order_date":"","show_pagination":"","show_pagination_results":"","show_feed_link":"","feed_summary":"","show_pagination_limit":"","filter_field":"","show_headings":"","list_show_date":"","date_format":"","list_show_hits":"","list_show_author":"","display_num":"10"}}'
WHERE element = 'COM_ARTICLES';