Create in phpMyAdmin

Change the prefix if necessary.

--
-- Table structure for table `jos_cron`
--

CREATE TABLE IF NOT EXISTS `jos_cron` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `extension_id` int(10) unsigned NOT NULL DEFAULT '0',
  `require_once` varchar(255) NOT NULL DEFAULT '',
  `user_function` text NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `enabled` tinyint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_extension_id` (`extension_id`),
  KEY `idx_title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `jos_cron`
--

INSERT INTO `jos_cron` (`id`, `title`, `extension_id`, `require_once`, `user_function`, `ordering`, `enabled`) VALUES
(1, 'Test', 0, 'plugins/system/cron/test.php', 'testClass::logFunction', 1, 1);