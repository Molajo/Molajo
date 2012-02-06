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

# Asset records for Assets
INSERT INTO `molajo_assets`
  (`asset_type_id`, `source_id`, `routable`,
  `sef_request`, `request`, `request_option`, `request_model`,
  `redirect_to_id`, `view_group_id`, `primary_category_id`)
  SELECT 3000, `id`, true,
    CONCAT('category', '/', `alias`),
    CONCAT('index.php?option=categories&id=', `id`),
    'categories', 'category',
    0, 3, 0
    FROM `molajo_content`
        WHERE `asset_type_id` = 3000;

# ARTICLES
SET @id = (SELECT id FROM `molajo_extension_instances` WHERE `title` = 'articles' AND `asset_type_id` = 1050);
SET @category_id = (SELECT id FROM `molajo_content` WHERE `title` = 'Content' AND `asset_type_id` = 3000);
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

# Asset records for Assets
SET @category_id = (SELECT id FROM `molajo_content` WHERE `title` = 'Content' AND `asset_type_id` = 3000);
INSERT INTO `molajo_assets`
  (`asset_type_id`, `source_id`, `routable`,
  `sef_request`, `request`, `request_option`, `request_model`,
  `redirect_to_id`, `view_group_id`, `primary_category_id`)
  SELECT 10000, `id`, true,
    CONCAT(`path`, '/', `alias`),
    CONCAT('index.php?option=articles&model=article&id=', `id`),
    'articles', 'article',
    0, 3, @category_id
    FROM `molajo_content`
        WHERE `asset_type_id` = 10000;
