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
  `enabled`, `title`,`protected`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`,
  `extension_type_id`, `created_datetime`, `asset_id`, `extension_id`, `language`, `ordering`)
  SELECT 1, `name`, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1,
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
    ('en-GB', 2, 'English (UK)', 1),
    ('en-US', 2, 'English (UK)', 1);

INSERT INTO `molajo_extension_instances` (
  `enabled`, `title`,`protected`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`,
  `extension_type_id`, `created_datetime`, `asset_id`, `extension_id`, `language`, `ordering`)
  SELECT 1, `name`, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1,
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
  `enabled`, `title`,`protected`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`,
  `extension_type_id`, `created_datetime`, `asset_id`, `extension_id`, `language`, `ordering`)
  SELECT 1, `name`, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1,
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
  `enabled`, `title`,`protected`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`,
  `extension_type_id`, `created_datetime`, `asset_id`, `extension_id`, `language`, `ordering`)
  SELECT 1, `name`, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1,
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
  `enabled`, `title`,`protected`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`,
  `extension_type_id`, `created_datetime`, `asset_id`, `extension_id`, `language`, `ordering`)
  SELECT 1, `name`, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1,
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
  `enabled`, `title`,`protected`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`,
  `extension_type_id`, `created_datetime`, `asset_id`, `extension_id`, `language`, `ordering`)
  SELECT 1, `name`, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1,
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
  `enabled`, `title`,`protected`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`,
  `extension_type_id`, `created_datetime`, `asset_id`, `extension_id`, `language`, `ordering`)
  SELECT 1, `name`, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1,
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

ALTER TABLE `molajo_extension_instances`
  ADD INDEX `asset_id_UNIQUE` (asset_id)

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
  `enabled`, `extension_id`, `ordering`, `title`, `alias`, `menu_item_path`, `menu_item_link`,
  `protected`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`,
  `extension_type_id`, `created_datetime`, `asset_id`, `language`)
  VALUES
    (1, 1000, 1, 'Home', 'home', '', 'index.php?option=com_dashboard', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),

    (1, 1010, 1, 'Configure', 'configure', 'configure', 'index.php?option=com_dashboard&type=configure', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1010, 2, 'Access', 'access', 'access', 'index.php?option=com_dashboard&type=access', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1010, 3, 'Create', 'create', 'create', 'index.php?option=com_dashboard&type=create', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1010, 4, 'Build', 'build', 'build', 'index.php?option=com_dashboard&type=build', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1010, 5, 'Search', 'search', 'search', 'index.php?option=com_dashboard&type=search', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),

    (1, 1020, 1, 'Profile', 'profile', 'configure/profile', 'index.php?option=com_profile', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1020, 2, 'System', 'system', 'configure/system', 'index.php?option=com_config',1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1020, 3, 'Checkin', 'checkin', 'configure/checkin', 'index.php?option=com_checkin', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1020, 4, 'Cache', 'cache', 'configure/cache', 'index.php?option=com_cache', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1020, 5, 'Backup', 'backup', 'configure/backup', 'index.php?option=com_backup', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1020, 6, 'Redirects', 'redirects', 'configure/redirects', 'index.php?option=com_redirects', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),

    (1, 1030, 1, 'Users', 'users', 'access/users', 'index.php?option=com_users', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1030, 2, 'Groups', 'groups', 'access/groups', 'index.php?option=com_groups', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1030, 3, 'Permissions', 'permissions', 'access/permissions', 'index.php?option=com_permissions', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1030, 4, 'Messages', 'messages', 'access/messages', 'index.php?option=com_messages', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1030, 5, 'Activity', 'activity', 'access/activity', 'index.php?option=com_activity', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),

    (1, 1040, 1, 'Articles', 'articles', 'create/articles', 'index.php?option=com_articles', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1040, 2, 'Tags', 'tags', 'create/tags', 'index.php?option=com_tags', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1040, 3, 'Comments', 'comments', 'create/comments', 'index.php?option=com_comments', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1040, 4, 'Media', 'media', 'create/media', 'index.php?option=com_media', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1040, 5, 'Categories', 'categories', 'create/categories', 'index.php?option=com_categories', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),

    (1, 1050, 1, 'Extensions', 'extensions', 'build/extensions', 'index.php?option=com_extensions', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1050, 2, 'Languages', 'languages', 'build/languages', 'index.php?option=com_languages', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1050, 3, 'Layouts', 'layouts', 'build/layouts', 'index.php?option=com_layouts', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1050, 4, 'Modules', 'modules', 'build/modules', 'index.php?option=com_modules', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1050, 5, 'Plugins', 'plugins', 'build/plugins', 'index.php?option=com_plugins', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1050, 6, 'Templates', 'templates', 'build/templates', 'index.php?option=com_templates', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),

    (1, 1060, 1, 'Home', 'home', '', 'index.php?option=com_articles', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1060, 2, 'New Article', 'new-article', 'new-article', 'index.php?option=com_articles&view=article&layout=edit',  1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1060, 3, 'Article', 'article', 'article', 'index.php?option=com_articles&view=articles&layout=item&id=5',  1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1060, 4, 'Blog', 'blog', 'blog', 'index.php?option=com_articles&view=articles&layout=items&catid=2',  1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1060, 5, 'List', 'list', 'list', 'index.php?option=com_articles&view=articles&layout=table&catid=2',  1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1060, 6, 'Table', 'table', 'table', 'index.php?option=com_articles&type=search', 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1060, 7, 'Login', 'login', 'login', 'index.php?option=com_users&view=login',  1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB'),
    (1, 1060, 8, 'Search', 'search', 'search', 'index.php?option=com_search&type=search',  1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 5, '2011-11-01 00:00:00', 0, 'en-GB');

INSERT INTO `molajo_assets` (`title`, `source_table_id`, `source_id`, `path`, `link`, `view_group_id`, `language`, `translation_of_id`)
  SELECT `title`, 4, `id`, CONCAT('extensions/menuitem/', `id`), CONCAT('index.php?option=com_extensions&view=menuitem&id=', `id`), 5, 'en-GB', NULL
    FROM `molajo_extension_instances`
    WHERE `extension_type_id` = 5

UPDATE `molajo_extension_instances` a,
    `molajo_assets` b
  SET a.asset_id = b.id
WHERE a.id = b.source_id
  AND a.extension_type_id = 5

/** Administrator */
INSERT INTO `molajo_users` (`id`, `username`, `full_name`, `first_name`, `last_name`, `content_text`, `email`, `password`, `block`, `activated`, `send_email`, `register_datetime`, `last_visit_datetime`, `parameters`, `custom_fields`, `asset_id`) VALUES ('42', 'admin',  'Administrator',  '',  '',  '',  'admin@example.com',  'admin',  '0',  '1',  '0',  '2011-11-01 00:00:00',  '0000-00-00 00:00:00', NULL ,  '',  '2001');
INSERT INTO `molajo_user_applications` (`userid`, `application_id`) VALUES (42, 1), (42, 2), (42, 3);
INSERT INTO `molajo_user_groups` (`user_id`, `group_id`) VALUES (42, 3), (42, 4);

/** Registered User */
INSERT INTO `molajo_users` (`id`, `username`, `full_name`, `first_name`, `last_name`, `content_text`, `email`, `password`, `block`, `activated`, `send_email`, `register_datetime`, `last_visit_datetime`, `parameters`, `custom_fields`, `asset_id`) VALUES ('100', 'mark', 'Mark Robinson', 'Mark', 'Robinson', '<p>Great guy who sells insurance and coaches Little League.</p>', 'mark.robinson@example.com', 'mark', '0', '1', '0', '2011-11-02 17:45:17', '0000-00-00 00:00:00', NULL, '{"favorite_color":"red","nickname":"Fred","claim_to_fame":"No search results for Mark on Google."}', '2000');
INSERT INTO `molajo_user_applications` (`user_id`, `application_id`) VALUES (100, 1), (100, 3);
INSERT INTO `molajo_user_groups` (`user_id`, `group_id`) VALUES (100, 3);

/* User View Group */
INSERT INTO `molajo_user_view_groups`
  (`user_id`, `view_group_id`)
  SELECT DISTINCT a.`user_id`, b.`group_id`
    FROM `molajo_user_groups` a,
      `molajo_group_view_groups` b
    WHERE a.group_id = b.group_id

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
          'com_layouts',
          'com_login',
          'com_media',
          'com_search')

INSERT INTO `molajo_application_extensions`
  (`application_id`, `extension_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.extension_id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 1
      AND b.id = 2
      AND a.title IN
        ('com_admin',
          'com_articles',
          'com_categories',
          'com_config',
          'com_dashboard',
          'com_extensions',
          'com_installer',
          'com_layouts',
          'com_login',
          'com_media',
          'com_menus',
          'com_modules',
          'com_plugins',
          'com_redirect',
          'com_search',
          'com_templates',
          'com_admin',
          'com_users')

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
      AND b.id IN (1, 2);

INSERT INTO `molajo_application_extensions`
  (`application_id`, `extension_id`, `extension_instance_id`)
  SELECT b.id, a.extension_id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 5
      AND NOT (a.extension_id = 1060)
      AND b.id = 3;

/** 6. modules */
INSERT INTO `molajo_application_extensions`
  (`application_id`, `extension_id`, `extension_instance_id`)
  SELECT b.id, a.extension_id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE extension_type_id = 6
      AND b.id IN (1, 2)
      AND a.title IN
          ('mod_breadcrumbs',
          'mod_content',
          'mod_custom',
          'mod_feed',
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
      AND b.id = 3
      AND a.title IN
        ('mod_content',
        'mod_custom',
        'mod_feed',
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

/** default templates */
UPDATE `molajo_applications` a,
    `molajo_extension_instances` b
  SET a.default_template_extension_id = b.id
WHERE b.extension_type_id = 9
  AND b.title = 'construct'
  AND a.id IN (1, 3);

UPDATE `molajo_applications` a,
    `molajo_extension_instances` b
  SET a.default_template_extension_id = b.id
WHERE b.extension_type_id = 9
  AND b.title = 'molajito'
  AND a.id = 2;

/** still need to populate extension_usage */