SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `#__actions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__actions` ;

CREATE  TABLE IF NOT EXISTS `#__actions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key' ,
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `idx_actions_table_title` (`title` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__source_tables`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__source_tables` ;

CREATE  TABLE IF NOT EXISTS `#__source_tables` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key' ,
  `source_table` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `idx_actions_table_title` (`source_table` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__assets`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__assets` ;

CREATE  TABLE IF NOT EXISTS `#__assets` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Assets Primary Key' ,
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  `source_table_id` INT(11) NOT NULL ,
  `source_id` INT(11) UNSIGNED NOT NULL COMMENT 'Content Primary Key' ,
  `path` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'URL' ,
  `link` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'The actually link the menu item refers to.' ,
  `view_group_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__groupings table' ,
  `language` CHAR(7) NOT NULL DEFAULT 'en-GB' ,
  `translation_of_id` INT(11) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `idx_content_table_id_join` (`source_table_id` ASC, `id` ASC) ,
  UNIQUE INDEX `idx_content_table_content_id_join` (`source_table_id` ASC, `source_id` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 7011
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__applications`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__applications` ;

CREATE  TABLE IF NOT EXISTS `#__applications` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Application Primary Key' ,
  `name` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Title' ,
  `path` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'URL Alias' ,
  `description` MEDIUMTEXT NULL DEFAULT NULL ,
  `asset_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.' ,
  `parameters` MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Configurable Parameter Values' ,
  `custom_fields` MEDIUMTEXT NULL DEFAULT NULL ,
  `default_template_extension_id` INT(11) NOT NULL DEFAULT 0 ,
  `default_application_indicator` INT(3) NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__update_sites`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__update_sites` ;

CREATE  TABLE IF NOT EXISTS `#__update_sites` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL DEFAULT ' ' ,
  `type` VARCHAR(20) NULL DEFAULT ' ' ,
  `location` TEXT NOT NULL ,
  `enabled` INT(11) NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__extension_types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__extension_types` ;

CREATE  TABLE IF NOT EXISTS `#__extension_types` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key' ,
  `extension_type` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `idx_actions_table_title` (`extension_type` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__extensions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__extensions` ;

CREATE  TABLE IF NOT EXISTS `#__extensions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `extension_type_id` INT(11) NULL ,
  `element` VARCHAR(100) NOT NULL ,
  `folder` VARCHAR(255) NOT NULL ,
  `update_site_id` INT(11) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `element_application_id` (`element` ASC) ,
  INDEX `element_folder_application_id` (`element` ASC, `folder` ASC) ,
  INDEX `extension` (`extension_type_id` ASC, `element` ASC, `folder` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 2551
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__extension_instances`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__extension_instances` ;

CREATE  TABLE IF NOT EXISTS `#__extension_instances` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key' ,
  `extension_type_id` INT(11) NULL ,
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Title' ,
  `sub_title` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Subtitle' ,
  `alias` VARCHAR(255) NULL DEFAULT ' ' ,
  `content_text` MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Content Primary Text Field, can include break to designate Introductory and Full text' ,
  `protected` INT(3) NULL DEFAULT 0 ,
  `status` TINYINT(3) NOT NULL DEFAULT '0' COMMENT 'Published State 2: Archived 1: Published 0: Unpublished -1: Trashed -2: Spam -10 Version' ,
  `start_publishing_datetime` DATETIME NULL COMMENT 'Publish Begin Date and Time' ,
  `stop_publishing_datetime` DATETIME NULL COMMENT 'Publish End Date and Time' ,
  `version` INT(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Version Number' ,
  `version_of_id` INT(11) NULL DEFAULT NULL COMMENT 'Primary ID for this Version' ,
  `status_prior_to_version` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'State value prior to creating this version copy and changing the state to Version' ,
  `created_datetime` DATETIME NULL COMMENT 'Created Date and Time' ,
  `created_by` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Created by User ID' ,
  `modified_datetime` DATETIME NULL COMMENT 'Modified Date' ,
  `modified_by` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Modified By User ID' ,
  `checked_out_datetime` DATETIME NULL COMMENT 'Checked out Date and Time' ,
  `checked_out_by` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Checked out by User Id' ,
  `asset_id` INT(11) NOT NULL ,
  `extension_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Primary Key for Component Content' ,
  `custom_fields` MEDIUMTEXT NULL DEFAULT NULL ,
  `parameters` MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Attributes (Custom Fields)' ,
  `module_position` VARCHAR(50) NOT NULL DEFAULT ' ' COMMENT 'User DEFAULT 1-DEFAULT 0-Not DEFAULT' ,
  `menu_item_parent_id` INT(11) NULL ,
  `menu_item_level` INT(11) NULL ,
  `menu_item_type` VARCHAR(45) NULL ,
  `menu_item_extension_id` VARCHAR(45) NULL ,
  `menu_item_template_id` INT(11) NULL ,
  `menu_item_link_target` VARCHAR(45) NULL ,
  `menu_item_lft` INT(11) NULL ,
  `menu_item_rgt` INT(11) NULL ,
  `menu_item_home` TINYINT(3) NULL ,
  `menu_item_path` VARCHAR(2048) NULL ,
  `menu_item_link` VARCHAR(2048) NULL ,
  `language` CHAR(7) NULL DEFAULT 'en-GB' ,
  `translation_of_id` INT(11) NULL DEFAULT NULL ,
  `ordering` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Ordering' ,
  PRIMARY KEY (`id`) ,
  INDEX `idx_component_component_id_id` (`extension_id` ASC, `id` ASC) ,
  INDEX `idx_checkout` (`checked_out_by` ASC) ,
  INDEX `idx_state` (`status` ASC) ,
  INDEX `idx_createdby` (`created_by` ASC) ,
  UNIQUE INDEX `asset_id_UNIQUE` (`asset_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__categories`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__categories` ;

CREATE  TABLE IF NOT EXISTS `#__categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(255) NOT NULL ,
  `subtitle` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  `alias` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  `content_text` MEDIUMTEXT NULL DEFAULT NULL ,
  `status` INT(3) UNSIGNED NOT NULL DEFAULT '0' ,
  `start_publishing_datetime` DATETIME NULL ,
  `stop_publishing_datetime` DATETIME NULL ,
  `version` INT(11) NOT NULL DEFAULT 0 ,
  `version_of_id` INT(11) NOT NULL DEFAULT 0 ,
  `status_prior_to_version` INT(11) NULL ,
  `checked_out_by` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `checked_out_datetime` DATETIME NOT NULL ,
  `created_by` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `created_datetime` DATETIME NOT NULL ,
  `modified_by` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `modified_datetime` DATETIME NOT NULL ,
  `asset_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK to the #__assets table.' ,
  `extension_instance_id` INT(11) NOT NULL ,
  `parent_id` VARCHAR(45) NULL ,
  `lft` INT(11) NULL ,
  `rgt` INT(11) NULL ,
  `level` INT(11) NULL ,
  `metakey` TEXT NULL COMMENT 'The meta keywords for the page.' ,
  `metadesc` TEXT NULL COMMENT 'The meta description for the page.' ,
  `metadata` TEXT NULL COMMENT 'JSON encoded metadata properties.' ,
  `custom_fields` MEDIUMTEXT NULL ,
  `parameters` MEDIUMTEXT NULL COMMENT 'Configurable Parameter Values' ,
  `language` CHAR(7) NOT NULL DEFAULT ' ' ,
  `translation_of_id` INT(11) NULL ,
  `ordering` INT(11) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `idx_asset_table_id_join` (`asset_id` ASC) ,
  INDEX `cat_idx` (`status` ASC) ,
  INDEX `idx_checkout` (`checked_out_by` ASC) ,
  INDEX `idx_alias` (`alias` ASC) ,
  INDEX `idx_language` (`language` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__content`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__content` ;

CREATE  TABLE IF NOT EXISTS `#__content` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key' ,
  `catid` INT(11) UNSIGNED NOT NULL COMMENT 'Category ID associated with the Primary Key' ,
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Title' ,
  `subtitle` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Subtitle' ,
  `alias` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'URL Alias' ,
  `content_type` TINYINT(3) NOT NULL DEFAULT '0' COMMENT 'Content Type: Links to #__configuration.option_id = 10 and component_option values matching ' ,
  `content_text` MEDIUMTEXT NULL COMMENT 'Content Primary Text Field, can include break to designate Introductory and Full text' ,
  `content_link` VARCHAR(2083) NULL COMMENT 'Content Link for Weblink or Newsfeed Field' ,
  `content_email_address` VARCHAR(255) NULL COMMENT 'Content Email Field' ,
  `content_numeric_value` TINYINT(3) NULL COMMENT 'Content Numeric Value, ex. vote on poll' ,
  `content_file` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Content Network Path to File' ,
  `featured` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Featured 1-Featured 0-Not Featured' ,
  `stickied` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Stickied 1-Stickied 0-Not Stickied' ,
  `user_default` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'User DEFAULT 1-DEFAULT 0-Not DEFAULT' ,
  `category_default` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Category DEFAULT 1-DEFAULT 0-Not DEFAULT' ,
  `status` TINYINT(3) NOT NULL DEFAULT '0' COMMENT 'Published State 2: Archived 1: Published 0: Unpublished -1: Trashed -2: Spam -10 Version' ,
  `start_publishing_datetime` DATETIME NOT NULL COMMENT 'Publish Begin Date and Time' ,
  `stop_publishing_datetime` DATETIME NOT NULL COMMENT 'Publish End Date and Time' ,
  `version` INT(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Version Number' ,
  `version_of_id` INT(11) NULL COMMENT 'Primary ID for this Version' ,
  `status_prior_to_version` INT(11) UNSIGNED NULL COMMENT 'State value prior to creating this version copy and changing the state to Version' ,
  `created_datetime` DATETIME NOT NULL COMMENT 'Created Date and Time' ,
  `created_by` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Created by User ID' ,
  `created_by_alias` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Created by Alias' ,
  `created_by_email` VARCHAR(255) NULL COMMENT 'Created By Email Address' ,
  `created_by_website` VARCHAR(255) NULL COMMENT 'Created By Website' ,
  `created_by_ip_address` CHAR(15) NULL COMMENT 'Created By IP Address' ,
  `created_by_referer` VARCHAR(255) NULL COMMENT 'Created By Referer' ,
  `modified_datetime` DATETIME NOT NULL COMMENT 'Modified Date' ,
  `modified_by` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Modified By User ID' ,
  `checked_out_datetime` DATETIME NOT NULL COMMENT 'Checked out Date and Time' ,
  `checked_out_by` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Checked out by User Id' ,
  `asset_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK to the #__assets table.' ,
  `extension_id` INT(11) UNSIGNED NOT NULL COMMENT 'Primary Key for Component Content' ,
  `parent_id` INT(11) NULL COMMENT 'Nested set parent' ,
  `lft` INT(11) NULL COMMENT 'Nested set lft' ,
  `rgt` INT(11) NULL COMMENT 'Nested set rgt' ,
  `level` INT(11) NULL DEFAULT '0' COMMENT 'The cached level in the nested tree' ,
  `metakey` TEXT NULL COMMENT 'Meta Key' ,
  `metadesc` TEXT NULL COMMENT 'Meta Description' ,
  `metadata` TEXT NULL COMMENT 'Meta Data' ,
  `custom_fields` MEDIUMTEXT NULL COMMENT 'Attributes (Custom Fields)' ,
  `parameters` MEDIUMTEXT NULL COMMENT 'Configurable Parameter Values' ,
  `language` CHAR(7) NULL DEFAULT 'en-GB' ,
  `translation_of_id` INT(11) NULL DEFAULT NULL ,
  `ordering` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Ordering' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `idx_asset_table_id_join` (`asset_id` ASC) ,
  INDEX `idx_component_component_id_id` (`extension_id` ASC, `id` ASC) ,
  INDEX `idx_checkout` (`checked_out_by` ASC) ,
  INDEX `idx_state` (`status` ASC) ,
  INDEX `idx_catid` (`catid` ASC) ,
  INDEX `idx_createdby` (`created_by` ASC) ,
  INDEX `idx_featured_catid` (`featured` ASC, `catid` ASC) ,
  INDEX `idx_stickied_catid` (`stickied` ASC, `catid` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__configuration`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__configuration` ;

CREATE  TABLE IF NOT EXISTS `#__configuration` (
  `component_option` VARCHAR(50) NOT NULL DEFAULT ' ' ,
  `option_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' ,
  `option_value` VARCHAR(80) NOT NULL DEFAULT ' ' ,
  `option_value_literal` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  `ordering` INT(11) NOT NULL DEFAULT '0' ,
  UNIQUE INDEX `idx_component_option_id_value_key` (`component_option` ASC, `option_id` ASC, `option_value` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__groups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__groups` ;

CREATE  TABLE IF NOT EXISTS `#__groups` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Group Primary Key' ,
  `title` VARCHAR(255) NOT NULL DEFAULT '  ' ,
  `subtitle` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  `description` MEDIUMTEXT NOT NULL ,
  `parent_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Parent ID' ,
  `lft` INT(11) NOT NULL DEFAULT '0' COMMENT 'Nested set lft.' ,
  `rgt` INT(11) NOT NULL DEFAULT '0' COMMENT 'Nested set rgt.' ,
  `type_id` INT(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Users: 0, Groups: 1' ,
  `asset_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.' ,
  `protected` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'If true, protects group from system removal via the interface.' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `idx_usergroup_parent_title_lookup` (`parent_id` ASC, `title` ASC, `type_id` ASC) ,
  UNIQUE INDEX `idx_asset_table_id_join` (`asset_id` ASC) ,
  INDEX `idx_usergroup_title_lookup` (`title` ASC) ,
  INDEX `idx_usergroup_adjacency_lookup` (`parent_id` ASC) ,
  INDEX `idx_usergroup_type_id` (`type_id` ASC) ,
  INDEX `idx_usergroup_nested_set_lookup` USING BTREE (`lft` ASC, `rgt` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__view_groups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__view_groups` ;

CREATE  TABLE IF NOT EXISTS `#__view_groups` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Groupings Primary Key' ,
  `view_group_name_list` TEXT NOT NULL ,
  `view_group_id_list` TEXT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__group_view_groups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__group_view_groups` ;

CREATE  TABLE IF NOT EXISTS `#__group_view_groups` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Group to Group Primary Key' ,
  `group_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK to the #__group table.' ,
  `view_group_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK to the #__groupings table.' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `idx_group_to_groupings_id` (`group_id` ASC, `view_group_id` ASC) ,
  INDEX `fk_molajo_group_to_groupings_molajo_groups1` (`group_id` ASC) ,
  INDEX `fk_molajo_group_to_groupings_molajo_groupings1` (`view_group_id` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 7
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__extension_usage`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__extension_usage` ;

CREATE  TABLE IF NOT EXISTS `#__extension_usage` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `extension_id` INT(11) NOT NULL ,
  `asset_id` INT(11) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__view_group_permissions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__view_group_permissions` ;

CREATE  TABLE IF NOT EXISTS `#__view_group_permissions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Permissions Primary Key' ,
  `view_group_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__groups.id' ,
  `asset_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__assets.id' ,
  `action_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__actions.id' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `idx_asset_action_to_group_lookup` (`asset_id` ASC, `action_id` ASC, `view_group_id` ASC) ,
  UNIQUE INDEX `idx_group_to_asset_action_lookup` (`view_group_id` ASC, `asset_id` ASC, `action_id` ASC) ,
  INDEX `fk_molajo_permissions_groupings_molajo_groupings1` (`view_group_id` ASC) ,
  INDEX `fk_molajo_permissions_groupings_molajo_assets1` (`asset_id` ASC) ,
  INDEX `fk_molajo_permissions_groupings_molajo_actions1` (`action_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__group_permissions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__group_permissions` ;

CREATE  TABLE IF NOT EXISTS `#__group_permissions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Permissions Primary Key' ,
  `group_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #_groups.id' ,
  `asset_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__assets.id' ,
  `action_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__actions.id' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `idx_asset_action_to_group_lookup` (`asset_id` ASC, `action_id` ASC, `group_id` ASC) ,
  UNIQUE INDEX `idx_group_to_asset_action_lookup` (`group_id` ASC, `asset_id` ASC, `action_id` ASC) ,
  INDEX `fk_molajo_permissions_groups_molajo_groups1` (`group_id` ASC) ,
  INDEX `fk_molajo_permissions_groups_molajo_assets1` (`asset_id` ASC) ,
  INDEX `fk_molajo_permissions_groups_molajo_actions1` (`action_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__session`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__session` ;

CREATE  TABLE IF NOT EXISTS `#__session` (
  `session_id` VARCHAR(32) NOT NULL DEFAULT ' ' ,
  `application_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' ,
  `guest` INT(3) UNSIGNED NULL DEFAULT '1' ,
  `session_time` VARCHAR(14) NULL DEFAULT ' ' ,
  `data` LONGTEXT NULL DEFAULT NULL ,
  `userid` INT(11) NULL DEFAULT '0' ,
  `username` VARCHAR(150) NULL DEFAULT ' ' ,
  PRIMARY KEY (`session_id`) ,
  INDEX `whosonline` (`guest` ASC) ,
  INDEX `userid` (`userid` ASC) ,
  INDEX `time` (`session_time` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__users` ;

CREATE  TABLE IF NOT EXISTS `#__users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(255) NOT NULL DEFAULT '  ' ,
  `full_name` VARCHAR(255) NOT NULL DEFAULT '  ' ,
  `first_name` VARCHAR(100) NULL ,
  `last_name` VARCHAR(100) NULL ,
  `content_text` MEDIUMTEXT NULL ,
  `email` VARCHAR(255) NOT NULL DEFAULT '  ' ,
  `password` VARCHAR(100) NOT NULL DEFAULT '  ' ,
  `block` INT(3) NOT NULL DEFAULT '0' ,
  `activated` INT(3) NULL ,
  `send_email` INT(3) NULL DEFAULT '0' ,
  `register_datetime` DATETIME NULL ,
  `last_visit_datetime` DATETIME NULL ,
  `parameters` MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Configurable Parameter Values' ,
  `custom_fields` MEDIUMTEXT NULL ,
  `asset_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `idx_asset_table_id_join` (`asset_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__user_applications`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__user_applications` ;

CREATE  TABLE IF NOT EXISTS `#__user_applications` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__users.id' ,
  `application_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__applications.id' ,
  INDEX `user_id` (`user_id` ASC) ,
  INDEX `fk_molajo_user_applications_molajo_users1` (`application_id` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__user_view_groups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__user_view_groups` ;

CREATE  TABLE IF NOT EXISTS `#__user_view_groups` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__users.id' ,
  `view_group_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__groupings.id' ,
  PRIMARY KEY (`id`, `user_id`, `view_group_id`) ,
  INDEX `fk_molajo_user_groupings_molajo_users1` (`user_id` ASC) ,
  INDEX `fk_molajo_user_groupings_molajo_groupings1` (`view_group_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__user_groups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__user_groups` ;

CREATE  TABLE IF NOT EXISTS `#__user_groups` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__users.id' ,
  `group_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__groups.id' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_molajo_user_groups_molajo_users1` (`user_id` ASC) ,
  INDEX `fk_molajo_user_groups_molajo_groups1` (`group_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__sites`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__sites` ;

CREATE  TABLE IF NOT EXISTS `#__sites` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Application Primary Key' ,
  `name` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Title' ,
  `path` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'URL Alias' ,
  `description` MEDIUMTEXT NULL DEFAULT NULL ,
  `parameters` MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Configurable Parameter Values' ,
  `custom_fields` MEDIUMTEXT NULL DEFAULT NULL ,
  `base_url` VARCHAR(2048) NOT NULL DEFAULT ' ' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `#__site_application_extensions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__site_application_extensions` ;

CREATE  TABLE IF NOT EXISTS `#__site_application_extensions` (
  `id` INT(11) NOT NULL ,
  `site_id` INT(11) NOT NULL ,
  `application_id` INT(11) NOT NULL ,
  `extension_id` INT(11) NOT NULL ,
  `extension_instance_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `#__content_categories`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__content_categories` ;

CREATE  TABLE IF NOT EXISTS `#__content_categories` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key' ,
  `category_id` INT(11) NOT NULL ,
  `content_id` INT(11) NOT NULL ,
  `content_table_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `idx_actions_table_title` (`content_table_id` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
