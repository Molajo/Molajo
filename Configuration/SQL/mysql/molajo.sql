#
# Molajo System Data
#

#
# Action Types
#
INSERT INTO `molajo_action_types` (`id` , `title` , `protected`)
  VALUES
    (1, 'login', 1),
    (2, 'create', 1),
    (3, 'view', 1),
    (4, 'edit', 1),
    (5, 'publish', 1),
    (6, 'delete', 1),
    (7, 'administer', 1);

#
# Catalog Types
#
INSERT INTO `molajo_catalog_types` (`id`, `title`, `protected`, `source_table`, `component_option`)
  VALUES
  (1, 'System', 1, '', ''),

  (10, 'Sites', 1, '__sites', 'sites'),

  (50, 'Applications', 1, '__applications', 'applications'),

  (100, 'System', 1, '__content', 'groups'),
  (110, 'Normal', 1, '__content', 'groups'),
  (120, 'User', 1, '__content', 'groups'),
  (130, 'Friend', 1, '__content', 'groups'),

  (500, 'Users', 1, '__users', 'users'),

  (1050, 'Components', 1, '__extension_instances', 'extensions'),
  (1100, 'Languages', 1, '__extension_instances', 'extensions'),
  (1150, 'Page View', 1, '__extension_instances', 'extensions'),
  (1200, 'Template View', 1, '__extension_instances', 'extensions'),
  (1250, 'Wrap View', 1, '__extension_instances', 'extensions'),
  (1300, 'Menus', 1, '__extension_instances', 'extensions'),
  (1350, 'Modules', 1, '__extension_instances', 'extensions'),
  (1450, 'Triggers', 1, '__extension_instances', 'extensions'),
  (1500, 'Themes', 1, '__extension_instances', 'extensions'),

  (2000, 'Component', 1, '__content', 'menus'),
  (2100, 'Link', 1, '__content', 'menus'),
  (2200, 'Module', 1, '__content', 'menus'),
  (2300, 'Separator', 1, '__content', 'menus'),

  (3000, 'List', 0, '__content', 'categories'),
  (3500, 'Tags', 0, '__content', 'categories'),

  (10000, 'Articles', 0, '__content', 'articles'),
  (30000, 'Comments', 0, '__content', 'comments'),
  (20000, 'Contacts', 0, '__content', 'contacts'),
  (40000, 'Dashboard', 0, '', 'dashboard'),
  (50000, 'Media', 0, '__content', 'media'),

#
# EXTENSION SITES
#
INSERT INTO `molajo_extension_sites`
 (`id`, `name`, `enabled`, `location`, `customfields`, `parameters`)
  VALUES
    (1, 'Molajo Core', 1, 'http://update.molajo.org/core.xml', '', ''),
    (2, 'Molajo Directory', 1, 'http://update.molajo.org/directory.xml', '', '');

#
# EXTENSIONS
#

# Components
INSERT INTO `molajo_extensions`
  (`id`,  `extension_site_id`,  `name`, `catalog_type_id`)
  VALUES
    (1, 1, 'applications', '', 1050),
    (2, 1, 'articles', '', 1050),
    (3, 1, 'catalog', '', 1050),
    (4, 1, 'categories', '', 1050),
    (5, 1, 'comments', '', 1050),
    (6, 1, 'contacts', '''', 1050),
    (7, 1, 'dashboard', '', 1050),
    (8, 1, 'extensions', '', 1050),
    (9, 1, 'groups', '', 1050),
    (10, 1, 'installer', '', 1050),
    (11, 1, 'login', '''', 1050),
    (12, 1, 'media', '', 1050),
    (13, 1, 'profile', '''', 1050),
    (14, 1, 'search', '', 1050),
    (15, 1, 'users', '', 1050),
    (16, 1, 'site', '', 1050),
    (17, 1, 'redirects', '', 1050);

INSERT INTO `molajo_extensions`
  (`id`,  `extension_site_id`,  `name`, `subtype`, `catalog_type_id`)
  VALUES
    (0, 1, 'core', '', 1050);
UPDATE `molajo_extensions`
  SET `id` = 0
  WHERE `name` = 'core';

INSERT INTO `molajo_extension_instances`
  (`id`, `extension_id`, `catalog_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `parameters`,
    `position`, `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `id`, `catalog_type_id`,
        `name`, `subtype`, '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `catalog_type_id` = 1050;
UPDATE `molajo_extension_instances`
  SET `id` = 0
  WHERE `title` = 'core';

# LANGUAGES
INSERT INTO `molajo_extensions`
  (`id`,  `extension_site_id`,  `name`, `subtype`, `catalog_type_id`)
  VALUES
    (18, 1, 'English (UK)', 'en-GB', 1100);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `catalog_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `parameters`,
    `position`, `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `catalog_type_id`,
        `name`, `subtype`, '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `catalog_type_id` = 1100;

# VIEWS
INSERT INTO `molajo_extensions`
    (`name`,`catalog_type_id`, `subtype`, `extension_site_id`)
  VALUES
    ('Dashboard', 1150, 'Template', 1),
    ('DashboardModule', 1150, 'Template', 1),

    ('default', 1150, 'Template', 1),

    ('DocumentDefer', 1150, 'Template', 1),
    ('DocumentHead', 1150, 'Template', 1),

    ('static', 1150, 'Template', 1),
    ('edit', 1150, 'Template', 1),
    ('edit-editor', 1150, 'Template', 1),
    ('edit-access-control', 1150, 'Template', 1),
    ('edit-custom-fields', 1150, 'Template', 1),
    ('edit-metadata', 1150, 'Template', 1),
    ('edit-parameters', 1150, 'Template', 1),
    ('edit-title', 1150, 'Template', 1),
    ('edit-toolbar', 1150, 'Template', 1),

    ('Grid', 1150, 'Template', 1),
    ('GridBatch', 1150, 'Template', 1),
    ('GridFilters', 1150, 'Template', 1),
    ('GridPagination', 1150, 'Template', 1),
    ('GridSubmenu', 1150, 'Template', 1),
    ('GridTable', 1150, 'Template', 1),
    ('GridTitle', 1150, 'Template', 1),
    ('GridToolbar', 1150, 'Template', 1),

    ('PageHeader', 1150, 'Template', 1),
    ('PageFooter', 1150, 'Template', 1),

    ('SystemErrors', 1150, 'Template', 1),
    ('SystemMessages', 1150, 'Template', 1),

    ('button', 1150, 'formfields', 1),
    ('colorpicker', 1150, 'formfields', 1),
    ('input', 1150, 'formfields', 1),
    ('media', 1150, 'formfields', 1),
    ('number', 1150, 'formfields', 1),
    ('option', 1150, 'formfields', 1),
    ('rules', 1150, 'formfields', 1),
    ('spacer', 1150, 'formfields', 1),
    ('textarea', 1150, 'formfields', 1),
    ('user', 1150, 'formfields', 1),

    ('default', 1150, 'Page', 1),
    ('SystemError', 1150, 'Page', 1),
    ('SystemOffline', 1150, 'Page', 1),

    ('Article', 1150, 'Wrap', 1),
    ('Aside', 1150, 'Wrap', 1),
    ('Default', 1150, 'Wrap', 1),
    ('Div', 1150, 'Wrap', 1),
    ('Footer', 1150, 'Wrap', 1),
    ('Header', 1150, 'Wrap', 1),
    ('Hgroup', 1150, 'Wrap', 1),
    ('Nav', 1150, 'Wrap', 1),
    ('None', 1150, 'Wrap', 1),
    ('Section', 1150, 'Wrap', 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `catalog_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `parameters`,
    `position`, `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `catalog_type_id`,
        `name`, `subtype`, '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `catalog_type_id` = 1150;

# LISTENERS
INSERT INTO `molajo_extensions`
    (`name`, `catalog_type_id`, `extension_site_id`)
  VALUES
    ('example', 1450, 'acl', 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `catalog_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `parameters`,
    `position`, `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `catalog_type_id`,
        `name`, `subtype`, '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `catalog_type_id` = 1450;

# THEMES
INSERT INTO `molajo_extensions`
    (`name`, `catalog_type_id`, `extension_site_id`)
  VALUES
    ('cleanslate', 1500, 1),
    ('molajito', 1500, 1),
    ('system', 1500, 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `catalog_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `parameters`,
    `position`, `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `catalog_type_id`,
        `name`, `subtype`, '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `catalog_type_id` = 1500;

## MENU
INSERT INTO `molajo_extensions`
    (`name`, `catalog_type_id`, `extension_site_id`)
  VALUES
    ('Administrator Menu', 1300, 1),
    ('Main Menu', 1300, 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `catalog_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `parameters`,
    `position`, `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `catalog_type_id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', '{}',
        '', 'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `catalog_type_id` = 1300;

# Modules
INSERT INTO `molajo_extensions`
    (`name`, `catalog_type_id`, `extension_site_id`)
  VALUES
    ('catalogwidget', 1350, 1),
    ('aclwidget', 1350, 1),
    ('breadcrumbs', 1350, 1),
    ('categorywidget', 1350, 1),
    ('content', 1350, 1),
    ('custom', 1350, 1),
    ('dashboard', 1350, 1),
    ('DashboardModule', 1350, 1),
    ('default', 1350, 1),
    ('DocumentDefer', 1350, 1),
    ('DocumentHead', 1350, 1),
    ('static', 1350, 1),
    ('PageHeader', 1350, 1),
    ('PageFooter', 1350, 1),
    ('SystemErrors', 1350, 1),
    ('SystemMessages', 1350, 1);

INSERT INTO `molajo_extension_instances`
  (`extension_id`, `catalog_type_id`,
    `title`, `subtitle`, `alias`, `content_text`,
    `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`,
    `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`,
    `position`, `customfields`, `parameters`,
    `language`, `translation_of_id`, `ordering`)
  SELECT `id`, `catalog_type_id`,
        `name`, '', '', '',
        1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0,
        '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0,
        SUBSTRING(`name`, 5, 99), '{}', '{}',
        'en-GB', 0, `id`
    FROM `molajo_extensions`
    WHERE `catalog_type_id` = 1350;

#
# SITES
#
INSERT INTO `molajo_sites`
  (`id`, `catalog_type_id`, `name`, `path`, `base_url`, `description`, `parameters`, `customfields`)
  VALUES
    (1, 10, 'Molajo', '', '', 'Primary Site', '{}', '{}');

#
# APPLICATIONS
# Note: after menus are defined, update applications for home
#
INSERT INTO `molajo_applications`
  (`id`, `catalog_type_id`, `name`, `path`, `description`, `parameters`, `customfields`)
  VALUES
    (1, 50, 'site', '', 'Primary application for site visitors', '{}', '{}'),
    (2, 50, 'administrator', 'administrator', 'Administrative site area for site construction', '{}', '{}');

#
# CONTENT ROOT ID
#
SET @id = (SELECT MIN(id) FROM `molajo_extension_instances` WHERE `title` = 'System');

INSERT INTO `molajo_content`
  (`id`, `title`, `subtitle`, `alias`, `content_text`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
  `extension_instance_id`, `catalog_type_id`,
  `version`, `root`, `parent_id`, `lft`, `rgt`, `lvl`, `language`, `ordering`)
  VALUES
    (1, 'ROOT', '', 'root', '<p>Root Content</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00',
    @id, 1000,
    0, 0, 0, 0, 0, 0, 'en-GB', 1);

UPDATE `molajo_content`
  SET id = 0
  WHERE `title` = 'ROOT';

#
# USERS AND GROUPS
#
SET @id = (SELECT id FROM `molajo_extension_instances` WHERE `title` = 'groups');

INSERT INTO `molajo_content`
  (`id`, `extension_instance_id`, `title`, `path`, `alias`, `content_text`, `catalog_type_id`,
   `root`, `parent_id`, `lft`, `rgt`, `lvl`, `ordering`,
   `protected`, `featured`, `stickied`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `customfields`, `parameters`, `language`, `translation_of_id`)
   VALUES
      (1, @id, 'Public', 'groups', 'public', 'All visitors regardless of authentication status', 100, 0, 0, 1, 2, 1, 1, 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '{}', '{}', 'en-GB', 0),
      (2, @id, 'Guest', 'groups', 'guest', 'Visitors not authenticated', 100, 0, 0, 3, 4, 1, 2, 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '{}', '{}', 'en-GB', 0),
      (3, @id, 'Registered', 'groups', 'registered', 'Authentication visitors', 100, 0, 0, 5, 6, 1, 3, 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '{}', '{}', 'en-GB', 0),
      (4, @id, 'Administrator', 'groups', 'administrator', 'System Administrator', 100, 0, 0, 7, 8, 1, 4, 1, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '{}', '{}', 'en-GB', 0);

INSERT INTO `molajo_view_groups`
  (`id`, `view_group_name_list`, `view_group_id_list`)
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

##  Administrator
INSERT INTO `molajo_users` (`id`, `catalog_type_id`, `username`, `first_name`, `last_name`, `content_text`, `email`, `password`, `block`, `activation`, `send_email`, `register_datetime`, `last_visit_datetime`, `parameters`, `customfields`) VALUES ('42', 500, 'admin',  'Administrator',  '',  '',  'admin@example.com',  'admin',  '0',  '1',  '0',  '2011-11-11 11:11:11',  '0000-00-00 00:00:00', '{}', '{}');
INSERT INTO `molajo_user_applications` (`user_id`, `application_id`) VALUES (42, 1), (42, 2);
SET @id = (SELECT id FROM `molajo_extension_instances` WHERE `title` = 'groups');
INSERT INTO `molajo_content`
  (`extension_instance_id`, `title`, `path`, `alias`, `content_text`, `catalog_type_id`,
   `parent_id`, `lft`, `rgt`, `lvl`, `ordering`,
   `protected`, `featured`, `stickied`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `customfields`, `parameters`, `language`, `translation_of_id`)
  SELECT @id, CONCAT(`first_name`, ' ', `last_name`), 'groups', `username`, '', 120, `id`, 0, 0, 0, 1, 0, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '{}', '{}', 'en-GB', 0
    FROM `molajo_users` WHERE username = 'admin';
INSERT INTO `molajo_user_groups` (`user_id`, `group_id`) VALUES (42, 3), (42, 4);

##  Sample Registered User
INSERT INTO `molajo_users` (`id`, `catalog_type_id`, `username`, `first_name`, `last_name`, `content_text`, `email`, `password`, `block`, `activation`, `send_email`, `register_datetime`, `last_visit_datetime`, `parameters`, `customfields`) VALUES ('100', 500, 'mark', 'Mark', 'Robinson', '<p>Great guy who sells insurance and coaches Little League.</p>', 'mark.robinson@example.com', 'mark', '0', '1', '0', '2011-11-02 17:45:17', '0000-00-00 00:00:00', '{}', '{"favorite_color":"red","nickname":"Fred","claim_to_fame":"No search results for Mark on Google."}');
INSERT INTO `molajo_user_applications` (`user_id`, `application_id`) VALUES (100, 1);
SET @id = (SELECT id FROM `molajo_extension_instances` WHERE `title` = 'groups');
INSERT INTO `molajo_content`
  (`extension_instance_id`, `title`, `path`, `alias`, `content_text`, `catalog_type_id`,
   `parent_id`, `lft`, `rgt`, `lvl`, `ordering`,
   `protected`, `featured`, `stickied`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `customfields`, `parameters`, `language`, `translation_of_id`)
  SELECT @id, CONCAT(`first_name`, ' ', `last_name`), 'groups', `username`, '', 120, `id`, 0, 0, 0, 1, 0, 0, 0, 1, '2011-11-11 11:11:11', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-11 11:11:11', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '{}', '{}', 'en-GB', 0
    FROM `molajo_users` WHERE username = 'mark';
INSERT INTO `molajo_user_groups` (`user_id`, `group_id`) VALUES (100, 3);

##  Authorize Users for their own group
INSERT INTO `molajo_user_groups` (`user_id`, `group_id`) SELECT parent_id, id FROM `molajo_content` WHERE catalog_type_id = 120;

##  user private view groups
INSERT INTO `molajo_view_groups`
  (`view_group_name_list`, `view_group_id_list`)
  SELECT 'Private', `id`
    FROM `molajo_content`
   WHERE `catalog_type_id` = 120;

##  user private view group permission
INSERT INTO `molajo_group_view_groups` ( `group_id` , `view_group_id` )
  SELECT a.`id`, b.`id`
  FROM `molajo_content` a,
    `molajo_view_groups` b
  WHERE b.`view_group_id_list` = a.`id`
    AND a.id > 5;

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
    (1, 2);

##  1. components
INSERT INTO `molajo_application_extension_instances`
  (`application_id`, `extension_instance_id`)
  SELECT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE b.id = 1
      AND a.catalog_type_id = 1050
       AND a.title IN
        ('articles',
          'comments',
          'contacts',
          'login',
          'media',
          'profile',
          'search');

INSERT INTO `molajo_application_extension_instances`
  (`application_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE b.id = 2
      AND a.catalog_type_id = 1050;

##  2. language
INSERT INTO `molajo_application_extension_instances`
  (`application_id`, `extension_instance_id`)
  SELECT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE a.catalog_type_id = 1100;

##  3. views
INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE a.catalog_type_id = 1150;

##  5. menus
INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE a.catalog_type_id = 1300
      AND NOT(a.title = 'Admin')
      AND b.id = 1;

INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE a.catalog_type_id = 1300
      AND a.title = 'Admin'
      AND b.id = 2;

##  6. modules
INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE a.catalog_type_id = 1350
      AND b.id = 1;

INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE a.catalog_type_id = 1350
      AND b.id = 2;

##  8. triggers
INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE a.catalog_type_id = 1450;

##  9. themes
INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE a.catalog_type_id = 1500
      AND a.title IN ('cleanslate', 'system')
      AND b.id = 1;

INSERT INTO `molajo_application_extension_instances`
 (`application_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_applications` b
    WHERE a.catalog_type_id = 1500
      AND a.title IN ('molajito', 'system')
      AND b.id IN (2);

##  site extension instances
INSERT INTO `molajo_site_extension_instances`
 (`site_id`, `extension_instance_id`)
  SELECT DISTINCT b.id, a.id
    FROM `molajo_extension_instances` a,
     `molajo_sites` b;

##
## Menu Items
##

## ## Admin: Root
INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`,
    `parameters`, `metadata`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 101, 101, 'Root', '', '', 101, 0, 0, 65, 0,
        `id`, 2000,
         '{}', '{}',
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

## ## Admin: Content
INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`,
    `parameters`, `metadata`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 102, 102, 'Content', '', 'content', 101, 101, 1, 12, 1,
        `id`, 2000,
        CONCAT ('{"extension_instance_id":"7","section":"content","id":"","category_id":"","theme_id":"","page_view_id":"","page_view_css_id":"","page_view_css_class":"","template_view_id":"19","template_view_css_id":"","template_view_css_class":"","wrap_view_id":"67","wrap_view_css_id":"","wrap_view_css_class":"","cache":"1","cache_time":"900"}'),
        '{"title":"Content", "description":"Dashboard", "keywords":"dashboard", "robots":"follow, index", "author":"Author Name", "content_rights":"CC"}',
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';
/**
INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`,
    `parameters`, `metadata`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 103, 103, 'Articles', 'content', 'articles', 101, 2, 2, 3, 2,
        `id`, 2000,
        CONCAT ('{"extension_instance_id":"2","id":"","category_id":"","theme_id":"","page_view_id":"","page_view_css_id":"","page_view_css_class":"","template_view_id":"128","template_view_css_id":"","template_view_css_class":"","wrap_view_id":"33","wrap_view_css_id":"","wrap_view_css_class":"","cache":"1","cache_time":"900"}'),
        '{"title":"Content", "description":"Dashboard", "keywords":"dashboard", "robots":"follow, index", "author":"Author Name", "content_rights":"CC"}',
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 104, 104, 'Contacts', 'content', 'contacts', 101, 2, 4, 5, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 105, 105, 'Comments', 'content', 'comments', 101, 2, 6, 7, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 107, 107, 'Media', 'content', 'media', 101, 2, 10, 11, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

## ## ## Admin: Access

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 108, 108, 'Access', '', 'access', 101, 1, 13, 22, 1,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 109, 109, 'Profile', 'access', 'profile', 101, 8, 14, 15, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 110, 110, 'Users', 'access', 'users', 101, 8, 16, 17, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 111, 111, 'Groups', 'access', 'groups', 101, 8, 18, 19, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 112, 112, 'Catalog', 'access', 'catalog', 101, 8, 20, 21, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

## ## Admin: Build
INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 113, 113, 'Build', '', 'build', 101, 1, 23, 34, 1,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 114, 114, 'Categories', 'build', 'categories', 101, 13, 24, 25, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 115, 115, 'Menus', 'build', 'menus', 101, 13, 26, 27, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 116, 116, 'Menu Items', 'build', 'menus', 101, 13, 28, 29, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 117, 117, 'Modules', 'build', 'modules', 101, 13, 30, 31, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 118, 118, 'Themes', 'build', 'themes', 101, 13, 32, 33, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

## ## ## Admin: Configure

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `paCrent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 119, 119, 'Configure', '', 'configure', 101, 1, 35, 48, 1,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 120, 120, 'Site', 'configure', 'sites', 101, 19, 36, 37, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 121, 121, 'Applications', 'configure', 'applications', 101, 19, 38, 39, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 122, 122, 'Checkin', 'configure', 'checkin', 101, 19, 40, 41, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 123, 123, 'Clean Cache', 'configure', 'cleancache', 101, 19, 42, 43, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 124, 124, 'Redirects', 'configure', 'redirects', 101, 19, 44, 45, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 125, 125, 'Triggers', 'configure', 'triggers', 101, 19, 46, 47, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

## ## Admin: Extend
INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 126, 126, 'Extend', '', 'extend', 101, 1, 49, 56, 1,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 127, 127, 'Install', 'extend', 'install', 101, 26, 50, 51, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 128, 128, 'Upgrade', 'extend', 'Upgrade', 101, 26, 52, 53, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 129, 129, 'Uninstall', 'extend', 'uninstall', 101, 26, 54, 55, 2,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

## ## Admin: Search
INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 130, 130, 'Search', '', 'search', 101, 1, 57, 58, 1,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

## ## Site: Main Menu
INSERT INTO `molajo_content`
  (`id`, `ordering`, `title`, `path`, `alias`, `root`, `parent_id`,`lft`, `rgt`, `lvl`,
    `extension_instance_id`, `catalog_type_id`, `parameters`,
    `subtitle`, `content_text`, `protected`, `featured`, `stickied`,
    `status`, `start_publishing_datetime`, `stop_publishing_datetime`,
    `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`,
    `checked_out_datetime`, `checked_out_by`, `customfields`, `language`, `translation_of_id`)
  SELECT 131, 131, 'Home', '', 'home', 101, 1, 59, 60, 1,
        `id`, 2000, CONCAT('{"request":"', `id`, '","page_view_title":"","page_view_id":"","page_view_class_suffix":"","category_id":"","author":"","number_of_items":"10","featured":"0","order_by":"1","pagination":"","view":"","wrap":"div","view_class_suffix":"","link_title":"","link_css":"","link_image":"","link_include_text":"","link_target":"","cache":"1","cache_time":"900","spam_protection":""}'),
        '', '', 1, 0, 0,
        1, '2011-11-11 11:11:11', '0000-00-00 00:00:00',
        1, 0, 0, '2011-11-11 11:11:11', 0, '2011-11-11 11:11:11', 0,
        '0000-00-00 00:00:00', 0, '{}', 'en-GB', 0
    FROM `molajo_extension_instances`
    WHERE `catalog_type_id` = 1300 AND `title` = 'Administrator Menu';

# Menu Item Home
UPDATE `molajo_content`
   SET `protected` = 1,
        `home` = 0;
*/
# Site Home
/*
todo: amy update configuration file
SET @id = (SELECT id FROM `molajo_content` WHERE `title` = 'Home' AND `catalog_type_id` = 2000);
UPDATE `molajo_content`
  SET `home` = 1
  WHERE `id` = @id;
UPDATE `molajo_applications`
  SET `home_menu_id` = @id
  WHERE `id` = 1;
*/
# Application Home
/*
SET @id = (SELECT id FROM `molajo_content` WHERE `title` = 'Content' AND `catalog_type_id` = 2000);
UPDATE `molajo_content`
  SET `home` = 1
  WHERE `id` = @id;
UPDATE `molajo_applications`
  SET `home_menu_id` = @id
  WHERE `id` = 2;
*/

#
# CATALOG
#

# Sites
INSERT INTO `molajo_catalog`
 (`catalog_type_id`, `source_id`, `routable`, `sef_request`,
 `request`, `request_option`, `request_model`, `redirect_to_id`,
 `view_group_id`, `primary_category_id`)
  SELECT `catalog_type_id`, `id`, false, `path`, '', '', '', 0, 1, 0
    FROM `molajo_sites`;

# Application
INSERT INTO `molajo_catalog`
 (`catalog_type_id`, `source_id`, `routable`, `sef_request`, `request`, `request_option`, `request_model`, `redirect_to_id`, `view_group_id`, `primary_category_id`)
  SELECT `catalog_type_id`, `id`, false, `path`, '', '', '', 0, 1, 0
    FROM  molajo_applications;

# Groups
INSERT INTO `molajo_catalog`
  (`catalog_type_id`, `source_id`, `routable`,
  `sef_request`, `request`, `request_option`, `request_model`,
  `redirect_to_id`, `view_group_id`, `primary_category_id`)
SELECT a.`catalog_type_id`, a.`id`, true,
    CONCAT('group/', a.`id`),
    CONCAT('index.php?option=groups&model=group&id=', a.`id`),
    'groups', 'group',
    0, 1, 0
    FROM `molajo_content` a,
        `molajo_catalog_types` b
    WHERE a.`catalog_type_id` = b.`id`
      AND a.`catalog_type_id` BETWEEN 100 AND 120 ;

# Extension Instances
INSERT INTO `molajo_catalog`
  (`catalog_type_id`, `source_id`, `routable`,
  `sef_request`, `request`, `request_option`, `request_model`,
  `redirect_to_id`, `view_group_id`, `primary_category_id`)
SELECT a.`catalog_type_id`, a.`id`, true,
    CONCAT(b.`component_option`, '/', LOWER(b.`title`), '/', a.`id`),
    CONCAT('index.php?option=extensions', '&model=', lower(b.`title`), '&id=', a.`id`),
    'extension', b.`title`, 0, 1, 0
    FROM `molajo_extension_instances` a,
        `molajo_catalog_types` b
    WHERE a.`catalog_type_id` = b.`id`;

# Menu Items
INSERT INTO `molajo_catalog`
  (`catalog_type_id`, `source_id`, `routable`,
  `sef_request`, `request`, `request_option`, `request_model`,
  `redirect_to_id`, `view_group_id`, `primary_category_id`)
  VALUES
  (2000, 102, true, 'content', 'index.php?option=dashboard&model=static', 'dashboard', 'static', 0, 1, 3),
  (10000, 130, true, 'content/articles', 'index.php?option=articles&model=articles', 'articles', 'articles', 0, 1, 3);

/**
  (2000, 103, 'Articles', 'index.php?option=articles', 'content/articles', 1, 'en-GB', 0, 0, 3),
  (2000, 104, 'Contacts', 'index.php?option=contacts', 'content/contacts', 1, 'en-GB', 0, 0, 3),
  (2000, 105, 'Comments', 'index.php?option=comments', 'content/comments', 1, 'en-GB', 0, 0, 3),
  (2000, 106, 'Views', 'index.php?option=views', 'content/views', 1, 'en-GB', 0, 0, 3),
  (2000, 107, 'Media', 'index.php?option=media', 'content/media', 1, 'en-GB', 0, 0, 3),

  (2000, 108, 'Access', 'index.php?option=dashboard&view=users', 'access', 1, 'en-GB', 0, 0, 3),
  (2000, 109, 'Profile', 'index.php?option=profile', 'access/profiles', 1, 'en-GB', 0, 0, 3),
  (2000, 110, 'Users', 'index.php?option=users', 'access/users', 1, 'en-GB', 0, 0, 3),
  (2000, 111, 'Groups', 'index.php?option=groups', 'access/groups', 1, 'en-GB', 0, 0, 3),
  (2000, 112, 'Catalog', 'index.php?option=catalog&view=users', 'access/catalog', 1, 'en-GB', 0, 0, 3),

  (2000, 113, 'Build', 'index.php?option=dashboard&view=build', 'build', 1, 'en-GB', 0, 0, 3),
  (2000, 114, 'Categories', 'index.php?option=categories', 'build/categories', 1, 'en-GB', 0, 0, 3),
  (2000, 115, 'Menus', 'index.php?option=extensions&view=menus', 'build/menus', 1, 'en-GB', 0, 0, 3),
  (2000, 116, 'Menu Items', 'index.php?option=extensions&view=menus', 'build/menus', 1, 'en-GB', 0, 0, 3),
  (2000, 117, 'Modules', 'index.php?option=extensions&view=modules', 'build/modules', 1, 'en-GB', 0, 0, 3),
  (2000, 118, 'Themes', 'index.php?option=extensions&view=themes', 'build/themes', 1, 'en-GB', 0, 0, 3),

  (2000, 119, 'Configure', 'index.php?option=dashboard&view=configure', 'configure', 1, 'en-GB', 0, 0, 3),
  (2000, 120, 'Site', 'index.php?option=extensions&view=sites', 'configure/sites', 1, 'en-GB', 0, 0, 3),
  (2000, 121, 'Applications', 'index.php?option=extensions&view=applications', 'configure/applications', 1, 'en-GB', 0, 0, 3),
  (2000, 122, 'Checkin', 'index.php?option=maintain&view=checkin', 'configure/checkin', 1, 'en-GB', 0, 0, 3),
  (2000, 123, 'Clean Cache', 'index.php?option=maintain&view=cleancache', 'configure/cleancache', 1, 'en-GB', 0, 0, 3),
  (2000, 124, 'Redirects', 'index.php?option=maintain&view=redirects', 'configure/redirects', 1, 'en-GB', 0, 0, 3),
  (2000, 125, 'Triggers', 'index.php?option=extensions&view=triggers', 'configure/triggers', 1, 'en-GB', 0, 0, 3),

  (2000, 126, 'Extend', 'index.php?option=dashboard&view=extend', 'extend', 1, 'en-GB', 0, 0, 3),
  (2000, 127, 'Create', 'index.php?option=installer&view=install', 'extend/install', 1, 'en-GB', 0, 0, 3),
  (2000, 128, 'Update', 'index.php?option=installer&view=upgrade', 'extend/upgrade', 1, 'en-GB', 0, 0, 3),
  (2000, 129, 'Uninstall', 'index.php?option=installer&view=uninstall', 'extend/uninstall', 1, 'en-GB', 0, 0, 3),

  (2000, 130, 'Search', 'index.php?option=search', 'search', 1, 'en-GB', 0, 0, 3),

  (2000, 131, 'Home', 'index.php?option=views', 'home', 1, 'en-GB', 0, 0, 1);
**/
# Catalog Categories

INSERT INTO `molajo_catalog_categories`
  (`catalog_id`, `category_id`)
  SELECT `id`, `primary_category_id`
    FROM `molajo_catalog`
    WHERE `primary_category_id` > 10;

# View Group Permissions
INSERT INTO `molajo_view_group_permissions`
  (`view_group_id`, `catalog_id`, `action_id`)
  SELECT DISTINCT `view_group_id`, `id` as catalog_id, 3 as `action_id`
    FROM `molajo_catalog`;

# Group Permissions (other than view)
# molajo_group_permissions;
