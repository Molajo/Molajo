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
  `title` VARCHAR(255) NOT NULL DEFAULT ' ',
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
  `content_table` VARCHAR(255) NOT NULL DEFAULT ' ',
  `content_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Content Primary Key',
  `option` VARCHAR(255) NOT NULL DEFAULT ' ',
  `path` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'URL',
  `link` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'The actually link the menu item refers to.',
  `access` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the amy_groupings table',
  PRIMARY KEY (`id`) )
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

#
# Applications
#

#
# Table structure for table `amy_applications`
#
CREATE TABLE `amy_applications` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Application Primary Key',
  `application_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Numeric value associated with the application',
  `name` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `path` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'URL Alias',
  `description` mediumtext NOT NULL,
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the amy_assets table.',
  `metakey` text COMMENT 'Meta Key',
  `metadesc` text COMMENT 'Meta Description',
  `metadata` text COMMENT 'Meta Data',
  `attribs` text COMMENT 'Attributes (Custom Fields)',
  `params` MEDIUMTEXT COMMENT 'Configurable Parameter Values',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

#
# USERS AND GROUPS
#

#
# Table structure for table `amy_users`
#

CREATE TABLE IF NOT EXISTS `amy_users` (
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
  `title` VARCHAR(255) NOT NULL DEFAULT '  ',
  `subtitle` VARCHAR(255) NOT NULL DEFAULT ' ',
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
# Table structure for table `amy_articles`
#

CREATE TABLE IF NOT EXISTS `amy_articles` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `catid` INT (11) UNSIGNED NOT NULL DEFAULT  0 COMMENT 'Category ID associated with the Primary Key',

  `title` VARCHAR (255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `subtitle` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Subtitle',
  `alias` VARCHAR (255) NOT NULL DEFAULT ' ' COMMENT 'URL Alias',

  `content_type` tinyint(3) NOT NULL DEFAULT 0 COMMENT 'Content Type: Links to amy_configuration.option_id = 10 and component_option values matching ',

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
  INDEX `element_folder_application_id`(`element`, `folder`),
  INDEX `extension`(`type`,`element`,`folder`)
) AUTO_INCREMENT=1 CHARACTER SET utf8;

#
# MENUS
#

#
# Table structure for table `amy_menus`
#

CREATE TABLE `amy_menus` (
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
# Table structure for table `amy_menu_items`
#

CREATE TABLE `amy_menu_items` (
  `id` INT (11) NOT NULL AUTO_INCREMENT,
  `menu_id` INT (11) NOT NULL DEFAULT 0 COMMENT 'The type of menu this item belongs to. FK to amy_menus.menu_id',
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'The display title of the menu item.',
  `alias` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'The SEF alias of the menu item.',
  `note` VARCHAR(255) NOT NULL DEFAULT ' ',
  `path` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'URL',
  `link` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'The actually link the menu item refers to.',
  `type` VARCHAR(16) NOT NULL DEFAULT ' ' COMMENT 'The type of link: Component, URL, Alias, Separator',
  `published` INT (11) NOT NULL DEFAULT 0 COMMENT 'The published state of the menu link.',
  `parent_id` INT (11) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'The parent menu item in the menu tree.',
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
  `home` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Indicates if this menu item is the home page.',
  `language` CHAR(7) NOT NULL DEFAULT ' ',
  `application_id` INT (11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_application_id_parent_id_alias` (`application_id`, `menu_id`, `parent_id`, `alias`),
  KEY `idx_menu_id` (`menu_id`, `ordering`),
  KEY `idx_left_right` (`lft`, `rgt`),
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
  PRIMARY KEY  (`module_id`, `menu_item_id`)
) DEFAULT CHARSET=utf8;

#
# Table structure for table `amy_modules`
#

CREATE TABLE `amy_modules` (
  `id` INT (11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL DEFAULT ' ',
  `subtitle` VARCHAR(255) NOT NULL DEFAULT ' ',
  `note` VARCHAR(255) NOT NULL DEFAULT ' ',
  `content` MEDIUMTEXT NOT NULL,
  `ordering` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Ordering',
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the amy_assets table.',
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
# TEMPLATES
#

#
# Table structure for table `amy_templates`
#

CREATE TABLE IF NOT EXISTS `amy_templates` (
  `id` INT (11) UNSIGNED NOT NULL auto_increment COMMENT 'Primary Key',
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
# Table structure for table `amy_template_styles`
#

CREATE TABLE IF NOT EXISTS `amy_template_styles` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_id` INT (11) UNSIGNED NOT NULL COMMENT 'Foreign Key to Template Table',
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Template Title',
  `description` MEDIUMTEXT COMMENT 'Template Description',
  `application_id` INT (11) UNSIGNED NOT NULL COMMENT 'Foreign Key to Application Table',
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
  `params` MEDIUMTEXT COMMENT 'Configurable Parameter Values',
  PRIMARY KEY  (`id`),
  KEY `idx_template` (`template_id`, `id`),
  KEY `idx_default` (`application_id`, `default`)
)  DEFAULT CHARSET=utf8 ;

#
# SYSTEM CONFIGURATION
#

#
# Table structure for table `amy_session`
#

CREATE TABLE `amy_session` (
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
# Table structure for table `amy_configuration`
#

CREATE TABLE IF NOT EXISTS `amy_configuration` (
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
# Table structure for table `amy_updates`
#

CREATE TABLE  `amy_updates` (
  `id` INT (11) NOT NULL auto_increment,
  `update_site_id` INT (11) DEFAULT 0,
  `extension_id` INT (11) DEFAULT 0,
  `name` VARCHAR(100) DEFAULT ' ',
  `description` text NOT NULL,
  `element` VARCHAR(100) DEFAULT ' ',
  `type` VARCHAR(20) DEFAULT ' ',
  `folder` VARCHAR(20) DEFAULT ' ',
  `version` VARCHAR(10) DEFAULT ' ',
  `data` text NOT NULL,
  `details_url` text NOT NULL,
  PRIMARY KEY  (`id`)
)  DEFAULT CHARSET=utf8;

#
# Table structure for table `amy_update_sites`
#

CREATE TABLE  `amy_update_sites` (
  `update_site_id` INT (11) NOT NULL auto_increment,
  `name` VARCHAR(100) DEFAULT ' ',
  `type` VARCHAR(20) DEFAULT ' ',
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
# Actions
#
INSERT INTO `amy_actions` (`id` ,`title`)
  VALUES
    (1, 'login'),
    (2, 'create'),
    (3, 'view'),
    (4, 'edit'),
    (5, 'publish'),
    (6, 'delete'),
    (7, 'admin');

#
# APPLICATIONS
#

INSERT INTO `amy_applications` (`id`, `asset_id`, `application_id`, `name`, `path`)
  VALUES
    (1, 0, 1, 'site', ''),
    (2, 0, 2, 'administrator', 'administrator'),
    (3, 0, 3, 'content', 'content');

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `id`, '__applications', '', `path`, '', 1
    FROM amy_applications

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 1 AND `content_table` = '__applications');
UPDATE `amy_applications` SET asset_id = @asset_id WHERE id = 1;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2 AND `content_table` = '__applications');
UPDATE `amy_applications` SET asset_id = @asset_id WHERE id = 2;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 3 AND `content_table` = '__applications');
UPDATE `amy_applications` SET asset_id = @asset_id WHERE id = 3;

#
# USERS AND GROUPS
#  5,6 asset id reserved for administrator

INSERT INTO `amy_groups`
  (`id`, `asset_id`, `parent_id`, `lft`, `rgt`, `title`, `protected`)
    VALUES
      (1, 0, 0, 0, 1, 'Public',        1),
      (2, 0, 0, 2, 3, 'Guest',         1),
      (3, 0, 0, 4, 5, 'Registered',    1),
      (4, 0, 0, 6, 7, 'Administrator', 1);

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `id`, '__groups', '', `title`, '', 4
    FROM amy_groups;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 1 AND `content_table` = '__groups');
UPDATE `amy_groups` SET asset_id = @asset_id WHERE id = 1;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2 AND `content_table` = '__groups');
UPDATE `amy_groups` SET asset_id = @asset_id WHERE id = 2;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 3 AND `content_table` = '__groups');
UPDATE `amy_groups` SET asset_id = @asset_id WHERE id = 3;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 4 AND `content_table` = '__groups');
UPDATE `amy_groups` SET asset_id = @asset_id WHERE id = 4;

INSERT INTO `amy_groupings`
  (`id`, `group_name_list`, `group_id_list` )
    VALUES
      (1, 'Public', '1'),
      (2, 'Guest', '2'),
      (3, 'Registered', '3'),
      (4, 'Administrator', '4'),
      (5, 'Registered, Administrator', '4,5');

INSERT INTO `amy_group_to_groupings`
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

# Components

INSERT INTO `amy_extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`,
  `checked_out`, `checked_out_time`, `state`, `ordering`)
  VALUES
    (1, 0, 'com_admin', 'component', 'com_admin', '', 1, 1, '', '{}', '{}', '{}', 0, '0000-00-00 00:00:00', 1, 1),
    (2, 0, 'com_articles', 'component', 'com_articles', '', 1, 0, '', '{}', '{}', '{}', 0, '0000-00-00 00:00:00', 1, 2),
    (3, 0, 'com_cache', 'component', 'com_cache', '', 1, 1, '', '{}', '{}', '{}', 0, '0000-00-00 00:00:00', 1, 3),
    (4, 0, 'com_categories', 'component', 'com_categories', '', 1, 1, '', '{}', '{}', '{}', 0, '0000-00-00 00:00:00', 1, 4),
    (5, 0, 'com_config', 'component', 'com_config',  '', 1, 1, '', '{}', '{}', '{}', 0, '0000-00-00 00:00:00', 1, 5),
    (6, 0, 'com_dashboard', 'component', 'com_dashboard',  '', 1, 1, '', '{}', '{}', '{}', 0, '0000-00-00 00:00:00', 1, 6),
    (7, 0, 'com_extensions', 'component', 'com_extensions',  '', 1, 1, '', '{}', '{}', '{}', 0, '0000-00-00 00:00:00', 1, 7),
    (8, 0, 'com_installer', 'component', 'com_installer',  '', 1, 1, '', '{}', '{}', '{}', 0, '0000-00-00 00:00:00', 1, 8),
    (9, 0, 'com_layouts', 'component', 'com_layouts',  '', 1, 0, '', '{}', '{}', '{}', 0, '0000-00-00 00:00:00', 1, 9),
    (10, 0, 'com_login', 'component', 'com_login',  '', 1, 1, '', '{}', '{}', '{}', 0, '0000-00-00 00:00:00', 1, 10),
    (11, 0, 'com_media', 'component', 'com_media', '', 1, 1, '', '{}', '{}', '{}', 0, '0000-00-00 00:00:00', 1, 11),
    (12, 0, 'com_menus', 'component', 'com_menus',  '', 1, 1, '', '{}', '{}', '{}', 0, '0000-00-00 00:00:00', 1, 12),
    (13, 0, 'com_modules', 'component', 'com_modules', '', 1, 1, '', '{}', '{}', '{}', 0, '0000-00-00 00:00:00', 1, 13),
    (14, 0, 'com_plugins', 'component', 'com_plugins',  '', 1, 1, '', '{}', '{}', '{}', 0, '0000-00-00 00:00:00', 1, 14),
    (15, 0, 'com_redirect', 'component', 'com_redirect', '', 1, 1, '', '{}', '{}', '{}', 0, '0000-00-00 00:00:00', 1, 15),
    (16, 0, 'com_search', 'component', 'com_search',  '', 1, 1, '', '{}', '{}', '{}', 0, '0000-00-00 00:00:00', 1, 16),
    (17, 0, 'com_templates', 'component', 'com_templates', '', 1, 1, '', '{}', '{}', '{}', 0, '0000-00-00 00:00:00', 1, 17),
    (18, 0, 'com_users', 'component', 'com_users',  '', 1, 1, '', '{}', '{}', '{}', 0, '0000-00-00 00:00:00', 1, 18);

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `extension_id`, '__extensions', '', `name`, CONCAT('index.php?option=com_extensions&id=', `extension_id`), 5
    FROM amy_extensions
    WHERE `type` = 'component'

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 1 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 1;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 3 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 3;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 4 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 4;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 5 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 5;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 6 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 6;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 7 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 7;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 8 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 8;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 9 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 9;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 10 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 10;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 11 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 11;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 12 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 12;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 13 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 13;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 14 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 14;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 15 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 15;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 16 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 16;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 17 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 17;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 18 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 18;

# Languages

INSERT INTO `amy_extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`,
  `checked_out`, `checked_out_time`, `state`, `ordering`)
  VALUES
    (30, 0, 'English (UK)', 'language', 'language', '', 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 30),
    (31, 0, 'English (US)', 'language', 'language', '', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 31);

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `extension_id`, '__extensions', '', `name`, CONCAT('index.php?option=com_extensions&id=', `extension_id`), 5
    FROM amy_extensions
    WHERE `type` = 'language'

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 30 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 30;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 31 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 31;

# Layouts: Document

INSERT INTO `amy_extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`,
  `checked_out`, `checked_out_time`, `state`, `ordering`)
  VALUES
    (40, 0, 'head', 'layout', 'layout', 'document', 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (41, 0, 'messages', 'layout', 'layout', 'document', 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 2),
    (42, 0, 'errors', 'layout', 'layout', 'document', 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 3),
    (43, 0, 'atom', 'layout', 'layout', 'document', 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 4),
    (44, 0, 'rss', 'layout', 'layout', 'document', 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 5);

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `extension_id`, '__extensions', '', `name`, CONCAT('index.php?option=com_extensions&id=', `extension_id`), 5
    FROM amy_extensions
    WHERE `type` = 'layout'
      AND `folder` = 'document'

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 40 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 40;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 41 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 41;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 42 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 42;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 43 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 43;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 44 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 44;

# Layouts: Extensions

INSERT INTO `amy_extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`,
  `checked_out`, `checked_out_time`, `state`, `ordering`)
  VALUES
    (200, 0, 'admin_acl_panel', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (205, 0, 'admin_activity', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 2),
    (210, 0, 'admin_edit', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 3),
    (215, 0, 'admin_favorites', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 4),
    (220, 0, 'admin_feed', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 5),
    (225, 0, 'admin_footer', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 6),
    (230, 0, 'admin_header', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 7),
    (235, 0, 'admin_inbox', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 8),
    (240, 0, 'admin_launchpad', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 9),
    (245, 0, 'admin_list', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 10),
    (250, 0, 'admin_login', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 11),
    (255, 0, 'admin_modal', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 12),
    (260, 0, 'admin_pagination', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 13),
    (265, 0, 'admin_toolbar', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 14),
    (270, 0, 'audio', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 15),
    (275, 0, 'contact_form', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 16),
    (280, 0, 'default', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 17),
    (285, 0, 'dummy', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 18),
    (290, 0, 'faq', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 19),
    (295, 0, 'item', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 20),
    (300, 0, 'items', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 21),
    (305, 0, 'list', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 22),
    (310, 0, 'pagination', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 23),
    (315, 0, 'social_bookmarks', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 24),
    (320, 0, 'syntaxhighlighter', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 25),
    (325, 0, 'table', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 26),
    (330, 0, 'tree', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 27),
    (335, 0, 'twig_example', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 28),
    (340, 0, 'video', 'layout', 'layout', 'extension', 1, 0, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 29);

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `extension_id`, '__extensions', '', `name`, CONCAT('index.php?option=com_extensions&id=', `extension_id`), 5
    FROM amy_extensions
    WHERE `type` = 'layout'
      AND `folder` = 'extension'

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 200 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 200;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 205 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 205;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 210 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 210;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 215 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 215;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 220 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 220;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 225 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 225;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 230 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 230;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 235 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 235;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 240 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 240;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 245 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 245;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 250 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 250;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 255 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 255;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 260 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 260;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 265 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 265;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 270 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 270;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 275 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 275;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 280 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 280;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 285 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 285;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 290 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 290;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 295 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 295;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 300 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 300;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 305 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 305;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 310 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 310;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 315 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 315;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 320 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 320;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 325 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 325;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 330 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 330;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 335 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 335;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 340 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 340;

# Layouts: Formfields

INSERT INTO `amy_extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`,
  `checked_out`, `checked_out_time`, `state`, `ordering`)
  VALUES
    (500, 0, 'button', 'layout', 'layout', 'formfield', 1, 1, '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (505, 0, 'colorpicker', 'layout', 'layout', 'formfield', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (510, 0, 'datepicker', 'layout', 'layout', 'formfield', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (515, 0, 'list', 'layout', 'layout', 'formfield', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (520, 0, 'media', 'layout', 'layout', 'formfield', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (525, 0, 'number', 'layout', 'layout', 'formfield', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (530, 0, 'option', 'layout', 'layout', 'formfield', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (535, 0, 'rules', 'layout', 'layout', 'formfield', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (540, 0, 'spacer', 'layout', 'layout', 'formfield', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (545, 0, 'text', 'layout', 'layout', 'formfield', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (550, 0, 'textarea', 'layout', 'layout', 'formfield', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (555, 0, 'user', 'layout', 'layout', 'formfield', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1);

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `extension_id`, '__extensions', '', `name`, CONCAT('index.php?option=com_extensions&id=', `extension_id`), 5
    FROM amy_extensions
    WHERE `type` = 'layout'
      AND `folder` = 'formfield'

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 500 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 500;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 505 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 505;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 510 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 510;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 515 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 515;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 520 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 520;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 525 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 525;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 530 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 530;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 535 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 535;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 540 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 540;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 545 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 545;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 550 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 550;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 555 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 555;

# Layouts: Wraps

INSERT INTO `amy_extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`,
  `checked_out`, `checked_out_time`, `state`, `ordering`)
  VALUES
    (600, 0, 'article', 'layout', 'layout', 'wrap', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (605, 0, 'aside', 'layout', 'layout', 'wrap', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 2),
    (610, 0, 'div', 'layout', 'layout', 'wrap', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 3),
    (615, 0, 'footer', 'layout', 'layout', 'wrap', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 4),
    (620, 0, 'footer', 'layout', 'layout', 'wrap', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 5),
    (625, 0, 'horizontal', 'layout', 'layout', 'wrap', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 6),
    (630, 0, 'nav', 'layout', 'layout', 'wrap', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 7),
    (635, 0, 'none', 'layout', 'layout', 'wrap', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 8),
    (640, 0, 'outline', 'layout', 'layout', 'wrap', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 9),
    (645, 0, 'section', 'layout', 'layout', 'wrap', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 10),
    (650, 0, 'table', 'layout', 'layout', 'wrap', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 11),
    (655, 0, 'tabs', 'layout', 'layout', 'wrap', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 12);

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `extension_id`, '__extensions', '', `name`, CONCAT('index.php?option=com_extensions&id=', `extension_id`), 5
    FROM amy_extensions
    WHERE `type` = 'layout'
      AND `folder` = 'wrap'

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 600 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 600;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 605 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 605;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 610 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 610;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 615 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 615;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 620 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 620;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 625 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 625;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 630 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 630;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 635 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 635;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 640 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 640;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 645 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 645;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 650 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 650;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 655 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 655;

# Libraries

INSERT INTO `amy_extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`,
  `checked_out`, `checked_out_time`, `state`, `ordering`)
  VALUES
    (660, 0, 'Akismet', 'library', 'akismet', 'akismet', 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (663, 0, 'Doctrine', 'library', 'doctrine', 'doctrine', 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 2),
    (667, 0, 'Forms', 'library', 'forms', 'forms', 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 3),
    (670, 0, 'Includes', 'library', 'includes', 'includes', 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 4),
    (672, 0, 'Joomla Platform', 'library', 'jplatform', 'jplatform', 1, 1, '{"legacy":false,"name":"Molajo Web Application Framework","type":"library","creationDate":"2008","author":"Joomla","copyright":"Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.","authorEmail":"admin@joomla.org","authorUrl":"http:\\/\\/www.joomla.org","version":"1.6.0","description":"The Molajo Web Application Framework","group":""}', '{}', '', '', 0, '0000-00-00 00:00:00', 3, 5),
    (674, 0, 'Molajo Application', 'library', 'molajo', 'molajo', 1, 1, '{"name":"Molajo Application","type":"library","creationDate":"2011","author":"Molajo Project Team","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved.","authorEmail":"collaborate@molajo.org","authorUrl":"http:\\/\\/molajo.org","version":"1.0.0","description":"Molajo is a web development environment useful for crafting custom solutions from simple to complex custom data architecture, presentation output, and access control.","group":""}\r\n', '', '', '', 0, '0000-00-00 00:00:00', 4, 6),
    (678, 0, 'Mollom', 'library', 'mollom', 'mollom', 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 5, 7),
    (680, 0, 'Recaptcha', 'library', 'recaptcha', 'recaptcha', 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 8),
    (682, 0, 'Twig', 'library', 'twig', 'twig', 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 9);

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `extension_id`, '__extensions', '', `name`, CONCAT('index.php?option=com_extensions&id=', `extension_id`), 5
    FROM amy_extensions
    WHERE `type` = 'library'

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 660 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 660;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 663 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 663;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 667 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 667;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 670 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 670;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 672 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 672;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 674 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 674;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 678 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 678;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 680 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 680;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 682 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 682;

# Modules

INSERT INTO `amy_extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`,
  `checked_out`, `checked_out_time`, `state`, `ordering`)
  VALUES
    (700, 0, 'mod_breadcrumbs', 'module', 'mod_breadcrumbs', 'mod_breadcrumbs', 0, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (705, 0, 'mod_content', 'module', 'mod_content', 'mod_content', 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 1, 2),
    (710, 0, 'mod_custom', 'module', 'mod_custom', 'mod_custom', 1, 1,'', '', '', '', 0, '0000-00-00 00:00:00', 1, 3),
    (720, 0, 'mod_feed', 'module', 'mod_feed', 'mod_feed', 1, 1,'', '', '', '', 0, '0000-00-00 00:00:00', 1, 4),
    (725, 0, 'mod_footer', 'module', 'mod_footer', 'mod_footer', 1, 1,'', '', '', '', 0, '0000-00-00 00:00:00', 1, 5),
    (730, 0, 'mod_header', 'module', 'mod_header', 'mod_header', 1, 1,'', '', '', '', 0, '0000-00-00 00:00:00', 1, 6),
    (735, 0, 'mod_launchpad', 'module', 'mod_launchpad', 'mod_launchpad', 1, 1,'', '', '', '', 0, '0000-00-00 00:00:00', 1, 7),
    (740, 0, 'mod_layout', 'module', 'mod_layout', 'mod_layout', 1, 1,'', '', '', '', 0, '0000-00-00 00:00:00', 1, 8),
    (745, 0, 'mod_login', 'module', 'mod_login', 'mod_login', 1, 1,'', '', '', '', 0, '0000-00-00 00:00:00', 1, 9),
    (755, 0, 'mod_logout', 'module', 'mod_logout', 'mod_logout', 1, 1,'', '', '', '', 0, '0000-00-00 00:00:00', 1, 10),
    (760, 0, 'mod_members', 'module', 'mod_members', 'mod_members', 1, 1,'', '', '', '', 0, '0000-00-00 00:00:00', 1, 11),
    (765, 0, 'mod_menu', 'module', 'mod_menu', 'mod_menu', 1, 1,'', '', '', '', 0, '0000-00-00 00:00:00', 1, 12),
    (770, 0, 'mod_pagination', 'module', 'mod_pagination', 'mod_pagination', 1, 1,'', '', '', '', 0, '0000-00-00 00:00:00', 1, 13),
    (775, 0, 'mod_search', 'module', 'mod_search', 'mod_search', 1, 1,'', '', '', '', 0, '0000-00-00 00:00:00', 1, 14),
    (780, 0, 'mod_syndicate', 'module', 'mod_syndicate', 'mod_syndicate', 1, 1,'', '', '', '', 0, '0000-00-00 00:00:00', 1, 15),
    (785, 0, 'mod_toolbar', 'module', 'mod_toolbar', 'mod_toolbar', 1, 1,'', '', '', '', 0, '0000-00-00 00:00:00', 1, 16);

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `extension_id`, '__extensions', '', `name`, CONCAT('index.php?option=com_extensions&id=', `extension_id`), 5
    FROM amy_extensions
    WHERE `type` = 'module'

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 700 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 700;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 705 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 705;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 710 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 710;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 715 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 715;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 720 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 720;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 725 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 725;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 730 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 730;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 735 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 735;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 740 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 740;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 745 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 745;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 750 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 750;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 755 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 755;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 760 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 760;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 765 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 765;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 770 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 770;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 775 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 775;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 780 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 780;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 785 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 785;

#
# Plugins
#

## ACL
INSERT INTO `amy_extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
 `state`, `ordering`)
  VALUES
    (1000, 0, 'plg_acl_example', 'plugin', 'example', 'acl', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1);

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `extension_id`, '__extensions', '', `name`, CONCAT('index.php?option=com_extensions&id=', `extension_id`), 5
    FROM amy_extensions
    WHERE `type` = 'plugin'
      AND `folder` = 'acl'

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 1000 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 1000;

## Authentication
INSERT INTO `amy_extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `state`, `ordering`)
  VALUES
    (1100, 0, 'plg_authentication_molajo', 'plugin', 'molajo', 'authentication', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1);

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `extension_id`, '__extensions', '', `name`, CONCAT('index.php?option=com_extensions&id=', `extension_id`), 5
    FROM amy_extensions
    WHERE `type` = 'plugin'
      AND `folder` = 'authentication'

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 1100 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 1100;

## Content
INSERT INTO `amy_extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `state`, `ordering`)
  VALUES
    (1200, 0, 'plg_content_broadcast', 'plugin', 'broadcast', 'content', 1, 1,  '', '{"mode":"1"}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (1210, 0, 'plg_content_content', 'plugin', 'content', 'content', 1, 1,  '', '{"wrap":"none"}', '', '', 0, '0000-00-00 00:00:00', 1, 2),
    (1220, 0, 'plg_content_emailcloak', 'plugin', 'emailcloak', 'content', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 3),
    (1230, 0, 'plg_content_links', 'plugin', 'links', 'content', 1, 1,  '', '{"mode":"1"}', '', '', 0, '0000-00-00 00:00:00', 1, 4),
    (1235, 0, 'plg_content_loadmodule', 'plugin', 'loadmodule', 'content', 1, 1,  '', '{"wrap":"none"}', '', '', 0, '0000-00-00 00:00:00', 1, 5),
    (1240, 0, 'plg_content_media', 'plugin', 'media', 'content', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 6),
    (1245, 0, 'plg_content_protect', 'plugin', 'protect', 'content', 1, 1,  '', '{"mode":"1"}', '', '', 0, '0000-00-00 00:00:00', 1, 7),
    (1250, 0, 'plg_content_responses', 'plugin', 'responses', 'content', 1, 1,  '', '{"wrap":"none"}', '', '', 0, '0000-00-00 00:00:00', 1, 8);

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `extension_id`, '__extensions', '', `name`, CONCAT('index.php?option=com_extensions&id=', `extension_id`), 5
    FROM amy_extensions
    WHERE `type` = 'plugin'
      AND `folder` = 'content'

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 1200 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 1200;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 1210 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 1210;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 1220 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 1220;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 1230 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 1230;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 1235 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 1235;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 1240 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 1240;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 1245 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 1245;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 1250 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 1250;

## Editors
INSERT INTO `amy_extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `state`, `ordering`)
  VALUES
    (1300, 0, 'plg_editors_aloha', 'plugin', 'aloha', 'editors', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (1310, 0, 'plg_editors_none', 'plugin', 'none', 'editors', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 3, 1);

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `extension_id`, '__extensions', '', `name`, CONCAT('index.php?option=com_extensions&id=', `extension_id`), 5
    FROM amy_extensions
    WHERE `type` = 'plugin'
      AND `folder` = 'editor'

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 1300 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 1300;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 1310 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 1310;

## Extended Editors
INSERT INTO `amy_extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `state`, `ordering`)
  VALUES
    (1400, 0, 'plg_editor_button_article', 'plugin', 'article', 'editor-button', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (1410, 0, 'plg_editor_button_audio', 'plugin', 'audio', 'editor-button', 1, 1,  '', '{"linenumbers":"0","tabmode":"indent"}', '', '', 0, '0000-00-00 00:00:00', 2, 1),
    (1420, 0, 'plg_editor_button_file', 'plugin', 'file', 'editor-button', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 3, 1),
    (1430, 0, 'plg_editor_button_pagebreak', 'plugin', 'pagebreak', 'editor-button', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 4, 1),
    (1440, 0, 'plg_editor_button_readmore', 'plugin', 'readmore', 'editor-button', 1, 1,  '', '{"linenumbers":"0","tabmode":"indent"}', '', '', 0, '0000-00-00 00:00:00', 5, 1),
    (1450, 0, 'plg_editor_button_video', 'plugin', 'video', 'editor-button', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 6, 1);

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `extension_id`, '__extensions', '', `name`, CONCAT('index.php?option=com_extensions&id=', `extension_id`), 5
    FROM amy_extensions
    WHERE `type` = 'plugin'
      AND `folder` = 'editor-button'

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 1400 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 1400;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 1410 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 1410;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 1420 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 1420;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 1430 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 1430;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 1440 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 1440;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 1450 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 1450;

## Extension Plugins
INSERT INTO `amy_extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `state`, `ordering`)
  VALUES
    (1500, 0, 'plg_extension_molajo', 'plugin', 'molajo', 'extension', 1, 1,  '', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1);

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `extension_id`, '__extensions', '', `name`, CONCAT('index.php?option=com_extensions&id=', `extension_id`), 5
    FROM amy_extensions
    WHERE `type` = 'plugin'
      AND `folder` = 'extension'

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 1500 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 1500;

## Molajo
INSERT INTO `amy_extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `state`, `ordering`)
  VALUES
    (2025, 0, 'plg_molajo_extend', 'plugin', 'extend', 'molajo', 1, 1,'{"legacy":false,"name":"PLG_SYSTEM_EXTEND_NAME","type":"plugin","creationDate":"May 2011","author":"Amy Stephen","copyright":"(C) 2011 Amy Stephen. All rights reserved.","authorEmail":"collaborate@molajo.org","authorUrl":"Molajo.org","version":"1.6.0","description":"PLG_SYSTEM_EXTEND_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 5, 1),
    (2030, 0, 'plg_molajo_minifier', 'plugin', 'links', 'molajo', 1, 1,'{"legacy":false,"name":"PLG_MOLAJO_LINKS_NAME","type":"plugin","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"collaborate@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_LINKS_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 6, 1),
    (2035, 0, 'plg_molajo_search', 'plugin', 'media', 'molajo', 1, 1,'{"legacy":false,"name":"PLG_MOLAJO_MEDIA_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"collaborate@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_MEDIA_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 7, 1),
    (2040, 0, 'plg_molajo_tags', 'plugin', 'protect', 'molajo', 1, 1,'{"legacy":false,"name":"PLG_MOLAJO_PROTECT_NAME","type":"plugin","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"collaborate@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_PROTECT_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 8, 1),
    (2060, 0, 'plg_molajo_urls', 'plugin', 'urls', 'molajo', 1, 1,'{"legacy":false,"name":"PLG_MOLAJO_URLS_NAME","type":"module","creationDate":"May 2011","author":"Molajo Project","copyright":"Copyright (C) 2011 Amy Stephen. All rights reserved. See http:\\/\\/molajo.org\\/copyright","authorEmail":"collaborate@molajo.org","authorUrl":"molajo.org\\/MaintainerTeam","version":"1.6.0","description":"PLG_MOLAJO_URLS_DESCRIPTION","group":""}', '', '', '', 0, '0000-00-00 00:00:00', 12, 1);

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `extension_id`, '__extensions', '', `name`, CONCAT('index.php?option=com_extensions&id=', `extension_id`), 5
    FROM amy_extensions
    WHERE `type` = 'plugin'
      AND `folder` = 'molajo'

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2025 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2025;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2030 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2030;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2035 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2035;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2040 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2040;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2060 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2025;

## Query
INSERT INTO `amy_extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `state`, `ordering`)
  VALUES
    (2100, 0, 'plg_query_example', 'plugin', 'example', 'query', 1, 1,'', '{"enable_example_feature":"1"}', '', '', 0, '0000-00-00 00:00:00', 1, 1);

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `extension_id`, '__extensions', '', `name`, CONCAT('index.php?option=com_extensions&id=', `extension_id`), 5
    FROM amy_extensions
    WHERE `type` = 'plugin'
      AND `folder` = 'query'

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2100 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2100;

## Search
INSERT INTO `amy_extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `state`, `ordering`)
  VALUES
    (2200, 0, 'plg_search_categories', 'plugin', 'categories', 'search', 1, 1,'', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (2205, 0, 'plg_search_articles', 'plugin', 'articles', 'search', 1, 1,'', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 2, 1),
    (2210, 0, 'plg_search_media', 'plugin', 'media', 'search', 1, 1,'', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 3, 1);

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `extension_id`, '__extensions', '', `name`, CONCAT('index.php?option=com_extensions&id=', `extension_id`), 5
    FROM amy_extensions
    WHERE `type` = 'plugin'
      AND `folder` = 'search'

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2200 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2200;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2205 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2205;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2210 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2210;

## System
INSERT INTO `amy_extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `state`, `ordering`)
  VALUES
    (2400, 0, 'plg_system_cache', 'plugin', 'cache', 'system', 1, 1,'', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 0, 1),
    (2405, 0, 'plg_system_debug', 'plugin', 'debug', 'system', 1, 1,'', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 0, 2),
    (2410, 0, 'plg_system_languagefilter', 'plugin', 'languagefilter', 'system', 1, 1,'', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 0, 3),
    (2415, 0, 'plg_system_log', 'plugin', 'log', 'system', 1, 1,'', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 0, 4),
    (2420, 0, 'plg_system_logout', 'plugin', 'logout', 'system', 1, 1,'', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 0, 5),
    (2425, 0, 'plg_system_molajo', 'plugin', 'molajo', 'system', 1, 1,'', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 0, 6),
    (2430, 0, 'plg_system_p3p', 'plugin', 'p3p', 'system', 1, 1,'', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 0, 7),
    (2432, 0, 'plg_system_parameters', 'plugin', 'parameters', 'system', 1, 1,'', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 0, 8),
    (2435, 0, 'plg_system_redirect', 'plugin', 'redirect', 'system', 1, 1,'', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 0, 9),
    (2440, 0, 'plg_system_remember', 'plugin', 'remember', 'system', 1, 1,'', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 0, 10),
    (2445, 0, 'plg_system_system', 'plugin', 'system', 'system', 1, 1,'', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 0, 11),
    (2448, 0, 'plg_system_webservices', 'plugin', 'webservices', 'system', 1, 1,'', '{"search_limit":"50","search_content":"1","search_archived":"1"}', '', '', 0, '0000-00-00 00:00:00', 0, 12);

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `extension_id`, '__extensions', '', `name`, CONCAT('index.php?option=com_extensions&id=', `extension_id`), 5
    FROM amy_extensions
    WHERE `type` = 'plugin'
      AND `folder` = 'system'

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2400 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2400;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2405 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2405;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2410 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2410;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2415 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2415;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2420 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2420;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2425 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2425;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2430 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2430;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2435 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2435;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2440 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2440;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2445 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2445;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2448 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2448;

## Users
INSERT INTO `amy_extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `state`, `ordering`)
  VALUES
    (2500, 0, 'plg_user_molajo', 'plugin', 'molajo', 'user', 1, 1,'', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (2550, 0, 'plg_user_profile', 'plugin', 'profile', 'user', 1, 1,'', '{}', '', '', 0, '0000-00-00 00:00:00', 2, 1);

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `extension_id`, '__extensions', '', `name`, CONCAT('index.php?option=com_extensions&id=', `extension_id`), 5
    FROM amy_extensions
    WHERE `type` = 'plugin'
      AND `folder` = 'user'

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2500 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2500;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 2550 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 2550;

## Template
INSERT INTO `amy_extensions` (
  `extension_id`, `asset_id`, `name`, `type`, `element`, `folder`, `enabled`,
  `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`,
  `state`, `ordering`)
  VALUES
    (3000, 0, 'construct', 'template', 'construct', '', 1, 1,'', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 1),
    (3100, 0, 'install', 'template', 'install', '', 1, 1,'', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 2),
    (3150, 0, 'molajito', 'template', 'molajito', '', 1, 1,'', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 3),
    (3200, 0, 'system', 'template', 'system', '', 1, 1,'', '{}', '', '', 0, '0000-00-00 00:00:00', 1, 4);

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `extension_id`, '__extensions', '', `name`, CONCAT('index.php?option=com_extensions&id=', `extension_id`), 5
    FROM amy_extensions
    WHERE `type` = 'template'

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 3000 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 3000;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 3100 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 3100;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 3150 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 3150;

SET @asset_id = (SELECT id FROM `amy_assets` WHERE `content_id` = 3200 AND `content_table` = '__extensions');
UPDATE `amy_extensions` SET asset_id = @asset_id WHERE extension_id = 3200;

#
# Menu - Administrator
#

INSERT INTO `amy_menus`
  (`id`, `application_id`, `title`, `description`, `created`, `created_by`,
    `checked_out`,`checked_out_time`,`version`,`version_of_id`,`state_prior_to_version`)
    VALUES
      (1, 1, 'Launchpad Main Menu', 'Main Menu for the Molajo Administrator linking to Home, Configure, Access, Create, and Build', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1, NULL, NULL),
      (2, 1, 'Launchpad Configure', 'Configure Menu for the Molajo Administrator that enables access to the Global and Personal Configuration Options and system functions such as Global Check-in, Cache, Redirects and System Information.', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1, NULL, NULL),
      (3, 1, 'Launchpad Access', 'Access Menu for the Molajo Administrator that enables access to the User, Mass Mail, Group, and ACL Configuration Options.', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1, NULL, NULL),
      (4, 1, 'Launchpad Create', 'Main Menu for the Molajo Administrator enabling access to Content Components, like Articles, Comments, and Tags', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1, NULL, NULL),
      (5, 1, 'Launchpad Build', 'Main Menu for the Molajo Administrator that allows site builders to access Create, Installer, and the various Managers for Plugins, Modules, Templates, and Layouts', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1, NULL, NULL);

INSERT INTO `amy_menu_items`
  (`id`, `menu_id`, `ordering`,  `application_id`, `asset_id`,
    `title`, `alias`, `note`, `path`, `link`, `type`,
    `published`, `parent_id`, `level`, `component_id`, `checked_out`, `checked_out_time`,
    `browserNav`, `img`, `template_style_id`, `params`,
    `lft`, `rgt`, `home`, `language`)
    VALUES
      (1, 1, 1, 0, 5010, 'Home', 'home', '', '', 'index.php?option=com_dashboard', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 1, '*'),

      (2, 1, 1, 0, 5020, 'Configure', 'configure', '', 'configure', 'index.php?option=com_dashboard&type=configure', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (3, 1, 2, 0, 5030, 'Access', 'access', '', 'access', 'index.php?option=com_dashboard&type=access', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (4, 1, 3, 0, 5040, 'Create', 'create', '', 'create', 'index.php?option=com_dashboard&type=create', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (5, 1, 4, 0, 5050, 'Build', 'build', '', 'build', 'index.php?option=com_dashboard&type=build', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (6, 1, 5, 0, 5060, 'Search', 'search', '', 'search', 'index.php?option=com_dashboard&type=search', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),

      (7, 2, 1, 0, 5070, 'Profile', 'profile', '', 'configure/profile', 'option=com_profile', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (8, 2, 2, 0, 5080, 'System', 'system', '', 'configure/system', 'index.php?option=com_config', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (9, 2, 3, 0, 5090, 'Checkin', 'checkin', '', 'configure/checkin', 'index.php?option=com_checkin', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (10, 2, 4, 0, 5100, 'Cache', 'cache', '', 'configure/cache', 'index.php?option=com_cache', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (11, 2, 5, 0, 5150, 'Backup', 'backup', '', 'configure/backup', 'index.php?option=com_backup', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (12, 2, 6, 0, 5200, 'Redirects', 'redirects', '', 'configure/redirects', 'index.php?option=com_redirects', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),

      (13, 3, 1, 0, 5250, 'Users', 'users', '', 'access/users', 'index.php?option=com_users', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (14, 3, 2, 0, 5300, 'Groups', 'groups', '', 'access/groups', 'index.php?option=com_groups', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (15, 3, 3, 0, 5350, 'Permissions', 'permissions', '', 'access/permissions', 'index.php?option=com_permissions', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (16, 3, 4, 0, 5400, 'Messages', 'messages', '', 'access/messages', 'index.php?option=com_messages', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (17, 3, 5, 0, 5500, 'Activity', 'activity', '', 'access/activity', 'index.php?option=com_activity', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),

      (18, 4, 1, 0, 5600, 'Articles', 'articles', '', 'create/articles', 'index.php?option=com_articles', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (19, 4, 2, 0, 5650, 'Tags', 'tags', '', 'create/tags', 'index.php?option=com_tags', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (20, 4, 3, 0, 5700, 'Comments', 'comments', '', 'create/comments', 'index.php?option=com_comments', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (21, 4, 4, 0, 5750, 'Media', 'media', '', 'create/media', 'index.php?option=com_media', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (22, 4, 5, 0, 5800, 'Categories', 'categories', '', 'create/categories', 'index.php?option=com_categories', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),

      (23, 5, 1, 0, 5900, 'Extensions', 'extensions', '', 'build/extensions', 'index.php?option=com_extensions', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (24, 5, 2, 0, 5910, 'Languages', 'languages', '', 'build/languages', 'index.php?option=com_languages', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (25, 5, 3, 0, 5920, 'Layouts', 'layouts', '', 'build/layouts', 'index.php?option=com_layouts', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (26, 5, 4, 0, 5930, 'Modules', 'modules', '', 'build/modules', 'index.php?option=com_modules', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (27, 5, 5, 0, 5940, 'Plugins', 'plugins', '', 'build/plugins', 'index.php?option=com_plugins', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*'),
      (28, 5, 6, 0, 5950, 'Templates', 'templates', '', 'build/templates', 'index.php?option=com_templates', '', 1, 0, 1, 7, 0, '0000-00-00 00:00:00', 0, '', 0, '{}', 0, 0, 0, '*');

INSERT INTO `amy_assets` ( `content_id`, `content_table`, `option`, `path`, `link`, `access`)
  SELECT `id`, '__menu_items', 'com_menus', `title`, `link`, 5
    FROM amy_menu_items

#
# Menu - Site
#

INSERT INTO `amy_menus`
  (`id`, `application_id`, `title`, `description`, `created`, `created_by`,
    `checked_out`,`checked_out_time`,`version`,`version_of_id`,`state_prior_to_version`)
    VALUES
      (100, 1, 'Main Menu', 'Default Main Menu for the Site Application', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1, NULL, NULL);

#
# Menu Items
#

INSERT INTO `amy_menu_items`
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

INSERT INTO `amy_assets`
  ( `content_id`, `id`, `content_table`, `option`, `path`, `link`, `access`)
    VALUES
      (100, 3500, '__articles', 'com_articles', '', 'index.php?option=com_articles', 1),
      (101, 3510, '__articles', 'com_articles', 'new-article', 'index.php?option=com_articles&view=article&layout=edit', 5),
      (102, 3520, '__articles', 'com_articles', 'article', 'index.php?option=com_articles&view=articles&layout=item&id=5', 1),
      (103, 3530, '__articles', 'com_articles', 'blog', 'index.php?option=com_articles&view=articles&layout=items&catid=2', 1),
      (104, 3540, '__articles', 'com_articles', 'list', 'index.php?option=com_articles&view=articles&layout=table&catid=2', 1),
      (105, 3550, '__articles', 'com_articles', 'table', 'index.php?option=com_articles&type=search', 1),
      (106, 3560, '__dummy', 'com_users', 'login', 'index.php?option=com_users&view=login', 1),
      (107, 3570, '__dummy', 'com_search', 'search', 'index.php?option=com_search&type=search', 1);
#
# MODULES
#

INSERT INTO `amy_modules`
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


INSERT INTO `amy_modules`
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

INSERT INTO `amy_modules_menu`
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

INSERT INTO `amy_templates`
  (`id`, `application_id`, `title`, `description`,
    `created`, `created_by`, `checked_out`, `checked_out_time`,
    `publish_up`, `publish_down`, `published`,
    `version`, `version_of_id`, `state_prior_to_version` )
  VALUES
    (1, 0, 'Construct', 'Construct is a code-based Template Development Framework. It is designed to be flexible and easily used for creating one-of-a-kind templates.', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, NULL),
    (2, 1, 'Mojito', 'Mojito is (cristina?).', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, NULL);

INSERT INTO `amy_template_styles`
  (`id`, `template_id`, `asset_id`, `title`, `description`, `default`,
    `created`, `created_by`, `checked_out`, `checked_out_time`,
    `publish_up`, `publish_down`, `published`,
    `params`, `version`, `version_of_id`, `state_prior_to_version`)
  VALUES
    (1, 1, 7000, 'Blank Slate', 'Blank Slate is (Cristina?)', 1, '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', NULL, NULL, NULL),
    (2, 2, 7010, 'Mojito - Style 1', 'Mojito Style 1 is (Cristina?)', 1, '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '{}', NULL, NULL, NULL);

#
# UPDATES
#
INSERT INTO `amy_update_sites`
  VALUES
    (1, 'Molajo Core', 'collection', 'http://update.molajo.org/core/list.xml', 1),
    (2, 'Molajo Directory', 'collection', 'http://update.molajo.org/directory/list.xml', 1);

INSERT INTO `amy_update_sites_extensions` VALUES (1, 700), (2, 700);

#
# Build Indexes
#

# Actions

CREATE UNIQUE INDEX `idx_actions_table_title` ON `amy_actions` (`title` ASC) ;

# Assets
CREATE UNIQUE INDEX `idx_content_table_id_join` ON `amy_assets` (`content_table` ASC, `id` ASC) ;
CREATE UNIQUE INDEX `idx_content_table_content_id_join` ON `amy_assets` (`content_table` ASC, `content_id` ASC) ;

# Applications
CREATE UNIQUE INDEX `idx_asset_table_id_join` ON `amy_applications` (`asset_id` ASC) ;

# Users
CREATE UNIQUE INDEX `idx_asset_table_id_join` ON `amy_users` (`asset_id` ASC) ;

# Groups
CREATE UNIQUE INDEX `idx_usergroup_parent_title_lookup` ON `amy_groups` (`parent_id` ASC, `title` ASC, `type_id` ASC) ;
CREATE UNIQUE INDEX `idx_asset_table_id_join` ON `amy_groups` (`asset_id` ASC) ;
CREATE INDEX `idx_usergroup_title_lookup` ON `amy_groups` (`title` ASC) ;
CREATE INDEX `idx_usergroup_adjacency_lookup` ON `amy_groups` (`parent_id` ASC) ;
CREATE INDEX `idx_usergroup_type_id` ON `amy_groups` (`type_id` ASC) ;
CREATE INDEX `idx_usergroup_nested_set_lookup` USING BTREE ON `amy_groups` (`lft` ASC, `rgt` ASC) ;

# User Groups
CREATE INDEX `fk_molajo_user_groups_molajo_users1` ON `amy_user_groups` (`user_id` ASC) ;
CREATE INDEX `fk_molajo_user_groups_molajo_groups1` ON `amy_user_groups` (`group_id` ASC) ;

# Group to Groupings
CREATE UNIQUE INDEX `idx_group_to_groupings_id` ON `amy_group_to_groupings` (`group_id` ASC, `grouping_id` ASC) ;
CREATE INDEX `fk_molajo_group_to_groupings_molajo_groups1` ON `amy_group_to_groupings` (`group_id` ASC) ;
CREATE INDEX `fk_molajo_group_to_groupings_molajo_groupings1` ON `amy_group_to_groupings` (`grouping_id` ASC) ;

# User Groupings
CREATE INDEX `fk_molajo_user_groupings_molajo_users1` ON `amy_user_groupings` (`user_id` ASC) ;
CREATE INDEX `fk_molajo_user_groupings_molajo_groupings1` ON `amy_user_groupings` (`grouping_id` ASC) ;

# User Applications
CREATE INDEX `user_id` ON `amy_user_applications` (`user_id` ASC) ;
CREATE INDEX `fk_molajo_user_applications_molajo_users1` ON `amy_user_applications` (`application_id` ASC) ;

# Permissions Groups
CREATE UNIQUE INDEX `idx_asset_action_to_group_lookup` ON `amy_permissions_groups` (`asset_id` ASC, `action_id` ASC, `group_id` ASC) ;
CREATE UNIQUE INDEX `idx_group_to_asset_action_lookup` ON `amy_permissions_groups` (`group_id` ASC, `asset_id` ASC, `action_id` ASC) ;
CREATE INDEX `fk_molajo_permissions_groups_molajo_groups1` ON `amy_permissions_groups` (`group_id` ASC) ;
CREATE INDEX `fk_molajo_permissions_groups_molajo_assets1` ON `amy_permissions_groups` (`asset_id` ASC) ;
CREATE INDEX `fk_molajo_permissions_groups_molajo_actions1` ON `amy_permissions_groups` (`action_id` ASC) ;

# Permissions Groupings
CREATE UNIQUE INDEX `idx_asset_action_to_group_lookup` ON `amy_permissions_groupings` (`asset_id` ASC, `action_id` ASC, `grouping_id` ASC) ;
CREATE UNIQUE INDEX `idx_group_to_asset_action_lookup` ON `amy_permissions_groupings` (`grouping_id` ASC, `asset_id` ASC, `action_id` ASC) ;
CREATE INDEX `fk_molajo_permissions_groupings_molajo_groupings1` ON `amy_permissions_groupings` (`grouping_id` ASC) ;
CREATE INDEX `fk_molajo_permissions_groupings_molajo_assets1` ON `amy_permissions_groupings` (`asset_id` ASC) ;
CREATE INDEX `fk_molajo_permissions_groupings_molajo_actions1` ON `amy_permissions_groupings` (`action_id` ASC) ;

# Categories
CREATE UNIQUE INDEX `idx_asset_table_id_join` ON `amy_categories` (`asset_id` ASC) ;

# Articles
CREATE UNIQUE INDEX `idx_asset_table_id_join` ON `amy_articles` (`asset_id` ASC) ;

# Common
CREATE UNIQUE INDEX `idx_asset_table_id_join` ON `amy_common` (`asset_id` ASC) ;

# Extensions
CREATE UNIQUE INDEX `idx_asset_table_id_join` ON `amy_extensions` (`asset_id` ASC) ;

# Menu Items
CREATE UNIQUE INDEX `idx_asset_table_id_join` ON `amy_menu_items` (`asset_id` ASC) ;

#
# Configuration
#

/* TABLE */

/* 100 MOLAJO_CONFIG_OPTION_ID_TABLE */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 100, '', '', 0),
('core', 100, '__common', '__common', 1);

/* 200 MOLAJO_CONFIG_OPTION_ID_FIELDS */
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
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
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 210, '', '', 0),
('core', 210, 'access', 'MOLAJO_FIELD_ACCESS_LABEL', 1),
('core', 210, 'featured', 'MOLAJO_FIELD_FEATURED_LABEL', 2),
('core', 210, 'ordering', 'MOLAJO_FIELD_ORDERING_LABEL', 3),
('core', 210, 'publish_down', 'MOLAJO_FIELD_PUBLISH_DOWN_LABEL', 4),
('core', 210, 'publish_up', 'MOLAJO_FIELD_PUBLISH_UP_LABEL', 5),
('core', 210, 'state', 'MOLAJO_FIELD_STATE_LABEL', 6),
('core', 210, 'stickied', 'MOLAJO_FIELD_STICKIED_LABEL', 7);

/* 220 MOLAJO_CONFIG_OPTION_ID_JSON_FIELDS */
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 220, '', '', 0),
('core', 220, 'attribs', 'MOLAJO_FIELD_ATTRIBS_LABEL', 1),
('core', 220, 'metadata', 'MOLAJO_FIELD_METADATA_LABEL', 2),
('core', 220, 'params', 'MOLAJO_FIELD_PARAMETERS_LABEL', 3);

/* 230 MOLAJO_CONFIG_OPTION_ID_CONTENT_TYPES */
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 230, '', '', 0),
('core', 230, 'content_type', 'Content Type', 1);

/* 250 MOLAJO_CONFIG_OPTION_ID_STATE */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 250, '', '', 0),
('core', 250, '2', 'MOLAJO_OPTION_ARCHIVED', 1),
('core', 250, '1', 'MOLAJO_OPTION_PUBLISHED', 2),
('core', 250, '0', 'MOLAJO_OPTION_UNPUBLISHED', 3),
('core', 250, '-1', 'MOLAJO_OPTION_TRASHED', 4),
('core', 250, '-2', 'MOLAJO_OPTION_SPAMMED', 5),
('core', 250, '-10', 'MOLAJO_OPTION_VERSION', 6);

/* USER INTERFACE */

/* 300 MOLAJO_CONFIG_OPTION_ID_LIST_TOOLBAR_BUTTONS */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
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
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
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
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 320, '', '', 0),
('core', 320, 'category', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_CATEGORY', 1),
('core', 320, 'default', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_DEFAULT', 2),
('core', 320, 'featured', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_FEATURED', 3),
('core', 320, 'revisions', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_REVISIONS', 4),
('core', 320, 'stickied', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_STICKIED', 5),
('core', 320, 'unpublished', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_UNPUBLISHED', 6);

/* 330 MOLAJO_CONFIG_OPTION_ID_LISTBOX_FILTER */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
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
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
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
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
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
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
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
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
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
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
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
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1100, '', '', 0),
('core', 1100, 'add', 'display', 1),
('core', 1100, 'edit', 'display', 2),
('core', 1100, 'display', 'display', 3);

INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1100, 'apply', 'edit', 4),
('core', 1100, 'cancel', 'edit', 5),
('core', 1100, 'create', 'edit', 6),
('core', 1100, 'save', 'edit', 7),
('core', 1100, 'save2copy', 'edit', 8),
('core', 1100, 'save2new', 'edit', 9),
('core', 1100, 'restore', 'edit', 10);

INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
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

INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1100, 'login', 'login', 28),
('core', 1100, 'logout', 'logout', 29);

/* 1100 MOLAJO_CONFIG_OPTION_ID_TASK_TO_CONTROLLER +application id */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1101, '', '', 0),
('core', 1101, 'add', 'display', 1),
('core', 1101, 'edit', 'display', 2),
('core', 1101, 'display', 'display', 3);

INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1101, 'apply', 'edit', 4),
('core', 1101, 'cancel', 'edit', 5),
('core', 1101, 'create', 'edit', 6),
('core', 1101, 'save', 'edit', 7),
('core', 1101, 'save2copy', 'edit', 8),
('core', 1101, 'save2new', 'edit', 9),
('core', 1101, 'restore', 'edit', 10);

INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
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

INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1101, 'login', 'login', 28),
('core', 1101, 'logout', 'login', 29);

/* OPTION */

/* 1800 MOLAJO_CONFIG_OPTION_ID_DEFAULT_OPTION */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1800, '', '', 0),
('core', 1800, 'com_articles', 'com_articles', 1),
('core', 1801, '', '', 0),
('core', 1801, 'com_login', 'com_login', 1);

/* VIEWS */

/* 2000 MOLAJO_CONFIG_OPTION_ID_VIEWS */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 2000, '', '', 0),
('core', 2000, 'display', 'display', 1),
('core', 2000, 'edit', 'edit', 2);

/* 2100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_VIEW */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 2100, '', '', 0),
('core', 2100, 'display', 'display', 1);

/* 2000 MOLAJO_CONFIG_OPTION_ID_VIEWS +application id **/
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 2001, '', '', 0),
('core', 2001, 'display', 'display', 1),
('core', 2001, 'edit', 'edit', 2);

/* 2100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_VIEW +application id **/
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 2101, '', '', 0),
('core', 2101, 'display', 'display', 1);

/* VIEW LAYOUTS */

/* 3000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_LAYOUTS */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3000, '', '', 0),
('core', 3000, 'default', 'default', 1),
('core', 3000, 'item', 'item', 1),
('core', 3000, 'items', 'items', 1),
('core', 3000, 'table', 'table', 1);

/* 3100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3100, '', '', 0),
('core', 3100, 'default', 'default', 1);

/* 3200 MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_LAYOUTS */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3200, '', '', 0),
('core', 3200, 'default', 'default', 1);

/* 3300 MOLAJO_CONFIG_OPTION_ID_DEFAULT_EDIT_VIEW_LAYOUTS */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3300, '', '', 0),
('core', 3300, 'default', 'default', 1);

/* 3000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_LAYOUTS +application id **/
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3001, '', '', 0),
('core', 3001, 'default', 'default', 1);

/* 3100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS +application id **/
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3101, '', '', 0),
('core', 3101, 'default', 'default', 1);

/* 3200 MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_LAYOUTS +application id **/
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3201, '', '', 0),
('core', 3201, 'default', 'default', 1);

/* 3300 MOLAJO_CONFIG_OPTION_ID_DEFAULT_EDIT_VIEW_LAYOUTS +application id **/
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3301, '', '', 0),
('core', 3301, 'default', 'default', 1);

/* VIEW FORMATS */

/* 4000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_FORMATS */
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4000, '', '', 0),
('core', 4000, 'html', 'html', 1);

/* 4100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_FORMATS */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4100, '', '', 0),
('core', 4100, 'html', 'html', 1);

/* 4200 MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_FORMATS */
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
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
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4300, '', '', 0),
('core', 4300, 'html', 'html', 1);


/* 4000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_FORMATS +application id */
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4001, '', '', 0),
('core', 4001, 'html', 'html', 1);

/* 4100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_FORMATS +application id */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4101, '', '', 0),
('core', 4101, 'html', 'html', 1);

/* 4200 MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_FORMATS +application id */
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
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
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4301, '', '', 0),
('core', 4301, 'html', 'html', 1);

/* 5000 MOLAJO_CONFIG_OPTION_ID_MODEL +application id */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 5000, '', '', 0),
('core', 5000, 'display', 'display', 1),
('core', 5000, 'edit', 'edit', 2);

/* 5001 MOLAJO_CONFIG_OPTION_ID_MODEL +application id */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 5001, '', '', 0),
('core', 5001, 'display', 'display', 1),
('core', 5001, 'edit', 'edit', 2);

/* 6000 MOLAJO_CONFIG_OPTION_ID_PLUGIN_TYPE */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 6000, '', '', 0),
('core', 6000, 'content', 'content', 1);

/** ACL Component Information */

/** 10000 MOLAJO_CONFIG_OPTION_ID_ACL_IMPLEMENTATION **/
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 10000, '', '', 0),
('core', 10000, 'core', 'Core ACL Implementation', 1);

/** 10100 MOLAJO_CONFIG_OPTION_ID_ACL_ITEM_TESTS **/
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 10100, '', '', 0),
('core', 10100, 'view', 'view', 1),
('core', 10100, 'create', 'create', 2),
('core', 10100, 'edit', 'edit', 3),
('core', 10100, 'publish', 'publish', 4),
('core', 10100, 'delete', 'delete', 5),
('core', 10100, 'admin', 'admin', 6);

/** 10000 MOLAJO_CONFIG_OPTION_ID_TASK_ACL_METHODS **/
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
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
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 100, '', '', 0),
('com_login', 100, '__dummy', '__dummy', 1);

/** MVC */

/* CONTROLLER TASKS */

/* 1100 MOLAJO_CONFIG_OPTION_ID_TASK_TO_CONTROLLER */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 1100, '', '', 0),
('com_login', 1100, 'display', 'display', 3);

INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 1100, 'login', 'login', 28),
('com_login', 1100, 'logout', 'login', 29);

/* 1100 MOLAJO_CONFIG_OPTION_ID_TASK_TO_CONTROLLER +application id */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 1101, '', '', 0),
('com_login', 1101, 'display', 'display', 3);

INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 1101, 'login', 'login', 28),
('com_login', 1101, 'logout', 'login', 29);

/* VIEWS */

/* 2000 MOLAJO_CONFIG_OPTION_ID_VIEWS */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 2000, '', '', 0),
('com_login', 2000, 'display', 'display', 1);

/* 2100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_VIEW */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 2100, '', '', 0),
('com_login', 2100, 'display', 'display', 1);

/* 2000 MOLAJO_CONFIG_OPTION_ID_VIEWS +application id **/
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 2001, '', '', 0),
('com_login', 2001, 'display', 'display', 1);

/* 2100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_VIEW +application id **/
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 2101, '', '', 0),
('com_login', 2101, 'display', 'display', 1);

/* VIEW LAYOUTS */

/* 3000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_LAYOUTS */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 3000, '', '', 0),
('com_login', 3000, 'login', 'login', 1);

/* 3100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 3100, '', '', 0),
('com_login', 3100, 'login', 'login', 1);

/* 3000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_LAYOUTS +application id **/
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 3001, '', '', 0),
('com_login', 3001, 'admin_login', 'admin_login', 1);

/* 3100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS +application id **/
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 3101, '', '', 0),
('com_login', 3101, 'admin_login', 'admin_login', 1);

/* VIEW FORMATS */

/* 4000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_FORMATS */
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 4000, '', '', 0),
('com_login', 4000, 'html', 'html', 1),
('com_login', 4001, 'html', 'html', 1);

/* MODELS */

/* 5000 MOLAJO_CONFIG_OPTION_ID_MODEL +application id */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 5000, '', '', 0),
('com_login', 5000, 'dummy', 'dummy', 1);

/* 5000 MOLAJO_CONFIG_OPTION_ID_MODEL +application id */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 5001, '', '', 0),
('com_login', 5001, 'dummy', 'dummy', 1);

/* 6000 MOLAJO_CONFIG_OPTION_ID_PLUGIN_TYPE */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 6000, '', '', 0),
('com_login', 6000, 'user', 'user', 1);

/** ACL Component Information */

/** 10000 MOLAJO_CONFIG_OPTION_ID_ACL_IMPLEMENTATION **/
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 10000, '', '', 0),
('com_login', 10000, 'core', 'Core ACL Implementation', 1);

/** 10100 MOLAJO_CONFIG_OPTION_ID_ACL_ITEM_TESTS **/
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 10100, '', '', 0),
('com_login', 10100, 'view', 'view', 1);

/** 10000 MOLAJO_CONFIG_OPTION_ID_TASK_ACL_METHODS **/
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 10200, '', '', 0),
('com_login', 10200, 'login', 'login', 15),
('com_login', 10200, 'logout', 'logout', 16);

/* ARTICLES */

/* 100 MOLAJO_CONFIG_OPTION_ID_TABLE */;
INSERT INTO `amy_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 100, '', '', 0),
('com_articles', 100, '__articles', '__articles', 1);
