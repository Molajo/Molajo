/** POPULATE CATALOG TABLE FOR SOURCE */

/** 1. Insert Catalog Entry for Sites */
INSERT INTO `molajo_catalog`(`id`, `catalog_type_id`, `source_id`, `routable`, `menuitem_type`, `sef_request`, `redirect_to_id`, `view_group_id`, `primary_category_id`, `tinyurl`)
SELECT NULL as `id`, `a`.`catalog_type_id`, `a`.`id` as `source_id`,  `b`.`routable`, 'Item' as `menuitem_type`, CONCAT(`b`.`slug`, '/', `a`.`path`), 0 as `redirect_to_id`, 1 as `view_group_id`, `b`.`primary_category_id`, '' as `tinyurl`
FROM `molajo_sites` a,
molajo_catalog_types b
where a.catalog_type_id = b.id

/** 2. Insert Catalog Entry for Applications */
INSERT INTO `molajo_catalog`(`id`, `catalog_type_id`, `source_id`, `routable`, `menuitem_type`, `sef_request`, `redirect_to_id`, `view_group_id`, `primary_category_id`, `tinyurl`)
SELECT NULL as `id`, `a`.`catalog_type_id`, `a`.`id` as `source_id`,  `b`.`routable`, 'Item' as `menuitem_type`, CONCAT(`b`.`slug`, '/', `a`.`path`), 0 as `redirect_to_id`, 1 as `view_group_id`, `b`.`primary_category_id`, '' as `tinyurl`
FROM `molajo_applications` a,
molajo_catalog_types b
where a.catalog_type_id = b.id

/** 3. Insert Catalog Entries for Extension Instances (but not menuitems) */
INSERT INTO `molajo_catalog`(`id`, `catalog_type_id`, `source_id`, `routable`, `menuitem_type`, `sef_request`, `redirect_to_id`, `view_group_id`, `primary_category_id`, `tinyurl`)
SELECT NULL as `id`, `a`.`catalog_type_id`, `a`.`id` as `source_id`,  `b`.`routable`, 'Item' as `menuitem_type`, CONCAT(`b`.`slug`, '/', `a`.`alias`), 0 as `redirect_to_id`, 1 as `view_group_id`, `b`.`primary_category_id`, '' as `tinyurl`
FROM `molajo_extension_instances` a,
	molajo_catalog_types b
WHERE a.catalog_type_id = b.id
  AND a.catalog_type_id <> 1300

/** 3. Insert Catalog Entries for Menuitems */
INSERT INTO `molajo_catalog`(`id`, `catalog_type_id`, `source_id`, `routable`, `menuitem_type`, `sef_request`, `redirect_to_id`, `view_group_id`, `primary_category_id`, `tinyurl`)
SELECT NULL as `id`, `a`.`catalog_type_id`, `a`.`id` as `source_id`,  `b`.`routable`, 'Item' as `menuitem_type`, CONCAT(`a`.`path`, '/', `a`.`alias`), 0 as `redirect_to_id`, 1 as `view_group_id`, `b`.`primary_category_id`, '' as `tinyurl`
FROM `molajo_extension_instances` a,
molajo_catalog_types b
WHERE a.catalog_type_id = b.id
AND a.catalog_type_id = 1300
AND a.path <> ''

update `molajo_catalog`
set menuitem_type = 'Dashboard'
where sef_request like '%dashboard%'
and catalog_type_id = 1300

update `molajo_catalog`
set menuitem_type = 'Configuration'
where sef_request like '%configuration%'
and catalog_type_id = 1300

INSERT INTO `molajo_catalog`(`id`, `catalog_type_id`, `source_id`, `routable`, `menuitem_type`, `sef_request`, `redirect_to_id`, `view_group_id`, `primary_category_id`, `tinyurl`)
SELECT NULL as `id`, `a`.`catalog_type_id`, `a`.`id` as `source_id`,  `b`.`routable`, 'Item' as `menuitem_type`, CONCAT(`a`.`alias`), 0 as `redirect_to_id`, 1 as `view_group_id`, `b`.`primary_category_id`, '' as `tinyurl`
FROM `molajo_extension_instances` a,
molajo_catalog_types b
WHERE a.catalog_type_id = b.id
AND a.catalog_type_id = 1300
AND a.path = ''

/** 4. Insert Catalog Entry for User */
INSERT INTO `molajo_catalog`(`id`, `catalog_type_id`, `source_id`, `routable`, `menuitem_type`, `sef_request`, `redirect_to_id`, `view_group_id`, `primary_category_id`, `tinyurl`)
	SELECT NULL as `id`, `a`.`catalog_type_id`, `a`.`id` as `source_id`,  `b`.`routable`, 'Item' as `menuitem_type`, CONCAT(`b`.`slug`, '/', `a`.`username`), 0 as `redirect_to_id`, 1 as `view_group_id`, `b`.`primary_category_id`, '' as `tinyurl`
		FROM `molajo_users` a,
			molajo_catalog_types b
		WHERE a.catalog_type_id = b.id


/** 5. Content Table   */
INSERT INTO `molajo_catalog`(`id`, `catalog_type_id`, `source_id`, `routable`, `menuitem_type`, `sef_request`, `redirect_to_id`, `view_group_id`, `primary_category_id`, `tinyurl`)
SELECT NULL as `id`, `a`.`catalog_type_id`, `a`.`id` as `source_id`,  `b`.`routable`, 'Item' as `menuitem_type`, CONCAT(`b`.`slug`, '/', `a`.`alias`), 0 as `redirect_to_id`, 1 as `view_group_id`, `b`.`primary_category_id`, '' as `tinyurl`
	FROM `molajo_content` a,
	molajo_catalog_types b
	WHERE a.catalog_type_id = b.id


/** 28. Build Catalog Categories Table */
INSERT INTO `molajo_catalog_categories`(`catalog_id`, `category_id`)
SELECT DISTINCT a.id, a.primary_category_id
FROM molajo_catalog a

/** 29. Build Catalog Activity */
INSERT INTO `molajo_catalog_activity`(`id`, `catalog_id`, `user_id`, `action_id`, `rating`, `activity_datetime`, `ip_address`, `customfields`)
SELECT NULL, a.id, 1, 2, NULL, '2012-07-01 12:00:00', '127.0.0.1', '{}'
FROM molajo_catalog a

/** 30. Build View Group Permissions */
INSERT INTO `molajo_view_group_permissions`(`id`, `view_group_id`, `catalog_id`, `action_id`)
SELECT DISTINCT NULL, a.view_group_id, a.id, '3'
FROM molajo_catalog a

//////

{{button,small,red}}
{{tabs,pretty,title}}
{{menu

* * * * To do * * * *

Text Filters - get text filters hooked up to groups and separate HTMLPurifier as an add on option

Registry - "lock" a registry

Install - core, extensions, packages - do this last

Sessions - connect to Symfony's


1. Images - http://adaptive-images.com/

4. Gmaps - http://hpneo.github.com/gmaps/examples/geolocation.html

5. RESS - Server side detection http://www.brettjankord.com/2012/02/29/hrwd-hybrid-responsive-web-design/

6. Client side detection - Modernizer



* * * * Database Code useful during development * * * *

1. Review

Services::Registry()->get('Parameters', '*');

2. add an extension

INSERT INTO `molajo_extensions`
(`id`, `extension_site_id`, `catalog_type_id`, `name`, `sub_title`, `language`, `translation_of_id`, `ordering`)
VALUES ( 6181 ,  '1',  '1450',  'Formselectlist', 'Plugin', '', 0, 1);

INSERT INTO `molajo_extension_instances` (`id`, `extension_id`, `catalog_type_id`, `title`, `subtitle`, `alias`, `content_text`, `protected`, `featured`, `stickied`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `customfields`, `parameters`, `metadata`, `language`, `translation_of_id`, `ordering`)
SELECT a.id, a.id, a.catalog_type_id, a.name, 'Template', '', '', 1, 0, 0, 1, '2012-07-01 12:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-07-01 12:00:00', 0, '2012-07-01 12:00:00', 0, '0000-00-00 00:00:00', 0, '{}', '\r\n{\r\n    "1":{\r\n        "criteria_display_view_on_no_results":"",\r\n        "criteria_snippet_length":"",\r\n        "criteria_extension_instance_id":"",\r\n        "criteria_catalog_type_id":"",\r\n\r\n        "cache":"",\r\n        "cache_time":"",\r\n        "cache_handler":""\r\n    },\r\n        \r\n    "2":{\r\n        "criteria_display_view_on_no_results":"",\r\n        "criteria_snippet_length":"",\r\n        "criteria_extension_instance_id":"",\r\n        "criteria_catalog_type_id":"",\r\n\r\n        "cache":"",\r\n        "cache_time":"",\r\n        "cache_handler":""\r\n    }\r\n}', NULL, 'en-GB', 0, 24
FROM molajo_extensions a
WHERE id = 6181;

INSERT INTO  `molajo_site_extension_instances` (  `site_id` ,  `extension_instance_id` )
SELECT a.id, b.id
FROM molajo_sites a, molajo_extension_instances b
WHERE b.id = 6181;

INSERT INTO  `molajo_application_extension_instances` (  `application_id` ,  `extension_instance_id` )
SELECT a.id, b.id
FROM molajo_applications a, molajo_extension_instances b
WHERE b.id = 6181;

INSERT INTO molajo_catalog
SELECT NULL, a.catalog_type_id, a.id, b.routable,
'' as menuitem_type, '' as sef_request, 0 as redirect_to_id,
1 as view_group_id, b.primary_category_id, '' as tinyurl
from molajo_extension_instances a,
molajo_catalog_types b
where a.id = 6181
and a.catalog_type_id = b.id;

2. sample data

INSERT INTO `molajo_content` (`id`, `extension_instance_id`, `catalog_type_id`, `title`, `subtitle`, `path`, `alias`, `content_text`, `protected`, `featured`, `stickied`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `root`, `parent_id`, `lft`, `rgt`, `lvl`, `home`, `customfields`, `parameters`, `metadata`, `language`, `translation_of_id`, `ordering`) VALUES
(NULL, 15, 11000, 'About Us', '', 'site', 'about-us', '<p>{mockimage}50,50,box{/mockimage}Eum ex dico saepe tritani. Eripuit denique at nam, mei veniam accusata ad, vis ei wisi expetenda. An congue feugait vivendum his. Nisl munere platonem ad vis, ea timeam laoreet eos. Ea sea erat definiebas. At case nihil eleifend cum, labitur splendide voluptatum nec cu, alii esse dicant eu sed.</p>\r\n{readmore}\r\n<p>Qui solet vidisse principes ne, accusam mnesarchum nec ut. :) Vim possit aliquam ea. Et tibique qualisque sit, amet populo impetus eum et. Ad semper melius sed, oratio ridens vocibus cum ea, at vix suscipit mnesarchum intellegam.</p>\r\n\r\n<p>{mockimage}250,250,box{/mockimage}Mel aperiam praesent at, placerat tincidunt temporibus nec ut, admodum molestie necessitatibus duo eu. Vide fastidii forensibus ne ius, recusabo eleifend similique vim id. In persius argumentum pri, fierent sententiae eu sit. At nec enim libris. Ad mei habemus fierent luptatum.</p>', 0, 0, 0, 1, '2012-07-01 12:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-07-01 12:00:00', 1, '2012-07-01 12:00:00', 1, '0000-00-00 00:00:00', 0, 101, 0, 1, 14, 1, 0, '', '\r\n{\r\n    "2":{\r\n\r\n        "criteria_title":"Tags",\r\n        "criteria_display_view_on_no_results":"0",\r\n        "criteria_snippet_length":"200",\r\n        "criteria_status":"",\r\n        "criteria_content_catalog_type_id":"",\r\n        "criteria_menuitem_catalog_type_id":"",\r\n        "criteria_content_extension_instance_id":"",\r\n        "criteria_menuitem_extension_instance_id":"",\r\n\r\n        "mustache":"0",\r\n\r\n        "parent_menuid":"2140",\r\n\r\n        "item_theme_id":"9010",\r\n        "item_page_view_id":"225",\r\n        "item_page_view_css_id":"",\r\n        "item_page_view_css_class":"",\r\n        "item_template_view_id":"1200",\r\n        "item_template_view_css_id":"",\r\n        "item_template_view_css_class":"",\r\n        "item_wrap_view_id":"2030",\r\n        "item_wrap_view_css_id":"",\r\n        "item_wrap_view_css_class":"",\r\n        "item_model_name":"Categories",\r\n        "item_model_type":"Table",\r\n        "item_model_query_object":"Item",\r\n\r\n        "form_theme_id":"9010",\r\n        "form_page_view_id":"210",\r\n        "form_page_view_css_id":"",\r\n        "form_page_view_css_class":"",\r\n        "form_template_view_id":"1260",\r\n        "form_template_view_css_id":"",\r\n        "form_template_view_css_class":"",\r\n        "form_wrap_view_id":"2030",\r\n        "form_wrap_view_css_id":"",\r\n        "form_wrap_view_css_class":"",\r\n        "form_model_name":"Categories",\r\n        "form_model_type":"Table",\r\n        "form_model_query_object":"Item",\r\n\r\n        "enable_draft_save":"1",\r\n        "enable_version_history":"1",\r\n        "enable_retain_versions_after_delete":"1",\r\n        "enable_maximum_version_count":"5",\r\n        "enable_hit_counts":"1",\r\n        "enable_comments":"1",\r\n        "enable_spam_protection":"1",\r\n        "enable_ratings":"1",\r\n        "enable_notifications":"1",\r\n        "enable_tweets":"1",\r\n        "enable_ping":"1",\r\n\r\n        "cache":"0",\r\n        "cache_time":"15",\r\n        "cache_handler":"file",\r\n\r\n        "log_user_activity_create":"1",\r\n        "log_user_activity_read":"1",\r\n        "log_user_activity_update":"1",\r\n        "log_user_activity_delete":"1",\r\n\r\n        "log_catalog_update_activity":"1",\r\n        "log_catalog_view_activity":"1",\r\n\r\n        "asset_priority_site":"100",\r\n        "asset_priority_application":"200",\r\n        "asset_priority_user":"300",\r\n        "asset_priority_extension":"400",\r\n        "asset_priority_request":"500",\r\n        "asset_priority_tag":"600",\r\n        "asset_priority_menu_item":"700",\r\n        "asset_priority_source":"800",\r\n        "asset_priority_theme":"900",\r\n\r\n        "image_xsmall":"50",\r\n        "image_small":"75",\r\n        "image_medium":"150",\r\n        "image_large":"300",\r\n        "image_xlarge":"500",\r\n        "image_folder":"Images",\r\n        "image_thumb_folder":"Thumbs",\r\n\r\n        "gravatar":"1",\r\n        "gravatar_size":"50",\r\n        "gravatar_type":"mm",\r\n        "gravatar_rating":"pg",\r\n        "gravatar_image":"1"\r\n\r\n    }\r\n}\r\n\r\n', '{"title":"Article 1", \r\n"description":"This is Article 1.",\r\n"keywords":"tag, content", \r\n"robots":"follow, index", \r\n"author":"", \r\n"content_rights":""}\r\n', 'en-GB', 0, 1),
(NULL, 15, 11000, 'Contact Us', '', 'site', 'contact-us', '<p>{mockimage}50,50,box{/mockimage}Eum ex dico saepe tritani. Eripuit denique at nam, mei veniam accusata ad, vis ei wisi expetenda. An congue feugait vivendum his. Nisl munere platonem ad vis, ea timeam laoreet eos. Ea sea erat definiebas. At case nihil eleifend cum, labitur splendide voluptatum nec cu, alii esse dicant eu sed.</p>\r\n{readmore}\r\n<p>Qui solet vidisse principes ne, accusam mnesarchum nec ut. :) Vim possit aliquam ea. Et tibique qualisque sit, amet populo impetus eum et. Ad semper melius sed, oratio ridens vocibus cum ea, at vix suscipit mnesarchum intellegam.</p>\r\n\r\n<p>{mockimage}250,250,box{/mockimage}Mel aperiam praesent at, placerat tincidunt temporibus nec ut, admodum molestie necessitatibus duo eu. Vide fastidii forensibus ne ius, recusabo eleifend similique vim id. In persius argumentum pri, fierent sententiae eu sit. At nec enim libris. Ad mei habemus fierent luptatum.</p>', 0, 0, 0, 1, '2012-07-01 12:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-07-01 12:00:00', 1, '2012-07-01 12:00:00', 1, '0000-00-00 00:00:00', 0, 102, 0, 0, 0, 0, 0, '', '\r\n{\r\n    "2":{\r\n\r\n        "criteria_title":"Categories",\r\n        "criteria_display_view_on_no_results":"0",\r\n        "criteria_snippet_length":"200",\r\n        "criteria_status":"",\r\n        "criteria_content_catalog_type_id":"",\r\n        "criteria_menuitem_catalog_type_id":"",\r\n        "criteria_content_extension_instance_id":"",\r\n        "criteria_menuitem_extension_instance_id":"",\r\n\r\n        "mustache":"0",\r\n\r\n        "parent_menuid":"2140",\r\n\r\n        "item_theme_id":"9010",\r\n        "item_page_view_id":"225",\r\n        "item_page_view_css_id":"",\r\n        "item_page_view_css_class":"",\r\n        "item_template_view_id":"1200",\r\n        "item_template_view_css_id":"",\r\n        "item_template_view_css_class":"",\r\n        "item_wrap_view_id":"2030",\r\n        "item_wrap_view_css_id":"",\r\n        "item_wrap_view_css_class":"",\r\n        "item_model_name":"Categories",\r\n        "item_model_type":"Table",\r\n        "item_model_query_object":"Item",\r\n\r\n        "form_theme_id":"9010",\r\n        "form_page_view_id":"210",\r\n        "form_page_view_css_id":"",\r\n        "form_page_view_css_class":"",\r\n        "form_template_view_id":"1260",\r\n        "form_template_view_css_id":"",\r\n        "form_template_view_css_class":"",\r\n        "form_wrap_view_id":"2030",\r\n        "form_wrap_view_css_id":"",\r\n        "form_wrap_view_css_class":"",\r\n        "form_model_name":"Categories",\r\n        "form_model_type":"Table",\r\n        "form_model_query_object":"Item",\r\n\r\n        "enable_draft_save":"1",\r\n        "enable_version_history":"1",\r\n        "enable_retain_versions_after_delete":"1",\r\n        "enable_maximum_version_count":"5",\r\n        "enable_hit_counts":"1",\r\n        "enable_comments":"1",\r\n        "enable_spam_protection":"1",\r\n        "enable_ratings":"1",\r\n        "enable_notifications":"1",\r\n        "enable_tweets":"1",\r\n        "enable_ping":"1",\r\n\r\n        "cache":"0",\r\n        "cache_time":"15",\r\n        "cache_handler":"file",\r\n\r\n        "log_user_activity_create":"1",\r\n        "log_user_activity_read":"1",\r\n        "log_user_activity_update":"1",\r\n        "log_user_activity_delete":"1",\r\n\r\n        "log_catalog_update_activity":"1",\r\n        "log_catalog_view_activity":"1",\r\n\r\n        "asset_priority_site":"100",\r\n        "asset_priority_application":"200",\r\n        "asset_priority_user":"300",\r\n        "asset_priority_extension":"400",\r\n        "asset_priority_request":"500",\r\n        "asset_priority_tag":"600",\r\n        "asset_priority_menu_item":"700",\r\n        "asset_priority_source":"800",\r\n        "asset_priority_theme":"900",\r\n\r\n        "image_xsmall":"50",\r\n        "image_small":"75",\r\n        "image_medium":"150",\r\n        "image_large":"300",\r\n        "image_xlarge":"500",\r\n        "image_folder":"Images",\r\n        "image_thumb_folder":"Thumbs",\r\n\r\n        "gravatar":"1",\r\n        "gravatar_size":"50",\r\n        "gravatar_type":"mm",\r\n        "gravatar_rating":"pg",\r\n        "gravatar_image":"1"\r\n\r\n    }\r\n}\r\n\r\n', '{"metadata_title":"Article 2", "metadata_description":"This is Article 2.", "metadata_keywords":"data-element, content", "metadata_robots":"follow, index", "metadata_author":"Author Name", "metadata_content_rights":"CC"}', 'en-GB', 0, 2),
(NULL, 15, 11000, 'Privacy Policy', '', 'site', 'privacy-policy', '<p>{mockimage}50,50,box{/mockimage}Eum ex dico saepe tritani. Eripuit denique at nam, mei veniam accusata ad, vis ei wisi expetenda. An congue feugait vivendum his. Nisl munere platonem ad vis, ea timeam laoreet eos. Ea sea erat definiebas. At case nihil eleifend cum, labitur splendide voluptatum nec cu, alii esse dicant eu sed.</p>\n{readmore}\n<p>Qui solet vidisse principes ne, accusam mnesarchum nec ut. :) Vim possit aliquam ea. Et tibique qualisque sit, amet populo impetus eum et. Ad semper melius sed, oratio ridens vocibus cum ea, at vix suscipit mnesarchum intellegam.</p>\n\n<p>{mockimage}250,250,box{/mockimage}Mel aperiam praesent at, placerat tincidunt temporibus nec ut, admodum molestie necessitatibus duo eu. Vide fastidii forensibus ne ius, recusabo eleifend similique vim id. In persius argumentum pri, fierent sententiae eu sit. At nec enim libris. Ad mei habemus fierent luptatum.</p>', 0, 0, 0, 1, '2012-07-01 12:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-07-01 12:00:00', 1, '2012-07-01 12:00:00', 1, '0000-00-00 00:00:00', 0, 103, 0, 0, 0, 0, 0, '', '\r\n{\r\n    "2":{\r\n\r\n        "criteria_title":"Categories",\r\n        "criteria_display_view_on_no_results":"0",\r\n        "criteria_snippet_length":"200",\r\n        "criteria_status":"",\r\n        "criteria_content_catalog_type_id":"",\r\n        "criteria_menuitem_catalog_type_id":"",\r\n        "criteria_content_extension_instance_id":"",\r\n        "criteria_menuitem_extension_instance_id":"",\r\n\r\n        "mustache":"0",\r\n\r\n        "parent_menuid":"2140",\r\n\r\n        "item_theme_id":"9010",\r\n        "item_page_view_id":"225",\r\n        "item_page_view_css_id":"",\r\n        "item_page_view_css_class":"",\r\n        "item_template_view_id":"1200",\r\n        "item_template_view_css_id":"",\r\n        "item_template_view_css_class":"",\r\n        "item_wrap_view_id":"2030",\r\n        "item_wrap_view_css_id":"",\r\n        "item_wrap_view_css_class":"",\r\n        "item_model_name":"Categories",\r\n        "item_model_type":"Table",\r\n        "item_model_query_object":"Item",\r\n\r\n        "form_theme_id":"9010",\r\n        "form_page_view_id":"210",\r\n        "form_page_view_css_id":"",\r\n        "form_page_view_css_class":"",\r\n        "form_template_view_id":"1260",\r\n        "form_template_view_css_id":"",\r\n        "form_template_view_css_class":"",\r\n        "form_wrap_view_id":"2030",\r\n        "form_wrap_view_css_id":"",\r\n        "form_wrap_view_css_class":"",\r\n        "form_model_name":"Categories",\r\n        "form_model_type":"Table",\r\n        "form_model_query_object":"Item",\r\n\r\n        "enable_draft_save":"1",\r\n        "enable_version_history":"1",\r\n        "enable_retain_versions_after_delete":"1",\r\n        "enable_maximum_version_count":"5",\r\n        "enable_hit_counts":"1",\r\n        "enable_comments":"1",\r\n        "enable_spam_protection":"1",\r\n        "enable_ratings":"1",\r\n        "enable_notifications":"1",\r\n        "enable_tweets":"1",\r\n        "enable_ping":"1",\r\n\r\n        "cache":"0",\r\n        "cache_time":"15",\r\n        "cache_handler":"file",\r\n\r\n        "log_user_activity_create":"1",\r\n        "log_user_activity_read":"1",\r\n        "log_user_activity_update":"1",\r\n        "log_user_activity_delete":"1",\r\n\r\n        "log_catalog_update_activity":"1",\r\n        "log_catalog_view_activity":"1",\r\n\r\n        "asset_priority_site":"100",\r\n        "asset_priority_application":"200",\r\n        "asset_priority_user":"300",\r\n        "asset_priority_extension":"400",\r\n        "asset_priority_request":"500",\r\n        "asset_priority_tag":"600",\r\n        "asset_priority_menu_item":"700",\r\n        "asset_priority_source":"800",\r\n        "asset_priority_theme":"900",\r\n\r\n        "image_xsmall":"50",\r\n        "image_small":"75",\r\n        "image_medium":"150",\r\n        "image_large":"300",\r\n        "image_xlarge":"500",\r\n        "image_folder":"Images",\r\n        "image_thumb_folder":"Thumbs",\r\n\r\n        "gravatar":"1",\r\n        "gravatar_size":"50",\r\n        "gravatar_type":"mm",\r\n        "gravatar_rating":"pg",\r\n        "gravatar_image":"1"\r\n\r\n    }\r\n}\r\n\r\n', '{"metadata_title":"Article 3", "metadata_description":"This is Article 3.", "metadata_keywords":"tag, content", "metadata_robots":"follow, index", "metadata_author":"Author Name", "metadata_content_rights":"CC"}', 'en-GB', 0, 3),
(NULL, 15, 11000, '404-Error', '', 'site', 'not-found', '<p>{mockimage}50,50,box{/mockimage}Eum ex dico saepe tritani. Eripuit denique at nam, mei veniam accusata ad, vis ei wisi expetenda. An congue feugait vivendum his. Nisl munere platonem ad vis, ea timeam laoreet eos. Ea sea erat definiebas. At case nihil eleifend cum, labitur splendide voluptatum nec cu, alii esse dicant eu sed.</p>\n{readmore}\n<p>Qui solet vidisse principes ne, accusam mnesarchum nec ut. :) Vim possit aliquam ea. Et tibique qualisque sit, amet populo impetus eum et. Ad semper melius sed, oratio ridens vocibus cum ea, at vix suscipit mnesarchum intellegam.</p>\n\n<p>{mockimage}250,250,box{/mockimage}Mel aperiam praesent at, placerat tincidunt temporibus nec ut, admodum molestie necessitatibus duo eu. Vide fastidii forensibus ne ius, recusabo eleifend similique vim id. In persius argumentum pri, fierent sententiae eu sit. At nec enim libris. Ad mei habemus fierent luptatum.</p>', 0, 0, 0, 1, '2012-07-01 12:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-07-01 12:00:00', 1, '2012-07-01 12:00:00', 1, '0000-00-00 00:00:00', 0, 104, 0, 0, 0, 0, 0, '', '\r\n{\r\n    "2":{\r\n\r\n        "criteria_title":"Categories",\r\n        "criteria_display_view_on_no_results":"0",\r\n        "criteria_snippet_length":"200",\r\n        "criteria_status":"",\r\n        "criteria_content_catalog_type_id":"",\r\n        "criteria_menuitem_catalog_type_id":"",\r\n        "criteria_content_extension_instance_id":"",\r\n        "criteria_menuitem_extension_instance_id":"",\r\n\r\n        "mustache":"0",\r\n\r\n        "parent_menuid":"2140",\r\n\r\n        "item_theme_id":"9010",\r\n        "item_page_view_id":"225",\r\n        "item_page_view_css_id":"",\r\n        "item_page_view_css_class":"",\r\n        "item_template_view_id":"1200",\r\n        "item_template_view_css_id":"",\r\n        "item_template_view_css_class":"",\r\n        "item_wrap_view_id":"2030",\r\n        "item_wrap_view_css_id":"",\r\n        "item_wrap_view_css_class":"",\r\n        "item_model_name":"Categories",\r\n        "item_model_type":"Table",\r\n        "item_model_query_object":"Item",\r\n\r\n        "form_theme_id":"9010",\r\n        "form_page_view_id":"210",\r\n        "form_page_view_css_id":"",\r\n        "form_page_view_css_class":"",\r\n        "form_template_view_id":"1260",\r\n        "form_template_view_css_id":"",\r\n        "form_template_view_css_class":"",\r\n        "form_wrap_view_id":"2030",\r\n        "form_wrap_view_css_id":"",\r\n        "form_wrap_view_css_class":"",\r\n        "form_model_name":"Categories",\r\n        "form_model_type":"Table",\r\n        "form_model_query_object":"Item",\r\n\r\n        "enable_draft_save":"1",\r\n        "enable_version_history":"1",\r\n        "enable_retain_versions_after_delete":"1",\r\n        "enable_maximum_version_count":"5",\r\n        "enable_hit_counts":"1",\r\n        "enable_comments":"1",\r\n        "enable_spam_protection":"1",\r\n        "enable_ratings":"1",\r\n        "enable_notifications":"1",\r\n        "enable_tweets":"1",\r\n        "enable_ping":"1",\r\n\r\n        "cache":"0",\r\n        "cache_time":"15",\r\n        "cache_handler":"file",\r\n\r\n        "log_user_activity_create":"1",\r\n        "log_user_activity_read":"1",\r\n        "log_user_activity_update":"1",\r\n        "log_user_activity_delete":"1",\r\n\r\n        "log_catalog_update_activity":"1",\r\n        "log_catalog_view_activity":"1",\r\n\r\n        "asset_priority_site":"100",\r\n        "asset_priority_application":"200",\r\n        "asset_priority_user":"300",\r\n        "asset_priority_extension":"400",\r\n        "asset_priority_request":"500",\r\n        "asset_priority_tag":"600",\r\n        "asset_priority_menu_item":"700",\r\n        "asset_priority_source":"800",\r\n        "asset_priority_theme":"900",\r\n\r\n        "image_xsmall":"50",\r\n        "image_small":"75",\r\n        "image_medium":"150",\r\n        "image_large":"300",\r\n        "image_xlarge":"500",\r\n        "image_folder":"Images",\r\n        "image_thumb_folder":"Thumbs",\r\n\r\n        "gravatar":"1",\r\n        "gravatar_size":"50",\r\n        "gravatar_type":"mm",\r\n        "gravatar_rating":"pg",\r\n        "gravatar_image":"1"\r\n\r\n    }\r\n}\r\n\r\n', '{"metadata_title":"Article 4", "metadata_description":"This is Article 4.", "metadata_keywords":"tag, content", "metadata_robots":"follow, index", "metadata_author":"Author Name", "metadata_content_rights":"CC"}', 'en-GB', 0, 4),
(NULL, 15, 11000, 'Site Map', '', 'site', 'site-map', '<p>Eum ex dico saepe tritani. Eripuit denique at nam, mei veniam accusata ad, vis ei wisi expetenda. An congue feugait vivendum his. Nisl munere platonem ad vis, ea timeam laoreet eos. Ea sea erat definiebas. At case nihil eleifend cum, labitur splendide voluptatum nec cu, alii esse dicant eu sed.</p>\n{readmore}\n<p>Qui solet vidisse principes ne, accusam mnesarchum nec ut. :) Vim possit aliquam ea. Et tibique qualisque sit, amet populo impetus eum et. Ad semper melius sed, oratio ridens vocibus cum ea, at vix suscipit mnesarchum intellegam.</p>\n\n<p>{mockimage}250,250,box{/mockimage}Mel aperiam praesent at, placerat tincidunt temporibus nec ut, admodum molestie necessitatibus duo eu. Vide fastidii forensibus ne ius, recusabo eleifend similique vim id. In persius argumentum pri, fierent sententiae eu sit. At nec enim libris. Ad mei habemus fierent luptatum.</p>', 0, 0, 0, 1, '2012-07-01 12:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-07-01 12:00:00', 1, '2012-07-01 12:00:00', 1, '0000-00-00 00:00:00', 0, 105, 0, 1, 8, 1, 0, '', '\r\n{\r\n    "2":{\r\n\r\n        "criteria_title":"Categories",\r\n        "criteria_display_view_on_no_results":"0",\r\n        "criteria_snippet_length":"200",\r\n        "criteria_status":"",\r\n        "criteria_content_catalog_type_id":"",\r\n        "criteria_menuitem_catalog_type_id":"",\r\n        "criteria_content_extension_instance_id":"",\r\n        "criteria_menuitem_extension_instance_id":"",\r\n\r\n        "mustache":"0",\r\n\r\n        "parent_menuid":"2140",\r\n\r\n        "item_theme_id":"9010",\r\n        "item_page_view_id":"225",\r\n        "item_page_view_css_id":"",\r\n        "item_page_view_css_class":"",\r\n        "item_template_view_id":"1200",\r\n        "item_template_view_css_id":"",\r\n        "item_template_view_css_class":"",\r\n        "item_wrap_view_id":"2030",\r\n        "item_wrap_view_css_id":"",\r\n        "item_wrap_view_css_class":"",\r\n        "item_model_name":"Categories",\r\n        "item_model_type":"Table",\r\n        "item_model_query_object":"Item",\r\n\r\n        "form_theme_id":"9010",\r\n        "form_page_view_id":"210",\r\n        "form_page_view_css_id":"",\r\n        "form_page_view_css_class":"",\r\n        "form_template_view_id":"1260",\r\n        "form_template_view_css_id":"",\r\n        "form_template_view_css_class":"",\r\n        "form_wrap_view_id":"2030",\r\n        "form_wrap_view_css_id":"",\r\n        "form_wrap_view_css_class":"",\r\n        "form_model_name":"Categories",\r\n        "form_model_type":"Table",\r\n        "form_model_query_object":"Item",\r\n\r\n        "enable_draft_save":"1",\r\n        "enable_version_history":"1",\r\n        "enable_retain_versions_after_delete":"1",\r\n        "enable_maximum_version_count":"5",\r\n        "enable_hit_counts":"1",\r\n        "enable_comments":"1",\r\n        "enable_spam_protection":"1",\r\n        "enable_ratings":"1",\r\n        "enable_notifications":"1",\r\n        "enable_tweets":"1",\r\n        "enable_ping":"1",\r\n\r\n        "cache":"0",\r\n        "cache_time":"15",\r\n        "cache_handler":"file",\r\n\r\n        "log_user_activity_create":"1",\r\n        "log_user_activity_read":"1",\r\n        "log_user_activity_update":"1",\r\n        "log_user_activity_delete":"1",\r\n\r\n        "log_catalog_update_activity":"1",\r\n        "log_catalog_view_activity":"1",\r\n\r\n        "asset_priority_site":"100",\r\n        "asset_priority_application":"200",\r\n        "asset_priority_user":"300",\r\n        "asset_priority_extension":"400",\r\n        "asset_priority_request":"500",\r\n        "asset_priority_tag":"600",\r\n        "asset_priority_menu_item":"700",\r\n        "asset_priority_source":"800",\r\n        "asset_priority_theme":"900",\r\n\r\n        "image_xsmall":"50",\r\n        "image_small":"75",\r\n        "image_medium":"150",\r\n        "image_large":"300",\r\n        "image_xlarge":"500",\r\n        "image_folder":"Images",\r\n        "image_thumb_folder":"Thumbs",\r\n\r\n        "gravatar":"1",\r\n        "gravatar_size":"50",\r\n        "gravatar_type":"mm",\r\n        "gravatar_rating":"pg",\r\n        "gravatar_image":"1"\r\n\r\n    }\r\n}\r\n\r\n', '{"metadata_title":"Article 5", "metadata_description":"This is Article 5.", "metadata_keywords":"tag, content", "metadata_robots":"follow, index", "metadata_author":"Author Name", "metadata_content_rights":"CC"}', 'en-GB', 0, 5),
(NULL, 15, 11000, 'Style Guide', '', 'site', 'style-guide', '<p>Eum ex dico saepe tritani. Eripuit denique at nam, mei veniam accusata ad, vis ei wisi expetenda. An congue feugait vivendum his. Nisl munere platonem ad vis, ea timeam laoreet eos. Ea sea erat definiebas. At case nihil eleifend cum, labitur splendide voluptatum nec cu, alii esse dicant eu sed.</p>\n{readmore}\n<p>Qui solet vidisse principes ne, accusam mnesarchum nec ut. :) Vim possit aliquam ea. Et tibique qualisque sit, amet populo impetus eum et. Ad semper melius sed, oratio ridens vocibus cum ea, at vix suscipit mnesarchum intellegam.</p>\n\n<p>{mockimage}250,250,box{/mockimage}Mel aperiam praesent at, placerat tincidunt temporibus nec ut, admodum molestie necessitatibus duo eu. Vide fastidii forensibus ne ius, recusabo eleifend similique vim id. In persius argumentum pri, fierent sententiae eu sit. At nec enim libris. Ad mei habemus fierent luptatum.</p>', 0, 0, 0, 1, '2012-07-01 12:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-07-01 12:00:00', 1, '2012-07-01 12:00:00', 1, '0000-00-00 00:00:00', 0, 105, 0, 1, 8, 1, 0, '', '\r\n{\r\n    "2":{\r\n\r\n        "criteria_title":"Categories",\r\n        "criteria_display_view_on_no_results":"0",\r\n        "criteria_snippet_length":"200",\r\n        "criteria_status":"",\r\n        "criteria_content_catalog_type_id":"",\r\n        "criteria_menuitem_catalog_type_id":"",\r\n        "criteria_content_extension_instance_id":"",\r\n        "criteria_menuitem_extension_instance_id":"",\r\n\r\n        "mustache":"0",\r\n\r\n        "parent_menuid":"2140",\r\n\r\n        "item_theme_id":"9010",\r\n        "item_page_view_id":"225",\r\n        "item_page_view_css_id":"",\r\n        "item_page_view_css_class":"",\r\n        "item_template_view_id":"1200",\r\n        "item_template_view_css_id":"",\r\n        "item_template_view_css_class":"",\r\n        "item_wrap_view_id":"2030",\r\n        "item_wrap_view_css_id":"",\r\n        "item_wrap_view_css_class":"",\r\n        "item_model_name":"Categories",\r\n        "item_model_type":"Table",\r\n        "item_model_query_object":"Item",\r\n\r\n        "form_theme_id":"9010",\r\n        "form_page_view_id":"210",\r\n        "form_page_view_css_id":"",\r\n        "form_page_view_css_class":"",\r\n        "form_template_view_id":"1260",\r\n        "form_template_view_css_id":"",\r\n        "form_template_view_css_class":"",\r\n        "form_wrap_view_id":"2030",\r\n        "form_wrap_view_css_id":"",\r\n        "form_wrap_view_css_class":"",\r\n        "form_model_name":"Categories",\r\n        "form_model_type":"Table",\r\n        "form_model_query_object":"Item",\r\n\r\n        "enable_draft_save":"1",\r\n        "enable_version_history":"1",\r\n        "enable_retain_versions_after_delete":"1",\r\n        "enable_maximum_version_count":"5",\r\n        "enable_hit_counts":"1",\r\n        "enable_comments":"1",\r\n        "enable_spam_protection":"1",\r\n        "enable_ratings":"1",\r\n        "enable_notifications":"1",\r\n        "enable_tweets":"1",\r\n        "enable_ping":"1",\r\n\r\n        "cache":"0",\r\n        "cache_time":"15",\r\n        "cache_handler":"file",\r\n\r\n        "log_user_activity_create":"1",\r\n        "log_user_activity_read":"1",\r\n        "log_user_activity_update":"1",\r\n        "log_user_activity_delete":"1",\r\n\r\n        "log_catalog_update_activity":"1",\r\n        "log_catalog_view_activity":"1",\r\n\r\n        "asset_priority_site":"100",\r\n        "asset_priority_application":"200",\r\n        "asset_priority_user":"300",\r\n        "asset_priority_extension":"400",\r\n        "asset_priority_request":"500",\r\n        "asset_priority_tag":"600",\r\n        "asset_priority_menu_item":"700",\r\n        "asset_priority_source":"800",\r\n        "asset_priority_theme":"900",\r\n\r\n        "image_xsmall":"50",\r\n        "image_small":"75",\r\n        "image_medium":"150",\r\n        "image_large":"300",\r\n        "image_xlarge":"500",\r\n        "image_folder":"Images",\r\n        "image_thumb_folder":"Thumbs",\r\n\r\n        "gravatar":"1",\r\n        "gravatar_size":"50",\r\n        "gravatar_type":"mm",\r\n        "gravatar_rating":"pg",\r\n        "gravatar_image":"1"\r\n\r\n    }\r\n}\r\n\r\n', '{"metadata_title":"Article 5", "metadata_description":"This is Article 5.", "metadata_keywords":"tag, content", "metadata_robots":"follow, index", "metadata_author":"Author Name", "metadata_content_rights":"CC"}', 'en-GB', 0, 6);

INSERT INTO `molajo_catalog`(`id`, `catalog_type_id`, `source_id`, `routable`, `menuitem_type`, `sef_request`, `redirect_to_id`, `view_group_id`, `primary_category_id`, `tinyurl`)
SELECT NULL, `a`.`catalog_type_id`,  `a`.`id`,  `b`.`routable`, ' ', CONCAT(`b`.`slug`, '/', `a`.`alias`), 0, 1, `b`.`primary_category_id`, ' '
FROM molajo_content a,
molajo_catalog_types b
WHERE `a`.`catalog_type_id` = `b`.`id`
AND  `a`.`catalog_type_id` = '11000'

3. remove an extension

delete from `molajo_site_extension_instances` WHERE `extension_instance_id` IN (9010, 9050);

delete from `molajo_application_extension_instances` WHERE `extension_instance_id` IN (9010, 9050);

delete from `molajo_catalog` WHERE `source_id` IN (9010, 9020) AND catalog_type_id = 1500;

delete from `molajo_extension_instances` WHERE `id` IN (9010, 9050);

delete from `molajo_extensions` WHERE `id` IN (9010, 9050);

4. rebuild security

drop and recreate

CREATE TABLE `molajo_view_group_permissions` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`view_group_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_groups.id',
`catalog_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_catalog.id',
`action_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_actions.id',
PRIMARY KEY (`id`),
KEY `fk_view_group_permissions_view_groups_index` (`view_group_id`),
KEY `fk_view_group_permissions_actions_index` (`action_id`),
KEY `fk_view_group_permissions_catalog_index` (`catalog_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `molajo_view_group_permissions` (view_group_id, catalog_id, action_id)

SELECT b.view_group_id, c.id, c.id

FROM `molajo_content` a,
`molajo_group_view_groups` b,
`molajo_catalog` c,
`molajo_actions` d
WHERE a.`catalog_type_id` in (100, 120)
AND a.id = b.group_id
AND b.view_group_id = c.view_group_id
AND d.title = 'view'

Administrator Menu Items
SELECT `id`, `title`, `alias`,
`root`, `parent_id`, `lft`, `rgt`, `lvl`,
`ordering`

FROM `molajo_content`

where `extension_instance_id` = 100
order by parent_id, id


Menu items
SELECT  `id`, `catalog_type_id`, `title`, `subtitle`, `path`, `alias`, `root`, `parent_id`, `lft`, `rgt`, `lvl`, `home`, `parameters`, `ordering`

FROM `molajo_content`
where extension_instance_id = 100
order by lft

breadcrumbs

SELECT  a.`id`, a.`catalog_type_id`, a.`title`, a.`subtitle`, a.`path`, a.`alias`, a.`root`, a.`parent_id`, a.`lft`, a.`rgt`, a.`lvl`, a.`home`, a.`parameters`, a.`ordering`

FROM `molajo_content` a,
molajo_content b
where b.id = 136
and b.lft > a.lft
and b.rgt < a.rgt
order by lft



"   &quot;
'   &apos;
<   &lt;
>   &gt;
&   &amp;





INSERT INTO `molajo_extension_instances` (`id`, `extension_id`, `catalog_type_id`, `title`, `subtitle`, `path`, `alias`, `content_text`, `protected`, `featured`, `stickied`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `root`, `parent_id`, `lft`, `rgt`, `lvl`, `home`, `customfields`, `parameters`, `metadata`, `language`, `translation_of_id`, `ordering`) VALUES
(3610, 15, 1050, 'Install', '', 'install', '', '', 1, 0, 0, 1, '2012-07-01 12:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-07-01 12:00:00', 0, '2012-07-01 12:00:00', 0, '0000-00-00 00:00:00', 0, 15, 3600, 13, 14, 2, 0, '{}', '', NULL, 'en-GB', 0, 1),
(3612, 15, 1050, 'Update', '', 'update', '', '', 1, 0, 0, 1, '2012-07-01 12:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-07-01 12:00:00', 0, '2012-07-01 12:00:00', 0, '0000-00-00 00:00:00', 0, 15, 3600, 15, 16, 2, 0, '{}', '', NULL, 'en-GB', 0, 2),
(3614, 15, 1050, 'Services', '', 'services', '', '', 1, 0, 0, 1, '2012-07-01 12:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-07-01 12:00:00', 0, '2012-07-01 12:00:00', 0, '0000-00-00 00:00:00', 0, 15, 3600, 17, 18, 2, 0, '{}', '', NULL, 'en-GB', 0, 3),
(3616, 15, 1050, 'Plugins', '', 'plugins', '', '', 1, 0, 0, 1, '2012-07-01 12:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-07-01 12:00:00', 0, '2012-07-01 12:00:00', 0, '0000-00-00 00:00:00', 0, 15, 3600, 15, 16, 2, 0, '{}', '', NULL, 'en-GB', 0, 4),
(3618, 15, 1050, 'Sites', '', 'sites', '', '', 1, 0, 0, 1, '2012-07-01 12:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-07-01 12:00:00', 0, '2012-07-01 12:00:00', 0, '0000-00-00 00:00:00', 0, 15, 3600, 17, 18, 2, 0, '{}', '', NULL, 'en-GB', 0, 5),
(3620, 15, 1050, 'Applications', '', 'applications', '', '', 1, 0, 0, 1, '2012-07-01 12:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2012-07-01 12:00:00', 0, '2012-07-01 12:00:00', 0, '0000-00-00 00:00:00', 0, 15, 3600, 17, 18, 2, 0, '{}', '', NULL, 'en-GB', 0, 6);

INSERT INTO `molajo_catalog`(`id`, `catalog_type_id`, `source_id`, `routable`, `menuitem_type`, `sef_request`, `redirect_to_id`, `view_group_id`, `primary_category_id`, `tinyurl`)

