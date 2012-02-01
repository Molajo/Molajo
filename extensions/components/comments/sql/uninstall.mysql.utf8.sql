DELETE FROM `#__assets` WHERE `name` LIKE 'comments%';
DELETE FROM `#__content` WHERE `component_option` = 'comments';
