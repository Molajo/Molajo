DROP TABLE IF EXISTS `#__articles`;
DELETE FROM `#__assets` WHERE `name` LIKE 'articles%';
DELETE FROM `#__configuration` WHERE `component_option` = 'articles';
