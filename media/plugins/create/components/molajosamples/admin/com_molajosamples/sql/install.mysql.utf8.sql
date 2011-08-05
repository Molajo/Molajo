/* primary component table */;
CREATE TABLE IF NOT EXISTS `#__molajosamples` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `catid` INT (11) NOT NULL COMMENT 'Category ID associated with the Primary Key',

  `title` VARCHAR (255) NOT NULL COMMENT 'Title',
  `alias` VARCHAR (255) NOT NULL COMMENT 'URL Alias',

  `content_type` tinyint(3) NOT NULL DEFAULT '0' COMMENT 'Content Type: Links to #__configuration.option_id = 10 and component_option values matching ',

  `content_text` MEDIUMTEXT NULL COMMENT 'Content Primary Text Field, can include break to designate Introductory and Full text',
  `content_link` VARCHAR (2083) NULL COMMENT 'Content Link for Weblink or Newsfeed Field',
  `content_email_address` VARCHAR (255) NULL COMMENT 'Content Email Field',
  `content_numeric_value` TINYINT (3) NULL COMMENT 'Content Numeric Value, ex. vote on poll',
  `content_file` VARCHAR (255) NOT NULL DEFAULT '' COMMENT 'Content Network Path to File',

  `featured` boolean NOT NULL DEFAULT '0' COMMENT 'Featured 1-Featured 0-Not Featured',
  `stickied` boolean NOT NULL DEFAULT '0' COMMENT 'Stickied 1-Stickied 0-Not Stickied',
  `user_default` boolean NOT NULL DEFAULT '0' COMMENT 'User Default 1-Default 0-Not Default',
  `category_default` boolean NOT NULL DEFAULT '0' COMMENT 'Category Default 1-Default 0-Not Default',
  `language` CHAR (7) NOT NULL DEFAULT '' COMMENT 'Language',
  `ordering` INT (11) NOT NULL DEFAULT '0' COMMENT 'Ordering',

  `state` TINYINT (3) NOT NULL DEFAULT '0' COMMENT 'Published State 2: Archived 1: Published 0: Unpublished -1: Trashed -2: Spam -10 Version',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish Begin Date and Time',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish End Date and Time',
  `version` INTEGER UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Version Number',
  `version_of_id` INT (11) NULL COMMENT 'Category ID associated with the Primary Key',
  `state_prior_to_version` INTEGER UNSIGNED NULL COMMENT 'State value prior to creating this version copy and changing the state to Version',

  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'  COMMENT 'Created Date and Time',
  `created_by` INTEGER UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Created by User ID',
  `created_by_alias` VARCHAR (255) NOT NULL DEFAULT '' COMMENT 'Created by Alias',
  `created_by_email` VARCHAR (255) NULL COMMENT 'Created By Email Address',
  `created_by_website` VARCHAR (255) NULL COMMENT 'Created By Website',
  `created_by_ip_address` CHAR(15) NULL COMMENT 'Created By IP Address',
  `created_by_referer` VARCHAR (255) NULL COMMENT 'Created By Referer',

  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modified Date',
  `modified_by` INTEGER UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Modified By User ID',

  `checked_out` INTEGER UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Checked out by User Id',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Checked out Date and Time',

  `asset_id` INTEGER UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `access` INTEGER UNSIGNED NOT NULL DEFAULT 0 COMMENT 'View Level Access',

  `component_option` VARCHAR(50) NOT NULL COMMENT 'Component Option Value',
  `component_id` INT (11) NOT NULL COMMENT 'Primary Key for Component Content',
  `parent_id` INT (11) NULL COMMENT 'Nested set parent',
  `lft` INT (11) NULL COMMENT 'Nested set lft',
  `rgt` INT (11) NULL COMMENT 'Nested set rgt',
  `level` INT (11) NULL DEFAULT '0' COMMENT 'The cached level in the nested tree',

  `metakey` TEXT NULL COMMENT 'Meta Key',
  `metadesc` TEXT NULL COMMENT 'Meta Description',
  `metadata` TEXT NULL COMMENT 'Meta Data',

  `attribs` TEXT NULL COMMENT 'Attributes (Custom Fields)',

  `params` TEXT NULL COMMENT 'Parameters (Content Detail Parameters)',

  PRIMARY KEY  (`id`),

  KEY `idx_component_component_id_id` (`component_option`, `component_id`, `id`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`state`),
  KEY `idx_catid` (`catid`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_featured_catid` (`featured`,`catid`),
  KEY `idx_stickied_catid` (`stickied`,`catid`),
  KEY `idx_language` (`language`)

) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/** 1. ASSET RECORD FOR COMPONENT **/

/* Insert Asset Record for Component */
INSERT INTO `#__assets` (`parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES
(1, 0, 0, 1, 'com_molajosamples', 'com_molajosamples', '{"admin":{"4":1},"view":[1],"manage":{"6":1},"create":{"3":1},"delete":[],"edit":{"4":1},"edit.state":{"5":1},"edit.own":[]}');

/* Update RGT for Component Asset Record */
SET @max_rgt = (SELECT MAX(rgt)+1 FROM `#__assets` WHERE `id` <> 1);
UPDATE `#__assets` SET rgt = @max_rgt WHERE `name` = 'com_molajosamples';

/* Update LFT for Component Asset Record */
SET @max_lft = (SELECT MAX(lft)+1 FROM `#__assets` WHERE `id` <> 1);
UPDATE `#__assets` SET lft = @max_lft WHERE `name` = 'com_molajosamples';

/** 2. CATEGORY RECORD **/

/* Insert Category without ID or Asset ID */
INSERT INTO `#__categories` (`level`, `parent_id`, `path`, `extension`, `title`, `alias`, `note`, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`, `metadesc`, `metakey`, `metadata`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hits`, `language`) VALUES
    (1, 1, 'uncategorised', 'com_molajosamples', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 1, '{"target":"","image":""}', '', '', '{"page_title":"","author":"","robots":""}', 42, '2010-06-28 13:26:37', 0, '0000-00-00 00:00:00', 0, '*');

/* Update RGT for Category Record */
SET @max_rgt = (SELECT MAX(rgt)+1 FROM `#__categories` WHERE `id` <> 1);
UPDATE `#__categories` SET rgt = @max_rgt WHERE `extension` = 'com_molajosamples';

/* Update LFT for Category Record */
SET @max_lft = (SELECT MAX(lft)+1 FROM `#__categories` WHERE `id` <> 1);
UPDATE `#__categories` SET lft = @max_lft WHERE `extension` = 'com_molajosamples';

/** 3. ASSET RECORD FOR CATEGORY **/

/* Create Category Asset Record */
INSERT INTO `#__assets` (`level`, `name`, `title`, `rules`)
    SELECT 2 as `level`, CONCAT('com_molajosamples.category', '.', id) as `name`, title as `title`, '{"create":[],"delete":[],"edit":[],"edit.state":[],"edit.own":[]}' as `rules`
     FROM `#__categories`
     WHERE extension = 'com_molajosamples';

/* Update Category Record for Asset ID */
SET @asset_id = (SELECT id FROM `#__assets` WHERE `name` like 'com_molajosamples.category.%');
UPDATE `#__categories` SET asset_id = @asset_id WHERE `extension` = 'com_molajosamples';

/* Update Parent ID for Component Asset Record */
SET @parent_id = (SELECT id FROM `#__assets` WHERE `name` = 'com_molajosamples');
UPDATE `#__assets` SET parent_id = @parent_id WHERE `name` like 'com_molajosamples.category.%';

/* Update RGT for Component Asset Record */
SET @max_rgt = (SELECT MAX(rgt)+1 FROM `#__assets` WHERE `id` <> 1);
UPDATE `#__assets` SET rgt = @max_rgt WHERE `name` like 'com_molajosamples.category.%';

/* Update LFT for Component Asset Record */
SET @max_lft = (SELECT MAX(lft)+1 FROM `#__assets` WHERE `id` <> 1);
UPDATE `#__assets` SET lft = @max_lft WHERE `name` like 'com_molajosamples.category.%';

/** 4 - Insert Content Record **/

/* Insert Sample Content Detail Line - both catid and asset id are 0 */
INSERT INTO `#__molajosamples` (`id`, `catid`, `title`, `alias`, `content_type`, `content_text`, `content_link`, `content_email_address`, `content_numeric_value`, `content_file`, `featured`, `stickied`, `language`, `ordering`, `state`, `publish_up`, `publish_down`, `version`, `version_of_id`, `state_prior_to_version`, `created`, `created_by`, `created_by_alias`, `created_by_email`, `created_by_website`, `created_by_ip_address`, `created_by_referer`, `modified`, `modified_by`, `checked_out`, `checked_out_time`, `asset_id`, `access`, `component_option`, `component_id`, `parent_id`, `lft`, `rgt`, `level`, `metakey`, `metadesc`, `metadata`, `attribs`, `params`) VALUES
(1, 0, 'My First Content', 'my-first-content', 10, 'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.', NULL, NULL, NULL, '', 1, 1, '*', 1, 1, '2011-03-06 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-03-06 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-03-06 00:00:00', 42, 0, '0000-00-00 00:00:00', 0, 1, '', 0, 0, NULL, NULL, 0, '', '', '{"robots":"1","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '{"show_title":"","link_titles":"","show_intro":"","show_category":"","link_category":"","show_parent_category":"","link_parent_category":"","show_author":"","link_author":"","show_create_date":"","show_modify_date":"","show_publish_date":"","show_item_navigation":"","show_icons":"","show_print_icon":"","show_email_icon":"","show_vote":"","show_hits":"","show_noauth":"","alternative_readmore":"","article_layout":""}');

/* Update Category Record for Asset ID */
SET @catid = (SELECT id FROM `#__categories` WHERE `extension` = 'com_molajosamples');
UPDATE `#__molajosamples` SET catid = @catid;

/** 5. ASSET RECORD FOR CONTENT **/

/* Create Content Asset Record */
INSERT INTO `#__assets` (`level`, `name`, `title`, `rules`)
    SELECT 3 as `level`, CONCAT('com_molajosamples.molajosample', '.', id) as `name`, title as `title`, '{"create":[],"delete":[],"edit":[],"edit.state":[],"edit.own":[]}' as `rules`
     FROM `#__molajosamples`;

/* Update Category Record for Asset ID */
SET @asset_id = (SELECT id FROM `#__assets` WHERE `name` like 'com_molajosamples.molajosample.%');
UPDATE `#__molajosamples` SET asset_id = @asset_id;

/* Update Parent ID for Component Asset Record */
SET @parent_id = (SELECT id FROM `#__assets` WHERE `name` = 'com_molajosamples');
UPDATE `#__assets` SET parent_id = @parent_id WHERE `name` like 'com_molajosamples.molajosample.%';

/* Update RGT for Component Asset Record */
SET @max_rgt = (SELECT MAX(rgt)+1 FROM `#__assets` WHERE `id` <> 1);
UPDATE `#__assets` SET rgt = @max_rgt WHERE `name` like 'com_molajosamples.molajosample.%';

/* Update LFT for Component Asset Record */
SET @max_lft = (SELECT MAX(lft)+1 FROM `#__assets` WHERE `id` <> 1);
UPDATE `#__assets` SET lft = @max_lft WHERE `name` like 'com_molajosamples.molajosample.%';

/** 6 - UPDATE ASSET ROOT **/

/* Update RGT for root */
SET @max_rgt = (SELECT MAX(rgt)+1 FROM `#__categories` WHERE `id` <> 1);
UPDATE `#__categories` SET rgt = @max_rgt WHERE id = 1;

/* FIELDS */

/* 010 MOLAJO_CONFIG_OPTION_ID_CONTENT_TYPES */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_molajosamples', 10, '', '', 0),
('com_molajosamples', 10, 'molajosamples', 'Molajosamples', 1);

/* VIEWS */

/* 020 MOLAJO_CONFIG_OPTION_ID_VIEW_PAIRS */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_molajosamples', 20, '', '', 0),
('com_molajosamples', 20, 'molajosample', 'molajosamples', 1);

/* TABLE */

/* 045 MOLAJO_CONFIG_OPTION_ID_TABLE */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_molajosamples', 45, '', '', 0),
('com_molajosamples', 45, '__molajosamples', '__molajosamples', 1);

/* 050 MOLAJO_CONFIG_OPTION_ID_EDIT_LAYOUTS */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_molajosamples', 50, '', '', 0),
('com_molajosamples', 50, 'molajosample', 'molajosample', 1);

/* 060 MOLAJO_CONFIG_OPTION_ID_DEFAULT_LAYOUT_APPLICATION1 */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_molajosamples', 60, '', '', 0),
('com_molajosamples', 60, 'molajosamples', 'molajosamples', 1);

/* initial configuration */
UPDATE `#__extensions`
SET params = '{"config_component_tags":"1","config_component_tag_categories":"1","config_component_state_spam":"0","config_component_enable_comments":"1","config_component_version_management":"1","config_component_maintain_version_count":5,"config_component_retain_versions_after_delete":"1","config_component_uninstall":"1","config_component_single_item_parameter1":"item","config_component_single_item_parameter2":"0","config_component_single_item_parameter3":"0","config_component_single_item_parameter4":"0","config_component_single_item_parameter5":"0","config_component_candy_editor_parameter1":"item","config_component_candy_editor_parameter2":"0","config_component_candy_editor_parameter3":"0","config_component_candy_editor_parameter4":"0","config_component_candy_editor_parameter5":"0","config_component_candy_default_parameter1":"item","config_component_candy_default_parameter2":"0","config_component_candy_default_parameter3":"0","config_component_candy_default_parameter4":"0","config_component_candy_default_parameter5":"0","config_component_land_blog_parameter1":"category","config_component_land_blog_parameter2":"blog","config_component_land_blog_parameter3":"item","config_component_land_blog_parameter4":"integration","config_component_land_blog_parameter5":"0","config_component_land_default_parameter1":"category","config_component_land_default_parameter2":"list","config_component_land_default_parameter3":"item","config_component_land_default_parameter4":"integration","config_component_land_default_parameter5":"0","config_manager_title":"1","config_manager_button_bar_option1":"new","config_manager_button_bar_option2":"edit","config_manager_button_bar_option3":"checkin","config_manager_button_bar_option4":"separator","config_manager_button_bar_option5":"publish","config_manager_button_bar_option6":"unpublish","config_manager_button_bar_option7":"feature","config_manager_button_bar_option8":"sticky","config_manager_button_bar_option9":"archive","config_manager_button_bar_option10":"separator","config_manager_button_bar_option11":"spam","config_manager_button_bar_option12":"trash","config_manager_button_bar_option13":"delete","config_manager_button_bar_option14":"restore","config_manager_button_bar_option15":"separator","config_manager_button_bar_option16":"options","config_manager_button_bar_option17":"separator","config_manager_button_bar_option18":"help","config_manager_button_bar_option19":"0","config_manager_button_bar_option20":"0","config_manager_sub_menu_for_content_types":"0","config_manager_sub_menu1":"default","config_manager_sub_menu2":"category","config_manager_sub_menu3":"featured","config_manager_sub_menu4":"revisions","config_manager_sub_menu5":"0","config_manager_list_search":"1","config_manager_list_filters1":"catid","config_manager_list_filters2":"state","config_manager_list_filters3":"featured","config_manager_list_filters4":"created_by","config_manager_list_filters5":"access","config_manager_list_filters6":"0","config_manager_list_filters7":"0","config_manager_list_filters8":"0","config_manager_list_filters9":"0","config_manager_list_filters10":"0","config_manager_list_filters_query_filters1":"access","config_manager_list_filters_query_filters2":"catid","config_manager_list_filters_query_filters3":"created_by","config_manager_list_filters_query_filters4":"0","config_manager_list_filters_query_filters5":"0","config_manager_grid_column_display_alias":"1","config_manager_grid_column1":"id","config_manager_grid_column2":"title","config_manager_grid_column3":"created_by","config_manager_grid_column4":"state","config_manager_grid_column5":"publish_up","config_manager_grid_column6":"publish_down","config_manager_grid_column7":"featured","config_manager_grid_column8":"stickied","config_manager_grid_column9":"catid","config_manager_grid_column10":"ordering","config_manager_grid_column11":"0","config_manager_grid_column12":"0","config_manager_grid_column13":"0","config_manager_grid_column14":"0","config_manager_grid_column15":"0","config_manager_editor_button_bar_new_option1":"apply","config_manager_editor_button_bar_new_option2":"save","config_manager_editor_button_bar_new_option3":"save2new","config_manager_editor_button_bar_new_option4":"close","config_manager_editor_button_bar_new_option5":"help","config_manager_editor_button_bar_new_option6":"0","config_manager_editor_button_bar_new_option7":"0","config_manager_editor_button_bar_new_option8":"0","config_manager_editor_button_bar_new_option9":"0","config_manager_editor_button_bar_new_option10":"0","config_manager_editor_button_bar_edit_option1":"save","config_manager_editor_button_bar_edit_option2":"0","config_manager_editor_button_bar_edit_option3":"save2new","config_manager_editor_button_bar_edit_option4":"save2copy","config_manager_editor_button_bar_edit_option5":"close","config_manager_editor_button_bar_edit_option6":"help","config_manager_editor_button_bar_edit_option7":"0","config_manager_editor_button_bar_edit_option8":"0","config_manager_editor_button_bar_edit_option9":"0","config_manager_editor_button_bar_edit_option10":"0","config_manager_editor_buttons1":"article","config_manager_editor_buttons2":"image","config_manager_editor_buttons3":"pagebreak","config_manager_editor_buttons4":"readmore","config_manager_editor_buttons5":"audio","config_manager_editor_buttons6":"video","config_manager_editor_buttons7":"file","config_manager_editor_buttons8":"gallery","config_manager_editor_buttons9":"0","config_manager_editor_buttons10":"0","config_manager_editor_left_top_column1":"title","config_manager_editor_left_top_column2":"alias","config_manager_editor_left_top_column3":"id","config_manager_editor_left_top_column4":"0","config_manager_editor_left_top_column5":"0","config_manager_editor_left_top_column6":"0","config_manager_editor_left_top_column7":"0","config_manager_editor_left_top_column8":"0","config_manager_editor_left_top_column9":"0","config_manager_editor_left_top_column10":"0","config_manager_editor_primary_column1":"content_text","config_manager_editor_left_bottom_column1":"catid","config_manager_editor_left_bottom_column2":"featured","config_manager_editor_left_bottom_column3":"stickied","config_manager_editor_left_bottom_column4":"language","config_manager_editor_left_bottom_column5":"0","config_manager_editor_right_publishing_column1":"state","config_manager_editor_right_publishing_column2":"created_by","config_manager_editor_right_publishing_column3":"created_by_alias","config_manager_editor_right_publishing_column4":"created","config_manager_editor_right_publishing_column5":"publish_up","config_manager_editor_right_publishing_column6":"publish_down","config_manager_editor_right_publishing_column7":"0","config_manager_editor_right_publishing_column8":"0","config_manager_editor_right_publishing_column9":"0","config_manager_editor_right_publishing_column10":"0","config_manager_editor_attribs":"1","config_manager_editor_params":"1","config_manager_editor_metadata":"1","params":{"show_title":"","link_titles":"","show_intro":"","show_category":"","link_category":"","show_parent_category":"","link_parent_category":"","show_author":"","link_author":"","show_create_date":"","show_modify_date":"","show_publish_date":"","show_item_navigation":"","show_vote":"","show_icons":"","show_print_icon":"","show_email_icon":"","show_hits":"","show_noauth":"","show_category_title":"","show_description":"","show_description_image":"","maxLevel":"","show_empty_categories":"","show_no_articles":"","show_subcat_desc":"","show_cat_num_articles":"","num_leading_articles":"1","num_intro_articles":"4","num_columns":"2","num_links":"3","multi_column_order":"1","show_subcategory_content":"","orderby_pri":"","orderby_sec":"","order_date":"","show_pagination":"","show_pagination_results":"","show_feed_link":"","feed_summary":"","show_pagination_limit":"","filter_field":"","show_headings":"","list_show_date":"","date_format":"","list_show_hits":"","list_show_author":"","display_num":"10"}}'
WHERE element = 'MOLAJOSAMPLES';