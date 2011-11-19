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
# Content Types
#
INSERT INTO `molajo_content_types` (`id`, `content_type_id`, `protected`, `source_table`, `component_option`)
  VALUES
  (10, 'System', 1, '', ''),
  (15, 'Applications', 1, '__applications', 'com_applications'),
  (20, 'Dashboard', 1, '__dummy', 'com_dashboard'),
  (25, 'Maintain', 1, '__dummy', 'com_maintain'),
  (30, 'Installer', 1, '__dummy', 'com_installer'),
  (35, 'Search', 1, '__dummy', 'com_search'),
  (40, 'Language', 1, '__dummy', 'com_language'),

  (100, 'Group System', 1, '__groups', 'com_groups'),
  (110, 'Group Normal', 1, '__groups', 'com_groups'),
  (120, 'Group User', 1, '__groups', 'com_groups'),

  (500, 'Users', 1, '__users', 'com_users'),

  (1000, 'Extension Core', 1, '__extension_instances', 'com_extensions'),
  (1050, 'Extension Component', 1, '__extension_instances', 'com_extensions'),
  (1100, 'Extension Language', 1, '__extension_instances', 'com_extensions'),
  (1150, 'Extension Layout', 1, '__extension_instances', 'com_extensions'),
  (1200, 'Extension Library', 1, '__extension_instances', 'com_extensions'),
  (1250, 'Extension Manifest', 1, '__extension_instances', 'com_extensions'),
  (1300, 'Extension Menu', 1, '__extension_instances', 'com_extensions'),
  (1350, 'Extension Module', 1, '__extension_instances', 'com_extensions'),
  (1400, 'Extension Parameter', 1, '__extension_instances', 'com_extensions'),
  (1450, 'Extension Plugin', 1, '__extension_instances', 'com_extensions'),
  (1500, 'Extension Template', 1, '__extension_instances', 'com_extensions'),

  (2000, 'Menu Item Component', 1, '__menu_items', 'com_menus'),
  (2100, 'Menu Item Link', 1, '__menu_items', 'com_menus'),
  (2200, 'Menu Item Module', 1, '__menu_items', 'com_menus'),
  (2300, 'Menu Item Separator', 1, '__menu_items', 'com_menus'),

  (3000, 'Category System', 1, '__categories', 'com_categories'),
  (3250, 'Category Content', 0, '__categories', 'com_categories'),
  (3500, 'Category Tags', 0, '__categories', 'com_categories'),

  (10000, 'Content Articles', 0, '__content', 'com_articles'),
  (20000, 'Content Contacts', 0, '__content', 'com_contacts'),
  (30000, 'Content Comments', 0, '__content', 'com_comments'),
  (40000, 'Content Media', 0, '__content', 'com_media'),
  (50000, 'Content Layouts', 0, '__content', 'com_layouts');

#
# SITES
#
INSERT INTO `molajo_sites` (`id`, `name`, `path`, `base_url`, `description`, `parameters`, `custom_fields`)
  VALUES
    (1, 'Molajo', '1', '', 'Primary Site', '{}', '{}');

#
# APPLICATIONS
# Note: after menuitems are defined, update applications for home
#
INSERT INTO `molajo_applications` (`id`, `name`, `path`, `home`, `description`, `parameters`, `custom_fields`)
  VALUES
    (1, 'site', '', 0, 'Primary application for site visitors', '{}', '{}'),
    (2, 'administrator', 'administrator', 0, 'Administrative site area for site construction', '{}', '{}'),
    (3, 'content', 'content', 0, 'Area for content development', '{}', '{}');

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
  (`name`, `content_type_id`,  `element`, `folder`, `update_site_id`)
  VALUES
    ('Core', 10, '', '', 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `content_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `content_type_id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `content_type_id` = 10;

# Components
INSERT INTO `molajo_extensions`
  (`name`, `content_type_id`,  `element`, `folder`, `update_site_id`)
  VALUES
    ('com_articles', 1050, '', '', 1),
    ('com_assets', 1050, '', '', 1),
    ('com_categories', 1050, '', '', 1),
    ('com_comments', 1050, '', '', 1),
    ('com_configuration', 1050, '', '', 1),
    ('com_contacts', 1050, '', '', 1),
    ('com_dashboard', 1050, '', '', 1),
    ('com_extensions', 1050, '', '', 1),
    ('com_groups', 1050, '', '', 1),
    ('com_installer', 1050, '', '', 1),
    ('com_layouts', 1050, '', '', 1),
    ('com_login', 1050, '', '', 1),
    ('com_maintain', 1050, '', '', 1),
    ('com_menus', 1050, '', '', 1),
    ('com_media', 1050, '', '', 1),
    ('com_profile', 1050, '', '', 1),
    ('com_search', 1050, '', '', 1),
    ('com_users', 1050, '', '', 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `content_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `content_type_id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `content_type_id` = 1050;

#
# System Category
#
INSERT INTO `molajo_categories`
  (`id`, `title`, `subtitle`, `alias`, `content_text`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
  `extension_instance_id`, `content_type_id`,
  `version`, `parent_id`, `lft`, `rgt`, `level`, `language`, `ordering`)
  VALUES
    (1, 'ROOT', '', 'root', '<p>Root category</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00',
    4, 10,
    0, 0, 0, 0, 0, 'en-GB', 1);

UPDATE `molajo_categories`
  SET id = 0
  WHERE `title` = 'ROOT';

# Do not add root category to assets

INSERT INTO `molajo_categories`
  (`id`, `title`, `subtitle`, `alias`, `content_text`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
  `extension_instance_id`, `content_type_id`,
  `version`, `parent_id`, `lft`, `rgt`, `level`, `language`, `ordering`)
  VALUES
    (1, 'System', '', 'system', '<p>System category</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00',
    4, 3000,
    0, 0, 0, 0, 0, 'en-GB', 1);

#
# Now that the system category is defined, add in assets

# Application
INSERT INTO `molajo_assets`
 (`content_type_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`, `template_id`, `language`, `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT 15, `id`, `name`, `path`, '', 1, 0, 'en-GB', 0, 0, 1
    FROM  molajo_applications;

# Extension - Core
INSERT INTO `molajo_assets`
  (`content_type_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`,  `template_id`, `language`,  `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT 1000, `id`, `title`, 'categories/1', 'index.php?option=com_categories&id=1', 1, 0, 'en-GB', 0, 0, 1
    FROM  molajo_groups;

# Extension - Components
INSERT INTO `molajo_assets`
  (`content_type_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`,  `template_id`, `language`,  `translation_of_id`, `redirect_to_id`, `view_group_id`)
SELECT 1050, `id`, `title`, 'extensions/components/1', 'index.php?option=com_extensions&view=component&id=1', 1, 0, 'en-GB', 0, 0, 1
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1050;

# Languages

INSERT INTO `molajo_extensions`
  (`name`, `content_type_id`,  `element`, `folder`, `update_site_id`)
  VALUES
    ('English (UK)', 40, 'en-UK', '', 1),
    ('English (US)', 40, 'en-US', '', 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `content_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `content_type_id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `content_type_id` = 40;

INSERT INTO `molajo_assets`
  (`content_type_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`,  `template_id`, `language`,  `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT `content_type_id`, `id`, `title`, CONCAT('extensions/languages/', `id`), CONCAT('index.php?option=com_extensions&view=languages&id=', `id`), 1, 0, 'en-GB', 0, 0, 1
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 40;

# Layouts

INSERT INTO `molajo_extensions`
  (`name`, `content_type_id`,  `element`, `folder`, `update_site_id`)
  VALUES
    ('head', 1150, '', 'document', 1),
    ('messages', 1150, '', 'document', 1),
    ('errors', 1150, '', 'document', 1),
    ('atom', 1150, '', 'document', 1),
    ('rss', 1150, '', 'document', 1),

    ('admin_acl_panel', 1150, '', 'extension', 1),
    ('admin_activity', 1150, '', 'extension', 1),
    ('admin_dashboard', 1150, '', 'extension', 1),
    ('admin_edit', 1150, '', 'extension', 1),
    ('admin_favorites', 1150, '', 'extension', 1),
    ('admin_feed', 1150, '', 'extension', 1),
    ('admin_footer', 1150, '', 'extension', 1),
    ('admin_header', 1150, '', 'extension', 1),
    ('admin_inbox', 1150, '', 'extension', 1),
    ('admin_launchpad', 1150, '', 'extension', 1),
    ('admin_list', 1150, '', 'extension', 1),
    ('admin_login', 1150, '', 'extension', 1),
    ('admin_modal', 1150, '', 'extension', 1),
    ('admin_pagination', 1150, '', 'extension', 1),
    ('admin_toolbar', 1150, '', 'extension', 1),
    ('audio', 1150, '', 'extension', 1),
    ('contact_form', 1150, '', 'extension', 1),
    ('default', 1150, '', 'extension', 1),
    ('dummy', 1150, '', 'extension', 1),
    ('faq', 1150, '', 'extension', 1),
    ('item', 1150, '', 'extension', 1),
    ('list', 1150, '', 'extension', 1),
    ('items', 1150, '', 'extension', 1),
    ('list', 1150, '', 'extension', 1),
    ('pagination', 1150, '', 'extension', 1),
    ('social_bookmarks', 1150, '', 'extension', 1),
    ('syntaxhighlighter', 1150, '', 'extension', 1),
    ('table', 1150, '', 'extension', 1),
    ('tree', 1150, '', 'extension', 1),
    ('twig_example', 1150, '', 'extension', 1),
    ('video', 1150, '', 'extension', 1),

    ('button', 1150, '', 'formfields', 1),
    ('colorpicker', 1150, '', 'formfields', 1),
    ('datepicker', 1150, '', 'formfields', 1),
    ('list', 1150, '', 'formfields', 1),
    ('media', 1150, '', 'formfields', 1),
    ('number', 1150, '', 'formfields', 1),
    ('option', 1150, '', 'formfields', 1),
    ('rules', 1150, '', 'formfields', 1),
    ('spacer', 1150, '', 'formfields', 1),
    ('text', 1150, '', 'formfields', 1),
    ('textarea', 1150, '', 'formfields', 1),
    ('user', 1150, '', 'formfields', 1),

    ('article', 1150, '', 'wrap', 1),
    ('aside', 1150, '', 'wrap', 1),
    ('div', 1150, '', 'wrap', 1),
    ('footer', 1150, '', 'wrap', 1),
    ('header', 1150, '', 'wrap', 1),
    ('horizontal', 1150, '', 'wrap', 1),
    ('nav', 1150, '', 'wrap', 1),
    ('none', 1150, '', 'wrap', 1),
    ('outline', 1150, '', 'wrap', 1),
    ('section', 1150, '', 'wrap', 1),
    ('table', 1150, '', 'wrap', 1),
    ('tabs', 1150, '', 'wrap', 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `content_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `content_type_id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `content_type_id` = 1150;

INSERT INTO `molajo_assets`
  (`content_type_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`,  `template_id`, `language`,  `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT `content_type_id`, `id`, `title`, CONCAT('extensions/layouts/', `id`), CONCAT('index.php?option=com_extensions&view=layouts&id=', `id`), 1, 0, 'en-GB', 0, 0, 1
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1150;

# Libraries
INSERT INTO `molajo_extensions`
  (`name`, `content_type_id`,  `element`, `folder`, `update_site_id`)
  VALUES
    ('Doctrine', 1200, '', '', 1),
    ('includes', 1200, '', '', 1),
    ('jplatform', 1200, '', '', 1),
    ('molajo', 1200, '', '', 1),
    ('Twig', 1200, '', '', 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `content_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `content_type_id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `content_type_id` = 1200;

INSERT INTO `molajo_assets`
  (`content_type_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`,  `template_id`, `language`,  `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT `content_type_id`, `id`, `title`, CONCAT('extensions/libraries/', `id`), CONCAT('index.php?option=com_extensions&view=libraries&id=', `id`), 1, 0, 'en-GB', 0, 0, 1
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1200;

# Plugins
INSERT INTO `molajo_extensions`
  (`name`, `content_type_id`,  `element`, `folder`, `update_site_id`)
  VALUES
    ('example', 1450, '', 'acl', 1),

    ('molajo', 1450, '', 'authentication', 1),

    ('broadcast', 1450, '', 'content', 1),
    ('content', 1450, '', 'content', 1),
    ('emailcloak', 1450, '', 'content', 1),
    ('links', 1450, '', 'content', 1),
    ('loadmodule', 1450, '', 'content', 1),
    ('media', 1450, '', 'content', 1),
    ('protect', 1450, '', 'content', 1),
    ('responses', 1450, '', 'content', 1),

    ('aloha', 1450, '', 'editors', 1),
    ('none', 1450, '', 'editors', 1),

    ('article', 1450, '', 'editor-buttons', 1),
    ('editor', 1450, '', 'editor-buttons', 1),
    ('image', 1450, '', 'editor-buttons', 1),
    ('pagebreak', 1450, '', 'editor-buttons', 1),
    ('readmore', 1450, '', 'editor-buttons', 1),

    ('molajo', 1450, '', 'extension', 1),

    ('extend', 1450, '', 'molajo', 1),
    ('minifier', 1450, '', 'molajo', 1),
    ('search', 1450, '', 'molajo', 1),
    ('tags', 1450, '', 'molajo', 1),
    ('urls', 1450, '', 'molajo', 1),

    ('molajosample', 1450, '', 'query', 1),

    ('categories', 1450, '', 'search', 1),
    ('articles', 1450, '', 'search', 1),

    ('cache', 1450, '', 'system', 1),
    ('compress', 1450, '', 'system', 1),
    ('create', 1450, '', 'system', 1),
    ('debug', 1450, '', 'system', 1),
    ('languagefilter', 1450, '', 'system', 1),
    ('log', 1450, '', 'system', 1),
    ('logout', 1450, '', 'system', 1),
    ('molajo', 1450, '', 'system', 1),
    ('p3p', 1450, '', 'system', 1),
    ('parameters', 1450, '', 'system', 1),
    ('redirect', 1450, '', 'system', 1),
    ('remember', 1450, '', 'system', 1),
    ('system', 1450, '', 'system', 1),
    ('webservices', 1450, '', 'system', 1),

    ('molajo', 1450, '', 'user', 1),
    ('profile', 1450, '', 'user', 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `content_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `content_type_id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `content_type_id` = 1450;

INSERT INTO `molajo_assets`
  (`content_type_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`,  `template_id`, `language`,  `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT `content_type_id`, `id`, `title`, CONCAT('extensions/plugins/', `id`), CONCAT('index.php?option=com_extensions&view=plugins&id=', `id`), 1, 0, 'en-GB', 0, 0, 1
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1450;

## Template

INSERT INTO `molajo_extensions`
  (`name`, `content_type_id`,  `element`, `folder`, `update_site_id`)
  VALUES
    ('construct', 1500, '', '', 1),
    ('install', 1500, '', '', 1),
    ('molajito', 1500, '', '', 1),
    ('system', 1500, '', '', 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `content_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `content_type_id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `content_type_id` = 1500;

INSERT INTO `molajo_assets`
  (`content_type_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`,  `template_id`, `language`,  `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT `content_type_id`, `id`, `title`, CONCAT('extensions/templates/', `id`), CONCAT('index.php?option=com_extensions&view=templates&id=', `id`), 1, 0, 'en-GB', 0, 0, 1
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1500;

## Menu

INSERT INTO `molajo_extensions`
  (`name`, `content_type_id`,  `element`, `folder`, `update_site_id`)
  VALUES
    ('Administrator Menu', 1300, '', '', 1),
    ('Main Menu', 1300, '', '', 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `content_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`,
    `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `content_type_id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `content_type_id` = 1300;

INSERT INTO `molajo_assets`
  (`content_type_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`,  `template_id`, `language`,  `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT `content_type_id`, `id`, `title`, CONCAT('extensions/menus/', `id`), CONCAT('index.php?option=com_menus&view=menus&id=', `id`), 1, 0, 'en-GB', 0, 0, 1
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300;

## ## Menu Items

## ## ## Admin: Root

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 1, 1, 'Root', '', 0, 0, 65, 0,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

## ## ## Admin: Content

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 2, 2, 'Create', 'create', 1, 1, 12, 1,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 3, 3, 'Articles', 'articles', 2, 2, 3, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 4, 4, 'Contacts', 'contacts', 2, 4, 5, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 5, 5, 'Comments', 'comments', 2, 6, 7, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 6, 6, 'Layouts', 'layouts', 2, 8, 9, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 7, 7, 'Media', 'media', 2, 10, 11, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

## ## ## Admin: Access

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 8, 8, 'Access', 'access', 1, 13, 22, 1,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 9, 9, 'Profile', 'profile', 8, 14, 15, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 10, 10, 'Users', 'users', 8, 16, 17, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 11, 11, 'Groups', 'groups', 8, 18, 19, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 12, 12, 'Assets', 'assets', 8, 20, 21, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

## ## ## Admin: Build

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 13, 13, 'Build', 'build', 1, 23, 34, 1,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 14, 14, 'Categories', 'categories', 13, 24, 25, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 15, 15, 'Menus', 'menus', 13, 26, 27, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 16, 16, 'Menu Items', 'menuitems', 13, 28, 29, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 17, 17, 'Modules', 'modules', 13, 30, 31, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 18, 18, 'Templates', 'templates', 13, 32, 33, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

## ## ## Admin: Configure

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 19, 19, 'Configure', 'configure', 1, 35, 48, 1,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 20, 20, 'Site', 'sites', 19, 36, 37, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 21, 21, 'Applications', 'applications', 19, 38, 39, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 22, 22, 'Checkin', 'checkin', 19, 40, 41, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 23, 23, 'Clean Cache', 'cleancache', 19, 42, 43, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 24, 24, 'Redirects', 'redirects', 19, 44, 45, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 25, 25, 'Plugins', 'plugins', 19, 46, 47, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

## ## ## Admin: Extend

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 26, 26, 'Extend', 'extend', 1, 49, 56, 1,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 27, 27, 'Install', 'install', 26, 50, 51, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 28, 28, 'Update', 'update', 26, 52, 53, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 29, 29, 'Uninstall', 'uninstall', 26, 54, 55, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

## ## ## Admin: Search
INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 30, 30, 'Search', 'search', 1, 57, 58, 1,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

## ## ## Site: Main Menu

INSERT INTO `molajo_menu_items`
  (`id`, `ordering`, `title`, `alias`, `parent_id`, `lft`, `rgt`, `level`,
    `extension_instance_id`, `content_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `custom_fields`, `language`, `translation_of_id`)
  SELECT 31, 31, 'Home', 'home', 1, 59, 60, 1,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_title":"","page_id":"","page_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","layout":"","wrap":"div","layout_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1300 AND `title` = 'Administrator Menu';

# Menu Item Home
UPDATE `molajo_menu_items`
   SET `protected` = 1,
        `home` = 0;
UPDATE `molajo_menu_items`
  SET `home` = 1
  WHERE `id` = 2;
UPDATE `molajo_menu_items`
  SET `home` = 1
  WHERE `id` = 31;

# Menu Item Metadata
UPDATE `molajo_menu_items`
  SET `metadata` = '{"metadata_description":"","metadata_keywords":"","metadata_robots":"","metadata_author":"","metadata_rights":""}';

# Application Home
UPDATE `molajo_applications`
  SET `home` = 31
  WHERE `id` IN (1, 3);
UPDATE `molajo_applications`
  SET `home` = 2
  WHERE `id` = 2;

INSERT INTO `molajo_assets` (`content_type_id`, `source_id`, `title`, `request`, `sef_request`, `primary_category_id`, `language`,  `translation_of_id`, `redirect_to_id`, `view_group_id`)
  VALUES
  (2000, 2, 'Create', 'index.php?option=com_dashboard&view=create', 'create', 1, 'en-GB', 0, 0, 3),
  (2000, 3, 'Articles', 'index.php?option=com_articles', 'create/articles', 1, 'en-GB', 0, 0, 3),
  (2000, 4, 'Contacts', 'index.php?option=com_contacts', 'create/contacts', 1, 'en-GB', 0, 0, 3),
  (2000, 5, 'Comments', 'index.php?option=com_comments', 'create/comments', 1, 'en-GB', 0, 0, 3),
  (2000, 6, 'Layouts', 'index.php?option=com_layouts', 'create/layouts', 1, 'en-GB', 0, 0, 3),
  (2000, 7, 'Media', 'index.php?option=com_media', 'create/media', 1, 'en-GB', 0, 0, 3),

  (2000, 8, 'Access', 'index.php?option=com_dashboard&view=users', 'access', 1, 'en-GB', 0, 0, 3),
  (2000, 9, 'Profile', 'index.php?option=com_profile', 'access/profiles', 1, 'en-GB', 0, 0, 3),
  (2000, 10, 'Users', 'index.php?option=com_users', 'access/users', 1, 'en-GB', 0, 0, 3),
  (2000, 11, 'Groups', 'index.php?option=com_groups', 'access/groups', 1, 'en-GB', 0, 0, 3),
  (2000, 12, 'Assets', 'index.php?option=com_assets&view=users', 'access/assets', 1, 'en-GB', 0, 0, 3),

  (2000, 13, 'Build', 'index.php?option=com_dashboard&view=build', 'build', 1, 'en-GB', 0, 0, 3),
  (2000, 14, 'Categories', 'index.php?option=com_categories', 'build/categories', 1, 'en-GB', 0, 0, 3),
  (2000, 15, 'Menus', 'index.php?option=com_extensions&view=menus', 'build/menus', 1, 'en-GB', 0, 0, 3),
  (2000, 16, 'Menu Items', 'index.php?option=com_extensions&view=menuitems', 'build/menuitems', 1, 'en-GB', 0, 0, 3),
  (2000, 17, 'Modules', 'index.php?option=com_extensions&view=modules', 'build/modules', 1, 'en-GB', 0, 0, 3),
  (2000, 18, 'Templates', 'index.php?option=com_extensions&view=templates', 'build/templates', 1, 'en-GB', 0, 0, 3),

  (2000, 19, 'Configure', 'index.php?option=com_dashboard&view=options', 'configure', 1, 'en-GB', 0, 0, 3),
  (2000, 20, 'Site', 'index.php?option=com_extensions&view=sites', 'configure/sites', 1, 'en-GB', 0, 0, 3),
  (2000, 21, 'Applications', 'index.php?option=com_extensions&view=applications', 'configure/applications', 1, 'en-GB', 0, 0, 3),
  (2000, 22, 'Checkin', 'index.php?option=com_maintain&view=checkin', 'configure/checkin', 1, 'en-GB', 0, 0, 3),
  (2000, 23, 'Clean Cache', 'index.php?option=com_maintain&view=cleancache', 'configure/cleancache', 1, 'en-GB', 0, 0, 3),
  (2000, 24, 'Redirects', 'index.php?option=com_maintain&view=redirects', 'configure/redirects', 1, 'en-GB', 0, 0, 3),
  (2000, 25, 'Plugins', 'index.php?option=com_extensions&view=plugins', 'configure/plugins', 1, 'en-GB', 0, 0, 3),

  (2000, 26, 'Extend', 'index.php?option=com_dashboard&view=install', 'extend', 1, 'en-GB', 0, 0, 3),
  (2000, 27, 'Create', 'index.php?option=com_installer&view=create', 'extend/install', 1, 'en-GB', 0, 0, 3),
  (2000, 28, 'Update', 'index.php?option=com_installer&view=update', 'install/update', 1, 'en-GB', 0, 0, 3),
  (2000, 29, 'Uninstall', 'index.php?option=com_installer&view=uninstall', 'install/uninstall', 1, 'en-GB', 0, 0, 3),

  (2000, 30, 'Search', 'index.php?option=com_search', 'search', 1, 'en-GB', 0, 0, 3),

  (2000, 31, 'Home', 'index.php?option=com_layouts', 'home', 1, 'en-GB', 0, 0, 1);

# Modules
INSERT INTO `molajo_extensions`
  (`name`, `content_type_id`,  `element`, `folder`, `update_site_id`)
  VALUES
    ('mod_assetwidget', 1350, '', '', 1),
    ('mod_aclwidget', 1350, '', '', 1),
    ('mod_breadcrumbs', 1350, '', '', 1),
    ('mod_debug', 1350, '', '', 1),
    ('mod_categorywidget', 1350, '', '', 1),
    ('mod_content', 1350, '', '', 1),
    ('mod_custom', 1350, '', '', 1),
    ('mod_groupwidget', 1350, '', '', 1),
    ('mod_feed', 1350, '', '', 1),
    ('mod_filters', 1350, '', '', 1),
    ('mod_filebrowser', 1350, '', '', 1),
    ('mod_footer', 1350, '', '', 1),
    ('mod_gallery', 1350, '', '', 1),
    ('mod_grid', 1350, '', '', 1),
    ('mod_gridbatch', 1350, '', '', 1),
    ('mod_header', 1350, '', '', 1),
    ('mod_iconbutton', 1350, '', '', 1),
    ('mod_layout', 1350, '', '', 1),
    ('mod_login', 1350, '', '', 1),
    ('mod_logout', 1350, '', '', 1),
    ('mod_members', 1350, '', '', 1),
    ('mod_menu', 1350, '', '', 1),
    ('mod_pagination', 1350, '', '', 1),
    ('mod_plugins', 1350, '', '', 1),
    ('mod_quicklinks', 1350, '', '', 1),
    ('mod_search', 1350, '', '', 1),
    ('mod_submenu', 1350, '', '', 1),
    ('mod_syndicate', 1350, '', '', 1),
    ('mod_textbox', 1350, '', '', 1),
    ('mod_title', 1350, '', '', 1),
    ('mod_toolbar', 1350, '', '', 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `content_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`,
    `position`, `custom_fields`, `parameters`,
    `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `content_type_id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0,
        SUBSTRING(`name`, 5, 99), '{}', '{}',
        'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `content_type_id` = 1350
      AND NOT (`name` = 'mod_menu');

# Administrator Menu Module
SET @menu_id = (SELECT `id` FROM `molajo_extension_instances` WHERE `title` = 'Administrator Menu' AND `content_type_id` = 1300);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `content_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`,
    `position`, `custom_fields`, `parameters`,
    `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `content_type_id`,
        'Administrator Menu Module', '', '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0,
        SUBSTRING(`name`, 5, 99), '{}',
        CONCAT('{"menu_id":"', @menu_id, '","wrap":"none","layout":"admin_launchpad","start_level":"0","end_level":"0","show_all_children":"0","max_depth":"0","tag_id":"","class_suffix":"","window_open":"","layout":"","moduleclass_suffix":"_menu","cache":"1","cache_time":"900","cachemode":"itemid"}'),
        'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `content_type_id` = 1350
     AND `name` = 'mod_menu';
/*
{"menu_id":"209","wrap":"none","layout":"admin_launchpad","start_level":"0","end_level":"0","show_all_children":"0","max_depth":"0","tag_id":"","class_suffix":"","window_open":"","layout":"","moduleclass_suffix":"_menu","cache":"1","cache_time":"900","cachemode":"itemid"}
 */
# Main Menu Module
SET @menu_id = (SELECT `id` FROM `molajo_extension_instances` WHERE `title` = 'Main Menu' AND `content_type_id` = 1300);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `content_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`,
    `position`, `custom_fields`, `parameters`,
    `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `content_type_id`,
        'Main Menu Module', '', '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0,
        SUBSTRING(`name`, 5, 99), '{}',
        CONCAT('{"wrap":"none","layout":"list","menu_id":', @menu_id, ',"start_level":"","end_level":"","show_all_children":"","max_depth":""}'),
        'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `content_type_id` = 1350
     AND `name` = 'mod_menu';

## Module Assets
INSERT INTO `molajo_assets`
  (`content_type_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`,  `template_id`, `language`,  `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT `content_type_id`, `id`, `title`, CONCAT('extensions/modules/', `id`), CONCAT('index.php?option=com_extensions&view=modules&id=', `id`), 1, 0, 'en-GB', 0, 0, 1
    FROM `molajo_extension_instances`
    WHERE `content_type_id` = 1350;

## Module Asset Modules
/*INSERT INTO `molajo_asset_modules`
(`asset_id`, `extension_instance_id`, `position`)
 SELECT `id`, `source_id`, SUBSTRING(`title`, 5, 99)
    FROM `molajo_assets`
    WHERE `content_type_id` = 1350;
*/
#
# USERS AND GROUPS
#
INSERT INTO `molajo_groups`
  (`id`, `extension_instance_id`, `title`, `alias`, `content_text`, `content_type_id`,
   `parent_id`, `lft`, `rgt`, `level`, `ordering`,
   `protected`, `featured`, `stickied`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`, `language`, `translation_of_id`)
   VALUES
      (1, 10, 'Public', '', 'All visitors regardless of authentication status', 100, 0, 1, 2, 1, 1, 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '{}', '{}', 'en-GB', 0),
      (2, 10, 'Guest', '', 'Visitors not authenticated',                        100, 0, 3, 4, 1, 2, 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '{}', '{}', 'en-GB', 0),
      (3, 10, 'Registered', '', 'Authentication visitors',                      100, 0, 5, 6, 1, 3, 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '{}', '{}', 'en-GB', 0),
      (4, 10, 'Administrator', '', 'System Administrator',                      100, 0, 7, 8, 1, 4, 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '{}', '{}', 'en-GB', 0);

INSERT INTO `molajo_assets`
  (`content_type_id`, `source_id`, `title`, `sef_request`, `request`, `primary_category_id`,  `template_id`, `language`,  `translation_of_id`, `redirect_to_id`, `view_group_id`)
  SELECT `content_type_id`, `id`, `title`, CONCAT('groups/', `id`), CONCAT('index.php?option=com_groups&id=', `id`), 1, 0, 'en-GB', 0, 0, 1
    FROM  molajo_groups
    ORDER BY `id`;

INSERT INTO `molajo_view_groups`
  (`id`, `view_group_name_list`, `view_group_id_list`, `content_type_id`  )
    VALUES
      (1, 'Public', '1', 100),
      (2, 'Guest', '2', 100),
      (3, 'Registered', '3', 100),
      (4, 'Administrator', '4', 100),
      (5, 'Registered, Administrator', '4,5', 100);

INSERT INTO `molajo_group_view_groups`
  ( `group_id` ,`view_group_id` )
  VALUES
    (1, 1),
    (2, 2),
    (3, 3),
    (4, 4),
    (3, 5),
    (4, 5);

##  Administrator 
INSERT INTO `molajo_users` (`id`, `username`, `first_name`, `last_name`, `content_text`, `email`, `password`, `block`, `activation`, `send_email`, `register_datetime`, `last_visit_datetime`, `parameters`, `custom_fields`) VALUES ('42', 'admin',  'Administrator',  '',  '',  'admin@example.com',  'admin',  '0',  '1',  '0',  '2011-11-11 11:11:11',  '0000-00-00 00:00:00', '{}', '{}');
INSERT INTO `molajo_user_applications` (`user_id`, `application_id`) VALUES (42, 1), (42, 2), (42, 3);
INSERT INTO `molajo_groups`
  (`extension_instance_id`, `title`, `alias`, `content_text`, `content_type_id`,
   `parent_id`, `lft`, `rgt`, `level`, `ordering`,
   `protected`, `featured`, `stickied`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 10, CONCAT(`first_name`, ' ', `last_name`), '', '', 120, `id`, 0, 0, 0, 1, 0, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '{}', '{}', 'en-GB', 0
    FROM `molajo_users` WHERE username = 'admin';
INSERT INTO `molajo_user_groups` (`user_id`, `group_id`) VALUES (42, 3), (42, 4);

##  Sample Registered User 
INSERT INTO `molajo_users` (`id`, `username`, `first_name`, `last_name`, `content_text`, `email`, `password`, `block`, `activation`, `send_email`, `register_datetime`, `last_visit_datetime`, `parameters`, `custom_fields`) VALUES ('100', 'mark', 'Mark', 'Robinson', '<p>Great guy who sells insurance and coaches Little League.</p>', 'mark.robinson@example.com', 'mark', '0', '1', '0', '2011-11-02 17:45:17', '0000-00-00 00:00:00', '{}', '{"favorite_color":"red","nickname":"Fred","claim_to_fame":"No search results for Mark on Google."}');
INSERT INTO `molajo_user_applications` (`user_id`, `application_id`) VALUES (100, 1), (100, 3);
INSERT INTO `molajo_groups`
  (`extension_instance_id`, `title`, `alias`, `content_text`, `content_type_id`,
   `parent_id`, `lft`, `rgt`, `level`, `ordering`,
   `protected`, `featured`, `stickied`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `custom_fields`, `parameters`, `language`, `translation_of_id`)
  SELECT 10, CONCAT(`first_name`, ' ', `last_name`), '', '', 120, `id`, 0, 0, 0, 1, 0, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '{}', '{}', 'en-GB', 0
    FROM `molajo_users` WHERE username = 'mark';
INSERT INTO `molajo_user_groups` (`user_id`, `group_id`) VALUES (100, 3);

##  Authorize Users for their own group 
INSERT INTO `molajo_user_groups` (`user_id`, `group_id`) SELECT parent_id, id FROM `molajo_groups` WHERE content_type_id = 120;

##  user private view groups 
INSERT INTO `molajo_view_groups`
  (`view_group_name_list`, `view_group_id_list`, `content_type_id` )
  SELECT 'Private', `id`, `content_type_id`
    FROM `molajo_groups`
   WHERE `content_type_id` = 120;

##  user private view group permission 
INSERT INTO `molajo_group_view_groups` ( `group_id` , `view_group_id` )
  SELECT a.`id`, b.`id`
  FROM `molajo_groups` a,
    `molajo_view_groups` b
  WHERE a.`content_type_id` = 120
    AND b.`content_type_id` = 120
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
    WHERE b.id IN (1, 3)
      AND a.content_type_id = 1050
       AND a.title IN
        ('com_articles',
          'com_comments',
          'com_contacts',
          'com_layouts',
          'com_login',
          'com_media',
          'com_menus',
          'com_search');

INSERT INTO `molajo_application_extension_instances`
  (`application_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE b.id = 2
      AND a.content_type_id = 1050
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
          'com_menus',
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
    WHERE content_type_id = 1100;

##  3. layouts 
INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE content_type_id = 1150;

##  5. menus 
INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE content_type_id = 1300
      AND NOT(a.title = 'Admin')
      AND b.id IN (1, 3);

INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE content_type_id = 1300
      AND a.title = 'Admin'
      AND b.id = 2;

##  6. modules 
INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE content_type_id = 1350
      AND b.id IN (1, 3)
      AND a.title IN
          ('mod_breadcrumbs',
          'mod_content',
          'mod_custom',
          'mod_feed',
          'mod_footer',
          'mod_header',
          'mod_layout',
          'Main Menu',
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
    WHERE content_type_id = 1350
      AND b.id = 2
      AND a.title IN
        ('mod_content',
        'mod_custom',
        'mod_debug',
        'mod_feed',
        'mod_footer',
        'mod_header',
        'mod_layout',
        'mod_login',
        'mod_logout',
        'mod_members',
        'Administrator Menu',
        'mod_pagination',
        'mod_search',
        'mod_toolbar');

##  8. plugins 
INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE content_type_id = 1450;

##  9. templates 
INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE content_type_id = 1500
      AND a.title = 'construct'
      AND b.id IN (1, 3);

INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE content_type_id = 1500
      AND a.title = 'install'
      AND b.id IN (0);

INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE content_type_id = 1500
      AND a.title = 'molajito'
      AND b.id IN (2);

INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE content_type_id = 1500
      AND a.title = 'system';

##  site extension instances 
INSERT INTO `molajo_site_extension_instances`
 (`site_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_sites` b;

# Asset Categories

INSERT INTO `molajo_asset_categories`
  (`asset_id`, `category_id`)
  SELECT `id`, `primary_category_id` 
    FROM `molajo_assets`
    WHERE `primary_category_id` > 0;

# View Group Permissions
INSERT INTO `molajo_view_group_permissions`
  (`view_group_id`, `asset_id`, `action_id`)
  SELECT DISTINCT `view_group_id`, `id` as asset_id, 3 as `action_id`
    FROM `molajo_assets`;

# Group Permissions (other than view)
# molajo_group_permissions;