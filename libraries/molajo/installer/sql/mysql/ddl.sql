-- -----------------------------------------------------
-- Generate from Data Model in MySQL Workbench
-- http://www.box.net/shared/rjsgbzgmal6ymedheb7t
--
-- Primary Keys: PK, NN, UN, and AI
-- Foreign Keys: NN, UN 
--
-- Build using the "Database" - "Forward Engineer" Menu Item
--
-- Use options:
-- DROP object Before Each CREATE Object (during development)
-- Skip creation of FOREIGN KEYS
-- Generate Separate CREATE INDEX Statements
--
-- Manually change `molajo_ to `molajo_
--
-- Remove these three statements from top of script:
--
-- SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';
-- CREATE SCHEMA IF NOT EXISTS `molajo` DEFAULT CHARACTER SET utf8 ;
-- USE `molajo` ;
-- 
-- Remove this line from bottom of script:
-- SET SQL_MODE=@OLD_SQL_MODE;
--
-- SEQUENCE MATTERS: individually replace Table Creation DDL in the sequence specified
--
-- Make certain to test changes all the way through the installation and sample data
-- -----------------------------------------------------

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

-- -----------------------------------------------------
-- Table 01 `molajo_actions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_actions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key' ,
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX `idx_actions_table_title` ON `molajo_actions` (`title` ASC) ;

-- -----------------------------------------------------
-- Table 02 `molajo_applications`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_applications` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Application Primary Key' ,
  `name` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Title' ,
  `path` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'URL Alias' ,
  `description` MEDIUMTEXT NULL DEFAULT NULL ,
  `parameters` MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Configurable Parameter Values' ,
  `custom_fields` MEDIUMTEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

-- -----------------------------------------------------
-- Table 03 `molajo_source_tables`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_source_tables` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key' ,
  `source_table` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  `option` VARCHAR(45) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

-- -----------------------------------------------------
-- Table 04 `molajo_categories`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_categories` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(255) NOT NULL DEFAULT '' ,
  `subtitle` VARCHAR(255) NOT NULL DEFAULT '' ,
  `alias` VARCHAR(255) NOT NULL DEFAULT '' ,
  `content_text` MEDIUMTEXT NULL ,
  `featured` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 ,
  `stickied` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 ,
  `status` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 ,
  `start_publishing_datetime` DATETIME NULL DEFAULT '0000-00-00 00:00:00' ,
  `stop_publishing_datetime` DATETIME NULL DEFAULT '0000-00-00 00:00:00' ,
  `version` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `version_of_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `status_prior_to_version` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `checked_out_by` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `checked_out_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created_by` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `created_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `modified_by` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `modified_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `parent_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `lft` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `rgt` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `level` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `metakey` TEXT NULL COMMENT 'The meta keywords for the page.' ,
  `metadesc` TEXT NULL COMMENT 'The meta description for the page.' ,
  `metadata` TEXT NULL COMMENT 'JSON encoded metadata properties.' ,
  `custom_fields` MEDIUMTEXT NULL ,
  `parameters` MEDIUMTEXT NULL COMMENT 'Configurable Parameter Values' ,
  `language` CHAR(7) NOT NULL DEFAULT 'en-GB' ,
  `translation_of_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `ordering` INT(11) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `cat_idx` ON `molajo_categories` (`status` ASC) ;

CREATE INDEX `idx_checkout` ON `molajo_categories` (`checked_out_by` ASC) ;

CREATE INDEX `idx_alias` ON `molajo_categories` (`alias` ASC) ;

CREATE INDEX `idx_language` ON `molajo_categories` (`language` ASC) ;

-- -----------------------------------------------------
-- Table 05 `molajo_extension_types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_extension_types` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key' ,
  `extension_type` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX `idx_actions_table_title_index` ON `molajo_extension_types` (`extension_type` ASC) ;

-- -----------------------------------------------------
-- Table 06 `molajo_update_sites`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_update_sites` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL DEFAULT ' ' ,
  `enabled` TINYINT(1) NOT NULL DEFAULT 0 ,
  `location` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

-- -----------------------------------------------------
-- Table 07 `molajo_groups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_groups` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Group Primary Key' ,
  `title` VARCHAR(255) NOT NULL DEFAULT '  ' ,
  `subtitle` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  `description` MEDIUMTEXT NOT NULL ,
  `type` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 ,
  `protected` TINYINT(4) NOT NULL DEFAULT 0 COMMENT 'If true, protects group from system removal via the interface.' ,
  `custom_fields` MEDIUMTEXT NULL ,
  `parameters` MEDIUMTEXT NULL ,
  `parent_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Parent ID' ,
  `lft` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Nested set lft.' ,
  `rgt` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Nested set rgt.' ,
  `ordering` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX `idx_usergroup_parent_title_lookup` ON `molajo_groups` (`parent_id` ASC, `title` ASC, `type` ASC) ;

CREATE INDEX `idx_usergroup_title_lookup` ON `molajo_groups` (`title` ASC) ;

CREATE INDEX `idx_usergroup_adjacency_lookup` ON `molajo_groups` (`parent_id` ASC) ;

CREATE INDEX `idx_usergroup_nested_set_lookup` USING BTREE ON `molajo_groups` (`lft` ASC, `rgt` ASC) ;

-- -----------------------------------------------------
-- Table 08 `molajo_view_groups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_view_groups` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `view_group_name_list` TEXT NOT NULL ,
  `view_group_id_list` TEXT NOT NULL ,
  `type` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

-- -----------------------------------------------------
-- Table 09 `molajo_users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(255) NOT NULL ,
  `first_name` VARCHAR(100) NULL ,
  `last_name` VARCHAR(150) NULL ,
  `content_text` MEDIUMTEXT NULL ,
  `email` VARCHAR(255) NULL DEFAULT '  ' ,
  `password` VARCHAR(100) NOT NULL DEFAULT '  ' ,
  `block` TINYINT(4) NOT NULL DEFAULT 0 ,
  `activation` VARCHAR(100) NOT NULL DEFAULT '' ,
  `send_email` TINYINT(4) NOT NULL DEFAULT 0 ,
  `register_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `last_visit_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `parameters` MEDIUMTEXT NULL COMMENT 'Configurable Parameter Values' ,
  `custom_fields` MEDIUMTEXT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

-- -----------------------------------------------------
-- Table 10 `molajo_sites`
-- -----------------------------------------------------
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
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

-- -----------------------------------------------------
-- Table 11 `molajo_extensions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_extensions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `extension_type_id` INT(11) UNSIGNED NOT NULL ,
  `element` VARCHAR(100) NOT NULL ,
  `folder` VARCHAR(255) NOT NULL ,
  `update_site_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_extensions_extension_types1`
    FOREIGN KEY (`extension_type_id` )
    REFERENCES `molajo_extension_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_extensions_update_sites1`
    FOREIGN KEY (`update_site_id` )
    REFERENCES `molajo_update_sites` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_extensions_extension_types2` ON `molajo_extensions` (`extension_type_id` ASC) ;

CREATE INDEX `fk_extensions_update_sites2` ON `molajo_extensions` (`update_site_id` ASC) ;

-- -----------------------------------------------------
-- Table 12 `molajo_assets`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_assets` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Assets Primary Key' ,
  `source_table_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `source_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Content Primary Key' ,
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  `sef_request` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'URL' ,
  `request` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'The actually link the menu item refers to.' ,
  `primary_category_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `language` CHAR(7) NOT NULL DEFAULT 'en-GB' ,
  `translation_of_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `redirect_to_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `view_group_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__groupings table' ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_assets_source_tables1`
    FOREIGN KEY (`source_table_id` )
    REFERENCES `molajo_source_tables` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_assets_source_tables2` ON `molajo_assets` (`source_table_id` ASC) ;

-- -----------------------------------------------------
-- Table 13 `molajo_extension_instances`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_extension_instances` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key' ,
  `extension_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Primary Key for Component Content' ,
  `extension_type_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Title' ,
  `subtitle` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Subtitle' ,
  `alias` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  `content_text` MEDIUMTEXT NULL COMMENT 'Content Primary Text Field, can include break to designate Introductory and Full text' ,
  `protected` TINYINT(4) NOT NULL DEFAULT 0 ,
  `featured` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 ,
  `stickied` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 ,
  `status` TINYINT(4) NOT NULL DEFAULT 0 COMMENT 'Published State 2: Archived 1: Published 0: Unpublished -1: Trashed -2: Spam -10 Version' ,
  `start_publishing_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish Begin Date and Time' ,
  `stop_publishing_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish End Date and Time' ,
  `version` INT(11) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Version Number' ,
  `version_of_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Primary ID for this Version' ,
  `status_prior_to_version` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'State value prior to creating this version copy and changing the state to Version' ,
  `created_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Created Date and Time' ,
  `created_by` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Created by User ID' ,
  `modified_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modified Date' ,
  `modified_by` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Modified By User ID' ,
  `checked_out_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Checked out Date and Time' ,
  `checked_out_by` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Checked out by User Id' ,
  `custom_fields` MEDIUMTEXT NULL ,
  `parameters` MEDIUMTEXT NULL COMMENT 'Attributes (Custom Fields)' ,
  `position` VARCHAR(50) NOT NULL DEFAULT ' ' COMMENT 'User DEFAULT 1-DEFAULT 0-Not DEFAULT' ,
  `language` CHAR(7) NULL DEFAULT 'en-GB' ,
  `translation_of_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `ordering` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Ordering' ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_extension_instances_extensions1`
    FOREIGN KEY (`extension_id` )
    REFERENCES `molajo_extensions` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_extension_instances_extension_types1`
    FOREIGN KEY (`extension_type_id` )
    REFERENCES `molajo_extension_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `idx_component_component_id_id` ON `molajo_extension_instances` (`extension_id` ASC, `id` ASC) ;

CREATE INDEX `idx_checkout` ON `molajo_extension_instances` (`checked_out_by` ASC) ;

CREATE INDEX `idx_state` ON `molajo_extension_instances` (`status` ASC) ;

CREATE INDEX `idx_createdby` ON `molajo_extension_instances` (`created_by` ASC) ;

CREATE INDEX `fk_extension_instances_extensions2` ON `molajo_extension_instances` (`extension_id` ASC) ;

CREATE INDEX `fk_extension_instances_extension_types2` ON `molajo_extension_instances` (`extension_type_id` ASC) ;

-- -----------------------------------------------------
-- Table 14 `molajo_extension_instance_options`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_extension_instance_options` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key' ,
  `extension_instance_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `extension_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Primary Key for Component Content' ,
  `extension_type_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Title' ,
  `subtitle` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Subtitle' ,
  `alias` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  `content_text` MEDIUMTEXT NULL COMMENT 'Content Primary Text Field, can include break to designate Introductory and Full text' ,
  `protected` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 ,
  `featured` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 ,
  `stickied` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 ,
  `status` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Published State 2: Archived 1: Published 0: Unpublished -1: Trashed -2: Spam -10 Version' ,
  `start_publishing_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish Begin Date and Time' ,
  `stop_publishing_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish End Date and Time' ,
  `version` INT(11) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Version Number' ,
  `version_of_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Primary ID for this Version' ,
  `status_prior_to_version` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'State value prior to creating this version copy and changing the state to Version' ,
  `created_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Created Date and Time' ,
  `created_by` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Created by User ID' ,
  `modified_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modified Date' ,
  `modified_by` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Modified By User ID' ,
  `checked_out_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Checked out Date and Time' ,
  `checked_out_by` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Checked out by User Id' ,
  `image` VARCHAR(255) NOT NULL ,
  `option_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `option_value` VARCHAR(80) NOT NULL DEFAULT '' ,
  `option_value_literal` VARCHAR(255) NOT NULL DEFAULT '' ,
  `trigger_asset_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `position` VARCHAR(50) NOT NULL DEFAULT ' ' COMMENT 'User DEFAULT 1-DEFAULT 0-Not DEFAULT' ,
  `menu_item_type` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 ,
  `parent_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `lft` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `rgt` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `level` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `home` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 ,
  `custom_fields` MEDIUMTEXT NULL ,
  `parameters` MEDIUMTEXT NULL COMMENT 'Attributes (Custom Fields)' ,
  `language` CHAR(7) NOT NULL DEFAULT 'en-GB' ,
  `translation_of_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `ordering` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Ordering' ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_extension_instance_options_extension_instances1`
    FOREIGN KEY (`extension_instance_id` )
    REFERENCES `molajo_extension_instances` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_extension_instance_options_extensions1`
    FOREIGN KEY (`extension_id` )
    REFERENCES `molajo_extensions` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_extension_instance_options_extension_types1`
    FOREIGN KEY (`extension_type_id` )
    REFERENCES `molajo_extension_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_extension_instance_options_extension_instances2` ON `molajo_extension_instance_options` (`extension_instance_id` ASC) ;

CREATE INDEX `fk_extension_instance_options_extensions2` ON `molajo_extension_instance_options` (`extension_id` ASC) ;

CREATE INDEX `fk_extension_instance_options_extension_types2` ON `molajo_extension_instance_options` (`extension_type_id` ASC) ;

 
-- -----------------------------------------------------
-- Table 15 `molajo_group_view_groups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_group_view_groups` (
  `group_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK to the #__group table.' ,
  `view_group_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK to the #__groupings table.' ,
  PRIMARY KEY (`view_group_id`, `group_id`) ,
  CONSTRAINT `fk_group_view_groups_groups1`
    FOREIGN KEY (`group_id` )
    REFERENCES `molajo_groups` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_group_view_groups_view_groups1`
    FOREIGN KEY (`view_group_id` )
    REFERENCES `molajo_view_groups` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_group_view_groups_groups2` ON `molajo_group_view_groups` (`group_id` ASC) ;

CREATE INDEX `fk_group_view_groups_view_groups2` ON `molajo_group_view_groups` (`view_group_id` ASC) ;

-- -----------------------------------------------------
-- Table 16 `molajo_view_group_permissions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_view_group_permissions` (
  `view_group_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__groups.id' ,
  `asset_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__assets.id' ,
  `action_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__actions.id' ,
  PRIMARY KEY (`view_group_id`, `asset_id`, `action_id`) ,
  CONSTRAINT `fk_view_group_permissions_view_groups1`
    FOREIGN KEY (`view_group_id` )
    REFERENCES `molajo_view_groups` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_view_group_permissions_actions1`
    FOREIGN KEY (`action_id` )
    REFERENCES `molajo_actions` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_view_group_permissions_assets1`
    FOREIGN KEY (`asset_id` )
    REFERENCES `molajo_assets` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_view_group_permissions_view_groups2` ON `molajo_view_group_permissions` (`view_group_id` ASC) ;

CREATE INDEX `fk_view_group_permissions_actions2` ON `molajo_view_group_permissions` (`action_id` ASC) ;

CREATE INDEX `fk_view_group_permissions_assets2` ON `molajo_view_group_permissions` (`asset_id` ASC) ;

-- -----------------------------------------------------
-- Table 17 `molajo_group_permissions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_group_permissions` (
  `group_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #_groups.id' ,
  `asset_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__assets.id' ,
  `action_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__actions.id' ,
  PRIMARY KEY (`action_id`, `asset_id`, `group_id`) ,
  CONSTRAINT `fk_group_permissions_groups1`
    FOREIGN KEY (`group_id` )
    REFERENCES `molajo_groups` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_group_permissions_assets1`
    FOREIGN KEY (`asset_id` )
    REFERENCES `molajo_assets` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_group_permissions_actions1`
    FOREIGN KEY (`action_id` )
    REFERENCES `molajo_actions` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_group_permissions_groups2` ON `molajo_group_permissions` (`group_id` ASC) ;

CREATE INDEX `fk_group_permissions_assets2` ON `molajo_group_permissions` (`asset_id` ASC) ;

CREATE INDEX `fk_group_permissions_actions2` ON `molajo_group_permissions` (`action_id` ASC) ;

-- -----------------------------------------------------
-- Table 18 `molajo_content`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_content` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key' ,
  `extension_instance_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `title` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Title' ,
  `subtitle` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Subtitle' ,
  `alias` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URL Alias' ,
  `content_type` TINYINT(4) NOT NULL DEFAULT 0 COMMENT 'Content Type: Links to #__configuration.option_id = 10 and component_option values matching ' ,
  `content_text` MEDIUMTEXT NULL COMMENT 'Content Primary Text Field, can include break to designate Introductory and Full text' ,
  `content_link` VARCHAR(2083) NULL COMMENT 'Content Link for Weblink or Newsfeed Field' ,
  `content_email_address` VARCHAR(255) NULL COMMENT 'Content Email Field' ,
  `content_numeric_value` TINYINT(4) NULL COMMENT 'Content Numeric Value, ex. vote on poll' ,
  `content_file` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Content Network Path to File' ,
  `user_default` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'User DEFAULT 1-DEFAULT 0-Not DEFAULT' ,
  `category_default` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Category DEFAULT 1-DEFAULT 0-Not DEFAULT' ,
  `featured` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Featured 1-Featured 0-Not Featured' ,
  `stickied` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Stickied 1-Stickied 0-Not Stickied' ,
  `status` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Published State 2: Archived 1: Published 0: Unpublished -1: Trashed -2: Spam -10 Version' ,
  `start_publishing_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish Begin Date and Time' ,
  `stop_publishing_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish End Date and Time' ,
  `version` INT(11) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Version Number' ,
  `version_of_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Primary ID for this Version' ,
  `status_prior_to_version` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'State value prior to creating this version copy and changing the state to Version' ,
  `created_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Created Date and Time' ,
  `created_by` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Created by User ID' ,
  `created_by_alias` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Created by Alias' ,
  `created_by_email` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Created By Email Address' ,
  `created_by_website` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Created By Website' ,
  `created_by_ip_address` CHAR(15) NOT NULL DEFAULT '' COMMENT 'Created By IP Address' ,
  `created_by_referer` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Created By Referer' ,
  `modified_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modified Date' ,
  `modified_by` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Modified By User ID' ,
  `checked_out_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Checked out Date and Time' ,
  `checked_out_by` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Checked out by User Id' ,
  `parent_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Nested set parent' ,
  `lft` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Nested set lft' ,
  `rgt` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Nested set rgt' ,
  `level` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'The cached level in the nested tree' ,
  `metakey` TEXT NULL DEFAULT '' COMMENT 'Meta Key' ,
  `metadesc` TEXT NULL COMMENT 'Meta Description' ,
  `metadata` TEXT NULL COMMENT 'Meta Data' ,
  `custom_fields` MEDIUMTEXT NULL COMMENT 'Attributes (Custom Fields)' ,
  `parameters` MEDIUMTEXT NULL COMMENT 'Configurable Parameter Values' ,
  `language` CHAR(7) NULL DEFAULT 'en-GB' ,
  `translation_of_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `ordering` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Ordering' ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_content_extension_instances1`
    FOREIGN KEY (`extension_instance_id` )
    REFERENCES `molajo_extension_instances` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `idx_component_component_id_id` ON `molajo_content` (`id` ASC) ;

CREATE INDEX `idx_checkout` ON `molajo_content` (`checked_out_by` ASC) ;

CREATE INDEX `idx_state` ON `molajo_content` (`status` ASC) ;

CREATE INDEX `idx_createdby` ON `molajo_content` (`created_by` ASC) ;

CREATE INDEX `idx_featured_catid` ON `molajo_content` (`featured` ASC) ;

CREATE INDEX `idx_stickied_catid` ON `molajo_content` (`stickied` ASC) ;

CREATE INDEX `fk_content_extension_instances2` ON `molajo_content` (`extension_instance_id` ASC) ;

-- -----------------------------------------------------
-- Table 19 `molajo_sessions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_sessions` (
  `session_id` VARCHAR(32) NOT NULL ,
  `application_id` INT(11) UNSIGNED NOT NULL ,
  `session_time` VARCHAR(14) NULL DEFAULT ' ' ,
  `data` LONGTEXT NULL ,
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`session_id`) ,
  CONSTRAINT `fk_sessions_applications1`
    FOREIGN KEY (`application_id` )
    REFERENCES `molajo_applications` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_sessions_applications2` ON `molajo_sessions` (`application_id` ASC) ;

-- -----------------------------------------------------
-- Table 20 `molajo_user_applications`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_user_applications` (
  `user_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__users.id' ,
  `application_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__applications.id' ,
  PRIMARY KEY (`application_id`, `user_id`) ,
  CONSTRAINT `fk_user_applications_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `molajo_users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_applications_applications1`
    FOREIGN KEY (`application_id` )
    REFERENCES `molajo_applications` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_user_applications_users` ON `molajo_user_applications` (`user_id` ASC) ;

CREATE INDEX `fk_user_applications_applications` ON `molajo_user_applications` (`application_id` ASC) ;

-- -----------------------------------------------------
-- Table 21 `molajo_user_view_groups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_user_view_groups` (
  `user_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__users.id' ,
  `view_group_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__groupings.id' ,
  PRIMARY KEY (`user_id`, `view_group_id`) ,
  CONSTRAINT `fk_user_view_groups_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `molajo_users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_view_groups_view_groups1`
    FOREIGN KEY (`view_group_id` )
    REFERENCES `molajo_view_groups` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_user_view_groups_users2` ON `molajo_user_view_groups` (`user_id` ASC) ;

CREATE INDEX `fk_user_view_groups_view_groups2` ON `molajo_user_view_groups` (`view_group_id` ASC) ;

-- -----------------------------------------------------
-- Table 22 `molajo_user_groups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_user_groups` (
  `user_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__users.id' ,
  `group_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__groups.id' ,
  PRIMARY KEY (`group_id`, `user_id`) ,
  CONSTRAINT `fk_user_groups_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `molajo_users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_groups_groups1`
    FOREIGN KEY (`group_id` )
    REFERENCES `molajo_groups` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_molajo_user_groups_molajo_users2` ON `molajo_user_groups` (`user_id` ASC) ;

CREATE INDEX `fk_molajo_user_groups_molajo_groups2` ON `molajo_user_groups` (`group_id` ASC) ;

-- -----------------------------------------------------
-- Table 23 `molajo_application_extension_instances`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_application_extension_instances` (
  `application_id` INT(11) UNSIGNED NOT NULL ,
  `extension_instance_id` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`application_id`, `extension_instance_id`) ,
  CONSTRAINT `fk_application_extensions_applications1`
    FOREIGN KEY (`application_id` )
    REFERENCES `molajo_applications` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_extensions_extension_instances1`
    FOREIGN KEY (`extension_instance_id` )
    REFERENCES `molajo_extension_instances` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_application_extensions_applications2` ON `molajo_application_extension_instances` (`application_id` ASC) ;

CREATE INDEX `fk_application_extensions_extension_instances2` ON `molajo_application_extension_instances` (`extension_instance_id` ASC) ;

-- -----------------------------------------------------
-- Table 24 `molajo_asset_categories`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_asset_categories` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key' ,
  `asset_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `category_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `ordering` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_content_categories_categories1`
    FOREIGN KEY (`category_id` )
    REFERENCES `molajo_categories` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_asset_categories_assets1`
    FOREIGN KEY (`asset_id` )
    REFERENCES `molajo_assets` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_content_categories_categories2` ON `molajo_asset_categories` (`category_id` ASC) ;

CREATE INDEX `fk_asset_categories_assets2` ON `molajo_asset_categories` (`asset_id` ASC) ;

-- -----------------------------------------------------
-- Table 25 `molajo_site_applications`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_site_applications` (
  `site_id` INT(11) UNSIGNED NOT NULL ,
  `application_id` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`site_id`, `application_id`) ,
  CONSTRAINT `fk_site_applications_sites`
    FOREIGN KEY (`site_id` )
    REFERENCES `molajo_sites` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_site_applications_applications`
    FOREIGN KEY (`application_id` )
    REFERENCES `molajo_applications` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_site_applications_sites2` ON `molajo_site_applications` (`site_id` ASC) ;

CREATE INDEX `fk_site_applications_applications2` ON `molajo_site_applications` (`application_id` ASC) ;

-- -----------------------------------------------------
-- Table 26 `molajo_site_extension_instances`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_site_extension_instances` (
  `site_id` INT(11) UNSIGNED NOT NULL ,
  `extension_instance_id` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`site_id`, `extension_instance_id`) ,
  CONSTRAINT `fk_site_extension_instances_sites1`
    FOREIGN KEY (`site_id` )
    REFERENCES `molajo_sites` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_site_extension_instances_extension_instances1`
    FOREIGN KEY (`extension_instance_id` )
    REFERENCES `molajo_extension_instances` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_site_extension_instances_sites2` ON `molajo_site_extension_instances` (`site_id` ASC) ;

CREATE INDEX `fk_site_extension_instances_extension_instances2` ON `molajo_site_extension_instances` (`extension_instance_id` ASC) ;
