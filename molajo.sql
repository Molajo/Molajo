-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 08, 2011 at 09:26 PM
-- Server version: 5.5.9
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `molajo`
--

-- --------------------------------------------------------

--
-- Table structure for table `molajo_content`
--

CREATE TABLE `molajo_content` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `title` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `subtitle` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Subtitle',
  `alias` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'URL Alias',
  `content_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Content Type: Links to #__configuration.option_id = 10 and component_option values matching ',
  `content_text` mediumtext COMMENT 'Content Primary Text Field, can include break to designate Introductory and Full text',
  `content_link` varchar(2083) DEFAULT NULL COMMENT 'Content Link for Weblink or Newsfeed Field',
  `content_email_address` varchar(255) DEFAULT NULL COMMENT 'Content Email Field',
  `content_numeric_value` tinyint(4) DEFAULT NULL COMMENT 'Content Numeric Value, ex. vote on poll',
  `content_file` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Content Network Path to File',
  `featured` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Featured 1-Featured 0-Not Featured',
  `stickied` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Stickied 1-Stickied 0-Not Stickied',
  `user_default` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'User DEFAULT 1-DEFAULT 0-Not DEFAULT',
  `category_default` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Category DEFAULT 1-DEFAULT 0-Not DEFAULT',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Published State 2: Archived 1: Published 0: Unpublished -1: Trashed -2: Spam -10 Version',
  `start_publishing_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish Begin Date and Time',
  `stop_publishing_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish End Date and Time',
  `version` int(11) unsigned NOT NULL DEFAULT '1' COMMENT 'Version Number',
  `version_of_id` int(11) DEFAULT NULL COMMENT 'Primary ID for this Version',
  `status_prior_to_version` int(11) unsigned DEFAULT NULL COMMENT 'State value prior to creating this version copy and changing the state to Version',
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Created Date and Time',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Created by User ID',
  `created_by_alias` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Created by Alias',
  `created_by_email` varchar(255) DEFAULT NULL COMMENT 'Created By Email Address',
  `created_by_website` varchar(255) DEFAULT NULL COMMENT 'Created By Website',
  `created_by_ip_address` char(15) DEFAULT NULL COMMENT 'Created By IP Address',
  `created_by_referer` varchar(255) DEFAULT NULL COMMENT 'Created By Referer',
  `modified_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modified Date',
  `modified_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Modified By User ID',
  `checked_out_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Checked out Date and Time',
  `checked_out_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Checked out by User Id',
  `parent_id` int(11) DEFAULT NULL COMMENT 'Nested set parent',
  `lft` int(11) DEFAULT NULL COMMENT 'Nested set lft',
  `rgt` int(11) DEFAULT NULL COMMENT 'Nested set rgt',
  `level` int(11) DEFAULT '0' COMMENT 'The cached level in the nested tree',
  `metakey` text COMMENT 'Meta Key',
  `metadesc` text COMMENT 'Meta Description',
  `metadata` text COMMENT 'Meta Data',
  `custom_fields` mediumtext COMMENT 'Attributes (Custom Fields)',
  `parameters` mediumtext COMMENT 'Configurable Parameter Values',
  `language` char(7) DEFAULT 'en-GB',
  `translation_of_id` int(11) DEFAULT NULL,
  `extension_instance_id` int(11) NOT NULL,
  `ordering` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  KEY `idx_component_component_id_id` (`id`),
  KEY `idx_checkout` (`checked_out_by`),
  KEY `idx_state` (`status`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_featured_catid` (`featured`),
  KEY `idx_stickied_catid` (`stickied`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `molajo_content`
--

INSERT INTO `molajo_content` VALUES(1, 'My First Article', 'Subtitle for My First Article', 'my-first-article', 10, '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>', NULL, NULL, NULL, '', 1, 0, 1, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-11-01 00:00:00', 42, '0000-00-00 00:00:00', 0, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 'en-GB', NULL, 2, 1);
INSERT INTO `molajo_content` VALUES(2, 'My Second Article', 'Subtitle for My Second Article', 'my-second-article', 10, '<h1>HTML Ipsum Presents</h1>\r\n	       \r\n<p><strong>Pellentesque habitant morbi tristique</strong> senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. <em>Aenean ultricies mi vitae est.</em> Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, <code>commodo vitae</code>, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. <a href="#">Donec non enim</a> in turpis pulvinar facilisis. Ut felis.</p>\r\n\r\n<h2>Header Level 2</h2>\r\n	       \r\n<ol>\r\n   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>\r\n   <li>Aliquam tincidunt mauris eu risus.</li>\r\n</ol>\r\n\r\n<blockquote><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus magna. Cras in mi at felis aliquet congue. Ut a est eget ligula molestie gravida. Curabitur massa. Donec eleifend, libero at sagittis mollis, tellus est malesuada tellus, at luctus turpis elit sit amet quam. Vivamus pretium ornare est.</p></blockquote>\r\n\r\n<h3>Header Level 3</h3>\r\n\r\n<ul>\r\n   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>\r\n   <li>Aliquam tincidunt mauris eu risus.</li>\r\n</ul>\r\n\r\n<pre><code>\r\n#header h1 a { \r\n	display: block; \r\n	width: 300px; \r\n	height: 80px; \r\n}\r\n</code></pre>', NULL, NULL, NULL, '', 0, 0, 0, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-11-01 00:00:00', 42, '0000-00-00 00:00:00', 0, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 'en-GB', NULL, 2, 2);
INSERT INTO `molajo_content` VALUES(3, 'My Third Article', 'Subtitle for My Third Article', 'my-third-article', 10, '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\r\n\r\n<ol>\r\n   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>\r\n   <li>Aliquam tincidunt mauris eu risus.</li>\r\n   <li>Vestibulum auctor dapibus neque.</li>\r\n</ol>\r\n\r\n<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\r\n	       ', NULL, NULL, NULL, '', 0, 0, 0, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-11-01 00:00:00', 42, '0000-00-00 00:00:00', 0, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 'en-GB', NULL, 2, 3);
INSERT INTO `molajo_content` VALUES(4, 'My Fourth Article', 'Subtitle for My Fourth Article', 'my-fourth-article', 10, '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>', NULL, NULL, NULL, '', 0, 0, 0, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-11-01 00:00:00', 42, '0000-00-00 00:00:00', 0, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 'en-GB', NULL, 2, 4);
