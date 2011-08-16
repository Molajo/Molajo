# $Id: tables.sql

#
# ACL
#

#
# Table structure for table `amy_actions`
#
#   Contains a definitive list of ACL Actions that can be defined within Molajo
#   Login is the only action associated with Client. Client has no other actions beyond login.
#   View and Create permissions for an asset do not imply any other permissions. 
#   Edit permission includes permission to View the asset.
#   Delete permission includes permission to View and Edit the asset.
#   Admin permission includes all other permissions for that asset and configuration permission.
#

CREATE TABLE IF NOT EXISTS `amy_actions` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key' ,  
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) )
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

#
# Table structure for table `amy_assets`
#
#   An Asset ID is a unique key assigned to any item (asset) subject to ACL control
#   The ACL Assets table contains a list of assigned ids and associated component_option
#   The asset id must be stored in the item using the column named asset_id
#

CREATE TABLE IF NOT EXISTS `amy_assets` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Assets Primary Key' ,
  `content_table` VARCHAR(100) NOT NULL DEFAULT '' ,
  `content_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Content Primary Key',
  `access` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the amy_groupings table',
  PRIMARY KEY (`id`) )
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

#
# CLIENTS (Applications)
#

#
# Table structure for table `amy_applications`
#
CREATE TABLE `amy_applications` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Application Primary Key',
  `application_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Numeric value associated with the application',
  `name` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `path` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'URL Alias',
  `description` mediumtext NOT NULL,
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the amy_assets table.',
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
# Table structure for table `amy_users`
#

CREATE TABLE IF NOT EXISTS `amy_users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL DEFAULT '' ,
  `username` VARCHAR(150) NOT NULL DEFAULT '' ,
  `email` VARCHAR(255) NOT NULL DEFAULT '' ,
  `password` VARCHAR(100) NOT NULL DEFAULT '' ,
  `block` TINYINT(4) NOT NULL DEFAULT 0 ,
  `sendEmail` TINYINT(4) NULL DEFAULT 0 ,
  `registerDate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `lastvisitDate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `activation` VARCHAR(100) NOT NULL DEFAULT '' ,
  `params` MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Configurable Parameter Values' ,
  `asset_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the amy_assets table.',
  PRIMARY KEY (`id`) )
DEFAULT CHARACTER SET = utf8;

#
# Table structure for table `amy_user_profiles`
#

CREATE TABLE IF NOT EXISTS `amy_user_profiles` (
  `user_id` INT(11) NOT NULL ,
  `profile_key` VARCHAR(100) NOT NULL ,
  `profile_value` VARCHAR(255) NOT NULL ,
  `ordering` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Ordering' )
DEFAULT CHARACTER SET = utf8;

#
# Table structure for table `amy_groups`
#
#   A group is a structure for defining a set of user(s) for the purpose of assigning permissions or other applications
#   When a user is assigned to a Group, that user is also a member of existing and future child groups
#   Each user is also assigned a special group that can be used to assign "Edit Own", "View Own" or "Delete Own" Permissions
#   "User Groups" are also a good tool to add someone to a specific item, rather than all assets associated with a Group
#   In smaller implementations or social networks, "User Groups" provides support for friending, etc.
#

CREATE TABLE IF NOT EXISTS `amy_groups` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Group Primary Key' ,
  `parent_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Parent ID' ,
  `lft` INT(11) NOT NULL DEFAULT 0 COMMENT 'Nested set lft.' ,
  `rgt` INT(11) NOT NULL DEFAULT 0 COMMENT 'Nested set rgt.' ,
  `title` VARCHAR(255) NOT NULL DEFAULT '' ,
  `subtitle` VARCHAR(255) NOT NULL DEFAULT '',
  `description` MEDIUMTEXT NOT NULL ,
  `type_id` INT(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Users: 0, Groups: 1' ,
  `asset_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the amy_assets table.' ,
  `protected` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'If true, protects group from system removal via the interface.' ,
  PRIMARY KEY (`id`) )
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

#
# Table structure for table `amy_user_groups`
#
#   Groups to which users belong
#

CREATE TABLE IF NOT EXISTS `amy_user_groups` (
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to amy_users.id' ,
  `group_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to amy_groups.id' ,
  PRIMARY KEY (`user_id`, `group_id`) )
DEFAULT CHARACTER SET = utf8;

#
# Table structure for table `amy_groupings`
#
#     A collection of groups which have been defined for a specific action and asset id
#     These are created by Molajo ACL and used for efficiency with database queries
#     Replaces viewlevel table and provides this structure for view and other ACL actions
#

CREATE  TABLE IF NOT EXISTS `amy_groupings` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Groupings Primary Key' ,
  `group_name_list` TEXT NOT NULL ,
  `group_id_list` TEXT NOT NULL ,
  PRIMARY KEY (`id`) )
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

#
# Table structure for table `amy_group_to_groupings`
#
#     A listing of groups that belong to the group
#

CREATE TABLE IF NOT EXISTS `amy_group_to_groupings` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Group to Group Primary Key' ,
  `group_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the amy_group table.' ,
  `grouping_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the amy_groupings table.' ,
  PRIMARY KEY (`id`) )
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

#
# Table structure for table `amy_user_groupings`
#
#   Groupings of groups to which users belong
#

CREATE TABLE IF NOT EXISTS `amy_user_groupings` (
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to amy_users.id' ,
  `grouping_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to amy_groupings.id' ,
  PRIMARY KEY (`user_id`, `grouping_id`) )
DEFAULT CHARACTER SET = utf8;

#
# Table structure for table `amy_user_applications`
#
#   Applications to which users belong
#

CREATE TABLE IF NOT EXISTS `amy_user_applications` (
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to amy_users.id' ,
  `application_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to amy_applications.id' ,
  PRIMARY KEY (`user_id`, `application_id`) )
DEFAULT CHARACTER SET = utf8;

#
# Table structure for table `amy_permissions_groups`
#   A complete list of assigned actions by asset id for groups
#

CREATE TABLE IF NOT EXISTS `amy_permissions_groups` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Permissions Primary Key' ,
  `group_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to #_groups.id' ,
  `asset_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to amy_assets.id' ,
  `action_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to amy_actions.id' ,
  PRIMARY KEY (`id`) )
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

#
# Table structure for table `amy_permissions_groupings`
#
#   A complete list of assigned actions by asset id for groupings of groups
#

CREATE TABLE IF NOT EXISTS `amy_permissions_groupings` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Permissions Primary Key' ,
  `grouping_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to amy_groups.id' ,
  `asset_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to amy_assets.id' ,
  `action_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to amy_actions.id' ,
  PRIMARY KEY (`id`) )
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

#
# CONTENT
#

#
# Table structure for table `amy_categories`
#

CREATE TABLE `amy_categories` (
  `id` INT (11) NOT NULL auto_increment,
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the amy_assets table.',
  `parent_id` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `lft` INT (11) NOT NULL DEFAULT 0,
  `rgt` INT (11) NOT NULL DEFAULT 0,
  `level` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `path` VARCHAR(255) NOT NULL DEFAULT '',
  `extension` VARCHAR(50) NOT NULL DEFAULT '',
  `title` VARCHAR(255) NOT NULL,
  `subtitle` VARCHAR(255) NOT NULL DEFAULT '',
  `alias` VARCHAR(255) NOT NULL DEFAULT '',
  `note` VARCHAR(255) NOT NULL DEFAULT '',
  `description` MEDIUMTEXT NOT NULL DEFAULT '',
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
  KEY `idx_path` (`path`),
  KEY `idx_left_right` (`lft`,`rgt`),
  KEY `idx_alias` (`alias`),
  INDEX `idx_language` (`language`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `amy_articles`
#

CREATE TABLE IF NOT EXISTS `amy_articles` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `catid` INT (11) UNSIGNED NOT NULL DEFAULT  0 COMMENT 'Category ID associated with the Primary Key',

  `title` VARCHAR (255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `subtitle` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Subtitle',
  `alias` VARCHAR (255) NOT NULL DEFAULT ' ' COMMENT 'URL Alias',

  `content_type` tinyint(3) NOT NULL DEFAULT 0 COMMENT 'Content Type: Links to amy_configuration.option_id = 10 and component_option values matching ',

  `content_text` MEDIUMTEXT NULL COMMENT 'Content Primary Text Field, can include break to designate Introductory and Full text',
  `content_link` VARCHAR (2083) NULL COMMENT 'Content Link for Weblink or Newsfeed Field',
  `content_email_address` VARCHAR (255) NULL COMMENT 'Content Email Field',
  `content_numeric_value` TINYINT (3) NULL COMMENT 'Content Numeric Value, ex. vote on poll',
  `content_file` VARCHAR (255) NOT NULL DEFAULT '' COMMENT 'Content Network Path to File',

  `featured` boolean NOT NULL DEFAULT 0 COMMENT 'Featured 1-Featured 0-Not Featured',
  `stickied` boolean NOT NULL DEFAULT 0 COMMENT 'Stickied 1-Stickied 0-Not Stickied',
  `user_default` boolean NOT NULL DEFAULT 0 COMMENT 'User DEFAULT 1-DEFAULT 0-Not DEFAULT',
  `category_default` boolean NOT NULL DEFAULT 0 COMMENT 'Category DEFAULT 1-DEFAULT 0-Not DEFAULT',
  `language` CHAR (7) NOT NULL DEFAULT '' COMMENT 'Language',
  `ordering` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Ordering',

  `state` TINYINT (3) NOT NULL DEFAULT 0 COMMENT 'Published State 2: Archived 1: Published 0: Unpublished -1: Trashed -2: Spam -10 Version',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish Begin Date and Time',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish End Date and Time',
  `version` INT (11) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Version Number',
  `version_of_id` INT (11) NULL COMMENT 'Primary ID for this Version',
  `state_prior_to_version` INT (11) UNSIGNED NULL COMMENT 'State value prior to creating this version copy and changing the state to Version',

  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'  COMMENT 'Created Date and Time',
  `created_by` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Created by User ID',
  `created_by_alias` VARCHAR (255) NOT NULL DEFAULT '' COMMENT 'Created by Alias',
  `created_by_email` VARCHAR (255) NULL COMMENT 'Created By Email Address',
  `created_by_website` VARCHAR (255) NULL COMMENT 'Created By Website',
  `created_by_ip_address` CHAR(15) NULL COMMENT 'Created By IP Address',
  `created_by_referer` VARCHAR (255) NULL COMMENT 'Created By Referer',

  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modified Date',
  `modified_by` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Modified By User ID',

  `checked_out` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Checked out by User Id',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Checked out Date and Time',

  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the amy_assets table.',

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
# Table structure for table `amy_common`
#

CREATE TABLE IF NOT EXISTS `amy_common` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `catid` INT (11) UNSIGNED NOT NULL DEFAULT  0 COMMENT 'Category ID associated with the Primary Key',

  `title` VARCHAR (255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `subtitle` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Subtitle',
  `alias` VARCHAR (255) NOT NULL DEFAULT ' ' COMMENT 'URL Alias',

  `content_type` tinyint(3) NOT NULL DEFAULT 0 COMMENT 'Content Type: Links to amy_configuration.option_id = 10 and component_option values matching ',

  `content_text` MEDIUMTEXT NULL COMMENT 'Content Primary Text Field, can include break to designate Introductory and Full text',
  `content_link` VARCHAR (2083) NULL COMMENT 'Content Link for Weblink or Newsfeed Field',
  `content_email_address` VARCHAR (255) NULL COMMENT 'Content Email Field',
  `content_numeric_value` TINYINT (3) NULL COMMENT 'Content Numeric Value, ex. vote on poll',
  `content_file` VARCHAR (255) NOT NULL DEFAULT '' COMMENT 'Content Network Path to File',

  `featured` boolean NOT NULL DEFAULT 0 COMMENT 'Featured 1-Featured 0-Not Featured',
  `stickied` boolean NOT NULL DEFAULT 0 COMMENT 'Stickied 1-Stickied 0-Not Stickied',
  `user_default` boolean NOT NULL DEFAULT 0 COMMENT 'User DEFAULT 1-DEFAULT 0-Not DEFAULT',
  `category_default` boolean NOT NULL DEFAULT 0 COMMENT 'Category DEFAULT 1-DEFAULT 0-Not DEFAULT',
  `language` CHAR (7) NOT NULL DEFAULT '' COMMENT 'Language',
  `ordering` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Ordering',

  `state` TINYINT (3) NOT NULL DEFAULT 0 COMMENT 'Published State 2: Archived 1: Published 0: Unpublished -1: Trashed -2: Spam -10 Version',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish Begin Date and Time',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish End Date and Time',
  `version` INT (11) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Version Number',
  `version_of_id` INT (11) NULL COMMENT 'Primary ID for this Version',
  `state_prior_to_version` INT (11) UNSIGNED NULL COMMENT 'State value prior to creating this version copy and changing the state to Version',

  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'  COMMENT 'Created Date and Time',
  `created_by` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Created by User ID',
  `created_by_alias` VARCHAR (255) NOT NULL DEFAULT '' COMMENT 'Created by Alias',
  `created_by_email` VARCHAR (255) NULL COMMENT 'Created By Email Address',
  `created_by_website` VARCHAR (255) NULL COMMENT 'Created By Website',
  `created_by_ip_address` CHAR(15) NULL COMMENT 'Created By IP Address',
  `created_by_referer` VARCHAR (255) NULL COMMENT 'Created By Referer',

  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modified Date',
  `modified_by` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Modified By User ID',

  `checked_out` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Checked out by User Id',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Checked out Date and Time',

  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the amy_assets table.',

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
# Table structure for table `amy_extensions`
#

CREATE TABLE `amy_extensions` (
  `extension_id` INT (11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `type` VARCHAR(20) NOT NULL,
  `element` VARCHAR(100) NOT NULL,
  `folder` VARCHAR(100) NOT NULL,
  `application_id` INT (11) NOT NULL,
  `enabled` TINYINT(3) NOT NULL DEFAULT '1',
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the amy_assets table.',
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

CREATE TABLE `amy_schemas` (
  `extension_id` int(11) NOT NULL,
  `version_id` varchar(20) NOT NULL,
  PRIMARY KEY (`extension_id`, `version_id`)
)  DEFAULT CHARSET=utf8;
# -------------------------------------------------------

#
# MENUS
#

#
# Table structure for table `amy_menus`
#

CREATE TABLE `amy_menus` (
  `id` INT (11) UNSIGNED NOT NULL auto_increment,
  `application_id` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `menu_id` VARCHAR(255) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY  (`id`),
  UNIQUE `idx_menu_id` (`menu_id`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `amy_menu_items`
#

CREATE TABLE `amy_menu_items` (
  `id` INT (11) NOT NULL AUTO_INCREMENT,
  `menu_id` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'The type of menu this item belongs to. FK to amy_menus.menu_id',
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'The display title of the menu item.',
  `alias` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'The SEF alias of the menu item.',
  `note` VARCHAR(255) NOT NULL DEFAULT '',
  `path` VARCHAR(1024) NOT NULL DEFAULT ' ' COMMENT 'The computed path of the menu item based on the alias field.',
  `link` VARCHAR(1024) NOT NULL DEFAULT ' ' COMMENT 'The actually link the menu item refers to.',
  `type` VARCHAR(16) NOT NULL DEFAULT ' ' COMMENT 'The type of link: Component, URL, Alias, Separator',
  `published` INT (11)NOT NULL DEFAULT 0 COMMENT 'The published state of the menu link.',
  `parent_id` INT (11) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'The parent menu item in the menu tree.',
  `level` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'The relative level in the tree.',
  `component_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to amy_extensions.id',
  `ordering` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Ordering',
  `checked_out` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to amy_users.id',
  `checked_out_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'The time the menu item was checked out.',
  `browserNav` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'The click behaviour of the link.',
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the amy_assets table.',
  `img` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'The image of the menu item.',
  `template_style_id` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `params` MEDIUMTEXT COMMENT 'Configurable Parameter Values',
  `lft` INT (11) NOT NULL DEFAULT 0 COMMENT 'Nested set lft.',
  `rgt` INT (11) NOT NULL DEFAULT 0 COMMENT 'Nested set rgt.',
  `home` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Indicates if this menu item is the home or DEFAULT page.',
  `language` CHAR(7) NOT NULL DEFAULT '',
  `application_id` INT (11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_application_id_parent_id_alias` (`application_id`,`parent_id`,`alias`),
  KEY `idx_componentid` (`component_id`, `menu_id`, `published`),
  KEY `idx_menu_id` (`menu_id`),
  KEY `idx_left_right` (`lft`,`rgt`),
  KEY `idx_alias` (`alias`),
  KEY `idx_path` (`path`(333)),
  KEY `idx_language` (`language`)
)   DEFAULT CHARSET=utf8;

#
# Table structure for table `amy_modules_menu`
#

CREATE TABLE `amy_modules_menu` (
  `module_id` INT (11) NOT NULL DEFAULT 0,
  `menu_item_id` INT (11) NOT NULL DEFAULT 0,
  PRIMARY KEY  (`module_id`,`menu_item_id`)
) DEFAULT CHARSET=utf8;

#
# Table structure for table `amy_modules`
#

CREATE TABLE `amy_modules` (
  `id` INT (11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `subtitle` VARCHAR(255) NOT NULL DEFAULT '',
  `note` VARCHAR(255) NOT NULL DEFAULT '',
  `content` MEDIUMTEXT NOT NULL,
  `ordering` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Ordering',
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
  KEY `newsfeeds` (`module`,`published`),
  KEY `idx_language` (`language`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `amy_languages`
#

CREATE TABLE `amy_languages` (
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
# Table structure for table `amy_template_styles`
#

CREATE TABLE IF NOT EXISTS `amy_template_styles` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `template` VARCHAR(50) NOT NULL DEFAULT '',
  `application_id` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `home` CHAR(7) NOT NULL DEFAULT 0,
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `params` MEDIUMTEXT COMMENT 'Configurable Parameter Values',
  PRIMARY KEY  (`id`),
  KEY `idx_template` (`template`),
  KEY `idx_home` (`home`)
)  DEFAULT CHARSET=utf8 ;

#
# SYSTEM CONFIGURATION
#

#
# Table structure for table `amy_messages`
#

CREATE TABLE `amy_messages` (
  `message_id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id_from` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `user_id_to` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `folder_id` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `date_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state` tinyint(1) NOT NULL DEFAULT 0,
  `priority` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `subject` VARCHAR(255) NOT NULL DEFAULT '',
  `message` MEDIUMTEXT COMMENT 'Messages',
  PRIMARY KEY (`message_id`),
  KEY `useridto_state` (`user_id_to`,`state`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `amy_messages_cfg`
#

CREATE TABLE `amy_messages_cfg` (
  `user_id` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `cfg_name` VARCHAR(100) NOT NULL DEFAULT '',
  `cfg_value` VARCHAR(255) NOT NULL DEFAULT '',
  UNIQUE KEY `idx_user_var_name` (`user_id`,`cfg_name`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `amy_session`
#

CREATE TABLE `amy_session` (
  `session_id` VARCHAR(32) NOT NULL DEFAULT '',
  `application_id` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `guest` tinyint(4) UNSIGNED DEFAULT '1',
  `time` VARCHAR(14) DEFAULT '',
  `data` LONGTEXT DEFAULT NULL,
  `userid` INT (11) DEFAULT 0,
  `username` VARCHAR(150) DEFAULT '',
  PRIMARY KEY  (`session_id`),
  KEY `whosonline` (`guest`),
  KEY `userid` (`userid`),
  KEY `time` (`time`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `amy_configuration`
#

CREATE TABLE IF NOT EXISTS `amy_configuration` (
  `component_option` VARCHAR(50) NOT NULL DEFAULT '',
  `option_id` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `option_value` VARCHAR(80) NOT NULL DEFAULT '',
  `option_value_literal` VARCHAR(255) NOT NULL DEFAULT ' ',
  `ordering` INT (11) NOT NULL DEFAULT 0,
  UNIQUE KEY `idx_component_option_id_value_key` (`component_option`,`option_id`,`option_value`)
) DEFAULT CHARSET=utf8;

#
# UPDATES
#

#
# Table structure for table `amy_updates`
#

CREATE TABLE  `amy_updates` (
  `id` INT (11) NOT NULL auto_increment,
  `update_site_id` INT (11) DEFAULT 0,
  `extension_id` INT (11) DEFAULT 0,
  `categoryid` INT (11) DEFAULT 0,
  `name` VARCHAR(100) DEFAULT '',
  `description` text NOT NULL,
  `element` VARCHAR(100) DEFAULT '',
  `type` VARCHAR(20) DEFAULT '',
  `folder` VARCHAR(20) DEFAULT '',
  `application_id` INT (11) DEFAULT 0,
  `version` VARCHAR(10) DEFAULT '',
  `data` text NOT NULL,
  `detailsurl` text NOT NULL,
  PRIMARY KEY  (`id`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `amy_update_sites`
#

CREATE TABLE  `amy_update_sites` (
  `update_site_id` INT (11) NOT NULL auto_increment,
  `name` VARCHAR(100) DEFAULT '',
  `type` VARCHAR(20) DEFAULT '',
  `location` text NOT NULL,
  `enabled` INT (11) DEFAULT 0,
  PRIMARY KEY  (`update_site_id`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `amy_update_sites_extensions`
#

CREATE TABLE `amy_update_sites_extensions` (
  `update_site_id` INT(11) DEFAULT 0,
  `extension_id` INT(11) DEFAULT 0,
  PRIMARY KEY(`update_site_id`, `extension_id`)
)  DEFAULT CHARSET=utf8;

#
# APPLICATIONS
#

INSERT INTO `amy_applications` (`id`, `asset_id`, `application_id`, `name`, `path`)
  VALUES
    (1, 1, 0, 'site', ''),
    (2, 2, 1, 'administrator', 'administrator'),
    (3, 3, 2, 'installation', 'installation'),
    (4, 4, 3, 'content', 'content');

#
# USERS AND GROUPS
#

INSERT INTO `amy_groups` (`id`, `asset_id`, `parent_id`, `lft`, `rgt`, `title`, `protected`)
  VALUES
    (1, 11, 0, 0, 1, 'Public',        1),
    (2, 12, 0, 2, 3, 'Guest',         1),
    (3, 13, 0, 4, 5, 'Registered',    1),
    (4, 14, 0, 6, 7, 'Administrator', 1);

INSERT INTO `amy_groupings` (`id`, `group_name_list`, `group_id_list` )
  VALUES
    (1, 'Public', '1'),
    (2, 'Guest', '2'),
    (3, 'Registered', '3'),
    (4, 'Administrator', '4'),
    (5, 'Registered, Administrator', '4,5');

INSERT INTO `amy_group_to_groupings` ( `group_id` ,`grouping_id` ) SELECT 1, 1;
INSERT INTO `amy_group_to_groupings` ( `group_id` ,`grouping_id` ) SELECT 2, 2;
INSERT INTO `amy_group_to_groupings` ( `group_id` ,`grouping_id` ) SELECT 3, 3;
INSERT INTO `amy_group_to_groupings` ( `group_id` ,`grouping_id` ) SELECT 4, 4;
INSERT INTO `amy_group_to_groupings` ( `group_id` ,`grouping_id` ) SELECT 3, 5;
INSERT INTO `amy_group_to_groupings` ( `group_id` ,`grouping_id` ) SELECT 4, 5;

#
# EXTENSIONS
#

# Components - Administrator

INSERT INTO `amy_extensions` (
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

# Components - Site
INSERT INTO `amy_extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`)
  VALUES
    (201, 201, 'com_articles', 'component', 'com_articles', '', 0, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 2, 1),
    (202, 202, 'com_search', 'component', 'com_search', '', 0, 1, 1, '', '{"enabled":"0","show_date":"1"}', '', '', 0, '0000-00-00 00:00:00', 17, 1),
    (203, 203, 'com_users', 'component', 'com_users', '', 0, 1, 1, '', '{"allowUserRegistration":"1","useractivation":"1","frontend_userparams":"1","mailSubjectPrefix":"","mailBodySuffix":""}', '', '', 0, '0000-00-00 00:00:00', 19, 1);

# Layouts: Extensions

INSERT INTO `amy_extensions` (
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

# Layouts: Forms

INSERT INTO `amy_extensions` (
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

# Layouts: Wraps

INSERT INTO `amy_extensions` (
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

# Libraries

INSERT INTO `amy_extensions` (
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

# Modules - Administrator

INSERT INTO `amy_extensions` (
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

# Modules - Site

INSERT INTO `amy_extensions` (
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
