DELETE FROM `#__assets` WHERE `name` LIKE 'dashboard%';
DELETE FROM `#__configuration` WHERE `component_option` = 'dashboard';