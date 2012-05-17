-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 17, 2012 at 10:31 AM
-- Server version: 5.5.9
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `molajo`
--

-- --------------------------------------------------------

--
-- Table structure for table `molajo_actions`
--

CREATE TABLE `molajo_actions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key',
  `title` varchar(255) NOT NULL DEFAULT ' ',
  `protected` tinyint(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_actions_table_title` (`title`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `molajo_actions`
--

INSERT INTO `molajo_actions` VALUES(1, 'login', 1);
INSERT INTO `molajo_actions` VALUES(2, 'create', 1);
INSERT INTO `molajo_actions` VALUES(3, 'view', 1);
INSERT INTO `molajo_actions` VALUES(4, 'edit', 1);
INSERT INTO `molajo_actions` VALUES(5, 'publish', 1);
INSERT INTO `molajo_actions` VALUES(6, 'delete', 1);
INSERT INTO `molajo_actions` VALUES(7, 'administer', 1);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_applications`
--

CREATE TABLE `molajo_applications` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Application Primary Key',
  `catalog_type_id` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `path` varchar(2048) NOT NULL DEFAULT ' ' COMMENT 'URL Alias',
  `description` mediumtext,
  `customfields` mediumtext,
  `parameters` mediumtext COMMENT 'Configurable Parameter Values',
  `metadata` mediumtext,
  PRIMARY KEY (`id`),
  KEY `fk_applications_catalog_types_index` (`catalog_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `molajo_applications`
--

INSERT INTO `molajo_applications` VALUES(1, 50, 'site', '', 'Primary application for site visitors', '{}', '{\r\n    "application_name":"Molajo Site",\r\n\r\n    "url_sef":"1",\r\n    "url_sef_rewrite":"1",\r\n    "url_sef_suffix":"0",\r\n    "url_unicode_slugs":"0",\r\n    "url_force_ssl":"0",\r\n\r\n    "language":"en-GB",\r\n    "language_direction":"ltr",\r\n    "language_multilingual":"0",\r\n\r\n    "language_utc_offset":"UTC",\r\n    "language_utc_offset_user":"UTC",\r\n\r\n    "editor":"none",\r\n    "home_catalog_id":"139",\r\n    "logon_requirement":"0",\r\n\r\n    "debug":"0",\r\n    "debug_log":"echo",\r\n\r\n    "head_template_view_id":"23",\r\n    "head_wrap_view_id":"66",\r\n\r\n    "message_template_view_id":"56",\r\n    "message_wrap_view_id":"66",\r\n\r\n    "offline_theme_id":"99",\r\n    "offline_page_view_id":"57",\r\n    "offline":"0",\r\n    "offline_message":"This site is not available.<br /> Please check back again soon.",\r\n\r\n    "error_theme_id":"99",\r\n    "error_page_view_id":"56",\r\n    "error_404_message":"Page not found",\r\n    "error_403_message":"Not authorised",\r\n\r\n    "display_title":"Molajo Article Component: Site Configuration",\r\n\r\n    "display_view_on_no_results":"1",\r\n    "catalog_type_id":"10000",\r\n    "extension_catalog_type_id":"1050",\r\n\r\n    "criteria_enable_draft_save":"0",\r\n    "criteria_enable_version_history":"0",\r\n    "criteria_maximum_version_count":"5",\r\n    "criteria_enable_hit_counts":"0",\r\n    "criteria_enable_comments":"0",\r\n    "criteria_enable_ratings":"0",\r\n    "criteria_enable_notifications":"0",\r\n    "criteria_enable_tweets":"0",\r\n    "criteria_enable_ping":"0",\r\n\r\n    "criteria_html5":"1",\r\n    "criteria_html_display_filter":"1",\r\n\r\n    "criteria_image_xsmall":"50",\r\n    "criteria_image_small":"75",\r\n    "criteria_image_medium":"150",\r\n    "criteria_image_large":"300",\r\n    "criteria_image_xlarge":"500",\r\n    "criteria_image_folder":"images",\r\n    "criteria_thumb_folder":"thumbs",\r\n    \r\n    "criteria_asset_priority_site":"100",\r\n    "criteria_asset_priority_application":"200",\r\n    "criteria_asset_priority_user":"300",\r\n    "criteria_asset_priority_extension":"400",\r\n    "criteria_asset_priority_request":"500",\r\n    "criteria_asset_priority_category":"600",\r\n    "criteria_asset_priority_menu_item":"700",\r\n    "criteria_asset_priority_source":"800",\r\n    "criteria_asset_priority_theme":"900",\r\n\r\n    "theme_id":"119",\r\n\r\n    "page_view_id":"55",\r\n    "page_view_css_id":"",\r\n    "page_view_css_class":"",\r\n\r\n    "template_view_id":"236",\r\n    "template_view_css_id":"",\r\n    "template_view_css_class":"",\r\n    "wrap_view_id":"61",\r\n    "wrap_view_css_id":"",\r\n    "wrap_view_css_class":"",\r\n\r\n    "form_template_view_id":"25",\r\n    "form_template_view_css_id":"",\r\n    "form_template_view_css_class":"",\r\n    "form_wrap_view_id":"61",\r\n    "form_wrap_view_css_id":"",\r\n    "form_wrap_view_css_class":"",\r\n\r\n    "list_template_view_id":"33",\r\n    "list_template_view_css_id":"",\r\n    "list_template_view_css_class":"",\r\n    "list_wrap_view_id":"58",\r\n    "list_wrap_view_css_id":"",\r\n    "list_wrap_view_css_class":"",\r\n\r\n    "criteria_list_get_customfields":"1",\r\n    "criteria_list_get_item_children":"0",\r\n    "criteria_list_use_special_joins":"0",\r\n    "criteria_list_check_view_level_access":"0",\r\n\r\n    "criteria_list_display_archived_content":"1",\r\n    "criteria_list_display_featured_content":"0",\r\n    "criteria_list_display_stickied_content":"0",\r\n    "criteria_list_display_published_date_begin":"0",\r\n    "criteria_list_display_published_date_end":"1",\r\n    "criteria_list_display_category_list":"0",\r\n    "criteria_list_display_tag_list":"0",\r\n    "criteria_list_display_author_list":"0",\r\n    "criteria_list_begin":"1",\r\n    "criteria_list_length":"0",\r\n    "criteria_list_order_by_field1":"start_publishing_datetime",\r\n    "criteria_list_order_by_direction1":"DESC",\r\n    "criteria_list_order_by_field2":"",\r\n    "criteria_list_order_by_direction2":"",\r\n    "criteria_list_order_by_field3":"",\r\n    "criteria_list_order_by_direction3":"",\r\n\r\n    "feed_theme_id":"99",\r\n    "feed_page_view_id":"81",\r\n    "feed_limit":"10",\r\n    "feed_email":"author",\r\n\r\n    "system_caching":"0",\r\n    "system_cache_time":"15",\r\n    "system_cache_handler":"file"\r\n}', '{"metadata_title":"Molajo Site Application", \r\n"metadata_description":"Welcome to the Molajo Site Application", \r\n"metadata_keywords":"molajo", \r\n"metadata_robots":"follow, index", \r\n"metadata_author":"Author Name", \r\n"metadata_content_rights":"CC"}        ');
INSERT INTO `molajo_applications` VALUES(2, 50, 'admin', 'admin', 'Administrative site area for site construction', '{}', '{\r\n    "application_name":"Molajo Administrator",\r\n\r\n     "url_sef":"1",\r\n    "url_sef_rewrite":"1",\r\n    "url_sef_suffix":"0",\r\n    "url_unicode_slugs":"0",\r\n    "url_force_ssl":"0",\r\n\r\n    "language":"en-GB",\r\n    "language_direction":"ltr",\r\n    "language_multilingual":"0",\r\n\r\n    "language_utc_offset":"UTC",\r\n    "language_utc_offset_user":"UTC",\r\n\r\n\r\n    "editor":"none",\r\n    "home_catalog_id":"139",\r\n    "logon_requirement":"0",\r\n\r\n    "debug":"0",\r\n    "debug_log":"echo",\r\n\r\n    "head_template_view_id":"23",\r\n    "head_wrap_view_id":"66",\r\n\r\n    "message_template_view_id":"56",\r\n    "message_wrap_view_id":"66",\r\n\r\n    "offline_theme_id":"99",\r\n    "offline_page_view_id":"57",\r\n    "offline":"0",\r\n    "offline_message":"This site is not available.<br /> Please check back again soon.",\r\n\r\n    "error_theme_id":"99",\r\n    "error_page_view_id":"56",\r\n    "error_404_message":"Page not found",\r\n    "error_403_message":"Not authorised",\r\n\r\n    "display_title":"Molajo Article Component: Site Configuration",\r\n\r\n    "display_view_on_no_results":"1",\r\n    "catalog_type_id":"10000",\r\n    "extension_catalog_type_id":"1050",\r\n\r\n    "criteria_enable_draft_save":"0",\r\n    "criteria_enable_version_history":"0",\r\n    "criteria_maximum_version_count":"5",\r\n    "criteria_enable_hit_counts":"0",\r\n    "criteria_enable_comments":"0",\r\n    "criteria_enable_ratings":"0",\r\n    "criteria_enable_notifications":"0",\r\n    "criteria_enable_tweets":"0",\r\n    "criteria_enable_ping":"0",\r\n\r\n    "criteria_html5":"1",\r\n    "criteria_html_display_filter":"1",\r\n\r\n    "criteria_image_xsmall":"50",\r\n    "criteria_image_small":"75",\r\n    "criteria_image_medium":"150",\r\n    "criteria_image_large":"300",\r\n    "criteria_image_xlarge":"500",\r\n    "criteria_image_folder":"images",\r\n    "criteria_thumb_folder":"thumbs",\r\n    \r\n    "criteria_asset_priority_site":"100",\r\n    "criteria_asset_priority_application":"200",\r\n    "criteria_asset_priority_user":"300",\r\n    "criteria_asset_priority_extension":"400",\r\n    "criteria_asset_priority_request":"500",\r\n    "criteria_asset_priority_category":"600",\r\n    "criteria_asset_priority_menu_item":"700",\r\n    "criteria_asset_priority_source":"800",\r\n    "criteria_asset_priority_theme":"900",\r\n\r\n    "theme_id":"119",\r\n\r\n    "page_view_id":"55",\r\n    "page_view_css_id":"",\r\n    "page_view_css_class":"",\r\n\r\n    "template_view_id":"236",\r\n    "template_view_css_id":"",\r\n    "template_view_css_class":"",\r\n    "wrap_view_id":"61",\r\n    "wrap_view_css_id":"",\r\n    "wrap_view_css_class":"",\r\n\r\n    "form_template_view_id":"25",\r\n    "form_template_view_css_id":"",\r\n    "form_template_view_css_class":"",\r\n    "form_wrap_view_id":"61",\r\n    "form_wrap_view_css_id":"",\r\n    "form_wrap_view_css_class":"",\r\n\r\n    "list_template_view_id":"33",\r\n    "list_template_view_css_id":"",\r\n    "list_template_view_css_class":"",\r\n    "list_wrap_view_id":"58",\r\n    "list_wrap_view_css_id":"",\r\n    "list_wrap_view_css_class":"",\r\n\r\n    "criteria_list_get_customfields":"1",\r\n    "criteria_list_get_item_children":"0",\r\n    "criteria_list_use_special_joins":"0",\r\n    "criteria_list_check_view_level_access":"0",\r\n\r\n    "criteria_list_display_archived_content":"1",\r\n    "criteria_list_display_featured_content":"0",\r\n    "criteria_list_display_stickied_content":"0",\r\n    "criteria_list_display_published_date_begin":"0",\r\n    "criteria_list_display_published_date_end":"1",\r\n    "criteria_list_display_category_list":"0",\r\n    "criteria_list_display_tag_list":"0",\r\n    "criteria_list_display_author_list":"0",\r\n    "criteria_list_begin":"1",\r\n    "criteria_list_length":"0",\r\n    "criteria_list_order_by_field1":"start_publishing_datetime",\r\n    "criteria_list_order_by_direction1":"DESC",\r\n    "criteria_list_order_by_field2":"",\r\n    "criteria_list_order_by_direction2":"",\r\n    "criteria_list_order_by_field3":"",\r\n    "criteria_list_order_by_direction3":"",\r\n\r\n    "feed_theme_id":"99",\r\n    "feed_page_view_id":"81",\r\n    "feed_limit":"10",\r\n    "feed_email":"author",\r\n\r\n    "system_caching":"0",\r\n    "system_cache_time":"15",\r\n    "system_cache_handler":"file"\r\n}', '{"title":"Molajo Administrator Application", \r\n"description":"Welcome to the Molajo Administrator Application", \r\n"keywords":"molajo", \r\n"robots":"follow, index", \r\n"author":"Author Name", \r\n"content_rights":"CC"}        ');

-- --------------------------------------------------------

--
-- Table structure for table `molajo_application_extension_instances`
--

CREATE TABLE `molajo_application_extension_instances` (
  `extension_instance_id` int(11) unsigned NOT NULL,
  `application_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`extension_instance_id`,`application_id`),
  KEY `fk_application_extensions_applications_index` (`application_id`),
  KEY `fk_application_extension_instances_extension_instances_index` (`extension_instance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `molajo_application_extension_instances`
--

INSERT INTO `molajo_application_extension_instances` VALUES(2, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(5, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(6, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(11, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(12, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(13, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(14, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(18, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(19, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(20, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(21, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(22, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(23, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(24, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(25, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(26, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(27, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(28, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(29, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(30, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(31, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(32, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(41, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(42, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(43, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(44, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(55, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(56, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(57, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(58, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(59, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(60, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(61, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(62, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(63, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(64, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(65, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(66, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(67, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(82, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(83, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(84, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(85, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(86, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(87, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(88, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(89, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(90, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(91, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(92, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(93, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(94, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(97, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(99, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(101, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(103, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(104, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(105, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(106, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(107, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(108, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(109, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(110, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(111, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(112, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(113, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(114, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(115, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(116, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(117, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(118, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(119, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(120, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(121, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(122, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(123, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(124, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(125, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(236, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(243, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(244, 1);
INSERT INTO `molajo_application_extension_instances` VALUES(0, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(1, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(2, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(3, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(4, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(5, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(6, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(7, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(8, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(9, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(10, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(11, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(12, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(13, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(14, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(15, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(16, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(17, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(18, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(19, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(20, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(21, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(22, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(23, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(24, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(25, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(26, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(27, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(28, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(29, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(30, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(31, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(32, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(33, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(34, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(35, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(36, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(37, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(38, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(39, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(40, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(41, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(42, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(43, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(44, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(55, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(56, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(57, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(58, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(59, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(60, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(61, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(62, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(63, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(64, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(65, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(66, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(67, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(82, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(83, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(84, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(85, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(86, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(87, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(88, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(89, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(90, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(91, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(92, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(93, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(94, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(98, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(99, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(100, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(103, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(104, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(105, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(106, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(107, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(108, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(109, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(110, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(111, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(112, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(113, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(114, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(115, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(116, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(117, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(118, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(119, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(120, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(121, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(122, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(123, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(124, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(125, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(236, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(237, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(238, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(239, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(240, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(241, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(242, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(243, 2);
INSERT INTO `molajo_application_extension_instances` VALUES(244, 2);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_catalog`
--

CREATE TABLE `molajo_catalog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'catalog Primary Key',
  `catalog_type_id` int(11) unsigned NOT NULL DEFAULT '0',
  `source_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Content Primary Key',
  `routable` tinyint(1) NOT NULL DEFAULT '0',
  `sef_request` varchar(2048) NOT NULL DEFAULT ' ' COMMENT 'URL',
  `redirect_to_id` int(11) unsigned NOT NULL DEFAULT '0',
  `view_group_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the molajo_groupings table',
  `primary_category_id` int(11) unsigned NOT NULL DEFAULT '0',
  `tinyurl` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sef_request` (`sef_request`(255)),
  KEY `index_catalog_catalog_types` (`catalog_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=170 ;

--
-- Dumping data for table `molajo_catalog`
--

INSERT INTO `molajo_catalog` VALUES(1, 10, 1, 0, '', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(2, 50, 1, 0, '', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(3, 50, 2, 0, 'admin', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(5, 100, 1, 1, 'group/1', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(6, 100, 2, 1, 'group/2', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(7, 100, 3, 1, 'group/3', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(8, 100, 4, 1, 'group/4', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(9, 120, 5, 1, 'group/5', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(10, 120, 6, 1, 'group/6', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(12, 1050, 0, 1, 'extensions/components/0', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(13, 1050, 1, 1, 'extensions/components/1', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(14, 1050, 2, 1, 'extensions/components/2', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(15, 1050, 3, 1, 'extensions/components/3', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(16, 1050, 4, 1, 'extensions/components/4', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(17, 1050, 5, 1, 'extensions/components/5', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(18, 1050, 6, 1, 'extensions/components/6', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(19, 1050, 7, 1, 'extensions/components/7', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(20, 1050, 8, 1, 'extensions/components/8', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(21, 1050, 9, 1, 'extensions/components/9', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(22, 1050, 10, 1, 'extensions/components/10', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(23, 1050, 11, 1, 'extensions/components/11', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(24, 1050, 12, 1, 'extensions/components/12', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(25, 1050, 13, 1, 'extensions/components/13', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(26, 1050, 14, 1, 'extensions/components/14', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(27, 1050, 15, 1, 'extensions/components/15', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(28, 1050, 16, 1, 'extensions/components/16', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(29, 1050, 17, 1, 'extensions/components/17', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(30, 1100, 18, 1, 'extensions/languages/18', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(31, 1200, 19, 1, 'extensions/views/19', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(32, 1200, 20, 1, 'extensions/views/20', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(33, 1200, 21, 1, 'extensions/views/21', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(34, 1200, 22, 1, 'extensions/views/22', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(35, 1200, 23, 1, 'extensions/views/23', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(36, 1200, 24, 1, 'extensions/views/24', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(37, 1200, 25, 1, 'extensions/views/25', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(38, 1200, 26, 1, 'extensions/views/26', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(39, 1200, 27, 1, 'extensions/views/27', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(40, 1200, 28, 1, 'extensions/views/28', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(41, 1200, 29, 1, 'extensions/views/29', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(42, 1200, 30, 1, 'extensions/views/30', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(43, 1200, 31, 1, 'extensions/views/31', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(44, 1200, 32, 1, 'extensions/views/32', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(45, 1200, 33, 1, 'extensions/views/33', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(46, 1200, 34, 1, 'extensions/views/34', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(47, 1200, 35, 1, 'extensions/views/35', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(48, 1200, 36, 1, 'extensions/views/36', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(49, 1200, 37, 1, 'extensions/views/37', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(50, 1200, 38, 1, 'extensions/views/38', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(51, 1200, 39, 1, 'extensions/views/39', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(52, 1200, 40, 1, 'extensions/views/40', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(53, 1200, 41, 1, 'extensions/views/41', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(54, 1200, 42, 1, 'extensions/views/42', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(55, 1200, 43, 1, 'extensions/views/43', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(56, 1200, 44, 1, 'extensions/views/44', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(67, 1150, 55, 1, 'extensions/views/55', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(68, 1150, 56, 1, 'extensions/views/56', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(69, 1150, 57, 1, 'extensions/views/57', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(70, 1250, 58, 1, 'extensions/views/58', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(71, 1250, 59, 1, 'extensions/views/59', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(72, 1250, 60, 1, 'extensions/views/60', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(73, 1250, 61, 1, 'extensions/views/61', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(74, 1250, 62, 1, 'extensions/views/62', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(75, 1250, 63, 1, 'extensions/views/63', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(76, 1250, 64, 1, 'extensions/views/64', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(77, 1250, 65, 1, 'extensions/views/65', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(78, 1250, 66, 1, 'extensions/views/66', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(79, 1250, 67, 1, 'extensions/views/67', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(80, 1450, 82, 1, 'extensions/plugins/82', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(81, 1450, 83, 1, 'extensions/plugins/83', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(82, 1450, 84, 1, 'extensions/plugins/84', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(83, 1450, 85, 1, 'extensions/plugins/85', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(84, 1450, 86, 1, 'extensions/plugins/86', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(85, 1450, 87, 1, 'extensions/plugins/87', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(86, 1450, 88, 1, 'extensions/plugins/88', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(87, 1450, 89, 1, 'extensions/plugins/89', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(88, 1450, 90, 1, 'extensions/plugins/90', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(89, 1450, 91, 1, 'extensions/plugins/91', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(90, 1450, 92, 1, 'extensions/plugins/92', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(91, 1450, 93, 1, 'extensions/plugins/93', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(92, 1450, 94, 1, 'extensions/plugins/94', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(93, 1500, 97, 1, 'extensions/templates/97', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(94, 1500, 98, 1, 'extensions/templates/98', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(95, 1500, 99, 1, 'extensions/templates/99', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(96, 1300, 100, 1, 'extensions/menus/100', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(97, 1300, 101, 1, 'extensions/menus/101', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(98, 1350, 103, 1, 'extensions/modules/103', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(99, 1350, 104, 1, 'extensions/modules/104', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(100, 1350, 105, 1, 'extensions/modules/105', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(101, 1350, 106, 1, 'extensions/modules/106', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(102, 1350, 107, 1, 'extensions/modules/107', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(103, 1350, 108, 1, 'extensions/modules/108', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(104, 1350, 109, 1, 'extensions/modules/109', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(105, 1350, 110, 1, 'extensions/modules/110', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(106, 1350, 111, 1, 'extensions/modules/111', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(107, 1350, 112, 1, 'extensions/modules/112', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(108, 1350, 113, 1, 'extensions/modules/113', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(109, 1350, 114, 1, 'extensions/modules/114', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(110, 1350, 115, 1, 'extensions/modules/115', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(111, 1350, 116, 1, 'extensions/modules/116', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(112, 1350, 117, 1, 'extensions/modules/117', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(113, 1350, 118, 1, 'extensions/modules/118', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(139, 2000, 102, 1, 'content', 0, 1, 3, '');
INSERT INTO `molajo_catalog` VALUES(140, 2000, 114, 1, 'articles', 0, 1, 3, '');
INSERT INTO `molajo_catalog` VALUES(141, 3000, 103, 1, 'category/content', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(142, 10000, 104, 1, 'articles/article-alias-here', 0, 1, 103, '');
INSERT INTO `molajo_catalog` VALUES(143, 10000, 105, 1, 'articles/second-alias', 0, 1, 103, '');
INSERT INTO `molajo_catalog` VALUES(144, 10000, 106, 1, 'articles/article-three', 0, 1, 103, '');
INSERT INTO `molajo_catalog` VALUES(145, 10000, 107, 1, 'articles/article-4', 0, 1, 103, '');
INSERT INTO `molajo_catalog` VALUES(146, 10000, 108, 1, 'articles/article-5', 0, 1, 103, '');
INSERT INTO `molajo_catalog` VALUES(147, 10000, 109, 1, 'articles/article-6', 0, 1, 103, '');
INSERT INTO `molajo_catalog` VALUES(148, 10000, 110, 1, 'articles/article-7', 0, 1, 103, '');
INSERT INTO `molajo_catalog` VALUES(149, 10000, 111, 1, 'articles/article-8', 0, 1, 103, '');
INSERT INTO `molajo_catalog` VALUES(150, 10000, 112, 1, 'articles/article-9', 0, 1, 103, '');
INSERT INTO `molajo_catalog` VALUES(151, 10000, 113, 1, 'articles/article-10', 0, 1, 103, '');
INSERT INTO `molajo_catalog` VALUES(152, 1500, 119, 1, 'extensions/templates/119', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(153, 1500, 120, 1, 'extensions/templates/120', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(154, 1500, 121, 1, 'extensions/templates/121', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(155, 1150, 122, 1, 'extensions/views/122', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(156, 1150, 123, 1, 'extensions/views/123', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(157, 1150, 124, 1, 'extensions/views/124', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(158, 1150, 125, 1, 'extensions/views/125', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(159, 1200, 236, 0, 'extensions/template_view/236', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(160, 10000, 104, 1, 'articles/article-1', 142, 1, 103, '');
INSERT INTO `molajo_catalog` VALUES(161, 1350, 237, 1, 'extensions/modules/237', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(162, 1350, 238, 1, 'extensions/modules/238', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(163, 1350, 239, 1, 'extensions/modules/239', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(164, 1350, 240, 1, 'extensions/modules/240', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(165, 1350, 241, 1, 'extensions/modules/241', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(166, 1350, 242, 1, 'extensions/modules/242', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(168, 1500, 243, 1, 'extensions/themes/243', 0, 1, 0, '');
INSERT INTO `molajo_catalog` VALUES(169, 1500, 244, 1, 'extensions/themes/244', 0, 1, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `molajo_catalog_activity`
--

CREATE TABLE `molajo_catalog_activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catalog_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `rating` tinyint(4) unsigned DEFAULT NULL,
  `activity_datetime` datetime DEFAULT NULL,
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `customfields` mediumtext,
  PRIMARY KEY (`id`),
  KEY `catalog_activity_catalog_index` (`catalog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `molajo_catalog_activity`
--


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

--
-- Dumping data for table `molajo_catalog_categories`
--


-- --------------------------------------------------------

--
-- Table structure for table `molajo_catalog_types`
--

CREATE TABLE `molajo_catalog_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key',
  `title` varchar(255) NOT NULL DEFAULT ' ',
  `protected` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `source_table` varchar(255) NOT NULL DEFAULT ' ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=60001 ;

--
-- Dumping data for table `molajo_catalog_types`
--

INSERT INTO `molajo_catalog_types` VALUES(1, 'system', 1, '');
INSERT INTO `molajo_catalog_types` VALUES(10, 'sites', 1, '#__sites');
INSERT INTO `molajo_catalog_types` VALUES(50, 'applications', 1, '#__applications');
INSERT INTO `molajo_catalog_types` VALUES(100, 'system', 1, '#__content');
INSERT INTO `molajo_catalog_types` VALUES(110, 'normal', 1, '#__content');
INSERT INTO `molajo_catalog_types` VALUES(120, 'user', 1, '#__content');
INSERT INTO `molajo_catalog_types` VALUES(130, 'friend', 1, '#__content');
INSERT INTO `molajo_catalog_types` VALUES(500, 'users', 1, '#__users');
INSERT INTO `molajo_catalog_types` VALUES(1050, 'components', 1, '#__extension_instances');
INSERT INTO `molajo_catalog_types` VALUES(1100, 'languages', 1, '#__extension_instances');
INSERT INTO `molajo_catalog_types` VALUES(1150, 'page_view', 1, '#__extension_instances');
INSERT INTO `molajo_catalog_types` VALUES(1200, 'template_view', 1, '#__extension_instances');
INSERT INTO `molajo_catalog_types` VALUES(1250, 'wrap_view', 1, '#__extension_instances');
INSERT INTO `molajo_catalog_types` VALUES(1300, 'menus', 1, '#__extension_instances');
INSERT INTO `molajo_catalog_types` VALUES(1350, 'modules', 1, '#__extension_instances');
INSERT INTO `molajo_catalog_types` VALUES(1450, 'triggers', 1, '#__extension_instances');
INSERT INTO `molajo_catalog_types` VALUES(1500, 'themes', 1, '#__extension_instances');
INSERT INTO `molajo_catalog_types` VALUES(2000, 'component', 1, '#__content');
INSERT INTO `molajo_catalog_types` VALUES(2100, 'link', 1, '#__content');
INSERT INTO `molajo_catalog_types` VALUES(2200, 'module', 1, '#__content');
INSERT INTO `molajo_catalog_types` VALUES(2300, 'separator', 1, '#__content');
INSERT INTO `molajo_catalog_types` VALUES(3000, 'list', 0, '#__content');
INSERT INTO `molajo_catalog_types` VALUES(3500, 'tags', 0, '#__content');
INSERT INTO `molajo_catalog_types` VALUES(10000, 'articles', 0, '#__content');
INSERT INTO `molajo_catalog_types` VALUES(20000, 'contacts', 0, '#__content');
INSERT INTO `molajo_catalog_types` VALUES(30000, 'comments', 0, '#__content');
INSERT INTO `molajo_catalog_types` VALUES(40000, 'dashboard', 0, '');
INSERT INTO `molajo_catalog_types` VALUES(50000, 'media', 0, '#__content');
INSERT INTO `molajo_catalog_types` VALUES(60000, 'views', 0, '#__content');

-- --------------------------------------------------------

--
-- Table structure for table `molajo_content`
--

CREATE TABLE `molajo_content` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `extension_instance_id` int(11) unsigned NOT NULL DEFAULT '0',
  `catalog_type_id` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `subtitle` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Subtitle',
  `path` varchar(2048) NOT NULL DEFAULT ' ',
  `alias` varchar(255) NOT NULL DEFAULT ' ',
  `content_text` mediumtext,
  `protected` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `featured` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `stickied` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Published State 2: Archived 1: Published 0: Unpublished -1: Trashed -2: Spam -10 Version',
  `start_publishing_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish Begin Date and Time',
  `stop_publishing_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish End Date and Time',
  `version` int(11) unsigned NOT NULL DEFAULT '1' COMMENT 'Version Number',
  `version_of_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Primary ID for this Version',
  `status_prior_to_version` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'State value prior to creating this version copy and changing the state to Version',
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Created Date and Time',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Created by User ID',
  `modified_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modified Date',
  `modified_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Modified By User ID',
  `checked_out_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Checked out Date and Time',
  `checked_out_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Checked out by User Id',
  `root` int(11) unsigned NOT NULL DEFAULT '0',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `lft` int(11) unsigned NOT NULL DEFAULT '0',
  `rgt` int(11) unsigned NOT NULL DEFAULT '0',
  `lvl` int(11) unsigned NOT NULL DEFAULT '0',
  `home` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `customfields` mediumtext,
  `parameters` mediumtext COMMENT 'Attributes (Custom Fields)',
  `metadata` mediumtext,
  `language` char(7) NOT NULL DEFAULT 'en-GB',
  `translation_of_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  KEY `fk_content_extension_instances_index` (`extension_instance_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=115 ;

--
-- Dumping data for table `molajo_content`
--

INSERT INTO `molajo_content` VALUES(0, 93, 1000, 'ROOT', '', '', 'root', '<p>Root Content</p>', 0, 0, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, 'en-GB', 0, 1);
INSERT INTO `molajo_content` VALUES(1, 9, 100, 'Public', ' ', '', 'public', 'All visitors regardless of authentication status', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, 0, 0, 1, 2, 1, 0, '{}', '{}', NULL, 'en-GB', 0, 1);
INSERT INTO `molajo_content` VALUES(2, 9, 100, 'Guest', ' ', '', 'guest', 'Visitors not authenticated', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 4, 1, 0, '{}', '{}', NULL, 'en-GB', 0, 2);
INSERT INTO `molajo_content` VALUES(3, 9, 100, 'Registered', ' ', '', 'registered', 'Authentication visitors', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, 0, 0, 5, 6, 1, 0, '{}', '{}', NULL, 'en-GB', 0, 3);
INSERT INTO `molajo_content` VALUES(4, 9, 100, 'Administrator', ' ', '', 'administrator', 'System Administrator', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, 0, 0, 7, 8, 1, 0, '{}', '{}', NULL, 'en-GB', 0, 4);
INSERT INTO `molajo_content` VALUES(5, 9, 120, 'Administrator ', ' ', '', 'admin', '', 0, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, 0, 42, 0, 0, 0, 0, '{}', '{}', NULL, 'en-GB', 0, 1);
INSERT INTO `molajo_content` VALUES(6, 9, 120, 'Mark Robinson', ' ', '', 'mark', '', 0, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, 0, 100, 0, 0, 0, 0, '{}', '{}', NULL, 'en-GB', 0, 1);
INSERT INTO `molajo_content` VALUES(101, 100, 2000, 'Root', '', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, 101, 0, 0, 65, 0, 0, '{}', '{}', '{}', 'en-GB', 0, 101);
INSERT INTO `molajo_content` VALUES(102, 100, 2000, 'Content', '', '', 'content', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, 101, 101, 1, 12, 1, 0, '{"custom_field":"stuff"}', '{"section":"content",\r\n"display_suppress_no_results":"0",\r\n"disable_view_access_check":"0",\r\n"template_view_id":"19",\r\n"wrap_view_id":"67",\r\n"controller":"MolajoDisplayController",\r\n"model":"MolajoModel",\r\n"cache":"1",\r\n"cache_time":"900"}', '{"metadata_title":"Content", "metadata_description":"Dashboard", "metadata_keywords":"dashboard", "metadata_robots":"follow, index", "metadata_author":"Author Name", "metadata_content_rights":"CC"}', 'en-GB', 0, 102);
INSERT INTO `molajo_content` VALUES(103, 4, 3000, 'Content', '', 'categories', 'content', '<p>Category for Content</p>', 0, 0, 0, 1, '2012-01-28 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-01-28 00:00:00', 100, '2012-01-28 00:00:00', 100, '0000-00-00 00:00:00', 0, 0, 0, 0, 0, 0, 0, NULL, '{\r\n    "1":{\r\n        "display_title":"Molajo Category 103: Site Configuration",\r\n\r\n        "display_view_on_no_results":"1",\r\n        "catalog_type_id":"10000",\r\n        "extension_catalog_type_id":"1050",\r\n\r\n        "criteria_enable_draft_save":"0",\r\n        "criteria_enable_version_history":"0",\r\n        "criteria_maximum_version_count":"5",\r\n        "criteria_enable_hit_counts":"0",\r\n        "criteria_enable_comments":"0",\r\n        "criteria_enable_ratings":"0",\r\n        "criteria_enable_notifications":"0",\r\n        "criteria_enable_tweets":"0",\r\n        "criteria_enable_ping":"0",\r\n        \r\n        "criteria_html5":"1",\r\n        "criteria_html_display_filter":"1",\r\n\r\n        "criteria_image_xsmall":"",\r\n        "criteria_image_small":"",\r\n        "criteria_image_medium":"",\r\n        "criteria_image_large":"",\r\n        "criteria_image_xlarge":"",\r\n        "criteria_image_folder":"",\r\n        "criteria_thumb_folder":"",\r\n\r\n        "criteria_asset_priority_site":"100",\r\n        "criteria_asset_priority_application":"200",\r\n        "criteria_asset_priority_user":"300",\r\n        "criteria_asset_priority_extension":"400",\r\n        "criteria_asset_priority_request":"500",\r\n        "criteria_asset_priority_category":"600",\r\n        "criteria_asset_priority_menu_item":"700",\r\n        "criteria_asset_priority_source":"800",\r\n        "criteria_asset_priority_theme":"900",\r\n\r\n        "theme_id":"119",\r\n\r\n        "page_view_id":"55",\r\n        "page_view_css_id":"",\r\n        "page_view_css_class":"",\r\n\r\n        "template_view_id":"236",\r\n        "template_view_css_id":"",\r\n        "template_view_css_class":"",\r\n        "wrap_view_id":"61",\r\n        "wrap_view_css_id":"",\r\n        "wrap_view_css_class":"",\r\n\r\n        "form_template_view_id":"25",\r\n        "form_template_view_css_id":"",\r\n        "form_template_view_css_class":"",\r\n        "form_wrap_view_id":"61",\r\n        "form_wrap_view_css_id":"",\r\n        "form_wrap_view_css_class":"",\r\n\r\n        "list_template_view_id":"33",\r\n        "list_template_view_css_id":"",\r\n        "list_template_view_css_class":"",\r\n        "list_wrap_view_id":"58",\r\n        "list_wrap_view_css_id":"",\r\n        "list_wrap_view_css_class":"",\r\n\r\n        "criteria_list_get_customfields":"1",\r\n        "criteria_list_get_item_children":"0",\r\n        "criteria_list_use_special_joins":"0",\r\n        "criteria_list_check_view_level_access":"0",\r\n\r\n        "criteria_list_display_archived_content":"1",\r\n        "criteria_list_display_featured_content":"0",\r\n        "criteria_list_display_stickied_content":"0",\r\n        "criteria_list_display_published_date_begin":"0",\r\n        "criteria_list_display_published_date_end":"1",\r\n        "criteria_list_display_category_list":"0",\r\n        "criteria_list_display_tag_list":"0",\r\n        "criteria_list_display_author_list":"0",\r\n        "criteria_list_begin":"1",\r\n        "criteria_list_length":"0",\r\n        "criteria_list_order_by_field1":"start_publishing_datetime",\r\n        "criteria_list_order_by_direction1":"DESC",\r\n        "criteria_list_order_by_field2":"",\r\n        "criteria_list_order_by_direction2":"",\r\n        "criteria_list_order_by_field3":"",\r\n        "criteria_list_order_by_direction3":"",\r\n\r\n        "feed_theme_id":"99",\r\n        "feed_page_view_id":"81",\r\n        "feed_limit":"10",\r\n        "feed_email":"author"\r\n\r\n     \r\n    },\r\n    "2":{\r\n        "display_title":"Molajo Category 103: Site Configuration",\r\n\r\n        "display_view_on_no_results":"1",\r\n        "catalog_type_id":"10000",\r\n        "extension_catalog_type_id":"1050",\r\n\r\n        "criteria_enable_draft_save":"0",\r\n        "criteria_enable_version_history":"0",\r\n        "criteria_maximum_version_count":"5",\r\n        "criteria_enable_hit_counts":"0",\r\n        "criteria_enable_comments":"0",\r\n        "criteria_enable_ratings":"0",\r\n        "criteria_enable_notifications":"0",\r\n        "criteria_enable_tweets":"0",\r\n        "criteria_enable_ping":"0",\r\n\r\n        "criteria_html5":"1",\r\n        "criteria_html_display_filter":"1",\r\n\r\n        "criteria_image_xsmall":"",\r\n        "criteria_image_small":"",\r\n        "criteria_image_medium":"",\r\n        "criteria_image_large":"",\r\n        "criteria_image_xlarge":"",\r\n        "criteria_image_folder":"",\r\n        "criteria_thumb_folder":"",\r\n\r\n        "criteria_asset_priority_site":"100",\r\n        "criteria_asset_priority_application":"200",\r\n        "criteria_asset_priority_user":"300",\r\n        "criteria_asset_priority_extension":"400",\r\n        "criteria_asset_priority_request":"500",\r\n        "criteria_asset_priority_category":"600",\r\n        "criteria_asset_priority_menu_item":"700",\r\n        "criteria_asset_priority_source":"800",\r\n        "criteria_asset_priority_theme":"900",\r\n\r\n        "theme_id":"119",\r\n\r\n        "page_view_id":"55",\r\n        "page_view_css_id":"",\r\n        "page_view_css_class":"",\r\n\r\n        "template_view_id":"236",\r\n        "template_view_css_id":"",\r\n        "template_view_css_class":"",\r\n        "wrap_view_id":"61",\r\n        "wrap_view_css_id":"",\r\n        "wrap_view_css_class":"",\r\n\r\n        "form_template_view_id":"25",\r\n        "form_template_view_css_id":"",\r\n        "form_template_view_css_class":"",\r\n        "form_wrap_view_id":"61",\r\n        "form_wrap_view_css_id":"",\r\n        "form_wrap_view_css_class":"",\r\n\r\n        "list_template_view_id":"33",\r\n        "list_template_view_css_id":"",\r\n        "list_template_view_css_class":"",\r\n        "list_wrap_view_id":"58",\r\n        "list_wrap_view_css_id":"",\r\n        "list_wrap_view_css_class":"",\r\n\r\n        "criteria_list_get_customfields":"1",\r\n        "criteria_list_get_item_children":"0",\r\n        "criteria_list_use_special_joins":"0",\r\n        "criteria_list_check_view_level_access":"0",\r\n\r\n        "criteria_list_display_archived_content":"1",\r\n        "criteria_list_display_featured_content":"0",\r\n        "criteria_list_display_stickied_content":"0",\r\n        "criteria_list_display_published_date_begin":"0",\r\n        "criteria_list_display_published_date_end":"1",\r\n        "criteria_list_display_category_list":"0",\r\n        "criteria_list_display_tag_list":"0",\r\n        "criteria_list_display_author_list":"0",\r\n        "criteria_list_begin":"1",\r\n        "criteria_list_length":"0",\r\n        "criteria_list_order_by_field1":"start_publishing_datetime",\r\n        "criteria_list_order_by_direction1":"DESC",\r\n        "criteria_list_order_by_field2":"",\r\n        "criteria_list_order_by_direction2":"",\r\n        "criteria_list_order_by_field3":"",\r\n        "criteria_list_order_by_direction3":"",\r\n\r\n        "feed_theme_id":"99",\r\n        "feed_page_view_id":"81",\r\n        "feed_limit":"10",\r\n        "feed_email":"author"\r\n    }\r\n}\r\n', '{"title":"Content Category 103", "description":"This is a category for content", \r\n"keywords":"category, content", \r\n"robots":"follow, index", \r\n"author":"Dr. Doolittle", \r\n"content_rights":"CC"}', 'en-GB', 0, 1);
INSERT INTO `molajo_content` VALUES(104, 2, 10000, 'Article 1', '', 'articles', 'article-alias-here', '<p>Lorizzle ipsizzle crazy check it out amizzle, consectetizzle adipiscing check it out. Nullizzle dope velizzle, go to hizzle volutpat, that''s the shizzle quis, boom shackalack vizzle, fizzle. Pellentesque fo shizzle mah nizzle fo rizzle, mah home g-dizzle dope.</p> \r\n <iframe width="420" height="315" src="http://www.youtube.com/embed/nsBvgAnhr7w" frameborder="0" allowfullscreen></iframe>\r\n<hr id="system-readmore" />\r\n<p>Bling bling erizzle. Crackalackin break yo neck, yall uhuh ... yih! mofo fo shizzle my nizzle shiz shizznit. Maurizzle shiznit boofron et turpizzle. Pizzle izzle tortizzle. Pellentesque sure rhoncizzle fo shizzle.</p>\r\n<p>In hac fo shizzle that''s the shizzle dictumst. Donec things. Da bomb tellizzle boom shackalack, ghetto eu, brizzle break it down, ass check out this, nunc. Bizzle suscipit. Integizzle sempizzle fizzle sizzle ghetto.</p>', 0, 0, 0, 1, '2012-01-28 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-01-28 00:00:00', 100, '2012-01-28 00:00:00', 100, '0000-00-00 00:00:00', 0, 0, 0, 0, 0, 0, 0, '{"image1_caption":"Caption",\r\n"image1_credit":"Credit to you",\r\n"image1_xsmall":"http://dummyimage.com/50x50/000/fff&text=xsmall",\r\n"image1_small":"http://dummyimage.com/75x75/000/fff&text=small",\r\n"image1_medium":"http://dummyimage.com/150x150/000/fff&text=medium",\r\n"image1_large":"http://dummyimage.com/300x300/000/fff&text=large",\r\n"image1_xlarge":"http://dummyimage.com/500x500/000/fff&text=xlarge",\r\n"link1":"https://twitter.com/Molajo",\r\n"link2":"https://github.com/Molajo/Molajo",\r\n"link3":"https://www.ohloh.net/p/Molajo",\r\n"video1":"http://t.co/QW4M4Ux3"}', '{\r\n    "1":{\r\n        "display_title":"Article 1",\r\n\r\n        "display_view_on_no_results":"1",\r\n        "catalog_type_id":"10000",\r\n\r\n        "criteria_enable_draft_save":"0",\r\n        "criteria_enable_version_history":"0",\r\n        "criteria_maximum_version_count":"5",\r\n        "criteria_enable_hit_counts":"0",\r\n        "criteria_enable_comments":"0",\r\n        "criteria_enable_ratings":"0",\r\n        "criteria_enable_notifications":"0",\r\n        "criteria_enable_tweets":"0",\r\n        "criteria_enable_ping":"0",\r\n\r\n        "theme_id":"",\r\n\r\n        "page_view_id":"",\r\n        "page_view_css_id":"",\r\n        "page_view_css_class":"",\r\n\r\n        "template_view_id":"",\r\n        "template_view_css_id":"",\r\n        "template_view_css_class":"",\r\n        "wrap_view_id":"",\r\n        "wrap_view_css_id":"",\r\n        "wrap_view_css_class":"",\r\n\r\n        "form_template_view_id":"",\r\n        "form_template_view_css_id":"",\r\n        "form_template_view_css_class":"",\r\n        "form_wrap_view_id":"",\r\n        "form_wrap_view_css_id":"",\r\n        "form_wrap_view_css_class":"",\r\n\r\n        "caching":"0",\r\n        "cache_time":"15",\r\n        "cache_handler":"file"\r\n    },\r\n    "2":{\r\n        "display_title":"Article 1",\r\n\r\n        "display_view_on_no_results":"1",\r\n        "catalog_type_id":"10000",\r\n\r\n        "criteria_enable_draft_save":"0",\r\n        "criteria_enable_version_history":"0",\r\n        "criteria_maximum_version_count":"5",\r\n        "criteria_enable_hit_counts":"0",\r\n        "criteria_enable_comments":"0",\r\n        "criteria_enable_ratings":"0",\r\n        "criteria_enable_notifications":"0",\r\n        "criteria_enable_tweets":"0",\r\n        "criteria_enable_ping":"0",\r\n\r\n        "theme_id":"",\r\n\r\n        "page_view_id":"",\r\n        "page_view_css_id":"",\r\n        "page_view_css_class":"",\r\n\r\n        "template_view_id":"",\r\n        "template_view_css_id":"",\r\n        "template_view_css_class":"",\r\n        "wrap_view_id":"",\r\n        "wrap_view_css_id":"",\r\n        "wrap_view_css_class":"",\r\n\r\n        "form_template_view_id":"",\r\n        "form_template_view_css_id":"",\r\n        "form_template_view_css_class":"",\r\n        "form_wrap_view_id":"",\r\n        "form_wrap_view_css_id":"",\r\n        "form_wrap_view_css_class":"",\r\n\r\n        "caching":"0",\r\n        "cache_time":"15",\r\n        "cache_handler":"file"\r\n    }\r\n}\r\n', '{"title":"Article 1", \r\n"description":"This is Article 1.",\r\n"keywords":"article, content", \r\n"robots":"follow, index", \r\n"author":"", \r\n"content_rights":""}\r\n', 'en-GB', 0, 1);
INSERT INTO `molajo_content` VALUES(105, 2, 10000, 'Article 2', '', 'articles', 'second-alias', '<p>You son of a bizzle non mi non maurizzle posuere bibendum. Aliquizzle dizzle viverra lectus.</p>\r\n<hr id="system-readmore" />\r\n<p>I saw beyonces tizzles and my pizzle went crizzle break yo neck, yall fo shizzle dang that''s the shizzle sodalizzle euismod. Fizzle lobortizzle, yippiyo my shizz dapibus izzle, nulla owned bibendum metizzle, gangster i saw beyonces tizzles and my pizzle went crizzle augue dui izzle nizzle. Vivamizzle bling bling lacizzle the bizzle ipsizzle.</p><p>Vivamizzle arcu magna, fermentizzle its fo rizzle my shizz, faucibus izzle, placerizzle izzle, mauris. Bizzle vehicula laorizzle owned. Vestibulizzle erat dizzle, hendrerizzle izzle, bling bling fo shizzle, crunk a, arcu.</p><p>Morbi fizzle placerizzle nulla. Maecenizzle mah nizzle erizzle gangsta yippiyo. Brizzle yippiyo sem, egestizzle funky fresh, accumsizzle quis, elementum shit, neque.</p><p>Ma nizzle iaculizzle that''s the shizzle sizzle orci tincidunt da bomb. Fusce sagittizzle, da bomb fo shizzle sollicitudizzle mollis, go to hizzle quam luctizzle erat, vitae dawg augue purus vitae ma nizzle.</p><p>Etizzle funky fresh lacus. Nizzle sizzle mi. Dizzle shizzle my nizzle crocodizzle rizzle. Vestibulum a magna. Sizzle bling bling erizzle, away id, yo mamma break it down, own yo'' in, pede.</p><p>Dizzle yo. Nulla fizzle erizzle, tristique break it down amet, ultricizzle mofo, yo nizzle, augue</p>', 0, 0, 0, 1, '2012-01-28 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-01-28 00:00:00', 100, '2012-01-28 00:00:00', 100, '0000-00-00 00:00:00', 0, 0, 0, 0, 0, 0, 0, '{"image1_caption":"Caption",\r\n"image1_credit":"Credit to you",\r\n"image1_xsmall":"http://dummyimage.com/50x50/000/fff&text=xsmall",\r\n"image1_small":"http://dummyimage.com/75x75/000/fff&text=small",\r\n"image1_medium":"http://dummyimage.com/150x150/000/fff&text=medium",\r\n"image1_large":"http://dummyimage.com/300x300/000/fff&text=large",\r\n"image1_xlarge":"http://dummyimage.com/500x500/000/fff&text=xlarge",\r\n"link1":"https://twitter.com/Molajo",\r\n"link2":"https://github.com/Molajo/Molajo",\r\n"link3":"https://www.ohloh.net/p/Molajo",\r\n"video1":"http://t.co/QW4M4Ux3"}', NULL, '{"metadata_title":"Article 2", "metadata_description":"This is Article 2.", "metadata_keywords":"article, content", "metadata_robots":"follow, index", "metadata_author":"Author Name", "metadata_content_rights":"CC"}', 'en-GB', 0, 2);
INSERT INTO `molajo_content` VALUES(106, 2, 10000, 'Article 3', '', 'articles', 'article-three', '<p>Owned metizzle urna, shit ghetto, phat et, sollicitudizzle mah nizzle, uhuh ... yih!. Donec pharetra, nisi doggy izzle malesuada, neque fizzle consequizzle velizzle, check it out fringilla libero get down get down izzle yo. Shizzlin dizzle aptent taciti sociosqu cool shizznit fo shizzle pizzle conubia nostra, pizzle inceptizzle you son of a bizzle. </p>\r\n<hr id="system-readmore" />\r\n<p>Funky fresh things orci, fo shizzle egizzle, bizzle bow wow wow, accumsan id, elit. Nunc crunk. Fusce velizzle brizzle, bling bling eu, gizzle, yo mamma izzle, ma nizzle. Curabitur sizzle. Aenizzle non dolizzle sure enim funky fresh pharetra. Integizzle nulla you son of a bizzle, laorizzle shizzlin dizzle, elementizzle quizzle, mollizzle sure, sheezy. Pimpin'' ut shiz. Nam nibh. Nulla uhuh ... yih!.</p><p>Nulla blandit. Maecenizzle phat magna, sempizzle non, black izzle, molestie quis, doggy. Morbi mi nibh, go to hizzle sed, mah nizzle eu, pretium izzle, velizzle. Morbi crunk things funky fresh felizzle i saw beyonces tizzles and my pizzle went crizzle get down get down.</p>', 0, 0, 0, 1, '2012-01-28 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-01-28 00:00:00', 100, '2012-01-28 00:00:00', 100, '0000-00-00 00:00:00', 0, 0, 0, 0, 0, 0, 0, '{"image1_caption":"Caption",\r\n"image1_credit":"Credit to you",\r\n"image1_xsmall":"http://dummyimage.com/50x50/000/fff&text=xsmall",\r\n"image1_small":"http://dummyimage.com/75x75/000/fff&text=small",\r\n"image1_medium":"http://dummyimage.com/150x150/000/fff&text=medium",\r\n"image1_large":"http://dummyimage.com/300x300/000/fff&text=large",\r\n"image1_xlarge":"http://dummyimage.com/500x500/000/fff&text=xlarge",\r\n"link1":"https://twitter.com/Molajo",\r\n"link2":"https://github.com/Molajo/Molajo",\r\n"link3":"https://www.ohloh.net/p/Molajo",\r\n"video1":"http://t.co/QW4M4Ux3"}', NULL, '{"metadata_title":"Article 3", "metadata_description":"This is Article 3.", "metadata_keywords":"article, content", "metadata_robots":"follow, index", "metadata_author":"Author Name", "metadata_content_rights":"CC"}', 'en-GB', 0, 3);
INSERT INTO `molajo_content` VALUES(107, 2, 10000, 'Article 4', '', 'articles', 'article-4', '<p>Integizzle non leo. Phasellizzle sempizzle, shizznit eu vehicula congue, fo shizzle mah nizzle fo rizzle, mah home g-dizzle erizzle dope brizzle, ma nizzle elementum sapien leo hizzle things.</p>\r\n<hr id="system-readmore" />\r\n<p>Vivamizzle get down get down laoreet felis. Etiam rhoncus tempor magna. I saw beyonces tizzles and my pizzle went crizzle interdizzle i saw beyonces tizzles and my pizzle went crizzle dawg. Vestibulum urna quam, rhoncizzle a, lacinia doggy, faucibus izzle, urna. Boofron eleifend get down get down mofo. </p><p>Yippiyo tincidunt gravida lectizzle. Fusce izzle yo mamma egizzle fizzle ultricizzle pulvinizzle. Get down get down mollis sodalizzle shiznit. Nam egestas, metizzle phat laorizzle shiz, nisl ghetto aliquizzle urna, vulputate phat velit rizzle izzle pimpin''.</p><p>Suspendisse shiz enizzle quizzle sem. Pimpin'' ac i''m in the shizzle et its fo rizzle euismizzle for sure. Phasellizzle shut the shizzle up lorizzle. Bizzle velit. Own yo'' izzle nibh.</p>', 0, 0, 0, 1, '2012-01-28 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-01-28 00:00:00', 100, '2012-01-28 00:00:00', 100, '0000-00-00 00:00:00', 0, 0, 0, 0, 0, 0, 0, '{"image1_caption":"Caption",\r\n"image1_credit":"Credit to you",\r\n"image1_xsmall":"http://dummyimage.com/50x50/000/fff&text=xsmall",\r\n"image1_small":"http://dummyimage.com/75x75/000/fff&text=small",\r\n"image1_medium":"http://dummyimage.com/150x150/000/fff&text=medium",\r\n"image1_large":"http://dummyimage.com/300x300/000/fff&text=large",\r\n"image1_xlarge":"http://dummyimage.com/500x500/000/fff&text=xlarge",\r\n"link1":"https://twitter.com/Molajo",\r\n"link2":"https://github.com/Molajo/Molajo",\r\n"link3":"https://www.ohloh.net/p/Molajo",\r\n"video1":"http://t.co/QW4M4Ux3"}', NULL, '{"metadata_title":"Article 4", "metadata_description":"This is Article 4.", "metadata_keywords":"article, content", "metadata_robots":"follow, index", "metadata_author":"Author Name", "metadata_content_rights":"CC"}', 'en-GB', 0, 4);
INSERT INTO `molajo_content` VALUES(108, 2, 10000, 'Article 5', '', 'articles', 'article-5', '<p>Crizzle brizzle pede boofron mi. Gizzle socizzle shiz doggy ghetto magnizzle izzle mah nizzle montizzle, nascetur ridiculus mus. 8-) Fo shizzle purizzle shut the shizzle up, molestie quis, convallizzle izzle, sollicitudin mofo, crunk. Bow wow wow viverra izzle commodo libero. Check out this sagittis. Nullizzle fo shizzle mah nizzle fo rizzle, mah home g-dizzle orci, black a, aliquam a, rizzle izzle, ipsum.</p>\r\n<hr id="system-readmore" />\r\n\r\n<p>Crizzle yo, nulla sure amet own yo'' gravida, check out this fo shizzle mah nizzle fo rizzle, mah home g-dizzle nizzle enizzle, a rizzle nunc est brizzle check out this. Check it out quis odio. Nizzle nonummy black nizzle metus. Nulla facilisi.</p>', 0, 0, 0, 1, '2012-01-28 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-01-28 00:00:00', 100, '2012-01-28 00:00:00', 100, '0000-00-00 00:00:00', 0, 0, 0, 0, 0, 0, 0, '{"image1_caption":"Caption",\r\n"image1_credit":"Credit to you",\r\n"image1_xsmall":"http://dummyimage.com/50x50/000/fff&text=xsmall",\r\n"image1_small":"http://dummyimage.com/75x75/000/fff&text=small",\r\n"image1_medium":"http://dummyimage.com/150x150/000/fff&text=medium",\r\n"image1_large":"http://dummyimage.com/300x300/000/fff&text=large",\r\n"image1_xlarge":"http://dummyimage.com/500x500/000/fff&text=xlarge",\r\n"link1":"https://twitter.com/Molajo",\r\n"link2":"https://github.com/Molajo/Molajo",\r\n"link3":"https://www.ohloh.net/p/Molajo",\r\n"video1":"http://t.co/QW4M4Ux3"}', NULL, '{"metadata_title":"Article 5", "metadata_description":"This is Article 5.", "metadata_keywords":"article, content", "metadata_robots":"follow, index", "metadata_author":"Author Name", "metadata_content_rights":"CC"}', 'en-GB', 0, 5);
INSERT INTO `molajo_content` VALUES(109, 2, 10000, 'Article 6', '', 'articles', 'article-6', '<p>Nulla that''s the shizzle. Etiam yo mamma pharetra break it down. Vestibulizzle boofron arcu phat mauris. Brizzle accumsizzle gangster et pimpin''. mofo nibh fo shizzle lectizzle.</p>', 0, 0, 0, 1, '2012-01-28 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-01-28 00:00:00', 100, '2012-01-28 00:00:00', 100, '0000-00-00 00:00:00', 0, 0, 0, 0, 0, 0, 0, '{"image1_caption":"Caption",\r\n"image1_credit":"Credit to you",\r\n"image1_xsmall":"http://dummyimage.com/50x50/000/fff&text=xsmall",\r\n"image1_small":"http://dummyimage.com/75x75/000/fff&text=small",\r\n"image1_medium":"http://dummyimage.com/150x150/000/fff&text=medium",\r\n"image1_large":"http://dummyimage.com/300x300/000/fff&text=large",\r\n"image1_xlarge":"http://dummyimage.com/500x500/000/fff&text=xlarge",\r\n"link1":"https://twitter.com/Molajo",\r\n"link2":"https://github.com/Molajo/Molajo",\r\n"link3":"https://www.ohloh.net/p/Molajo",\r\n"video1":"http://t.co/QW4M4Ux3"}', NULL, '{"metadata_title":"Article 6", "metadata_description":"This is Article 6.", "metadata_keywords":"article, content", "metadata_robots":"follow, index", "metadata_author":"Author Name", "metadata_content_rights":"CC"}', 'en-GB', 0, 6);
INSERT INTO `molajo_content` VALUES(110, 2, 10000, 'Article 7', '', 'articles', 'article-7', '<p>Crizzle laoreet, we gonna chung egizzle ma nizzle tincidunt, dolizzle sem yippiyo things, we have a very interesting video of cristina fizzle placerat fo shizzle mah nizzle fo rizzle, mah home g-dizzle maurizzle fo shizzle my nizzle mi.</p>', 0, 0, 0, 1, '2012-01-28 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-01-28 00:00:00', 100, '2012-01-28 00:00:00', 100, '0000-00-00 00:00:00', 0, 0, 0, 0, 0, 0, 0, '{"image1_caption":"Caption",\r\n"image1_credit":"Credit to you",\r\n"image1_xsmall":"http://dummyimage.com/50x50/000/fff&text=xsmall",\r\n"image1_small":"http://dummyimage.com/75x75/000/fff&text=small",\r\n"image1_medium":"http://dummyimage.com/150x150/000/fff&text=medium",\r\n"image1_large":"http://dummyimage.com/300x300/000/fff&text=large",\r\n"image1_xlarge":"http://dummyimage.com/500x500/000/fff&text=xlarge",\r\n"link1":"https://twitter.com/Molajo",\r\n"link2":"https://github.com/Molajo/Molajo",\r\n"link3":"https://www.ohloh.net/p/Molajo",\r\n"video1":"http://t.co/QW4M4Ux3"}', NULL, '{"metadata_title":"Article 7", "metadata_description":"This is Article 7.", "metadata_keywords":"article, content", "metadata_robots":"follow, index", "metadata_author":"Author Name", "metadata_content_rights":"CC"}', 'en-GB', 0, 7);
INSERT INTO `molajo_content` VALUES(111, 2, 10000, 'Article 8', '', 'articles', 'article-8', '<p>Shiznit interdizzle. Suspendisse potenti. Fo shizzle my nizzle nisl. Brizzle things ante, ullamcorper pimpin'', ullamcorpizzle go to hizzle, check out this phat, leo. Yo mamma egizzle that''s the shizzle.</p>', 0, 0, 0, 1, '2012-01-28 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-01-28 00:00:00', 100, '2012-01-28 00:00:00', 100, '0000-00-00 00:00:00', 0, 0, 0, 0, 0, 0, 0, '{"image1_caption":"Caption",\r\n"image1_credit":"Credit to you",\r\n"image1_xsmall":"http://dummyimage.com/50x50/000/fff&text=xsmall",\r\n"image1_small":"http://dummyimage.com/75x75/000/fff&text=small",\r\n"image1_medium":"http://dummyimage.com/150x150/000/fff&text=medium",\r\n"image1_large":"http://dummyimage.com/300x300/000/fff&text=large",\r\n"image1_xlarge":"http://dummyimage.com/500x500/000/fff&text=xlarge",\r\n"link1":"https://twitter.com/Molajo",\r\n"link2":"https://github.com/Molajo/Molajo",\r\n"link3":"https://www.ohloh.net/p/Molajo",\r\n"video1":"http://t.co/QW4M4Ux3"}', NULL, '{"metadata_title":"Article 8", "metadata_description":"This is Article 8.", "metadata_keywords":"article, content", "metadata_robots":"follow, index", "metadata_author":"Author Name", "metadata_content_rights":"CC"}', 'en-GB', 0, 8);
INSERT INTO `molajo_content` VALUES(112, 2, 10000, 'Article 9', '', 'articles', 'article-9', '<p>Yo, yo, yo. Nam egestas, metizzle phat laorizzle shiz, nisl ghetto aliquizzle urna, vulputate phat velit rizzle izzle pimpin''.Pimpin'' ac i''m in the shizzle et its fo rizzle euismizzle for sure. Phasellizzle shut the shizzle up lorizzle. Bizzle velit. Own yo'' izzle nibh.</p>', 0, 0, 0, 1, '2012-01-28 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-01-28 00:00:00', 100, '2012-01-28 00:00:00', 100, '0000-00-00 00:00:00', 0, 0, 0, 0, 0, 0, 0, '{"image1_caption":"Caption",\r\n"image1_credit":"Credit to you",\r\n"image1_xsmall":"http://dummyimage.com/50x50/000/fff&text=xsmall",\r\n"image1_small":"http://dummyimage.com/75x75/000/fff&text=small",\r\n"image1_medium":"http://dummyimage.com/150x150/000/fff&text=medium",\r\n"image1_large":"http://dummyimage.com/300x300/000/fff&text=large",\r\n"image1_xlarge":"http://dummyimage.com/500x500/000/fff&text=xlarge",\r\n"link1":"https://twitter.com/Molajo",\r\n"link2":"https://github.com/Molajo/Molajo",\r\n"link3":"https://www.ohloh.net/p/Molajo",\r\n"video1":"http://t.co/QW4M4Ux3"}', '', '{"metadata_title":"Article 9", "metadata_description":"This is Article 9.", "metadata_keywords":"article, content", "metadata_robots":"follow, index", "metadata_author":"Author Name", "metadata_content_rights":"CC"}', 'en-GB', 0, 9);
INSERT INTO `molajo_content` VALUES(113, 2, 10000, 'One long summer', '', 'articles', 'article-10', '<p>Phasellizzle check it out i''m in the shizzle tellus. Ut we gonna chung adipiscing lorizzle. Donizzle nizzle est. That''s the shizzle sapizzle massa, ultrices nizzle, accumsizzle dizzle, fo shizzle quis, pede. Duis nec shiznit. Etizzle rutrizzle ornare ante. Maurizzle fo shizzle.</p> \r\n<hr id="system-readmore" />\r\n<p>Vestibulizzle izzle pede fo shizzle nibh commodo commodo. Lorem ipsum dolizzle sizzle gangster, gangster adipiscing elit. Shizzle my nizzle crocodizzle phat mi. Check it out phat black, sodalizzle et, shiznit a, eleifend a, elit.</p>', 0, 0, 0, 1, '2012-01-28 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-01-28 00:00:00', 100, '2012-01-28 00:00:00', 100, '0000-00-00 00:00:00', 0, 0, 0, 0, 0, 0, 0, '{"image1_caption":"Caption",\r\n"image1_credit":"Credit to you",\r\n"image1_xsmall":"http://dummyimage.com/50x50/000/fff&text=xsmall",\r\n"image1_small":"http://dummyimage.com/75x75/000/fff&text=small",\r\n"image1_medium":"http://dummyimage.com/150x150/000/fff&text=medium",\r\n"image1_large":"http://dummyimage.com/300x300/000/fff&text=large",\r\n"image1_xlarge":"http://dummyimage.com/500x500/000/fff&text=xlarge",\r\n"link1":"https://twitter.com/Molajo",\r\n"link2":"https://github.com/Molajo/Molajo",\r\n"link3":"https://www.ohloh.net/p/Molajo",\r\n"video1":"http://t.co/QW4M4Ux3"}', NULL, '{"metadata_title":"Article 10", "metadata_description":"This is Article 10.", "metadata_keywords":"article, content", "metadata_robots":"follow, index", "metadata_author":"Author Name", "metadata_content_rights":"CC"}', 'en-GB', 0, 10);
INSERT INTO `molajo_content` VALUES(114, 100, 2000, 'Articles', '', '', 'articles', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, 101, 101, 1, 12, 1, 0, '{}', '{"display_title":"Article Manager",\r\n"display_suppress_no_results":"1",\r\n"disable_view_access_check":"0",\r\n"display_extension_instance_id":"2",\r\n"display_extension_catalog_id":"14",\r\n"display_extension_option":"articles",\r\n"criteria_catalog_type_id":"10000",\r\n"search":"1",\r\n"filters":"author,status,language",\r\n"select":"id,title,content_text,featured,Primarycategory,Author,start_publishing_datetime,ordering,language",\r\n"ordering":"a.start_publishing_datetime DESC",\r\n"item_methods":"itemExpandjsonfields,itemSplittext,itemSnippet,itemURL,itemDateformats,itemUserPermission",\r\n"toolbar_buttons":"new,edit,publish,feature,archive,checkin,restore,delete,trash,options",\r\n"submenu_items":"items,categories,drafts",\r\n"columns":"id,featured,title,created_by_name,start_publishing_datetime,ordering",\r\n"pagination":"",\r\n"batch":"",\r\n"theme_id":"",\r\n"page_view_id":"",\r\n"template_view_id":"33",\r\n"wrap_view_id":"61",\r\n"caching":"0",\r\n"cache_time":"15",\r\n"cache_handler":"file"}', '{"metadata_title":"Articles", "metadata_description":"Article Grid", "metadata_keywords":"articles, grid", "metadata_robots":"follow, index", "metadata_author":"Author Name", "metadata_content_rights":"CC"}', 'en-GB', 0, 103);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_extensions`
--

CREATE TABLE `molajo_extensions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `extension_site_id` int(11) unsigned NOT NULL DEFAULT '0',
  `catalog_type_id` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT ' ',
  PRIMARY KEY (`id`),
  KEY `extensions_extension_sites_index` (`extension_site_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=245 ;

--
-- Dumping data for table `molajo_extensions`
--

INSERT INTO `molajo_extensions` VALUES(0, 1, 1050, 'core');
INSERT INTO `molajo_extensions` VALUES(1, 1, 1050, 'applications');
INSERT INTO `molajo_extensions` VALUES(2, 1, 1050, 'articles');
INSERT INTO `molajo_extensions` VALUES(3, 1, 1050, 'catalog');
INSERT INTO `molajo_extensions` VALUES(4, 1, 1050, 'categories');
INSERT INTO `molajo_extensions` VALUES(5, 1, 1050, 'comments');
INSERT INTO `molajo_extensions` VALUES(6, 1, 1050, 'contacts');
INSERT INTO `molajo_extensions` VALUES(7, 1, 1050, 'dashboard');
INSERT INTO `molajo_extensions` VALUES(8, 1, 1050, 'extensions');
INSERT INTO `molajo_extensions` VALUES(9, 1, 1050, 'groups');
INSERT INTO `molajo_extensions` VALUES(10, 1, 1050, 'installer');
INSERT INTO `molajo_extensions` VALUES(11, 1, 1050, 'login');
INSERT INTO `molajo_extensions` VALUES(12, 1, 1050, 'media');
INSERT INTO `molajo_extensions` VALUES(13, 1, 1050, 'profile');
INSERT INTO `molajo_extensions` VALUES(14, 1, 1050, 'search');
INSERT INTO `molajo_extensions` VALUES(15, 1, 1050, 'users');
INSERT INTO `molajo_extensions` VALUES(16, 1, 1050, 'site');
INSERT INTO `molajo_extensions` VALUES(17, 1, 1050, 'redirects');
INSERT INTO `molajo_extensions` VALUES(18, 1, 1100, 'English (UK)');
INSERT INTO `molajo_extensions` VALUES(19, 1, 1200, 'dashboard');
INSERT INTO `molajo_extensions` VALUES(20, 1, 1200, 'dashboard-module');
INSERT INTO `molajo_extensions` VALUES(21, 1, 1200, 'default');
INSERT INTO `molajo_extensions` VALUES(22, 1, 1200, 'document-defer');
INSERT INTO `molajo_extensions` VALUES(23, 1, 1200, 'document-head');
INSERT INTO `molajo_extensions` VALUES(24, 1, 1200, 'dummy');
INSERT INTO `molajo_extensions` VALUES(25, 1, 1200, 'edit');
INSERT INTO `molajo_extensions` VALUES(26, 1, 1200, 'edit-editor');
INSERT INTO `molajo_extensions` VALUES(27, 1, 1200, 'edit-access-control');
INSERT INTO `molajo_extensions` VALUES(28, 1, 1200, 'edit-custom-fields');
INSERT INTO `molajo_extensions` VALUES(29, 1, 1200, 'edit-metadata');
INSERT INTO `molajo_extensions` VALUES(30, 1, 1200, 'edit-parameters');
INSERT INTO `molajo_extensions` VALUES(31, 1, 1200, 'edit-title');
INSERT INTO `molajo_extensions` VALUES(32, 1, 1200, 'edit-toolbar');
INSERT INTO `molajo_extensions` VALUES(33, 1, 1200, 'grid');
INSERT INTO `molajo_extensions` VALUES(34, 1, 1200, 'grid-batch');
INSERT INTO `molajo_extensions` VALUES(35, 1, 1200, 'grid-filters');
INSERT INTO `molajo_extensions` VALUES(36, 1, 1200, 'grid-pagination');
INSERT INTO `molajo_extensions` VALUES(37, 1, 1200, 'grid-submenu');
INSERT INTO `molajo_extensions` VALUES(38, 1, 1200, 'grid-table');
INSERT INTO `molajo_extensions` VALUES(39, 1, 1200, 'grid-title');
INSERT INTO `molajo_extensions` VALUES(40, 1, 1200, 'grid-toolbar');
INSERT INTO `molajo_extensions` VALUES(41, 1, 1200, 'page-header');
INSERT INTO `molajo_extensions` VALUES(42, 1, 1200, 'page-footer');
INSERT INTO `molajo_extensions` VALUES(43, 1, 1200, 'system-errors');
INSERT INTO `molajo_extensions` VALUES(44, 1, 1200, 'system-messages');
INSERT INTO `molajo_extensions` VALUES(55, 1, 1150, 'default');
INSERT INTO `molajo_extensions` VALUES(56, 1, 1150, 'system-error');
INSERT INTO `molajo_extensions` VALUES(57, 1, 1150, 'system-offline');
INSERT INTO `molajo_extensions` VALUES(58, 1, 1250, 'article');
INSERT INTO `molajo_extensions` VALUES(59, 1, 1250, 'aside');
INSERT INTO `molajo_extensions` VALUES(60, 1, 1250, 'default');
INSERT INTO `molajo_extensions` VALUES(61, 1, 1250, 'div');
INSERT INTO `molajo_extensions` VALUES(62, 1, 1250, 'footer');
INSERT INTO `molajo_extensions` VALUES(63, 1, 1250, 'header');
INSERT INTO `molajo_extensions` VALUES(64, 1, 1250, 'hgroup');
INSERT INTO `molajo_extensions` VALUES(65, 1, 1250, 'nav');
INSERT INTO `molajo_extensions` VALUES(66, 1, 1250, 'none');
INSERT INTO `molajo_extensions` VALUES(67, 1, 1250, 'section');
INSERT INTO `molajo_extensions` VALUES(68, 1, 1450, 'example');
INSERT INTO `molajo_extensions` VALUES(69, 1, 1450, 'molajo');
INSERT INTO `molajo_extensions` VALUES(70, 1, 1450, 'none');
INSERT INTO `molajo_extensions` VALUES(71, 1, 1450, 'article');
INSERT INTO `molajo_extensions` VALUES(72, 1, 1450, 'editor');
INSERT INTO `molajo_extensions` VALUES(73, 1, 1450, 'image');
INSERT INTO `molajo_extensions` VALUES(74, 1, 1450, 'pagebreak');
INSERT INTO `molajo_extensions` VALUES(75, 1, 1450, 'readmore');
INSERT INTO `molajo_extensions` VALUES(76, 1, 1450, 'logout');
INSERT INTO `molajo_extensions` VALUES(77, 1, 1450, 'molajo');
INSERT INTO `molajo_extensions` VALUES(78, 1, 1450, 'remember');
INSERT INTO `molajo_extensions` VALUES(79, 1, 1450, 'system');
INSERT INTO `molajo_extensions` VALUES(80, 1, 1450, 'molajo');
INSERT INTO `molajo_extensions` VALUES(81, 1, 1500, 'cleanslate');
INSERT INTO `molajo_extensions` VALUES(82, 1, 1500, 'molajito');
INSERT INTO `molajo_extensions` VALUES(83, 1, 1500, 'system');
INSERT INTO `molajo_extensions` VALUES(84, 1, 1300, 'Administrator Menu');
INSERT INTO `molajo_extensions` VALUES(85, 1, 1300, 'Main Menu');
INSERT INTO `molajo_extensions` VALUES(86, 1, 1350, 'catalogwidget');
INSERT INTO `molajo_extensions` VALUES(87, 1, 1350, 'aclwidget');
INSERT INTO `molajo_extensions` VALUES(88, 1, 1350, 'breadcrumbs');
INSERT INTO `molajo_extensions` VALUES(89, 1, 1350, 'categorywidget');
INSERT INTO `molajo_extensions` VALUES(90, 1, 1350, 'content');
INSERT INTO `molajo_extensions` VALUES(91, 1, 1350, 'custom');
INSERT INTO `molajo_extensions` VALUES(92, 1, 1350, 'dashboard');
INSERT INTO `molajo_extensions` VALUES(93, 1, 1350, 'dashboard-module');
INSERT INTO `molajo_extensions` VALUES(94, 1, 1350, 'default');
INSERT INTO `molajo_extensions` VALUES(95, 1, 1350, 'document-defer');
INSERT INTO `molajo_extensions` VALUES(96, 1, 1350, 'document-head');
INSERT INTO `molajo_extensions` VALUES(97, 1, 1350, 'dummy');
INSERT INTO `molajo_extensions` VALUES(98, 1, 1350, 'page-header');
INSERT INTO `molajo_extensions` VALUES(99, 1, 1350, 'page-footer');
INSERT INTO `molajo_extensions` VALUES(100, 1, 1350, 'system-errors');
INSERT INTO `molajo_extensions` VALUES(101, 1, 1350, 'system-messages');
INSERT INTO `molajo_extensions` VALUES(102, 1, 1500, 'foundation');
INSERT INTO `molajo_extensions` VALUES(103, 1, 1500, 'bootstrap');
INSERT INTO `molajo_extensions` VALUES(104, 1, 1500, 'kendoui');
INSERT INTO `molajo_extensions` VALUES(105, 1, 1150, 'left-sidebar');
INSERT INTO `molajo_extensions` VALUES(106, 1, 1150, 'right-sidebar');
INSERT INTO `molajo_extensions` VALUES(107, 1, 1150, 'full');
INSERT INTO `molajo_extensions` VALUES(108, 1, 1150, 'both-sidebars');
INSERT INTO `molajo_extensions` VALUES(236, 1, 1200, 'Article');
INSERT INTO `molajo_extensions` VALUES(237, 1, 1350, ' admin-toolbar');
INSERT INTO `molajo_extensions` VALUES(238, 1, 1350, ' admin-submenu');
INSERT INTO `molajo_extensions` VALUES(239, 1, 1350, 'grid-filters');
INSERT INTO `molajo_extensions` VALUES(240, 1, 1350, 'grid-table');
INSERT INTO `molajo_extensions` VALUES(241, 1, 1350, 'grid-pagination');
INSERT INTO `molajo_extensions` VALUES(242, 1, 1350, 'grid-batch');
INSERT INTO `molajo_extensions` VALUES(243, 1, 1500, 'amazium');
INSERT INTO `molajo_extensions` VALUES(244, 1, 1500, 'Responsive');

-- --------------------------------------------------------

--
-- Table structure for table `molajo_extension_instances`
--

CREATE TABLE `molajo_extension_instances` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `extension_id` int(11) unsigned NOT NULL,
  `catalog_type_id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `subtitle` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Subtitle',
  `alias` varchar(255) NOT NULL DEFAULT ' ',
  `content_text` mediumtext,
  `protected` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `featured` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `stickied` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Published State 2: Archived 1: Published 0: Unpublished -1: Trashed -2: Spam -10 Version',
  `start_publishing_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish Begin Date and Time',
  `stop_publishing_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish End Date and Time',
  `version` int(11) unsigned NOT NULL DEFAULT '1' COMMENT 'Version Number',
  `version_of_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Primary ID for this Version',
  `status_prior_to_version` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'State value prior to creating this version copy and changing the state to Version',
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Created Date and Time',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Created by User ID',
  `modified_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modified Date',
  `modified_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Modified By User ID',
  `checked_out_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Checked out Date and Time',
  `checked_out_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Checked out by User Id',
  `customfields` mediumtext,
  `parameters` mediumtext COMMENT 'Attributes (Custom Fields)',
  `metadata` mediumtext,
  `language` char(7) NOT NULL DEFAULT 'en-GB',
  `translation_of_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  KEY `fk_extension_instances_extensions_index` (`extension_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=245 ;

--
-- Dumping data for table `molajo_extension_instances`
--

INSERT INTO `molajo_extension_instances` VALUES(0, 0, 1050, 'Core', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 0);
INSERT INTO `molajo_extension_instances` VALUES(1, 1, 1050, 'Applications', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(2, 2, 1050, 'Articles', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{\r\n    "1":{\r\n        "display_title":"Molajo Article Component: Site Configuration",\r\n\r\n        "display_view_on_no_results":"1",\r\n        "catalog_type_id":"10000",\r\n        "extension_catalog_type_id":"1050",\r\n\r\n        "criteria_enable_draft_save":"0",\r\n        "criteria_enable_version_history":"0",\r\n        "criteria_maximum_version_count":"5",\r\n        "criteria_enable_hit_counts":"0",\r\n        "criteria_enable_comments":"0",\r\n        "criteria_enable_ratings":"0",\r\n        "criteria_enable_notifications":"0",\r\n        "criteria_enable_tweets":"0",\r\n        "criteria_enable_ping":"0",\r\n        \r\n        "criteria_html5":"1",\r\n        "criteria_html_display_filter":"1",\r\n\r\n        "criteria_image_xsmall":"",\r\n        "criteria_image_small":"",\r\n        "criteria_image_medium":"",\r\n        "criteria_image_large":"",\r\n        "criteria_image_xlarge":"",\r\n        "criteria_image_folder":"",\r\n        "criteria_thumb_folder":"",\r\n\r\n        "criteria_asset_priority_site":"100",\r\n        "criteria_asset_priority_application":"200",\r\n        "criteria_asset_priority_user":"300",\r\n        "criteria_asset_priority_extension":"400",\r\n        "criteria_asset_priority_request":"500",\r\n        "criteria_asset_priority_category":"600",\r\n        "criteria_asset_priority_menu_item":"700",\r\n        "criteria_asset_priority_source":"800",\r\n        "criteria_asset_priority_theme":"900",\r\n\r\n        "theme_id":"119",\r\n\r\n        "page_view_id":"55",\r\n        "page_view_css_id":"",\r\n        "page_view_css_class":"",\r\n\r\n        "template_view_id":"236",\r\n        "template_view_css_id":"",\r\n        "template_view_css_class":"",\r\n        "wrap_view_id":"61",\r\n        "wrap_view_css_id":"",\r\n        "wrap_view_css_class":"",\r\n\r\n        "form_template_view_id":"25",\r\n        "form_template_view_css_id":"",\r\n        "form_template_view_css_class":"",\r\n        "form_wrap_view_id":"61",\r\n        "form_wrap_view_css_id":"",\r\n        "form_wrap_view_css_class":"",\r\n\r\n        "list_template_view_id":"33",\r\n        "list_template_view_css_id":"",\r\n        "list_template_view_css_class":"",\r\n        "list_wrap_view_id":"58",\r\n        "list_wrap_view_css_id":"",\r\n        "list_wrap_view_css_class":"",\r\n\r\n        "criteria_list_get_customfields":"1",\r\n        "criteria_list_get_item_children":"0",\r\n        "criteria_list_use_special_joins":"0",\r\n        "criteria_list_check_view_level_access":"0",\r\n\r\n        "criteria_list_display_archived_content":"1",\r\n        "criteria_list_display_featured_content":"0",\r\n        "criteria_list_display_stickied_content":"0",\r\n        "criteria_list_display_published_date_begin":"0",\r\n        "criteria_list_display_published_date_end":"1",\r\n        "criteria_list_display_category_list":"0",\r\n        "criteria_list_display_tag_list":"0",\r\n        "criteria_list_display_author_list":"0",\r\n        "criteria_list_begin":"1",\r\n        "criteria_list_length":"0",\r\n        "criteria_list_order_by_field1":"start_publishing_datetime",\r\n        "criteria_list_order_by_direction1":"DESC",\r\n        "criteria_list_order_by_field2":"",\r\n        "criteria_list_order_by_direction2":"",\r\n        "criteria_list_order_by_field3":"",\r\n        "criteria_list_order_by_direction3":"",\r\n\r\n        "feed_theme_id":"99",\r\n        "feed_page_view_id":"81",\r\n        "feed_limit":"10",\r\n        "feed_email":"author"\r\n\r\n     \r\n    },\r\n    "2":{\r\n        "display_title":"Molajo Article Component: Site Configuration",\r\n\r\n        "display_view_on_no_results":"1",\r\n        "catalog_type_id":"10000",\r\n        "extension_catalog_type_id":"1050",\r\n\r\n        "criteria_enable_draft_save":"0",\r\n        "criteria_enable_version_history":"0",\r\n        "criteria_maximum_version_count":"5",\r\n        "criteria_enable_hit_counts":"0",\r\n        "criteria_enable_comments":"0",\r\n        "criteria_enable_ratings":"0",\r\n        "criteria_enable_notifications":"0",\r\n        "criteria_enable_tweets":"0",\r\n        "criteria_enable_ping":"0",\r\n\r\n        "criteria_html5":"1",\r\n        "criteria_html_display_filter":"1",\r\n\r\n        "criteria_image_xsmall":"",\r\n        "criteria_image_small":"",\r\n        "criteria_image_medium":"",\r\n        "criteria_image_large":"",\r\n        "criteria_image_xlarge":"",\r\n        "criteria_image_folder":"",\r\n        "criteria_thumb_folder":"",\r\n\r\n        "criteria_asset_priority_site":"100",\r\n        "criteria_asset_priority_application":"200",\r\n        "criteria_asset_priority_user":"300",\r\n        "criteria_asset_priority_extension":"400",\r\n        "criteria_asset_priority_request":"500",\r\n        "criteria_asset_priority_category":"600",\r\n        "criteria_asset_priority_menu_item":"700",\r\n        "criteria_asset_priority_source":"800",\r\n        "criteria_asset_priority_theme":"900",\r\n\r\n        "theme_id":"119",\r\n\r\n        "page_view_id":"55",\r\n        "page_view_css_id":"",\r\n        "page_view_css_class":"",\r\n\r\n        "template_view_id":"236",\r\n        "template_view_css_id":"",\r\n        "template_view_css_class":"",\r\n        "wrap_view_id":"61",\r\n        "wrap_view_css_id":"",\r\n        "wrap_view_css_class":"",\r\n\r\n        "form_template_view_id":"25",\r\n        "form_template_view_css_id":"",\r\n        "form_template_view_css_class":"",\r\n        "form_wrap_view_id":"61",\r\n        "form_wrap_view_css_id":"",\r\n        "form_wrap_view_css_class":"",\r\n\r\n        "list_template_view_id":"33",\r\n        "list_template_view_css_id":"",\r\n        "list_template_view_css_class":"",\r\n        "list_wrap_view_id":"58",\r\n        "list_wrap_view_css_id":"",\r\n        "list_wrap_view_css_class":"",\r\n\r\n        "criteria_list_get_customfields":"1",\r\n        "criteria_list_get_item_children":"0",\r\n        "criteria_list_use_special_joins":"0",\r\n        "criteria_list_check_view_level_access":"0",\r\n\r\n        "criteria_list_display_archived_content":"1",\r\n        "criteria_list_display_featured_content":"0",\r\n        "criteria_list_display_stickied_content":"0",\r\n        "criteria_list_display_published_date_begin":"0",\r\n        "criteria_list_display_published_date_end":"1",\r\n        "criteria_list_display_category_list":"0",\r\n        "criteria_list_display_tag_list":"0",\r\n        "criteria_list_display_author_list":"0",\r\n        "criteria_list_begin":"1",\r\n        "criteria_list_length":"0",\r\n        "criteria_list_order_by_field1":"start_publishing_datetime",\r\n        "criteria_list_order_by_direction1":"DESC",\r\n        "criteria_list_order_by_field2":"",\r\n        "criteria_list_order_by_direction2":"",\r\n        "criteria_list_order_by_field3":"",\r\n        "criteria_list_order_by_direction3":"",\r\n\r\n        "feed_theme_id":"99",\r\n        "feed_page_view_id":"81",\r\n        "feed_limit":"10",\r\n        "feed_email":"author"\r\n    }\r\n}\r\n', '{"title":"Molajo Article Component", \r\n"description":"Welcome to the Molajo Article Component",\r\n"keywords":"articles, molajo", \r\n"robots":"follow, index",\r\n"author":"Amy Stephen", \r\n"content_rights":"CC"}', 'en-GB', 0, 2);
INSERT INTO `molajo_extension_instances` VALUES(3, 3, 1050, 'Catalog', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 3);
INSERT INTO `molajo_extension_instances` VALUES(4, 4, 1050, 'Categories', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 4);
INSERT INTO `molajo_extension_instances` VALUES(5, 5, 1050, 'Comments', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 5);
INSERT INTO `molajo_extension_instances` VALUES(6, 6, 1050, 'Contacts', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 6);
INSERT INTO `molajo_extension_instances` VALUES(7, 7, 1050, 'Dashboard', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{"display_title":"Dashboard",\r\n"display_suppress_no_results":"0",\r\n"disable_view_access_check":"1",\r\n"template_view_id":"19",\r\n"wrap_view_id":"67",\r\n"controller":"MolajoDisplayController",\r\n"model":"MolajoModel",\r\n"caching":"0",\r\n"cache_time":"15",\r\n"cache_handler":"file"}', NULL, 'en-GB', 0, 7);
INSERT INTO `molajo_extension_instances` VALUES(8, 8, 1050, 'Extensions', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 8);
INSERT INTO `molajo_extension_instances` VALUES(9, 9, 1050, 'Groups', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 9);
INSERT INTO `molajo_extension_instances` VALUES(10, 10, 1050, 'Installer', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 10);
INSERT INTO `molajo_extension_instances` VALUES(11, 11, 1050, 'Login', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 11);
INSERT INTO `molajo_extension_instances` VALUES(12, 12, 1050, 'Media', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 12);
INSERT INTO `molajo_extension_instances` VALUES(13, 13, 1050, 'Profile', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 13);
INSERT INTO `molajo_extension_instances` VALUES(14, 14, 1050, 'Search', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 14);
INSERT INTO `molajo_extension_instances` VALUES(15, 15, 1050, 'Users', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 15);
INSERT INTO `molajo_extension_instances` VALUES(16, 16, 1050, 'Sites', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 16);
INSERT INTO `molajo_extension_instances` VALUES(17, 17, 1050, 'Redirects', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 17);
INSERT INTO `molajo_extension_instances` VALUES(18, 18, 1100, 'English (UK)', 'en-GB', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 18);
INSERT INTO `molajo_extension_instances` VALUES(19, 19, 1200, 'Dashboard', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 19);
INSERT INTO `molajo_extension_instances` VALUES(20, 20, 1200, 'DashboardModule', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 20);
INSERT INTO `molajo_extension_instances` VALUES(21, 21, 1200, 'Default', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 21);
INSERT INTO `molajo_extension_instances` VALUES(22, 22, 1200, 'open', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 22);
INSERT INTO `molajo_extension_instances` VALUES(23, 23, 1200, 'Documenthead', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 23);
INSERT INTO `molajo_extension_instances` VALUES(24, 24, 1200, 'Dummy', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 24);
INSERT INTO `molajo_extension_instances` VALUES(25, 25, 1200, 'Edit', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 25);
INSERT INTO `molajo_extension_instances` VALUES(26, 26, 1200, 'Editeditor', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 26);
INSERT INTO `molajo_extension_instances` VALUES(27, 27, 1200, 'Editaccesscontrol', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 27);
INSERT INTO `molajo_extension_instances` VALUES(28, 28, 1200, 'Editcustomfields', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 28);
INSERT INTO `molajo_extension_instances` VALUES(29, 29, 1200, 'Editmetadata', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 29);
INSERT INTO `molajo_extension_instances` VALUES(30, 30, 1200, 'Editparameters', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 30);
INSERT INTO `molajo_extension_instances` VALUES(31, 31, 1200, 'Edittitle', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 31);
INSERT INTO `molajo_extension_instances` VALUES(32, 32, 1200, 'Edittoolbar', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 32);
INSERT INTO `molajo_extension_instances` VALUES(33, 33, 1200, 'Grid', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 33);
INSERT INTO `molajo_extension_instances` VALUES(34, 34, 1200, 'Gridbatch', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 34);
INSERT INTO `molajo_extension_instances` VALUES(35, 35, 1200, 'Gridfilters', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 35);
INSERT INTO `molajo_extension_instances` VALUES(36, 36, 1200, 'Gridpagination', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 36);
INSERT INTO `molajo_extension_instances` VALUES(37, 37, 1200, 'Adminsubmenu', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 37);
INSERT INTO `molajo_extension_instances` VALUES(38, 38, 1200, 'Gridtable', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 38);
INSERT INTO `molajo_extension_instances` VALUES(39, 39, 1200, 'Gridtitle', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 39);
INSERT INTO `molajo_extension_instances` VALUES(40, 40, 1200, 'Admintoolbar', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 40);
INSERT INTO `molajo_extension_instances` VALUES(41, 41, 1200, 'Pageheader', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '', NULL, 'en-GB', 0, 41);
INSERT INTO `molajo_extension_instances` VALUES(42, 42, 1200, 'Pagefooter', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{\r\n    "1":{\r\n    "display_title":"Molajo Article Component: Site Page Header",\r\n\r\n    "display_view_on_no_results":"1",\r\n\r\n    "template_view_id":"42",\r\n    "template_view_css_id":"",\r\n    "template_view_css_class":"",\r\n\r\n    "wrap_view_id":"61",\r\n    "wrap_view_css_id":"",\r\n    "wrap_view_css_class":"",\r\n\r\n    "caching":"0",\r\n    "cache_time":"15",\r\n    "cache_handler":"file"\r\n\r\n    },\r\n    "2":{\r\n\r\n        "display_title":"Molajo Article Component: Administrator Page Header",\r\n\r\n        "display_view_on_no_results":"1",\r\n\r\n        "template_view_id":"42",\r\n        "template_view_css_id":"",\r\n        "template_view_css_class":"",\r\n\r\n        "wrap_view_id":"61",\r\n        "wrap_view_css_id":"",\r\n        "wrap_view_css_class":"",\r\n\r\n        "caching":"0",\r\n        "cache_time":"15",\r\n        "cache_handler":"file"\r\n\r\n    }\r\n}', NULL, 'en-GB', 0, 42);
INSERT INTO `molajo_extension_instances` VALUES(43, 43, 1200, 'Systemerrors', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 43);
INSERT INTO `molajo_extension_instances` VALUES(44, 44, 1200, 'Systemmessages', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 44);
INSERT INTO `molajo_extension_instances` VALUES(55, 55, 1150, 'Default', 'pages', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 55);
INSERT INTO `molajo_extension_instances` VALUES(56, 56, 1150, 'Systemerrors', 'pages', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 56);
INSERT INTO `molajo_extension_instances` VALUES(57, 57, 1150, 'Systemoffline', 'pages', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 57);
INSERT INTO `molajo_extension_instances` VALUES(58, 58, 1250, 'Article', 'wraps', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 58);
INSERT INTO `molajo_extension_instances` VALUES(59, 59, 1250, 'Aside', 'wraps', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 59);
INSERT INTO `molajo_extension_instances` VALUES(60, 60, 1250, 'Default', 'wraps', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 60);
INSERT INTO `molajo_extension_instances` VALUES(61, 61, 1250, 'Div', 'wraps', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 61);
INSERT INTO `molajo_extension_instances` VALUES(62, 62, 1250, 'Footer', 'wraps', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 62);
INSERT INTO `molajo_extension_instances` VALUES(63, 63, 1250, 'Header', 'wraps', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 63);
INSERT INTO `molajo_extension_instances` VALUES(64, 64, 1250, 'Hgroup', 'wraps', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 64);
INSERT INTO `molajo_extension_instances` VALUES(65, 65, 1250, 'Nav', 'wraps', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 65);
INSERT INTO `molajo_extension_instances` VALUES(66, 66, 1250, 'None', 'wraps', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 66);
INSERT INTO `molajo_extension_instances` VALUES(67, 67, 1250, 'Section', 'wraps', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 67);
INSERT INTO `molajo_extension_instances` VALUES(82, 68, 1450, 'Example', 'acl', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 68);
INSERT INTO `molajo_extension_instances` VALUES(83, 69, 1450, 'Molajo', 'authentication', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 69);
INSERT INTO `molajo_extension_instances` VALUES(84, 70, 1450, 'None', 'editors', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 70);
INSERT INTO `molajo_extension_instances` VALUES(85, 71, 1450, 'Article', 'editor-buttons', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 71);
INSERT INTO `molajo_extension_instances` VALUES(86, 72, 1450, 'Editor', 'editor-buttons', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 72);
INSERT INTO `molajo_extension_instances` VALUES(87, 73, 1450, 'Image', 'editor-buttons', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 73);
INSERT INTO `molajo_extension_instances` VALUES(88, 74, 1450, 'Pagebreak', 'editor-buttons', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 74);
INSERT INTO `molajo_extension_instances` VALUES(89, 75, 1450, 'Readmore', 'editor-buttons', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 75);
INSERT INTO `molajo_extension_instances` VALUES(90, 76, 1450, 'Logout', 'system', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 76);
INSERT INTO `molajo_extension_instances` VALUES(91, 77, 1450, 'Molajo', 'system', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 77);
INSERT INTO `molajo_extension_instances` VALUES(92, 78, 1450, 'Remember', 'system', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 78);
INSERT INTO `molajo_extension_instances` VALUES(93, 79, 1450, 'System', 'system', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 79);
INSERT INTO `molajo_extension_instances` VALUES(94, 80, 1450, 'Molajo', 'user', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 80);
INSERT INTO `molajo_extension_instances` VALUES(97, 81, 1500, 'open', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 81);
INSERT INTO `molajo_extension_instances` VALUES(98, 82, 1500, 'Molajito', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 82);
INSERT INTO `molajo_extension_instances` VALUES(99, 83, 1500, 'System', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 83);
INSERT INTO `molajo_extension_instances` VALUES(100, 84, 1300, 'Administrator Menu', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 84);
INSERT INTO `molajo_extension_instances` VALUES(101, 85, 1300, 'Main Menu', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 85);
INSERT INTO `molajo_extension_instances` VALUES(103, 86, 1350, 'catalogwidget', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 86);
INSERT INTO `molajo_extension_instances` VALUES(104, 87, 1350, 'aclwidget', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 87);
INSERT INTO `molajo_extension_instances` VALUES(105, 88, 1350, 'breadcrumbs', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 88);
INSERT INTO `molajo_extension_instances` VALUES(106, 89, 1350, 'categorywidget', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 89);
INSERT INTO `molajo_extension_instances` VALUES(107, 90, 1350, 'content', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 90);
INSERT INTO `molajo_extension_instances` VALUES(108, 91, 1350, 'custom', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 91);
INSERT INTO `molajo_extension_instances` VALUES(109, 92, 1350, 'dashboard', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 92);
INSERT INTO `molajo_extension_instances` VALUES(110, 93, 1350, 'dashboard-module', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 93);
INSERT INTO `molajo_extension_instances` VALUES(111, 94, 1350, 'default', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 94);
INSERT INTO `molajo_extension_instances` VALUES(112, 95, 1350, 'document-defer', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 95);
INSERT INTO `molajo_extension_instances` VALUES(113, 96, 1350, 'document-head', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 96);
INSERT INTO `molajo_extension_instances` VALUES(114, 97, 1350, 'dummy', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 97);
INSERT INTO `molajo_extension_instances` VALUES(115, 98, 1350, 'Pageheader', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{\r\n    "1":{\r\n    "display_title":"Molajo Article Module: Site Page Header",\r\n\r\n    "display_view_on_no_results":"1",\r\n\r\n    "template_view_id":"41",\r\n    "template_view_css_id":"",\r\n    "template_view_css_class":"",\r\n\r\n    "wrap_view_id":"61",\r\n    "wrap_view_css_id":"",\r\n    "wrap_view_css_class":"",\r\n\r\n    "caching":"0",\r\n    "cache_time":"15",\r\n    "cache_handler":"file"\r\n\r\n    },\r\n    "2":{\r\n\r\n        "display_title":"Molajo Article Module: Administrator Page Header",\r\n\r\n        "display_view_on_no_results":"1",\r\n\r\n        "template_view_id":"41",\r\n        "template_view_css_id":"",\r\n        "template_view_css_class":"",\r\n\r\n        "wrap_view_id":"61",\r\n        "wrap_view_css_id":"",\r\n        "wrap_view_css_class":"",\r\n\r\n        "caching":"0",\r\n        "cache_time":"15",\r\n        "cache_handler":"file"\r\n\r\n    }\r\n}', NULL, 'en-GB', 0, 98);
INSERT INTO `molajo_extension_instances` VALUES(116, 99, 1350, 'Pagefooter', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{\r\n    "1":{\r\n        "site_name":"Molajo",\r\n        "link":"http://Molajo.org",\r\n        "linked_text":"Molajo&#174 ",\r\n        "remaining_text":"is Free Software",\r\n        \r\n        "display_title":"Molajo Article Component: Site Page Footer",\r\n\r\n        "display_view_on_no_results":"1",\r\n\r\n        "template_view_id":"42",\r\n        "template_view_css_id":"",\r\n        "template_view_css_class":"",\r\n\r\n        "wrap_view_id":"62",\r\n        "wrap_view_css_id":"",\r\n        "wrap_view_css_class":"",\r\n\r\n        "caching":"0",\r\n        "cache_time":"15",\r\n        "cache_handler":"file"\r\n\r\n    },\r\n    "2":{\r\n        "site_name":"Molajo",\r\n        "link":"http://Molajo.org",\r\n        "linked_text":"Molajo&#174",\r\n        "remaining_text":"is Free Software",\r\n        \r\n        "display_title":"Molajo Article Component: Administrator Page Footer",\r\n\r\n        "display_view_on_no_results":"1",\r\n\r\n        "template_view_id":"42",\r\n        "template_view_css_id":"",\r\n        "template_view_css_class":"",\r\n\r\n        "wrap_view_id":"62",\r\n        "wrap_view_css_id":"",\r\n        "wrap_view_css_class":"",\r\n\r\n        "caching":"0",\r\n        "cache_time":"15",\r\n        "cache_handler":"file"\r\n\r\n    }\r\n}', NULL, 'en-GB', 0, 99);
INSERT INTO `molajo_extension_instances` VALUES(117, 100, 1350, 'system-errors', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 100);
INSERT INTO `molajo_extension_instances` VALUES(118, 101, 1350, 'system-messages', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 101);
INSERT INTO `molajo_extension_instances` VALUES(119, 102, 1500, 'Bootstrap', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 81);
INSERT INTO `molajo_extension_instances` VALUES(120, 103, 1500, 'Foundation', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 81);
INSERT INTO `molajo_extension_instances` VALUES(121, 104, 1500, 'Jqueryuibootstrap', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 81);
INSERT INTO `molajo_extension_instances` VALUES(122, 105, 1150, 'Sidebar', 'pages', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 57);
INSERT INTO `molajo_extension_instances` VALUES(123, 106, 1150, 'Logon', 'pages', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 57);
INSERT INTO `molajo_extension_instances` VALUES(124, 107, 1150, 'Full', 'pages', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 57);
INSERT INTO `molajo_extension_instances` VALUES(125, 108, 1150, 'open', 'pages', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 57);
INSERT INTO `molajo_extension_instances` VALUES(236, 236, 1200, 'Article', 'templates', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 236);
INSERT INTO `molajo_extension_instances` VALUES(237, 237, 1350, 'admin-toolbar', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', '{"display_suppress_no_results":"0",\r\n"template_view_id":"40",\r\n"template_view_css_id":"",\r\n"template_view_css_class":"",\r\n"wrap_view_id":"63",\r\n"wrap_view_css_id":"",\r\n"wrap_view_css_class":"submenu",\r\n"cache":"1",\r\n"cache_time":"900"}', NULL, 'en-GB', 0, 86);
INSERT INTO `molajo_extension_instances` VALUES(238, 238, 1350, 'admin-submenu', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{"template_view_id":"37",\r\n"template_view_css_id":"",\r\n"template_view_css_class":"",\r\n"wrap_view_id":"65",\r\n"wrap_view_css_id":"",\r\n"wrap_view_css_class":"submenu",\r\n"cache":"1",\r\n"cache_time":"900"}', NULL, 'en-GB', 0, 86);
INSERT INTO `molajo_extension_instances` VALUES(239, 239, 1350, 'grid-filters', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{"template_view_id":"35",\r\n"template_view_css_id":"",\r\n"template_view_css_class":"",\r\n"wrap_view_id":"65",\r\n"wrap_view_css_id":"",\r\n"wrap_view_css_class":"filters",\r\n"cache":"1",\r\n"cache_time":"900"}', NULL, 'en-GB', 0, 86);
INSERT INTO `molajo_extension_instances` VALUES(240, 240, 1350, 'grid-table', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{"template_view_id":"38",\r\n"template_view_css_id":"",\r\n"template_view_css_class":"",\r\n"wrap_view_id":"67",\r\n"wrap_view_css_id":"",\r\n"wrap_view_css_class":"grid",\r\n"cache":"1",\r\n"cache_time":"900"}', NULL, 'en-GB', 0, 86);
INSERT INTO `molajo_extension_instances` VALUES(241, 241, 1350, 'grid-pagination', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{"template_view_id":"36",\r\n"template_view_css_id":"",\r\n"template_view_css_class":"",\r\n"wrap_view_id":"67",\r\n"wrap_view_css_id":"",\r\n"wrap_view_css_class":"pagination",\r\n"cache":"1",\r\n"cache_time":"900"}', NULL, 'en-GB', 0, 86);
INSERT INTO `molajo_extension_instances` VALUES(242, 242, 1350, 'grid-batch', '', '', '', 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{"template_view_id":"34",\r\n"template_view_css_id":"",\r\n"template_view_css_class":"",\r\n"wrap_view_id":"67",\r\n"wrap_view_css_id":"",\r\n"wrap_view_css_class":"batch",\r\n"cache":"1",\r\n"cache_time":"900"}', NULL, 'en-GB', 0, 86);
INSERT INTO `molajo_extension_instances` VALUES(243, 243, 1500, 'Amazium', '', '', '', 0, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 82);
INSERT INTO `molajo_extension_instances` VALUES(244, 244, 1500, 'Responsive', '', '', '', 0, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '{}', '{}', NULL, 'en-GB', 0, 82);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_extension_sites`
--

CREATE TABLE `molajo_extension_sites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT ' ',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `location` varchar(2048) NOT NULL,
  `customfields` mediumtext,
  `parameters` mediumtext,
  `metadata` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `molajo_extension_sites`
--

INSERT INTO `molajo_extension_sites` VALUES(1, 'Molajo Core', 1, 'http://update.molajo.org/core.xml', '', '', NULL);
INSERT INTO `molajo_extension_sites` VALUES(2, 'Molajo Directory', 1, 'http://update.molajo.org/directory.xml', '', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_group_permissions`
--

CREATE TABLE `molajo_group_permissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to #_groups.id',
  `catalog_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_catalog.id',
  `action_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_actions.id',
  PRIMARY KEY (`id`),
  KEY `fk_group_permissions_actions_index` (`action_id`),
  KEY `fk_group_permissions_content_index` (`group_id`),
  KEY `fk_group_permissions_catalog_index` (`catalog_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=122 ;

--
-- Dumping data for table `molajo_group_permissions`
--

INSERT INTO `molajo_group_permissions` VALUES(1, 1, 1, 3);
INSERT INTO `molajo_group_permissions` VALUES(2, 1, 2, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 1, 3, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 1, 5, 3);
INSERT INTO `molajo_group_permissions` VALUES(5, 1, 6, 3);
INSERT INTO `molajo_group_permissions` VALUES(6, 1, 7, 3);
INSERT INTO `molajo_group_permissions` VALUES(7, 1, 8, 3);
INSERT INTO `molajo_group_permissions` VALUES(8, 1, 9, 3);
INSERT INTO `molajo_group_permissions` VALUES(9, 1, 10, 3);
INSERT INTO `molajo_group_permissions` VALUES(10, 1, 12, 3);
INSERT INTO `molajo_group_permissions` VALUES(11, 1, 13, 3);
INSERT INTO `molajo_group_permissions` VALUES(12, 1, 14, 3);
INSERT INTO `molajo_group_permissions` VALUES(13, 1, 15, 3);
INSERT INTO `molajo_group_permissions` VALUES(14, 1, 16, 3);
INSERT INTO `molajo_group_permissions` VALUES(15, 1, 17, 3);
INSERT INTO `molajo_group_permissions` VALUES(16, 1, 18, 3);
INSERT INTO `molajo_group_permissions` VALUES(17, 1, 19, 3);
INSERT INTO `molajo_group_permissions` VALUES(18, 1, 20, 3);
INSERT INTO `molajo_group_permissions` VALUES(19, 1, 21, 3);
INSERT INTO `molajo_group_permissions` VALUES(20, 1, 22, 3);
INSERT INTO `molajo_group_permissions` VALUES(21, 1, 23, 3);
INSERT INTO `molajo_group_permissions` VALUES(22, 1, 24, 3);
INSERT INTO `molajo_group_permissions` VALUES(23, 1, 25, 3);
INSERT INTO `molajo_group_permissions` VALUES(24, 1, 26, 3);
INSERT INTO `molajo_group_permissions` VALUES(25, 1, 27, 3);
INSERT INTO `molajo_group_permissions` VALUES(26, 1, 28, 3);
INSERT INTO `molajo_group_permissions` VALUES(27, 1, 29, 3);
INSERT INTO `molajo_group_permissions` VALUES(28, 1, 30, 3);
INSERT INTO `molajo_group_permissions` VALUES(29, 1, 31, 3);
INSERT INTO `molajo_group_permissions` VALUES(30, 1, 32, 3);
INSERT INTO `molajo_group_permissions` VALUES(31, 1, 33, 3);
INSERT INTO `molajo_group_permissions` VALUES(32, 1, 34, 3);
INSERT INTO `molajo_group_permissions` VALUES(33, 1, 35, 3);
INSERT INTO `molajo_group_permissions` VALUES(34, 1, 36, 3);
INSERT INTO `molajo_group_permissions` VALUES(35, 1, 37, 3);
INSERT INTO `molajo_group_permissions` VALUES(36, 1, 38, 3);
INSERT INTO `molajo_group_permissions` VALUES(37, 1, 39, 3);
INSERT INTO `molajo_group_permissions` VALUES(38, 1, 40, 3);
INSERT INTO `molajo_group_permissions` VALUES(39, 1, 41, 3);
INSERT INTO `molajo_group_permissions` VALUES(40, 1, 42, 3);
INSERT INTO `molajo_group_permissions` VALUES(41, 1, 43, 3);
INSERT INTO `molajo_group_permissions` VALUES(42, 1, 44, 3);
INSERT INTO `molajo_group_permissions` VALUES(43, 1, 45, 3);
INSERT INTO `molajo_group_permissions` VALUES(44, 1, 46, 3);
INSERT INTO `molajo_group_permissions` VALUES(45, 1, 47, 3);
INSERT INTO `molajo_group_permissions` VALUES(46, 1, 48, 3);
INSERT INTO `molajo_group_permissions` VALUES(47, 1, 49, 3);
INSERT INTO `molajo_group_permissions` VALUES(48, 1, 50, 3);
INSERT INTO `molajo_group_permissions` VALUES(49, 1, 51, 3);
INSERT INTO `molajo_group_permissions` VALUES(50, 1, 52, 3);
INSERT INTO `molajo_group_permissions` VALUES(51, 1, 53, 3);
INSERT INTO `molajo_group_permissions` VALUES(52, 1, 54, 3);
INSERT INTO `molajo_group_permissions` VALUES(53, 1, 55, 3);
INSERT INTO `molajo_group_permissions` VALUES(54, 1, 56, 3);
INSERT INTO `molajo_group_permissions` VALUES(55, 1, 67, 3);
INSERT INTO `molajo_group_permissions` VALUES(56, 1, 68, 3);
INSERT INTO `molajo_group_permissions` VALUES(57, 1, 69, 3);
INSERT INTO `molajo_group_permissions` VALUES(58, 1, 70, 3);
INSERT INTO `molajo_group_permissions` VALUES(59, 1, 71, 3);
INSERT INTO `molajo_group_permissions` VALUES(60, 1, 72, 3);
INSERT INTO `molajo_group_permissions` VALUES(61, 1, 73, 3);
INSERT INTO `molajo_group_permissions` VALUES(62, 1, 74, 3);
INSERT INTO `molajo_group_permissions` VALUES(63, 1, 75, 3);
INSERT INTO `molajo_group_permissions` VALUES(64, 1, 76, 3);
INSERT INTO `molajo_group_permissions` VALUES(65, 1, 77, 3);
INSERT INTO `molajo_group_permissions` VALUES(66, 1, 78, 3);
INSERT INTO `molajo_group_permissions` VALUES(67, 1, 79, 3);
INSERT INTO `molajo_group_permissions` VALUES(68, 1, 155, 3);
INSERT INTO `molajo_group_permissions` VALUES(69, 1, 156, 3);
INSERT INTO `molajo_group_permissions` VALUES(70, 1, 157, 3);
INSERT INTO `molajo_group_permissions` VALUES(71, 1, 158, 3);
INSERT INTO `molajo_group_permissions` VALUES(72, 1, 96, 3);
INSERT INTO `molajo_group_permissions` VALUES(73, 1, 97, 3);
INSERT INTO `molajo_group_permissions` VALUES(74, 1, 98, 3);
INSERT INTO `molajo_group_permissions` VALUES(75, 1, 99, 3);
INSERT INTO `molajo_group_permissions` VALUES(76, 1, 100, 3);
INSERT INTO `molajo_group_permissions` VALUES(77, 1, 101, 3);
INSERT INTO `molajo_group_permissions` VALUES(78, 1, 102, 3);
INSERT INTO `molajo_group_permissions` VALUES(79, 1, 103, 3);
INSERT INTO `molajo_group_permissions` VALUES(80, 1, 104, 3);
INSERT INTO `molajo_group_permissions` VALUES(81, 1, 105, 3);
INSERT INTO `molajo_group_permissions` VALUES(82, 1, 106, 3);
INSERT INTO `molajo_group_permissions` VALUES(83, 1, 107, 3);
INSERT INTO `molajo_group_permissions` VALUES(84, 1, 108, 3);
INSERT INTO `molajo_group_permissions` VALUES(85, 1, 109, 3);
INSERT INTO `molajo_group_permissions` VALUES(86, 1, 110, 3);
INSERT INTO `molajo_group_permissions` VALUES(87, 1, 111, 3);
INSERT INTO `molajo_group_permissions` VALUES(88, 1, 112, 3);
INSERT INTO `molajo_group_permissions` VALUES(89, 1, 113, 3);
INSERT INTO `molajo_group_permissions` VALUES(90, 1, 80, 3);
INSERT INTO `molajo_group_permissions` VALUES(91, 1, 81, 3);
INSERT INTO `molajo_group_permissions` VALUES(92, 1, 82, 3);
INSERT INTO `molajo_group_permissions` VALUES(93, 1, 83, 3);
INSERT INTO `molajo_group_permissions` VALUES(94, 1, 84, 3);
INSERT INTO `molajo_group_permissions` VALUES(95, 1, 85, 3);
INSERT INTO `molajo_group_permissions` VALUES(96, 1, 86, 3);
INSERT INTO `molajo_group_permissions` VALUES(97, 1, 87, 3);
INSERT INTO `molajo_group_permissions` VALUES(98, 1, 88, 3);
INSERT INTO `molajo_group_permissions` VALUES(99, 1, 89, 3);
INSERT INTO `molajo_group_permissions` VALUES(100, 1, 90, 3);
INSERT INTO `molajo_group_permissions` VALUES(101, 1, 91, 3);
INSERT INTO `molajo_group_permissions` VALUES(102, 1, 92, 3);
INSERT INTO `molajo_group_permissions` VALUES(103, 1, 93, 3);
INSERT INTO `molajo_group_permissions` VALUES(104, 1, 94, 3);
INSERT INTO `molajo_group_permissions` VALUES(105, 1, 95, 3);
INSERT INTO `molajo_group_permissions` VALUES(106, 1, 152, 3);
INSERT INTO `molajo_group_permissions` VALUES(107, 1, 153, 3);
INSERT INTO `molajo_group_permissions` VALUES(108, 1, 154, 3);
INSERT INTO `molajo_group_permissions` VALUES(109, 1, 139, 3);
INSERT INTO `molajo_group_permissions` VALUES(110, 1, 141, 3);
INSERT INTO `molajo_group_permissions` VALUES(111, 1, 140, 3);
INSERT INTO `molajo_group_permissions` VALUES(112, 1, 142, 3);
INSERT INTO `molajo_group_permissions` VALUES(113, 1, 143, 3);
INSERT INTO `molajo_group_permissions` VALUES(114, 1, 144, 3);
INSERT INTO `molajo_group_permissions` VALUES(115, 1, 145, 3);
INSERT INTO `molajo_group_permissions` VALUES(116, 1, 146, 3);
INSERT INTO `molajo_group_permissions` VALUES(117, 1, 147, 3);
INSERT INTO `molajo_group_permissions` VALUES(118, 1, 148, 3);
INSERT INTO `molajo_group_permissions` VALUES(119, 1, 149, 3);
INSERT INTO `molajo_group_permissions` VALUES(120, 1, 150, 3);
INSERT INTO `molajo_group_permissions` VALUES(121, 1, 151, 3);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_group_view_groups`
--

CREATE TABLE `molajo_group_view_groups` (
  `group_id` int(11) unsigned NOT NULL COMMENT 'FK to the molajo_group table.',
  `view_group_id` int(11) unsigned NOT NULL COMMENT 'FK to the molajo_groupings table.',
  PRIMARY KEY (`view_group_id`,`group_id`),
  KEY `fk_group_view_groups_view_groups_index` (`view_group_id`),
  KEY `fk_group_view_groups_groups_index` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `molajo_group_view_groups`
--

INSERT INTO `molajo_group_view_groups` VALUES(1, 1);
INSERT INTO `molajo_group_view_groups` VALUES(2, 2);
INSERT INTO `molajo_group_view_groups` VALUES(3, 3);
INSERT INTO `molajo_group_view_groups` VALUES(4, 4);
INSERT INTO `molajo_group_view_groups` VALUES(3, 5);
INSERT INTO `molajo_group_view_groups` VALUES(4, 5);
INSERT INTO `molajo_group_view_groups` VALUES(6, 7);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_log`
--

CREATE TABLE `molajo_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Log Primary Key',
  `priority` int(11) DEFAULT NULL,
  `message` mediumtext,
  `date` datetime DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_category_date_priority` (`category`,`date`,`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `molajo_log`
--


-- --------------------------------------------------------

--
-- Table structure for table `molajo_sessions`
--

CREATE TABLE `molajo_sessions` (
  `session_id` varchar(32) NOT NULL,
  `application_id` int(11) unsigned NOT NULL,
  `session_time` varchar(14) DEFAULT ' ',
  `data` longtext,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`),
  KEY `fk_sessions_applications_index` (`application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `molajo_sessions`
--


-- --------------------------------------------------------

--
-- Table structure for table `molajo_sites`
--

CREATE TABLE `molajo_sites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Application Primary Key',
  `catalog_type_id` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `path` varchar(2048) NOT NULL DEFAULT ' ' COMMENT 'URL Alias',
  `base_url` varchar(2048) NOT NULL DEFAULT ' ',
  `description` mediumtext,
  `customfields` mediumtext,
  `parameters` mediumtext COMMENT 'Configurable Parameter Values',
  `metadata` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `molajo_sites`
--

INSERT INTO `molajo_sites` VALUES(1, 10, 'Molajo', '', '', 'Primary Site', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `molajo_site_applications`
--

CREATE TABLE `molajo_site_applications` (
  `site_id` int(11) unsigned NOT NULL,
  `application_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`site_id`,`application_id`),
  KEY `fk_site_applications_sites_index` (`site_id`),
  KEY `fk_site_applications_applications_index` (`application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `molajo_site_applications`
--

INSERT INTO `molajo_site_applications` VALUES(1, 1);
INSERT INTO `molajo_site_applications` VALUES(1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_site_extension_instances`
--

CREATE TABLE `molajo_site_extension_instances` (
  `site_id` int(11) unsigned NOT NULL,
  `extension_instance_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`site_id`,`extension_instance_id`),
  KEY `fk_site_extension_instances_sites_index` (`site_id`),
  KEY `fk_site_extension_instances_extension_instances_index` (`extension_instance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `molajo_site_extension_instances`
--

INSERT INTO `molajo_site_extension_instances` VALUES(1, 0);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 1);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 2);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 3);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 4);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 5);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 6);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 7);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 8);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 9);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 10);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 11);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 12);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 13);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 14);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 15);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 16);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 17);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 18);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 19);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 20);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 21);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 22);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 23);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 24);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 25);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 26);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 27);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 28);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 29);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 30);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 31);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 32);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 33);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 34);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 35);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 36);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 37);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 38);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 39);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 40);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 41);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 42);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 43);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 44);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 55);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 56);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 57);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 58);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 59);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 60);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 61);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 62);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 63);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 64);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 65);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 66);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 67);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 82);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 83);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 84);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 85);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 86);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 87);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 88);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 89);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 90);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 91);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 92);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 93);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 94);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 97);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 98);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 99);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 100);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 101);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 103);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 104);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 105);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 106);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 107);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 108);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 109);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 110);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 111);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 112);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 113);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 114);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 115);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 116);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 117);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 118);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 119);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 120);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 121);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 122);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 123);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 124);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 125);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 236);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 237);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 238);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 239);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 240);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 241);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 242);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 243);
INSERT INTO `molajo_site_extension_instances` VALUES(1, 244);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_users`
--

CREATE TABLE `molajo_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catalog_type_id` int(11) unsigned NOT NULL DEFAULT '0',
  `username` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT '',
  `last_name` varchar(150) DEFAULT '',
  `content_text` mediumtext,
  `email` varchar(255) DEFAULT '  ',
  `password` varchar(100) NOT NULL DEFAULT '  ',
  `block` tinyint(4) NOT NULL DEFAULT '0',
  `activation` varchar(100) NOT NULL DEFAULT '',
  `send_email` tinyint(4) NOT NULL DEFAULT '0',
  `register_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_visit_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `customfields` mediumtext,
  `parameters` mediumtext COMMENT 'Configurable Parameter Values',
  `metadata` mediumtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `last_name_first_name` (`last_name`,`first_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=101 ;

--
-- Dumping data for table `molajo_users`
--

INSERT INTO `molajo_users` VALUES(42, 500, 'admin', 'System', 'Administrator', '', 'admin@example.com', 'admin', 0, '1', 0, '2011-11-11 11:11:11', '0000-00-00 00:00:00', '{"gender":"F",\r\n"about_me":"I am the system administrator on this site, so watch out.",\r\n"twitter":"AmyStephen",\r\n"date_of_birth":"1961-09-17"}', '{"display_gravatar":"1",\r\n"display_birthdate":"1",\r\n"display_email":"1",\r\n"theme_id":"",\r\n"page_view_id":""}', '{}');
INSERT INTO `molajo_users` VALUES(100, 500, 'mark', 'Mark', 'Robinson', '<p>Great guy who sells insurance and coaches Little League.</p>', 'mark.robinson@example.com', 'mark', 0, '1', 0, '2011-11-02 17:45:17', '0000-00-00 00:00:00', '{"gender":"M",\r\n"about_me":"No search results about me on Google.",\r\n"twitter":"Test",\r\n"date_of_birth":"1991-01-06"}', '{"display_gravatar":"1",\r\n"display_birthdate":"1",\r\n"display_email":"1",\r\n"theme_id":"",\r\n"page_view_id":""}', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_user_activity`
--

CREATE TABLE `molajo_user_activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Foreign Key to molajo_users.id',
  `action_id` int(11) unsigned NOT NULL DEFAULT '0',
  `catalog_id` int(11) unsigned NOT NULL DEFAULT '0',
  `activity_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_activity_user_index` (`user_id`),
  KEY `user_activity_catalog_index` (`catalog_id`),
  KEY `user_activity_action_index` (`action_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `molajo_user_activity`
--

INSERT INTO `molajo_user_activity` VALUES(1, 42, 1, 3, '2012-05-01 00:03:01');
INSERT INTO `molajo_user_activity` VALUES(2, 42, 3, 147, '2012-05-01 00:03:01');

-- --------------------------------------------------------

--
-- Table structure for table `molajo_user_applications`
--

CREATE TABLE `molajo_user_applications` (
  `user_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_users.id',
  `application_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_applications.id',
  PRIMARY KEY (`application_id`,`user_id`),
  KEY `fk_user_applications_users_index` (`user_id`),
  KEY `fk_user_applications_applications_index` (`application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `molajo_user_applications`
--

INSERT INTO `molajo_user_applications` VALUES(42, 1);
INSERT INTO `molajo_user_applications` VALUES(42, 2);
INSERT INTO `molajo_user_applications` VALUES(100, 1);

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

--
-- Dumping data for table `molajo_user_groups`
--

INSERT INTO `molajo_user_groups` VALUES(42, 1);
INSERT INTO `molajo_user_groups` VALUES(42, 3);
INSERT INTO `molajo_user_groups` VALUES(42, 4);
INSERT INTO `molajo_user_groups` VALUES(42, 5);
INSERT INTO `molajo_user_groups` VALUES(100, 1);
INSERT INTO `molajo_user_groups` VALUES(100, 3);
INSERT INTO `molajo_user_groups` VALUES(100, 6);

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

--
-- Dumping data for table `molajo_user_view_groups`
--

INSERT INTO `molajo_user_view_groups` VALUES(42, 1);
INSERT INTO `molajo_user_view_groups` VALUES(42, 3);
INSERT INTO `molajo_user_view_groups` VALUES(42, 4);
INSERT INTO `molajo_user_view_groups` VALUES(42, 5);
INSERT INTO `molajo_user_view_groups` VALUES(100, 3);
INSERT INTO `molajo_user_view_groups` VALUES(100, 5);
INSERT INTO `molajo_user_view_groups` VALUES(100, 7);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_view_groups`
--

CREATE TABLE `molajo_view_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `view_group_name_list` text NOT NULL,
  `view_group_id_list` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `molajo_view_groups`
--

INSERT INTO `molajo_view_groups` VALUES(1, 'Public', '1');
INSERT INTO `molajo_view_groups` VALUES(2, 'Guest', '2');
INSERT INTO `molajo_view_groups` VALUES(3, 'Registered', '3');
INSERT INTO `molajo_view_groups` VALUES(4, 'Administrator', '4');
INSERT INTO `molajo_view_groups` VALUES(5, 'Registered, Administrator', '4,5');
INSERT INTO `molajo_view_groups` VALUES(6, 'Private', '5');
INSERT INTO `molajo_view_groups` VALUES(7, 'Private', '6');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=122 ;

--
-- Dumping data for table `molajo_view_group_permissions`
--

INSERT INTO `molajo_view_group_permissions` VALUES(1, 1, 1, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(2, 1, 2, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(3, 1, 3, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(4, 1, 5, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 1, 6, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(6, 1, 7, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(7, 1, 8, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(8, 1, 9, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(9, 1, 10, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(10, 1, 12, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(11, 1, 13, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(12, 1, 14, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(13, 1, 15, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(14, 1, 16, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(15, 1, 17, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(16, 1, 18, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(17, 1, 19, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(18, 1, 20, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(19, 1, 21, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(20, 1, 22, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(21, 1, 23, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(22, 1, 24, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(23, 1, 25, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(24, 1, 26, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(25, 1, 27, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(26, 1, 28, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(27, 1, 29, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(28, 1, 30, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(29, 1, 31, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(30, 1, 32, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(31, 1, 33, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(32, 1, 34, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(33, 1, 35, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(34, 1, 36, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(35, 1, 37, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(36, 1, 38, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(37, 1, 39, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(38, 1, 40, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(39, 1, 41, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(40, 1, 42, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(41, 1, 43, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(42, 1, 44, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(43, 1, 45, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(44, 1, 46, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(45, 1, 47, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(46, 1, 48, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(47, 1, 49, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(48, 1, 50, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(49, 1, 51, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(50, 1, 52, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(51, 1, 53, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(52, 1, 54, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(53, 1, 55, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(54, 1, 56, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(55, 1, 67, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(56, 1, 68, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(57, 1, 69, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(58, 1, 70, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(59, 1, 71, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(60, 1, 72, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(61, 1, 73, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(62, 1, 74, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(63, 1, 75, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(64, 1, 76, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(65, 1, 77, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(66, 1, 78, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(67, 1, 79, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(68, 1, 80, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(69, 1, 81, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(70, 1, 82, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(71, 1, 83, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(72, 1, 84, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(73, 1, 85, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(74, 1, 86, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(75, 1, 87, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(76, 1, 88, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(77, 1, 89, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(78, 1, 90, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(79, 1, 91, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(80, 1, 92, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(81, 1, 93, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(82, 1, 94, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(83, 1, 95, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(84, 1, 96, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(85, 1, 97, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(86, 1, 98, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(87, 1, 99, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(88, 1, 100, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(89, 1, 101, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(90, 1, 102, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(91, 1, 103, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(92, 1, 104, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(93, 1, 105, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(94, 1, 106, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(95, 1, 107, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(96, 1, 108, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(97, 1, 109, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(98, 1, 110, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(99, 1, 111, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(100, 1, 112, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(101, 1, 113, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(102, 1, 139, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(103, 1, 140, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(104, 3, 141, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(105, 3, 142, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(106, 3, 143, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(107, 3, 144, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(108, 3, 145, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(109, 3, 146, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(110, 3, 147, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(111, 3, 148, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(112, 3, 149, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(113, 3, 150, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(114, 3, 151, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(115, 1, 152, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(116, 1, 153, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(117, 1, 154, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(118, 1, 155, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(119, 1, 156, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(120, 1, 157, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(121, 1, 158, 3);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `molajo_application_extension_instances`
--
ALTER TABLE `molajo_application_extension_instances`
  ADD CONSTRAINT `fk_application_extensions_applications` FOREIGN KEY (`application_id`) REFERENCES `molajo_applications` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_application_extension_instances_extension_instances` FOREIGN KEY (`extension_instance_id`) REFERENCES `molajo_extension_instances` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_catalog`
--
ALTER TABLE `molajo_catalog`
  ADD CONSTRAINT `fk_catalog_catalog_types` FOREIGN KEY (`catalog_type_id`) REFERENCES `molajo_catalog_types` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_catalog_activity`
--
ALTER TABLE `molajo_catalog_activity`
  ADD CONSTRAINT `fk_catalog_activity_catalog` FOREIGN KEY (`catalog_id`) REFERENCES `molajo_catalog` (`catalog_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_catalog_categories`
--
ALTER TABLE `molajo_catalog_categories`
  ADD CONSTRAINT `fk_catalog_categories_catalog` FOREIGN KEY (`catalog_id`) REFERENCES `molajo_catalog` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_catalog_categories_categories` FOREIGN KEY (`category_id`) REFERENCES `molajo_content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_content`
--
ALTER TABLE `molajo_content`
  ADD CONSTRAINT `fk_content_extension_instances` FOREIGN KEY (`extension_instance_id`) REFERENCES `molajo_extension_instances` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_extensions`
--
ALTER TABLE `molajo_extensions`
  ADD CONSTRAINT `fk_extensions_extension_sites` FOREIGN KEY (`extension_site_id`) REFERENCES `molajo_extension_sites` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_extension_instances`
--
ALTER TABLE `molajo_extension_instances`
  ADD CONSTRAINT `fk_extension_instances_extensions` FOREIGN KEY (`extension_id`) REFERENCES `molajo_extensions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_group_permissions`
--
ALTER TABLE `molajo_group_permissions`
  ADD CONSTRAINT `fk_group_permissions_actions` FOREIGN KEY (`action_id`) REFERENCES `molajo_actions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_group_permissions_catalog` FOREIGN KEY (`catalog_id`) REFERENCES `molajo_catalog` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_group_permissions_content` FOREIGN KEY (`group_id`) REFERENCES `molajo_content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_group_view_groups`
--
ALTER TABLE `molajo_group_view_groups`
  ADD CONSTRAINT `fk_group_view_groups_groups` FOREIGN KEY (`group_id`) REFERENCES `molajo_content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_group_view_groups_view_groups` FOREIGN KEY (`view_group_id`) REFERENCES `molajo_view_groups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_sessions`
--
ALTER TABLE `molajo_sessions`
  ADD CONSTRAINT `fk_sessions_applications` FOREIGN KEY (`application_id`) REFERENCES `molajo_applications` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_site_applications`
--
ALTER TABLE `molajo_site_applications`
  ADD CONSTRAINT `fk_site_applications_applications` FOREIGN KEY (`application_id`) REFERENCES `molajo_applications` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_site_applications_sites` FOREIGN KEY (`site_id`) REFERENCES `molajo_sites` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_site_extension_instances`
--
ALTER TABLE `molajo_site_extension_instances`
  ADD CONSTRAINT `fk_site_extension_instances_extension_instances` FOREIGN KEY (`extension_instance_id`) REFERENCES `molajo_extension_instances` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_site_extension_instances_sites` FOREIGN KEY (`site_id`) REFERENCES `molajo_sites` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_user_activity`
--
ALTER TABLE `molajo_user_activity`
  ADD CONSTRAINT `fk_user_activity_stream_action_types_fk'` FOREIGN KEY (`action_id`) REFERENCES `molajo_actions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_activity_stream_catalog_fk` FOREIGN KEY (`catalog_id`) REFERENCES `molajo_catalog` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_applications_users_fk` FOREIGN KEY (`user_id`) REFERENCES `molajo_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_user_applications`
--
ALTER TABLE `molajo_user_applications`
  ADD CONSTRAINT `fk_user_applications_applications` FOREIGN KEY (`application_id`) REFERENCES `molajo_applications` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_applications_users` FOREIGN KEY (`user_id`) REFERENCES `molajo_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_user_groups`
--
ALTER TABLE `molajo_user_groups`
  ADD CONSTRAINT `fk_user_groups_groups` FOREIGN KEY (`group_id`) REFERENCES `molajo_content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_groups_users` FOREIGN KEY (`user_id`) REFERENCES `molajo_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_user_view_groups`
--
ALTER TABLE `molajo_user_view_groups`
  ADD CONSTRAINT `fk_user_view_groups_users` FOREIGN KEY (`user_id`) REFERENCES `molajo_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_view_groups_view_groups` FOREIGN KEY (`view_group_id`) REFERENCES `molajo_view_groups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_view_group_permissions`
--
ALTER TABLE `molajo_view_group_permissions`
  ADD CONSTRAINT `fk_view_group_permissions_actions` FOREIGN KEY (`action_id`) REFERENCES `molajo_actions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_view_group_permissions_catalog` FOREIGN KEY (`catalog_id`) REFERENCES `molajo_catalog` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_view_group_permissions_view_groups` FOREIGN KEY (`view_group_id`) REFERENCES `molajo_view_groups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
