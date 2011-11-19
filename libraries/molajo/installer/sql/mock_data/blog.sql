#
# IMPORTANT - THIS FILE MUST BE SAVED WITH UTF-8 ENCODING ONLY. BEWARE IF EDITING!
#

#
# CONTENT
#

# CATEGORIES


#
# System Category
#
INSERT INTO `molajo_categories`
    (`id`, `title`, `subtitle`, `alias`, `content_text`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `extension_instance_id`, `content_type_id`, `version`, `parent_id`, `lft`, `rgt`, `level`, `language`, `ordering`)
  VALUES
    (2, 'Content', '', 'content', '<p>Category for Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 2, 3250, 0, 0, 0, 0, 0, 'en-GB', 1);

INSERT INTO `molajo_assets`
  (`content_type_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`,  `template_id`, `language`,  `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT 3250, `id`, `title`, 'categories/1', 'index.php?option=com_categories&id=1', 1, 0, 'en-GB', 0, 0, 1
    FROM  molajo_groups;

# ARTICLES
INSERT INTO `molajo_content`
    (`id`, `title`, `subtitle`, `alias`, `content_text`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `extension_instance_id`, `content_type_id`, `version`, `parent_id`, `lft`, `rgt`, `level`, `language`, `ordering`)
  VALUES
    (1, 'Article 1', '', 'content', '<p>Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 2, 10000, 0, 0, 0, 0, 0, 'en-GB', 1),
    (2, 'Article 2', '', 'content', '<p>Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 2, 10000, 0, 0, 0, 0, 0, 'en-GB', 2),
    (3, 'Article 3', '', 'content', '<p>Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 2, 10000, 0, 0, 0, 0, 0, 'en-GB', 3),
    (4, 'Article 4', '', 'content', '<p>Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 2, 10000, 0, 0, 0, 0, 0, 'en-GB', 4),
    (5, 'Article 5', '', 'content', '<p>Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 2, 10000, 0, 0, 0, 0, 0, 'en-GB', 5),
    (6, 'Article 6', '', 'content', '<p>Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 2, 10000, 0, 0, 0, 0, 0, 'en-GB', 6),
    (7, 'Article 7', '', 'content', '<p>Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 2, 10000, 0, 0, 0, 0, 0, 'en-GB', 7),
    (8, 'Article 8', '', 'content', '<p>Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 2, 10000, 0, 0, 0, 0, 0, 'en-GB', 8),
    (9, 'Article 9', '', 'content', '<p>Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 2, 10000, 0, 0, 0, 0, 0, 'en-GB', 9),
    (10, 'Article 10', '', 'content', '<p>Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 2, 10000, 0, 0, 0, 0, 0, 'en-GB', 10);

INSERT INTO `molajo_assets` (`title`, `content_type_id`, `source_id`, `sef_request`, `request`, `view_group_id`, `language`)
  SELECT `title`, 10000, `id`, CONCAT('articles/', `id`), CONCAT('index.php?option=com_articles&view=article&id=', `id`), 1, 'en-GB'
    FROM `molajo_content`;