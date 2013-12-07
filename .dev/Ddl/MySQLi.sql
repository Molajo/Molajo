-- phpMyAdmin SQL Dump
-- version 3.5.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 25, 2013 at 06:24 PM
-- Server version: 5.5.29
-- PHP Version: 5.4.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `molajo_site2`
--

-- --------------------------------------------------------

--
-- Table structure for table `molajo_actions`
--

CREATE TABLE `molajo_actions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key',
  `title` varchar(255) NOT NULL DEFAULT ' ',
  `protected` tinyint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_actions_table_title` (`title`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_applications`
--

CREATE TABLE `molajo_applications` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Application Primary Key',
  `catalog_type_id` int(11) unsigned NOT NULL DEFAULT '2000' COMMENT 'Catalog Type ID',
  `name` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Application Name',
  `path` varchar(2048) NOT NULL DEFAULT ' ' COMMENT 'Application Path',
  `description` longtext COMMENT 'Application Description',
  `customfields` longtext COMMENT 'Custom Fields for this Application',
  `parameters` longtext COMMENT 'Custom Parameters for this Application',
  `metadata` longtext COMMENT 'Metadata definitions for this Application',
  PRIMARY KEY (`id`),
  KEY `fk_applications_catalog_types_index` (`catalog_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_application_extension_instances`
--

CREATE TABLE `molajo_application_extension_instances` (
  `application_id` int(11) unsigned NOT NULL,
  `extension_instance_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`application_id`,`extension_instance_id`),
  KEY `fk_application_extensions_applications_index` (`application_id`),
  KEY `fk_application_extension_instances_extension_instances_index` (`extension_instance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_catalog`
--

CREATE TABLE `molajo_catalog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Catalog Primary Key',
  `application_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Application ID',
  `catalog_type_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Catalog Type ID',
  `source_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Primary Key of source data stored in table associated with Catalog Type ID Model',
  `enabled` tinyint(6) NOT NULL DEFAULT '0' COMMENT 'Enabled - 1 or Disabled - 0',
  `redirect_to_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Redirect to Catalog ID',
  `sef_request` varchar(2048) NOT NULL DEFAULT ' ' COMMENT 'SEF Request',
  `page_type` varchar(255) NOT NULL COMMENT 'Menu Item Type includes such values as Item, List, or a specific Menuitem Type',
  `extension_instance_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Extension Instance ID',
  `view_group_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'View Group ID',
  `primary_category_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Primary Category ID',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_catalog_catalog_types` (`application_id`,`catalog_type_id`,`source_id`,`enabled`,`redirect_to_id`,`page_type`),
  KEY `sef_request` (`application_id`,`enabled`,`redirect_to_id`),
  KEY `index_catalog_application_id` (`application_id`),
  KEY `index_catalog_catalog_type_id` (`catalog_type_id`),
  KEY `index_catalog_view_group_id` (`view_group_id`),
  KEY `index_catalog_primary_category_id` (`primary_category_id`),
  KEY `index_catalog_extension_instance_id` (`extension_instance_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29160 ;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_catalog_activity`
--

CREATE TABLE `molajo_catalog_activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catalog_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `action_id` int(11) NOT NULL,
  `rating` tinyint(6) unsigned DEFAULT NULL,
  `activity_datetime` datetime DEFAULT NULL,
  `ip_address` char(15) NOT NULL DEFAULT '',
  `customfields` longtext,
  PRIMARY KEY (`id`),
  KEY `catalog_activity_catalog_index` (`catalog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_catalog_categories`
--

CREATE TABLE `molajo_catalog_categories` (
  `catalog_id` int(11) unsigned NOT NULL DEFAULT '0',
  `category_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`catalog_id`,`category_id`),
  KEY `fk_catalog_categories_catalog_index` (`catalog_id`),
  KEY `fk_catalog_categories_categories_index` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_catalog_types`
--

CREATE TABLE `molajo_catalog_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Catalog Types Primary Key',
  `primary_category_id` int(11) NOT NULL COMMENT 'Primary Category ID',
  `title` varchar(255) NOT NULL COMMENT 'Catalog Type Title',
  `alias` varchar(255) NOT NULL COMMENT 'Catalog Type Alias',
  `model_type` varchar(255) NOT NULL COMMENT 'Catalog Type Model Type',
  `model_name` varchar(255) NOT NULL COMMENT 'Catalog Type Model Name',
  `protected` tinyint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'Protected from system removal',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`),
  UNIQUE KEY `alias` (`alias`),
  UNIQUE KEY `model_name` (`model_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100001 ;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_content`
--

CREATE TABLE `molajo_content` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Content Table Primary Key',
  `site_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Site Primary Key or 0',
  `extension_instance_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Extension Instance Primary Key',
  `catalog_type_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Catalog Type Primary Key',
  `title` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `subtitle` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Subtitle',
  `path` varchar(2048) NOT NULL DEFAULT ' ' COMMENT 'URI Path to append to Alias',
  `alias` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Slug, or alias, associated with Title, must be unique when combined with path.',
  `content_text` longtext COMMENT 'Text field',
  `protected` tinyint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'If activated, represents an important feature required for operations that cannot be removed.',
  `featured` tinyint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'Indicator representing content designated as Featured. Can be used in queries.',
  `stickied` tinyint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'Indicator representing content designated as Stickied. Can be used in queries.',
  `status` tinyint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'Content Status, must be one of the following values: 2 Archived 1 Published 0 Unpublished -1 Trashed -2 Marked as Spam -10 Version',
  `start_publishing_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish Begin Date and Time',
  `stop_publishing_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish End Date and Time',
  `version` int(11) NOT NULL DEFAULT '1' COMMENT 'Version Number',
  `version_of_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Primary Key for this Version',
  `status_prior_to_version` int(11) NOT NULL DEFAULT '0' COMMENT 'State value prior to creating this version, can be used to determine if content was just published',
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Created Date and Time',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Created by User ID',
  `modified_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modified Date',
  `modified_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Modified By User ID',
  `checked_out_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Checked out Date and Time',
  `checked_out_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Checked out by User Id',
  `root` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Used with Hierarchical Data to indicate the root node for the tree',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Used with Hierarchical Data to indicate the parent for this node.',
  `lft` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Number which increases from the root node in sequential order until the lowest branch is reached.',
  `rgt` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Number which provides sequence by decreasing in value from the lowest branch in the tree to the highest root level of the tree.',
  `lvl` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Number representing the heirarchical level of the content. The number one is the first level. ',
  `home` tinyint(6) unsigned NOT NULL DEFAULT '0',
  `customfields` longtext COMMENT 'Custom Fields for this Resource Item',
  `parameters` longtext COMMENT 'Custom Parameters for this Resource Item',
  `metadata` longtext COMMENT 'Metadata definitions for this Resource Item',
  `language` char(7) NOT NULL DEFAULT 'en-GB',
  `translation_of_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`catalog_type_id`,`alias`),
  KEY `fk_content_extension_instance_id` (`extension_instance_id`),
  KEY `fk_content_catalog_type_id` (`catalog_type_id`),
  KEY `fk_content_site_id` (`site_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=133 ;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_extensions`
--

CREATE TABLE `molajo_extensions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Extension Primary Key',
  `extension_site_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Extension Site ID',
  `catalog_type_id` int(11) unsigned NOT NULL COMMENT 'Catalog Type ID',
  `name` char(255) NOT NULL DEFAULT '' COMMENT 'Name of Extension',
  `subtitle` char(255) NOT NULL DEFAULT '' COMMENT 'Extension Subtitle (Yes, I know it has no title.)',
  `language` char(7) NOT NULL DEFAULT 'en-GB',
  `translation_of_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'This data is a translation for this the data with this primary key',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  KEY `extensions_extension_sites_index` (`extension_site_id`),
  KEY `fk_extension_catalog_type_index` (`catalog_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25001 ;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_extension_instances`
--

CREATE TABLE `molajo_extension_instances` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Extension Instance Primary Key',
  `extension_id` int(11) unsigned NOT NULL COMMENT 'Extension Primary Key',
  `catalog_type_id` int(11) unsigned NOT NULL COMMENT 'Catalog Type ID',
  `title` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `subtitle` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Subtitle',
  `path` varchar(2048) NOT NULL DEFAULT ' ' COMMENT 'Path prepended to alias to create URL',
  `alias` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'URI Alias of Title',
  `menu` varchar(255) NOT NULL COMMENT 'For Menuitem content types, contains the name of the associated Menu',
  `page_type` varchar(255) NOT NULL COMMENT 'For Menuitem content types, contains the name of the associated Menuitem Type',
  `content_text` longtext COMMENT 'Information about the Extension',
  `protected` tinyint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'If activated, represents an important feature required for operations that cannot be removed.',
  `featured` tinyint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'Indicator representing content designated as Featured. Can be used in queries.',
  `stickied` tinyint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'Indicator representing content designated as Stickied. Can be used in queries.',
  `status` tinyint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'Content Status, must be one of the following values: 2 Archived 1 Published 0 Unpublished -1 Trashed -2 Marked as Spam -10 Version',
  `start_publishing_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish Begin Date and Time',
  `stop_publishing_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish End Date and Time',
  `version` int(11) unsigned NOT NULL DEFAULT '1' COMMENT 'Version Number',
  `version_of_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Primary Key for this Version',
  `status_prior_to_version` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'State value prior to creating this version, can be used to determine if content was just published',
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Created Date and Time',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Created by User ID',
  `modified_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modified Date',
  `modified_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Modified By User ID',
  `checked_out_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Checked out Date and Time',
  `checked_out_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Checked out by User Id',
  `root` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Used with Hierarchical Data to indicate the root node for the tree',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Used with Hierarchical Data to indicate the parent for this node.',
  `lft` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Number which increases from the root node in sequential order until the lowest branch is reached.',
  `rgt` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Number which provides sequence by decreasing in value from the lowest branch in the tree to the highest root level of the tree.',
  `lvl` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Number representing the heirarchical level of the content. The number one is the first level. ',
  `home` tinyint(6) unsigned NOT NULL DEFAULT '0',
  `customfields` longtext COMMENT 'Custom Fields for this Resource Item',
  `parameters` longtext COMMENT 'Custom Parameters for this Resource Item',
  `metadata` longtext COMMENT 'Metadata definitions for this Resource Item',
  `language` char(7) NOT NULL DEFAULT 'en-GB',
  `translation_of_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  KEY `fk_extension_instances_extensions_index` (`extension_id`),
  KEY `fk_extension_instances_catalog_type_index` (`catalog_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25068 ;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_extension_sites`
--

CREATE TABLE `molajo_extension_sites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT ' ',
  `enabled` tinyint(6) NOT NULL DEFAULT '0',
  `location` varchar(2048) NOT NULL,
  `customfields` longtext,
  `parameters` longtext,
  `metadata` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_groups`
--

CREATE TABLE `molajo_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Groups Table Primary Key',
  `title` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `subtitle` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Subtitle',
  `path` varchar(2048) NOT NULL DEFAULT ' ' COMMENT 'URI Path to append to Alias',
  `alias` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Slug, or alias, associated with Title, must be unique when combined with path.',
  `content_text` longtext COMMENT 'Text field',
  `protected` tinyint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'If activated, represents an important feature required for operations that cannot be removed.',
  `featured` tinyint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'Indicator representing content designated as Featured. Can be used in queries.',
  `stickied` tinyint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'Indicator representing content designated as Stickied. Can be used in queries.',
  `status` tinyint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'Content Status, must be one of the following values: 2 Archived 1 Published 0 Unpublished -1 Trashed -2 Marked as Spam -10 Version',
  `start_publishing_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish Begin Date and Time',
  `stop_publishing_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish End Date and Time',
  `version` int(11) NOT NULL DEFAULT '1' COMMENT 'Version Number',
  `version_of_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Primary Key for this Version',
  `status_prior_to_version` int(11) NOT NULL DEFAULT '0' COMMENT 'State value prior to creating this version, can be used to determine if content was just published',
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Created Date and Time',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Created by User ID',
  `modified_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modified Date',
  `modified_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Modified By User ID',
  `checked_out_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Checked out Date and Time',
  `checked_out_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Checked out by User Id',
  `root` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Used with Hierarchical Data to indicate the root node for the tree',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Used with Hierarchical Data to indicate the parent for this node.',
  `lft` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Number which increases from the root node in sequential order until the lowest branch is reached.',
  `rgt` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Number which provides sequence by decreasing in value from the lowest branch in the tree to the highest root level of the tree.',
  `lvl` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Number representing the heirarchical level of the content. The number one is the first level. ',
  `home` tinyint(6) unsigned NOT NULL DEFAULT '0',
  `customfields` longtext COMMENT 'Custom Fields for this Resource Item',
  `parameters` longtext COMMENT 'Custom Parameters for this Resource Item',
  `metadata` longtext COMMENT 'Metadata definitions for this Resource Item',
  `language` char(7) NOT NULL DEFAULT 'en-GB',
  `translation_of_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Ordering',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_group_permissions`
--

CREATE TABLE `molajo_group_permissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_groups.id',
  `catalog_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_catalog.id',
  `action_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_actions.id',
  PRIMARY KEY (`id`),
  KEY `fk_group_permissions_actions_index` (`action_id`),
  KEY `fk_group_permissions_group_index` (`group_id`),
  KEY `fk_group_permissions_catalog_index` (`catalog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_group_view_groups`
--

CREATE TABLE `molajo_group_view_groups` (
  `group_id` int(11) unsigned NOT NULL COMMENT 'FK to the molajo_group table.',
  `view_group_id` int(11) unsigned NOT NULL COMMENT 'FK to the molajo_view_groups table.',
  PRIMARY KEY (`view_group_id`,`group_id`),
  KEY `fk_group_view_groups_view_groups_index` (`view_group_id`),
  KEY `fk_group_view_groups_groups_index` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_language_strings`
--

CREATE TABLE `molajo_language_strings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Language String Table Primary Key',
  `site_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Site Primary Key or 0',
  `extension_instance_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Extension Instance Primary Key',
  `catalog_type_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Catalog Type Primary Key',
  `title` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `subtitle` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Subtitle',
  `path` varchar(2048) NOT NULL DEFAULT ' ' COMMENT 'URI Path to append to Alias',
  `alias` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Slug, or alias, associated with Title, must be unique when combined with path.',
  `content_text` mediumtext COMMENT 'Text field',
  `protected` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'If activated, represents an important feature required for operations that cannot be removed.',
  `featured` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Indicator representing content designated as Featured. Can be used in queries.',
  `stickied` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Indicator representing content designated as Stickied. Can be used in queries.',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Content Status, must be one of the following values: 2 Archived 1 Published 0 Unpublished -1 Trashed -2 Marked as Spam -10 Version',
  `start_publishing_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish Begin Date and Time',
  `stop_publishing_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish End Date and Time',
  `version` int(11) unsigned NOT NULL DEFAULT '1' COMMENT 'Version Number',
  `version_of_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Primary Key for this Version',
  `status_prior_to_version` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'State value prior to creating this version, can be used to determine if content was just published',
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Created Date and Time',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Created by User ID',
  `modified_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modified Date',
  `modified_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Modified By User ID',
  `checked_out_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Checked out Date and Time',
  `checked_out_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Checked out by User Id',
  `root` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Used with Hierarchical Data to indicate the root node for the tree',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Used with Hierarchical Data to indicate the parent for this node.',
  `lft` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Number which increases from the root node in sequential order until the lowest branch is reached.',
  `rgt` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Number which provides sequence by decreasing in value from the lowest branch in the tree to the highest root level of the tree.',
  `lvl` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Number representing the heirarchical level of the content. The number one is the first level. ',
  `home` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `customfields` mediumtext COMMENT 'Custom Fields for this Resource Item',
  `parameters` mediumtext COMMENT 'Custom Parameters for this Resource Item',
  `metadata` mediumtext COMMENT 'Metadata definitions for this Resource Item',
  `language` char(7) NOT NULL DEFAULT 'en-GB',
  `translation_of_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title_language` (`title`,`language`),
  UNIQUE KEY `path_alias` (`path`(250),`alias`,`title`),
  KEY `fk_language_strings_extension_instance_id` (`extension_instance_id`),
  KEY `fk_language_strings_catalog_type_id` (`catalog_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7344 ;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_log`
--

CREATE TABLE `molajo_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Log Primary Key',
  `priority` int(11) DEFAULT NULL,
  `message` longtext,
  `date` datetime DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `customfields` longtext,
  PRIMARY KEY (`id`),
  KEY `idx_category_date_priority` (`category`,`date`,`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_sessions`
--

CREATE TABLE `molajo_sessions` (
  `session_id` varchar(255) NOT NULL,
  `site_id` int(11) unsigned NOT NULL,
  `application_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned DEFAULT '0',
  `session_time` datetime DEFAULT NULL,
  `data` longtext,
  `activity_datetime` datetime DEFAULT NULL COMMENT 'Activity Datetime',
  `ip_address` varchar(15) NOT NULL DEFAULT '' COMMENT 'IP Address',
  PRIMARY KEY (`session_id`),
  KEY `fk_sessions_applications_index` (`site_id`,`application_id`,`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_sites`
--

CREATE TABLE `molajo_sites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Site Primary Key',
  `catalog_type_id` int(11) unsigned NOT NULL DEFAULT '1000' COMMENT 'Catalog Type ID',
  `name` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Name of Extension',
  `path` varchar(2048) NOT NULL DEFAULT ' ' COMMENT 'Path for this site within the Sites Folder',
  `base_url` varchar(2048) NOT NULL DEFAULT 'Used only as documentation',
  `description` longtext COMMENT 'Site Description',
  `customfields` longtext COMMENT 'Custom Fields for this Site',
  `parameters` longtext COMMENT 'Custom Parameters for this Site',
  `metadata` longtext COMMENT 'Metadata definitions for this Site',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_site_applications`
--

CREATE TABLE `molajo_site_applications` (
  `application_id` int(11) unsigned NOT NULL,
  `site_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`site_id`,`application_id`),
  KEY `fk_site_applications_sites_index` (`site_id`),
  KEY `fk_site_applications_applications_index` (`application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_site_extension_instances`
--

CREATE TABLE `molajo_site_extension_instances` (
  `site_id` int(11) unsigned NOT NULL,
  `extension_instance_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`site_id`,`extension_instance_id`),
  KEY `fk_application_extensions_sites_index` (`site_id`),
  KEY `fk_application_extension_instances_extension_instances_index` (`extension_instance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_users`
--

CREATE TABLE `molajo_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key for Users',
  `username` varchar(255) NOT NULL COMMENT 'Username',
  `alias` varchar(255) NOT NULL COMMENT 'User alias',
  `first_name` varchar(100) DEFAULT '' COMMENT 'First name of User',
  `last_name` varchar(150) DEFAULT '' COMMENT 'Last name of User',
  `full_name` varchar(255) NOT NULL COMMENT 'Full name of User',
  `email` varchar(255) DEFAULT '  ' COMMENT 'Email address of user',
  `language` int(11) unsigned NOT NULL,
  `content_text` longtext COMMENT 'Text for User',
  `session_key` varchar(255) DEFAULT NULL COMMENT 'Session Key for User',
  `block` tinyint(6) NOT NULL DEFAULT '0' COMMENT 'If activiated, blocks user from logging on',
  `register_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Registration date for User',
  `activation_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Activation date for User',
  `activation_code` varchar(255) DEFAULT NULL,
  `last_visit_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last visit date for User',
  `last_activity_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last activity date for User',
  `password_changed_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Password changed date for User',
  `password` varchar(100) NOT NULL DEFAULT '  ' COMMENT 'User password',
  `reset_password_code` varchar(255) DEFAULT NULL,
  `login_attempts` tinyint(3) unsigned NOT NULL,
  `customfields` longtext COMMENT 'Custom Fields for this User',
  `parameters` longtext COMMENT 'Custom Parameters for this User',
  `metadata` longtext COMMENT 'Metadata definitions for this User',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `last_name_first_name` (`last_name`,`first_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_user_activity`
--

CREATE TABLE `molajo_user_activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'User Activity Primary Key',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'User ID Foreign Key',
  `action_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Action ID Foreign Key',
  `catalog_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Catalog ID Foreign Key',
  `session_id` varchar(255) NOT NULL DEFAULT '' COMMENT 'User Session ID',
  `activity_datetime` datetime DEFAULT NULL COMMENT 'Activity Datetime',
  `ip_address` varchar(15) NOT NULL DEFAULT '' COMMENT 'IP Address',
  PRIMARY KEY (`id`),
  KEY `user_activity_catalog_index` (`catalog_id`),
  KEY `user_activity_action_index` (`action_id`),
  KEY `fk_user_activity_users_1` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_user_applications`
--

CREATE TABLE `molajo_user_applications` (
  `user_id` int(11) unsigned NOT NULL COMMENT 'User ID Foreign Key',
  `application_id` int(11) unsigned NOT NULL COMMENT 'Application ID Foreign Key',
  PRIMARY KEY (`application_id`,`user_id`),
  KEY `fk_user_applications_users_index` (`user_id`),
  KEY `fk_user_applications_applications_index` (`application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_user_groups`
--

CREATE TABLE `molajo_user_groups` (
  `user_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_users.id',
  `group_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_groups.id',
  PRIMARY KEY (`group_id`,`user_id`),
  KEY `fk_molajo_user_groups_molajo_users_index` (`user_id`),
  KEY `fk_molajo_user_groups_molajo_groups_index` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_user_sites`
--

CREATE TABLE `molajo_user_sites` (
  `user_id` int(11) unsigned NOT NULL COMMENT 'User ID Foreign Key',
  `site_id` int(11) unsigned NOT NULL COMMENT 'Site ID Foreign Key',
  PRIMARY KEY (`site_id`,`user_id`),
  KEY `fk_user_sites_users_index` (`user_id`),
  KEY `fk_user_sites_site_index` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_user_view_groups`
--

CREATE TABLE `molajo_user_view_groups` (
  `user_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_users.id',
  `view_group_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_groups.id',
  PRIMARY KEY (`view_group_id`,`user_id`),
  KEY `fk_user_groups_users_index` (`user_id`),
  KEY `fk_user_view_groups_view_groups_index` (`view_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_view_groups`
--

CREATE TABLE `molajo_view_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `view_group_name_list` longtext NOT NULL,
  `view_group_id_list` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `molajo_view_group_permissions`
--

CREATE TABLE `molajo_view_group_permissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `view_group_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_groups.id',
  `catalog_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_catalog.id',
  `action_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_actions.id',
  PRIMARY KEY (`id`),
  KEY `fk_view_group_permissions_view_groups_index` (`view_group_id`),
  KEY `fk_view_group_permissions_actions_index` (`action_id`),
  KEY `fk_view_group_permissions_catalog_index` (`catalog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `molajo_application_extension_instances`
--
ALTER TABLE `molajo_application_extension_instances`
  ADD CONSTRAINT `fk_application_extension_instances_applications_1` FOREIGN KEY (`application_id`) REFERENCES `molajo_applications` (`id`),
  ADD CONSTRAINT `fk_application_extension_instances_extension_instances_1` FOREIGN KEY (`extension_instance_id`) REFERENCES `molajo_extension_instances` (`id`);

--
-- Constraints for table `molajo_catalog`
--
ALTER TABLE `molajo_catalog`
  ADD CONSTRAINT `fk_catalog_application_id` FOREIGN KEY (`application_id`) REFERENCES `molajo_applications` (`id`),
  ADD CONSTRAINT `fk_catalog_catalog_types` FOREIGN KEY (`catalog_type_id`) REFERENCES `molajo_catalog_types` (`id`),
  ADD CONSTRAINT `fk_catalog_view_group_id` FOREIGN KEY (`view_group_id`) REFERENCES `molajo_view_groups` (`id`);

--
-- Constraints for table `molajo_catalog_activity`
--
ALTER TABLE `molajo_catalog_activity`
  ADD CONSTRAINT `fk_catalog_activity_catalog` FOREIGN KEY (`catalog_id`) REFERENCES `molajo_catalog` (`id`);

--
-- Constraints for table `molajo_catalog_categories`
--
ALTER TABLE `molajo_catalog_categories`
  ADD CONSTRAINT `fk_catalog_categories_catalog` FOREIGN KEY (`catalog_id`) REFERENCES `molajo_catalog` (`id`),
  ADD CONSTRAINT `fk_catalog_categories_categories` FOREIGN KEY (`category_id`) REFERENCES `molajo_content` (`id`);

--
-- Constraints for table `molajo_content`
--
ALTER TABLE `molajo_content`
  ADD CONSTRAINT `fk_content_extension_instances` FOREIGN KEY (`extension_instance_id`) REFERENCES `molajo_extension_instances` (`id`);

--
-- Constraints for table `molajo_extensions`
--
ALTER TABLE `molajo_extensions`
  ADD CONSTRAINT `fk_extensions_extension_sites_1` FOREIGN KEY (`extension_site_id`) REFERENCES `molajo_extension_sites` (`id`);

--
-- Constraints for table `molajo_extension_instances`
--
ALTER TABLE `molajo_extension_instances`
  ADD CONSTRAINT `fk_extension_instances_extensions` FOREIGN KEY (`extension_id`) REFERENCES `molajo_extensions` (`id`);

--
-- Constraints for table `molajo_group_permissions`
--
ALTER TABLE `molajo_group_permissions`
  ADD CONSTRAINT `fk_group_permissions_actions_1` FOREIGN KEY (`action_id`) REFERENCES `molajo_actions` (`id`),
  ADD CONSTRAINT `fk_group_permissions_catalog_1` FOREIGN KEY (`catalog_id`) REFERENCES `molajo_catalog` (`id`),
  ADD CONSTRAINT `fk_group_permissions_groups_1` FOREIGN KEY (`group_id`) REFERENCES `molajo_groups` (`id`);

--
-- Constraints for table `molajo_group_view_groups`
--
ALTER TABLE `molajo_group_view_groups`
  ADD CONSTRAINT `fk_group_view_groups_groups` FOREIGN KEY (`group_id`) REFERENCES `molajo_groups` (`id`),
  ADD CONSTRAINT `fk_group_view_groups_view_groups` FOREIGN KEY (`view_group_id`) REFERENCES `molajo_view_groups` (`id`);

--
-- Constraints for table `molajo_site_applications`
--
ALTER TABLE `molajo_site_applications`
  ADD CONSTRAINT `fk_site_applications_applications` FOREIGN KEY (`application_id`) REFERENCES `molajo_applications` (`id`),
  ADD CONSTRAINT `fk_site_applications_sites` FOREIGN KEY (`site_id`) REFERENCES `molajo_sites` (`id`);

--
-- Constraints for table `molajo_site_extension_instances`
--
ALTER TABLE `molajo_site_extension_instances`
  ADD CONSTRAINT `fk_site_extension_instances_extension_instances_1` FOREIGN KEY (`extension_instance_id`) REFERENCES `molajo_extension_instances` (`id`),
  ADD CONSTRAINT `fk_site_extension_instances_sites_1` FOREIGN KEY (`site_id`) REFERENCES `molajo_sites` (`id`);

--
-- Constraints for table `molajo_user_activity`
--
ALTER TABLE `molajo_user_activity`
  ADD CONSTRAINT `fk_user_activity_catalog_1` FOREIGN KEY (`catalog_id`) REFERENCES `molajo_catalog` (`id`),
  ADD CONSTRAINT `fk_user_activity_users_1` FOREIGN KEY (`user_id`) REFERENCES `molajo_users` (`id`);

--
-- Constraints for table `molajo_user_applications`
--
ALTER TABLE `molajo_user_applications`
  ADD CONSTRAINT `fk_user_applications_users` FOREIGN KEY (`user_id`) REFERENCES `molajo_users` (`id`);

--
-- Constraints for table `molajo_user_groups`
--
ALTER TABLE `molajo_user_groups`
  ADD CONSTRAINT `fk_user_groups_groups_1` FOREIGN KEY (`group_id`) REFERENCES `molajo_groups` (`id`),
  ADD CONSTRAINT `fk_user_groups_users_1` FOREIGN KEY (`user_id`) REFERENCES `molajo_users` (`id`);

--
-- Constraints for table `molajo_user_view_groups`
--
ALTER TABLE `molajo_user_view_groups`
  ADD CONSTRAINT `fk_user_view_groups_users_1` FOREIGN KEY (`user_id`) REFERENCES `molajo_users` (`id`),
  ADD CONSTRAINT `fk_user_view_groups_view_groups_1` FOREIGN KEY (`view_group_id`) REFERENCES `molajo_view_groups` (`id`);

--
-- Constraints for table `molajo_view_group_permissions`
--
ALTER TABLE `molajo_view_group_permissions`
  ADD CONSTRAINT `fk_view_group_permissions_actions_1` FOREIGN KEY (`action_id`) REFERENCES `molajo_actions` (`id`),
  ADD CONSTRAINT `fk_view_group_permissions_catalog_1` FOREIGN KEY (`catalog_id`) REFERENCES `molajo_catalog` (`id`),
  ADD CONSTRAINT `fk_view_group_permissions_view_groups_1` FOREIGN KEY (`view_group_id`) REFERENCES `molajo_view_groups` (`id`);
