-- -----------------------------------------------------
-- Generate from Data Model in MySQL Workbench
-- http://www.box.net/shared/rjsgbzgmal6ymedheb7t
--
-- Primary Keys: PK, NN, UN, and AI
-- Foreign Keys: NN, UN 
--
-- Build using the "Database" - "Forward Engineer" Menu Item
--
-- Use all options except:
-- DROP Object Before Each CREATE Object
-- Generate Separate CREATE INDEX Statements
--
-- Manually change `molajo`.` to `molajo_
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
-- Change AUTO_INCREMENT values to 1
--
-- SEQUENCE MATTERS: individually replace Table Creation DDL in the sequence specified
--
-- Make certain to make and test changes all the way through the installation
--  and sample data scripts and the Molajo code
-- -----------------------------------------------------

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

-- -----------------------------------------------------
-- Table 01 `molajo_action_types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_action_types` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key' ,
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  `protected` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX `idx_actions_table_title` ON `molajo_action_types` (`title` ASC) ;

-- -----------------------------------------------------
-- Table 02 `molajo_asset_types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_asset_types` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key' ,
  `title` VARCHAR(255) NOT NULL DEFAULT '' ,
  `protected` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 ,
  `source_table` VARCHAR(255) NOT NULL DEFAULT '' ,
  `component_option` VARCHAR(45) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

-- -----------------------------------------------------
-- Table 03 `molajo_extension_sites`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_extension_sites` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL DEFAULT ' ' ,
  `enabled` TINYINT(1) NOT NULL DEFAULT 0 ,
  `location` VARCHAR(2048) NOT NULL ,
  `custom_fields` MEDIUMTEXT NULL ,
  `parameters` MEDIUMTEXT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

-- -----------------------------------------------------
-- Table 04 `molajo_assets`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_assets` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Assets Primary Key' ,
  `asset_type_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `source_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Content Primary Key' ,
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  `sef_request` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'URL' ,
  `request` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'The actually link the menu item refers to.' ,
  `primary_category_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `template_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `template_page` VARCHAR(255) NOT NULL DEFAULT '' ,
  `language` CHAR(7) NOT NULL DEFAULT 'en-GB' ,
  `translation_of_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `redirect_to_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `view_group_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__groupings table' ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_assets_asset_type`
    FOREIGN KEY (`asset_type_id` )
    REFERENCES `molajo_asset_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `sef_request` ON `molajo_assets` (`sef_request` ASC) ;

CREATE INDEX `request` ON `molajo_assets` (`request` ASC) ;

CREATE INDEX `fk_assets_asset_type_index` ON `molajo_assets` (`asset_type_id` ASC) ;

-- -----------------------------------------------------
-- Table 05 `molajo_extensions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_extensions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `extension_site_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `asset_type_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `name` VARCHAR(255) NOT NULL DEFAULT '' ,
  `element` VARCHAR(100) NOT NULL DEFAULT '' ,
  `folder` VARCHAR(255) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_extensions_extension_sites`
    FOREIGN KEY (`extension_site_id` )
    REFERENCES `molajo_extension_sites` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_extensions_extension_sites_index` ON `molajo_extensions` (`extension_site_id` ASC) ;

-- -----------------------------------------------------
-- Table 06 `molajo_extension_instances`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_extension_instances` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key' ,
  `extension_id` INT(11) UNSIGNED NOT NULL ,
  `asset_type_id` INT(11) UNSIGNED NOT NULL ,
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Title' ,
  `subtitle` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Subtitle' ,
  `alias` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  `content_text` MEDIUMTEXT NULL ,
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
  `position` VARCHAR(45) NOT NULL DEFAULT '' ,
  `custom_fields` MEDIUMTEXT NULL ,
  `parameters` MEDIUMTEXT NULL COMMENT 'Attributes (Custom Fields)' ,
  `metadata` MEDIUMTEXT NULL ,
  `language` CHAR(7) NOT NULL DEFAULT 'en-GB' ,
  `translation_of_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `ordering` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Ordering' ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_extension_instances_asset_types`
    FOREIGN KEY (`asset_type_id` )
    REFERENCES `molajo_asset_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_extension_instances_extensions`
    FOREIGN KEY (`extension_id` )
    REFERENCES `molajo_extensions` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_extension_instances_asset_types_index` ON `molajo_extension_instances` (`asset_type_id` ASC) ;

CREATE INDEX `fk_extension_instances_extensions_index` ON `molajo_extension_instances` (`extension_id` ASC) ;

-- -----------------------------------------------------
-- Table 07 `molajo_sites`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_sites` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Sites Primary Key' ,
  `asset_type_id` INT(11) UNSIGNED NOT NULL ,
  `name` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Title' ,
  `path` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'URL Alias' ,
  `base_url` VARCHAR(2048) NOT NULL DEFAULT ' ' ,
  `description` MEDIUMTEXT NULL DEFAULT NULL ,
  `custom_fields` MEDIUMTEXT NULL DEFAULT NULL ,
  `parameters` MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Configurable Parameter Values' ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_sites_asset_types`
    FOREIGN KEY (`asset_type_id` )
    REFERENCES `molajo_asset_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_sites_asset_types_index` ON `molajo_sites` (`asset_type_id` ASC) ;

-- -----------------------------------------------------
-- Table 08 `molajo_applications`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_applications` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Application Primary Key' ,
  `asset_type_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `name` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Title' ,
  `path` VARCHAR(2048) NOT NULL DEFAULT ' ' COMMENT 'URL Alias' ,
  `description` MEDIUMTEXT NULL DEFAULT NULL ,
  `home_menu_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `custom_fields` MEDIUMTEXT NULL DEFAULT NULL ,
  `parameters` MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Configurable Parameter Values' ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_applications_asset_types`
    FOREIGN KEY (`asset_type_id` )
    REFERENCES `molajo_asset_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_applications_asset_types_index` ON `molajo_applications` (`asset_type_id` ASC) ;

-- -----------------------------------------------------
-- Table 09 `molajo_asset_modules`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_asset_modules` (
  `asset_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Actions Primary Key' ,
  `extension_instance_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`asset_id`, `extension_instance_id`) ,
  CONSTRAINT `fk_asset_modules_assets`
    FOREIGN KEY (`asset_id` )
    REFERENCES `molajo_assets` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_asset_modules_extension_instances`
    FOREIGN KEY (`extension_instance_id` )
    REFERENCES `molajo_extension_instances` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_asset_modules_assets_index` ON `molajo_asset_modules` (`asset_id` ASC) ;

CREATE INDEX `fk_asset_modules_extension_instances_index` ON `molajo_asset_modules` (`extension_instance_id` ASC) ;

-- -----------------------------------------------------
-- Table 10 `molajo_extension_options`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `molajo_extension_options` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key' ,
  `extension_instance_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `application_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `protected` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 ,
  `option_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `option_value_literal` VARCHAR(255) NOT NULL DEFAULT '' ,
  `option_value` VARCHAR(80) NOT NULL DEFAULT '' ,
  `ordering` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Ordering' ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_component_options_extension_instances`
    FOREIGN KEY (`extension_instance_id` )
    REFERENCES `molajo_extension_instances` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_extension_options_applications`
    FOREIGN KEY (`application_id` )
    REFERENCES `molajo_applications` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_extension_options_extension_instances_index` ON `molajo_extension_options` (`extension_instance_id` ASC) ;

CREATE INDEX `fk_extension_options_applications_index` ON `molajo_extension_options` (`application_id` ASC) ;

CREATE UNIQUE INDEX `application_extension_id` ON `molajo_extension_options` (`application_id` ASC, `extension_instance_id` ASC, `option_id` ASC, `option_value` ASC) ;

-- -----------------------------------------------------
-- Table 11 `molajo_content`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_content` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key' ,
  `extension_instance_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `asset_type_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `title` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Title' ,
  `subtitle` VARCHAR(255) NOT NULL DEFAULT ' ' COMMENT 'Subtitle' ,
  `path` VARCHAR(2048) NOT NULL DEFAULT ' ' ,
  `alias` VARCHAR(255) NOT NULL DEFAULT ' ' ,
  `content_text` MEDIUMTEXT NULL ,
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
  `root` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `parent_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `lft` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `rgt` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `lvl` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `home` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 ,
  `position` VARCHAR(45) NOT NULL DEFAULT '' ,
  `custom_fields` MEDIUMTEXT NULL ,
  `parameters` MEDIUMTEXT NULL COMMENT 'Attributes (Custom Fields)' ,
  `metadata` MEDIUMTEXT NULL ,
  `language` CHAR(7) NOT NULL DEFAULT 'en-GB' ,
  `translation_of_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `ordering` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Ordering' ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_content_asset_types`
    FOREIGN KEY (`asset_type_id` )
    REFERENCES `molajo_asset_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_content_extension_instances`
    FOREIGN KEY (`extension_instance_id` )
    REFERENCES `molajo_extension_instances` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_content_asset_types_index` ON `molajo_content` (`asset_type_id` ASC) ;

CREATE INDEX `fk_content_extension_instances_index` ON `molajo_content` (`extension_instance_id` ASC) ;

-- -----------------------------------------------------
-- Table 12 `molajo_users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `asset_type_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `username` VARCHAR(255) NOT NULL ,
  `first_name` VARCHAR(100) NULL DEFAULT '' ,
  `last_name` VARCHAR(150) NULL DEFAULT '' ,
  `content_text` MEDIUMTEXT NULL ,
  `email` VARCHAR(255) NULL DEFAULT '  ' ,
  `password` VARCHAR(100) NOT NULL DEFAULT '  ' ,
  `block` TINYINT(4) NOT NULL DEFAULT 0 ,
  `activation` VARCHAR(100) NOT NULL DEFAULT '' ,
  `send_email` TINYINT(4) NOT NULL DEFAULT 0 ,
  `register_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `last_visit_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `custom_fields` MEDIUMTEXT NULL ,
  `parameters` MEDIUMTEXT NULL COMMENT 'Configurable Parameter Values' ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_users_asset_types`
    FOREIGN KEY (`asset_type_id` )
    REFERENCES `molajo_asset_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_users_asset_types_index` ON `molajo_users` (`asset_type_id` ASC) ;

CREATE INDEX `last_name_first_name` ON `molajo_users` (`last_name` ASC, `first_name` ASC) ;

CREATE UNIQUE INDEX `username` ON `molajo_users` (`username`) ;

CREATE UNIQUE INDEX `email` ON `molajo_users` (`email`) ;

-- -----------------------------------------------------
-- Table 13 `molajo_site_applications`
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

CREATE INDEX `fk_site_applications_sites_index` ON `molajo_site_applications` (`site_id` ASC) ;

CREATE INDEX `fk_site_applications_applications_index` ON `molajo_site_applications` (`application_id` ASC) ;

-- -----------------------------------------------------
-- Table 14 `molajo_site_extension_instances`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_site_extension_instances` (
  `site_id` INT(11) UNSIGNED NOT NULL ,
  `extension_instance_id` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`site_id`, `extension_instance_id`) ,
  CONSTRAINT `fk_site_extension_instances_sites`
    FOREIGN KEY (`site_id` )
    REFERENCES `molajo_sites` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_site_extension_instances_extension_instances`
    FOREIGN KEY (`extension_instance_id` )
    REFERENCES `molajo_extension_instances` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_site_extension_instances_sites_index` ON `molajo_site_extension_instances` (`site_id` ASC) ;

CREATE INDEX `fk_site_extension_instances_extension_instances_index` ON `molajo_site_extension_instances` (`extension_instance_id` ASC) ;

-- -----------------------------------------------------
-- Table 15 `molajo_application_extension_instances`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_application_extension_instances` (
  `application_id` INT(11) UNSIGNED NOT NULL ,
  `extension_instance_id` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`application_id`, `extension_instance_id`) ,
  CONSTRAINT `fk_application_extensions_applications`
    FOREIGN KEY (`application_id` )
    REFERENCES `molajo_applications` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_application_extension_instances_extension_instances`
    FOREIGN KEY (`extension_instance_id` )
    REFERENCES `molajo_extension_instances` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_application_extensions_applications_index` ON `molajo_application_extension_instances` (`application_id` ASC) ;

CREATE INDEX `fk_application_extension_instances_extension_instances_index` ON `molajo_application_extension_instances` (`extension_instance_id` ASC) ;

-- -----------------------------------------------------
-- Table 16 `molajo_sessions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_sessions` (
  `session_id` VARCHAR(32) NOT NULL ,
  `application_id` INT(11) UNSIGNED NOT NULL ,
  `session_time` VARCHAR(14) NULL DEFAULT ' ' ,
  `data` LONGTEXT NULL ,
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`session_id`) ,
  CONSTRAINT `fk_sessions_applications`
    FOREIGN KEY (`application_id` )
    REFERENCES `molajo_applications` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_sessions_applications_index` ON `molajo_sessions` (`application_id` ASC) ;

-- -----------------------------------------------------
-- Table 17 `molajo_user_applications`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_user_applications` (
  `user_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__users.id' ,
  `application_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__applications.id' ,
  PRIMARY KEY (`application_id`, `user_id`) ,
  CONSTRAINT `fk_user_applications_users`
    FOREIGN KEY (`user_id` )
    REFERENCES `molajo_users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_applications_applications`
    FOREIGN KEY (`application_id` )
    REFERENCES `molajo_applications` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_user_applications_users_index` ON `molajo_user_applications` (`user_id` ASC) ;

CREATE INDEX `fk_user_applications_applications_index` ON `molajo_user_applications` (`application_id` ASC) ;

-- -----------------------------------------------------
-- Table 18 `molajo_user_groups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_user_groups` (
  `user_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__users.id' ,
  `group_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__content.id' ,
  PRIMARY KEY (`group_id`, `user_id`) ,
  CONSTRAINT `fk_user_groups_users`
    FOREIGN KEY (`user_id` )
    REFERENCES `molajo_users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_groups_groups`
    FOREIGN KEY (`group_id` )
    REFERENCES `molajo_content` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_user_groups_users_index` ON `molajo_user_groups` (`user_id` ASC) ;

CREATE INDEX `fk_user_groups_groups_index` ON `molajo_user_groups` (`group_id` ASC) ;

-- -----------------------------------------------------
-- Table 19 `molajo_view_groups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_view_groups` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `view_group_name_list` TEXT NOT NULL ,
  `view_group_id_list` TEXT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

-- -----------------------------------------------------
-- Table 20 `molajo_group_view_groups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_group_view_groups` (
  `group_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK to the #__group table.' ,
  `view_group_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK to the #__groupings table.' ,
  PRIMARY KEY (`view_group_id`, `group_id`) ,
  CONSTRAINT `fk_group_view_groups_view_groups`
    FOREIGN KEY (`view_group_id` )
    REFERENCES `molajo_view_groups` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_group_view_groups_groups`
    FOREIGN KEY (`group_id` )
    REFERENCES `molajo_content` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_group_view_groups_view_groups_index` ON `molajo_group_view_groups` (`view_group_id` ASC) ;

CREATE INDEX `fk_group_view_groups_groups_index` ON `molajo_group_view_groups` (`group_id` ASC) ;

-- -----------------------------------------------------
-- Table 21 `molajo_user_view_groups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_user_view_groups` (
  `user_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__users.id' ,
  `view_group_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__content.id' ,
  PRIMARY KEY (`view_group_id`, `user_id`) ,
  CONSTRAINT `fk_user_view_groups_users`
    FOREIGN KEY (`user_id` )
    REFERENCES `molajo_users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_view_groups_view_groups`
    FOREIGN KEY (`view_group_id` )
    REFERENCES `molajo_view_groups` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_user_view_groups_users_index` ON `molajo_user_view_groups` (`user_id` ASC) ;

CREATE INDEX `fk_user_view_groups_view_groups_index` ON `molajo_user_view_groups` (`view_group_id` ASC) ;

-- -----------------------------------------------------
-- Table 22 `molajo_view_group_permissions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_view_group_permissions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `view_group_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__content.id' ,
  `asset_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__assets.id' ,
  `action_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__actions.id' ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_view_group_permissions_view_groups`
    FOREIGN KEY (`view_group_id` )
    REFERENCES `molajo_view_groups` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_view_group_permissions_actions`
    FOREIGN KEY (`action_id` )
    REFERENCES `molajo_action_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_view_group_permissions_assets`
    FOREIGN KEY (`asset_id` )
    REFERENCES `molajo_assets` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_view_group_permissions_view_groups_index` ON `molajo_view_group_permissions` (`view_group_id` ASC) ;

CREATE INDEX `fk_view_group_permissions_actions_index` ON `molajo_view_group_permissions` (`action_id` ASC) ;

CREATE INDEX `fk_view_group_permissions_assets_index` ON `molajo_view_group_permissions` (`asset_id` ASC) ;

-- -----------------------------------------------------
-- Table 23 `molajo_group_permissions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_group_permissions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `group_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #_groups.id' ,
  `asset_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__assets.id' ,
  `action_id` INT(11) UNSIGNED NOT NULL COMMENT 'Foreign Key to #__actions.id' ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_group_permissions_actions`
    FOREIGN KEY (`action_id` )
    REFERENCES `molajo_action_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_group_permissions_content`
    FOREIGN KEY (`group_id` )
    REFERENCES `molajo_content` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_group_permissions_assets`
    FOREIGN KEY (`asset_id` )
    REFERENCES `molajo_assets` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_group_permissions_actions_index` ON `molajo_group_permissions` (`action_id` ASC) ;

CREATE INDEX `fk_group_permissions_content_index` ON `molajo_group_permissions` (`group_id` ASC) ;

CREATE INDEX `fk_group_permissions_assets_index` ON `molajo_group_permissions` (`asset_id` ASC) ;

-- -----------------------------------------------------
-- Table 24 `molajo_asset_categories`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `molajo_asset_categories` (
  `asset_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `category_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`asset_id`, `category_id`) ,
  CONSTRAINT `fk_asset_categories_assets`
    FOREIGN KEY (`asset_id` )
    REFERENCES `molajo_assets` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_asset_categories_categories`
    FOREIGN KEY (`category_id` )
    REFERENCES `molajo_content` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `fk_asset_categories_assets_index` ON `molajo_asset_categories` (`asset_id` ASC) ;

CREATE INDEX `fk_asset_categories_categories_index` ON `molajo_asset_categories` (`category_id` ASC) ;
