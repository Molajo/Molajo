#
# Table structure for table `#__temp_permissions`
#   Calculate assigned actions by asset id for groups
#

CREATE TABLE IF NOT EXISTS `#__temp_permissions` (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Permissions Primary Key',
  `group_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to #_groups.id',
  `asset_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to #__assets.id',
  `action_id` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Foreign Key to #__actions.id',
  PRIMARY KEY  (`id`)
)  DEFAULT CHARSET=utf8;

# groups
INSERT INTO `#__assets` SELECT DISTINCT `asset_id`, 'groups' FROM `#__groups`;
# administrator has full control of groups (no 1=login needed)
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT 1, a.asset_id, b.id FROM `#__groups` a, `#__actions` b WHERE b.id > 1;

# clients
INSERT INTO `#__assets` SELECT DISTINCT `asset_id`, 'clients' FROM `#__clients`;
# administrator has full control
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT 1, a.asset_id, b.id FROM `#__clients` a, `#__actions` b where b.id <> 3;
# load access (View Level Access) permissions
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT a.access, a.asset_id, b.id FROM `#__clients` a, `#__actions` b where b.id = 3;

# categories
INSERT INTO `#__assets` SELECT DISTINCT `asset_id`, 'categories' FROM `#__categories`;
# administrator has full control
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT 1, a.asset_id, b.id FROM `#__categories` a, `#__actions` b where b.id NOT IN (1, 3);
# load access (View Level Access) permissions
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT a.access, a.asset_id, b.id FROM `#__categories` a, `#__actions` b where b.id = 3;

# articles
INSERT INTO `#__assets` SELECT DISTINCT `asset_id`, 'articles' FROM `#__articles`;
# administrator has full control
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT 1, a.asset_id, b.id FROM `#__articles` a, `#__actions` b where b.id <> 1;
# load access (View Level Access) permissions
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT a.access, a.asset_id, b.id FROM `#__articles` a, `#__actions` b where b.id = 3;

# menus
INSERT INTO `#__assets` SELECT DISTINCT `asset_id`, 'menu' FROM `#__menu`;
# administrator has full control
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT 1, a.asset_id, b.id FROM `#__menu` a, `#__actions` b where b.id <> 1;
# load access (View Level Access) permissions
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT a.access, a.asset_id, b.id FROM `#__menu` a, `#__actions` b where b.id = 3;

# extensions
INSERT INTO `#__assets` SELECT DISTINCT `asset_id`, 'extensions' FROM `#__extensions`;
# administrator has full control
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT 1, a.asset_id, b.id FROM `#__extensions` a, `#__actions` b where b.id <> 1;
# load access (View Level Access) permissions
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT a.access, a.asset_id, b.id FROM `#__extensions` a, `#__actions` b where b.id = 3;

# modules
INSERT INTO `#__assets` SELECT DISTINCT asset_id, 'modules' FROM `#__modules`;
# administrator has full control
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT 1, a.asset_id, b.id FROM `#__modules` a, `#__actions` b where b.id NOT IN (1, 3);
# load access (View Level Access) permissions
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT a.access, a.asset_id, b.id FROM `#__modules` a, `#__actions` b where b.id = 3;

# users
INSERT INTO `#__assets` SELECT DISTINCT asset_id, 'users' FROM `#__users`;
# administrator has full control
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT 1, a.asset_id, b.id FROM `#__users` a, `#__actions` b where b.id NOT IN (1, 3);
# load access (View Level Access) permissions
INSERT INTO `#__temp_permissions` (`group_id`,`asset_id`,`action_id`) SELECT DISTINCT a.access, a.asset_id, b.id FROM `#__users` a, `#__actions` b where b.id = 3;

/** aggregate permissions */
INSERT INTO `#__permissions_groups` (`group_id`,`asset_id`,`action_id`)
  SELECT DISTINCT `group_id`,`asset_id`,`action_id`
    FROM `#__temp_permissions`;

INSERT INTO `#__permissions_groupings` ( `grouping_id`, `asset_id`, `action_id`)
  SELECT DISTINCT b.grouping_id, a.asset_id, a.action_id
  FROM `#__temp_permissions` a,
    `#__group_to_groupings` b
  WHERE a.group_id = b.group_id;

DROP TABLE `#__temp_permissions`;