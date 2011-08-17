# $Id: tables.sql

#
# ACL
#

#
# Table structure for table `#__actions`
#
#   Contains a definitive list of ACL Actions that can be defined within Molajo
#   Login is the only action associated with Client. Client has no other actions beyond login.
#   View and Create permissions for an asset do not imply any other permissions. 
#   Edit permission includes permission to View the asset.
#   Delete permission includes permission to View and Edit the asset.
#   Admin permission includes all other permissions for that asset and configuration permission.
#

CREATE TABLE IF NOT EXISTS `#__actions` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key' ,  
  `title` VARCHAR(255) NOT NULL DEFAULT ' ',
  PRIMARY KEY (`id`) )
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

#
# Table structure for table `#__assets`
#
#   An Asset ID is a unique key assigned to any item (asset) subject to ACL control
#   The ACL Assets table contains a list of assigned ids and associated component_option
#   The asset id must be stored in the item using the column named asset_id
#

CREATE TABLE IF NOT EXISTS `#__assets` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Assets Primary Key' ,
  `content_table` VARCHAR(255) NOT NULL DEFAULT ' ',
  `content_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Content Primary Key',
  `option` VARCHAR(255) NOT NULL DEFAULT ' ',
  `path` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'URL',
  `link` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'The actually link the menu item refers to.',
  `access` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__groupings table',
  PRIMARY KEY (`id`) )
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

#
# CLIENTS (Applications)
#

#
# Table structure for table `#__applications`
#
CREATE TABLE `#__applications` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Application Primary Key',
  `application_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Numeric value associated with the application',
  `name` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `path` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'URL Alias',
  `description` mediumtext NOT NULL,
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `metakey` text COMMENT 'Meta Key',
  `metadesc` text COMMENT 'Meta Description',
  `metadata` text COMMENT 'Meta Data',
  `attribs` text COMMENT 'Attributes (Custom Fields)',
  `params` MEDIUMTEXT COMMENT 'Configurable Parameter Values',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_asset_id` (`asset_id`)
) DEFAULT CHARSET=utf8;

#
# USERS AND GROUPS
#

#
# Table structure for table `#__users`
#

CREATE TABLE IF NOT EXISTS `#__users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL DEFAULT '  ',
  `username` VARCHAR(150) NOT NULL DEFAULT '  ',
  `email` VARCHAR(255) NOT NULL DEFAULT '  ',
  `password` VARCHAR(100) NOT NULL DEFAULT '  ',
  `block` TINYINT(4) NOT NULL DEFAULT 0 ,
  `sendEmail` TINYINT(4) NULL DEFAULT 0 ,
  `registerDate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `lastvisitDate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `activation` VARCHAR(100) NOT NULL DEFAULT '  ',
  `params` MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Configurable Parameter Values' ,
  `asset_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  PRIMARY KEY (`id`) )
DEFAULT CHARACTER SET = utf8;

#
# Table structure for table `#__user_profiles`
#

CREATE TABLE IF NOT EXISTS `#__user_profiles` (
  `user_id` INT(11) NOT NULL ,
  `profile_key` VARCHAR(100) NOT NULL ,
  `profile_value` VARCHAR(255) NOT NULL ,
  `ordering` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Ordering' )
DEFAULT CHARACTER SET = utf8;

#
# Table structure for table `#__groups`
#
#   A group is a structure for defining a set of user(s) for the purpose of assigning permissions or other applications
#   When a user is assigned to a Group, that user is also a member of existing and future child groups
#   Each user is also assigned a special group that can be used to assign "Edit Own", "View Own" or "Delete Own" Permissions
#   "User Groups" are also a good tool to add someone to a specific item, rather than all assets associated with a Group
#   In smaller implementations or social networks, "User Groups" provides support for friending, etc.
#

CREATE TABLE IF NOT EXISTS `#__groups` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Group Primary Key' ,
  `parent_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Parent ID' ,
  `lft` INT(11) NOT NULL DEFAULT 0 COMMENT 'Nested set lft.' ,
  `rgt` INT(11) NOT NULL DEFAULT 0 COMMENT 'Nested set rgt.' ,
  `title` VARCHAR(255) NOT NULL DEFAULT '  ',
  `subtitle` VARCHAR(255) NOT NULL DEFAULT ' ',
  `description` MEDIUMTEXT NOT NULL ,
  `type_id` INT(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Users: 0, Groups: 1' ,
  `asset_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.' ,
  `protected` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'If true, protects group from system removal via the interface.' ,
  PRIMARY KEY (`id`) )
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

#
# Table structure for table `#__user_groups`
#
#   Groups to which users belong
#

CREATE TABLE IF NOT EXISTS `#__user_groups` (
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to #__users.id' ,
  `group_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to #__groups.id' ,
  PRIMARY KEY (`user_id`, `group_id`) )
DEFAULT CHARACTER SET = utf8;

#
# Table structure for table `#__groupings`
#
#     A collection of groups which have been defined for a specific action and asset id
#     These are created by Molajo ACL and used for efficiency with database queries
#     Replaces viewlevel table and provides this structure for view and other ACL actions
#

CREATE  TABLE IF NOT EXISTS `#__groupings` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Groupings Primary Key' ,
  `group_name_list` TEXT NOT NULL ,
  `group_id_list` TEXT NOT NULL ,
  PRIMARY KEY (`id`) )
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

#
# Table structure for table `#__group_to_groupings`
#
#     A listing of groups that belong to the group
#

CREATE TABLE IF NOT EXISTS `#__group_to_groupings` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Group to Group Primary Key' ,
  `group_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__group table.' ,
  `grouping_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__groupings table.' ,
  PRIMARY KEY (`id`) )
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

#
# Table structure for table `#__user_groupings`
#
#   Groupings of groups to which users belong
#

CREATE TABLE IF NOT EXISTS `#__user_groupings` (
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to #__users.id' ,
  `grouping_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to #__groupings.id' ,
  PRIMARY KEY (`user_id`, `grouping_id`) )
DEFAULT CHARACTER SET = utf8;

#
# Table structure for table `#__user_applications`
#
#   Applications to which users belong
#

CREATE TABLE IF NOT EXISTS `#__user_applications` (
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to #__users.id' ,
  `application_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to #__applications.id' ,
  PRIMARY KEY (`user_id`, `application_id`) )
DEFAULT CHARACTER SET = utf8;

#
# Table structure for table `#__permissions_groups`
#   A complete list of assigned actions by asset id for groups
#

CREATE TABLE IF NOT EXISTS `#__permissions_groups` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Permissions Primary Key' ,
  `group_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to #_groups.id' ,
  `asset_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to #__assets.id' ,
  `action_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to #__actions.id' ,
  PRIMARY KEY (`id`) )
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

#
# Table structure for table `#__permissions_groupings`
#
#   A complete list of assigned actions by asset id for groupings of groups
#

CREATE TABLE IF NOT EXISTS `#__permissions_groupings` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Permissions Primary Key' ,
  `grouping_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to #__groups.id' ,
  `asset_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to #__assets.id' ,
  `action_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to #__actions.id' ,
  PRIMARY KEY (`id`) )
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

#
# CONTENT
#

#
# Table structure for table `#__categories`
#

CREATE TABLE `#__categories` (
  `id` INT (11) NOT NULL auto_increment,
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `parent_id` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `lft` INT (11) NOT NULL DEFAULT 0,
  `rgt` INT (11) NOT NULL DEFAULT 0,
  `level` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `path` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'URL Alias',
  `extension` VARCHAR(50) NOT NULL DEFAULT ' ',
  `title` VARCHAR(255) NOT NULL,
  `subtitle` VARCHAR(255) NOT NULL DEFAULT ' ',
  `alias` VARCHAR(255) NOT NULL DEFAULT ' ',
  `note` VARCHAR(255) NOT NULL DEFAULT ' ',
  `description` MEDIUMTEXT NULL,
  `published` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` MEDIUMTEXT COMMENT 'Configurable Parameter Values',
  `metadesc` VARCHAR(1024) NOT NULL DEFAULT ' ' COMMENT 'The meta description for the page.',
  `metakey` VARCHAR(1024) NOT NULL DEFAULT ' ' COMMENT 'The meta keywords for the page.',
  `metadata` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'JSON encoded metadata properties.',
  `created_user_id` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `created_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `modified_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hits` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `language` CHAR(7) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cat_idx` (`extension`,`published`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_path` (`path` (333)),
  KEY `idx_left_right` (`lft`,`rgt`),
  KEY `idx_alias` (`alias`),
  INDEX `idx_language` (`language`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__articles`
#

CREATE TABLE IF NOT EXISTS `#__articles` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `catid` INT (11) UNSIGNED NOT NULL DEFAULT  0 COMMENT 'Category ID associated with the Primary Key',

  `title` VARCHAR (255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `subtitle` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Subtitle',
  `alias` VARCHAR (255) NOT NULL DEFAULT ' ' COMMENT 'URL Alias',

  `content_type` tinyint(3) NOT NULL DEFAULT 0 COMMENT 'Content Type: Links to #__configuration.option_id = 10 and component_option values matching ',

  `content_text` MEDIUMTEXT NULL COMMENT 'Content Primary Text Field, can include break to designate Introductory and Full text',
  `content_link` VARCHAR (2083) NULL COMMENT 'Content Link for Weblink or Newsfeed Field',
  `content_email_address` VARCHAR (255) NULL COMMENT 'Content Email Field',
  `content_numeric_value` TINYINT (3) NULL COMMENT 'Content Numeric Value, ex. vote on poll',
  `content_file` VARCHAR (255) NOT NULL DEFAULT ' ' COMMENT 'Content Network Path to File',

  `featured` boolean NOT NULL DEFAULT 0 COMMENT 'Featured 1-Featured 0-Not Featured',
  `stickied` boolean NOT NULL DEFAULT 0 COMMENT 'Stickied 1-Stickied 0-Not Stickied',
  `user_default` boolean NOT NULL DEFAULT 0 COMMENT 'User DEFAULT 1-DEFAULT 0-Not DEFAULT',
  `category_default` boolean NOT NULL DEFAULT 0 COMMENT 'Category DEFAULT 1-DEFAULT 0-Not DEFAULT',
  `language` CHAR (7) NOT NULL DEFAULT ' ' COMMENT 'Language',
  `ordering` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Ordering',

  `state` TINYINT (3) NOT NULL DEFAULT 0 COMMENT 'Published State 2: Archived 1: Published 0: Unpublished -1: Trashed -2: Spam -10 Version',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish Begin Date and Time',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish End Date and Time',
  `version` INT (11) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Version Number',
  `version_of_id` INT (11) NULL COMMENT 'Primary ID for this Version',
  `state_prior_to_version` INT (11) UNSIGNED NULL COMMENT 'State value prior to creating this version copy and changing the state to Version',

  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'  COMMENT 'Created Date and Time',
  `created_by` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Created by User ID',
  `created_by_alias` VARCHAR (255) NOT NULL DEFAULT ' ' COMMENT 'Created by Alias',
  `created_by_email` VARCHAR (255) NULL COMMENT 'Created By Email Address',
  `created_by_website` VARCHAR (255) NULL COMMENT 'Created By Website',
  `created_by_ip_address` CHAR(15) NULL COMMENT 'Created By IP Address',
  `created_by_referer` VARCHAR (255) NULL COMMENT 'Created By Referer',

  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modified Date',
  `modified_by` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Modified By User ID',

  `checked_out` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Checked out by User Id',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Checked out Date and Time',

  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',

  `component_option` VARCHAR(50) NOT NULL DEFAULT 'com_articles' COMMENT 'Component Option Value',
  `component_id` INT (11) UNSIGNED NOT NULL DEFAULT 2 COMMENT 'Primary Key for Component Content',
  `parent_id` INT (11) NULL COMMENT 'Nested set parent',
  `lft` INT (11) NULL COMMENT 'Nested set lft',
  `rgt` INT (11) NULL COMMENT 'Nested set rgt',
  `level` INT (11) NULL DEFAULT 0 COMMENT 'The cached level in the nested tree',
  `metakey` TEXT NULL COMMENT 'Meta Key',
  `metadesc` TEXT NULL COMMENT 'Meta Description',
  `metadata` TEXT NULL COMMENT 'Meta Data',
  `attribs` TEXT NULL COMMENT 'Attributes (Custom Fields)',
  `params` MEDIUMTEXT COMMENT 'Configurable Parameter Values',

  PRIMARY KEY  (`id`),

  KEY `idx_component_component_id_id` (`component_option`, `component_id`, `id`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`state`),
  KEY `idx_catid` (`catid`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_featured_catid` (`featured`,`catid`),
  KEY `idx_stickied_catid` (`stickied`,`catid`),
  KEY `idx_language` (`language`)

) DEFAULT CHARSET=utf8;

#
# Table structure for table `#__common`
#

CREATE TABLE IF NOT EXISTS `#__common` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `catid` INT (11) UNSIGNED NOT NULL DEFAULT  0 COMMENT 'Category ID associated with the Primary Key',

  `title` VARCHAR (255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `subtitle` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Subtitle',
  `alias` VARCHAR (255) NOT NULL DEFAULT ' ' COMMENT 'URL Alias',

  `content_type` tinyint(3) NOT NULL DEFAULT 0 COMMENT 'Content Type: Links to #__configuration.option_id = 10 and component_option values matching ',

  `content_text` MEDIUMTEXT NULL COMMENT 'Content Primary Text Field, can include break to designate Introductory and Full text',
  `content_link` VARCHAR (2083) NULL COMMENT 'Content Link for Weblink or Newsfeed Field',
  `content_email_address` VARCHAR (255) NULL COMMENT 'Content Email Field',
  `content_numeric_value` TINYINT (3) NULL COMMENT 'Content Numeric Value, ex. vote on poll',
  `content_file` VARCHAR (255) NOT NULL DEFAULT ' ' COMMENT 'Content Network Path to File',

  `featured` boolean NOT NULL DEFAULT 0 COMMENT 'Featured 1-Featured 0-Not Featured',
  `stickied` boolean NOT NULL DEFAULT 0 COMMENT 'Stickied 1-Stickied 0-Not Stickied',
  `user_default` boolean NOT NULL DEFAULT 0 COMMENT 'User DEFAULT 1-DEFAULT 0-Not DEFAULT',
  `category_default` boolean NOT NULL DEFAULT 0 COMMENT 'Category DEFAULT 1-DEFAULT 0-Not DEFAULT',
  `language` CHAR (7) NOT NULL DEFAULT ' ' COMMENT 'Language',
  `ordering` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Ordering',

  `state` TINYINT (3) NOT NULL DEFAULT 0 COMMENT 'Published State 2: Archived 1: Published 0: Unpublished -1: Trashed -2: Spam -10 Version',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish Begin Date and Time',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish End Date and Time',
  `version` INT (11) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Version Number',
  `version_of_id` INT (11) NULL COMMENT 'Primary ID for this Version',
  `state_prior_to_version` INT (11) UNSIGNED NULL COMMENT 'State value prior to creating this version copy and changing the state to Version',

  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'  COMMENT 'Created Date and Time',
  `created_by` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Created by User ID',
  `created_by_alias` VARCHAR (255) NOT NULL DEFAULT ' ' COMMENT 'Created by Alias',
  `created_by_email` VARCHAR (255) NULL COMMENT 'Created By Email Address',
  `created_by_website` VARCHAR (255) NULL COMMENT 'Created By Website',
  `created_by_ip_address` CHAR(15) NULL COMMENT 'Created By IP Address',
  `created_by_referer` VARCHAR (255) NULL COMMENT 'Created By Referer',

  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modified Date',
  `modified_by` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Modified By User ID',

  `checked_out` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Checked out by User Id',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Checked out Date and Time',

  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',

  `component_option` VARCHAR(50) NOT NULL DEFAULT ' ' COMMENT 'Component Option Value',
  `component_id` INT (11) UNSIGNED NOT NULL DEFAULT  0 COMMENT 'Primary Key for Component Content',
  `parent_id` INT (11) NULL COMMENT 'Nested set parent',
  `lft` INT (11) NULL COMMENT 'Nested set lft',
  `rgt` INT (11) NULL COMMENT 'Nested set rgt',
  `level` INT (11) NULL DEFAULT 0 COMMENT 'The cached level in the nested tree',
  `metakey` TEXT NULL COMMENT 'Meta Key',
  `metadesc` TEXT NULL COMMENT 'Meta Description',
  `metadata` TEXT NULL COMMENT 'Meta Data',
  `attribs` TEXT NULL COMMENT 'Attributes (Custom Fields)',
  `params` MEDIUMTEXT COMMENT 'Configurable Parameter Values',

  PRIMARY KEY  (`id`),

  KEY `idx_component_component_id_id` (`component_option`, `component_id`, `id`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`state`),
  KEY `idx_catid` (`catid`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_featured_catid` (`featured`,`catid`),
  KEY `idx_stickied_catid` (`stickied`,`catid`),
  KEY `idx_language` (`language`)

) DEFAULT CHARSET=utf8;

#
# EXTENSIONS
#

#
# Table structure for table `#__extensions`
#

CREATE TABLE `#__extensions` (
  `extension_id` INT (11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `type` VARCHAR(20) NOT NULL,
  `element` VARCHAR(100) NOT NULL,
  `folder` VARCHAR(100) NOT NULL,
  `application_id` INT (11) NOT NULL,
  `enabled` TINYINT(3) NOT NULL DEFAULT '1',
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `protected` TINYINT(3) NOT NULL DEFAULT 0,
  `manifest_cache` MEDIUMTEXT  NOT NULL,
  `params` MEDIUMTEXT COMMENT 'Configurable Parameter Values',
  `custom_data` MEDIUMTEXT COMMENT 'Available for Custom Data needed by the Extension',
  `system_data` MEDIUMTEXT COMMENT 'Configurable Parameter Values',
  `checked_out` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Ordering',
  `state` TINYINT (3) NOT NULL DEFAULT 0 COMMENT 'Published State 2: Archived 1: Published 0: Unpublished -1: Trashed -2: Spam -10 Version',
  PRIMARY KEY (`extension_id`),
  INDEX `element_application_id`(`element`, `application_id`),
  INDEX `element_folder_application_id`(`element`, `folder`, `application_id`),
  INDEX `extension`(`type`,`element`,`folder`,`application_id`)
) AUTO_INCREMENT=1 CHARACTER SET utf8;

#
# SCHEMAS
#

CREATE TABLE `#__schemas` (
  `extension_id` int(11) NOT NULL,
  `version_id` varchar(20) NOT NULL,
  PRIMARY KEY (`extension_id`, `version_id`)
)  DEFAULT CHARSET=utf8;
# -------------------------------------------------------

#
# MENUS
#

#
# Table structure for table `#__menus`
#

CREATE TABLE `#__menus` (
  `id` INT (11) UNSIGNED NOT NULL auto_increment COMMENT 'Primary Key',
  `application_id` INT (11) UNSIGNED NOT NULL DEFAULT 0  COMMENT 'Application ID Foreign Key',
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Menu Title',
  `description` MEDIUMTEXT COMMENT 'Menu Description',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'  COMMENT 'Created Date and Time',
  `created_by` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Created by User ID',
  `checked_out` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `version` INT (11) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Version Number',
  `version_of_id` INT (11) NULL COMMENT 'Primary ID for this Version',
  `state_prior_to_version` INT (11) UNSIGNED NULL COMMENT 'State value prior to creating this version copy and changing the state to Version',
  PRIMARY KEY  (`id`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__menu_items`
#

CREATE TABLE `#__menu_items` (
  `id` INT (11) NOT NULL AUTO_INCREMENT,
  `menu_id` INT (11) NOT NULL DEFAULT 0 COMMENT 'The type of menu this item belongs to. FK to #__menus.menu_id',
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'The display title of the menu item.',
  `alias` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'The SEF alias of the menu item.',
  `note` VARCHAR(255) NOT NULL DEFAULT ' ',
  `path` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'URL',
  `link` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'The actually link the menu item refers to.',
  `type` VARCHAR(16) NOT NULL DEFAULT ' ' COMMENT 'The type of link: Component, URL, Alias, Separator',
  `published` INT (11) NOT NULL DEFAULT 0 COMMENT 'The published state of the menu link.',
  `parent_id` INT (11) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'The parent menu item in the menu tree.',
  `level` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'The relative level in the tree.',
  `component_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to #__extensions.id',
  `ordering` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Ordering',
  `checked_out` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to #__users.id',
  `checked_out_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'The time the menu item was checked out.',
  `browserNav` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'The click behaviour of the link.',
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `img` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'The image of the menu item.',
  `template_style_id` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `params` MEDIUMTEXT COMMENT 'Configurable Parameter Values',
  `lft` INT (11) NOT NULL DEFAULT 0 COMMENT 'Nested set lft.',
  `rgt` INT (11) NOT NULL DEFAULT 0 COMMENT 'Nested set rgt.',
  `home` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Indicates if this menu item is the home page.',
  `language` CHAR(7) NOT NULL DEFAULT ' ',
  `application_id` INT (11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_application_id_parent_id_alias` (`application_id`, `menu_id`, `parent_id`, `alias`),
  KEY `idx_menu_id` (`menu_id`, `ordering`),
  KEY `idx_left_right` (`lft`, `rgt`),
  KEY `idx_alias` (`alias`),
  KEY `idx_path` (`application_id`, `path`(333)),
  KEY `idx_language` (`language`)
)   DEFAULT CHARSET=utf8;

#
# Table structure for table `#__modules_menu`
#

CREATE TABLE `#__modules_menu` (
  `module_id` INT (11) NOT NULL DEFAULT 0,
  `menu_item_id` INT (11) NOT NULL DEFAULT 0,
  PRIMARY KEY  (`module_id`, `menu_item_id`)
) DEFAULT CHARSET=utf8;

#
# Table structure for table `#__modules`
#

CREATE TABLE `#__modules` (
  `id` INT (11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL DEFAULT ' ',
  `subtitle` VARCHAR(255) NOT NULL DEFAULT ' ',
  `note` VARCHAR(255) NOT NULL DEFAULT ' ',
  `content` MEDIUMTEXT NOT NULL,
  `ordering` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Ordering',
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `position` VARCHAR(50) DEFAULT NULL,
  `checked_out` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `module` VARCHAR(255) DEFAULT NULL,
  `showtitle` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `params` MEDIUMTEXT COMMENT 'Configurable Parameter Values',
  `application_id` INT (11) NOT NULL DEFAULT 0,
  `language` CHAR(7) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_module` (`module`,`published`),
  KEY `idx_language` (`language`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__languages`
#

CREATE TABLE `#__languages` (
  `lang_id` INT (11) UNSIGNED NOT NULL auto_increment,
  `lang_code` CHAR(7) NOT NULL,
  `title` VARCHAR(50) NOT NULL,
  `title_native` VARCHAR(50) NOT NULL,
  `sef` VARCHAR(50) NOT NULL,
  `image` VARCHAR(50) NOT NULL,
  `description` VARCHAR(512) NOT NULL,
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `published` INT (11) NOT NULL DEFAULT 0,
  PRIMARY KEY  (`lang_id`),
  UNIQUE `idx_sef` (`sef`)
)  DEFAULT CHARSET=utf8;

#
# TEMPLATES
#

#
# Table structure for table `#__templates`
#

CREATE TABLE IF NOT EXISTS `#__templates` (
  `id` INT (11) UNSIGNED NOT NULL auto_increment COMMENT 'Primary Key',
  `application_id` INT (11) UNSIGNED NOT NULL DEFAULT 0  COMMENT 'Application ID Foreign Key',
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Template Title',
  `description` MEDIUMTEXT COMMENT 'Template Description',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'  COMMENT 'Created Date and Time',
  `created_by` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Created by User ID',
  `checked_out` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `version` INT (11) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Version Number',
  `version_of_id` INT (11) NULL COMMENT 'Primary ID for this Version',
  `state_prior_to_version` INT (11) UNSIGNED NULL COMMENT 'State value prior to creating this version copy and changing the state to Version',
  PRIMARY KEY  (`id`),
  KEY `idx_template` (`title`)
)  DEFAULT CHARSET=utf8 ;


#
# Table structure for table `#__template_styles`
#

CREATE TABLE IF NOT EXISTS `#__template_styles` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_id` INT (11) UNSIGNED NOT NULL COMMENT 'Foreign Key to Template Table',
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Template Title',
  `description` MEDIUMTEXT COMMENT 'Template Description',
  `default` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Indicates if this is the default style',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'  COMMENT 'Created Date and Time',
  `created_by` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Created by User ID',
  `checked_out` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `version` INT (11) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Version Number',
  `version_of_id` INT (11) NULL COMMENT 'Primary ID for this Version',
  `state_prior_to_version` INT (11) UNSIGNED NULL COMMENT 'State value prior to creating this version copy and changing the state to Version',
  `asset_id` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `params` MEDIUMTEXT COMMENT 'Configurable Parameter Values',
  PRIMARY KEY  (`id`),
  KEY `idx_template` (`template_id`, `id`),
  KEY `idx_default` (`default`)
)  DEFAULT CHARSET=utf8 ;

#
# SYSTEM CONFIGURATION
#

#
# Table structure for table `#__messages`
#

CREATE TABLE `#__messages` (
  `message_id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id_from` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `user_id_to` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `folder_id` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `date_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state` tinyint(1) NOT NULL DEFAULT 0,
  `priority` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `subject` VARCHAR(255) NOT NULL DEFAULT ' ',
  `message` MEDIUMTEXT COMMENT 'Messages',
  PRIMARY KEY (`message_id`),
  KEY `useridto_state` (`user_id_to`,`state`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__messages_cfg`
#

CREATE TABLE `#__messages_cfg` (
  `user_id` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `cfg_name` VARCHAR(100) NOT NULL DEFAULT ' ',
  `cfg_value` VARCHAR(255) NOT NULL DEFAULT ' ',
  UNIQUE KEY `idx_user_var_name` (`user_id`,`cfg_name`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__session`
#

CREATE TABLE `#__session` (
  `session_id` VARCHAR(32) NOT NULL DEFAULT ' ',
  `application_id` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `guest` tinyint(4) UNSIGNED DEFAULT '1',
  `time` VARCHAR(14) DEFAULT ' ',
  `data` LONGTEXT DEFAULT NULL,
  `userid` INT (11) DEFAULT 0,
  `username` VARCHAR(150) DEFAULT ' ',
  PRIMARY KEY  (`session_id`),
  KEY `whosonline` (`guest`),
  KEY `userid` (`userid`),
  KEY `time` (`time`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__configuration`
#

CREATE TABLE IF NOT EXISTS `#__configuration` (
  `component_option` VARCHAR(50) NOT NULL DEFAULT ' ',
  `option_id` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `option_value` VARCHAR(80) NOT NULL DEFAULT ' ',
  `option_value_literal` VARCHAR(255) NOT NULL DEFAULT ' ',
  `ordering` INT (11) NOT NULL DEFAULT 0,
  UNIQUE KEY `idx_component_option_id_value_key` (`component_option`,`option_id`,`option_value`)
) DEFAULT CHARSET=utf8;

#
# UPDATES
#

#
# Table structure for table `#__updates`
#

CREATE TABLE  `#__updates` (
  `id` INT (11) NOT NULL auto_increment,
  `update_site_id` INT (11) DEFAULT 0,
  `extension_id` INT (11) DEFAULT 0,
  `categoryid` INT (11) DEFAULT 0,
  `name` VARCHAR(100) DEFAULT ' ',
  `description` text NOT NULL,
  `element` VARCHAR(100) DEFAULT ' ',
  `type` VARCHAR(20) DEFAULT ' ',
  `folder` VARCHAR(20) DEFAULT ' ',
  `application_id` INT (11) DEFAULT 0,
  `version` VARCHAR(10) DEFAULT ' ',
  `data` text NOT NULL,
  `detailsurl` text NOT NULL,
  PRIMARY KEY  (`id`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__update_sites`
#

CREATE TABLE  `#__update_sites` (
  `update_site_id` INT (11) NOT NULL auto_increment,
  `name` VARCHAR(100) DEFAULT ' ',
  `type` VARCHAR(20) DEFAULT ' ',
  `location` text NOT NULL,
  `enabled` INT (11) DEFAULT 0,
  PRIMARY KEY  (`update_site_id`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__update_sites_extensions`
#

CREATE TABLE `#__update_sites_extensions` (
  `update_site_id` INT(11) DEFAULT 0,
  `extension_id` INT(11) DEFAULT 0,
  PRIMARY KEY(`update_site_id`, `extension_id`)
)  DEFAULT CHARSET=utf8;

#
# APPLICATIONS
#

INSERT INTO `#__applications` (`id`, `asset_id`, `application_id`, `name`, `path`)
  VALUES
    (1, 1, 0, 'site', ''),
    (2, 2, 1, 'administrator', 'administrator'),
    (3, 3, 2, 'installation', 'installation'),
    (4, 4, 3, 'content', 'content');

INSERT INTO `#__assets` ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
  VALUES
    (1, 1, '__applications', '', '', '', 1),
    (2, 2, '__applications', '', 'administrator', '', 1),
    (3, 3, '__applications', '', 'installation', '', 1),
    (4, 4, '__applications', '', 'content', '', 1);


#
# USERS AND GROUPS
#

INSERT INTO `#__groups`
  (`id`, `asset_id`, `parent_id`, `lft`, `rgt`, `title`, `protected`)
    VALUES
      (1, 11, 0, 0, 1, 'Public',        1),
      (2, 12, 0, 2, 3, 'Guest',         1),
      (3, 13, 0, 4, 5, 'Registered',    1),
      (4, 14, 0, 6, 7, 'Administrator', 1);

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
      (1, 11, '__groups', 'com_groups', '', 'index.php?option=com_groups', 4),
      (2, 12, '__groups', 'com_groups', '', 'index.php?option=com_groups', 4),
      (3, 13, '__groups', 'com_groups', '', 'index.php?option=com_groups', 4),
      (4, 14, '__groups', 'com_groups', '', 'index.php?option=com_groups', 4);

INSERT INTO `#__groupings`
  (`id`, `group_name_list`, `group_id_list` )
    VALUES
      (1, 'Public', '1'),
      (2, 'Guest', '2'),
      (3, 'Registered', '3'),
      (4, 'Administrator', '4'),
      (5, 'Registered, Administrator', '4,5');

INSERT INTO `#__group_to_groupings`
  ( `group_id` ,`grouping_id` )
  VALUES
    (1, 1),
    (2, 2),
    (3, 3),
    (4, 4),
    (3, 5),
    (4, 5);

#
# EXTENSIONS
#

# Components - Administrator

INSERT INTO `#__extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`)
  VALUES
    (1, 101, 'com_admin', 'component', 'com_admin', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (2, 102, 'com_articles', 'component', 'com_articles', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 2, 1),
    (3, 103, 'com_cache', 'component', 'com_cache', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 3, 1),
    (4, 104, 'com_categories', 'component', 'com_categories', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 4, 1),
    (5, 105, 'com_checkin', 'component', 'com_checkin', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 5, 1),
    (6, 106, 'com_config', 'component', 'com_config', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 6, 1),
    (7, 107, 'com_dashboard', 'component', 'com_dashboard', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 7, 1),
    (8, 108, 'com_installer', 'component', 'com_installer', '', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 8, 1),
    (9, 109, 'com_languages', 'component', 'com_languages', '', 1, 1, 1, '', '{"administrator":"en-GB","site":"en-GB"}', '', '', 0, '0000-00-00 00:00:00', 9, 1),
    (10, 110, 'com_layouts', 'component', 'com_layouts', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 10, 1),
    (11, 111, 'com_login', 'component', 'com_login', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 10, 1),
    (12, 112, 'com_media', 'component', 'com_media', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 11, 1),
    (13, 113, 'com_menus', 'component', 'com_menus', '', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 12, 1),
    (14, 114, 'com_messages', 'component', 'com_messages', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 13, 1),
    (15, 115, 'com_modules', 'component', 'com_modules', '', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 14, 1),
    (16, 116, 'com_plugins', 'component', 'com_plugins', '', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 15, 1),
    (17, 117, 'com_redirect', 'component', 'com_redirect', '', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 16, 1),
    (18, 118, 'com_search', 'component', 'com_search', '', 1, 1, 1, '', '{"enabled":"0","show_date":"1"}', '', '', 0, '0000-00-00 00:00:00', 17, 1),
    (19, 119, 'com_templates', 'component', 'com_templates', '', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 18, 1),
    (20, 120, 'com_users', 'component', 'com_users', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 19, 1);

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
    (1, 101, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2, 102, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (3, 103, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (4, 104, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (5, 105, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (6, 106, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (7, 107, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (8, 108, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (9, 109, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (10, 110, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (11, 111, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (12, 112, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (13, 113, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (14, 114, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (15, 115, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (16, 116, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (17, 117, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (18, 118, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (19, 119, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (20, 120, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4);

# Components - Site
INSERT INTO `#__extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`)
  VALUES
    (201, 201, 'com_articles', 'component', 'com_articles', '', 0, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 2, 1),
    (202, 202, 'com_search', 'component', 'com_search', '', 0, 1, 1, '', '{"enabled":"0","show_date":"1"}', '', '', 0, '0000-00-00 00:00:00', 17, 1),
    (203, 203, 'com_users', 'component', 'com_users', '', 0, 1, 1, '', '{"allowUserRegistration":"1","useractivation":"1","frontend_userparams":"1","mailSubjectPrefix":"","mailBodySuffix":""}', '', '', 0, '0000-00-00 00:00:00', 19, 1);

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
    (201, 201, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (202, 202, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (203, 203, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4);

# Layouts: Extensions

INSERT INTO `#__extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`)
  VALUES
    (300, 300, 'admin_acl_panel', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (301, 301, 'admin_activity', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (302, 302, 'admin_edit', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (303, 303, 'admin_favorites', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (304, 304, 'admin_feed', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (305, 305, 'admin_footer', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (306, 306, 'admin_header', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (307, 307, 'admin_inbox', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (308, 308, 'admin_launchpad', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (309, 309, 'admin_list', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (310, 310, 'admin_login', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (311, 311, 'admin_pagination', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (312, 312, 'admin_toolbar', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (313, 313, 'audio', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (314, 314, 'contact', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (315, 315, 'contact_form', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (316, 316, 'default', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (317, 317, 'faq', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (318, 318, 'item', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (319, 319, 'items', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (320, 320, 'list', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (321, 321, 'pagination', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (322, 322, 'syntaxhighlighter', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (323, 323, 'table', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (324, 324, 'table', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (325, 325, 'tree', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (326, 326, 'twig_example', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (327, 327, 'video', 'layout', 'layout', 'extensions', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1);

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
    (300, 300, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (301, 301, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (302, 302, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (303, 303, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (304, 304, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (305, 305, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (306, 306, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (307, 307, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (308, 308, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (309, 309, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (310, 310, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (311, 311, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (312, 312, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (313, 313, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (314, 314, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (315, 315, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (316, 316, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (317, 317, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (318, 318, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (319, 319, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (320, 320, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (321, 321, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (322, 322, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (323, 323, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (324, 324, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (325, 325, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (326, 326, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (327, 327, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4);

# Layouts: Forms

INSERT INTO `#__extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`)
  VALUES
    (400, 400, 'button', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (401, 401, 'colorpicker', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (402, 402, 'datepicker', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (403, 403, 'list', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (404, 404, 'media', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (405, 405, 'number', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (406, 406, 'option', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (407, 407, 'rules', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (408, 408, 'spacer', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (409, 409, 'text', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (410, 410, 'textarea', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (411, 411, 'user', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1);

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
    (400, 400, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (401, 401, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (402, 402, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (403, 403, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (404, 404, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (405, 405, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (406, 406, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (407, 407, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (408, 408, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (409, 409, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (410, 410, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (411, 411, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4);

# Layouts: Wraps

INSERT INTO `#__extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`)
  VALUES
    (500, 500, 'article', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (501, 501, 'aside', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (502, 502, 'div', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (503, 503, 'footer', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (504, 504, 'header', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (505, 505, 'horizontal', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (506, 506, 'nav', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (507, 507, 'none', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (508, 508, 'outline', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (509, 509, 'section', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (510, 510, 'table', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (511, 511, 'tabs', 'layout', 'layout', 'formfields', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1);

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
      (500, 500, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (501, 501, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (502, 502, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (503, 503, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (504, 504, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (505, 505, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (506, 506, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (507, 507, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (508, 508, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (509, 509, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (510, 510, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (511, 511, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4);

# Libraries

INSERT INTO `#__extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`)
  VALUES
    (601, 601, 'Akismet', 'library', 'akismet', 'akismet', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (602, 602, 'Doctrine', 'library', 'doctrine', 'doctrine', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (603, 603, 'Joomla Platform', 'library', 'joomla', 'jplatform', 1, 1, 1, '{"legacy":false,"name":"Molajo Web Application Framework","type":"library","creationDate":"2008","author":"Joomla","copyright":"Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.","authorEmail":"admin@joomla.org","authorUrl":"http:\\/\\/www.joomla.org","version":"1.6.0","description":"The Molajo Web Application Framework","group":""}', '{}', '', '', 0, '0000-00-00 00:00:00', 3, 1),
    (604, 604, 'Krumo', 'library', 'krumo', 'krumo', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 2, 1),
    (605, 605, 'Molajo Application', 'library', 'molajo', 'molajo', 1, 1, 1, '{"name":"Molajo Application","type":"library","creationDate":"2011","author":"Molajo Project Team","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved.","authorEmail":"collaborate@molajo.org","authorUrl":"http:\\/\\/molajo.org","version":"1.0.0","description":"Molajo is a web development environment useful for crafting custom solutions from simple to complex custom data architecture, presentation output, and access control.","group":""}\r\n', '', '', '', 0, '0000-00-00 00:00:00', 4, 1),
    (606, 606, 'Mollom', 'library', 'mollom', 'mollom', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 5, 1),
    (607, 607, 'Overrides', 'library', 'overrides', 'overrides', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 5, 1),
    (608, 608, 'phpexcel', 'library', 'phpexcel', 'phpexcel', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 6, 1),
    (609, 609, 'Recaptcha', 'library', 'recaptcha', 'recaptcha', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 8, 1),
    (610, 610, 'Secureimage', 'library', 'secureimage', 'secureimage', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 9, 1),
    (611, 611, 'Twig', 'library', 'twig', 'twig', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 11, 1),
    (612, 612, 'WideImage', 'library', 'wideimage', 'wideimage', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 12, 1);

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
      (601, 601, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (602, 602, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (603, 603, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (604, 604, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (605, 605, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (606, 606, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (607, 607, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (608, 608, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (609, 609, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (610, 610, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (611, 611, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (612, 612, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4);


# Modules - Administrator

INSERT INTO `#__extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`)
  VALUES
    (700, 700, 'mod_content', 'module', 'mod_content', 'mod_content', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 3, 1),
    (701, 701, 'mod_custom', 'module', 'mod_custom', 'mod_custom', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (702, 702, 'mod_favorites', 'module', 'mod_favorites', 'mod_favorites', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 2, 1),
    (703, 703, 'mod_feed', 'module', 'mod_feed', 'mod_feed', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 3, 1),
    (704, 704, 'mod_footer', 'module', 'mod_footer', 'mod_footer', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 4, 1),
    (705, 705, 'mod_header', 'module', 'mod_header', 'mod_header', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 6, 1),
    (706, 706, 'mod_launchpad', 'module', 'mod_launchpad', 'mod_launchpad', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 7, 1),
    (707, 707, 'mod_logout', 'module', 'mod_logout', 'mod_logout', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 8, 1),
    (708, 708, 'mod_members', 'module', 'mod_members', 'mod_members', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 9, 1),
    (709, 709, 'mod_messages', 'module', 'mod_messages', 'mod_messages', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 10, 1),
    (710, 710, 'mod_statistics', 'module', 'mod_statistics', 'mod_statistics', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 11, 1),
    (711, 711, 'mod_toolbar', 'module', 'mod_toolbar', 'mod_toolbar', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 12, 1);

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
      (700, 700, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (701, 701, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (702, 702, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (703, 703, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (704, 704, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (705, 705, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (706, 706, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (707, 707, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (708, 708, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (709, 709, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (710, 710, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (711, 711, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4);

# Modules - Site

INSERT INTO `#__extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`)
    VALUES
      (801, 801, 'mod_articles', 'module', 'mod_articles', 'mod_articles', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 1),
      (802, 802, 'mod_breadcrumbs', 'module', 'mod_breadcrumbs', 'mod_breadcrumbs', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 2, 1),
      (803, 803, 'mod_custom', 'module', 'mod_custom', 'mod_custom', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 3, 1),
      (804, 804, 'mod_feed', 'module', 'mod_feed', 'mod_feed', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 4, 1),
      (805, 805, 'mod_footer', 'module', 'mod_footer', 'mod_footer', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 5, 1),
      (806, 806, 'mod_languages', 'module', 'mod_languages', 'mod_languages', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 6, 1),
      (807, 807, 'mod_login', 'module', 'mod_login', 'mod_login', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 7, 1),
      (808, 808, 'mod_media', 'module', 'mod_media', 'mod_media', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 8, 1),
      (809, 809, 'mod_menu', 'module', 'mod_menu', 'mod_menu', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 9, 1),
      (810, 810, 'mod_related_items', 'module', 'mod_related_items', 'mod_related_items', 0, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 10, 1),
      (811, 811, 'mod_search', 'module', 'mod_search', 'mod_search', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 11, 1),
      (812, 812, 'mod_syndicate', 'module', 'mod_syndicate', 'mod_syndicate', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 12, 1),
      (813, 813, 'mod_users_latest', 'module', 'mod_users_latest', 'mod_users_latest', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 13, 1),
      (814, 814, 'mod_whosonline', 'module', 'mod_whosonline', 'mod_whosonline', 0, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 14, 1);

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
      (801, 801, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (802, 802, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (803, 803, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (804, 804, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (805, 805, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (806, 806, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (807, 807, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (808, 808, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (809, 809, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (810, 810, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (811, 811, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (812, 812, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (813, 813, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (814, 814, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4);
#
# Plugins
#

## ACL
INSERT INTO `#__extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`)
  VALUES
    (1000, 1000, 'plg_acl_example', 'plugin', 'example', 'acl', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1);

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
      (1000, 1000, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4);

## Authentication
INSERT INTO `#__extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`)
  VALUES
    (1100, 1100, 'plg_authentication_molajo', 'plugin', 'molajo', 'authentication', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1);

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
      (1100, 1100, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4);

## Content
INSERT INTO `#__extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`)
  VALUES
    (1200, 1200, 'plg_content_emailcloak', 'plugin', 'emailcloak', 'content', 1, 1, 1, '', '{"mode":"1"}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (1210, 1210, 'plg_content_loadmodule', 'plugin', 'loadmodule', 'content', 1, 1, 1, '', '{"wrap":"none"}', '', '', 0, '0000-00-00 00:00:00', 2, 1),
    (1220, 1220, 'plg_content_example', 'plugin', 'example', 'content', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 3, 1);

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
      (1200, 1200, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (1210, 1210, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (1220, 1220, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4);

## Editors
INSERT INTO `#__extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`)
  VALUES
    (1300, 1300, 'plg_editors_aloha', 'plugin', 'aloha', 'editors', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (1310, 1310, 'plg_editors_codemirror', 'plugin', 'codemirror', 'editors', 1, 1, 1, '', '{"linenumbers":"0","tabmode":"indent"}', '', '', 0, '0000-00-00 00:00:00', 2, 1),
    (1320, 1320, 'plg_editors_none', 'plugin', 'none', 'editors', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 3, 1);

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
      (1300, 1300, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (1310, 1310, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
      (1320, 1320, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4);

## Extended Editors
INSERT INTO `#__extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`)
  VALUES
    (1400, 1400, 'plg_editors-xtd_article', 'plugin', 'article', 'editors-xtd', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (1410, 1410, 'plg_editors-xtd_audio', 'plugin', 'audio', 'editors-xtd', 1, 1, 1, '', '{"linenumbers":"0","tabmode":"indent"}', '', '', 0, '0000-00-00 00:00:00', 2, 1),
    (1420, 1420, 'plg_editors-xtd_file', 'plugin', 'file', 'editors-xtd', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 3, 1),
    (1430, 1430, 'plg_editors-xtd_pagebreak', 'pagebreak', 'aloha', 'editors-xtd', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 4, 1),
    (1440, 1440, 'plg_editors-xtd_readmore', 'readmore', 'codemirror', 'editors-xtd', 1, 1, 1, '', '{"linenumbers":"0","tabmode":"indent"}', '', '', 0, '0000-00-00 00:00:00', 5, 1),
    (1450, 1450, 'plg_editors-xtd_video', 'image', 'none', 'editors-xtd', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 6, 1);

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
    (1400, 1400, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (1410, 1410, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (1420, 1420, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (1430, 1430, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (1440, 1440, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (1450, 1450, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4);

## Extension Plugins
INSERT INTO `#__extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`)
  VALUES
    (1500, 1500, 'plg_extension_molajo', 'plugin', 'molajo', 'extension', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1);

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
    (1500, 1500, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4);

## Language
INSERT INTO `#__extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`)
  VALUES
    (1600, 1600, 'English (United Kingdom)', 'language', 'en-GB', '', 0, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (1601, 1601, 'English (United Kingdom)', 'language', 'en-GB', '', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1);

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
      (1600, 1600, '__languages', 'com_languages', '', 'index.php?option=com_languages', 4),
      (1601, 1601, '__languages', 'com_languages', '', 'index.php?option=com_languages', 4);

## Molajo
INSERT INTO `#__extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`)
  VALUES
    (2005, 2005, 'plg_molajo_broadcast', 'plugin', 'broadcast', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_BROADCAST_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved.","authorEmail":"collaborate@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_BROADCAST_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (2010, 2010, 'plg_molajo_compress', 'plugin', 'compress', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_COMPRESS_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"collaborate@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_COMPRESS_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 2, 1),
    (2015, 2015, 'plg_molajo_categorization', 'plugin', 'categorization', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_CATEGORIZATION_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"collaborate@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_CATEGORIZATION_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 3, 1),
    (2020, 2020, 'plg_molajo_content', 'plugin', 'content', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_CONTENT_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"collaborate@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_CONTENT_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 4, 1),
    (2025, 2025, 'plg_molajo_extend', 'plugin', 'extend', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_SYSTEM_EXTEND_NAME","type":"plugin","creationDate":"May 2011","author":"Amy Stephen","copyright":"(C) 2011 Amy Stephen. All rights reserved.","authorEmail":"collaborate@molajo.org","authorUrl":"Molajo.org","version":"1.6.0","description":"PLG_SYSTEM_EXTEND_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 5, 1),
    (2030, 2030, 'plg_molajo_links', 'plugin', 'links', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_LINKS_NAME","type":"plugin","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"collaborate@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_LINKS_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 6, 1),
    (2035, 2035, 'plg_molajo_media', 'plugin', 'media', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_MEDIA_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"collaborate@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_MEDIA_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 7, 1),
    (2040, 2040, 'plg_molajo_protect', 'plugin', 'protect', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_PROTECT_NAME","type":"plugin","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"collaborate@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_PROTECT_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 8, 1),
    (2045, 2045, 'plg_molajo_responses', 'plugin', 'responses', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_RESPONSES_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"collaborate@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_RESPONSES_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 9, 1),
    (2050, 2050, 'plg_molajo_search', 'plugin', 'search', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_SEARCH_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"collaborate@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_SEARCH_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 10, 1),
    (2055, 2055, 'plg_molajo_system', 'plugin', 'system', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_SYSTEM_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"collaborate@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_SYSTEM_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 11, 1),
    (2060, 2060, 'plg_molajo_urls', 'plugin', 'urls', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_URLS_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"collaborate@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_URLS_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 12, 1),
    (2065, 2065, 'plg_molajo_webservices', 'plugin', 'webservices', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_WEBSERVICES_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"collaborate@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_WEBSERVICES_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 13, 1);

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
    (2005, 2005, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2010, 2010, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2015, 2015, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2020, 2020, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2025, 2025, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2030, 2030, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2035, 2035, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2040, 2040, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2045, 2045, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2050, 2050, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2055, 2055, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2060, 2060, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2065, 2065, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4);

## Search
INSERT INTO `#__extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`)
  VALUES
    (2100, 2100, 'plg_search_categories', 'plugin', 'categories', 'search', 1, 1, 0, '', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (2105, 2105, 'plg_search_articles', 'plugin', 'articles', 'search', 1, 1, 0, '', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 2, 1),
    (2110, 2110, 'plg_search_media', 'plugin', 'media', 'search', 1, 1, 0, '', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 3, 1);

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
    (2100, 2100, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2105, 2105, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2110, 2110, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4);

## System
INSERT INTO `#__extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`)
  VALUES
    (2200, 2200, 'plg_system_cache', 'plugin', 'cache', 'system', 1, 1, 0, '', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (2205, 2205, 'plg_system_debug', 'plugin', 'debug', 'system', 1, 1, 0, '', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 2, 1),
    (2210, 2210, 'plg_system_languagefilter', 'plugin', 'languagefilter', 'system', 1, 1, 0, '', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 3, 1),
    (2215, 2215, 'plg_system_log', 'plugin', 'log', 'system', 1, 1, 0, '', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 4, 1),
    (2220, 2220, 'plg_system_logout', 'plugin', 'logout', 'system', 1, 1, 0, '', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 5, 1),
    (2225, 2225, 'plg_system_molajo', 'plugin', 'molajo', 'system', 1, 1, 0, '', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 6, 1),
    (2230, 2230, 'plg_system_p3p', 'plugin', 'p3p', 'system', 1, 1, 0, '', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 7, 1),
    (2235, 2235, 'plg_system_redirect', 'plugin', 'redirect', 'system', 1, 1, 0, '', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 8, 1),
    (2240, 2240, 'plg_system_remember', 'plugin', 'remember', 'system', 1, 1, 0, '', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 9, 1),
    (2245, 2245, 'plg_system_sef', 'plugin', 'sef', 'system', 1, 1, 0, '', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 10, 1);

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
    (2200, 2200, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2205, 2205, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2210, 2210, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2215, 2215, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2220, 2220, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2225, 2225, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2230, 2230, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2235, 2235, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2240, 2240, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2245, 2245, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4);

## Query
INSERT INTO `#__extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`)
  VALUES
    (2300, 2300, 'plg_query_example', 'plugin', 'example', 'query', 1, 1, 0, '', '{"enable_example_feature":"1"}', '', '', 0, '0000-00-00 00:00:00', 1, 1);

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
      (2300, 2300, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4);

## Template
INSERT INTO `#__extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`)
  VALUES
    (2400, 2400, 'construct', 'template', 'construct', '', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (2450, 2450, 'mojito', 'template', 'mojito', '', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 2, 1);

## Users
INSERT INTO `#__extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`)
  VALUES
    (2500, 2500, 'plg_user_molajo', 'plugin', 'molajo', 'user', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (2550, 2550, 'plg_user_profile', 'plugin', 'profile', 'user', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 2, 1);

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
    (2500, 2500, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4),
    (2550, 2550, '__extensions', 'com_extensions', '', 'index.php?option=com_extensions', 4);

#
# LANGUAGES
#
INSERT INTO `#__languages` (`lang_id`,`lang_code`,`title`,`title_native`,`sef`,`image`,`description`,`metakey`,`metadesc`,`published`)
  VALUES
    (1, 'en-GB', 'English (UK)', 'English (UK)', 'en', 'en', '', '', '', 1);

#
# Menu - Administrator
#

INSERT INTO `#__menus`
  (`id`, `application_id`, `title`, `description`, `created`, `created_by`,
    `checked_out`,`checked_out_time`,`version`,`version_of_id`,`state_prior_to_version`)
    VALUES
      (1, 1, 'Launchpad Main Menu', 'Main Menu for the Molajo Administrator linking to Home, Configure, Access, Create, and Build', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1, NULL, NULL),
      (2, 1, 'Launchpad Configure', 'Configure Menu for the Molajo Administrator that enables access to the Global and Personal Configuration Options and system functions such as Global Check-in, Cache, Redirects and System Information.', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1, NULL, NULL),
      (3, 1, 'Launchpad Access', 'Access Menu for the Molajo Administrator that enables access to the User, Mass Mail, Group, and ACL Configuration Options.', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1, NULL, NULL),
      (4, 1, 'Launchpad Create', 'Main Menu for the Molajo Administrator enabling access to Content Components, like Articles, Comments, and Tags', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1, NULL, NULL),
      (5, 1, 'Launchpad Build', 'Main Menu for the Molajo Administrator that allows site builders to access Create, Installer, and the various Managers for Plugins, Modules, Templates, and Layouts', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1, NULL, NULL);

INSERT INTO `#__menu_items`
  (`id`, `menu_id`, `ordering`,  `application_id`, `asset_id`,
    `title`, `alias`, `note`, `path`, `link`, `type`,
    `published`, `parent_id`, `level`, `component_id`, `checked_out`, `checked_out_time`,
    `browserNav`, `img`, `template_style_id`, `params`,
    `lft`, `rgt`, `home`, `language`)
    VALUES
      (1, 1, 1, 0, 3000, 'Home', 'home', '', '', 'index.php?option=com_dashboard', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 1, '*'),

      (2, 1, 1, 0, 3010, 'Configure', 'configure', '', 'configure', 'index.php?option=com_dashboard&type=configure', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (3, 1, 2, 0, 3020, 'Access', 'access', '', 'access', 'index.php?option=com_dashboard&type=access', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (4, 1, 3, 0, 3030, 'Create', 'create', '', 'create', 'index.php?option=com_dashboard&type=create', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (5, 1, 4, 0, 3040, 'Build', 'build', '', 'build', 'index.php?option=com_dashboard&type=build', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (6, 1, 5, 0, 3050, 'Search', 'search', '', 'search', 'index.php?option=com_dashboard&type=search', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),

      (7, 2, 1, 0, 3100, 'Profile', 'profile', '', 'configure/profile', 'option=com_profile', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (8, 2, 2, 0, 3110, 'System', 'system', '', 'configure/system', 'index.php?option=com_config', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (9, 2, 3, 0, 3120, 'Checkin', 'checkin', '', 'configure/checkin', 'index.php?option=com_checkin', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (10, 2, 4, 0, 3130, 'Cache', 'cache', '', 'configure/cache', 'index.php?option=com_cache', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (11, 2, 5, 0, 3140, 'Backup', 'backup', '', 'configure/backup', 'index.php?option=com_backup', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (12, 2, 6, 0, 3150, 'Redirects', 'redirects', '', 'configure/redirects', 'index.php?option=com_redirects', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),

      (13, 3, 1, 0, 3210, 'Users', 'users', '', 'access/users', 'index.php?option=com_users', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (14, 3, 2, 0, 3220, 'Groups', 'groups', '', 'access/groups', 'index.php?option=com_groups', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (15, 3, 3, 0, 3230, 'Permissions', 'permissions', '', 'access/permissions', 'index.php?option=com_permissions', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (16, 3, 4, 0, 3240, 'Messages', 'messages', '', 'access/messages', 'index.php?option=com_messages', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (17, 3, 5, 0, 3250, 'Activity', 'activity', '', 'access/activity', 'index.php?option=com_activity', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),

      (18, 4, 1, 0, 3310, 'Articles', 'articles', '', 'create/articles', 'index.php?option=com_articles', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (19, 4, 2, 0, 3320, 'Tags', 'tags', '', 'create/tags', 'index.php?option=com_tags', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (20, 4, 3, 0, 3330, 'Comments', 'comments', '', 'create/comments', 'index.php?option=com_comments', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (21, 4, 4, 0, 3340, 'Media', 'media', '', 'create/media', 'index.php?option=com_media', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (22, 4, 5, 0, 3350, 'Categories', 'categories', '', 'create/categories', 'index.php?option=com_categories', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),

      (23, 5, 1, 0, 3400, 'Extensions', 'extensions', '', 'build/extensions', 'index.php?option=com_extensions', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (24, 5, 2, 0, 3410, 'Languages', 'languages', '', 'build/languages', 'index.php?option=com_languages', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (25, 5, 3, 0, 3420, 'Layouts', 'layouts', '', 'build/layouts', 'index.php?option=com_layouts', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (26, 5, 4, 0, 3430, 'Modules', 'modules', '', 'build/modules', 'index.php?option=com_modules', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (27, 5, 5, 0, 3440, 'Plugins', 'plugins', '', 'build/plugins', 'index.php?option=com_plugins', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (28, 5, 6, 0, 3450, 'Templates', 'templates', '', 'build/templates', 'index.php?option=com_templates', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*');

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
      (1, 3000, '__menu_items', 'com_menus', '', 'index.php?option=com_extensions', 4),

      (2, 3010, '__menu_items', 'com_menus', 'configure', 'index.php?option=com_dashboard&type=configure', 4),
      (3, 3020, '__menu_items', 'com_menus', 'access', 'index.php?option=com_dashboard&type=access', 4),
      (4, 3030, '__menu_items', 'com_menus', 'create', 'index.php?option=com_dashboard&type=create', 4),
      (5, 3040, '__menu_items', 'com_menus', 'build', 'index.php?option=com_dashboard&type=build', 4),
      (6, 3050, '__menu_items', 'com_menus', 'search', 'index.php?option=com_dashboard&type=search', 4),

      (7, 3100, '__menu_items', 'com_menus', 'configure/profile', 'option=com_profile', 4),
      (8, 3110, '__menu_items', 'com_menus', 'configure/system', 'index.php?option=com_config', 4),
      (9, 3120, '__menu_items', 'com_menus', 'configure/checkin', 'index.php?option=com_checkin', 4),
      (10, 3130, '__menu_items', 'com_menus', 'configure/cache', 'index.php?option=com_cache', 4),
      (11, 3140, '__menu_items', 'com_menus', 'configure/backup', 'index.php?option=com_backup', 4),
      (12, 3150, '__menu_items', 'com_menus', 'configure/redirects', 'index.php?option=com_redirects', 4),

      (13, 3210, '__menu_items', 'com_menus', 'access/users', 'index.php?option=com_users', 4),
      (14, 3220, '__menu_items', 'com_menus', 'access/groups', 'index.php?option=com_groups', 4),
      (15, 3230, '__menu_items', 'com_menus', 'access/permissions', 'index.php?option=com_permissions', 4),
      (16, 3240, '__menu_items', 'com_menus', 'access/messages', 'index.php?option=com_messages', 4),
      (17, 3250, '__menu_items', 'com_menus', 'access/activity', 'index.php?option=com_activity', 4),

      (18, 3310, '__menu_items', 'com_menus', 'create/articles', 'index.php?option=com_articles', 4),
      (19, 3320, '__menu_items', 'com_menus', 'create/tags', 'index.php?option=com_tags', 4),
      (20, 3330, '__menu_items', 'com_menus', 'create/comments', 'index.php?option=com_comments', 4),
      (21, 3340, '__menu_items', 'com_menus', 'create/media', 'index.php?option=com_media', 4),
      (22, 3350, '__menu_items', 'com_menus', 'create/categories', 'index.php?option=com_categories', 4),

      (23, 3400, '__menu_items', 'com_menus', 'build/extensions', 'index.php?option=com_extensions', 4),
      (24, 3410, '__menu_items', 'com_menus', 'build/languages', 'index.php?option=com_languages', 4),
      (25, 3420, '__menu_items', 'com_menus', 'build/layouts', 'index.php?option=com_layouts', 4),
      (26, 3430, '__menu_items', 'com_menus', 'build/modules', 'index.php?option=com_modules', 4),
      (27, 3440, '__menu_items', 'com_menus', 'build/plugins', 'index.php?option=com_plugins', 4),
      (28, 3450, '__menu_items', 'com_menus', 'build/templates', 'index.php?option=com_templates', 4);

#
# Menu - Site
#

INSERT INTO `#__menus`
  (`id`, `application_id`, `title`, `description`, `created`, `created_by`,
    `checked_out`,`checked_out_time`,`version`,`version_of_id`,`state_prior_to_version`)
    VALUES
      (100, 1, 'Main Menu', 'Default Main Menu for the Site Application', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1, NULL, NULL);

#
# Menu Items
#

INSERT INTO `#__menu_items`
  (`id`, `menu_id`, `ordering`,  `application_id`, `asset_id`,
    `title`, `alias`, `note`, `path`, `link`, `type`,
    `published`, `parent_id`, `level`, `component_id`, `checked_out`, `checked_out_time`,
    `browserNav`, `img`, `template_style_id`, `params`,
    `lft`, `rgt`, `home`, `language`)
    VALUES
      (100, 100, 1, 1, 3500, 'Home', 'home', '', '', 'index.php?option=com_articles', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 1, '*'),
      (101, 100, 2, 1, 3510, 'New Article', 'new-article', '', 'new-article', 'index.php?option=com_articles&view=article&layout=edit', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (102, 100, 3, 1, 3520, 'Article', 'article', '', 'article', 'index.php?option=com_articles&view=articles&layout=item&id=5', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (103, 100, 4, 1, 3530, 'Blog', 'blog', '', 'blog', 'index.php?option=com_articles&view=articles&layout=items&catid=2', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (104, 100, 5, 1, 3540, 'List', 'list', '', 'list', 'index.php?option=com_articles&view=articles&layout=table&catid=2', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (105, 100, 6, 1, 3550, 'Table', 'table', '', 'table', 'index.php?option=com_articles&type=search', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (106, 100, 7, 1, 3560, 'Login', 'login', '', 'login', 'index.php?option=com_users&view=login', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (107, 100, 8, 1, 3570, 'Search', 'search', '', 'search', 'index.php?option=com_search&type=search', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*');

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
      (100, 3500, '__articles', 'com_articles', '', 'index.php?option=com_articles', 4),
      (101, 3510, '__articles', 'com_articles', 'new-article', 'index.php?option=com_articles&view=article&layout=edit', 4),
      (102, 3520, '__articles', 'com_articles', 'article', 'index.php?option=com_articles&view=articles&layout=item&id=5', 4),
      (103, 3530, '__articles', 'com_articles', 'blog', 'index.php?option=com_articles&view=articles&layout=items&catid=2', 4),
      (104, 3540, '__articles', 'com_articles', 'list', 'index.php?option=com_articles&view=articles&layout=table&catid=2', 4),
      (105, 3550, '__articles', 'com_articles', 'table', 'index.php?option=com_articles&type=search', 4),
      (106, 3560, '__dummy', 'com_users', 'login', 'index.php?option=com_users&view=login', 4),
      (107, 3570, '__dummy', 'com_search', 'search', 'index.php?option=com_search&type=search', 4);
#
# MODULES
#

INSERT INTO `#__modules`
  ( `id`, `asset_id`, `title`, `subtitle`, `position`, `application_id`, `ordering`, `published`, `module`,
    `note`, `content`, `checked_out`, `checked_out_time`,
    `publish_up`, `publish_down`, `showtitle`, `params`, `language`)
  VALUES
    (1, 4010, 'Articles', '', 'content', 1, 1, 1, 'mod_content', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (2, 4020, 'Custom', '', 'content', 1, 2, 1, 'mod_custom', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (3, 4030, 'Favorites', '', 'content', 1, 3, 1, 'mod_favorites', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (4, 4040, 'Feed', '', 'home', 1, 1, 1, 'mod_feed', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (5, 4050, 'Footer', '', 'footer', 1, 1, 1, 'mod_footer', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (6, 4060, 'Header', '', 'header', 1, 1, 1, 'mod_header', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (7, 4070, 'Launchpad', '', 'launchpad', 1, 1, 1, 'mod_launchpad', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (8, 4080, 'Logout', '', 'logout', 1, 1, 1, 'mod_logout', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (9, 4090, 'Members', '', 'access', 1, 1, 1, 'mod_members', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (10, 4100, 'Messages', '', 'messages', 1, 1, 1, 'mod_messages', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (11, 4110, 'Statistics', '', 'home', 1, 2, 1, 'mod_statistics', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (12, 4120, 'Toolbar', '', 'toolbar', 1, 1, 1, 'mod_toolbar', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*');

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
      (1, 4010, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (2, 4020, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (3, 4030, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (4, 4040, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (5, 4050, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (6, 4060, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (7, 4070, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (8, 4080, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (9, 4090, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (10, 4100, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (11, 4110, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (12, 4120, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4');

INSERT INTO `#__modules`
  ( `id`, `asset_id`, `title`, `subtitle`, `position`, `application_id`, `ordering`, `published`, `module`,
    `note`, `content`, `checked_out`, `checked_out_time`,
    `publish_up`, `publish_down`, `showtitle`, `params`, `language`)
  VALUES
    (13, 4510, 'Articles', '', 'aside', 0, 1, 1, 'mod_content', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (14, 4520, 'Breadcrumbs', '', 'breadcrumbs', 0, 1, 1, 'mod_breadcrumbs', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (15, 4530, 'Custom', '', 'aside', 0, 2, 1, 'mod_custom', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (16, 4540, 'Feed', '', 'aside', 0, 3, 1, 'mod_feed', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (17, 4550, 'Footer', '', 'footer', 0, 1, 1, 'mod_footer', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (18, 4560, 'Languages', '', 'language', 0, 1, 1, 'mod_languages', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (19, 4570, 'Login', '', 'user', 0, 1, 1, 'mod_login', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (20, 4580, 'Logout', '', 'user', 0, 2, 1, 'mod_logout', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (21, 4590, 'Main Menu', '', 'mainmenu', 0, 1, 1, 'mod_menu', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (22, 4600, 'Page Navigation', '', 'navigation', 0, 1, 1, 'mod_pagination', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (23, 4610, 'Related Items', '', 'aside', 0, 4, 1, 'mod_related', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (24, 4620, 'Search', '', 'search', 0, 1, 1, 'mod_search', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (25, 4630, 'Syndicate', '', 'syndicate', 0, 1, 1, 'mod_syndicate', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (26, 4640, 'Users', '', 'online', 0, 1, 1, 'mod_users', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*'),
    (27, 4650, 'Online', '', 'online', 0, 2, 1, 'mod_online', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', '*');

INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
      (13, 4510, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (14, 4520, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (15, 4530, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (16, 4540, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (17, 4550, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (18, 4560, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (19, 4570, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (20, 4580, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (21, 4590, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (22, 4600, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (23, 4610, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (24, 4620, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (25, 4630, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (26, 4640, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4'),
      (27, 4650, '__modules', 'com_modules', '', 'index.php?option=com_modules', '4');

INSERT INTO `#__modules_menu`
 ( `module_id`, `menu_item_id`)
  VALUES
  (1, 0),
  (2, 0),
  (3, 0),
  (4, 0),
  (5, 0),
  (6, 0),
  (7, 0),
  (8, 0),
  (9, 0),
  (10, 0),
  (11, 0),
  (12, 0),
  (13, 0),
  (14, 0),
  (15, 0),
  (16, 0),
  (17, 0),
  (18, 0),
  (19, 0),
  (20, 0),
  (21, 0),
  (22, 0),
  (23, 0),
  (24, 0),
  (25, 0),
  (26, 0),
  (27, 0);

#
# TEMPLATES
#

INSERT INTO `#__templates`
  (`id`, `application_id`, `title`, `description`,
    `created`, `created_by`, `checked_out`, `checked_out_time`,
    `publish_up`, `publish_down`, `published`,
    `version`, `version_of_id`, `state_prior_to_version` )
  VALUES
    (1, 0, 'Construct', 'Construct is a code-based Template Development Framework. It is designed to be flexible and easily used for creating one-of-a-kind templates.', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, NULL),
    (2, 1, 'Mojito', 'Mojito is (cristina?).', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, NULL);

INSERT INTO `#__template_styles`
  (`id`, `template_id`, `asset_id`, `title`, `description`, `default`,
    `created`, `created_by`, `checked_out`, `checked_out_time`,
    `publish_up`, `publish_down`, `published`,
    `params`, `version`, `version_of_id`, `state_prior_to_version`)
  VALUES
    (1, 1, 7000, 'Blank Slate', 'Blank Slate is (Cristina?)', 1, '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', NULL, NULL, NULL),
    (2, 2, 7010, 'Mojito - Style 1', 'Mojito Style 1 is (Cristina?)', 1, '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', NULL, NULL, NULL);


INSERT INTO `#__assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
      (1, 7000, '__template_styles', 'com_templates', '', 'index.php?option=com_templates', '4'),
      (2, 7010, '__template_styles', 'com_templates', '', 'index.php?option=com_templates', '4');

#
# UPDATES
#
INSERT INTO `#__update_sites`
  VALUES
    (1, 'Molajo Core', 'collection', 'http://update.molajo.org/core/list.xml', 1),
    (2, 'Molajo Directory', 'collection', 'http://update.molajo.org/directory/list.xml', 1);

INSERT INTO `#__update_sites_extensions` VALUES (1, 700), (2, 700);

#
# Actions
#
INSERT INTO `#__actions` (`id` ,`title`)
  VALUES
    (1, 'login'),
    (2, 'create'),
    (3, 'view'),
    (4, 'edit'),
    (5, 'publish'),
    (6, 'delete'),
    (7, 'admin');

#
# Build Indexes
#

# Actions
CREATE UNIQUE INDEX `idx_actions_table_id_join` ON `#__actions` (`id` ASC) ;
CREATE UNIQUE INDEX `idx_actions_table_title` ON `#__actions` (`title` ASC) ;

# Assets
CREATE UNIQUE INDEX `idx_content_table_id_join` ON `#__assets` (`content_table` ASC, `id` ASC) ;
CREATE UNIQUE INDEX `idx_content_table_content_id_join` ON `#__assets` (`content_table` ASC, `content_id` ASC) ;

# Applications
CREATE UNIQUE INDEX `idx_asset_table_id_join` ON `#__applications` (`asset_id` ASC) ;

# Users
CREATE UNIQUE INDEX `idx_asset_table_id_join` ON `#__users` (`asset_id` ASC) ;

# Groups
CREATE UNIQUE INDEX `idx_usergroup_parent_title_lookup` ON `#__groups` (`parent_id` ASC, `title` ASC, `type_id` ASC) ;
CREATE UNIQUE INDEX `idx_asset_table_id_join` ON `#__groups` (`asset_id` ASC) ;
CREATE INDEX `idx_usergroup_title_lookup` ON `#__groups` (`title` ASC) ;
CREATE INDEX `idx_usergroup_adjacency_lookup` ON `#__groups` (`parent_id` ASC) ;
CREATE INDEX `idx_usergroup_type_id` ON `#__groups` (`type_id` ASC) ;
CREATE INDEX `idx_usergroup_nested_set_lookup` USING BTREE ON `#__groups` (`lft` ASC, `rgt` ASC) ;

# User Groups
CREATE INDEX `fk_molajo_user_groups_molajo_users1` ON `#__user_groups` (`user_id` ASC) ;
CREATE INDEX `fk_molajo_user_groups_molajo_groups1` ON `#__user_groups` (`group_id` ASC) ;

# Group to Groupings
CREATE UNIQUE INDEX `idx_group_to_groupings_id` ON `#__group_to_groupings` (`group_id` ASC, `grouping_id` ASC) ;
CREATE INDEX `fk_molajo_group_to_groupings_molajo_groups1` ON `#__group_to_groupings` (`group_id` ASC) ;
CREATE INDEX `fk_molajo_group_to_groupings_molajo_groupings1` ON `#__group_to_groupings` (`grouping_id` ASC) ;

# User Groupings
CREATE INDEX `fk_molajo_user_groupings_molajo_users1` ON `#__user_groupings` (`user_id` ASC) ;
CREATE INDEX `fk_molajo_user_groupings_molajo_groupings1` ON `#__user_groupings` (`grouping_id` ASC) ;

# User Applications
CREATE INDEX `user_id` ON `#__user_applications` (`user_id` ASC) ;
CREATE INDEX `fk_molajo_user_applications_molajo_users1` ON `#__user_applications` (`application_id` ASC) ;

# Permissions Groups
CREATE UNIQUE INDEX `idx_asset_action_to_group_lookup` ON `#__permissions_groups` (`asset_id` ASC, `action_id` ASC, `group_id` ASC) ;
CREATE UNIQUE INDEX `idx_group_to_asset_action_lookup` ON `#__permissions_groups` (`group_id` ASC, `asset_id` ASC, `action_id` ASC) ;
CREATE INDEX `fk_molajo_permissions_groups_molajo_groups1` ON `#__permissions_groups` (`group_id` ASC) ;
CREATE INDEX `fk_molajo_permissions_groups_molajo_assets1` ON `#__permissions_groups` (`asset_id` ASC) ;
CREATE INDEX `fk_molajo_permissions_groups_molajo_actions1` ON `#__permissions_groups` (`action_id` ASC) ;

# Permissions Groupings
CREATE UNIQUE INDEX `idx_asset_action_to_group_lookup` ON `#__permissions_groupings` (`asset_id` ASC, `action_id` ASC, `grouping_id` ASC) ;
CREATE UNIQUE INDEX `idx_group_to_asset_action_lookup` ON `#__permissions_groupings` (`grouping_id` ASC, `asset_id` ASC, `action_id` ASC) ;
CREATE INDEX `fk_molajo_permissions_groupings_molajo_groupings1` ON `#__permissions_groupings` (`grouping_id` ASC) ;
CREATE INDEX `fk_molajo_permissions_groupings_molajo_assets1` ON `#__permissions_groupings` (`asset_id` ASC) ;
CREATE INDEX `fk_molajo_permissions_groupings_molajo_actions1` ON `#__permissions_groupings` (`action_id` ASC) ;

# Categories
CREATE UNIQUE INDEX `idx_asset_table_id_join` ON `#__categories` (`asset_id` ASC) ;

# Articles
CREATE UNIQUE INDEX `idx_asset_table_id_join` ON `#__articles` (`asset_id` ASC) ;

# Common
CREATE UNIQUE INDEX `idx_asset_table_id_join` ON `#__common` (`asset_id` ASC) ;

# Extensions
CREATE UNIQUE INDEX `idx_asset_table_id_join` ON `#__extensions` (`asset_id` ASC) ;

# Menu Items
CREATE UNIQUE INDEX `idx_asset_table_id_join` ON `#__menu_items` (`asset_id` ASC) ;

#
# Configuration
#

/* TABLE */

/* 100 MOLAJO_CONFIG_OPTION_ID_TABLE */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 100, '', '', 0),
('core', 100, '__common', '__common', 1);

/* 200 MOLAJO_CONFIG_OPTION_ID_FIELDS */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 200, '', '', 0),
('core', 200, 'access', 'MOLAJO_FIELD_ACCESS_LABEL', 1),
('core', 200, 'alias', 'MOLAJO_FIELD_ALIAS_LABEL', 2),
('core', 200, 'asset_id', 'MOLAJO_FIELD_ASSET_ID_LABEL', 3),
('core', 200, 'attribs', 'MOLAJO_FIELD_ATTRIBS_LABEL', 4),
('core', 200, 'catid', 'MOLAJO_FIELD_CATID_LABEL', 5),
('core', 200, 'checked_out', 'MOLAJO_FIELD_CHECKED_OUT_LABEL', 6),
('core', 200, 'checked_out_time', 'MOLAJO_FIELD_CHECKED_OUT_TIME_LABEL', 7),
('core', 200, 'component_id', 'MOLAJO_FIELD_COMPONENT_ID_LABEL', 8),
('core', 200, 'content_table', 'MOLAJO_FIELD_CONTENT_TABLE_LABEL', 9),
('core', 200, 'content_email_address', 'MOLAJO_FIELD_CONTENT_EMAIL_ADDRESS_LABEL', 10),
('core', 200, 'content_file', 'MOLAJO_FIELD_CONTENT_FILE_LABEL', 11),
('core', 200, 'content_link', 'MOLAJO_FIELD_CONTENT_LINK_LABEL', 12),
('core', 200, 'content_numeric_value', 'MOLAJO_FIELD_CONTENT_NUMERIC_VALUE_LABEL', 13),
('core', 200, 'content_text', 'MOLAJO_FIELD_CONTENT_TEXT_LABEL', 14),
('core', 200, 'content_type', 'MOLAJO_FIELD_CONTENT_TYPE_LABEL', 15),
('core', 200, 'created', 'MOLAJO_FIELD_CREATED_LABEL', 16),
('core', 200, 'created_by', 'MOLAJO_FIELD_CREATED_BY_LABEL', 17),
('core', 200, 'created_by_alias', 'MOLAJO_FIELD_CREATED_BY_ALIAS_LABEL', 18),
('core', 200, 'created_by_email', 'MOLAJO_FIELD_CREATED_BY_EMAIL_LABEL', 19),
('core', 200, 'created_by_ip_address', 'MOLAJO_FIELD_CREATED_BY_IP_ADDRESS_LABEL', 20),
('core', 200, 'created_by_referer', 'MOLAJO_FIELD_CREATED_BY_REFERER_LABEL', 21),
('core', 200, 'created_by_website', 'MOLAJO_FIELD_CREATED_BY_WEBSITE_LABEL', 22),
('core', 200, 'featured', 'MOLAJO_FIELD_FEATURED_LABEL', 23),
('core', 200, 'id', 'MOLAJO_FIELD_ID_LABEL', 24),
('core', 200, 'language', 'MOLAJO_FIELD_LANGUAGE_LABEL', 25),
('core', 200, 'level', 'MOLAJO_FIELD_LEVEL_LABEL', 26),
('core', 200, 'lft', 'MOLAJO_FIELD_LFT_LABEL', 27),
('core', 200, 'metadata', 'MOLAJO_FIELD_METADATA_LABEL', 28),
('core', 200, 'metadesc', 'MOLAJO_FIELD_METADESC_LABEL', 29),
('core', 200, 'metakey', 'MOLAJO_FIELD_METAKEY_LABEL', 30),
('core', 200, 'meta_author', 'MOLAJO_FIELD_META_AUTHOR_LABEL', 31),
('core', 200, 'meta_rights', 'MOLAJO_FIELD_META_RIGHTS_LABEL', 32),
('core', 200, 'meta_robots', 'MOLAJO_FIELD_META_ROBOTS_LABEL', 33),
('core', 200, 'modified', 'MOLAJO_FIELD_MODIFIED_LABEL', 34),
('core', 200, 'modified_by', 'MOLAJO_FIELD_MODIFIED_BY_LABEL', 35),
('core', 200, 'ordering', 'MOLAJO_FIELD_ORDERING_LABEL', 36),
('core', 200, 'publish_down', 'MOLAJO_FIELD_PUBLISH_DOWN_LABEL', 37),
('core', 200, 'publish_up', 'MOLAJO_FIELD_PUBLISH_UP_LABEL', 38),
('core', 200, 'rgt', 'MOLAJO_FIELD_RGT_LABEL', 39),
('core', 200, 'state', 'MOLAJO_FIELD_STATE_LABEL', 40),
('core', 200, 'state_prior_to_version', 'MOLAJO_FIELD_STATE_PRIOR_TO_VERSION_LABEL', 41),
('core', 200, 'stickied', 'MOLAJO_FIELD_STICKIED_LABEL', 42),
('core', 200, 'user_default', 'MOLAJO_FIELD_USER_DEFAULT_LABEL', 43),
('core', 200, 'category_default', 'MOLAJO_FIELD_CATEGORY_DEFAULT_LABEL', 44),
('core', 200, 'title', 'MOLAJO_FIELD_TITLE_LABEL', 45),
('core', 200, 'subtitle', 'MOLAJO_FIELD_SUBTITLE_LABEL', 46),
('core', 200, 'version', 'MOLAJO_FIELD_VERSION_LABEL', 47),
('core', 200, 'version_of_id', 'MOLAJO_FIELD_VERSION_OF_ID_LABEL', 48);

/* 210 MOLAJO_CONFIG_OPTION_ID_PUBLISH_FIELDS */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 210, '', '', 0),
('core', 210, 'access', 'MOLAJO_FIELD_ACCESS_LABEL', 1),
('core', 210, 'featured', 'MOLAJO_FIELD_FEATURED_LABEL', 2),
('core', 210, 'ordering', 'MOLAJO_FIELD_ORDERING_LABEL', 3),
('core', 210, 'publish_down', 'MOLAJO_FIELD_PUBLISH_DOWN_LABEL', 4),
('core', 210, 'publish_up', 'MOLAJO_FIELD_PUBLISH_UP_LABEL', 5),
('core', 210, 'state', 'MOLAJO_FIELD_STATE_LABEL', 6),
('core', 210, 'stickied', 'MOLAJO_FIELD_STICKIED_LABEL', 7);

/* 220 MOLAJO_CONFIG_OPTION_ID_JSON_FIELDS */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 220, '', '', 0),
('core', 220, 'attribs', 'MOLAJO_FIELD_ATTRIBS_LABEL', 1),
('core', 220, 'metadata', 'MOLAJO_FIELD_METADATA_LABEL', 2),
('core', 220, 'params', 'MOLAJO_FIELD_PARAMETERS_LABEL', 3);

/* 230 MOLAJO_CONFIG_OPTION_ID_CONTENT_TYPES */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 230, '', '', 0),
('core', 230, 'content_type', 'Content Type', 1);

/* 250 MOLAJO_CONFIG_OPTION_ID_STATE */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 250, '', '', 0),
('core', 250, '2', 'MOLAJO_OPTION_ARCHIVED', 1),
('core', 250, '1', 'MOLAJO_OPTION_PUBLISHED', 2),
('core', 250, '0', 'MOLAJO_OPTION_UNPUBLISHED', 3),
('core', 250, '-1', 'MOLAJO_OPTION_TRASHED', 4),
('core', 250, '-2', 'MOLAJO_OPTION_SPAMMED', 5),
('core', 250, '-10', 'MOLAJO_OPTION_VERSION', 6);

/* USER INTERFACE */

/* 300 MOLAJO_CONFIG_OPTION_ID_LIST_TOOLBAR_BUTTONS */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 300, '', '', 0),
('core', 300, 'archive', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_ARCHIVE', 1),
('core', 300, 'checkin', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_CHECKIN', 2),
('core', 300, 'delete', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_DELETE', 3),
('core', 300, 'edit', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_EDIT', 4),
('core', 300, 'feature', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_FEATURE', 5),
('core', 300, 'help', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_HELP', 6),
('core', 300, 'new', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_NEW', 7),
('core', 300, 'options', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_OPTIONS', 8),
('core', 300, 'publish', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_PUBLISH', 9),
('core', 300, 'restore', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_RESTORE', 10),
('core', 300, 'separator', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SEPARATOR', 11),
('core', 300, 'spam', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SPAM', 12),
('core', 300, 'sticky', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_STICKY', 13),
('core', 300, 'trash', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_TRASH', 14),
('core', 300, 'unpublish', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_UNPUBLISH', 15);

/* 310 MOLAJO_CONFIG_OPTION_ID_EDIT_TOOLBAR_BUTTONS */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 310, '', '', 0),
('core', 310, 'apply', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_APPLY', 1),
('core', 310, 'close', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_CLOSE', 2),
('core', 310, 'help', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_HELP', 3),
('core', 310, 'restore', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_RESTORE', 4),
('core', 310, 'save', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SAVE', 5),
('core', 310, 'save2new', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SAVE_AND_NEW', 6),
('core', 310, 'save2copy', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SAVE_AS_COPY', 7),
('core', 310, 'separator', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SEPARATOR', 8);

/* 320 MOLAJO_CONFIG_OPTION_ID_TOOLBAR_SUBMENU_LINKS */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 320, '', '', 0),
('core', 320, 'category', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_CATEGORY', 1),
('core', 320, 'default', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_DEFAULT', 2),
('core', 320, 'featured', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_FEATURED', 3),
('core', 320, 'revisions', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_REVISIONS', 4),
('core', 320, 'stickied', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_STICKIED', 5),
('core', 320, 'unpublished', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_UNPUBLISHED', 6);

/* 330 MOLAJO_CONFIG_OPTION_ID_LISTBOX_FILTER */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 330, '', '', 0),
('core', 330, 'access', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_ACCESS', 1),
('core', 330, 'alias', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_ALIAS', 2),
('core', 330, 'created_by', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_AUTHOR', 3),
('core', 330, 'catid', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_CATEGORY', 4),
('core', 330, 'content_type', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_CONTENT_TYPE', 5),
('core', 330, 'created', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_CREATE_DATE', 6),
('core', 330, 'featured', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_FEATURED', 7),
('core', 330, 'language', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_LANGUAGE', 9),
('core', 330, 'modified', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_UPDATE_DATE', 10),
('core', 330, 'publish_up', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_PUBLISH_DATE', 11),
('core', 330, 'state', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_STATE', 12),
('core', 330, 'stickied', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_STICKIED', 13),
('core', 330, 'title', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_TITLE', 14),
('core', 330, 'subtitle', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_SUBTITLE', 15);

/* 340 MOLAJO_CONFIG_OPTION_ID_EDITOR_BUTTONS */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 340, '', '', 0),
('core', 340, 'article', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_ARTICLE', 1),
('core', 340, 'audio', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_AUDIO', 2),
('core', 340, 'file', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_FILE', 3),
('core', 340, 'gallery', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_GALLERY', 4),
('core', 340, 'image', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_IMAGE', 5),
('core', 340, 'pagebreak', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_PAGEBREAK', 6),
('core', 340, 'readmore', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_READMORE', 7),
('core', 340, 'video', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_VIDEO', 8);

/* MIME from ftp://ftp.iana.org/assignments/media-types/ */

/* 400 MOLAJO_CONFIG_OPTION_ID_AUDIO_MIMES */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 400, '', '', 0),
('core', 400, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 1),
('core', 400, 'sp-midi', 'sp-midi', 2),
('core', 400, 'vnd.3gpp.iufp', 'vnd.3gpp.iufp', 3),
('core', 400, 'vnd.4SB', 'vnd.4SB', 4),
('core', 400, 'vnd.CELP', 'vnd.CELP', 5),
('core', 400, 'vnd.audiokoz', 'vnd.audiokoz', 6),
('core', 400, 'vnd.cisco.nse', 'vnd.cisco.nse', 7),
('core', 400, 'vnd.cmles.radio-events', 'vnd.cmles.radio-events', 8),
('core', 400, 'vnd.cns.anp1', 'vnd.cns.anp1', 9),
('core', 400, 'vnd.cns.inf1', 'vnd.cns.inf1', 10),
('core', 400, 'vnd.dece.audio', 'vnd.dece.audio', 11),
('core', 400, 'vnd.digital-winds', 'vnd.digital-winds', 12),
('core', 400, 'vnd.dlna.adts', 'vnd.dlna.adts', 13),
('core', 400, 'vnd.dolby.heaac.1', 'vnd.dolby.heaac.1', 14),
('core', 400, 'vnd.dolby.heaac.2', 'vnd.dolby.heaac.2', 15),
('core', 400, 'vnd.dolby.mlp', 'vnd.dolby.mlp', 16),
('core', 400, 'vnd.dolby.mps', 'vnd.dolby.mps', 17),
('core', 400, 'vnd.dolby.pl2', 'vnd.dolby.pl2', 18),
('core', 400, 'vnd.dolby.pl2x', 'vnd.dolby.pl2x', 19),
('core', 400, 'vnd.dolby.pl2z', 'vnd.dolby.pl2z', 20),
('core', 400, 'vnd.dolby.pulse.1', 'vnd.dolby.pulse.1', 21),
('core', 400, 'vnd.dra', 'vnd.dra', 22),
('core', 400, 'vnd.dts', 'vnd.dts', 23),
('core', 400, 'vnd.dts.hd', 'vnd.dts.hd', 24),
('core', 400, 'vnd.dvb.file', 'vnd.dvb.file', 25),
('core', 400, 'vnd.everad.plj', 'vnd.everad.plj', 26),
('core', 400, 'vnd.hns.audio', 'vnd.hns.audio', 27),
('core', 400, 'vnd.lucent.voice', 'vnd.lucent.voice', 28),
('core', 400, 'vnd.ms-playready.media.pya', 'vnd.ms-playready.media.pya', 29),
('core', 400, 'vnd.nokia.mobile-xmf', 'vnd.nokia.mobile-xmf', 30),
('core', 400, 'vnd.nortel.vbk', 'vnd.nortel.vbk', 31),
('core', 400, 'vnd.nuera.ecelp4800', 'vnd.nuera.ecelp4800', 32),
('core', 400, 'vnd.nuera.ecelp7470', 'vnd.nuera.ecelp7470', 33),
('core', 400, 'vnd.nuera.ecelp9600', 'vnd.nuera.ecelp9600', 34),
('core', 400, 'vnd.octel.sbc', 'vnd.octel.sbc', 35),
('core', 400, 'vnd.qcelp', 'vnd.qcelp', 36),
('core', 400, 'vnd.rhetorex.32kadpcm', 'vnd.rhetorex.32kadpcm', 37),
('core', 400, 'vnd.rip', 'vnd.rip', 38),
('core', 400, 'vnd.sealedmedia.softseal-mpeg', 'vnd.sealedmedia.softseal-mpeg', 39),
('core', 400, 'vnd.vmx.cvsd', 'vnd.vmx.cvsd', 40);

/* 410 MOLAJO_CONFIG_OPTION_ID_IMAGE_MIMES */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 410, '', '', 0),
('core', 410, 'cgm', 'cgm', 1),
('core', 410, 'jp2', 'jp2', 2),
('core', 410, 'jpm', 'jpm', 3),
('core', 410, 'jpx', 'jpx', 4),
('core', 410, 'naplps', 'naplps', 5),
('core', 410, 'png', 'png', 6),
('core', 410, 'prs.btif', 'prs.btif', 7),
('core', 410, 'prs.pti', 'prs.pti', 8),
('core', 410, 'vnd-djvu', 'vnd-djvu', 9),
('core', 410, 'vnd-svf', 'vnd-svf', 10),
('core', 410, 'vnd-wap-wbmp', 'vnd-wap-wbmp', 11),
('core', 410, 'vnd.adobe.photoshop', 'vnd.adobe.photoshop', 12),
('core', 410, 'vnd.cns.inf2', 'vnd.cns.inf2', 13),
('core', 410, 'vnd.dece.graphic', 'vnd.dece.graphic', 14),
('core', 410, 'vnd.dvb.subtitle', 'vnd.dvb.subtitle', 15),
('core', 410, 'vnd.dwg', 'vnd.dwg', 16),
('core', 410, 'vnd.dxf', 'vnd.dxf', 17),
('core', 410, 'vnd.fastbidsheet', 'vnd.fastbidsheet', 18),
('core', 410, 'vnd.fpx', 'vnd.fpx', 19),
('core', 410, 'vnd.fst', 'vnd.fst', 20),
('core', 410, 'vnd.fujixerox.edmics-mmr', 'vnd.fujixerox.edmics-mmr', 21),
('core', 410, 'vnd.fujixerox.edmics-rlc', 'vnd.fujixerox.edmics-rlc', 22),
('core', 410, 'vnd.globalgraphics.pgb', 'vnd.globalgraphics.pgb', 23),
('core', 410, 'vnd.microsoft.icon', 'vnd.microsoft.icon', 24),
('core', 410, 'vnd.mix', 'vnd.mix', 25),
('core', 410, 'vnd.ms-modi', 'vnd.ms-modi', 26),
('core', 410, 'vnd.net-fpx', 'vnd.net-fpx', 27),
('core', 410, 'vnd.radiance', 'vnd.radiance', 28),
('core', 410, 'vnd.sealed-png', 'vnd.sealed-png', 29),
('core', 410, 'vnd.sealedmedia.softseal-gif', 'vnd.sealedmedia.softseal-gif', 30),
('core', 410, 'vnd.sealedmedia.softseal-jpg', 'vnd.sealedmedia.softseal-jpg', 31),
('core', 410, 'vnd.xiff', 'vnd.xiff', 32);

/* 420 MOLAJO_CONFIG_OPTION_ID_TEXT_MIMES */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 420, '', '', 0),
('core', 420, 'n3', 'n3', 1),
('core', 420, 'prs.fallenstein.rst', 'prs.fallenstein.rst', 2),
('core', 420, 'prs.lines.tag', 'prs.lines.tag', 3),
('core', 420, 'rtf', 'rtf', 4),
('core', 420, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 5),
('core', 420, 'tab-separated-values', 'tab-separated-values', 6),
('core', 420, 'turtle', 'turtle', 7),
('core', 420, 'vnd-curl', 'vnd-curl', 8),
('core', 420, 'vnd.DMClientScript', 'vnd.DMClientScript', 9),
('core', 420, 'vnd.IPTC.NITF', 'vnd.IPTC.NITF', 10),
('core', 420, 'vnd.IPTC.NewsML', 'vnd.IPTC.NewsML', 11),
('core', 420, 'vnd.abc', 'vnd.abc', 12),
('core', 420, 'vnd.curl', 'vnd.curl', 13),
('core', 420, 'vnd.dvb.subtitle', 'vnd.dvb.subtitle', 14),
('core', 420, 'vnd.esmertec.theme-descriptor', 'vnd.esmertec.theme-descriptor', 15),
('core', 420, 'vnd.fly', 'vnd.fly', 16),
('core', 420, 'vnd.fmi.flexstor', 'vnd.fmi.flexstor', 17),
('core', 420, 'vnd.graphviz', 'vnd.graphviz', 18),
('core', 420, 'vnd.in3d.3dml', 'vnd.in3d.3dml', 19),
('core', 420, 'vnd.in3d.spot', 'vnd.in3d.spot', 20),
('core', 420, 'vnd.latex-z', 'vnd.latex-z', 21),
('core', 420, 'vnd.motorola.reflex', 'vnd.motorola.reflex', 22),
('core', 420, 'vnd.ms-mediapackage', 'vnd.ms-mediapackage', 23),
('core', 420, 'vnd.net2phone.commcenter.command', 'vnd.net2phone.commcenter.command', 24),
('core', 420, 'vnd.si.uricatalogue', 'vnd.si.uricatalogue', 25),
('core', 420, 'vnd.sun.j2me.app-descriptor', 'vnd.sun.j2me.app-descriptor', 26),
('core', 420, 'vnd.trolltech.linguist', 'vnd.trolltech.linguist', 27),
('core', 420, 'vnd.wap-wml', 'vnd.wap-wml', 28),
('core', 420, 'vnd.wap.si', 'vnd.wap.si', 29),
('core', 420, 'vnd.wap.wmlscript', 'vnd.wap.wmlscript', 30);

/* 430 MOLAJO_CONFIG_OPTION_ID_VIDEO_MIMES */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 430, '', '', 0),
('core', 430, 'jpm', 'jpm', 1),
('core', 430, 'mj2', 'mj2', 2),
('core', 430, 'quicktime', 'quicktime', 3),
('core', 430, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 4),
('core', 430, 'vnd-mpegurl', 'vnd-mpegurl', 5),
('core', 430, 'vnd-vivo', 'vnd-vivo', 6),
('core', 430, 'vnd.CCTV', 'vnd.CCTV', 7),
('core', 430, 'vnd.dece-mp4', 'vnd.dece-mp4', 8),
('core', 430, 'vnd.dece.hd', 'vnd.dece.hd', 9),
('core', 430, 'vnd.dece.mobile', 'vnd.dece.mobile', 10),
('core', 430, 'vnd.dece.pd', 'vnd.dece.pd', 11),
('core', 430, 'vnd.dece.sd', 'vnd.dece.sd', 12),
('core', 430, 'vnd.dece.video', 'vnd.dece.video', 13),
('core', 430, 'vnd.directv-mpeg', 'vnd.directv-mpeg', 14),
('core', 430, 'vnd.directv.mpeg-tts', 'vnd.directv.mpeg-tts', 15),
('core', 430, 'vnd.dvb.file', 'vnd.dvb.file', 16),
('core', 430, 'vnd.fvt', 'vnd.fvt', 17),
('core', 430, 'vnd.hns.video', 'vnd.hns.video', 18),
('core', 430, 'vnd.iptvforum.1dparityfec-1010', 'vnd.iptvforum.1dparityfec-1010', 19),
('core', 430, 'vnd.iptvforum.1dparityfec-2005', 'vnd.iptvforum.1dparityfec-2005', 20),
('core', 430, 'vnd.iptvforum.2dparityfec-1010', 'vnd.iptvforum.2dparityfec-1010', 21),
('core', 430, 'vnd.iptvforum.2dparityfec-2005', 'vnd.iptvforum.2dparityfec-2005', 22),
('core', 430, 'vnd.iptvforum.ttsavc', 'vnd.iptvforum.ttsavc', 23),
('core', 430, 'vnd.iptvforum.ttsmpeg2', 'vnd.iptvforum.ttsmpeg2', 24),
('core', 430, 'vnd.motorola.video', 'vnd.motorola.video', 25),
('core', 430, 'vnd.motorola.videop', 'vnd.motorola.videop', 26),
('core', 430, 'vnd.mpegurl', 'vnd.mpegurl', 27),
('core', 430, 'vnd.ms-playready.media.pyv', 'vnd.ms-playready.media.pyv', 28),
('core', 430, 'vnd.nokia.interleaved-multimedia', 'vnd.nokia.interleaved-multimedia', 29),
('core', 430, 'vnd.nokia.videovoip', 'vnd.nokia.videovoip', 30),
('core', 430, 'vnd.objectvideo', 'vnd.objectvideo', 31),
('core', 430, 'vnd.sealed-swf', 'vnd.sealed-swf', 32),
('core', 430, 'vnd.sealed.mpeg1', 'vnd.sealed.mpeg1', 33),
('core', 430, 'vnd.sealed.mpeg4', 'vnd.sealed.mpeg4', 34),
('core', 430, 'vnd.sealed.swf', 'vnd.sealed.swf', 35),
('core', 430, 'vnd.sealedmedia.softseal-mov', 'vnd.sealedmedia.softseal-mov', 36),
('core', 430, 'vnd.uvvu.mp4', 'vnd.uvvu.mp4', 37);

/** MVC */

/* CONTROLLER TASKS */

/* 1100 MOLAJO_CONFIG_OPTION_ID_TASK_TO_CONTROLLER */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1100, '', '', 0),
('core', 1100, 'add', 'display', 1),
('core', 1100, 'edit', 'display', 2),
('core', 1100, 'display', 'display', 3);

INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1100, 'apply', 'edit', 4),
('core', 1100, 'cancel', 'edit', 5),
('core', 1100, 'create', 'edit', 6),
('core', 1100, 'save', 'edit', 7),
('core', 1100, 'save2copy', 'edit', 8),
('core', 1100, 'save2new', 'edit', 9),
('core', 1100, 'restore', 'edit', 10);

INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1100, 'archive', 'multiple', 11),
('core', 1100, 'publish', 'multiple', 12),
('core', 1100, 'unpublish', 'multiple', 13),
('core', 1100, 'spam', 'multiple', 14),
('core', 1100, 'trash', 'multiple', 15),
('core', 1100, 'feature', 'multiple', 16),
('core', 1100, 'unfeature', 'multiple', 17),
('core', 1100, 'sticky', 'multiple', 18),
('core', 1100, 'unsticky', 'multiple', 19),
('core', 1100, 'checkin', 'multiple', 20),
('core', 1100, 'reorder', 'multiple', 21),
('core', 1100, 'orderup', 'multiple', 22),
('core', 1100, 'orderdown', 'multiple', 23),
('core', 1100, 'saveorder', 'multiple', 24),
('core', 1100, 'delete', 'multiple', 25),
('core', 1100, 'copy', 'multiple', 26),
('core', 1100, 'move', 'multiple', 27);

INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1100, 'login', 'login', 28),
('core', 1100, 'logout', 'logout', 29);

/* 1100 MOLAJO_CONFIG_OPTION_ID_TASK_TO_CONTROLLER +application id */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1101, '', '', 0),
('core', 1101, 'add', 'display', 1),
('core', 1101, 'edit', 'display', 2),
('core', 1101, 'display', 'display', 3);

INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1101, 'apply', 'edit', 4),
('core', 1101, 'cancel', 'edit', 5),
('core', 1101, 'create', 'edit', 6),
('core', 1101, 'save', 'edit', 7),
('core', 1101, 'save2copy', 'edit', 8),
('core', 1101, 'save2new', 'edit', 9),
('core', 1101, 'restore', 'edit', 10);

INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1101, 'archive', 'multiple', 11),
('core', 1101, 'publish', 'multiple', 12),
('core', 1101, 'unpublish', 'multiple', 13),
('core', 1101, 'spam', 'multiple', 14),
('core', 1101, 'trash', 'multiple', 15),
('core', 1101, 'feature', 'multiple', 16),
('core', 1101, 'unfeature', 'multiple', 17),
('core', 1101, 'sticky', 'multiple', 18),
('core', 1101, 'unsticky', 'multiple', 19),
('core', 1101, 'checkin', 'multiple', 20),
('core', 1101, 'reorder', 'multiple', 21),
('core', 1101, 'orderup', 'multiple', 22),
('core', 1101, 'orderdown', 'multiple', 23),
('core', 1101, 'saveorder', 'multiple', 24),
('core', 1101, 'delete', 'multiple', 25),
('core', 1101, 'copy', 'multiple', 26),
('core', 1101, 'move', 'multiple', 27);

INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1101, 'login', 'login', 28),
('core', 1101, 'logout', 'login', 29);

/* OPTION */

/* 1800 MOLAJO_CONFIG_OPTION_ID_DEFAULT_OPTION */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1800, '', '', 0),
('core', 1800, 'com_articles', 'com_articles', 1),
('core', 1801, '', '', 0),
('core', 1801, 'com_login', 'com_login', 1);

/* VIEWS */

/* 2000 MOLAJO_CONFIG_OPTION_ID_VIEWS */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 2000, '', '', 0),
('core', 2000, 'display', 'display', 1),
('core', 2000, 'edit', 'edit', 2);

/* 2100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_VIEW */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 2100, '', '', 0),
('core', 2100, 'display', 'display', 1);

/* 2000 MOLAJO_CONFIG_OPTION_ID_VIEWS +application id **/
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 2001, '', '', 0),
('core', 2001, 'display', 'display', 1),
('core', 2001, 'edit', 'edit', 2);

/* 2100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_VIEW +application id **/
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 2101, '', '', 0),
('core', 2101, 'display', 'display', 1);

/* VIEW LAYOUTS */

/* 3000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_LAYOUTS */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3000, '', '', 0),
('core', 3000, 'default', 'default', 1),
('core', 3000, 'item', 'item', 1),
('core', 3000, 'items', 'items', 1),
('core', 3000, 'table', 'table', 1);

/* 3100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3100, '', '', 0),
('core', 3100, 'default', 'default', 1);

/* 3200 MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_LAYOUTS */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3200, '', '', 0),
('core', 3200, 'default', 'default', 1);

/* 3300 MOLAJO_CONFIG_OPTION_ID_DEFAULT_EDIT_VIEW_LAYOUTS */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3300, '', '', 0),
('core', 3300, 'default', 'default', 1);

/* 3000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_LAYOUTS +application id **/
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3001, '', '', 0),
('core', 3001, 'default', 'default', 1);

/* 3100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS +application id **/
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3101, '', '', 0),
('core', 3101, 'default', 'default', 1);

/* 3200 MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_LAYOUTS +application id **/
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3201, '', '', 0),
('core', 3201, 'default', 'default', 1);

/* 3300 MOLAJO_CONFIG_OPTION_ID_DEFAULT_EDIT_VIEW_LAYOUTS +application id **/
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3301, '', '', 0),
('core', 3301, 'default', 'default', 1);

/* VIEW FORMATS */

/* 4000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_FORMATS */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4000, '', '', 0),
('core', 4000, 'html', 'html', 1);

/* 4100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_FORMATS */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4100, '', '', 0),
('core', 4100, 'html', 'html', 1);

/* 4200 MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_FORMATS */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4200, '', '', 0),
('core', 4200, 'error', 'error', 1),
('core', 4200, 'feed', 'feed', 2),
('core', 4200, 'html', 'html', 3),
('core', 4200, 'json', 'json', 4),
('core', 4200, 'opensearch', 'opensearch', 5),
('core', 4200, 'raw', 'raw', 6),
('core', 4200, 'xls', 'xls', 7),
('core', 4200, 'xml', 'xml', 8),
('core', 4200, 'xmlrpc', 'xmlrpc', 9);

/* 4300 MOLAJO_CONFIG_OPTION_ID_DEFAULT_EDIT_VIEW_FORMATS */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4300, '', '', 0),
('core', 4300, 'html', 'html', 1);


/* 4000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_FORMATS +application id */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4001, '', '', 0),
('core', 4001, 'html', 'html', 1);

/* 4100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_FORMATS +application id */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4101, '', '', 0),
('core', 4101, 'html', 'html', 1);

/* 4200 MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_FORMATS +application id */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4201, '', '', 0),
('core', 4201, 'error', 'error', 1),
('core', 4201, 'feed', 'feed', 2),
('core', 4201, 'html', 'html', 3),
('core', 4201, 'json', 'json', 4),
('core', 4201, 'opensearch', 'opensearch', 5),
('core', 4201, 'raw', 'raw', 6),
('core', 4201, 'xls', 'xls', 7),
('core', 4201, 'xml', 'xml', 8),
('core', 4201, 'xmlrpc', 'xmlrpc', 9);

/* 4300 MOLAJO_CONFIG_OPTION_ID_DEFAULT_EDIT_VIEW_FORMATS +application id */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4301, '', '', 0),
('core', 4301, 'html', 'html', 1);

/* 5000 MOLAJO_CONFIG_OPTION_ID_MODEL +application id */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 5000, '', '', 0),
('core', 5000, 'display', 'display', 1),
('core', 5000, 'edit', 'edit', 2);

/* 5001 MOLAJO_CONFIG_OPTION_ID_MODEL +application id */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 5001, '', '', 0),
('core', 5001, 'display', 'display', 1),
('core', 5001, 'edit', 'edit', 2);

/* 6000 MOLAJO_CONFIG_OPTION_ID_PLUGIN_TYPE */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 6000, '', '', 0),
('core', 6000, 'content', 'content', 1);

/** ACL Component Information */

/** 10000 MOLAJO_CONFIG_OPTION_ID_ACL_IMPLEMENTATION **/
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 10000, '', '', 0),
('core', 10000, 'core', 'Core ACL Implementation', 1);

/** 10100 MOLAJO_CONFIG_OPTION_ID_ACL_ITEM_TESTS **/
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 10100, '', '', 0),
('core', 10100, 'view', 'view', 1),
('core', 10100, 'create', 'create', 2),
('core', 10100, 'edit', 'edit', 3),
('core', 10100, 'publish', 'publish', 4),
('core', 10100, 'delete', 'delete', 5),
('core', 10100, 'admin', 'admin', 6);

/** 10000 MOLAJO_CONFIG_OPTION_ID_TASK_ACL_METHODS **/
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 10200, '', '', 0),
('core', 10200, 'add', 'create', 1),
('core', 10200, 'admin', 'admin', 2),
('core', 10200, 'apply', 'edit', 3),
('core', 10200, 'archive', 'publish', 4),
('core', 10200, 'cancel', '', 5),
('core', 10200, 'checkin', 'admin', 6),
('core', 10200, 'close', '', 7),
('core', 10200, 'copy', 'create', 8),
('core', 10200, 'create', 'create', 9),
('core', 10200, 'delete', 'delete', 10),
('core', 10200, 'view', 'view', 11),
('core', 10200, 'edit', 'edit', 12),
('core', 10200, 'editstate', 'publish', 13),
('core', 10200, 'feature', 'publish', 14),
('core', 10200, 'login', 'login', 15),
('core', 10200, 'logout', 'logout', 16),
('core', 10200, 'manage', 'edit', 17),
('core', 10200, 'move', 'edit', 18),
('core', 10200, 'orderdown', 'publish', 19),
('core', 10200, 'orderup', 'publish', 20),
('core', 10200, 'publish', 'publish', 21),
('core', 10200, 'reorder', 'publish', 22),
('core', 10200, 'restore', 'publish', 23),
('core', 10200, 'save', 'edit', 24),
('core', 10200, 'save2copy', 'edit', 25),
('core', 10200, 'save2new', 'edit', 26),
('core', 10200, 'saveorder', 'publish', 27),
('core', 10200, 'search', 'view', 28),
('core', 10200, 'spam', 'publish', 29),
('core', 10200, 'state', 'publish', 30),
('core', 10200, 'sticky', 'publish', 31),
('core', 10200, 'trash', 'publish', 32),
('core', 10200, 'unfeature', 'publish', 33),
('core', 10200, 'unpublish', 'publish', 34),
('core', 10200, 'unsticky', 'publish', 35);

#
# com_login
#

/* TABLE */

/* 100 MOLAJO_CONFIG_OPTION_ID_TABLE */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 100, '', '', 0),
('com_login', 100, '__dummy', '__dummy', 1);

/** MVC */

/* CONTROLLER TASKS */

/* 1100 MOLAJO_CONFIG_OPTION_ID_TASK_TO_CONTROLLER */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 1100, '', '', 0),
('com_login', 1100, 'display', 'display', 3);

INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 1100, 'login', 'login', 28),
('com_login', 1100, 'logout', 'login', 29);

/* 1100 MOLAJO_CONFIG_OPTION_ID_TASK_TO_CONTROLLER +application id */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 1101, '', '', 0),
('com_login', 1101, 'display', 'display', 3);

INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 1101, 'login', 'login', 28),
('com_login', 1101, 'logout', 'login', 29);

/* VIEWS */

/* 2000 MOLAJO_CONFIG_OPTION_ID_VIEWS */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 2000, '', '', 0),
('com_login', 2000, 'display', 'display', 1);

/* 2100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_VIEW */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 2100, '', '', 0),
('com_login', 2100, 'display', 'display', 1);

/* 2000 MOLAJO_CONFIG_OPTION_ID_VIEWS +application id **/
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 2001, '', '', 0),
('com_login', 2001, 'display', 'display', 1);

/* 2100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_VIEW +application id **/
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 2101, '', '', 0),
('com_login', 2101, 'display', 'display', 1);

/* VIEW LAYOUTS */

/* 3000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_LAYOUTS */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 3000, '', '', 0),
('com_login', 3000, 'login', 'login', 1);

/* 3100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 3100, '', '', 0),
('com_login', 3100, 'login', 'login', 1);

/* 3000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_LAYOUTS +application id **/
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 3001, '', '', 0),
('com_login', 3001, 'adminlogin', 'adminlogin', 1);

/* 3100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS +application id **/
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 3101, '', '', 0),
('com_login', 3101, 'adminlogin', 'adminlogin', 1);

/* VIEW FORMATS */

/* 4000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_FORMATS */
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 4000, '', '', 0),
('com_login', 4000, 'html', 'html', 1),
('com_login', 4001, 'html', 'html', 1);

/* MODELS */

/* 5000 MOLAJO_CONFIG_OPTION_ID_MODEL +application id */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 5000, '', '', 0),
('com_login', 5000, 'dummy', 'dummy', 1);

/* 5000 MOLAJO_CONFIG_OPTION_ID_MODEL +application id */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 5001, '', '', 0),
('com_login', 5001, 'dummy', 'dummy', 1);

/* 6000 MOLAJO_CONFIG_OPTION_ID_PLUGIN_TYPE */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 6000, '', '', 0),
('com_login', 6000, 'user', 'user', 1);

/** ACL Component Information */

/** 10000 MOLAJO_CONFIG_OPTION_ID_ACL_IMPLEMENTATION **/
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 10000, '', '', 0),
('com_login', 10000, 'core', 'Core ACL Implementation', 1);

/** 10100 MOLAJO_CONFIG_OPTION_ID_ACL_ITEM_TESTS **/
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 10100, '', '', 0),
('com_login', 10100, 'view', 'view', 1);

/** 10000 MOLAJO_CONFIG_OPTION_ID_TASK_ACL_METHODS **/
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 10200, '', '', 0),
('com_login', 10200, 'login', 'login', 15),
('com_login', 10200, 'logout', 'logout', 16);

/* ARTICLES */

/* 100 MOLAJO_CONFIG_OPTION_ID_TABLE */;
INSERT INTO `#__configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 100, '', '', 0),
('com_articles', 100, '__articles', '__articles', 1);
