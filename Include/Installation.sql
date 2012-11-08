/* Resource */

SET @extension_id = 1;
SET @extension_instance_id = 1;
SET @extension_catalog_type_id = 10000;
SET @extension_title = 'Articles';
SET @extension_alias = 'articles';
SET @datetime = '2012-08-30 12:00:00';
SET @zero_datetime = '0000-00-00 00:00:00';
SET @protected = 1;
SET @status = 1;
SET @by = 1;

INSERT INTO `molajo_extension_instances` (`id`, `extension_id`, `catalog_type_id`, `title`, `subtitle`, `path`, `alias`, `content_text`, `protected`, `featured`, `stickied`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `root`, `parent_id`, `lft`, `rgt`, `lvl`, `home`, `customfields`, `parameters`, `metadata`, `language`, `translation_of_id`, `ordering`)
  VALUES (@extension_instance_id, @extension_id, @extension_catalog_type_id, @extension_title, '', '', '', '', @protected, 0, 0, @status, @datetime, @zero_datetime, @by, 0, 0, @datetime, @by, @datetime, @by, @zero_datetime, 0, 0, 0, 0, 0, 0, 0, '{}', '{}', '{}', 'en-GB', 0, 1);


  '
  WHERE extension_instance_id = @extension_id;



/* Grid */
SET @grid_extension_id = nn;
SET @grid_extension_instance_id = nn;
SET @grid_catalog_type_id = 1300;
SET @grid_menuitem_title = 'Articles';
SET @grid_menuitem_alias = 'articles';
SET @datetime = '2012-08-30 12:00:00';
SET @zero_datetime = '0000-00-00 00:00:00';
SET @protected = 1;
SET @status = 1;
SET @by = 1;
SET @root = 3000;
SET @parent = 3010;
SET @lft = 3;
SET @rgt = 4;
SET @lvl = 3;
SET @home = 0;

INSERT INTO `molajo_extension_instances`
  (`id`, `extension_id`, `catalog_type_id`, `title`, `subtitle`, `path`, `alias`, `content_text`, `protected`, `featured`, `stickied`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `root`, `parent_id`, `lft`, `rgt`, `lvl`, `home`, `customfields`, `parameters`, `metadata`, `language`, `translation_of_id`, `ordering`)
  VALUES (@grid_extension_instance_id, @grid_extension_id, @grid_catalog_type_id, @extension_title, '', @grid_menuitem_alias, '', '', 1, 0, 0, 1, @datetime, @zero_datetime, @by, 0, 0, @datetime, @by, @datetime, @by, @zero_datetime, 0, @root, @parent, @lft, @rgt, @lvl, @home, '{}', '{}', '{}', 'en-GB', 0, 1);

UPDATE `molajo_extension_instances`
SET parameters =
' '
WHERE extension_instance_id = @grid_extension_instance_id;



/* Configuration */
SET @configuration_extension_instance_id = 0;
SET @configuration_extension_id = 0;
SET @menuitem_catalog_type_id = 1300;
SET @configuration_title = '';
SET @configuration_link = 'articles/configuration';
SET @datetime = '2012-08-30 12:00:00';

INSERT INTO `molajo_extension_instances` (`id`, `extension_id`, `catalog_type_id`, `title`, `subtitle`, `path`, `alias`, `content_text`, `protected`, `featured`, `stickied`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `root`, `parent_id`, `lft`, `rgt`, `lvl`, `home`, `customfields`, `parameters`, `metadata`, `language`, `translation_of_id`, `ordering`)
  VALUES (@configuration_extension_instance_id, @configuration_extension_id, @menuitem_catalog_type_id, @configuration_title,', '', @configuration_link, '', '', 1, 0, 0, 1, @datetime, @zero_datetime, 1, 0, 0, @datetime, 1, @datetime, 1, @zero_datetime, 0, 3000, 3012, 3, 4, 4, 0, '{}', '{}', '{}', 'en-GB', 0, 1);


SET @configuration_extension_id = 2;
SET @configuration_extension_instance_id = 2;
SET @configuration_extension_catalog_type_id = 12000;
SET @configuration_title = 'Audio Configuration';
SET @configuration_alias = 'audio/configuration';


/*  Configuration */
SET @configuration_extension_id = 3000;
SET @configuration_extension_instance_id = @configuration_extension_instance_id + 20000;
SET @configuration_catalog_type_id = 1300;
SET @configuration_subtitle = '';
SET @datetime = '2012-08-30 12:00:00';
SET @zero_datetime = '0000-00-00 00:00:00';
SET @parent = 3012;
SET @protected = 1;
SET @status = 1;
SET @by = 1;
SET @root = 3000;
SET @lft = 3;
SET @rgt = 4;
SET @lvl = 4;
SET @home = 0;

SET @parameters = CONCAT('
{
    "1":{},
    "2":{
"configuration_class":"mobile",
"configuration_namespace":"config",
"configuration_array":"{{Basic,basic}}{{Customfields,customfields}}{{Metadata,metadata}}{{Form,form}}{{Item,item}}{{List,list}}{{Editor,editor}}{{Grid,grid}}",
"configuration_tab":"",
"configuration_basic":"{{Basics,criteria*}}{{Enable Features,enable*}}",
"configuration_customfields":"{{Create,create}}{{Delete,delete}}{{Customfields,customfields*}}",
"page_type":"Configuration",
"configuration_metadata":"{{Metadata,metadata*}}",
"configuration_form":"{{Page,form_parent*,form_theme*,form_page*}}{{Template,form_template*}}{{Wrap,form_wrap*}}{{Model,form_model*}}",
"configuration_item":"{{Page,item_parent*,item_theme*,item_page*}}{{Template,item_template*}}{{Wrap,item_wrap*}}{{Model,item_model*}}",
"configuration_list":"{{Page,list_parent*,list_theme*,list_page*}}{{Template,list_template*}}{{Wrap,list_wrap*}}{{Model,list_model*}}",
"configuration_editor":"{{Main,editor_toolbar*,editor_main*,editor_secondary*}}{{Publish,editor_publish*}}{{Permissions,editor_permission*}}{{SEO,editor_seo*}}",
"configuration_grid":"{{Grid,grid_toolbar*,grid_stat*,grid_ordering*}}{{Lists,grid_list*}}{{Columns,grid_col*}}{{Batch,grid_batch*}}",

"menuitem_source_id":"",
"menuitem_source_catalog_type_id":"', @configuration_extension_catalog_type_id, '",
"menuitem_extension_instance_id":"', @configuration_extension_instance_id, '",

"menuitem_theme_id":"9020",
"menuitem_page_view_id":"205",
"menuitem_page_view_css_id":"",
"menuitem_page_view_css_class":"",
"menuitem_template_view_id":"1015",
"menuitem_template_view_css_id":"",
"menuitem_template_view_css_class":"",
"menuitem_wrap_view_id":"2100",
"menuitem_wrap_view_css_id":"",
"menuitem_wrap_view_css_class":"",
"menuitem_wrap_view_role":"",
"menuitem_wrap_view_property":"",
"menuitem_model_name":"Plugindata",
"menuitem_model_type":"dbo",
"menuitem_model_query_object":"getPlugindata",
"menuitem_model_parameter":"Adminconfiguration",
"menuitem_model_offset":"0",
"menuitem_model_count":"0",
"menuitem_model_use_pagination":"1",

"criteria_title":"', @configuration_title, '",
"criteria_display_view_on_no_results":"0",
"criteria_snippet_length":"150",
"criteria_status":"1,2",
"criteria_catalog_type_id":"', @configuration_extension_catalog_type_id, '",
"criteria_extension_instance_id":"', @configuration_extension_instance_id, '",

"cache":"0",
"cache_time":"0",
"cache_handler":"file",

"editor_page_array":"{{Main,main}}{{Publish,publish}}{{Permissions,permissions}}{{SEO,seo}}",
		"editor_page_main":"{{Main,editor_toolbar*,editor_main*,editor_secondary*}}",
		"editor_page_publish":"{{Publish,editor_publish*}}",
		"editor_page_permissions":"{{Permissions,editor_permission*}}",
		"editor_page_seo":"{{SEO,editor_seo*}}"


}
}
');

SET @metadata = CONCAT('
{"title":"', @configuration_title, '",
"description":"",
"keywords":"",
"robots":"follow, index",
"author":"",
"content_rights":""}');

INSERT INTO `molajo_extension_instances`
  (`id`, `extension_id`, `catalog_type_id`, `title`, `subtitle`, `path`, `alias`, `content_text`, `protected`, `featured`, `stickied`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `root`, `parent_id`, `lft`, `rgt`, `lvl`, `home`, `customfields`, `parameters`, `metadata`, `language`, `translation_of_id`, `ordering`)
  VALUES (@configuration_extension_instance_id, @configuration_extension_id, @configuration_catalog_type_id, @configuration_title, @configuration_subtitle, @configuration_alias, '', '', 1, 0, 0, 1, @datetime, @zero_datetime, @by, 0, 0, @datetime, @by, @datetime, @by, @zero_datetime, 0, @root, @parent, @lft, @rgt, @lvl, @home, '{}', @parameters, @metadata, 'en-GB', 0, 1);





INSERT INTO `molajo_catalog`( `catalog_type_id`, `source_id`, `routable`, `page_type`, `sef_request`,
`redirect_to_id`, `view_group_id`, `primary_category_id`, `tinyurl`)

SELECT a.catalog_type_id, a.id, 1, 'Configuration', path, 0, 1, 1001, '' FROM `molajo_extension_instances` a
where id > 20000



















/*  Dashboard */
SET @dashboard_extension_id = nn;
SET @dashboard_extension_instance_id = nn;
SET @dashboard_catalog_type_id = 1300;
SET @dashboard_title = 'Resources';
SET @dashboard_subtitle = 'icon-pencil';
SET @dashboard_alias = 'resources';
SET @datetime = '2012-08-30 12:00:00';
SET @zero_datetime = '0000-00-00 00:00:00';
SET @protected = 1;
SET @status = 1;
SET @by = 1;
SET @root = 3000;
SET @parent = 3010;
SET @lft = 3;
SET @rgt = 4;
SET @lvl = 3;
SET @home = 0;

INSERT INTO `molajo_extension_instances`
  (`id`, `extension_id`, `catalog_type_id`, `title`, `subtitle`, `path`, `alias`, `content_text`, `protected`, `featured`, `stickied`, `status`, `start_publishing_datetime`, `stop_publishing_datetime`, `version`, `version_of_id`, `status_prior_to_version`, `created_datetime`, `created_by`, `modified_datetime`, `modified_by`, `checked_out_datetime`, `checked_out_by`, `root`, `parent_id`, `lft`, `rgt`, `lvl`, `home`, `customfields`, `parameters`, `metadata`, `language`, `translation_of_id`, `ordering`)
  VALUES (@dashboard_extension_instance_id, @dashboard_extension_id, @dashboard_catalog_type_id, @dashboard_title, @dashboard_subtitle, @dashboard_alias, '', '', 1, 0, 0, 1, @datetime, @zero_datetime, @by, 0, 0, @datetime, @by, @datetime, @by, @zero_datetime, 0, @root, @parent, @lft, @rgt, @lvl, @home, '{}', '{}', '{}', 'en-GB', 0, 1);

UPDATE `molajo_extension_instances`
SET parameters = '
{
}
'
WHERE extension_instance_id = @dashboard_extension_instance_id;

UPDATE `molajo_extension_instances`
SET metadata =
' '
WHERE extension_instance_id = @dashboard_extension_instance_id;


