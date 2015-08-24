--
-- Catalog
--

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


DELETE FROM `molajo_application_extension_instances`;
DELETE FROM `molajo_site_extension_instances`;
DELETE FROM `molajo_catalog`;

INSERT INTO `molajo_catalog`(`application_id`, `catalog_type_id`, `source_id`, `enabled`, `redirect_to_id`, `sef_request`, `page_type`, `extension_instance_id`, `view_group_id`, `primary_category_id`)
  SELECT DISTINCT `a`.`id`, `a`.`catalog_type_id`, `a`.`id`, 1, 0, `a`.`path`, '', `b`.`id`, 1, 12
  FROM `molajo_applications` `a`,
    `molajo_extension_instances` `b`
  WHERE `a`.`catalog_type_id` = `b`.`catalog_type_id`;

INSERT INTO `molajo_catalog`(`application_id`, `catalog_type_id`, `source_id`, `enabled`, `redirect_to_id`, `sef_request`, `page_type`, `extension_instance_id`, `view_group_id`, `primary_category_id`)
  SELECT DISTINCT `b`.`id`, `a`.`catalog_type_id`, `a`.`id`, 1, 0, CONCAT(`a`.`path`, '/', `a`.`alias`), `a`.`page_type`, `a`.`id`, 1, 12
  FROM `molajo_extension_instances` `a`,
    `molajo_applications` b
  WHERE `a`.`path` <> '';

INSERT INTO `molajo_catalog`(`application_id`, `catalog_type_id`, `source_id`, `enabled`, `redirect_to_id`, `sef_request`, `page_type`, `extension_instance_id`, `view_group_id`, `primary_category_id`)
  SELECT DISTINCT `b`.`id`, `a`.`catalog_type_id`, `a`.`id`, 1, 0, `a`.`alias`, `a`.`page_type`, `a`.`id`, 1, 12
  FROM `molajo_extension_instances` `a`,
    `molajo_applications` b
  WHERE `a`.`path` = '';

INSERT INTO `molajo_catalog`(`application_id`, `catalog_type_id`, `source_id`, `enabled`, `redirect_to_id`, `sef_request`, `page_type`, `extension_instance_id`, `view_group_id`, `primary_category_id`)
  SELECT DISTINCT `b`.`id`, `a`.`catalog_type_id`, `a`.`id`, 1, 0, CONCAT(`a`.`path`, '/', `a`.`alias`), 'Item', `a`.`id`, 1, 12
  FROM `molajo_language_strings` `a`,
    `molajo_applications` b;

INSERT INTO `molajo_catalog`(`application_id`, `catalog_type_id`, `source_id`, `enabled`, `redirect_to_id`, `sef_request`, `page_type`, `extension_instance_id`, `view_group_id`, `primary_category_id`)
  SELECT DISTINCT `b`.`id`, `a`.`catalog_type_id`, `a`.`id`, 1, 0, CONCAT(`c`.`alias`, '/', `a`.`alias`), 'Item', `c`.`id`, 1, 12
  FROM `molajo_content` `a`,
    `molajo_applications` `b`,
    `molajo_extension_instances` `c`
  WHERE `a`.`extension_instance_id` = `c`.`id`;

INSERT INTO `molajo_catalog`(`application_id`, `catalog_type_id`, `source_id`, `enabled`, `redirect_to_id`, `sef_request`, `page_type`, `extension_instance_id`, `view_group_id`, `primary_category_id`)
  SELECT DISTINCT `b`.`id`, 3000, `a`.`id`, 1, 0, CONCAT('users/', `a`.`alias`), 'Item', `a`.`id`, 1, 12
  FROM `molajo_users` `a`,
    `molajo_applications` b;

INSERT INTO `molajo_catalog`(`application_id`, `catalog_type_id`, `source_id`, `enabled`, `redirect_to_id`, `sef_request`, `page_type`, `extension_instance_id`, `view_group_id`, `primary_category_id`)
  SELECT DISTINCT `b`.`id`, `a`.`catalog_type_id`, `a`.`id`, 1, 0, CONCAT(`c`.`path`, '/', `a`.`alias`), 'Item', `c`.`id`, 1, 12
  FROM `molajo_groups` `a`,
    `molajo_applications` `b`,
    `molajo_extension_instances` `c`
  WHERE `a`.`extension_instance_id` = `c`.`id`;

INSERT INTO `molajo_application_extension_instances` (`application_id`, `extension_instance_id`)
  SELECT DISTINCT application_id, extension_instance_id
  FROM `molajo_catalog`
  WHERE `extension_instance_id` IN (SELECT `id` FROM `molajo_extension_instances` WHERE `status` = 1);

INSERT INTO `molajo_site_extension_instances`(`site_id`, `extension_instance_id`)
  SELECT DISTINCT 1, `extension_instance_id`
  FROM `molajo_catalog`
  WHERE `extension_instance_id` IN (SELECT `id` FROM `molajo_extension_instances` WHERE `status` = 1);


---
--- Initialise
---
DELETE FROM `molajo_application_extension_instances`;
DELETE FROM `molajo_site_extension_instances`;
DELETE FROM `molajo_catalog`;


---
--- Applications
---

INSERT INTO `molajo_catalog`(`application_id`, `catalog_type_id`, `source_id`, `enabled`, `redirect_to_id`, `sef_request`, `page_type`, `extension_instance_id`, `view_group_id`, `primary_category_id`)
SELECT DISTINCT `a`.`id`, `a`.`catalog_type_id`, `a`.`id`, 1, 0, `a`.`path`, '', `b`.`id`, 1, 12
  FROM `molajo_applications` `a`,
    `molajo_extension_instances` `b`
  WHERE `a`.`catalog_type_id` = `b`.`catalog_type_id`;

---
--- Extensions
---

INSERT INTO `molajo_catalog`(`application_id`, `catalog_type_id`, `source_id`, `enabled`, `redirect_to_id`, `sef_request`, `page_type`, `extension_instance_id`, `view_group_id`, `primary_category_id`)
SELECT DISTINCT `b`.`id`, `a`.`catalog_type_id`, `a`.`id`, 1, 0, CONCAT(`a`.`path`, '/', `a`.`alias`), `a`.`page_type`, `a`.`id`, 1, 12
  FROM `molajo_extension_instances` `a`,
      `molajo_applications` b
  WHERE `a`.`path` <> '';

INSERT INTO `molajo_catalog`(`application_id`, `catalog_type_id`, `source_id`, `enabled`, `redirect_to_id`, `sef_request`, `page_type`, `extension_instance_id`, `view_group_id`, `primary_category_id`)
SELECT DISTINCT `b`.`id`, `a`.`catalog_type_id`, `a`.`id`, 1, 0, `a`.`alias`, `a`.`page_type`, `a`.`id`, 1, 12
  FROM `molajo_extension_instances` `a`,
      `molajo_applications` b
  WHERE `a`.`path` = '';

---
--- Language Strings
---

INSERT INTO `molajo_catalog`(`application_id`, `catalog_type_id`, `source_id`, `enabled`, `redirect_to_id`, `sef_request`, `page_type`, `extension_instance_id`, `view_group_id`, `primary_category_id`)
SELECT DISTINCT `b`.`id`, `a`.`catalog_type_id`, `a`.`id`, 1, 0, CONCAT(`a`.`path`, '/', `a`.`alias`), 'Item', `a`.`id`, 1, 12
  FROM `molajo_language_strings` `a`,
      `molajo_applications` b;

---
--- Content
---

INSERT INTO `molajo_catalog`(`application_id`, `catalog_type_id`, `source_id`, `enabled`, `redirect_to_id`, `sef_request`, `page_type`, `extension_instance_id`, `view_group_id`, `primary_category_id`)
  SELECT DISTINCT `b`.`id`, `a`.`catalog_type_id`, `a`.`id`, 1, 0, CONCAT(`c`.`alias`, '/', `a`.`alias`), 'Item', `c`.`id`, 1, 12
  FROM `molajo_content` `a`,
    `molajo_applications` `b`,
    `molajo_extension_instances` `c`
  WHERE `a`.`extension_instance_id` = `c`.`id`;

---
--- Users
---

INSERT INTO `molajo_catalog`(`application_id`, `catalog_type_id`, `source_id`, `enabled`, `redirect_to_id`, `sef_request`, `page_type`, `extension_instance_id`, `view_group_id`, `primary_category_id`)
  SELECT DISTINCT `b`.`id`, 3000, `a`.`id`, 1, 0, CONCAT('users/', `a`.`alias`), 'Item', `a`.`id`, 1, 12
  FROM `molajo_users` `a`,
    `molajo_applications` b;

---
--- Groups
---

INSERT INTO `molajo_catalog`(`application_id`, `catalog_type_id`, `source_id`, `enabled`, `redirect_to_id`, `sef_request`, `page_type`, `extension_instance_id`, `view_group_id`, `primary_category_id`)
SELECT DISTINCT `b`.`id`, `a`.`catalog_type_id`, `a`.`id`, 1, 0, CONCAT(`c`.`path`, '/', `a`.`alias`), 'Item', `c`.`id`, 1, 12
FROM `molajo_groups` `a`,
    `molajo_applications` `b`,
    `molajo_extension_instances` `c`
WHERE `a`.`extension_instance_id` = `c`.`id`;

--
-- Application Extension Instances
--

INSERT INTO `molajo_application_extension_instances` (`application_id`, `extension_instance_id`)
  SELECT DISTINCT application_id, extension_instance_id
  FROM `molajo_catalog`
  WHERE `extension_instance_id` IN (SELECT `id` FROM `molajo_extension_instances` WHERE `status` = 1);

--
-- Site Extension Instances
--

INSERT INTO `molajo_site_extension_instances`(`site_id`, `extension_instance_id`)
  SELECT DISTINCT 1, `extension_instance_id`
  FROM `molajo_catalog`
  WHERE `extension_instance_id` IN (SELECT `id` FROM `molajo_extension_instances` WHERE `status` = 1);
