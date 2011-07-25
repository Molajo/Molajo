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
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) )
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX `idx_actions_table_id_join` ON `#__actions` (`id` ASC) ;
CREATE UNIQUE INDEX `idx_actions_table_title` ON `#__actions` (`title` ASC) ;

#
# Table structure for table `#__assets`
#
#   An Asset ID is a unique key assigned to any item (asset) subject to ACL control
#   The ACL Assets table contains a list of assigned ids and associated content_table
#   The asset id must be stored in the item using the column named asset_id
#

CREATE TABLE IF NOT EXISTS `#__assets` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Assets Primary Key' ,
  `content_table` VARCHAR(100) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`id`) )
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX `idx_content_table_id_join` ON `#__assets` (`content_table` ASC, `id` ASC) ;

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
  `path` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'URL Alias',
  `description` mediumtext NOT NULL,
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `access` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__groupings table',
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

CREATE TABLE IF NOT EXISTS `#__users` (
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
  `asset_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.' ,
  `access` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__groupings table' ,
  PRIMARY KEY (`id`) )
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `idx_name` ON `#__users` (`name` ASC) ;
CREATE INDEX `idx_block` ON `#__users` (`block` ASC) ;
CREATE INDEX `username` ON `#__users` (`username` ASC) ;
CREATE INDEX `email` ON `#__users` (`email` ASC) ;

#
# Table structure for table `#__user_profiles`
#

CREATE TABLE IF NOT EXISTS `#__user_profiles` (
  `user_id` INT(11) NOT NULL ,
  `profile_key` VARCHAR(100) NOT NULL ,
  `profile_value` VARCHAR(255) NOT NULL ,
  `ordering` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Ordering' )
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX `idx_user_id_profile_key` ON `#__user_profiles` (`user_id` ASC, `profile_key` ASC) ;

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
  `title` VARCHAR(255) NOT NULL DEFAULT '' ,
  `description` MEDIUMTEXT NOT NULL ,
  `type_id` INT(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Users: 0, Groups: 1' ,
  `asset_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.' ,
  `access` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'View level Access with a FK to the #__groupings table' ,
  `protected` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'If true, protects group from system removal via the interface.' ,
  PRIMARY KEY (`id`) )
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX `idx_usergroup_parent_title_lookup` ON `#__groups` (`parent_id` ASC, `title` ASC, `type_id` ASC) ;
CREATE INDEX `idx_access` ON `#__groups` (`access` ASC) ;
CREATE INDEX `idx_usergroup_title_lookup` ON `#__groups` (`title` ASC) ;
CREATE INDEX `idx_usergroup_adjacency_lookup` ON `#__groups` (`parent_id` ASC) ;
CREATE INDEX `idx_usergroup_type_id` ON `#__groups` (`type_id` ASC) ;
CREATE INDEX `idx_usergroup_nested_set_lookup` USING BTREE ON `#__groups` (`lft` ASC, `rgt` ASC) ;

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

CREATE INDEX `fk_molajo_user_groups_molajo_users1` ON `#__user_groups` (`user_id` ASC) ;
CREATE INDEX `fk_molajo_user_groups_molajo_groups1` ON `#__user_groups` (`group_id` ASC) ;

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

CREATE UNIQUE INDEX `idx_group_to_groupings_id` ON `#__group_to_groupings` (`group_id` ASC, `grouping_id` ASC) ;
CREATE INDEX `fk_molajo_group_to_groupings_molajo_groups1` ON `#__group_to_groupings` (`group_id` ASC) ;
CREATE INDEX `fk_molajo_group_to_groupings_molajo_groupings1` ON `#__group_to_groupings` (`grouping_id` ASC) ;

#
# Table structure for table `#__user_groupings`
#
#   Groupings of groups to which users belong
#

CREATE TABLE IF NOT EXISTS `#__user_groupings` (
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to #__users.id' ,
  `grouping_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to #__groupings.id' ,
  `id` INT(11) NOT NULL ,
  PRIMARY KEY (`user_id`, `grouping_id`, `id`) )
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_molajo_user_groupings_molajo_users1` ON `#__user_groupings` (`user_id` ASC) ;
CREATE INDEX `fk_molajo_user_groupings_molajo_groupings1` ON `#__user_groupings` (`grouping_id` ASC) ;

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

CREATE INDEX `user_id` ON `#__user_applications` (`user_id` ASC) ;
CREATE INDEX `fk_molajo_user_applications_molajo_users1` ON `#__user_applications` (`application_id` ASC) ;

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

CREATE UNIQUE INDEX `idx_asset_action_to_group_lookup` ON `#__permissions_groups` (`asset_id` ASC, `action_id` ASC, `group_id` ASC) ;
CREATE UNIQUE INDEX `idx_group_to_asset_action_lookup` ON `#__permissions_groups` (`group_id` ASC, `asset_id` ASC, `action_id` ASC) ;
CREATE INDEX `fk_molajo_permissions_groups_molajo_groups1` ON `#__permissions_groups` (`group_id` ASC) ;
CREATE INDEX `fk_molajo_permissions_groups_molajo_assets1` ON `#__permissions_groups` (`asset_id` ASC) ;
CREATE INDEX `fk_molajo_permissions_groups_molajo_actions1` ON `#__permissions_groups` (`action_id` ASC) ;

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

CREATE UNIQUE INDEX `idx_asset_action_to_group_lookup` ON `#__permissions_groupings` (`asset_id` ASC, `action_id` ASC, `grouping_id` ASC) ;
CREATE UNIQUE INDEX `idx_group_to_asset_action_lookup` ON `#__permissions_groupings` (`grouping_id` ASC, `asset_id` ASC, `action_id` ASC) ;
CREATE INDEX `fk_molajo_permissions_groupings_molajo_groupings1` ON `#__permissions_groupings` (`grouping_id` ASC) ;
CREATE INDEX `fk_molajo_permissions_groupings_molajo_assets1` ON `#__permissions_groupings` (`asset_id` ASC) ;
CREATE INDEX `fk_molajo_permissions_groupings_molajo_actions1` ON `#__permissions_groupings` (`action_id` ASC) ;

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
  `path` VARCHAR(255) NOT NULL DEFAULT '',
  `extension` VARCHAR(50) NOT NULL DEFAULT '',
  `title` VARCHAR(255) NOT NULL,
  `alias` VARCHAR(255) NOT NULL DEFAULT '',
  `note` VARCHAR(255) NOT NULL DEFAULT '',
  `description` MEDIUMTEXT NOT NULL DEFAULT '',
  `published` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__groupings table',
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

  `content_type` tinyint(3) NOT NULL DEFAULT 0 COMMENT 'Content Type: Links to #__configuration.option_id = 10 and content_table values matching ',

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

  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `access` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__groupings table',

  `content_table` VARCHAR(50) NOT NULL DEFAULT ' ' COMMENT 'Component Option Value',
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
  `extension_id` INT (11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `type` VARCHAR(20) NOT NULL,
  `element` VARCHAR(100) NOT NULL,
  `folder` VARCHAR(100) NOT NULL,
  `application_id` INT (11) NOT NULL,
  `enabled` TINYINT(3) NOT NULL DEFAULT '1',
  `access` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__groupings table',
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
  UNIQUE KEY `idx_asset_id` (`asset_id`, `access`),
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
  `published` INT (11)NOT NULL DEFAULT 0 COMMENT 'The published state of the menu link.',
  `parent_id` INT (11) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'The parent menu item in the menu tree.',
  `level` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'The relative level in the tree.',
  `component_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to #__extensions.id',
  `ordering` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Ordering',
  `checked_out` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to #__users.id',
  `checked_out_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'The time the menu item was checked out.',
  `browserNav` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'The click behaviour of the link.',
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `access` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__groupings table',
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
  `moduleid` INT (11) NOT NULL DEFAULT 0,
  `menuid` INT (11) NOT NULL DEFAULT 0,
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
  `ordering` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Ordering',
  `position` VARCHAR(50) DEFAULT NULL,
  `checked_out` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `module` VARCHAR(255) DEFAULT NULL,
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `access` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__groupings table',
  `showtitle` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `params` MEDIUMTEXT COMMENT 'Configurable Parameter Values',
  `application_id` INT (11) NOT NULL DEFAULT 0,
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
  `published` INT (11) NOT NULL DEFAULT 0,
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
  `subject` VARCHAR(255) NOT NULL DEFAULT '',
  `message` MEDIUMTEXT COMMENT 'Messages',
  PRIMARY KEY (`message_id`),
  KEY `useridto_state` (`user_id_to`,`state`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__messages_cfg`
#

CREATE TABLE `#__messages_cfg` (
  `user_id` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `cfg_name` VARCHAR(100) NOT NULL DEFAULT '',
  `cfg_value` VARCHAR(255) NOT NULL DEFAULT '',
  UNIQUE KEY `idx_user_var_name` (`user_id`,`cfg_name`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `#__session`
#

CREATE TABLE `#__session` (
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
# Table structure for table `#__configuration`
#

CREATE TABLE IF NOT EXISTS `#__configuration` (
  `content_table` VARCHAR(50) NOT NULL DEFAULT '',
  `option_id` INT (11) UNSIGNED NOT NULL DEFAULT 0,
  `option_value` VARCHAR(80) NOT NULL DEFAULT '',
  `option_value_literal` VARCHAR(255) NOT NULL DEFAULT ' ',
  `ordering` INT (11) NOT NULL DEFAULT 0,
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
# Table structure for table `#__update_sites`
#

CREATE TABLE  `#__update_sites` (
  `update_site_id` INT (11) NOT NULL auto_increment,
  `name` VARCHAR(100) DEFAULT '',
  `type` VARCHAR(20) DEFAULT '',
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

# $Id: configuration.sql

#
# CLIENTS
#

INSERT INTO `#__applications` (`id`, `application_id`, `name`, `path`, `access`, `asset_id`) VALUES (1, 0, 'site', 'MOLAJO_PATH_SITE', 1, 1);
INSERT INTO `#__applications` (`id`, `application_id`, `name`, `path`, `access`, `asset_id`) VALUES (2, 1, 'administrator', 'MOLAJO_PATH_ADMINISTRATOR', 5, 2);
INSERT INTO `#__applications` (`id`, `application_id`, `name`, `path`, `access`, `asset_id`) VALUES (3, 2, 'installation', 'MOLAJO_PATH_INSTALLATION', 0, 3);

#
# USERS AND GROUPS
#

INSERT INTO `#__groups` (`id` ,`parent_id` ,`lft` ,`rgt` ,`title`, `access`, `protected`, `asset_id`)
VALUES (1, 0, 0, 1, 'Public',        4, 1, 50);
INSERT INTO `#__groups` (`id` ,`parent_id` ,`lft` ,`rgt` ,`title`, `access`, `protected`, `asset_id`)
VALUES (2, 0, 2, 3, 'Guest',         4, 1, 60);
INSERT INTO `#__groups` (`id` ,`parent_id` ,`lft` ,`rgt` ,`title`, `access`, `protected`, `asset_id`)
VALUES (3, 0, 4, 5, 'Registered',    4, 1, 70);
INSERT INTO `#__groups` (`id` ,`parent_id` ,`lft` ,`rgt` ,`title`, `access`, `protected`, `asset_id`)
VALUES (4, 0, 6, 7, 'Administrator', 4, 1, 80);
INSERT INTO `#__groupings` (`id`, `group_name_list`, `group_id_list` ) VALUES
    (1, 'Public', '1'),
    (2, 'Guest', '2'),
    (3, 'Registered', '3'),
    (4, 'Administrator', '4'),
    (5, 'Registered, Administrator', '4,5');

INSERT INTO `#__group_to_groupings` ( `group_id` ,`grouping_id` ) SELECT 1, 1;
INSERT INTO `#__group_to_groupings` ( `group_id` ,`grouping_id` ) SELECT 2, 2;
INSERT INTO `#__group_to_groupings` ( `group_id` ,`grouping_id` ) SELECT 3, 3;
INSERT INTO `#__group_to_groupings` ( `group_id` ,`grouping_id` ) SELECT 4, 4;
INSERT INTO `#__group_to_groupings` ( `group_id` ,`grouping_id` ) SELECT 3, 5;
INSERT INTO `#__group_to_groupings` ( `group_id` ,`grouping_id` ) SELECT 4, 5;

#
# EXTENSIONS
#

# Components - Administrator
INSERT INTO `#__extensions` (
  `extension_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`, `protected`,
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
  `extension_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (210, 'com_articles', 'component', 'com_articles', '', 0, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 2, 1, 1, 1210),
    (220, 'com_search', 'component', 'com_search', '', 0, 1, 1, '', '{"enabled":"0","show_date":"1"}', '', '', 0, '0000-00-00 00:00:00', 17, 1, 1, 1220),
    (230, 'com_users', 'component', 'com_users', '', 0, 1, 1, '', '{"allowUserRegistration":"1","useractivation":"1","frontend_userparams":"1","mailSubjectPrefix":"","mailBodySuffix":""}', '', '', 0, '0000-00-00 00:00:00', 19, 1, 1, 1230);

# Layouts
INSERT INTO `#__extensions` (
  `extension_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`, `protected`,
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
  `extension_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (401, 'Akismet', 'library', 'akismet', 'akismet', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 1, 4, 1400),
    (402, 'Curl', 'library', 'curl', 'curl', 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 2, 1, 5, 1402),
    (403, 'Joomla Framework', 'library', 'joomla', 'joomla', 1, 1, 1, '{"legacy":false,"name":"Joomla! Web Application Framework","type":"library","creationDate":"2008","author":"Joomla","copyright":"Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.","authorEmail":"admin@joomla.org","authorUrl":"http:\\/\\/www.joomla.org","version":"1.6.0","description":"The Joomla! Web Application Framework","group":""}', '{}', '', '', 0, '0000-00-00 00:00:00', 3, 1, 4, 1404),
    (404, 'Molajo Application', 'library', 'molajo', 'molajo', 1, 1, 1, '{"legacy":false,"name":"Molajo Application","type":"library","creationDate":"2011","author":"Molajo Project Team","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved.","authorEmail":"collaborate@molajo.org","authorUrl":"http:\\/\\/molajo.org","version":"1.0.0","description":"Molajo is a web development environment useful for crafting custom solutions from simple to complex custom data architecture, presentation output, and access control.","group":""}\r\n', '', '', '', 0, '0000-00-00 00:00:00', 4, 1, 4, 1406),
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
  `extension_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`, `protected`,
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
  `extension_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`, `protected`,
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
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`,
  `application_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`,
  `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)
  VALUES
  (615, 'plg_authentication_gmail', 'plugin', 'gmail', 'authentication',
    0, 0, 1, 0, '', '{"applysuffix":"0","suffix":"","verifypeer":"1","user_blacklist":""}',
    '', '', 0, '0000-00-00 00:00:00', 4, 1616);

## Content
INSERT INTO `#__extensions` (
  `extension_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (701, 'plg_content_emailcloak', 'plugin', 'emailcloak', 'content', 1, 1, 0, '{}', '{"mode":"1"}', '', '', 0, '0000-00-00 00:00:00', 1, 2, 4, 1700),
    (705, 'plg_content_loadmodule', 'plugin', 'loadmodule', 'content', 1, 1, 0, '{}', '{"style":"none"}', '', '', 0, '0000-00-00 00:00:00', 3, 0, 4, 1705),
    (710, 'plg_content_molajosample', 'plugin', 'molajosample', 'content', 1, 1, 0, '{}', '{"enable_molajosample_feature":"1"}', '', '', 0, '0000-00-00 00:00:00', 4, 0, 4, 1710);

## Editors
INSERT INTO `#__extensions` (
  `extension_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (800, 'plg_editors_aloha', 'plugin', 'aloha', 'editors', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 5, 0, 4, 1800),
    (805, 'plg_editors_codemirror', 'plugin', 'codemirror', 'editors', 1, 1, 1, '', '{"linenumbers":"0","tabmode":"indent"}', '', '', 0, '0000-00-00 00:00:00', 6, 0, 4, 1805),
    (810, 'plg_editors_none', 'plugin', 'none', 'editors', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 7, 0, 4, 1810);

## Extended Editor
INSERT INTO `#__extensions` (
  `extension_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`, `protected`,
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
  `extension_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (926, 'plg_extension_joomla', 'plugin', 'joomla', 'extension', 1, 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 13, 0, 1, 926);

## Language
INSERT INTO `#__extensions` (
  `extension_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUE
    (950, 'English (United Kingdom)', 'language', 'en-GB', '', 0, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 44, 1, 4, 1950),
    (951, 'English (United Kingdom)', 'language', 'en-GB', '', 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 45, 1, 4, 1951);

## Molajo
INSERT INTO `#__extensions` (
  `extension_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (1005, 'plg_molajo_broadcast', 'plugin', 'broadcast', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_BROADCAST_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved.","authorEmail":"AmyStephen@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_BROADCAST_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 14, -1, 4, 10005),
    (1010, 'plg_molajo_compress', 'plugin', 'compress', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_COMPRESS_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"AmyStephen@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_COMPRESS_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 15, -1, 4, 10010),
    (1015, 'plg_molajo_categorization', 'plugin', 'categorization', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_CATEGORIZATION_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"AmyStephen@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_CATEGORIZATION_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 16, -1, 4, 10015),
    (1020, 'plg_molajo_content', 'plugin', 'content', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_CONTENT_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"AmyStephen@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_CONTENT_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 17, -1, 4, 10020),
    (1025, 'plg_molajo_extend', 'plugin', 'extend', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_SYSTEM_EXTEND_NAME","type":"plugin","creationDate":"May 2011","author":"Amy Stephen","copyright":"(C) 2011 Amy Stephen. All rights reserved.","authorEmail":"AmyStephen@molajo.org","authorUrl":"Molajo.org","version":"1.6.0","description":"PLG_SYSTEM_EXTEND_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 0, -1, 4, 10025),
    (1030, 'plg_molajo_links', 'plugin', 'links', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_LINKS_NAME","type":"plugin","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"AmyStephen@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_LINKS_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 18, -1, 4, 10030),
    (1035, 'plg_molajo_media', 'plugin', 'media', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_MEDIA_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"AmyStephen@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_MEDIA_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 19, -1, 4, 10035),
    (1040, 'plg_molajo_protect', 'plugin', 'protect', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_PROTECT_NAME","type":"plugin","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"AmyStephen@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_PROTECT_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 20, -1, 4, 10040),
    (1045, 'plg_molajo_responses', 'plugin', 'responses', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_RESPONSES_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"AmyStephen@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_RESPONSES_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 21, -1, 4, 10045),
    (1050, 'plg_molajo_search', 'plugin', 'search', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_SEARCH_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"AmyStephen@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_SEARCH_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 23, -1, 4, 10050),
    (1055, 'plg_molajo_system', 'plugin', 'system', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_SYSTEM_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"AmyStephen@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_SYSTEM_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 24, -1, 4, 10055),
    (1060, 'plg_molajo_urls', 'plugin', 'urls', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_URLS_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"AmyStephen@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_URLS_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 25, -1, 4, 10060),
    (1065, 'plg_molajo_webservices', 'plugin', 'webservices', 'molajo', 1, 1, 0, '{"legacy":false,"name":"PLG_MOLAJO_WEBSERVICES_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"AmyStephen@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_WEBSERVICES_XML_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 26, -1, 4, 10065);

## Search
INSERT INTO `#__extensions` (
  `extension_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (1100, 'plg_search_categories', 'plugin', 'categories', 'search', 1, 1, 0, '', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 27, 0, 4, 11000),
    (1105, 'plg_search_articles', 'plugin', 'articles', 'search', 1, 1, 0, '', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 28, 0, 4, 11005),
    (1110, 'plg_search_media', 'plugin', 'media', 'search', 1, 1, 0, '', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 29, 0, 4, 11100);

## System
INSERT INTO `#__extensions` (
  `extension_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`, `protected`,
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
  `extension_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUES
    (1200, 'plg_query_molajosample', 'plugin', 'molajosample', 'query', 1, 1, 0, '', '{"enable_molajosample_feature":"1"}', '', '', 0, '0000-00-00 00:00:00', 41, 0, 4, 12000);

## Template
INSERT INTO `#__extensions` (
  `extension_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`, `protected`,
  `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `ordering`, `state`, `access`, `asset_id` )
    VALUE
    (1300, 'molajo-construct', 'template', 'molajo-construct', '', 0, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 42, 1, 4, 13000),
    (1305, 'bluestork', 'template', 'bluestork', '', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 43, 1, 4, 13050),
    (1310, 'minima', 'template', 'minima', '', 1, 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 43, 1, 4, 13100);

## Users
INSERT INTO `#__extensions` (
  `extension_id`, `name`, `type`, `element`, `folder`, `application_id`, `enabled`, `protected`,
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

/* 500 MOLAJO_CONFIG_OPTION_ID_LAYOUTS */;
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

/* MIME from ftp://ftp.iana.org/assignments/media-types/ */

/* 1000 MOLAJO_CONFIG_OPTION_ID_AUDIO_MIMES */;
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1000, '', '', 0),
('core', 1000, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 1),
('core', 1000, 'sp-midi', 'sp-midi', 2),
('core', 1000, 'vnd.3gpp.iufp', 'vnd.3gpp.iufp', 3),
('core', 1000, 'vnd.4SB', 'vnd.4SB', 4),
('core', 1000, 'vnd.CELP', 'vnd.CELP', 5),
('core', 1000, 'vnd.audiokoz', 'vnd.audiokoz', 6),
('core', 1000, 'vnd.cisco.nse', 'vnd.cisco.nse', 7),
('core', 1000, 'vnd.cmles.radio-events', 'vnd.cmles.radio-events', 8),
('core', 1000, 'vnd.cns.anp1', 'vnd.cns.anp1', 9),
('core', 1000, 'vnd.cns.inf1', 'vnd.cns.inf1', 10),
('core', 1000, 'vnd.dece.audio', 'vnd.dece.audio', 11),
('core', 1000, 'vnd.digital-winds', 'vnd.digital-winds', 12),
('core', 1000, 'vnd.dlna.adts', 'vnd.dlna.adts', 13),
('core', 1000, 'vnd.dolby.heaac.1', 'vnd.dolby.heaac.1', 14),
('core', 1000, 'vnd.dolby.heaac.2', 'vnd.dolby.heaac.2', 15),
('core', 1000, 'vnd.dolby.mlp', 'vnd.dolby.mlp', 16),
('core', 1000, 'vnd.dolby.mps', 'vnd.dolby.mps', 17),
('core', 1000, 'vnd.dolby.pl2', 'vnd.dolby.pl2', 18),
('core', 1000, 'vnd.dolby.pl2x', 'vnd.dolby.pl2x', 19),
('core', 1000, 'vnd.dolby.pl2z', 'vnd.dolby.pl2z', 20),
('core', 1000, 'vnd.dolby.pulse.1', 'vnd.dolby.pulse.1', 21),
('core', 1000, 'vnd.dra', 'vnd.dra', 22),
('core', 1000, 'vnd.dts', 'vnd.dts', 23),
('core', 1000, 'vnd.dts.hd', 'vnd.dts.hd', 24),
('core', 1000, 'vnd.dvb.file', 'vnd.dvb.file', 25),
('core', 1000, 'vnd.everad.plj', 'vnd.everad.plj', 26),
('core', 1000, 'vnd.hns.audio', 'vnd.hns.audio', 27),
('core', 1000, 'vnd.lucent.voice', 'vnd.lucent.voice', 28),
('core', 1000, 'vnd.ms-playready.media.pya', 'vnd.ms-playready.media.pya', 29),
('core', 1000, 'vnd.nokia.mobile-xmf', 'vnd.nokia.mobile-xmf', 30),
('core', 1000, 'vnd.nortel.vbk', 'vnd.nortel.vbk', 31),
('core', 1000, 'vnd.nuera.ecelp4800', 'vnd.nuera.ecelp4800', 32),
('core', 1000, 'vnd.nuera.ecelp7470', 'vnd.nuera.ecelp7470', 33),
('core', 1000, 'vnd.nuera.ecelp9600', 'vnd.nuera.ecelp9600', 34),
('core', 1000, 'vnd.octel.sbc', 'vnd.octel.sbc', 35),
('core', 1000, 'vnd.qcelp', 'vnd.qcelp', 36),
('core', 1000, 'vnd.rhetorex.32kadpcm', 'vnd.rhetorex.32kadpcm', 37),
('core', 1000, 'vnd.rip', 'vnd.rip', 38),
('core', 1000, 'vnd.sealedmedia.softseal-mpeg', 'vnd.sealedmedia.softseal-mpeg', 39),
('core', 1000, 'vnd.vmx.cvsd', 'vnd.vmx.cvsd', 40);

/* 1010 MOLAJO_CONFIG_OPTION_ID_IMAGE_MIMES */;
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1010, '', '', 0),
('core', 1010, 'cgm', 'cgm', 1),
('core', 1010, 'jp2', 'jp2', 2),
('core', 1010, 'jpm', 'jpm', 3),
('core', 1010, 'jpx', 'jpx', 4),
('core', 1010, 'naplps', 'naplps', 5),
('core', 1010, 'png', 'png', 6),
('core', 1010, 'prs.btif', 'prs.btif', 7),
('core', 1010, 'prs.pti', 'prs.pti', 8),
('core', 1010, 'vnd-djvu', 'vnd-djvu', 9),
('core', 1010, 'vnd-svf', 'vnd-svf', 10),
('core', 1010, 'vnd-wap-wbmp', 'vnd-wap-wbmp', 11),
('core', 1010, 'vnd.adobe.photoshop', 'vnd.adobe.photoshop', 12),
('core', 1010, 'vnd.cns.inf2', 'vnd.cns.inf2', 13),
('core', 1010, 'vnd.dece.graphic', 'vnd.dece.graphic', 14),
('core', 1010, 'vnd.dvb.subtitle', 'vnd.dvb.subtitle', 15),
('core', 1010, 'vnd.dwg', 'vnd.dwg', 16),
('core', 1010, 'vnd.dxf', 'vnd.dxf', 17),
('core', 1010, 'vnd.fastbidsheet', 'vnd.fastbidsheet', 18),
('core', 1010, 'vnd.fpx', 'vnd.fpx', 19),
('core', 1010, 'vnd.fst', 'vnd.fst', 20),
('core', 1010, 'vnd.fujixerox.edmics-mmr', 'vnd.fujixerox.edmics-mmr', 21),
('core', 1010, 'vnd.fujixerox.edmics-rlc', 'vnd.fujixerox.edmics-rlc', 22),
('core', 1010, 'vnd.globalgraphics.pgb', 'vnd.globalgraphics.pgb', 23),
('core', 1010, 'vnd.microsoft.icon', 'vnd.microsoft.icon', 24),
('core', 1010, 'vnd.mix', 'vnd.mix', 25),
('core', 1010, 'vnd.ms-modi', 'vnd.ms-modi', 26),
('core', 1010, 'vnd.net-fpx', 'vnd.net-fpx', 27),
('core', 1010, 'vnd.radiance', 'vnd.radiance', 28),
('core', 1010, 'vnd.sealed-png', 'vnd.sealed-png', 29),
('core', 1010, 'vnd.sealedmedia.softseal-gif', 'vnd.sealedmedia.softseal-gif', 30),
('core', 1010, 'vnd.sealedmedia.softseal-jpg', 'vnd.sealedmedia.softseal-jpg', 31),
('core', 1010, 'vnd.xiff', 'vnd.xiff', 32);

/* 1020 MOLAJO_CONFIG_OPTION_ID_TEXT_MIMES */;
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1020, '', '', 0),
('core', 1020, 'n3', 'n3', 1),
('core', 1020, 'prs.fallenstein.rst', 'prs.fallenstein.rst', 2),
('core', 1020, 'prs.lines.tag', 'prs.lines.tag', 3),
('core', 1020, 'rtf', 'rtf', 4),
('core', 1020, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 5),
('core', 1020, 'tab-separated-values', 'tab-separated-values', 6),
('core', 1020, 'turtle', 'turtle', 7),
('core', 1020, 'vnd-curl', 'vnd-curl', 8),
('core', 1020, 'vnd.DMClientScript', 'vnd.DMClientScript', 9),
('core', 1020, 'vnd.IPTC.NITF', 'vnd.IPTC.NITF', 10),
('core', 1020, 'vnd.IPTC.NewsML', 'vnd.IPTC.NewsML', 11),
('core', 1020, 'vnd.abc', 'vnd.abc', 12),
('core', 1020, 'vnd.curl', 'vnd.curl', 13),
('core', 1020, 'vnd.dvb.subtitle', 'vnd.dvb.subtitle', 14),
('core', 1020, 'vnd.esmertec.theme-descriptor', 'vnd.esmertec.theme-descriptor', 15),
('core', 1020, 'vnd.fly', 'vnd.fly', 16),
('core', 1020, 'vnd.fmi.flexstor', 'vnd.fmi.flexstor', 17),
('core', 1020, 'vnd.graphviz', 'vnd.graphviz', 18),
('core', 1020, 'vnd.in3d.3dml', 'vnd.in3d.3dml', 19),
('core', 1020, 'vnd.in3d.spot', 'vnd.in3d.spot', 20),
('core', 1020, 'vnd.latex-z', 'vnd.latex-z', 21),
('core', 1020, 'vnd.motorola.reflex', 'vnd.motorola.reflex', 22),
('core', 1020, 'vnd.ms-mediapackage', 'vnd.ms-mediapackage', 23),
('core', 1020, 'vnd.net2phone.commcenter.command', 'vnd.net2phone.commcenter.command', 24),
('core', 1020, 'vnd.si.uricatalogue', 'vnd.si.uricatalogue', 25),
('core', 1020, 'vnd.sun.j2me.app-descriptor', 'vnd.sun.j2me.app-descriptor', 26),
('core', 1020, 'vnd.trolltech.linguist', 'vnd.trolltech.linguist', 27),
('core', 1020, 'vnd.wap-wml', 'vnd.wap-wml', 28),
('core', 1020, 'vnd.wap.si', 'vnd.wap.si', 29),
('core', 1020, 'vnd.wap.wmlscript', 'vnd.wap.wmlscript', 30);

/* 1030 MOLAJO_CONFIG_OPTION_ID_VIDEO_MIMES */;
INSERT INTO `#__configuration` (`content_table`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1030, '', '', 0),
('core', 1030, 'jpm', 'jpm', 1),
('core', 1030, 'mj2', 'mj2', 2),
('core', 1030, 'quicktime', 'quicktime', 3),
('core', 1030, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 4),
('core', 1030, 'vnd-mpegurl', 'vnd-mpegurl', 5),
('core', 1030, 'vnd-vivo', 'vnd-vivo', 6),
('core', 1030, 'vnd.CCTV', 'vnd.CCTV', 7),
('core', 1030, 'vnd.dece-mp4', 'vnd.dece-mp4', 8),
('core', 1030, 'vnd.dece.hd', 'vnd.dece.hd', 9),
('core', 1030, 'vnd.dece.mobile', 'vnd.dece.mobile', 10),
('core', 1030, 'vnd.dece.pd', 'vnd.dece.pd', 11),
('core', 1030, 'vnd.dece.sd', 'vnd.dece.sd', 12),
('core', 1030, 'vnd.dece.video', 'vnd.dece.video', 13),
('core', 1030, 'vnd.directv-mpeg', 'vnd.directv-mpeg', 14),
('core', 1030, 'vnd.directv.mpeg-tts', 'vnd.directv.mpeg-tts', 15),
('core', 1030, 'vnd.dvb.file', 'vnd.dvb.file', 16),
('core', 1030, 'vnd.fvt', 'vnd.fvt', 17),
('core', 1030, 'vnd.hns.video', 'vnd.hns.video', 18),
('core', 1030, 'vnd.iptvforum.1dparityfec-1010', 'vnd.iptvforum.1dparityfec-1010', 19),
('core', 1030, 'vnd.iptvforum.1dparityfec-2005', 'vnd.iptvforum.1dparityfec-2005', 20),
('core', 1030, 'vnd.iptvforum.2dparityfec-1010', 'vnd.iptvforum.2dparityfec-1010', 21),
('core', 1030, 'vnd.iptvforum.2dparityfec-2005', 'vnd.iptvforum.2dparityfec-2005', 22),
('core', 1030, 'vnd.iptvforum.ttsavc', 'vnd.iptvforum.ttsavc', 23),
('core', 1030, 'vnd.iptvforum.ttsmpeg2', 'vnd.iptvforum.ttsmpeg2', 24),
('core', 1030, 'vnd.motorola.video', 'vnd.motorola.video', 25),
('core', 1030, 'vnd.motorola.videop', 'vnd.motorola.videop', 26),
('core', 1030, 'vnd.mpegurl', 'vnd.mpegurl', 27),
('core', 1030, 'vnd.ms-playready.media.pyv', 'vnd.ms-playready.media.pyv', 28),
('core', 1030, 'vnd.nokia.interleaved-multimedia', 'vnd.nokia.interleaved-multimedia', 29),
('core', 1030, 'vnd.nokia.videovoip', 'vnd.nokia.videovoip', 30),
('core', 1030, 'vnd.objectvideo', 'vnd.objectvideo', 31),
('core', 1030, 'vnd.sealed-swf', 'vnd.sealed-swf', 32),
('core', 1030, 'vnd.sealed.mpeg1', 'vnd.sealed.mpeg1', 33),
('core', 1030, 'vnd.sealed.mpeg4', 'vnd.sealed.mpeg4', 34),
('core', 1030, 'vnd.sealed.swf', 'vnd.sealed.swf', 35),
('core', 1030, 'vnd.sealedmedia.softseal-mov', 'vnd.sealedmedia.softseal-mov', 36),
('core', 1030, 'vnd.uvvu.mp4', 'vnd.uvvu.mp4', 37);

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
INSERT INTO `#__menu` VALUES (1, '', 'Menu_Item_Root', 'root', '', '', '', '', 1, 0, 0, 0, 1, 0, '0000-00-00 00:00:00', 0, 121, 5, '', 0, '', 0, 49, 0, '*', 1);

INSERT INTO `#__menu` VALUES (2, 'menu', 'com_messages', 'Messaging', '', 'Messaging', 'index.php?option=com_messages', 'component', 1, 1, 1, 13, 2, 0, '0000-00-00 00:00:00', 0, 122, 5, 'class:messages', 0, '', 17, 22, 0, '*', 1);
INSERT INTO `#__menu` VALUES (3, 'menu', 'com_messages_add', 'New Private Message', '', 'list/New Private Message', 'index.php?option=com_messages&task=message.add', 'component', 1, 2, 2, 13, 3, 0, '0000-00-00 00:00:00', 0, 123, 5, 'class:messages-add', 0, '', 46, 49, 0, '*', 1);
INSERT INTO `#__menu` VALUES (4, 'menu', 'com_messages_read', 'Read Private Message', '', 'list/Read Private Message', 'index.php?option=com_messages', 'component', 1, 2, 2, 13, 4, 0, '0000-00-00 00:00:00', 0, 124, 5, 'class:messages-read', 0, '', 50, 51, 0, '*', 1);
INSERT INTO `#__menu` VALUES (5, 'menu', 'com_redirect', 'Redirect', '', 'Redirect', 'index.php?option=com_redirect', 'component', 1, 1, 1, 16, 5, 0, '0000-00-00 00:00:00', 0, 125, 5, 'class:redirect', 0, '', 37, 38, 0, '*', 1);
INSERT INTO `#__menu` VALUES (6, 'menu', 'com_search', 'Search', '', 'Search', 'index.php?option=com_search', 'component', 1, 1, 1, 17, 6, 0, '0000-00-00 00:00:00', 0, 126, 5, 'class:search', 0, '', 29, 30, 0, '*', 1);

# Client
INSERT INTO `#__menu` VALUES (7, 'mainmenu', 'Home', 'home', '', 'home', 'index.php?option=com_users&view=login', 'component', 1, 1, 1, 19, 1, 0, '0000-00-00 00:00:00', 0, 127, 1, '', 0, '{"login_redirect_url":"","logindescription_show":"1","login_description":"","login_image":"","logout_redirect_url":"","logoutdescription_show":"1","logout_description":"","logout_image":"","menu-anchor_title":"","menu-anchor_css":"","menu_image":"","menu_text":1,"page_title":"","show_page_heading":0,"page_heading":"","pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0}', 39, 40, 1, '*', 0);
INSERT INTO `#__menu` VALUES (8, 'mainmenu', 'Edit Article', 'edit', '', 'edit', 'index.php?option=com_articles&view=article&layout=edit', 'component', 1, 1, 1, 2, 2, 0, '0000-00-00 00:00:00', 0, 128, 2, '', 0, '{"menu-anchor_title":"","menu-anchor_css":"","menu_image":"","menu_text":1,"page_title":"","show_page_heading":0,"page_heading":"","pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0}', 41, 42, 0, '*', 0);
INSERT INTO `#__menu` VALUES (9, 'mainmenu', 'Display Article', 'item', '', 'item', 'index.php?option=com_articles&view=articles&layout=item&id=5', 'component', 1, 1, 1, 2, 2, 0, '0000-00-00 00:00:00', 0, 129, 1, '', 0, '{"menu-anchor_title":"","menu-anchor_css":"","menu_image":"","menu_text":1,"page_title":"","show_page_heading":0,"page_heading":"","pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0}', 41, 42, 0, '*', 0);
INSERT INTO `#__menu` VALUES (10, 'mainmenu', 'Article Blog', 'items', '', 'items', 'index.php?option=com_articles&view=articles&layout=items&catid=2', 'component', 1, 1, 1, 2, 3, 0, '0000-00-00 00:00:00', 0, 130, 1, '', 0, '{"menu-anchor_title":"","menu-anchor_css":"","menu_image":"","menu_text":1,"page_title":"","show_page_heading":0,"page_heading":"","pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0}', 43, 44, 0, '*', 0);
INSERT INTO `#__menu` VALUES (11, 'mainmenu', 'Article List', 'list', '', 'list', 'index.php?option=com_articles&view=articles&catid=2', 'component', 1, 1, 1, 2, 4, 0, '0000-00-00 00:00:00', 0, 131, 1, '', 0, '{"menu-anchor_title":"","menu-anchor_css":"","menu_image":"","menu_text":1,"page_title":"","show_page_heading":0,"page_heading":"","pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0}', 45, 52, 0, '*', 0);
INSERT INTO `#__menu` VALUES (12, 'mainmenu', 'Article Table', 'table', '', 'table', 'index.php?option=com_articles&view=articles&layout=table&catid=2', 'component', 1, 1, 1, 5, 5, 0, '0000-00-00 00:00:00', 0, 132, 1, '', 0, '{"menu-anchor_title":"","menu-anchor_css":"","menu_image":"","menu_text":1,"page_title":"","show_page_heading":0,"page_heading":"","pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0}', 47, 48, 0, '*', 0);

#
# MODULES
#

# admin modules
INSERT INTO `#__modules` (`id`, `title`,  `note`, `content`, `ordering`, `position`, `checked_out`,
  `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`,`showtitle`, `params`,
  `application_id`, `language`, `access`,  `asset_id`)
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
  `application_id`, `language`, `access`,  `asset_id`)
    VALUES
    (15, 'Main Menu', '', '', 1, 'nav', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_menu', 1, '{"menutype":"mainmenu","startLevel":"1","endLevel":"0","showAllChildren":"0","tag_id":"","class_sfx":"","window_open":"","layout":"_:DEFAULT","moduleclass_sfx":"_menu","cache":"1","cache_time":"900","cachemode":"itemid"}', 0, '*', 5, 5070),
    (16, 'Login Form', '', '', 7, 'content-above-1', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_login', 1, '{"greeting":"1","name":"0"}', 0, '*', 5, 5085),
    (17, 'Breadcrumbs', '', '', 1, 'breadcrumbs', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_breadcrumbs', 1, '{"moduleclass_sfx":"","showHome":"1","homeText":"Home","showComponent":"1","separator":"","cache":"1","cache_time":"900","cachemode":"itemid"}', 0, '*', 5, 5080);


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
# TEMPLATES
#
INSERT INTO `#__template_styles` VALUES (1, 'molajo-construct', '0', '0', 'Molajo Construct', '{}');
INSERT INTO `#__template_styles` VALUES (2, 'Blank Slate', '0', '1', 'Molajo Blankslate - DEFAULT', '{}');
INSERT INTO `#__template_styles` VALUES (3, 'bluestork', '1', '1', 'Bluestork - DEFAULT', '{"useRoundedCorners":"1","showSiteName":"0"}');
INSERT INTO `#__template_styles` VALUES (4, 'minima', '1', '0', 'Minima', '{}');

#
# UPDATES
#
INSERT INTO `#__update_sites` VALUES
(1, 'Molajo Core', 'collection', 'http://update.molajo.org/core/list.xml', 1),
(2, 'Molajo Directory', 'collection', 'http://update.molajo.org/directory/list.xml', 1);

INSERT INTO `#__update_sites_extensions` VALUES (1, 700), (2, 700);

#
# Actions
#
INSERT INTO `#__actions` (`id` ,`title`)
  VALUES (1, 'login'),
        (2, 'create'),
        (3, 'view'),
        (4, 'edit'),
        (5, 'delete'),
        (6, 'admin');
