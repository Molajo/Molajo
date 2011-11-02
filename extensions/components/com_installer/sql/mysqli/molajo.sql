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
    (1, 'components'),
    (2, 'languages'),
    (3, 'layouts'),
    (4, 'manifests'),
    (5, 'menus'),
    (6, 'modules'),
    (7, 'parameters'),
    (8, 'plugins'),
    (9, 'templates');

#
# Source Tables
#
INSERT INTO `molajo_source_tables` (`id` ,`source_table`)
  VALUES
    (1, '__applications'),
    (2, '__categories'),
    (3, '__content'),
    (4, '__extension_instances'),
    (5, '__users');

#
# SITES
#
INSERT INTO `molajo_sites` (`id`, `name`, `path`, `description`, `parameters`, `custom_fields`, `base_url`)
  VALUES
    (1, 'Molajo', '1', 'Primary Site', '{}', '{}', ''),
    (2, 'Molajo Site 2', '2', 'Second Site', '{}', '{}', '');

#
# USERS AND GROUPS
#  5,6 asset id reserved for administrator
ALTER TABLE `molajo_groups`
  DROP INDEX `idx_asset_table_id_join`;
  
INSERT INTO `molajo_groups`
  (`id`, `title`, `subtitle`, `description`, `parent_id`, `lft`, `rgt`, `type_id`, `asset_id`, `protected`)
    VALUES
      (1, 'Public', '', 'All visitors regardless of authentication status', 0, 0, 1, 1, 0, 1),
      (2, 'Guest', '', 'Visitors not authenticated', 0, 2, 3, 1, 0, 1),
      (3, 'Registered', '', 'Authentication visitors', 0, 4, 5, 1, 0, 1),
      (4, 'Administrator', '', 'System Administrator', 0, 6, 7, 1, 0, 1);

INSERT INTO `molajo_assets` (`title`, `source_table_id`, `source_id`, `path`, `link`, `view_group_id`, `language`, `translation_of_id`)
  SELECT `title`, 6, `id`, '', '', 1, 'en-GB', NULL
    FROM  molajo_groups

SET @asset_id = (SELECT id FROM `molajo_assets` WHERE `source_id` = 1 AND `source_table_id` = 6);
UPDATE `molajo_groups` SET asset_id = @asset_id WHERE id = 1;

SET @asset_id = (SELECT id FROM `molajo_assets` WHERE `source_id` = 2 AND `source_table_id` = 6);
UPDATE `molajo_groups` SET asset_id = @asset_id WHERE id = 2;

SET @asset_id = (SELECT id FROM `molajo_assets` WHERE `source_id` = 3 AND `source_table_id` = 6);
UPDATE `molajo_groups` SET asset_id = @asset_id WHERE id = 3;

SET @asset_id = (SELECT id FROM `molajo_assets` WHERE `source_id` = 4 AND `source_table_id` = 6);
UPDATE `molajo_groups` SET asset_id = @asset_id WHERE id = 4;

ALTER TABLE `molajo_groups` ADD UNIQUE INDEX `idx_asset_table_id_join` (`asset_id` ASC);

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

INSERT INTO `molajo_applications` (`id`, `name`, `path`, `description`, `asset_id`, `parameters`, `custom_fields`, `default_template_extension_id`, `default_application_indicator`)
  VALUES
    (1, 'site', '', 'Primary application for site visitors', 0, '{}', '{}', 1, 1),
    (2, 'administrator', 'administrator', 'Administrative site area for site construction', 0, '{}', '{}', 2, 0),
    (3, 'content', 'content', 'Area for content development', 0, '{}', '{}', 3, 0);

INSERT INTO `molajo_assets` (`title`, `source_table_id`, `source_id`, `path`, `link`, `view_group_id`, `language`, `translation_of_id`)
  SELECT `name`, 1, `id`, `path`, '', 1, 'en-GB', NULL
    FROM  molajo_applications

SET @asset_id = (SELECT id FROM `molajo_assets` WHERE `source_id` = 1 AND `source_table_id` = 1);
UPDATE `molajo_applications` SET asset_id = @asset_id WHERE id = 1;

SET @asset_id = (SELECT id FROM `molajo_assets` WHERE `source_id` = 2 AND `source_table_id` = 1);
UPDATE `molajo_applications` SET asset_id = @asset_id WHERE id = 2;

SET @asset_id = (SELECT id FROM `molajo_assets` WHERE `source_id` = 3 AND `source_table_id` = 1);
UPDATE `molajo_applications` SET asset_id = @asset_id WHERE id = 3;

SET @asset_id = (SELECT id FROM `molajo_assets` WHERE `source_id` = 4 AND `source_table_id` = 1);
UPDATE `molajo_applications` SET asset_id = @asset_id WHERE id = 4;

#
# UPDATE SITES
#
INSERT INTO `molajo_update_sites`
  VALUES
    (1, 'Molajo Core', 'collection', 'http://update.molajo.org/core/list.xml', 1),
    (2, 'Molajo Directory', 'collection', 'http://update.molajo.org/directory/list.xml', 1);

#
# EXTENSIONS
#

ALTER TABLE `molajo_extension_instances`
  DROP INDEX `asset_id_UNIQUE`

# Components

INSERT INTO `molajo_extensions` (
  `name`, `extension_type_id`, `folder`, `update_site_id`)
  VALUES
    ('com_admin', 1, '', 1),
    ('com_articles', 1, '', 1),
    ('com_categories', 1, '', 1),
    ('com_config', 1, '', 1),
    ('com_dashboard', 1, '', 1),
    ('com_extensions', 1, '', 1),
    ('com_installer', 1, '', 1),
    ('com_layouts', 1, '', 1),
    ('com_login', 1, '', 1),
    ('com_media', 1, '', 1),
    ('com_menus', 1, '', 1),
    ('com_modules', 1, '', 1),
    ('com_plugins', 1, '', 1),
    ('com_redirect', 1, '', 1),
    ('com_search', 1, '', 1),
    ('com_templates', 1, '', 1),
    ('com_admin', 1, '', 1),
    ('com_users', 1, '', 1);

INSERT INTO `molajo_extension_instances` (
  `title`, `protected`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`,
  `extension_type_id`, `created_datetime`, `asset_id`, `extension_id`, `language`, `ordering`)
  SELECT `name`, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1,
        `extension_type_id`, '2011-11-01 00:00:00', 0, `id`, 'en-GB', 1
    FROM molajo_extensions
    WHERE extension_type_id = 1

INSERT INTO `molajo_assets` (`title`, `source_table_id`, `source_id`, `path`, `link`, `view_group_id`, `language`, `translation_of_id`)
  SELECT `title`, 4, `id`, CONCAT('extensions/components/', `id`), CONCAT('index.php?option=com_extensions&view=components&id=', `id`), 5, 'en-GB', NULL
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 1

UPDATE `molajo_extension_instances` a,
    `molajo_assets` b
  SET a.asset_id = b.id
WHERE a.id = b.source_id
  AND a.extension_type_id = 1

# Languages

INSERT INTO `molajo_extensions` (
  `name`, `extension_type_id`, `folder`, `update_site_id`)
  VALUES
    ('English (UK)', 2, '', 1),
    ('English (US)', 2, '', 1);

INSERT INTO `molajo_extension_instances` (
  `title`, `protected`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`,
  `extension_type_id`, `created_datetime`, `asset_id`, `extension_id`, `language`, `ordering`)
  SELECT `name`, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1,
        `extension_type_id`, '2011-11-01 00:00:00', 0, `id`, 'en-GB', 1
    FROM molajo_extensions
    WHERE extension_type_id = 2

INSERT INTO `molajo_assets` (`title`, `source_table_id`, `source_id`, `path`, `link`, `view_group_id`, `language`, `translation_of_id`)
  SELECT `title`, 2, `id`, CONCAT('extensions/languages/', `id`), CONCAT('index.php?option=com_extensions&view=languages&id=', `id`), 5, 'en-GB', NULL
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 2

UPDATE `molajo_extension_instances` a,
    `molajo_assets` b
  SET a.asset_id = b.id
WHERE a.id = b.source_id
  AND a.extension_type_id = 2

# Layouts

INSERT INTO `molajo_extensions` (
  `name`, `extension_type_id`, `folder`, `update_site_id`)
  VALUES
    ('head', 3, 'document', 1),
    ('messages', 3, 'document', 1),
    ('errors', 3, 'document', 1),
    ('atom', 3, 'document', 1),   
    ('rss', 3, 'document', 1),
    
    ('admin_acl_panel', 3, 'extension', 1),
    ('admin_activity', 3, 'extension', 1),
    ('admin_edit', 3, 'extension', 1),
    ('admin_favorites', 3, 'extension', 1),   
    ('admin_feed', 3, 'extension', 1),
    ('admin_footer', 3, 'extension', 1),
    ('admin_header', 3, 'extension', 1),
    ('admin_inbox', 3, 'extension', 1),
    ('admin_launchpad', 3, 'extension', 1),   
    ('admin_list', 3, 'extension', 1),
    ('admin_login', 3, 'extension', 1),
    ('admin_modal', 3, 'extension', 1),
    ('admin_pagination', 3, 'extension', 1),
    ('admin_toolbar', 3, 'extension', 1),   
    ('audio', 3, 'extension', 1),
    ('contact_form', 3, 'extension', 1),
    ('default', 3, 'extension', 1),
    ('dummy', 3, 'extension', 1),
    ('faq', 3, 'extension', 1),   
    ('item', 3, 'extension', 1),
    ('list', 3, 'extension', 1),
    ('items', 3, 'extension', 1),   
    ('list', 3, 'extension', 1),
    ('pagination', 3, 'extension', 1),
    ('social_bookmarks', 3, 'extension', 1),
    ('syntaxhighlighter', 3, 'extension', 1),
    ('table', 3, 'extension', 1),   
    ('tree', 3, 'extension', 1),
    ('twig_example', 3, 'extension', 1),   
    ('video', 3, 'extension', 1),

    ('button', 3, 'formfields', 1),
    ('colorpicker', 3, 'formfields', 1),
    ('list', 3, 'formfields', 1),
    ('media', 3, 'formfields', 1),   
    ('number', 3, 'formfields', 1),
    ('option', 3, 'formfields', 1),
    ('rules', 3, 'formfields', 1),
    ('spacer', 3, 'formfields', 1),
    ('text', 3, 'formfields', 1),   
    ('textarea', 3, 'formfields', 1),
    ('user', 3, 'formfields', 1),
    
    ('article', 3, 'wrap', 1),
    ('aside', 3, 'wrap', 1),
    ('div', 3, 'wrap', 1),
    ('footer', 3, 'wrap', 1),   
    ('horizontal', 3, 'wrap', 1),
    ('nav', 3, 'wrap', 1),
    ('none', 3, 'wrap', 1),
    ('outline', 3, 'wrap', 1),
    ('section', 3, 'wrap', 1),   
    ('table', 3, 'wrap', 1),
    ('tabs', 3, 'wrap', 1);
        
INSERT INTO `molajo_extension_instances` (
  `title`, `protected`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`,
  `extension_type_id`, `created_datetime`, `asset_id`, `extension_id`, `language`, `ordering`)
  SELECT `name`, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1,
        `extension_type_id`, '2011-11-01 00:00:00', 0, `id`, 'en-GB', 1
    FROM molajo_extensions
    WHERE extension_type_id = 3

INSERT INTO `molajo_assets` (`title`, `source_table_id`, `source_id`, `path`, `link`, `view_group_id`, `language`, `translation_of_id`)
  SELECT `title`, 2, `id`, CONCAT('extensions/layouts/', `id`), CONCAT('index.php?option=com_extensions&view=layouts&id=', `id`), 5, 'en-GB', NULL
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 3

UPDATE `molajo_extension_instances` a,
    `molajo_assets` b
  SET a.asset_id = b.id
WHERE a.id = b.source_id
  AND a.extension_type_id = 3

# Libraries

INSERT INTO `molajo_extensions` (
  `name`, `extension_type_id`, `folder`, `update_site_id`)
  VALUES
    ('akismet', 10, '', 1),
    ('Doctrine', 10, '', 1),
    ('Forms', 10, '', 1),
    ('includes', 10, '', 1),
    ('jplatform', 10, '', 1),
    ('molajo', 10, '', 1),
    ('mollom', 10, '', 1),
    ('recaptcha', 10, '', 1),
    ('Twig', 10, '', 1);

INSERT INTO `molajo_extension_instances` (
  `title`, `protected`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`,
  `extension_type_id`, `created_datetime`, `asset_id`, `extension_id`, `language`, `ordering`)
  SELECT `name`, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1,
        `extension_type_id`, '2011-11-01 00:00:00', 0, `id`, 'en-GB', 1
    FROM molajo_extensions
    WHERE extension_type_id = 10

INSERT INTO `molajo_assets` (`title`, `source_table_id`, `source_id`, `path`, `link`, `view_group_id`, `language`, `translation_of_id`)
  SELECT `title`, 4, `id`, CONCAT('extensions/libraries/', `id`), CONCAT('index.php?option=com_extensions&view=libraries&id=', `id`), 5, 'en-GB', NULL
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 10

UPDATE `molajo_extension_instances` a,
    `molajo_assets` b
  SET a.asset_id = b.id
WHERE a.id = b.source_id
  AND a.extension_type_id = 10

# Modules

INSERT INTO `molajo_extensions` (
  `name`, `extension_type_id`, `folder`, `update_site_id`)
  VALUES
    ('mod_breadcrumbs', 6, '', 1),
    ('mod_content', 6, '', 1),
    ('mod_custom', 6, '', 1),
    ('mod_feed', 6, '', 1),
    ('mod_header', 6, '', 1),
    ('mod_launchpad', 6, '', 1),
    ('mod_layout', 6, '', 1),
    ('mod_login', 6, '', 1),
    ('mod_logout', 6, '', 1),
    ('mod_members', 6, '', 1),
    ('mod_menu', 6, '', 1),
    ('mod_pagination', 6, '', 1),
    ('mod_search', 6, '', 1),
    ('mod_syndicate', 6, '', 1),
    ('mod_toolbar', 6, '', 1);

INSERT INTO `molajo_extension_instances` (
  `title`, `protected`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`,
  `extension_type_id`, `created_datetime`, `asset_id`, `extension_id`, `language`, `ordering`)
  SELECT `name`, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1,
        `extension_type_id`, '2011-11-01 00:00:00', 0, `id`, 'en-GB', 1
    FROM molajo_extensions
    WHERE extension_type_id = 6

INSERT INTO `molajo_assets` (`title`, `source_table_id`, `source_id`, `path`, `link`, `view_group_id`, `language`, `translation_of_id`)
  SELECT `title`, 4, `id`, CONCAT('extensions/modules/', `id`), CONCAT('index.php?option=com_extensions&view=modules&id=', `id`), 5, 'en-GB', NULL
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 6

UPDATE `molajo_extension_instances` a,
    `molajo_assets` b
  SET a.asset_id = b.id
WHERE a.id = b.source_id
  AND a.extension_type_id = 6

# Plugins

INSERT INTO `molajo_extensions` (
  `name`, `extension_type_id`, `folder`, `update_site_id`)
  VALUES
    ('example', 8, 'acl', 1),

    ('molajo', 8, 'authentication', 1),

    ('broadcast', 8, 'content', 1),
    ('content', 8, 'content', 1),
    ('emailcloak', 8, 'content', 1),
    ('links', 8, 'content', 1),
    ('loadmodule', 8, 'content', 1),
    ('media', 8, 'content', 1),
    ('protect', 8, 'content', 1),
    ('responses', 8, 'content', 1),

    ('aloha', 8, 'editors', 1),
    ('none', 8, 'editors', 1),

    ('article', 8, 'editor-buttons', 1),
    ('editor', 8, 'editor-buttons', 1),
    ('image', 8, 'editor-buttons', 1),
    ('pagebreak', 8, 'editor-buttons', 1),
    ('readmore', 8, 'editor-buttons', 1),

    ('molajo', 8, 'extension', 1),

    ('extend', 8, 'molajo', 1),
    ('minifier', 8, 'molajo', 1),
    ('search', 8, 'molajo', 1),
    ('tags', 8, 'molajo', 1),
    ('urls', 8, 'molajo', 1),

    ('molajosample', 8, 'query', 1),

    ('categories', 8, 'search', 1),
    ('articles', 8, 'search', 1),

    ('cache', 8, 'system', 1),
    ('compress', 8, 'system', 1),
    ('create', 8, 'system', 1),
    ('debug', 8, 'system', 1),
    ('languagefilter', 8, 'system', 1),
    ('log', 8, 'system', 1),
    ('logout', 8, 'system', 1),
    ('molajo', 8, 'system', 1),
    ('p3p', 8, 'system', 1),
    ('parameters', 8, 'system', 1),
    ('redirect', 8, 'system', 1),
    ('remember', 8, 'system', 1),
    ('system', 8, 'system', 1),
    ('webservices', 8, 'system', 1),

    ('molajo', 8, 'user', 1),
    ('profile', 8, 'user', 1);

INSERT INTO `molajo_extension_instances` (
  `title`, `protected`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`,
  `extension_type_id`, `created_datetime`, `asset_id`, `extension_id`, `language`, `ordering`)
  SELECT `name`, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1,
        `extension_type_id`, '2011-11-01 00:00:00', 0, `id`, 'en-GB', 1
    FROM molajo_extensions
    WHERE extension_type_id = 8

INSERT INTO `molajo_assets` (`title`, `source_table_id`, `source_id`, `path`, `link`, `view_group_id`, `language`, `translation_of_id`)
  SELECT `title`, 4, `id`, CONCAT('extensions/plugins/', `id`), CONCAT('index.php?option=com_extensions&view=plugins&id=', `id`), 5, 'en-GB', NULL
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 8

UPDATE `molajo_extension_instances` a,
    `molajo_assets` b
  SET a.asset_id = b.id
WHERE a.id = b.source_id
  AND a.extension_type_id = 8

## Template
INSERT INTO `molajo_extensions` (
  `name`, `extension_type_id`, `folder`, `update_site_id`)
  VALUES
    ('construct', 9, '', 1),
    ('install', 9, '', 1),
    ('molajito', 9, '', 1),
    ('system', 9, '', 1);

INSERT INTO `molajo_extension_instances` (
  `title`, `protected`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`,
  `extension_type_id`, `created_datetime`, `asset_id`, `extension_id`, `language`, `ordering`)
  SELECT `name`, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1,
        `extension_type_id`, '2011-11-01 00:00:00', 0, `id`, 'en-GB', 1
    FROM molajo_extensions
    WHERE extension_type_id = 9

INSERT INTO `molajo_assets` (`title`, `source_table_id`, `source_id`, `path`, `link`, `view_group_id`, `language`, `translation_of_id`)
  SELECT `title`, 4, `id`, CONCAT('extensions/templates/', `id`), CONCAT('index.php?option=com_extensions&view=templates&id=', `id`), 5, 'en-GB', NULL
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 9

UPDATE `molajo_extension_instances` a,
    `molajo_assets` b
  SET a.asset_id = b.id
WHERE a.id = b.source_id
  AND a.extension_type_id = 9

#
# Menu - Administrator
#

INSERT INTO `molajo_extensions` (
  `id`, `name`, `extension_type_id`, `folder`, `update_site_id`)
  VALUES
    (1000, 'Administrator Home', 5, '', 1),

    (1010, 'Launchpad Main Menu', 5, '', 1),
    (1020, 'Launchpad Configure', 5, '', 1),
    (1030, 'Launchpad Access', 5, '', 1),
    (1040, 'Launchpad Create', 5, '', 1),
    (1050, 'Launchpad Build', 5, '', 1),

    (1060, 'Main Menu', 5, '', 1);

INSERT INTO `molajo_extension_instances` (
  `extension_id`, `ordering`, `title`, `alias`, `menu_item_path`, `menu_item_link`,
  `protected`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`,
  `extension_type_id`, `created_datetime`, `asset_id`, `language`)
  VALUES
    (1000, 1, 'Home', 'home', '', 'index.php?option=com_dashboard', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),

    (1010, 1, 'Configure', 'configure', 'configure', 'index.php?option=com_dashboard&type=configure', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1010, 2, 'Access', 'access', 'access', 'index.php?option=com_dashboard&type=access', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1010, 3, 'Create', 'create', 'create', 'index.php?option=com_dashboard&type=create', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1010, 4, 'Build', 'build', 'build', 'index.php?option=com_dashboard&type=build', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1010, 5, 'Search', 'search', 'search', 'index.php?option=com_dashboard&type=search', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),

    (1020, 1, 'Profile', 'profile', 'configure/profile', 'index.php?option=com_profile', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1020, 2, 'System', 'system', 'configure/system', 'index.php?option=com_config',1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1020, 3, 'Checkin', 'checkin', 'configure/checkin', 'index.php?option=com_checkin', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1020, 4, 'Cache', 'cache', 'configure/cache', 'index.php?option=com_cache', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1020, 5, 'Backup', 'backup', 'configure/backup', 'index.php?option=com_backup', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1020, 6, 'Redirects', 'redirects', 'configure/redirects', 'index.php?option=com_redirects', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),

    (1030, 1, 'Users', 'users', 'access/users', 'index.php?option=com_users', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1030, 2, 'Groups', 'groups', 'access/groups', 'index.php?option=com_groups', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1030, 3, 'Permissions', 'permissions', 'access/permissions', 'index.php?option=com_permissions', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1030, 4, 'Messages', 'messages', 'access/messages', 'index.php?option=com_messages', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1030, 5, 'Activity', 'activity', 'access/activity', 'index.php?option=com_activity', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),

    (1040, 1, 'Articles', 'articles', 'create/articles', 'index.php?option=com_articles', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1040, 2, 'Tags', 'tags', 'create/tags', 'index.php?option=com_tags', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1040, 3, 'Comments', 'comments', 'create/comments', 'index.php?option=com_comments', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1040, 4, 'Media', 'media', 'create/media', 'index.php?option=com_media', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1040, 5, 'Categories', 'categories', 'create/categories', 'index.php?option=com_categories', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),

    (1050, 1, 'Extensions', 'extensions', 'build/extensions', 'index.php?option=com_extensions', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1050, 2, 'Languages', 'languages', 'build/languages', 'index.php?option=com_languages', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1050, 3, 'Layouts', 'layouts', 'build/layouts', 'index.php?option=com_layouts', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1050, 4, 'Modules', 'modules', 'build/modules', 'index.php?option=com_modules', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1050, 5, 'Plugins', 'plugins', 'build/plugins', 'index.php?option=com_plugins', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1050, 6, 'Templates', 'templates', 'build/templates', 'index.php?option=com_templates', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),

    (1060, 1, 'Home', 'home', '', 'index.php?option=com_articles', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1060, 2, 'New Article', 'new-article', 'new-article', 'index.php?option=com_articles&view=article&layout=edit',  1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1060, 3, 'Article', 'article', 'article', 'index.php?option=com_articles&view=articles&layout=item&id=5',  1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1060, 4, 'Blog', 'blog', 'blog', 'index.php?option=com_articles&view=articles&layout=items&catid=2',  1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1060, 5, 'List', 'list', 'list', 'index.php?option=com_articles&view=articles&layout=table&catid=2',  1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1060, 6, 'Table', 'table', 'table', 'index.php?option=com_articles&type=search', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1060, 7, 'Login', 'login', 'login', 'index.php?option=com_users&view=login',  1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1060, 8, 'Search', 'search', 'search', 'index.php?option=com_search&type=search',  1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB');

INSERT INTO `molajo_assets` (`title`, `source_table_id`, `source_id`, `path`, `link`, `view_group_id`, `language`, `translation_of_id`)
  SELECT `title`, 4, `id`, CONCAT('extensions/menuitem/', `id`), CONCAT('index.php?option=com_extensions&view=menuitem&id=', `id`), 5, 'en-GB', NULL
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5

UPDATE `molajo_extension_instances` a,
    `molajo_assets` b
  SET a.asset_id = b.id
WHERE a.id = b.source_id
  AND a.extension_type_id = 5

#
# Configuration
#

/* TABLE */

/* 100 MOLAJO_CONFIG_OPTION_ID_TABLE */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 100, '', '', 0),
('core', 100, '__common', '__common', 1);

/* 200 MOLAJO_CONFIG_OPTION_ID_FIELDS */
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 200, '', '', 0),
('core', 200, 'access', 'MOLAJO_FIELD_ACCESS_LABEL', 1),
('core', 200, 'alias', 'MOLAJO_FIELD_ALIAS_LABEL', 2),
('core', 200, 'asset_id', 'MOLAJO_FIELD_ASSET_ID_LABEL', 3),
('core', 200, 'attribs', 'MOLAJO_FIELD_ATTRIBS_LABEL', 4),
('core', 200, 'catid', 'MOLAJO_FIELD_CATID_LABEL', 5),
('core', 200, 'checked_out', 'MOLAJO_FIELD_CHECKED_OUT_LABEL', 6),
('core', 200, 'checked_out_time', 'MOLAJO_FIELD_CHECKED_OUT_TIME_LABEL', 7),
('core', 200, 'component_id', 'MOLAJO_FIELD_COMPONENT_ID_LABEL', 8),
('core', 200, 'content_table', 'MOLAJO_FIELD_CONTENT_TABLE_LABEL', 9),
('core', 200, 'content_email_address', 'MOLAJO_FIELD_CONTENT_EMAIL_ADDRESS_LABEL', 10),
('core', 200, 'content_file', 'MOLAJO_FIELD_CONTENT_FILE_LABEL', 11),
('core', 200, 'content_link', 'MOLAJO_FIELD_CONTENT_LINK_LABEL', 12),
('core', 200, 'content_numeric_value', 'MOLAJO_FIELD_CONTENT_NUMERIC_VALUE_LABEL', 13),
('core', 200, 'content_text', 'MOLAJO_FIELD_CONTENT_TEXT_LABEL', 14),
('core', 200, 'content_type', 'MOLAJO_FIELD_CONTENT_TYPE_LABEL', 15),
('core', 200, 'created', 'MOLAJO_FIELD_CREATED_LABEL', 16),
('core', 200, 'created_by', 'MOLAJO_FIELD_CREATED_BY_LABEL', 17),
('core', 200, 'created_by_alias', 'MOLAJO_FIELD_CREATED_BY_ALIAS_LABEL', 18),
('core', 200, 'created_by_email', 'MOLAJO_FIELD_CREATED_BY_EMAIL_LABEL', 19),
('core', 200, 'created_by_ip_address', 'MOLAJO_FIELD_CREATED_BY_IP_ADDRESS_LABEL', 20),
('core', 200, 'created_by_referer', 'MOLAJO_FIELD_CREATED_BY_REFERER_LABEL', 21),
('core', 200, 'created_by_website', 'MOLAJO_FIELD_CREATED_BY_WEBSITE_LABEL', 22),
('core', 200, 'featured', 'MOLAJO_FIELD_FEATURED_LABEL', 23),
('core', 200, 'id', 'MOLAJO_FIELD_ID_LABEL', 24),
('core', 200, 'language', 'MOLAJO_FIELD_LANGUAGE_LABEL', 25),
('core', 200, 'level', 'MOLAJO_FIELD_LEVEL_LABEL', 26),
('core', 200, 'lft', 'MOLAJO_FIELD_LFT_LABEL', 27),
('core', 200, 'metadata', 'MOLAJO_FIELD_METADATA_LABEL', 28),
('core', 200, 'metadesc', 'MOLAJO_FIELD_METADESC_LABEL', 29),
('core', 200, 'metakey', 'MOLAJO_FIELD_METAKEY_LABEL', 30),
('core', 200, 'meta_author', 'MOLAJO_FIELD_META_AUTHOR_LABEL', 31),
('core', 200, 'meta_rights', 'MOLAJO_FIELD_META_RIGHTS_LABEL', 32),
('core', 200, 'meta_robots', 'MOLAJO_FIELD_META_ROBOTS_LABEL', 33),
('core', 200, 'modified', 'MOLAJO_FIELD_MODIFIED_LABEL', 34),
('core', 200, 'modified_by', 'MOLAJO_FIELD_MODIFIED_BY_LABEL', 35),
('core', 200, 'ordering', 'MOLAJO_FIELD_ORDERING_LABEL', 36),
('core', 200, 'publish_down', 'MOLAJO_FIELD_PUBLISH_DOWN_LABEL', 37),
('core', 200, 'publish_up', 'MOLAJO_FIELD_PUBLISH_UP_LABEL', 38),
('core', 200, 'rgt', 'MOLAJO_FIELD_RGT_LABEL', 39),
('core', 200, 'state', 'MOLAJO_FIELD_STATE_LABEL', 40),
('core', 200, 'state_prior_to_version', 'MOLAJO_FIELD_STATE_PRIOR_TO_VERSION_LABEL', 41),
('core', 200, 'stickied', 'MOLAJO_FIELD_STICKIED_LABEL', 42),
('core', 200, 'user_default', 'MOLAJO_FIELD_USER_DEFAULT_LABEL', 43),
('core', 200, 'category_default', 'MOLAJO_FIELD_CATEGORY_DEFAULT_LABEL', 44),
('core', 200, 'title', 'MOLAJO_FIELD_TITLE_LABEL', 45),
('core', 200, 'subtitle', 'MOLAJO_FIELD_SUBTITLE_LABEL', 46),
('core', 200, 'version', 'MOLAJO_FIELD_VERSION_LABEL', 47),
('core', 200, 'version_of_id', 'MOLAJO_FIELD_VERSION_OF_ID_LABEL', 48);

/* 210 MOLAJO_CONFIG_OPTION_ID_PUBLISH_FIELDS */
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 210, '', '', 0),
('core', 210, 'access', 'MOLAJO_FIELD_ACCESS_LABEL', 1),
('core', 210, 'featured', 'MOLAJO_FIELD_FEATURED_LABEL', 2),
('core', 210, 'ordering', 'MOLAJO_FIELD_ORDERING_LABEL', 3),
('core', 210, 'publish_down', 'MOLAJO_FIELD_PUBLISH_DOWN_LABEL', 4),
('core', 210, 'publish_up', 'MOLAJO_FIELD_PUBLISH_UP_LABEL', 5),
('core', 210, 'state', 'MOLAJO_FIELD_STATE_LABEL', 6),
('core', 210, 'stickied', 'MOLAJO_FIELD_STICKIED_LABEL', 7);

/* 220 MOLAJO_CONFIG_OPTION_ID_JSON_FIELDS */
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 220, '', '', 0),
('core', 220, 'attribs', 'MOLAJO_FIELD_ATTRIBS_LABEL', 1),
('core', 220, 'metadata', 'MOLAJO_FIELD_METADATA_LABEL', 2),
('core', 220, 'params', 'MOLAJO_FIELD_PARAMETERS_LABEL', 3);

/* 230 MOLAJO_CONFIG_OPTION_ID_CONTENT_TYPES */
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 230, '', '', 0),
('core', 230, 'content_type', 'Content Type', 1);

/* 250 MOLAJO_CONFIG_OPTION_ID_STATE */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 250, '', '', 0),
('core', 250, '2', 'MOLAJO_OPTION_ARCHIVED', 1),
('core', 250, '1', 'MOLAJO_OPTION_PUBLISHED', 2),
('core', 250, '0', 'MOLAJO_OPTION_UNPUBLISHED', 3),
('core', 250, '-1', 'MOLAJO_OPTION_TRASHED', 4),
('core', 250, '-2', 'MOLAJO_OPTION_SPAMMED', 5),
('core', 250, '-10', 'MOLAJO_OPTION_VERSION', 6);

/* USER INTERFACE */

/* 300 MOLAJO_CONFIG_OPTION_ID_LIST_TOOLBAR_BUTTONS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 300, '', '', 0),
('core', 300, 'archive', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_ARCHIVE', 1),
('core', 300, 'checkin', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_CHECKIN', 2),
('core', 300, 'delete', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_DELETE', 3),
('core', 300, 'edit', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_EDIT', 4),
('core', 300, 'feature', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_FEATURE', 5),
('core', 300, 'help', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_HELP', 6),
('core', 300, 'new', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_NEW', 7),
('core', 300, 'options', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_OPTIONS', 8),
('core', 300, 'publish', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_PUBLISH', 9),
('core', 300, 'restore', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_RESTORE', 10),
('core', 300, 'separator', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SEPARATOR', 11),
('core', 300, 'spam', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SPAM', 12),
('core', 300, 'sticky', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_STICKY', 13),
('core', 300, 'trash', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_TRASH', 14),
('core', 300, 'unpublish', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_UNPUBLISH', 15);

/* 310 MOLAJO_CONFIG_OPTION_ID_EDIT_TOOLBAR_BUTTONS */
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 310, '', '', 0),
('core', 310, 'apply', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_APPLY', 1),
('core', 310, 'close', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_CLOSE', 2),
('core', 310, 'help', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_HELP', 3),
('core', 310, 'restore', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_RESTORE', 4),
('core', 310, 'save', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SAVE', 5),
('core', 310, 'save2new', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SAVE_AND_NEW', 6),
('core', 310, 'save2copy', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SAVE_AS_COPY', 7),
('core', 310, 'separator', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SEPARATOR', 8);

/* 320 MOLAJO_CONFIG_OPTION_ID_TOOLBAR_SUBMENU_LINKS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 320, '', '', 0),
('core', 320, 'category', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_CATEGORY', 1),
('core', 320, 'default', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_DEFAULT', 2),
('core', 320, 'featured', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_FEATURED', 3),
('core', 320, 'revisions', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_REVISIONS', 4),
('core', 320, 'stickied', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_STICKIED', 5),
('core', 320, 'unpublished', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_UNPUBLISHED', 6);

/* 330 MOLAJO_CONFIG_OPTION_ID_LISTBOX_FILTER */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 330, '', '', 0),
('core', 330, 'access', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_ACCESS', 1),
('core', 330, 'alias', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_ALIAS', 2),
('core', 330, 'created_by', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_AUTHOR', 3),
('core', 330, 'catid', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_CATEGORY', 4),
('core', 330, 'content_type', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_CONTENT_TYPE', 5),
('core', 330, 'created', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_CREATE_DATE', 6),
('core', 330, 'featured', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_FEATURED', 7),
('core', 330, 'language', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_LANGUAGE', 9),
('core', 330, 'modified', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_UPDATE_DATE', 10),
('core', 330, 'publish_up', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_PUBLISH_DATE', 11),
('core', 330, 'state', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_STATE', 12),
('core', 330, 'stickied', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_STICKIED', 13),
('core', 330, 'title', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_TITLE', 14),
('core', 330, 'subtitle', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_SUBTITLE', 15);

/* 340 MOLAJO_CONFIG_OPTION_ID_EDITOR_BUTTONS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 340, '', '', 0),
('core', 340, 'article', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_ARTICLE', 1),
('core', 340, 'audio', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_AUDIO', 2),
('core', 340, 'file', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_FILE', 3),
('core', 340, 'gallery', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_GALLERY', 4),
('core', 340, 'image', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_IMAGE', 5),
('core', 340, 'pagebreak', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_PAGEBREAK', 6),
('core', 340, 'readmore', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_READMORE', 7),
('core', 340, 'video', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_VIDEO', 8);

/* MIME from ftp://ftp.iana.org/assignments/media-types/ */

/* 400 MOLAJO_CONFIG_OPTION_ID_AUDIO_MIMES */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 400, '', '', 0),
('core', 400, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 1),
('core', 400, 'sp-midi', 'sp-midi', 2),
('core', 400, 'vnd.3gpp.iufp', 'vnd.3gpp.iufp', 3),
('core', 400, 'vnd.4SB', 'vnd.4SB', 4),
('core', 400, 'vnd.CELP', 'vnd.CELP', 5),
('core', 400, 'vnd.audiokoz', 'vnd.audiokoz', 6),
('core', 400, 'vnd.cisco.nse', 'vnd.cisco.nse', 7),
('core', 400, 'vnd.cmles.radio-events', 'vnd.cmles.radio-events', 8),
('core', 400, 'vnd.cns.anp1', 'vnd.cns.anp1', 9),
('core', 400, 'vnd.cns.inf1', 'vnd.cns.inf1', 10),
('core', 400, 'vnd.dece.audio', 'vnd.dece.audio', 11),
('core', 400, 'vnd.digital-winds', 'vnd.digital-winds', 12),
('core', 400, 'vnd.dlna.adts', 'vnd.dlna.adts', 13),
('core', 400, 'vnd.dolby.heaac.1', 'vnd.dolby.heaac.1', 14),
('core', 400, 'vnd.dolby.heaac.2', 'vnd.dolby.heaac.2', 15),
('core', 400, 'vnd.dolby.mlp', 'vnd.dolby.mlp', 16),
('core', 400, 'vnd.dolby.mps', 'vnd.dolby.mps', 17),
('core', 400, 'vnd.dolby.pl2', 'vnd.dolby.pl2', 18),
('core', 400, 'vnd.dolby.pl2x', 'vnd.dolby.pl2x', 19),
('core', 400, 'vnd.dolby.pl2z', 'vnd.dolby.pl2z', 20),
('core', 400, 'vnd.dolby.pulse.1', 'vnd.dolby.pulse.1', 21),
('core', 400, 'vnd.dra', 'vnd.dra', 22),
('core', 400, 'vnd.dts', 'vnd.dts', 23),
('core', 400, 'vnd.dts.hd', 'vnd.dts.hd', 24),
('core', 400, 'vnd.dvb.file', 'vnd.dvb.file', 25),
('core', 400, 'vnd.everad.plj', 'vnd.everad.plj', 26),
('core', 400, 'vnd.hns.audio', 'vnd.hns.audio', 27),
('core', 400, 'vnd.lucent.voice', 'vnd.lucent.voice', 28),
('core', 400, 'vnd.ms-playready.media.pya', 'vnd.ms-playready.media.pya', 29),
('core', 400, 'vnd.nokia.mobile-xmf', 'vnd.nokia.mobile-xmf', 30),
('core', 400, 'vnd.nortel.vbk', 'vnd.nortel.vbk', 31),
('core', 400, 'vnd.nuera.ecelp4800', 'vnd.nuera.ecelp4800', 32),
('core', 400, 'vnd.nuera.ecelp7470', 'vnd.nuera.ecelp7470', 33),
('core', 400, 'vnd.nuera.ecelp9600', 'vnd.nuera.ecelp9600', 34),
('core', 400, 'vnd.octel.sbc', 'vnd.octel.sbc', 35),
('core', 400, 'vnd.qcelp', 'vnd.qcelp', 36),
('core', 400, 'vnd.rhetorex.32kadpcm', 'vnd.rhetorex.32kadpcm', 37),
('core', 400, 'vnd.rip', 'vnd.rip', 38),
('core', 400, 'vnd.sealedmedia.softseal-mpeg', 'vnd.sealedmedia.softseal-mpeg', 39),
('core', 400, 'vnd.vmx.cvsd', 'vnd.vmx.cvsd', 40);

/* 410 MOLAJO_CONFIG_OPTION_ID_IMAGE_MIMES */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 410, '', '', 0),
('core', 410, 'cgm', 'cgm', 1),
('core', 410, 'jp2', 'jp2', 2),
('core', 410, 'jpm', 'jpm', 3),
('core', 410, 'jpx', 'jpx', 4),
('core', 410, 'naplps', 'naplps', 5),
('core', 410, 'png', 'png', 6),
('core', 410, 'prs.btif', 'prs.btif', 7),
('core', 410, 'prs.pti', 'prs.pti', 8),
('core', 410, 'vnd-djvu', 'vnd-djvu', 9),
('core', 410, 'vnd-svf', 'vnd-svf', 10),
('core', 410, 'vnd-wap-wbmp', 'vnd-wap-wbmp', 11),
('core', 410, 'vnd.adobe.photoshop', 'vnd.adobe.photoshop', 12),
('core', 410, 'vnd.cns.inf2', 'vnd.cns.inf2', 13),
('core', 410, 'vnd.dece.graphic', 'vnd.dece.graphic', 14),
('core', 410, 'vnd.dvb.subtitle', 'vnd.dvb.subtitle', 15),
('core', 410, 'vnd.dwg', 'vnd.dwg', 16),
('core', 410, 'vnd.dxf', 'vnd.dxf', 17),
('core', 410, 'vnd.fastbidsheet', 'vnd.fastbidsheet', 18),
('core', 410, 'vnd.fpx', 'vnd.fpx', 19),
('core', 410, 'vnd.fst', 'vnd.fst', 20),
('core', 410, 'vnd.fujixerox.edmics-mmr', 'vnd.fujixerox.edmics-mmr', 21),
('core', 410, 'vnd.fujixerox.edmics-rlc', 'vnd.fujixerox.edmics-rlc', 22),
('core', 410, 'vnd.globalgraphics.pgb', 'vnd.globalgraphics.pgb', 23),
('core', 410, 'vnd.microsoft.icon', 'vnd.microsoft.icon', 24),
('core', 410, 'vnd.mix', 'vnd.mix', 25),
('core', 410, 'vnd.ms-modi', 'vnd.ms-modi', 26),
('core', 410, 'vnd.net-fpx', 'vnd.net-fpx', 27),
('core', 410, 'vnd.radiance', 'vnd.radiance', 28),
('core', 410, 'vnd.sealed-png', 'vnd.sealed-png', 29),
('core', 410, 'vnd.sealedmedia.softseal-gif', 'vnd.sealedmedia.softseal-gif', 30),
('core', 410, 'vnd.sealedmedia.softseal-jpg', 'vnd.sealedmedia.softseal-jpg', 31),
('core', 410, 'vnd.xiff', 'vnd.xiff', 32);

/* 420 MOLAJO_CONFIG_OPTION_ID_TEXT_MIMES */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 420, '', '', 0),
('core', 420, 'n3', 'n3', 1),
('core', 420, 'prs.fallenstein.rst', 'prs.fallenstein.rst', 2),
('core', 420, 'prs.lines.tag', 'prs.lines.tag', 3),
('core', 420, 'rtf', 'rtf', 4),
('core', 420, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 5),
('core', 420, 'tab-separated-values', 'tab-separated-values', 6),
('core', 420, 'turtle', 'turtle', 7),
('core', 420, 'vnd-curl', 'vnd-curl', 8),
('core', 420, 'vnd.DMClientScript', 'vnd.DMClientScript', 9),
('core', 420, 'vnd.IPTC.NITF', 'vnd.IPTC.NITF', 10),
('core', 420, 'vnd.IPTC.NewsML', 'vnd.IPTC.NewsML', 11),
('core', 420, 'vnd.abc', 'vnd.abc', 12),
('core', 420, 'vnd.curl', 'vnd.curl', 13),
('core', 420, 'vnd.dvb.subtitle', 'vnd.dvb.subtitle', 14),
('core', 420, 'vnd.esmertec.theme-descriptor', 'vnd.esmertec.theme-descriptor', 15),
('core', 420, 'vnd.fly', 'vnd.fly', 16),
('core', 420, 'vnd.fmi.flexstor', 'vnd.fmi.flexstor', 17),
('core', 420, 'vnd.graphviz', 'vnd.graphviz', 18),
('core', 420, 'vnd.in3d.3dml', 'vnd.in3d.3dml', 19),
('core', 420, 'vnd.in3d.spot', 'vnd.in3d.spot', 20),
('core', 420, 'vnd.latex-z', 'vnd.latex-z', 21),
('core', 420, 'vnd.motorola.reflex', 'vnd.motorola.reflex', 22),
('core', 420, 'vnd.ms-mediapackage', 'vnd.ms-mediapackage', 23),
('core', 420, 'vnd.net2phone.commcenter.command', 'vnd.net2phone.commcenter.command', 24),
('core', 420, 'vnd.si.uricatalogue', 'vnd.si.uricatalogue', 25),
('core', 420, 'vnd.sun.j2me.app-descriptor', 'vnd.sun.j2me.app-descriptor', 26),
('core', 420, 'vnd.trolltech.linguist', 'vnd.trolltech.linguist', 27),
('core', 420, 'vnd.wap-wml', 'vnd.wap-wml', 28),
('core', 420, 'vnd.wap.si', 'vnd.wap.si', 29),
('core', 420, 'vnd.wap.wmlscript', 'vnd.wap.wmlscript', 30);

/* 430 MOLAJO_CONFIG_OPTION_ID_VIDEO_MIMES */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 430, '', '', 0),
('core', 430, 'jpm', 'jpm', 1),
('core', 430, 'mj2', 'mj2', 2),
('core', 430, 'quicktime', 'quicktime', 3),
('core', 430, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 4),
('core', 430, 'vnd-mpegurl', 'vnd-mpegurl', 5),
('core', 430, 'vnd-vivo', 'vnd-vivo', 6),
('core', 430, 'vnd.CCTV', 'vnd.CCTV', 7),
('core', 430, 'vnd.dece-mp4', 'vnd.dece-mp4', 8),
('core', 430, 'vnd.dece.hd', 'vnd.dece.hd', 9),
('core', 430, 'vnd.dece.mobile', 'vnd.dece.mobile', 10),
('core', 430, 'vnd.dece.pd', 'vnd.dece.pd', 11),
('core', 430, 'vnd.dece.sd', 'vnd.dece.sd', 12),
('core', 430, 'vnd.dece.video', 'vnd.dece.video', 13),
('core', 430, 'vnd.directv-mpeg', 'vnd.directv-mpeg', 14),
('core', 430, 'vnd.directv.mpeg-tts', 'vnd.directv.mpeg-tts', 15),
('core', 430, 'vnd.dvb.file', 'vnd.dvb.file', 16),
('core', 430, 'vnd.fvt', 'vnd.fvt', 17),
('core', 430, 'vnd.hns.video', 'vnd.hns.video', 18),
('core', 430, 'vnd.iptvforum.1dparityfec-1010', 'vnd.iptvforum.1dparityfec-1010', 19),
('core', 430, 'vnd.iptvforum.1dparityfec-2005', 'vnd.iptvforum.1dparityfec-2005', 20),
('core', 430, 'vnd.iptvforum.2dparityfec-1010', 'vnd.iptvforum.2dparityfec-1010', 21),
('core', 430, 'vnd.iptvforum.2dparityfec-2005', 'vnd.iptvforum.2dparityfec-2005', 22),
('core', 430, 'vnd.iptvforum.ttsavc', 'vnd.iptvforum.ttsavc', 23),
('core', 430, 'vnd.iptvforum.ttsmpeg2', 'vnd.iptvforum.ttsmpeg2', 24),
('core', 430, 'vnd.motorola.video', 'vnd.motorola.video', 25),
('core', 430, 'vnd.motorola.videop', 'vnd.motorola.videop', 26),
('core', 430, 'vnd.mpegurl', 'vnd.mpegurl', 27),
('core', 430, 'vnd.ms-playready.media.pyv', 'vnd.ms-playready.media.pyv', 28),
('core', 430, 'vnd.nokia.interleaved-multimedia', 'vnd.nokia.interleaved-multimedia', 29),
('core', 430, 'vnd.nokia.videovoip', 'vnd.nokia.videovoip', 30),
('core', 430, 'vnd.objectvideo', 'vnd.objectvideo', 31),
('core', 430, 'vnd.sealed-swf', 'vnd.sealed-swf', 32),
('core', 430, 'vnd.sealed.mpeg1', 'vnd.sealed.mpeg1', 33),
('core', 430, 'vnd.sealed.mpeg4', 'vnd.sealed.mpeg4', 34),
('core', 430, 'vnd.sealed.swf', 'vnd.sealed.swf', 35),
('core', 430, 'vnd.sealedmedia.softseal-mov', 'vnd.sealedmedia.softseal-mov', 36),
('core', 430, 'vnd.uvvu.mp4', 'vnd.uvvu.mp4', 37);

/** MVC */

/* CONTROLLER TASKS */

/* 1100 MOLAJO_CONFIG_OPTION_ID_TASK_TO_CONTROLLER */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1100, '', '', 0),
('core', 1100, 'add', 'display', 1),
('core', 1100, 'edit', 'display', 2),
('core', 1100, 'display', 'display', 3);

INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1100, 'apply', 'edit', 4),
('core', 1100, 'cancel', 'edit', 5),
('core', 1100, 'create', 'edit', 6),
('core', 1100, 'save', 'edit', 7),
('core', 1100, 'save2copy', 'edit', 8),
('core', 1100, 'save2new', 'edit', 9),
('core', 1100, 'restore', 'edit', 10);

INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1100, 'archive', 'multiple', 11),
('core', 1100, 'publish', 'multiple', 12),
('core', 1100, 'unpublish', 'multiple', 13),
('core', 1100, 'spam', 'multiple', 14),
('core', 1100, 'trash', 'multiple', 15),
('core', 1100, 'feature', 'multiple', 16),
('core', 1100, 'unfeature', 'multiple', 17),
('core', 1100, 'sticky', 'multiple', 18),
('core', 1100, 'unsticky', 'multiple', 19),
('core', 1100, 'checkin', 'multiple', 20),
('core', 1100, 'reorder', 'multiple', 21),
('core', 1100, 'orderup', 'multiple', 22),
('core', 1100, 'orderdown', 'multiple', 23),
('core', 1100, 'saveorder', 'multiple', 24),
('core', 1100, 'delete', 'multiple', 25),
('core', 1100, 'copy', 'multiple', 26),
('core', 1100, 'move', 'multiple', 27);

INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1100, 'login', 'login', 28),
('core', 1100, 'logout', 'logout', 29);

/* 1100 MOLAJO_CONFIG_OPTION_ID_TASK_TO_CONTROLLER +application id */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1101, '', '', 0),
('core', 1101, 'add', 'display', 1),
('core', 1101, 'edit', 'display', 2),
('core', 1101, 'display', 'display', 3);

INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1101, 'apply', 'edit', 4),
('core', 1101, 'cancel', 'edit', 5),
('core', 1101, 'create', 'edit', 6),
('core', 1101, 'save', 'edit', 7),
('core', 1101, 'save2copy', 'edit', 8),
('core', 1101, 'save2new', 'edit', 9),
('core', 1101, 'restore', 'edit', 10);

INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1101, 'archive', 'multiple', 11),
('core', 1101, 'publish', 'multiple', 12),
('core', 1101, 'unpublish', 'multiple', 13),
('core', 1101, 'spam', 'multiple', 14),
('core', 1101, 'trash', 'multiple', 15),
('core', 1101, 'feature', 'multiple', 16),
('core', 1101, 'unfeature', 'multiple', 17),
('core', 1101, 'sticky', 'multiple', 18),
('core', 1101, 'unsticky', 'multiple', 19),
('core', 1101, 'checkin', 'multiple', 20),
('core', 1101, 'reorder', 'multiple', 21),
('core', 1101, 'orderup', 'multiple', 22),
('core', 1101, 'orderdown', 'multiple', 23),
('core', 1101, 'saveorder', 'multiple', 24),
('core', 1101, 'delete', 'multiple', 25),
('core', 1101, 'copy', 'multiple', 26),
('core', 1101, 'move', 'multiple', 27);

INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1101, 'login', 'login', 28),
('core', 1101, 'logout', 'login', 29);

/* OPTION */

/* 1800 MOLAJO_CONFIG_OPTION_ID_DEFAULT_OPTION */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 1800, '', '', 0),
('core', 1800, 'com_articles', 'com_articles', 1),
('core', 1801, '', '', 0),
('core', 1801, 'com_login', 'com_login', 1);

/* VIEWS */

/* 2000 MOLAJO_CONFIG_OPTION_ID_VIEWS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 2000, '', '', 0),
('core', 2000, 'display', 'display', 1),
('core', 2000, 'edit', 'edit', 2);

/* 2100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_VIEW */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 2100, '', '', 0),
('core', 2100, 'display', 'display', 1);

/* 2000 MOLAJO_CONFIG_OPTION_ID_VIEWS +application id **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 2001, '', '', 0),
('core', 2001, 'display', 'display', 1),
('core', 2001, 'edit', 'edit', 2);

/* 2100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_VIEW +application id **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 2101, '', '', 0),
('core', 2101, 'display', 'display', 1);

/* VIEW LAYOUTS */

/* 3000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_LAYOUTS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3000, '', '', 0),
('core', 3000, 'default', 'default', 1),
('core', 3000, 'item', 'item', 1),
('core', 3000, 'items', 'items', 1),
('core', 3000, 'table', 'table', 1);

/* 3100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3100, '', '', 0),
('core', 3100, 'default', 'default', 1);

/* 3200 MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_LAYOUTS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3200, '', '', 0),
('core', 3200, 'default', 'default', 1);

/* 3300 MOLAJO_CONFIG_OPTION_ID_DEFAULT_EDIT_VIEW_LAYOUTS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3300, '', '', 0),
('core', 3300, 'default', 'default', 1);

/* 3000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_LAYOUTS +application id **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3001, '', '', 0),
('core', 3001, 'default', 'default', 1);

/* 3100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS +application id **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3101, '', '', 0),
('core', 3101, 'default', 'default', 1);

/* 3200 MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_LAYOUTS +application id **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3201, '', '', 0),
('core', 3201, 'default', 'default', 1);

/* 3300 MOLAJO_CONFIG_OPTION_ID_DEFAULT_EDIT_VIEW_LAYOUTS +application id **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 3301, '', '', 0),
('core', 3301, 'default', 'default', 1);

/* VIEW FORMATS */

/* 4000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_FORMATS */
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4000, '', '', 0),
('core', 4000, 'html', 'html', 1);

/* 4100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_FORMATS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4100, '', '', 0),
('core', 4100, 'html', 'html', 1);

/* 4200 MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_FORMATS */
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4200, '', '', 0),
('core', 4200, 'error', 'error', 1),
('core', 4200, 'feed', 'feed', 2),
('core', 4200, 'html', 'html', 3),
('core', 4200, 'json', 'json', 4),
('core', 4200, 'opensearch', 'opensearch', 5),
('core', 4200, 'raw', 'raw', 6),
('core', 4200, 'xls', 'xls', 7),
('core', 4200, 'xml', 'xml', 8),
('core', 4200, 'xmlrpc', 'xmlrpc', 9);

/* 4300 MOLAJO_CONFIG_OPTION_ID_DEFAULT_EDIT_VIEW_FORMATS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4300, '', '', 0),
('core', 4300, 'html', 'html', 1);


/* 4000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_FORMATS +application id */
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4001, '', '', 0),
('core', 4001, 'html', 'html', 1);

/* 4100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_FORMATS +application id */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4101, '', '', 0),
('core', 4101, 'html', 'html', 1);

/* 4200 MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_FORMATS +application id */
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4201, '', '', 0),
('core', 4201, 'error', 'error', 1),
('core', 4201, 'feed', 'feed', 2),
('core', 4201, 'html', 'html', 3),
('core', 4201, 'json', 'json', 4),
('core', 4201, 'opensearch', 'opensearch', 5),
('core', 4201, 'raw', 'raw', 6),
('core', 4201, 'xls', 'xls', 7),
('core', 4201, 'xml', 'xml', 8),
('core', 4201, 'xmlrpc', 'xmlrpc', 9);

/* 4300 MOLAJO_CONFIG_OPTION_ID_DEFAULT_EDIT_VIEW_FORMATS +application id */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 4301, '', '', 0),
('core', 4301, 'html', 'html', 1);

/* 5000 MOLAJO_CONFIG_OPTION_ID_MODEL +application id */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 5000, '', '', 0),
('core', 5000, 'display', 'display', 1),
('core', 5000, 'edit', 'edit', 2);

/* 5001 MOLAJO_CONFIG_OPTION_ID_MODEL +application id */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 5001, '', '', 0),
('core', 5001, 'display', 'display', 1),
('core', 5001, 'edit', 'edit', 2);

/* 6000 MOLAJO_CONFIG_OPTION_ID_PLUGIN_TYPE */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 6000, '', '', 0),
('core', 6000, 'content', 'content', 1);

/** ACL Component Information */

/** 10000 MOLAJO_CONFIG_OPTION_ID_ACL_IMPLEMENTATION **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 10000, '', '', 0),
('core', 10000, 'core', 'Core ACL Implementation', 1);

/** 10100 MOLAJO_CONFIG_OPTION_ID_ACL_ITEM_TESTS **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 10100, '', '', 0),
('core', 10100, 'view', 'view', 1),
('core', 10100, 'create', 'create', 2),
('core', 10100, 'edit', 'edit', 3),
('core', 10100, 'publish', 'publish', 4),
('core', 10100, 'delete', 'delete', 5),
('core', 10100, 'admin', 'admin', 6);

/** 10000 MOLAJO_CONFIG_OPTION_ID_TASK_ACL_METHODS **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('core', 10200, '', '', 0),
('core', 10200, 'add', 'create', 1),
('core', 10200, 'admin', 'admin', 2),
('core', 10200, 'apply', 'edit', 3),
('core', 10200, 'archive', 'publish', 4),
('core', 10200, 'cancel', '', 5),
('core', 10200, 'checkin', 'admin', 6),
('core', 10200, 'close', '', 7),
('core', 10200, 'copy', 'create', 8),
('core', 10200, 'create', 'create', 9),
('core', 10200, 'delete', 'delete', 10),
('core', 10200, 'view', 'view', 11),
('core', 10200, 'edit', 'edit', 12),
('core', 10200, 'editstate', 'publish', 13),
('core', 10200, 'feature', 'publish', 14),
('core', 10200, 'login', 'login', 15),
('core', 10200, 'logout', 'logout', 16),
('core', 10200, 'manage', 'edit', 17),
('core', 10200, 'move', 'edit', 18),
('core', 10200, 'orderdown', 'publish', 19),
('core', 10200, 'orderup', 'publish', 20),
('core', 10200, 'publish', 'publish', 21),
('core', 10200, 'reorder', 'publish', 22),
('core', 10200, 'restore', 'publish', 23),
('core', 10200, 'save', 'edit', 24),
('core', 10200, 'save2copy', 'edit', 25),
('core', 10200, 'save2new', 'edit', 26),
('core', 10200, 'saveorder', 'publish', 27),
('core', 10200, 'search', 'view', 28),
('core', 10200, 'spam', 'publish', 29),
('core', 10200, 'state', 'publish', 30),
('core', 10200, 'sticky', 'publish', 31),
('core', 10200, 'trash', 'publish', 32),
('core', 10200, 'unfeature', 'publish', 33),
('core', 10200, 'unpublish', 'publish', 34),
('core', 10200, 'unsticky', 'publish', 35);

#
# com_login
#

/* TABLE */

/* 100 MOLAJO_CONFIG_OPTION_ID_TABLE */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 100, '', '', 0),
('com_login', 100, '__dummy', '__dummy', 1);

/** MVC */

/* CONTROLLER TASKS */

/* 1100 MOLAJO_CONFIG_OPTION_ID_TASK_TO_CONTROLLER */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 1100, '', '', 0),
('com_login', 1100, 'display', 'display', 3);

INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 1100, 'login', 'login', 28),
('com_login', 1100, 'logout', 'login', 29);

/* 1100 MOLAJO_CONFIG_OPTION_ID_TASK_TO_CONTROLLER +application id */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 1101, '', '', 0),
('com_login', 1101, 'display', 'display', 3);

INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 1101, 'login', 'login', 28),
('com_login', 1101, 'logout', 'login', 29);

/* VIEWS */

/* 2000 MOLAJO_CONFIG_OPTION_ID_VIEWS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 2000, '', '', 0),
('com_login', 2000, 'display', 'display', 1);

/* 2100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_VIEW */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 2100, '', '', 0),
('com_login', 2100, 'display', 'display', 1);

/* 2000 MOLAJO_CONFIG_OPTION_ID_VIEWS +application id **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 2001, '', '', 0),
('com_login', 2001, 'display', 'display', 1);

/* 2100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_VIEW +application id **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 2101, '', '', 0),
('com_login', 2101, 'display', 'display', 1);

/* VIEW LAYOUTS */

/* 3000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_LAYOUTS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 3000, '', '', 0),
('com_login', 3000, 'login', 'login', 1);

/* 3100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 3100, '', '', 0),
('com_login', 3100, 'login', 'login', 1);

/* 3000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_LAYOUTS +application id **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 3001, '', '', 0),
('com_login', 3001, 'admin_login', 'admin_login', 1);

/* 3100 MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS +application id **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 3101, '', '', 0),
('com_login', 3101, 'admin_login', 'admin_login', 1);

/* VIEW FORMATS */

/* 4000 MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_FORMATS */
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 4000, '', '', 0),
('com_login', 4000, 'html', 'html', 1),
('com_login', 4001, 'html', 'html', 1);

/* MODELS */

/* 5000 MOLAJO_CONFIG_OPTION_ID_MODEL +application id */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 5000, '', '', 0),
('com_login', 5000, 'dummy', 'dummy', 1);

/* 5000 MOLAJO_CONFIG_OPTION_ID_MODEL +application id */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 5001, '', '', 0),
('com_login', 5001, 'dummy', 'dummy', 1);

/* 6000 MOLAJO_CONFIG_OPTION_ID_PLUGIN_TYPE */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 6000, '', '', 0),
('com_login', 6000, 'user', 'user', 1);

/** ACL Component Information */

/** 10000 MOLAJO_CONFIG_OPTION_ID_ACL_IMPLEMENTATION **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 10000, '', '', 0),
('com_login', 10000, 'core', 'Core ACL Implementation', 1);

/** 10100 MOLAJO_CONFIG_OPTION_ID_ACL_ITEM_TESTS **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 10100, '', '', 0),
('com_login', 10100, 'view', 'view', 1);

/** 10000 MOLAJO_CONFIG_OPTION_ID_TASK_ACL_METHODS **/
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_login', 10200, '', '', 0),
('com_login', 10200, 'login', 'login', 15),
('com_login', 10200, 'logout', 'logout', 16);

/* ARTICLES */

/* 100 MOLAJO_CONFIG_OPTION_ID_TABLE */;
INSERT INTO `molajo_configuration` (`component_option`, `option_id`, `option_value`, `option_value_literal`, `ordering`) VALUES
('com_articles', 100, '', '', 0),
('com_articles', 100, '__articles', '__articles', 1);
