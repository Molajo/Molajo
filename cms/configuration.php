<?php
class MolajoConfigExtension
{
    /* Page */

    public $url_current;
    public $url_base;

    public $site;
    public $application;
    public $template;
    public $template_layout;
    public $user;

    public $title;
    public $subtitle;
    public $metadata;
    public $position;

    public $page_mvc_controller;
    public $page_mvc_option;
    public $page_mvc_task;
    public $page_mvc_view;
    public $page_mvc_model;
    public $page_mvc_format;
    public $page_mvc_redirect_success;
    public $page_mvc_redirect_failure;

    public $page_extension;
    public $page_extension_type;
    public $page_extension_layout;
    public $page_extension_layout_wrap;
    public $page_extension_layout_wrap_id;
    public $page_extension_layout_wrap_class;

    public $page_extension_menu_item;
    public $page_extension_primary_category;
    public $page_extension_plugin_type;
    public $page_extension_item_id;
    public $page_extension_acl;
    public $page_extension_table;
    public $page_extension_path;
    public $page_filter_name;
    public $page_select_name;

    public $page_component_specific;

  $request['application_id'] = $session->get('page.application_id');
  $request['current_url'] = $session->get('page.current_url');
  $request['component_path'] = $session->get('page.component_path');
  $request['base_url'] = $session->get('page.base_url');
  $request['item_id'] = $session->get('page.item_id');

  $request['controller'] = $session->get('page.controller');
  $request['extension_type'] = $session->get('page.extension_type');
  $request['option'] = $session->get('page.option');
  $request['view'] = $session->get('page.view');
  $request['layout'] = $session->get('page.layout');
  $request['wrap'] = $session->get('page.wrap');
  $request['wrap_id'] = $session->get('page.wrap_id');
  $request['wrap_class'] = $session->get('page.wrap_class');

  $request['model'] = $session->get('page.model');
  $request['task'] = $session->get('page.task');
  $request['format'] = $session->get('page.format');
  $request['plugin_type'] = $session->get('page.plugin_type');

  $request['id'] = $session->get('page.id');
  $request['cid'] = $session->get('page.cid');
  $request['catid'] = $session->get('page.catid');
  $request['parameters'] = $session->get('page.parameters');
  $request['extension'] = $session->get('page.extension');
  $request['component_specific'] = $session->get('page.component_specific');

  $request['acl_implementation'] = $session->get('page.acl_implementation');
  $request['component_table'] = $session->get('page.component_table');
  $request['filter_name'] = $session->get('page.filter_name');
  $request['select_name'] = $session->get('page.select_name');

  $request['title'] = $session->get('page.title');
  $request['subtitle'] = $session->get('page.subtitle');
  $request['metakey'] = $session->get('page.metakey');
  $request['metadesc'] = $session->get('page.metadesc');
  $request['metadata'] = $session->get('page.metadata');
  $request['position'] = $session->get('page.position');

  $request['wrap_title'] = $request['title'];
  $request['wrap_subtitle'] = $request['subtitle'];
  $request['wrap_date'] = '';
  $request['wrap_author'] = '';
  $request['wrap_more_array'] = array();
}
