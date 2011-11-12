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
    (9, '__content', 'com_media');

#
# SITES
#
INSERT INTO `molajo_sites` (`id`, `name`, `path`, `base_url`, `description`, `parameters`, `custom_fields`)
  VALUES
    (1, 'Molajo', '1', '', 'Primary Site', '{}', '{}');

#
# USERS AND GROUPS
#  5,6 id reserved for administrator

INSERT INTO `molajo_groups`
  (`id`, `title`, `subtitle`, `description`, `type`, `parent_id`, `lft`, `rgt`, `protected`, `ordering` )
    VALUES
      (1, 'Public', '', 'All visitors regardless of authentication status', 0, 0, 0, 1, 1, 1),
      (2, 'Guest', '', 'Visitors not authenticated', 0, 0, 2, 3, 1, 2),
      (3, 'Registered', '', 'Authentication visitors', 0, 0, 4, 5, 1, 3),
      (4, 'Administrator', '', 'System Administrator', 0, 0, 6, 7, 1, 4);

INSERT INTO `molajo_assets`
  (`source_table_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`, `language`, `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT 6, `id`, `title`, '', '', 1, 'en-GB', 0, 0, 1
    FROM  molajo_groups;

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
  (`extension_type_id`, `extension_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `position`, `parent_id`, `lft`, `rgt`, `level`,
    `language`, `translation_of_id`, `ordering`)
  SELECT `extension_type_id`, `id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-01 00:00:00', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-01 00:00:00', 0, '2011-11-01 00:00:00', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 0, 0, 0, 0,
        'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `extension_type_id` = 0;
    
INSERT INTO `molajo_assets`
  (`source_table_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`, `language`, `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT 1, `id`, `title`, '', '', 1, 'en-GB', 0, 0, 1
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 0;
    
# Components

INSERT INTO `molajo_extensions`
  (`name`, `extension_type_id`, `element`, `folder`, `update_site_id`)
  VALUES
    ('com_articles', 1, '', '', 1),
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
  (`extension_type_id`, `extension_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `position`, `parent_id`, `lft`, `rgt`, `level`,
    `language`, `translation_of_id`, `ordering`)
  SELECT `extension_type_id`, `id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-01 00:00:00', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-01 00:00:00', 0, '2011-11-01 00:00:00', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 0, 0, 0, 0,
        'en-GB', 0, `id`
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
  (`extension_type_id`, `extension_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `position`, `parent_id`, `lft`, `rgt`, `level`,
    `language`, `translation_of_id`, `ordering`)
  SELECT `extension_type_id`, `id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-01 00:00:00', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-01 00:00:00', 0, '2011-11-01 00:00:00', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 0, 0, 0, 0,
        'en-GB', 0, `id`
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
  (`extension_type_id`, `extension_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `position`, `parent_id`, `lft`, `rgt`, `level`,
    `language`, `translation_of_id`, `ordering`)
  SELECT `extension_type_id`, `id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-01 00:00:00', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-01 00:00:00', 0, '2011-11-01 00:00:00', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 0, 0, 0, 0,
        'en-GB', 0, `id`
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
  (`extension_type_id`, `extension_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `position`, `parent_id`, `lft`, `rgt`, `level`,
    `language`, `translation_of_id`, `ordering`)
  SELECT `extension_type_id`, `id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-01 00:00:00', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-01 00:00:00', 0, '2011-11-01 00:00:00', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 0, 0, 0, 0,
        'en-GB', 0, `id`
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
  (`extension_type_id`, `extension_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `position`, `parent_id`, `lft`, `rgt`, `level`,
    `language`, `translation_of_id`, `ordering`)
  SELECT `extension_type_id`, `id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-01 00:00:00', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-01 00:00:00', 0, '2011-11-01 00:00:00', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 0, 0, 0, 0,
        'en-GB', 0, `id`
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
  (`extension_type_id`, `extension_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `position`, `parent_id`, `lft`, `rgt`, `level`,
    `language`, `translation_of_id`, `ordering`)
  SELECT `extension_type_id`, `id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-01 00:00:00', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-01 00:00:00', 0, '2011-11-01 00:00:00', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 0, 0, 0, 0,
        'en-GB', 0, `id`
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
  (`extension_type_id`, `extension_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `position`, `parent_id`, `lft`, `rgt`, `level`,
    `language`, `translation_of_id`, `ordering`)
  SELECT `extension_type_id`, `id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-01 00:00:00', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-01 00:00:00', 0, '2011-11-01 00:00:00', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 0, 0, 0, 0,
        'en-GB', 0, `id`
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
  (`extension_type_id`, `extension_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `position`, `parent_id`, `lft`, `rgt`, `level`,
    `language`, `translation_of_id`, `ordering`)
  SELECT `extension_type_id`, `id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-01 00:00:00', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-01 00:00:00', 0, '2011-11-01 00:00:00', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 0, 0, 0, 0,
        'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `extension_type_id` = 5;

INSERT INTO `molajo_assets`
  (`source_table_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`, `language`, `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT 4, `id`, `name`, CONCAT('extensions/menus/', `id`), CONCAT('index.php?option=com_extensions&view=menus&id=', `id`), 1, 'en-GB', 0, 0, 1
    FROM `molajo_extensions`
    WHERE `extension_type_id` = 5;

INSERT INTO `molajo_extension_instance_options`
  (`extension_instance_id`, `extension_type_id`, `option_id`, `option_value`, `option_value_literal`,
  `parent_id`, `lft`, `rgt`, `level`, `ordering`)
  SELECT `id`, `extension_type_id`, 5000, 'xxx',
    FROM `molajo_extensions`
    WHERE `extension_type_id` = 5;

/** Administrator */
INSERT INTO `molajo_users` (`id`, `username`, `first_name`, `last_name`, `content_text`, `email`, `password`, `block`, `activated`, `send_email`, `register_datetimetime`, `last_visit_datetime`, `parameters`, `custom_fields`) VALUES ('42', 'admin',  'Administrator',  '',  '',  'admin@example.com',  'admin',  '0',  '1',  '0',  '2011-11-01 00:00:00',  '0000-00-00 00:00:00', NULL ,  '');
INSERT INTO `molajo_user_applications` (`user_id`, `application_id`) VALUES (42, 1), (42, 2), (42, 3);
INSERT INTO `molajo_user_groups` (`user_id`, `group_id`) VALUES (42, 3), (42, 4);

/** Registered User */
INSERT INTO `molajo_users` (`id`, `username`, `first_name`, `last_name`, `content_text`, `email`, `password`, `block`, `activated`, `send_email`, `register_datetimetime`, `last_visit_datetime`, `parameters`, `custom_fields`) VALUES ('100', 'mark', 'Mark', 'Robinson', '<p>Great guy who sells insurance and coaches Little League.</p>', 'mark.robinson@example.com', 'mark', '0', '1', '0', '2011-11-02 17:45:17', '0000-00-00 00:00:00', NULL, '{"favorite_color":"red","nickname":"Fred","claim_to_fame":"No search results for Mark on Google."}');
INSERT INTO `molajo_user_applications` (`user_id`, `application_id`) VALUES (100, 1), (100, 3);
INSERT INTO `molajo_user_groups` (`user_id`, `group_id`) VALUES (100, 3);

/* User View Group */
INSERT INTO `molajo_user_view_groups`
  (`user_id`, `view_group_id`)
  SELECT DISTINCT a.`user_id`, b.`group_id`
    FROM `molajo_user_groups` a,
      `molajo_group_view_groups` b
    WHERE a.group_id = b.group_id;

/* View Group Permissions */
INSERT INTO `molajo_view_group_permissions`
  (`view_group_id`, `asset_id`, `action_id`)
  SELECT DISTINCT `view_group_id`, `id` as asset_id, 3 as `action_id`
    FROM `molajo_assets`;

/* Group View Permissions */
INSERT INTO `molajo_group_permissions`
  (`group_id`, `asset_id`, `action_id`)
  SELECT DISTINCT `group_id`, `asset_id`, `action_id`
    FROM `molajo_view_group_permissions` a,
      `molajo_group_view_groups` b
    WHERE a.`view_group_id` = b.`view_group_id`;

/**
  SITE APPLICATIONS
 */
INSERT INTO `molajo_site_applications`
  (`site_id`, `application_id`)
  VALUES
    (1, 1),
    (1, 2),
    (1, 3);

INSERT INTO `molajo_site_applications`
  (`site_id`, `application_id`)
  VALUES
    (2, 1);

/** 1. components */
INSERT INTO `molajo_application_extensions`
  (`application_id`, `extension_id`, `extension_instance_id`)
  SELECT b.id, a.extension_id, a.id
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

INSERT INTO `molajo_application_extensions`
  (`application_id`, `extension_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.extension_id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 1
      AND b.id = 2
      AND a.title IN
        ('com_articles',
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

/** 2. language */
INSERT INTO `molajo_application_extensions`
  (`application_id`, `extension_id`, `extension_instance_id`)
  SELECT b.id, a.extension_id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 2;

/** 3. layouts */
INSERT INTO `molajo_application_extensions`
  (`application_id`, `extension_id`, `extension_instance_id`)
  SELECT b.id, a.extension_id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 3;

/** 5. menus */
INSERT INTO `molajo_application_extensions`
  (`application_id`, `extension_id`, `extension_instance_id`)
  SELECT b.id, a.extension_id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 5
      AND a.extension_id = 1060
      AND b.id IN (1, 3);

INSERT INTO `molajo_application_extensions`
  (`application_id`, `extension_id`, `extension_instance_id`)
  SELECT b.id, a.extension_id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 5
      AND NOT (a.extension_id = 1060)
      AND b.id = 2;

/** 6. modules */
INSERT INTO `molajo_application_extensions`
  (`application_id`, `extension_id`, `extension_instance_id`)
  SELECT b.id, a.extension_id, a.id
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

INSERT INTO `molajo_application_extensions`
  (`application_id`, `extension_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.extension_id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 6
      AND NOT (a.extension_id = 1060)
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

/** 8. plugins */
INSERT INTO `molajo_application_extensions`
  (`application_id`, `extension_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.extension_id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 8;

/** 9. templates */
INSERT INTO `molajo_application_extensions`
  (`application_id`, `extension_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.extension_id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 9
      AND a.title = 'construct'
      AND b.id IN (1, 3);

INSERT INTO `molajo_application_extensions`
  (`application_id`, `extension_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.extension_id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 9
      AND a.title = 'install'
      AND b.id IN (0);

INSERT INTO `molajo_application_extensions`
  (`application_id`, `extension_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.extension_id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 9
      AND a.title = 'molajito'
      AND b.id IN (2);

INSERT INTO `molajo_application_extensions`
  (`application_id`, `extension_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.extension_id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 9
      AND a.title = 'system';
