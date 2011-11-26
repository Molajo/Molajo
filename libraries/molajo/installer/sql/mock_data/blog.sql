#
# IMPORTANT - THIS FILE MUST BE SAVED WITH UTF-8 ENCODING ONLY. BEWARE IF EDITING!
#

#
# CONTENT
#

# CATEGORIES


#
# Content Categories
#
INSERT INTO `molajo_content`
    (`title`, `subtitle`, `alias`, `content_text`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `extension_instance_id`, `asset_type_id`, `version`, `parent_id`, `lft`, `rgt`, `lvl`, `language`, `ordering`)
  VALUES
    ('Content', '', 'content', '<p>Category for Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 2, 3000, 0, 0, 0, 0, 0, 'en-GB', 1);

INSERT INTO `molajo_assets`
  (`asset_type_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`,  `template_id`, `language`,  `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT 3000, `id`, `title`, CONCAT('categories/', `id`), 
    CONCAT('index.php?option=com_categories&id=', `id`), 10, 0, 'en-GB', 0, 0, 1
    FROM  molajo_content
    WHERE `title` = 'Content'
      AND asset_type_id = 3000;

# ARTICLES
SET @id = (SELECT id FROM `molajo_extension_instances` WHERE `title` = 'com_articles' AND `asset_type_id` = 1050);
SET @catid = (SELECT id FROM `molajo_content` WHERE `title` = 'Content' AND `asset_type_id` = 3000);
INSERT INTO `molajo_content`
    (`title`, `subtitle`, `alias`, `path`, `content_text`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `extension_instance_id`, `asset_type_id`, `version`, `parent_id`, `lft`, `rgt`, `lvl`, `language`, `ordering`)
  VALUES
    ('Article 1', '', 'article-1', 'content', '<p>Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', @id, 10000, 0, 0, 0, 0, 0, 'en-GB', 1),
    ('Article 2', '', 'article-2', 'content', '<p>Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', @id, 10000, 0, 0, 0, 0, 0, 'en-GB', 2),
    ('Article 3', '', 'article-3', 'content', '<p>Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', @id, 10000, 0, 0, 0, 0, 0, 'en-GB', 3),
    ('Article 4', '', 'article-4', 'content', '<p>Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', @id, 10000, 0, 0, 0, 0, 0, 'en-GB', 4),
    ('Article 5', '', 'article-5', 'content', '<p>Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', @id, 10000, 0, 0, 0, 0, 0, 'en-GB', 5),
    ('Article 6', '', 'article-6', 'content', '<p>Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', @id, 10000, 0, 0, 0, 0, 0, 'en-GB', 6),
    ('Article 7', '', 'article-7', 'content', '<p>Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', @id, 10000, 0, 0, 0, 0, 0, 'en-GB', 7),
    ('Article 8', '', 'article-8', 'content', '<p>Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', @id, 10000, 0, 0, 0, 0, 0, 'en-GB', 8),
    ('Article 9', '', 'article-9', 'content', '<p>Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', @id, 10000, 0, 0, 0, 0, 0, 'en-GB', 9),
    ('Article 10', '', 'article-10', 'content', '<p>Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', @id, 10000, 0, 0, 0, 0, 0, 'en-GB', 10);

INSERT INTO `molajo_assets` (`title`, `asset_type_id`, `source_id`, `sef_request`, `request`, `view_group_id`, `language`)
  SELECT `title`, `asset_type_id`, `id`, CONCAT(`path`, '/', `alias`), CONCAT('index.php?option=com_articles&view=article&id=', `id`), 1, 'en-GB'
    FROM `molajo_content`
    WHERE `asset_type_id` = 10000;