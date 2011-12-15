DROP TABLE IF EXISTS `#__comments`;
DELETE FROM `#__assets` WHERE `name` LIKE 'comments%';
DELETE FROM `#__configuration` WHERE `component_option` = 'comments';