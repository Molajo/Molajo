DROP TABLE IF EXISTS `#__molajosamples`;
DELETE FROM `#__assets` WHERE `name` LIKE 'com_molajosamples%';
DELETE FROM `#__configuration` WHERE `component_option` = 'com_molajosamples';
DELETE FROM `#__categories` WHERE `extension` = 'com_molajosamples';