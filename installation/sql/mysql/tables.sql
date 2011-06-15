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
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key',
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY  (`id`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__assets`
#
#   An Asset ID is a unique key assigned to any item (asset) subject to ACL control
#   The ACL Assets table contains a list of assigned ids and associated content_table 
#   The asset id must be stored in the item using the column named asset_id
#

CREATE TABLE IF NOT EXISTS `#__assets` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Assets Primary Key',
  `content_table` VARCHAR(100) NOT NULL DEFAULT '',
    PRIMARY KEY  (`id`),
    UNIQUE KEY `idx_content_table_id_join` (`content_table`,`id`)
) DEFAULT CHARSET=utf8;

#
# Table structure for table `#__permissions_groups`
#   A complete list of assigned actions by asset id for groups
#

CREATE TABLE IF NOT EXISTS `#__permissions_groups` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Permissions Primary Key',
  `group_id` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #_groups.id',
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__assets.id',
  `action_id` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__actions.id',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idx_asset_action_to_group_lookup` (`asset_id`,`action_id`,`group_id`),
  UNIQUE KEY `idx_group_to_asset_action_lookup` (`group_id`,`asset_id`,`action_id`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__permissions_groupings`
#
#   A complete list of assigned actions by asset id for groupings of groups
#

CREATE TABLE IF NOT EXISTS `#__permissions_groupings` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Permissions Primary Key',
  `grouping_id` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__groups.id',
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__assets.id',
  `action_id` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__actions.id',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idx_asset_action_to_group_lookup` (`asset_id`,`action_id`,`grouping_id`),
  UNIQUE KEY `idx_group_to_asset_action_lookup` (`grouping_id`,`asset_id`,`action_id`)
)  DEFAULT CHARSET=utf8;

#
# CLIENTS (Applications)
#

#
# Table structure for table `#__clients`
#
CREATE TABLE `#__clients` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Client Primary Key',
  `client_id` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Numeric value associated with the client',
  `name` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `path` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'URL Alias',
  `description` mediumtext NOT NULL,
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `access` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'FK to the #__groupings table',
  `metakey` text COMMENT 'Meta Key',
  `metadesc` text COMMENT 'Meta Description',
  `metadata` text COMMENT 'Meta Data',
  `attribs` text COMMENT 'Attributes (Custom Fields)',
  `params` MEDIUMTEXT COMMENT 'Configurable Parameter Values',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_asset_id` (`asset_id`),
  KEY `idx_access` (`access`)
) DEFAULT CHARSET=utf8;

#
# USERS AND GROUPS
#

#
# Table structure for table `#__users`
#

CREATE TABLE `#__users` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `username` VARCHAR(150) NOT NULL DEFAULT '',
  `email` VARCHAR(255) NOT NULL DEFAULT '',
  `password` VARCHAR(100) NOT NULL DEFAULT '',
  `block` tinyint(4) NOT NULL DEFAULT '0',
  `sendEmail` tinyint(4) DEFAULT '0',
  `registerDate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastvisitDate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activation` VARCHAR(100) NOT NULL DEFAULT '',
  `params` MEDIUMTEXT COMMENT 'Configurable Parameter Values',
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `access` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__groupings table',
  PRIMARY KEY  (`id`),
  KEY `idx_name` (`name`),
  KEY `idx_block` (`block`),
  KEY `username` (`username`),
  KEY `email` (`email`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__user_profiles`
#

CREATE TABLE `#__user_profiles` (
  `user_id` INT (11) NOT NULL,
  `profile_key` VARCHAR(100) NOT NULL,
  `profile_value` VARCHAR(255) NOT NULL,
  `ordering` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Ordering',
  UNIQUE KEY `idx_user_id_profile_key` (`user_id`,`profile_key`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__groups`
#   A group is a structure for defining a set of user(s) for the purpose of assigning permissions or other applications
#   When a user is assigned to a Group, that user is also a member of existing and future child groups
#   Each user is also assigned a special group that can be used to assign "Edit Own", "View Own" or "Delete Own" Permissions
#   "User Groups" are also a good tool to add someone to a specific item, rather than all assets associated with a Group
#   In smaller implementations or social networks, "User Groups" provides support for friending, etc.
#

CREATE TABLE IF NOT EXISTS `#__groups` (
  `id` INT (11) UNSIGNED NOT NULL auto_increment COMMENT 'Group Primary Key',
  `parent_id` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Parent ID',
  `lft` INT (11) NOT NULL DEFAULT '0' COMMENT 'Nested set lft.',
  `rgt` INT (11) NOT NULL DEFAULT '0' COMMENT 'Nested set rgt.',
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `description` MEDIUMTEXT NOT NULL DEFAULT '',
  `type_id` INT (11) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Users: 0, Groups: 1',
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `access` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'View level Access with a FK to the #__groupings table',
  `protected` boolean NOT NULL DEFAULT 0 COMMENT 'If true, protects group from system removal via the interface.',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idx_usergroup_parent_title_lookup` (`parent_id`,`title`,`type_id`),
  KEY `idx_access` (`access`),
  KEY `idx_usergroup_title_lookup` (`title`),
  KEY `idx_usergroup_adjacency_lookup` (`parent_id`),
  KEY `idx_usergroup_type_id` (`type_id`),
  KEY `idx_usergroup_nested_set_lookup` USING BTREE (`lft`,`rgt`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__groupings`
#
#     A collection of groups which have been defined for a specific action and asset id
#     These are created by Molajo ACL and used for efficiency with database queries
#     Replaces viewlevel table and provides this structure for view and other ACL actions
#

CREATE TABLE IF NOT EXISTS `#__groupings` (
  `id` INT (11) UNSIGNED NOT NULL auto_increment COMMENT 'Groupings Primary Key',
  `group_name_list` TEXT NOT NULL DEFAULT '',
  `group_id_list` TEXT NOT NULL DEFAULT '',
  PRIMARY KEY  (`id`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__group_to_groupings`
#
#     Links the group to the groupings table
#

CREATE TABLE IF NOT EXISTS `#__group_to_groupings` (
  `id` INT (11) UNSIGNED NOT NULL auto_increment COMMENT 'Group to Group Primary Key',
  `group_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__group table.',
  `grouping_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__groupings table.',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idx_group_to_groupings_id` (`group_id`, `grouping_id`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__user_groups`
#
#   Groups to which users belong
#

CREATE TABLE IF NOT EXISTS `#__user_groups` (
  `user_id` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__users.id',
  `group_id` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__groups.id',
  PRIMARY KEY  (`user_id`,`group_id`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__user_groupings`
#
#   Groupings of groups to which users belong
#

CREATE TABLE IF NOT EXISTS `#__user_groupings` (
  `user_id` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__users.id',
  `grouping_id` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__groupings.id',
  PRIMARY KEY  (`user_id`,`grouping_id`)
)  DEFAULT CHARSET=utf8;


#
# Table structure for table `#__user_clients`
#
#   Clients to which users belong
#

CREATE TABLE IF NOT EXISTS `#__user_clients` (
  `user_id` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__users.id',
  `client_id` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__clients.id',
  PRIMARY KEY  (`user_id`,`client_id`)
)  DEFAULT CHARSET=utf8;

#
# CONTENT
#

#
# Table structure for table `#__categories`
#

CREATE TABLE `#__categories` (
  `id` INT (11) NOT NULL auto_increment,
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `parent_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
  `lft` INT (11) NOT NULL DEFAULT '0',
  `rgt` INT (11) NOT NULL DEFAULT '0',
  `level` INT (11) UNSIGNED NOT NULL DEFAULT '0',
  `path` VARCHAR(255) NOT NULL DEFAULT '',
  `extension` VARCHAR(50) NOT NULL DEFAULT '',
  `title` VARCHAR(255) NOT NULL,
  `alias` VARCHAR(255) NOT NULL DEFAULT '',
  `note` VARCHAR(255) NOT NULL DEFAULT '',
  `description` MEDIUMTEXT NOT NULL DEFAULT '',
  `published` INT (11) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out` INT (11) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__groupings table',
  `params` MEDIUMTEXT COMMENT 'Configurable Parameter Values',
  `metadesc` VARCHAR(1024) NOT NULL DEFAULT ' ' COMMENT 'The meta description for the page.',
  `metakey` VARCHAR(1024) NOT NULL DEFAULT ' ' COMMENT 'The meta keywords for the page.',
  `metadata` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'JSON encoded metadata properties.',
  `created_user_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
  `created_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
  `modified_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hits` INT (11) UNSIGNED NOT NULL DEFAULT '0',
  `language` CHAR(7) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cat_idx` (`extension`,`published`,`access`),
  UNIQUE KEY `idx_asset_id` (`asset_id`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_path` (`path`),
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
  `alias` VARCHAR (255) NOT NULL DEFAULT ' ' COMMENT 'URL Alias',

  `content_type` tinyint(3) NOT NULL DEFAULT '0' COMMENT 'Content Type: Links to #__configuration.option_id = 10 and content_table values matching ',

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

  `content_table` VARCHAR(50) NOT NULL DEFAULT ' ' COMMENT 'Component Option Value',
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

  KEY `idx_component_component_id_id` (`content_table`, `component_id`, `id`),
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
# EXTENSIONS
#

#
# Table structure for table `#__extensions`
#

CREATE TABLE `#__extensions` (
  `id` INT (11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `type` VARCHAR(20) NOT NULL,
  `element` VARCHAR(100) NOT NULL,
  `folder` VARCHAR(100) NOT NULL,
  `client_id` INT (11) NOT NULL,
  `enabled` TINYINT(3) NOT NULL DEFAULT '1',
  `access` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__groupings table',
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `protected` TINYINT(3) NOT NULL DEFAULT '0',
  `manifest_cache` MEDIUMTEXT  NOT NULL,
  `params` MEDIUMTEXT COMMENT 'Configurable Parameter Values',
  `custom_data` MEDIUMTEXT COMMENT 'Available for Custom Data needed by the Extension',
  `system_data` MEDIUMTEXT COMMENT 'Configurable Parameter Values',
  `checked_out` INT (11) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Ordering',
  `state` TINYINT (3) NOT NULL DEFAULT '0' COMMENT 'Published State 2: Archived 1: Published 0: Unpublished -1: Trashed -2: Spam -10 Version',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_asset_id` (`asset_id`, `access`),
  INDEX `element_clientid`(`element`, `client_id`),
  INDEX `element_folder_clientid`(`element`, `folder`, `client_id`),
  INDEX `extension`(`type`,`element`,`folder`,`client_id`)
) AUTO_INCREMENT=10000 CHARACTER SET utf8;

#
# MENUS
#

#
# Table structure for table `#__menu_types`
#

CREATE TABLE `#__menu_types` (
  `id` INT (11) UNSIGNED NOT NULL auto_increment,
  `menutype` VARCHAR(255) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY  (`id`),
  UNIQUE `idx_menutype` (`menutype`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__menu`
#

CREATE TABLE `#__menu` (
  `id` INT (11) NOT NULL AUTO_INCREMENT,
  `menutype` VARCHAR(24) NOT NULL DEFAULT ' ' COMMENT 'The type of menu this item belongs to. FK to #__menu_types.menutype',
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'The display title of the menu item.',
  `alias` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'The SEF alias of the menu item.',
  `note` VARCHAR(255) NOT NULL DEFAULT '',
  `path` VARCHAR(1024) NOT NULL DEFAULT ' ' COMMENT 'The computed path of the menu item based on the alias field.',
  `link` VARCHAR(1024) NOT NULL DEFAULT ' ' COMMENT 'The actually link the menu item refers to.',
  `type` VARCHAR(16) NOT NULL DEFAULT ' ' COMMENT 'The type of link: Component, URL, Alias, Separator',
  `published` INT (11)NOT NULL DEFAULT '0' COMMENT 'The published state of the menu link.',
  `parent_id` INT (11) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'The parent menu item in the menu tree.',
  `level` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The relative level in the tree.',
  `component_id` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'FK to #__extensions.id',
  `ordering` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Ordering',
  `checked_out` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'FK to #__users.id',
  `checked_out_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'The time the menu item was checked out.',
  `browserNav` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'The click behaviour of the link.',
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `access` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__groupings table',
  `img` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'The image of the menu item.',
  `template_style_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
  `params` MEDIUMTEXT COMMENT 'Configurable Parameter Values',
  `lft` INT (11) NOT NULL DEFAULT '0' COMMENT 'Nested set lft.',
  `rgt` INT (11) NOT NULL DEFAULT '0' COMMENT 'Nested set rgt.',
  `home` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Indicates if this menu item is the home or DEFAULT page.',
  `language` CHAR(7) NOT NULL DEFAULT '',
  `client_id` INT (11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_client_id_parent_id_alias` (`client_id`,`parent_id`,`alias`),
  UNIQUE KEY `idx_asset_id` (`asset_id`),
  KEY `idx_componentid` (`component_id`, `menutype`, `published`, `access`),
  KEY `idx_menutype` (`menutype`),
  KEY `idx_left_right` (`lft`,`rgt`),
  KEY `idx_alias` (`alias`),
  KEY `idx_path` (`path`(333)),
  KEY `idx_language` (`language`)
)   DEFAULT CHARSET=utf8;

#
# Table structure for table `#__modules_menu`
#

CREATE TABLE `#__modules_menu` (
  `moduleid` INT (11) NOT NULL DEFAULT '0',
  `menuid` INT (11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`moduleid`,`menuid`)
) DEFAULT CHARSET=utf8;

#
# Table structure for table `#__modules`
#

CREATE TABLE `#__modules` (
  `id` INT (11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `note` VARCHAR(255) NOT NULL DEFAULT '',
  `content` MEDIUMTEXT NOT NULL,
  `ordering` INT (11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Ordering',
  `position` VARCHAR(50) DEFAULT NULL,
  `checked_out` INT (11) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` INT (11) UNSIGNED NOT NULL DEFAULT '0',
  `module` VARCHAR(255) DEFAULT NULL,
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `access` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__groupings table',
  `showtitle` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `params` MEDIUMTEXT COMMENT 'Configurable Parameter Values',
  `client_id` INT (11) NOT NULL DEFAULT '0',
  `language` CHAR(7) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idx_asset_id` (`asset_id`),
  KEY `published` (`published`,`access`),
  KEY `newsfeeds` (`module`,`published`),
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
  `published` INT (11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`lang_id`),
  UNIQUE `idx_sef` (`sef`)
)  DEFAULT CHARSET=utf8;

#
# TEMPLATES
#

#
# Table structure for table `#__template_styles`
#

CREATE TABLE IF NOT EXISTS `#__template_styles` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `template` VARCHAR(50) NOT NULL DEFAULT '',
  `client_id` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `home` CHAR(7) NOT NULL DEFAULT '0',
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
# Table structure for table `#__messages`
#

CREATE TABLE `#__messages` (
  `message_id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id_from` INT (11) UNSIGNED NOT NULL DEFAULT '0',
  `user_id_to` INT (11) UNSIGNED NOT NULL DEFAULT '0',
  `folder_id` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `date_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `priority` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `subject` VARCHAR(255) NOT NULL DEFAULT '',
  `message` MEDIUMTEXT COMMENT 'Messages',
  PRIMARY KEY (`message_id`),
  KEY `useridto_state` (`user_id_to`,`state`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__messages_cfg`
#

CREATE TABLE `#__messages_cfg` (
  `user_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
  `cfg_name` VARCHAR(100) NOT NULL DEFAULT '',
  `cfg_value` VARCHAR(255) NOT NULL DEFAULT '',
  UNIQUE KEY `idx_user_var_name` (`user_id`,`cfg_name`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__session`
#

CREATE TABLE `#__session` (
  `session_id` VARCHAR(32) NOT NULL DEFAULT '',
  `client_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
  `guest` tinyint(4) UNSIGNED DEFAULT '1',
  `time` VARCHAR(14) DEFAULT '',
  `data` LONGTEXT DEFAULT NULL,
  `userid` INT (11) DEFAULT '0',
  `username` VARCHAR(150) DEFAULT '',
  PRIMARY KEY  (`session_id`),
  KEY `whosonline` (`guest`),
  KEY `userid` (`userid`),
  KEY `time` (`time`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__configuration`
#

CREATE TABLE IF NOT EXISTS `#__configuration` (
  `content_table` VARCHAR(50) NOT NULL DEFAULT '',
  `option_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
  `option_value` VARCHAR(80) NOT NULL DEFAULT '',
  `option_value_literal` VARCHAR(255) NOT NULL DEFAULT ' ',
  `ordering` INT (11) NOT NULL DEFAULT '0',
  UNIQUE KEY `idx_content_table_id_value_key` (`content_table`,`option_id`,`option_value`)
) DEFAULT CHARSET=utf8;

#
# UPDATES
#

#
# Table structure for table `#__updates`
#

CREATE TABLE  `#__updates` (
  `id` INT (11) NOT NULL auto_increment,
  `update_site_id` INT (11) DEFAULT '0',
  `extension_id` INT (11) DEFAULT '0',
  `categoryid` INT (11) DEFAULT '0',
  `name` VARCHAR(100) DEFAULT '',
  `description` text NOT NULL,
  `element` VARCHAR(100) DEFAULT '',
  `type` VARCHAR(20) DEFAULT '',
  `folder` VARCHAR(20) DEFAULT '',
  `client_id` INT (11) DEFAULT '0',
  `version` VARCHAR(10) DEFAULT '',
  `data` text NOT NULL,
  `detailsurl` text NOT NULL,
  PRIMARY KEY  (`id`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__update_sites`
#

CREATE TABLE  `#__update_sites` (
  `update_site_id` INT (11) NOT NULL auto_increment,
  `name` VARCHAR(100) DEFAULT '',
  `type` VARCHAR(20) DEFAULT '',
  `location` text NOT NULL,
  `enabled` INT (11) DEFAULT '0',
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