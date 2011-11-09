SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

-- -----------------------------------------------------
-- Table `molajo_actions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_actions` ;

CREATE  TABLE IF NOT EXISTS `molajo_actions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key' ,
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX `idx_actions_table_title` ON `molajo_actions` (`title` ASC) ;


-- -----------------------------------------------------
-- Table `molajo_applications`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_applications` ;

CREATE  TABLE IF NOT EXISTS `molajo_applications` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Application Primary Key' ,
  `name` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Title' ,
  `path` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'URL Alias' ,
  `description` MEDIUMTEXT NULL DEFAULT NULL ,
  `parameters` MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Configurable Parameter Values' ,
  `custom_fields` MEDIUMTEXT NULL DEFAULT NULL ,
  `default_template_extension_id` INT(11) NOT NULL DEFAULT 0 ,
  `default_application_indicator` TINYINT(1) NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;


CREATE  TABLE IF NOT EXISTS `molajo_content` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key' ,
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Title' ,
  `subtitle` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Subtitle' ,
  `alias` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'URL Alias' ,
  `content_type` TINYINT(4) NOT NULL DEFAULT '0' COMMENT 'Content Type: Links to #__configuration.option_id = 10 and component_option values matching ' ,
  `content_text` MEDIUMTEXT NULL COMMENT 'Content Primary Text Field, can include break to designate Introductory and Full text' ,
  `content_link` VARCHAR(2083) NULL COMMENT 'Content Link for Weblink or Newsfeed Field' ,
  `content_email_address` VARCHAR(255) NULL COMMENT 'Content Email Field' ,
  `content_numeric_value` TINYINT(4) NULL COMMENT 'Content Numeric Value, ex. vote on poll' ,
  `content_file` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Content Network Path to File' ,
  `featured` TINYINT(4) NOT NULL DEFAULT '0' COMMENT 'Featured 1-Featured 0-Not Featured' ,
  `stickied` TINYINT(4) NOT NULL DEFAULT '0' COMMENT 'Stickied 1-Stickied 0-Not Stickied' ,
  `user_default` TINYINT(4) NOT NULL DEFAULT '0' COMMENT 'User DEFAULT 1-DEFAULT 0-Not DEFAULT' ,
  `category_default` TINYINT(4) NOT NULL DEFAULT '0' COMMENT 'Category DEFAULT 1-DEFAULT 0-Not DEFAULT' ,
  `status` TINYINT(4) NOT NULL DEFAULT '0' COMMENT 'Published State 2: Archived 1: Published 0: Unpublished -1: Trashed -2: Spam -10 Version' ,
  `start_publishing_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish Begin Date and Time' ,
  `stop_publishing_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish End Date and Time' ,
  `version` INT(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Version Number' ,
  `version_of_id` INT(11) NULL COMMENT 'Primary ID for this Version' ,
  `status_prior_to_version` INT(11) UNSIGNED NULL COMMENT 'State value prior to creating this version copy and changing the state to Version' ,
  `created_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Created Date and Time' ,
  `created_by` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Created by User ID' ,
  `created_by_alias` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Created by Alias' ,
  `created_by_email` VARCHAR(255) NULL COMMENT 'Created By Email Address' ,
  `created_by_website` VARCHAR(255) NULL COMMENT 'Created By Website' ,
  `created_by_ip_address` CHAR(15) NULL COMMENT 'Created By IP Address' ,
  `created_by_referer` VARCHAR(255) NULL COMMENT 'Created By Referer' ,
  `modified_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modified Date' ,
  `modified_by` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Modified By User ID' ,
  `checked_out_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Checked out Date and Time' ,
  `checked_out_by` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Checked out by User Id' ,
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
  `extension_instance_id` INT(11) NOT NULL ,
  `ordering` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Ordering' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `idx_component_component_id_id` ON `molajo_content` (`id` ASC) ;

CREATE INDEX `idx_checkout` ON `molajo_content` (`checked_out_by` ASC) ;

CREATE INDEX `idx_state` ON `molajo_content` (`status` ASC) ;

CREATE INDEX `idx_createdby` ON `molajo_content` (`created_by` ASC) ;

CREATE INDEX `idx_featured_catid` ON `molajo_content` (`featured` ASC) ;

CREATE INDEX `idx_stickied_catid` ON `molajo_content` (`stickied` ASC) ;



-- -----------------------------------------------------
-- Table `molajo_source_tables`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_source_tables` ;

CREATE  TABLE IF NOT EXISTS `molajo_source_tables` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key' ,
  `source_table` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `molajo_assets`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_assets` ;

CREATE  TABLE IF NOT EXISTS `molajo_assets` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Assets Primary Key' ,
  `source_table_id` INT(11) NOT NULL ,
  `source_id` INT(11) UNSIGNED NOT NULL COMMENT 'Content Primary Key' ,
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  `sef_request` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'URL' ,
  `request` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'The actually link the menu item refers to.' ,
  `language` CHAR(7) NOT NULL DEFAULT 'en-GB' ,
  `translation_of_id` INT(11) NULL DEFAULT NULL ,
  `redirect_to_id` INT(11) NULL DEFAULT NULL ,
  `view_group_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__groupings table' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 7011
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_assets_source_tables1` ON `molajo_assets` (`source_table_id` ASC) ;


-- -----------------------------------------------------
-- Table `molajo_categories`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_categories` ;

CREATE  TABLE IF NOT EXISTS `molajo_categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(255) NOT NULL ,
  `subtitle` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  `alias` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  `content_text` MEDIUMTEXT NULL DEFAULT NULL ,
  `status` INT(3) UNSIGNED NOT NULL DEFAULT '0' ,
  `start_publishing_datetime` DATETIME NULL DEFAULT '0000-00-00 00:00:00' ,
  `stop_publishing_datetime` DATETIME NULL DEFAULT '0000-00-00 00:00:00' ,
  `version` INT(11) NOT NULL DEFAULT 0 ,
  `version_of_id` INT(11) NOT NULL DEFAULT 0 ,
  `status_prior_to_version` INT(11) NULL ,
  `checked_out_by` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `checked_out_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created_by` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `created_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `modified_by` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `modified_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
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
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `cat_idx` ON `molajo_categories` (`status` ASC) ;

CREATE INDEX `idx_checkout` ON `molajo_categories` (`checked_out_by` ASC) ;

CREATE INDEX `idx_alias` ON `molajo_categories` (`alias` ASC) ;

CREATE INDEX `idx_language` ON `molajo_categories` (`language` ASC) ;


-- -----------------------------------------------------
-- Table `molajo_extension_types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_extension_types` ;

CREATE  TABLE IF NOT EXISTS `molajo_extension_types` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key' ,
  `extension_type` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX `idx_actions_table_title` ON `molajo_extension_types` (`extension_type` ASC) ;


-- -----------------------------------------------------
-- Table `molajo_extensions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_extensions` ;

CREATE  TABLE IF NOT EXISTS `molajo_extensions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `extension_type_id` INT(11) NULL ,
  `element` VARCHAR(100) NOT NULL ,
  `folder` VARCHAR(255) NOT NULL ,
  `update_site_id` INT(11) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 2551
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_extensions_extension_types1` ON `molajo_extensions` (`extension_type_id` ASC) ;


-- -----------------------------------------------------
-- Table `molajo_extension_instances`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_extension_instances` ;

CREATE  TABLE IF NOT EXISTS `molajo_extension_instances` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key' ,
  `extension_type_id` INT(11) NULL ,
  `extension_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Primary Key for Component Content' ,
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Title' ,
  `subtitle` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Subtitle' ,
  `alias` VARCHAR(255) NULL DEFAULT ' ' ,
  `content_text` MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Content Primary Text Field, can include break to designate Introductory and Full text' ,
  `protected` TINYINT(1) NULL DEFAULT 0 ,
  `enabled` TINYINT(1) NULL DEFAULT '0' ,
  `status` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Published State 2: Archived 1: Published 0: Unpublished -1: Trashed -2: Spam -10 Version' ,
  `start_publishing_datetime` DATETIME NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish Begin Date and Time' ,
  `stop_publishing_datetime` DATETIME NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish End Date and Time' ,
  `version` INT(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Version Number' ,
  `version_of_id` INT(11) NULL DEFAULT NULL COMMENT 'Primary ID for this Version' ,
  `status_prior_to_version` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'State value prior to creating this version copy and changing the state to Version' ,
  `created_datetime` DATETIME NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Created Date and Time' ,
  `created_by` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Created by User ID' ,
  `modified_datetime` DATETIME NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modified Date' ,
  `modified_by` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Modified By User ID' ,
  `checked_out_datetime` DATETIME NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Checked out Date and Time' ,
  `checked_out_by` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Checked out by User Id' ,
  `custom_fields` MEDIUMTEXT NULL ,
  `parameters` MEDIUMTEXT NULL COMMENT 'Attributes (Custom Fields)' ,
  `position` VARCHAR(50) NOT NULL DEFAULT ' ' COMMENT 'User DEFAULT 1-DEFAULT 0-Not DEFAULT' ,
  `menu_item_parent_id` INT(11) NULL ,
  `menu_item_level` INT(11) NULL ,
  `menu_item_type` VARCHAR(45) NULL ,
  `menu_item_extension_id` VARCHAR(45) NULL ,
  `menu_item_template_id` INT(11) NULL ,
  `menu_item_link_target` VARCHAR(45) NULL ,
  `menu_item_lft` INT(11) NULL ,
  `menu_item_rgt` INT(11) NULL ,
  `menu_item_home` TINYINT(1) NULL ,
  `menu_item_sef_request` VARCHAR(2048) NULL ,
  `menu_item_request` VARCHAR(2048) NULL ,
  `language` CHAR(7) NULL DEFAULT 'en-GB' ,
  `translation_of_id` INT(11) NULL DEFAULT NULL ,
  `ordering` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Ordering' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `idx_component_component_id_id` ON `molajo_extension_instances` (`extension_id` ASC, `id` ASC) ;

CREATE INDEX `idx_checkout` ON `molajo_extension_instances` (`checked_out_by` ASC) ;

CREATE INDEX `idx_state` ON `molajo_extension_instances` (`status` ASC) ;

CREATE INDEX `idx_createdby` ON `molajo_extension_instances` (`created_by` ASC) ;

CREATE INDEX `fk_extension_instances_extensions1` ON `molajo_extension_instances` (`extension_id` ASC) ;


-- -----------------------------------------------------
-- Table `molajo_configurations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_configurations` ;

CREATE  TABLE IF NOT EXISTS `molajo_configurations` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `extension_instances_id` INT(11) NOT NULL ,
  `option_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' ,
  `option_value` VARCHAR(80) NOT NULL DEFAULT ' ' ,
  `option_value_literal` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  `ordering` INT(11) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX `idx_component_option_id_value_key` ON `molajo_configurations` (`option_id` ASC, `option_value` ASC) ;

CREATE INDEX `fk_configurations_extension_instances1` ON `molajo_configurations` (`extension_instances_id` ASC) ;


-- -----------------------------------------------------
-- Table `molajo_groups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_groups` ;

CREATE  TABLE IF NOT EXISTS `molajo_groups` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Group Primary Key' ,
  `title` VARCHAR(255) NOT NULL DEFAULT '  ' ,
  `subtitle` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  `description` MEDIUMTEXT NOT NULL ,
  `protected` TINYINT(4) NOT NULL DEFAULT '0' COMMENT 'If true, protects group from system removal via the interface.' ,
  `custom_fields` MEDIUMTEXT NULL ,
  `parameters` MEDIUMTEXT NULL ,
  `parent_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Parent ID' ,
  `lft` INT(11) NOT NULL DEFAULT '0' COMMENT 'Nested set lft.' ,
  `rgt` INT(11) NOT NULL DEFAULT '0' COMMENT 'Nested set rgt.' ,
  `ordering` INT(11) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX `idx_usergroup_parent_title_lookup` ON `molajo_groups` (`parent_id` ASC, `title` ASC) ;

CREATE INDEX `idx_usergroup_title_lookup` ON `molajo_groups` (`title` ASC) ;

CREATE INDEX `idx_usergroup_adjacency_lookup` ON `molajo_groups` (`parent_id` ASC) ;

CREATE INDEX `idx_usergroup_nested_set_lookup` USING BTREE ON `molajo_groups` (`lft` ASC, `rgt` ASC) ;


-- -----------------------------------------------------
-- Table `molajo_view_groups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_view_groups` ;

CREATE  TABLE IF NOT EXISTS `molajo_view_groups` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `view_group_name_list` TEXT NOT NULL ,
  `view_group_id_list` TEXT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `molajo_group_view_groups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_group_view_groups` ;

CREATE  TABLE IF NOT EXISTS `molajo_group_view_groups` (
  `group_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK to the #__group table.' ,
  `view_group_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK to the #__groupings table.' ,
  PRIMARY KEY (`view_group_id`, `group_id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 7
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_group_view_groups_groups1` ON `molajo_group_view_groups` (`group_id` ASC) ;

CREATE INDEX `fk_group_view_groups_view_groups1` ON `molajo_group_view_groups` (`view_group_id` ASC) ;


-- -----------------------------------------------------
-- Table `molajo_extension_criteria`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_extension_criteria` ;

CREATE  TABLE IF NOT EXISTS `molajo_extension_criteria` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `extension_instance_id` INT(11) NOT NULL ,
  `extension_type_id` INT(11) NULL ,
  `position` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_extension_criteria_extension_types1` ON `molajo_extension_criteria` (`extension_type_id` ASC) ;


-- -----------------------------------------------------
-- Table `molajo_view_group_permissions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_view_group_permissions` ;

CREATE  TABLE IF NOT EXISTS `molajo_view_group_permissions` (
  `view_group_id` INT(11) NOT NULL COMMENT 'Foreign Key to #__groups.id' ,
  `asset_id` INT(11) NOT NULL COMMENT 'Foreign Key to #__assets.id' ,
  `action_id` INT(11) NOT NULL COMMENT 'Foreign Key to #__actions.id' ,
  PRIMARY KEY (`view_group_id`, `asset_id`, `action_id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_view_group_permissions_view_groups1` ON `molajo_view_group_permissions` (`view_group_id` ASC) ;

CREATE INDEX `fk_view_group_permissions_actions1` ON `molajo_view_group_permissions` (`action_id` ASC) ;

CREATE INDEX `fk_view_group_permissions_assets1` ON `molajo_view_group_permissions` (`asset_id` ASC) ;


-- -----------------------------------------------------
-- Table `molajo_group_permissions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_group_permissions` ;

CREATE  TABLE IF NOT EXISTS `molajo_group_permissions` (
  `group_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #_groups.id' ,
  `asset_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__assets.id' ,
  `action_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__actions.id' ,
  PRIMARY KEY (`action_id`, `asset_id`, `group_id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_group_permissions_groups1` ON `molajo_group_permissions` (`group_id` ASC) ;

CREATE INDEX `fk_group_permissions_assets1` ON `molajo_group_permissions` (`asset_id` ASC) ;

CREATE INDEX `fk_group_permissions_actions1` ON `molajo_group_permissions` (`action_id` ASC) ;


-- -----------------------------------------------------
-- Table `molajo_users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_users` ;

CREATE  TABLE IF NOT EXISTS `molajo_users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(255) NOT NULL ,
  `first_name` VARCHAR(100) NULL ,
  `last_name` VARCHAR(150) NULL ,
  `content_text` MEDIUMTEXT NULL ,
  `email` VARCHAR(255) NOT NULL DEFAULT '  ' ,
  `password` VARCHAR(100) NOT NULL DEFAULT '  ' ,
  `block` TINYINT(4) NOT NULL DEFAULT 0 ,
  `activated` TINYINT(4) NULL DEFAULT 0 ,
  `send_email` TINYINT(4) NULL DEFAULT 0 ,
  `register_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `last_visit_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `parameters` MEDIUMTEXT NULL COMMENT 'Configurable Parameter Values' ,
  `custom_fields` MEDIUMTEXT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `molajo_sessions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_sessions` ;

CREATE  TABLE IF NOT EXISTS `molajo_sessions` (
  `session_id` VARCHAR(32) NOT NULL DEFAULT ' ' ,
  `application_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' ,
  `guest` INT(1) UNSIGNED NULL DEFAULT '1' ,
  `session_time` VARCHAR(14) NULL DEFAULT ' ' ,
  `data` LONGTEXT NULL DEFAULT NULL ,
  `userid` INT(11) NULL DEFAULT '0' ,
  `username` VARCHAR(150) NULL DEFAULT ' ' ,
  PRIMARY KEY (`session_id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_sessions_applications1` ON `molajo_sessions` (`application_id` ASC) ;

CREATE INDEX `fk_sessions_users1` ON `molajo_sessions` (`userid` ASC) ;


-- -----------------------------------------------------
-- Table `molajo_update_sites`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_update_sites` ;

CREATE  TABLE IF NOT EXISTS `molajo_update_sites` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL DEFAULT ' ' ,
  `enabled` TINYINT(1) NULL DEFAULT 0 ,
  `extension_type_id` INT(11) NOT NULL ,
  `location` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_update_sites_extension_types1` ON `molajo_update_sites` (`extension_type_id` ASC) ;


-- -----------------------------------------------------
-- Table `molajo_user_applications`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_user_applications` ;

CREATE  TABLE IF NOT EXISTS `molajo_user_applications` (
  `user_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__users.id' ,
  `application_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__applications.id' ,
  PRIMARY KEY (`application_id`, `user_id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_user_applications_users` ON `molajo_user_applications` (`user_id` ASC) ;

CREATE INDEX `fk_user_applications_applications` ON `molajo_user_applications` (`application_id` ASC) ;


-- -----------------------------------------------------
-- Table `molajo_user_view_groups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_user_view_groups` ;

CREATE  TABLE IF NOT EXISTS `molajo_user_view_groups` (
  `user_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__users.id' ,
  `view_group_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__groupings.id' ,
  PRIMARY KEY (`user_id`, `view_group_id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_user_view_groups_users1` ON `molajo_user_view_groups` (`user_id` ASC) ;

CREATE INDEX `fk_user_view_groups_view_groups1` ON `molajo_user_view_groups` (`view_group_id` ASC) ;


-- -----------------------------------------------------
-- Table `molajo_user_groups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_user_groups` ;

CREATE  TABLE IF NOT EXISTS `molajo_user_groups` (
  `user_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__users.id' ,
  `group_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__groups.id' ,
  PRIMARY KEY (`group_id`, `user_id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_molajo_user_groups_molajo_users1` ON `molajo_user_groups` (`user_id` ASC) ;

CREATE INDEX `fk_molajo_user_groups_molajo_groups1` ON `molajo_user_groups` (`group_id` ASC) ;


-- -----------------------------------------------------
-- Table `molajo_application_extensions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_application_extensions` ;

CREATE  TABLE IF NOT EXISTS `molajo_application_extensions` (
  `application_id` INT(11) NOT NULL ,
  `extension_id` INT(11) NOT NULL ,
  `extension_instance_id` INT(11) NOT NULL ,
  PRIMARY KEY (`application_id`, `extension_id`, `extension_instance_id`) )
ENGINE = InnoDB;

CREATE INDEX `fk_application_extensions_applications1` ON `molajo_application_extensions` (`application_id` ASC) ;

CREATE INDEX `fk_application_extensions_extensions1` ON `molajo_application_extensions` (`extension_id` ASC) ;

CREATE INDEX `fk_application_extensions_extension_instances1` ON `molajo_application_extensions` (`extension_instance_id` ASC) ;


-- -----------------------------------------------------
-- Table `molajo_sites`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_sites` ;

CREATE  TABLE IF NOT EXISTS `molajo_sites` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Application Primary Key' ,
  `name` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Title' ,
  `path` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'URL Alias' ,
  `base_url` VARCHAR(2048) NOT NULL DEFAULT ' ' ,
  `description` MEDIUMTEXT NULL DEFAULT NULL ,
  `parameters` MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Configurable Parameter Values' ,
  `custom_fields` MEDIUMTEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `molajo_content_categories`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_content_categories` ;

CREATE  TABLE IF NOT EXISTS `molajo_content_categories` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key' ,
  `category_id` INT(11) NOT NULL ,
  `content_id` INT(11) NOT NULL ,
  `content_table_id` INT(11) NOT NULL ,
  `ordering` INT(11) NOT NULL DEFAULT 0 ,
  `primary_content_category` TINYINT(4) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_content_categories_categories1` ON `molajo_content_categories` (`category_id` ASC) ;


-- -----------------------------------------------------
-- Table `molajo_site_applications`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `molajo_site_applications` ;

CREATE  TABLE IF NOT EXISTS `molajo_site_applications` (
  `site_id` INT(11) NOT NULL ,
  `application_id` INT(11) NOT NULL ,
  PRIMARY KEY (`site_id`, `application_id`) )
ENGINE = InnoDB;

CREATE INDEX `fk_site_applications_sites` ON `molajo_site_applications` (`site_id` ASC) ;

CREATE INDEX `fk_site_applications_applications` ON `molajo_site_applications` (`application_id` ASC) ;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
