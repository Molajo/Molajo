#
# Molajo System Data
#

#
# Actions
#
INSERT INTO `molajo_actions` (`id` ,`title`)
  VALUES
    (1, 'login'),
    (2, 'create'),
    (3, 'view'),
    (4, 'edit'),
    (5, 'publish'),
    (6, 'delete'),
    (7, 'admin');

#
# Extension Types
#
INSERT INTO `molajo_extension_types` (`id` ,`extension_type`)
  VALUES
    (99, 'core'),
    (1, 'components'),
    (2, 'languages'),
    (3, 'layouts'),
    (4, 'manifests'),
    (5, 'menus'),
    (6, 'modules'),
    (7, 'parameters'),
    (8, 'plugins'),
    (9, 'templates'),
    (10, 'libraries');

UPDATE `molajo_extension_types`
SET id = 0
WHERE extension_type = 'core';

#
# Source Tables
#
INSERT INTO `molajo_source_tables` (`id` ,`source_table`, `option`)
  VALUES
    (1, '__applications', 'com_applications'),
    (2, '__categories', 'com_categories'),
    (3, '__content', 'com_articles'),
    (4, '__extension_instances', 'com_extensions'),
    (5, '__users', 'com_users'),
    (6, '__groups', 'com_groups'),
    (7, '__content', 'com_contacts'),
    (8, '__content', 'com_comments'),
    (9, '__content', 'com_media'),
    (10, '__extension_instance_options', 'com_extensions'),
    (11, '__dummy', 'com_dashboard'),
    (12, '__dummy', 'com_layouts'),
    (13, '__users', 'com_profile'),
    (14, '__assets', 'com_assets'),
    (15, '__dummy', 'com_maintain'),
    (16, '__dummy', 'com_installer'),
    (17, '__dummy', 'com_search');

#
# System Category
#
INSERT INTO `molajo_categories`
  (`id`, `title`, `subtitle`, `alias`, `content_text`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
  `version`, `parent_id`, `lft`, `rgt`, `level`, `language`, `ordering`)
  VALUES
    (1, 'ROOT', '', 'root', '<p>Root category</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, 0, 0, 'en-GB', 1);

UPDATE `molajo_categories`
  SET id = 0
  WHERE `title` = 'ROOT';

INSERT INTO `molajo_categories`
  (`id`, `title`, `subtitle`, `alias`, `content_text`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
  `version`, `parent_id`, `lft`, `rgt`, `level`, `language`, `ordering`)
  VALUES
    (1, 'System', '', 'system', '<p>System category</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, 0, 0, 'en-GB', 1);

# Do not add root category to assets

INSERT INTO `molajo_assets`
  (`source_table_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`, `language`, `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT 2, `id`, `title`, 'categories/1', 'index.php?option=com_categories&id=1', 1, 'en-GB', 0, 0, 1
    FROM  molajo_groups;

#
# SITES
#
INSERT INTO `molajo_sites` (`id`, `name`, `path`, `base_url`, `description`, `parameters`, `custom_fields`)
  VALUES
    (1, 'Molajo', '1', '', 'Primary Site', '{}', '{}');

#
# USERS AND GROUPS
#
INSERT INTO `molajo_groups`
  (`id`, `title`, `subtitle`, `description`, `type`, `parent_id`, `lft`, `rgt`, `protected`, `ordering` )
    VALUES
      (1, 'Public', '', 'All visitors regardless of authentication status', 1, 0, 1, 2, 1, 1),
      (2, 'Guest', '', 'Visitors not authenticated', 1, 0, 3, 4, 1, 2),
      (3, 'Registered', '', 'Authentication visitors', 1, 0, 5, 6, 1, 3),
      (4, 'Administrator', '', 'System Administrator', 1, 0, 7, 8, 1, 4);

INSERT INTO `molajo_assets`
  (`source_table_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`, `language`, `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT 6, `id`, `title`, CONCAT('groups/', `id`), CONCAT('index.php?option=com_groups&id=', `id`), 1, 'en-GB', 0, 0, 1
    FROM  molajo_groups
    ORDER BY `id`;

INSERT INTO `molajo_view_groups`
  (`id`, `view_group_name_list`, `view_group_id_list` )
    VALUES
      (1, 'Public', '1'),
      (2, 'Guest', '2'),
      (3, 'Registered', '3'),
      (4, 'Administrator', '4'),
      (5, 'Registered, Administrator', '4,5');

INSERT INTO `molajo_group_view_groups`
  ( `group_id` ,`view_group_id` )
  VALUES
    (1, 1),
    (2, 2),
    (3, 3),
    (4, 4),
    (3, 5),
    (4, 5);

#
# APPLICATIONS
#

INSERT INTO `molajo_applications` (`id`, `name`, `path`, `description`, `parameters`, `custom_fields`)
  VALUES
    (1, 'site', '', 'Primary application for site visitors', '{}', '{}'),
    (2, 'administrator', 'administrator', 'Administrative site area for site construction', '{}', '{}'),
    (3, 'content', 'content', 'Area for content development', '{}', '{}');

INSERT INTO `molajo_assets`
  (`source_table_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`, `language`, `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT 1, `id`, `name`, `path`, '', 1, 'en-GB', 0, 0, 1
    FROM  molajo_applications;

#
# UPDATE SITES
#
INSERT INTO `molajo_update_sites`
 (`id`, `name`, `enabled`, `location`)
  VALUES
    (1, 'Molajo Core', 1, 'http://update.molajo.org/core/list.xml'),
    (2, 'Molajo Directory', 1, 'http://update.molajo.org/directory/list.xml');

#
# EXTENSIONS
#

# CORE
INSERT INTO `molajo_extensions`
  (`name`, `extension_type_id`, `element`, `folder`, `update_site_id`)
  VALUES
    ('Core', 0, '', '', 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `extension_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `position`, `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `extension_type_id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `extension_type_id` = 0;
    
INSERT INTO `molajo_assets`
  (`source_table_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`, `language`, `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT 4, `id`, `title`, '', '', 1, 'en-GB', 0, 0, 1
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 0;
    
# Components

INSERT INTO `molajo_extensions`
  (`name`, `extension_type_id`, `element`, `folder`, `update_site_id`)
  VALUES
    ('com_articles', 1, '', '', 1),
    ('com_assets', 1, '', '', 1),
    ('com_categories', 1, '', '', 1),
    ('com_comments', 1, '', '', 1),
    ('com_configuration', 1, '', '', 1),
    ('com_contacts', 1, '', '', 1),
    ('com_dashboard', 1, '', '', 1),
    ('com_extensions', 1, '', '', 1),
    ('com_groups', 1, '', '', 1),
    ('com_installer', 1, '', '', 1),
    ('com_layouts', 1, '', '', 1),
    ('com_login', 1, '', '', 1),
    ('com_maintain', 1, '', '', 1),
    ('com_media', 1, '', '', 1),
    ('com_profile', 1, '', '', 1),
    ('com_search', 1, '', '', 1),
    ('com_users', 1, '', '', 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `extension_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `position`, `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `extension_type_id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `extension_type_id` = 1;

INSERT INTO `molajo_assets`
  (`source_table_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`, `language`, `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT 4, `id`, `name`, CONCAT('extensions/components/', `id`), CONCAT('index.php?option=com_extensions&view=components&id=', `id`), 1, 'en-GB', 0, 0, 1
    FROM `molajo_extensions`
    WHERE `extension_type_id` = 1;

# Languages

INSERT INTO `molajo_extensions`
  (`name`, `extension_type_id`, `element`, `folder`, `update_site_id`)
  VALUES
    ('English (UK)', 2, 'en-UK', '', 1),
    ('English (US)', 2, 'en-US', '', 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `extension_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `position`, `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `extension_type_id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `extension_type_id` = 2;

INSERT INTO `molajo_assets`
  (`source_table_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`, `language`, `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT 4, `id`, `name`, CONCAT('extensions/languages/', `id`), CONCAT('index.php?option=com_extensions&view=languages&id=', `id`), 1, 'en-GB', 0, 0, 1
    FROM `molajo_extensions`
    WHERE `extension_type_id` = 2;

# Layouts

INSERT INTO `molajo_extensions`
  (`name`, `extension_type_id`, `element`, `folder`, `update_site_id`)
  VALUES
    ('head', 3, '', 'document', 1),
    ('messages', 3, '', 'document', 1),
    ('errors', 3, '', 'document', 1),
    ('atom', 3, '', 'document', 1),
    ('rss', 3, '', 'document', 1),
    
    ('admin_acl_panel', 3, '', 'extension', 1),
    ('admin_activity', 3, '', 'extension', 1),
    ('admin_dashboard', 3, '', 'extension', 1),
    ('admin_edit', 3, '', 'extension', 1),
    ('admin_favorites', 3, '', 'extension', 1),
    ('admin_feed', 3, '', 'extension', 1),
    ('admin_footer', 3, '', 'extension', 1),
    ('admin_header', 3, '', 'extension', 1),
    ('admin_inbox', 3, '', 'extension', 1),
    ('admin_launchpad', 3, '', 'extension', 1),
    ('admin_list', 3, '', 'extension', 1),
    ('admin_login', 3, '', 'extension', 1),
    ('admin_modal', 3, '', 'extension', 1),
    ('admin_pagination', 3, '', 'extension', 1),
    ('admin_toolbar', 3, '', 'extension', 1),
    ('audio', 3, '', 'extension', 1),
    ('contact_form', 3, '', 'extension', 1),
    ('default', 3, '', 'extension', 1),
    ('dummy', 3, '', 'extension', 1),
    ('faq', 3, '', 'extension', 1),
    ('item', 3, '', 'extension', 1),
    ('list', 3, '', 'extension', 1),
    ('items', 3, '', 'extension', 1),
    ('list', 3, '', 'extension', 1),
    ('pagination', 3, '', 'extension', 1),
    ('social_bookmarks', 3, '', 'extension', 1),
    ('syntaxhighlighter', 3, '', 'extension', 1),
    ('table', 3, '', 'extension', 1),
    ('tree', 3, '', 'extension', 1),
    ('twig_example', 3, '', 'extension', 1),
    ('video', 3, '', 'extension', 1),

    ('button', 3, '', 'formfields', 1),
    ('colorpicker', 3, '', 'formfields', 1),
    ('datepicker', 3, '', 'formfields', 1),
    ('list', 3, '', 'formfields', 1),
    ('media', 3, '', 'formfields', 1),
    ('number', 3, '', 'formfields', 1),
    ('option', 3, '', 'formfields', 1),
    ('rules', 3, '', 'formfields', 1),
    ('spacer', 3, '', 'formfields', 1),
    ('text', 3, '', 'formfields', 1),
    ('textarea', 3, '', 'formfields', 1),
    ('user', 3, '', 'formfields', 1),
    
    ('article', 3, '', 'wrap', 1),
    ('aside', 3, '', 'wrap', 1),
    ('div', 3, '', 'wrap', 1),
    ('footer', 3, '', 'wrap', 1),
    ('header', 3, '', 'wrap', 1),
    ('horizontal', 3, '', 'wrap', 1),
    ('nav', 3, '', 'wrap', 1),
    ('none', 3, '', 'wrap', 1),
    ('outline', 3, '', 'wrap', 1),
    ('section', 3, '', 'wrap', 1),
    ('table', 3, '', 'wrap', 1),
    ('tabs', 3, '', 'wrap', 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `extension_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `position`, `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `extension_type_id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `extension_type_id` = 3;

INSERT INTO `molajo_assets`
  (`source_table_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`, `language`, `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT 4, `id`, `name`, CONCAT('extensions/layouts/', `id`), CONCAT('index.php?option=com_extensions&view=layouts&id=', `id`), 1, 'en-GB', 0, 0, 1
    FROM `molajo_extensions`
    WHERE `extension_type_id` = 3;

# Libraries

INSERT INTO `molajo_extensions`
  (`name`, `extension_type_id`, `element`, `folder`, `update_site_id`)
  VALUES
    ('Doctrine', 10, '', '', 1),
    ('includes', 10, '', '', 1),
    ('jplatform', 10, '', '', 1),
    ('molajo', 10, '', '', 1),
    ('Twig', 10, '', '', 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `extension_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `position`, `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `extension_type_id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `extension_type_id` = 10;

INSERT INTO `molajo_assets`
  (`source_table_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`, `language`, `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT 4, `id`, `name`, CONCAT('extensions/libraries/', `id`), CONCAT('index.php?option=com_extensions&view=libraries&id=', `id`), 1, 'en-GB', 0, 0, 1
    FROM `molajo_extensions`
    WHERE `extension_type_id` = 10;

# Modules

INSERT INTO `molajo_extensions`
  (`name`, `extension_type_id`, `element`, `folder`, `update_site_id`)
  VALUES
    ('mod_assetwidget', 6, '', '', 1),
    ('mod_aclwidget', 6, '', '', 1),
    ('mod_breadcrumbs', 6, '', '', 1),
    ('mod_debug', 6, '', '', 1),
    ('mod_categorywidget', 6, '', '', 1),
    ('mod_content', 6, '', '', 1),
    ('mod_custom', 6, '', '', 1),
    ('mod_groupwidget', 6, '', '', 1),
    ('mod_feed', 6, '', '', 1),
    ('mod_filters', 6, '', '', 1),
    ('mod_filebrowser', 6, '', '', 1),
    ('mod_footer', 6, '', '', 1),
    ('mod_gallery', 6, '', '', 1),
    ('mod_grid', 6, '', '', 1),
    ('mod_gridbatch', 6, '', '', 1),
    ('mod_header', 6, '', '', 1),
    ('mod_iconbutton', 6, '', '', 1),
    ('mod_launchpad', 6, '', '', 1),
    ('mod_layout', 6, '', '', 1),
    ('mod_login', 6, '', '', 1),
    ('mod_logout', 6, '', '', 1),
    ('mod_members', 6, '', '', 1),
    ('mod_menu', 6, '', '', 1),
    ('mod_pagination', 6, '', '', 1),
    ('mod_plugins', 6, '', '', 1),
    ('mod_quicklinks', 6, '', '', 1),
    ('mod_search', 6, '', '', 1),
    ('mod_submenu', 6, '', '', 1),
    ('mod_textbox', 6, '', '', 1),
    ('mod_title', 6, '', '', 1),
    ('mod_toolbar', 6, '', '', 1),
    ('mod_search', 6, '', '', 1),
    ('mod_syndicate', 6, '', '', 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `extension_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `position`, `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `extension_type_id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `extension_type_id` = 6;

INSERT INTO `molajo_assets`
  (`source_table_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`, `language`, `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT 4, `id`, `name`, CONCAT('extensions/modules/', `id`), CONCAT('index.php?option=com_extensions&view=modules&id=', `id`), 1, 'en-GB', 0, 0, 1
    FROM `molajo_extensions`
    WHERE `extension_type_id` = 6;

# Administrator Common
UPDATE `molajo_extension_instances`
  SET `position` = 'header'
  WHERE extension_type_id = 6
    AND `title` = 'mod_header';

UPDATE `molajo_extension_instances`
  SET `position` = 'launchpad'
  WHERE extension_type_id = 6
    AND `title` = 'mod_launchpad';

 UPDATE `molajo_extension_instances`
  SET `position` = 'footer'
  WHERE extension_type_id = 6
    AND `title` = 'mod_footer';

UPDATE `molajo_extension_instances`
  SET `position` = 'debug'
  WHERE extension_type_id = 6
    AND `title` = 'mod_debug';

# Administrator List Manager
UPDATE `molajo_extension_instances`
  SET `position` = 'title'
  WHERE extension_type_id = 6
    AND `title` = 'mod_title';

UPDATE `molajo_extension_instances`
  SET `position` = 'toolbar'
  WHERE extension_type_id = 6
    AND `title` = 'mod_toolbar';

 UPDATE `molajo_extension_instances`
  SET `position` = 'submenu'
  WHERE extension_type_id = 6
    AND `title` = 'mod_submenu';

UPDATE `molajo_extension_instances`
  SET `position` = 'filters'
  WHERE extension_type_id = 6
    AND `title` = 'mod_filters';

UPDATE `molajo_extension_instances`
  SET `position` = 'grid'
  WHERE extension_type_id = 6
    AND `title` = 'mod_grid';

UPDATE `molajo_extension_instances`
  SET `position` = 'gridbatch'
  WHERE extension_type_id = 6
    AND `title` = 'mod_gridbatch';

# Plugins

INSERT INTO `molajo_extensions`
  (`name`, `extension_type_id`, `element`, `folder`, `update_site_id`)
  VALUES
    ('example', 8, '', 'acl', 1),

    ('molajo', 8, '', 'authentication', 1),

    ('broadcast', 8, '', 'content', 1),
    ('content', 8, '', 'content', 1),
    ('emailcloak', 8, '', 'content', 1),
    ('links', 8, '', 'content', 1),
    ('loadmodule', 8, '', 'content', 1),
    ('media', 8, '', 'content', 1),
    ('protect', 8, '', 'content', 1),
    ('responses', 8, '', 'content', 1),

    ('aloha', 8, '', 'editors', 1),
    ('none', 8, '', 'editors', 1),

    ('article', 8, '', 'editor-buttons', 1),
    ('editor', 8, '', 'editor-buttons', 1),
    ('image', 8, '', 'editor-buttons', 1),
    ('pagebreak', 8, '', 'editor-buttons', 1),
    ('readmore', 8, '', 'editor-buttons', 1),

    ('molajo', 8, '', 'extension', 1),

    ('extend', 8, '', 'molajo', 1),
    ('minifier', 8, '', 'molajo', 1),
    ('search', 8, '', 'molajo', 1),
    ('tags', 8, '', 'molajo', 1),
    ('urls', 8, '', 'molajo', 1),

    ('molajosample', 8, '', 'query', 1),

    ('categories', 8, '', 'search', 1),
    ('articles', 8, '', 'search', 1),

    ('cache', 8, '', 'system', 1),
    ('compress', 8, '', 'system', 1),
    ('create', 8, '', 'system', 1),
    ('debug', 8, '', 'system', 1),
    ('languagefilter', 8, '', 'system', 1),
    ('log', 8, '', 'system', 1),
    ('logout', 8, '', 'system', 1),
    ('molajo', 8, '', 'system', 1),
    ('p3p', 8, '', 'system', 1),
    ('parameters', 8, '', 'system', 1),
    ('redirect', 8, '', 'system', 1),
    ('remember', 8, '', 'system', 1),
    ('system', 8, '', 'system', 1),
    ('webservices', 8, '', 'system', 1),

    ('molajo', 8, '', 'user', 1),
    ('profile', 8, '', 'user', 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `extension_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `position`, `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `extension_type_id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `extension_type_id` = 8;

INSERT INTO `molajo_assets`
  (`source_table_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`, `language`, `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT 4, `id`, `name`, CONCAT('extensions/plugins/', `id`), CONCAT('index.php?option=com_extensions&view=plugins&id=', `id`), 1, 'en-GB', 0, 0, 1
    FROM `molajo_extensions`
    WHERE `extension_type_id` = 8;

## Template

INSERT INTO `molajo_extensions`
  (`name`, `extension_type_id`, `element`, `folder`, `update_site_id`)
  VALUES
    ('construct', 9, '', '', 1),
    ('install', 9, '', '', 1),
    ('molajito', 9, '', '', 1),
    ('system', 9, '', '', 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `extension_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `position`, `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `extension_type_id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `extension_type_id` = 9;

INSERT INTO `molajo_assets`
  (`source_table_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`, `language`, `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT 4, `id`, `name`, CONCAT('extensions/templates/', `id`), CONCAT('index.php?option=com_extensions&view=templates&id=', `id`), 1, 'en-GB', 0, 0, 1
    FROM `molajo_extensions`
    WHERE `extension_type_id` = 9;

## Menu

INSERT INTO `molajo_extensions`
  (`name`, `extension_type_id`, `element`, `folder`, `update_site_id`)
  VALUES
    ('Admin', 5, '', '', 1),
    ('Main Menu', 5, '', '', 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `extension_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `position`, `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `extension_type_id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `extension_type_id` = 5;

INSERT INTO `molajo_assets`
  (`source_table_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`, `language`, `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT 4, `id`, `name`, CONCAT('extensions/menus/', `id`), CONCAT('index.php?option=com_extensions&view=menus&id=', `id`), 1, 'en-GB', 0, 0, 1
    FROM `molajo_extensions`
    WHERE `extension_type_id` = 5;

## ## Menu Items

## ## ## Admin: Content

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 1, 1, 'Root', '', 0, 0, 65, 0, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 2, 2, 'Content', 'content', 1, 1, 12, 1, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 3, 3, 'Articles', 'articles', 2, 2, 3, 2, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 4, 4, 'Contacts', 'contacts', 2, 4, 5, 2, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 5, 5, 'Comments', 'comments', 2, 6, 7, 2, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 6, 6, 'Layouts', 'layouts', 2, 8, 9, 2, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 7, 7, 'Media', 'media', 2, 10, 11, 2, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 8, 8, 'Users', 'users', 1, 13, 22, 1, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 9, 9, 'Profile', 'profile', 8, 14, 15, 2, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 10, 10, 'Users', 'users', 8, 16, 17, 2, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 11, 11, 'Groups', 'groups', 8, 18, 19, 2, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 12, 12, 'Assets', 'assets', 8, 20, 21, 2, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 13, 13, 'Interface', 'interface', 1, 23, 34, 1, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 14, 14, 'Categories', 'categories', 13, 24, 25, 2, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 15, 15, 'Menus', 'menus', 13, 26, 27, 2, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 16, 16, 'Menu Items', 'menuitems', 13, 28, 29, 2, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 17, 17, 'Modules', 'modules', 13, 30, 31, 2, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 18, 18, 'Templates', 'templates', 13, 32, 33, 2,  `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 19, 19, 'Options', 'options', 1, 35, 48, 1, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 20, 20, 'Site', 'sites', 19, 36, 37, 2, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 21, 21, 'Applications', 'applications', 19, 38, 39, 2, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 22, 22, 'Checkin', 'checkin', 19, 40, 41, 2, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 23, 23, 'Clean Cache', 'cleancache', 19, 42, 43, 2, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 24, 24, 'Redirects', 'redirects', 19, 44, 45, 2, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 25, 25, 'Plugins', 'plugins', 19, 46, 47, 2, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 26, 26, 'Install', 'install', 1, 49, 60, 1,  `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 27, 27, 'Create', 'create', 26, 50, 51, 2,  `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 28, 28, 'Install', 'install', 26, 52, 53, 2,  `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 29, 29, 'Discover', 'discover', 26, 54, 55, 2, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 30, 30, 'Update', 'update', 26, 56, 57, 2, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 31, 31, 'Uninstall', 'uninstall', 26, 58, 59, 2, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 32, 32, 'Search', 'search', 1, 61, 62, 1,  `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND `title` = 'Admin';

INSERT INTO `molajo_extension_instance_options` (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`, `extension_instance_id`, `extension_id`, `extension_type_id`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `image`, `option_id`, `option_value`, `option_value_literal`, `trigger_asset_id`, `position`, `menu_item_type`,  `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 33, 33, 'Home', 'home', 1, 63, 64, 1, `id`, `extension_id`, `extension_type_id`, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '', 0, '', '', 0, '', 1, '{}', '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5 AND NOT (`title` = 'Admin');

UPDATE `molajo_extension_instance_options`
   SET `protected` = 1;

##  Asset for working with the Menu Item URL 
INSERT INTO `molajo_assets`
  (`source_table_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`, `language`, `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT 4, `id`, `title`, CONCAT('extensions/menuitems/', `id`), CONCAT('index.php?option=com_extensions&view=menuitems&id=', `id`), 1, 'en-GB', 0, 0, 1
    FROM `molajo_extension_instance_options`
    WHERE `extension_type_id` = 5;

##  Actual Menu Item URL to destination 
INSERT INTO `molajo_assets`
  (`source_table_id`, `source_id`, `title`, `request`, `sef_request`,
  `primary_category_id`, `language`, `translation_of_id`, `redirect_to_id`, `view_group_id`)
  VALUES
  (11, 0, 'Content', 'index.php?option=com_dashboard&view=content', 'content', 1, 'en-GB', 0, 0, 2),
  (3, 0, 'Articles', 'index.php?option=com_articles', 'content/articles', 1, 'en-GB', 0, 0, 2),
  (7, 0, 'Contacts', 'index.php?option=com_contacts', 'content/contacts', 1, 'en-GB', 0, 0, 2),
  (8, 0, 'Comments', 'index.php?option=com_comments', 'content/comments', 1, 'en-GB', 0, 0, 2),
  (12, 0, 'Layouts', 'index.php?option=com_layouts', 'content/layouts', 1, 'en-GB', 0, 0, 2),
  (9, 0, 'Media', 'index.php?option=com_media', 'content/media', 1, 'en-GB', 0, 0, 2),

  (11, 0, 'Users', 'index.php?option=com_dashboard&view=users', 'users', 1, 'en-GB', 0, 0, 2),
  (13, 0, 'Profile', 'index.php?option=com_profile', 'users/profiles', 1, 'en-GB', 0, 0, 2),
  (5, 0, 'Users', 'index.php?option=com_users', 'users/users', 1, 'en-GB', 0, 0, 2),
  (6, 0, 'Groups', 'index.php?option=com_groups', 'users/groups', 1, 'en-GB', 0, 0, 2),
  (14, 0, 'Assets', 'index.php?option=com_assets&view=users', 'users/assets', 1, 'en-GB', 0, 0, 2),

  (11, 0, 'Interface', 'index.php?option=com_dashboard&view=interface', 'interface', 1, 'en-GB', 0, 0, 2),
  (2, 0, 'Categories', 'index.php?option=com_categories', 'interface/categories', 1, 'en-GB', 0, 0, 2),
  (4, 0, 'Menus', 'index.php?option=com_extensions&view=menus', 'interface/menus', 1, 'en-GB', 0, 0, 2),
  (10, 0, 'Menu Items', 'index.php?option=com_extensions&view=menuitems', 'interface/menuitems', 1, 'en-GB', 0, 0, 2),
  (4, 0, 'Modules', 'index.php?option=com_extensions&view=modules', 'interface/modules', 1, 'en-GB', 0, 0, 2),
  (4, 0, 'Templates', 'index.php?option=com_extensions&view=templates', 'interface/templates', 1, 'en-GB', 0, 0, 2),

  (11, 0, 'Options', 'index.php?option=com_dashboard&view=options', 'options', 1, 'en-GB', 0, 0, 2),
  (4, 0, 'Site', 'index.php?option=com_extensions&view=sites', 'options/sites', 1, 'en-GB', 0, 0, 2),
  (4, 0, 'Applications', 'index.php?option=com_extensions&view=applications', 'options/applications', 1, 'en-GB', 0, 0, 2),
  (15, 0, 'Checkin', 'index.php?option=com_maintain&view=checkin', 'options/checkin', 1, 'en-GB', 0, 0, 2),
  (15, 0, 'Clean Cache', 'index.php?option=com_maintain&view=cleancache', 'options/cleancache', 1, 'en-GB', 0, 0, 2),
  (15, 0, 'Redirects', 'index.php?option=com_maintain&view=redirects', 'options/redirects', 1, 'en-GB', 0, 0, 2),
  (4, 0, 'Plugins', 'index.php?option=com_extensions&view=plugins', 'options/plugins', 1, 'en-GB', 0, 0, 2),

  (11, 0, 'Install', 'index.php?option=com_dashboard&view=install', 'install', 1, 'en-GB', 0, 0, 2),
  (16, 0, 'Create', 'index.php?option=com_installer&view=create', 'install/create', 1, 'en-GB', 0, 0, 2),
  (16, 0, 'Install', 'index.php?option=com_installer&view=install', 'install/install', 1, 'en-GB', 0, 0, 2),
  (16, 0, 'Discover', 'index.php?option=com_installer&view=discover', 'install/discover', 1, 'en-GB', 0, 0, 2),
  (16, 0, 'Update', 'index.php?option=com_installer&view=update', 'install/update', 1, 'en-GB', 0, 0, 2),
  (16, 0, 'Uninstall', 'index.php?option=com_installer&view=uninstall', 'install/uninstall', 1, 'en-GB', 0, 0, 2),

  (17, 0, 'Search', 'index.php?option=com_search', 'search', 1, 'en-GB', 0, 0, 2),

  (12, 0, 'Home', 'index.php?option=com_layouts', 'home', 1, 'en-GB', 0, 0, 1);

##  Administrator 
INSERT INTO `molajo_users` (`id`, `username`, `first_name`, `last_name`, `content_text`, `email`, `password`, `block`, `activation`, `send_email`, `register_datetime`, `last_visit_datetime`, `parameters`, `custom_fields`) VALUES ('42', 'admin',  'Administrator',  '',  '',  'admin@example.com',  'admin',  '0',  '1',  '0',  '2011-11-11 11:11:11',  '0000-00-00 00:00:00', NULL ,  '');
INSERT INTO `molajo_user_applications` (`user_id`, `application_id`) VALUES (42, 1), (42, 2), (42, 3);
INSERT INTO `molajo_groups` (`title`, `subtitle`, `description`, `type`, `parent_id`, `lft`, `rgt`, `protected`, `ordering` ) SELECT CONCAT(`first_name`, ' ', `last_name`), '', '', 2, `id`, 0, 0, 1, 0 FROM `molajo_users` WHERE username = 'admin';
INSERT INTO `molajo_user_groups` (`user_id`, `group_id`) VALUES (42, 3), (42, 4);

##  Sample Registered User 
INSERT INTO `molajo_users` (`id`, `username`, `first_name`, `last_name`, `content_text`, `email`, `password`, `block`, `activation`, `send_email`, `register_datetime`, `last_visit_datetime`, `parameters`, `custom_fields`) VALUES ('100', 'mark', 'Mark', 'Robinson', '<p>Great guy who sells insurance and coaches Little League.</p>', 'mark.robinson@example.com', 'mark', '0', '1', '0', '2011-11-02 17:45:17', '0000-00-00 00:00:00', NULL, '{"favorite_color":"red","nickname":"Fred","claim_to_fame":"No search results for Mark on Google."}');
INSERT INTO `molajo_user_applications` (`user_id`, `application_id`) VALUES (100, 1), (100, 3);
INSERT INTO `molajo_groups` (`title`, `subtitle`, `description`, `type`, `parent_id`, `lft`, `rgt`, `protected`, `ordering` ) SELECT CONCAT(`first_name`, ' ', `last_name`), '', '', 2, `id`, 0, 0, 0, 0 FROM `molajo_users` WHERE username = 'mark';
INSERT INTO `molajo_user_groups` (`user_id`, `group_id`) VALUES (100, 3);

##  Authorize Users for their own group 
INSERT INTO `molajo_user_groups` (`user_id`, `group_id`) SELECT parent_id, id FROM `molajo_groups` WHERE type = 2;

##  user private view groups 
INSERT INTO `molajo_view_groups` (`view_group_name_list`, `view_group_id_list`, `type` ) SELECT 'Private', `id`, `type` FROM `molajo_groups` WHERE `type` = 2;

##  user private view group permission 
INSERT INTO `molajo_group_view_groups` ( `group_id` , `view_group_id` )
  SELECT a.`id`, b.`id`
  FROM `molajo_groups` a,
    `molajo_view_groups` b
  WHERE a.`type` = 2
    AND b.`type` = 2
    AND b.`view_group_id_list` = a.`id`;

## User View Group
INSERT INTO `molajo_user_view_groups`
  (`user_id`, `view_group_id`)
  SELECT DISTINCT a.`user_id`, b.`view_group_id`
    FROM `molajo_user_groups` a,
      `molajo_group_view_groups` b
    WHERE a.group_id = b.group_id;

## 
## SITE APPLICATIONS
##
INSERT INTO `molajo_site_applications`
  (`site_id`, `application_id`)
  VALUES
    (1, 1),
    (1, 2),
    (1, 3);

##  1. components 
INSERT INTO `molajo_application_extension_instances`
  (`application_id`, `extension_instance_id`)
  SELECT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 1
      AND b.id IN (1, 3)
       AND a.title IN
        ('com_articles',
          'com_comments',
          'com_contacts',
          'com_layouts',
          'com_login',
          'com_media',
          'com_search');

INSERT INTO `molajo_application_extension_instances`
  (`application_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 1
      AND b.id = 2
      AND a.title IN
        ('com_articles',
          'com_assets',
          'com_categories',
          'com_comments',
          'com_configuration',
          'com_contacts',
          'com_dashboard',
          'com_extensions',
          'com_groups',
          'com_installer',
          'com_layouts',
          'com_login',
          'com_media',
          'com_maintain',
          'com_profile',
          'com_search',
          'com_users');

##  2. language 
INSERT INTO `molajo_application_extension_instances`
  (`application_id`, `extension_instance_id`)
  SELECT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 2;

##  3. layouts 
INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 3;

##  5. menus 
INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 5
      AND NOT(a.title = 'Admin')
      AND b.id IN (1, 3);

INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 5
      AND a.title = 'Admin'
      AND b.id = 2;

##  6. modules 
INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 6
      AND b.id IN (1, 3)
      AND a.title IN
          ('mod_breadcrumbs',
          'mod_content',
          'mod_custom',
          'mod_feed',
          'mod_footer',
          'mod_header',
          'mod_layout',
          'mod_login',
          'mod_logout',
          'mod_menu',
          'mod_pagination',
          'mod_search',
          'mod_syndicate');

INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 6
      AND b.id = 2
      AND a.title IN
        ('mod_content',
        'mod_custom',
        'mod_debug',
        'mod_feed',
        'mod_footer',
        'mod_header',
        'mod_launchpad',
        'mod_layout',
        'mod_login',
        'mod_logout',
        'mod_members',
        'mod_menu',
        'mod_pagination',
        'mod_search',
        'mod_toolbar');

##  8. plugins 
INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 8;

##  9. templates 
INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 9
      AND a.title = 'construct'
      AND b.id IN (1, 3);

INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 9
      AND a.title = 'install'
      AND b.id IN (0);

INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 9
      AND a.title = 'molajito'
      AND b.id IN (2);

INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 9
      AND a.title = 'system';

##  site extension instances 
INSERT INTO `molajo_site_extension_instances`
 (`site_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_sites` b;

# Asset Categories

INSERT INTO `molajo_asset_categories`
  (`asset_id`, `category_id`, `ordering`)
  SELECT `id`, `primary_category_id`, 1
    FROM `molajo_assets`
    WHERE `primary_category_id` > 0;

# View Group Permissions
INSERT INTO `molajo_view_group_permissions`
  (`view_group_id`, `asset_id`, `action_id`)
  SELECT DISTINCT `view_group_id`, `id` as asset_id, 3 as `action_id`
    FROM `molajo_assets`;

# Group Permissions (other than view)
# molajo_group_permissions;