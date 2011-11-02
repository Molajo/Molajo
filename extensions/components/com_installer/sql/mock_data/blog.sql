#
# IMPORTANT - THIS FILE MUST BE SAVED WITH UTF-8 ENCODING ONLY. BEWARE IF EDITING!
#

#
# CONTENT
#

# CATEGORIES
ALTER TABLE `molajo_categories`
  DROP INDEX `idx_asset_table_id_join`;

INSERT INTO `molajo_categories`
  (`id`, `title`, `subtitle`, `alias`, `content_text`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
  `version`, `extension_instance_id`, `parent_id`, `lft`, `rgt`, `level`, `language`, `ordering`)
  VALUES
    (1, 'ROOT', '', 'root', '<p>Root category</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 3, 0, 0, 0, 0, 'en-GB', 1),
    (2, 'Articles', 'com_articles', 'articles', '<p>Category for Articles</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 3, 1, 1, 2, 1, 'en-GB', 1);

INSERT INTO `molajo_assets` (`title`, `source_table_id`, `source_id`, `path`, `link`, `view_group_id`, `language`, `translation_of_id`)
  SELECT `title`, 2, `id`, CONCAT('categories/', `id`), CONCAT('index.php?option=com_categories&id=', `id`), 1, 'en-GB', NULL
    FROM `molajo_categories`

UPDATE `molajo_categories` a,
    `molajo_assets` b
  SET a.asset_id = b.id
WHERE a.id = b.source_id
  AND b.source_table_id = 2

ALTER TABLE `molajo_categories`
  ADD UNIQUE INDEX `idx_asset_table_id_join` (`asset_id` ASC);

# ARTICLES

ALTER TABLE `molajo_content`
  DROP INDEX `idx_asset_table_id_join`;

INSERT INTO `molajo_content` (
  `id`, `catid`, `title`, `subtitle`, `alias`, `content_type`, `content_text`, `content_link`, `content_email_address`, `content_numeric_value`, `content_file`, `featured`, `stickied`, `user_default`, `category_default`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `created_by_alias`, `created_by_email`, `created_by_website`, `created_by_ip_address`, `created_by_referer`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `asset_id`, `extension_id`, `parent_id`, `lft`, `rgt`, `level`, `metakey`, `metadesc`, `metadata`, `custom_fields`, `parameters`, `language`, `translation_of_id`, `ordering`)
  VALUES
  (1, 2, 'My First Article', 'Subtitle for My First Article', 'my-first-article', 10, '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>', NULL, NULL, NULL, '', 1, 0, 1, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-11-01 00:00:00', 42, '0000-00-00 00:00:00', 0, 0, 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 'en-GB', NULL, 1),
  (2, 2, 'My Second Article', 'Subtitle for My Second Article', 'my-second-article', 10, '<h1>HTML Ipsum Presents</h1>\r\n	       \r\n<p><strong>Pellentesque habitant morbi tristique</strong> senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. <em>Aenean ultricies mi vitae est.</em> Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, <code>commodo vitae</code>, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. <a href="#">Donec non enim</a> in turpis pulvinar facilisis. Ut felis.</p>\r\n\r\n<h2>Header Level 2</h2>\r\n	       \r\n<ol>\r\n   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>\r\n   <li>Aliquam tincidunt mauris eu risus.</li>\r\n</ol>\r\n\r\n<blockquote><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus magna. Cras in mi at felis aliquet congue. Ut a est eget ligula molestie gravida. Curabitur massa. Donec eleifend, libero at sagittis mollis, tellus est malesuada tellus, at luctus turpis elit sit amet quam. Vivamus pretium ornare est.</p></blockquote>\r\n\r\n<h3>Header Level 3</h3>\r\n\r\n<ul>\r\n   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>\r\n   <li>Aliquam tincidunt mauris eu risus.</li>\r\n</ul>\r\n\r\n<pre><code>\r\n#header h1 a { \r\n	display: block; \r\n	width: 300px; \r\n	height: 80px; \r\n}\r\n</code></pre>', NULL, NULL, NULL, '', 0, 0, 0, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-11-01 00:00:00', 42, '0000-00-00 00:00:00', 0, 0, 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 'en-GB', NULL, 2),
  (3, 2, 'My Third Article', 'Subtitle for My Third Article', 'my-third-article', 10, '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\r\n\r\n<ol>\r\n   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>\r\n   <li>Aliquam tincidunt mauris eu risus.</li>\r\n   <li>Vestibulum auctor dapibus neque.</li>\r\n</ol>\r\n\r\n<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\r\n	       ', NULL, NULL, NULL, '', 0, 0, 0, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-11-01 00:00:00', 42, '0000-00-00 00:00:00', 0, 0, 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 'en-GB', NULL, 3),
  (4, 2, 'My Fourth Article', 'Subtitle for My Fourth Article', 'my-fourth-article', 10, '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>', NULL, NULL, NULL, '', 0, 0, 0, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-11-01 00:00:00', 42, '0000-00-00 00:00:00', 0, 0, 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 'en-GB', NULL, 4),
  (5, 2, 'My Fifth Article', 'Subtitle for My Fifth Article', 'my-fifth-article', 10, '<dl> <dt>Definition list</dt>\r\n   <dd>Consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna \r\naliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea \r\ncommodo consequat.</dd>\r\n   <dt>Lorem ipsum dolor sit amet</dt>\r\n   <dd>Consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna \r\naliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea \r\ncommodo consequat.</dd>\r\n</dl>', NULL, NULL, NULL, '', 0, 0, 0, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-11-01 00:00:00', 42, '0000-00-00 00:00:00', 0, 0, 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 'en-GB', NULL, 5);

INSERT INTO `molajo_assets` (`title`, `source_table_id`, `source_id`, `path`, `link`, `view_group_id`, `language`, `translation_of_id`)
  SELECT `title`, 3, `id`, CONCAT('articles/', `id`), CONCAT('index.php?option=com_articles&view=article&id=', `id`), 1, 'en-GB', NULL
    FROM `molajo_content`

UPDATE `molajo_content` a,
    `molajo_assets` b
  SET a.asset_id = b.id
WHERE a.id = b.source_id
  AND b.source_table_id = 3

ALTER TABLE `molajo_content`
  ADD UNIQUE INDEX `idx_asset_table_id_join` (`asset_id` ASC);