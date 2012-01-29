<?php
class MolajoConfigApplication
{
    /* Application Settings */
    public $application_name = 'Molajo Site';

    /* Cache */
    public $caching = '0';
    public $cache_time = '15';
    public $cache_handler = 'file';

    /* SEO */
    public $sef = '1';
    public $sef_rewrite = '1';
    public $sef_suffix = '0';
    public $unicode_slugs = '0';
    public $force_ssl = '0';

    /* Locale */
    public $language = 'en-GB';
    public $offset = 'UTC';
    public $offset_user = 'UTC';
    public $multilingual = '0';

    /* Lists */
    public $list_limit = 20;

    /* HTML5 */
    public $html5 = '1';

    /* Media */
    public $image_xsmall = '50';
    public $image_small = '75';
    public $image_medium = '150';
    public $image_large = '300';
    public $image_xlarge = '500';
    public $image_folder = 'images';
    public $thumb_folder = 'thumbs';

    /* User Defaults */
    public $editor = 'none';

    /* ACL */
    public $view_access = '1';

    /* Home */
    public $home_asset_id = 139;

    /* Logon Requirement */
    public $logon_requirement = 139;

    /* Theme and View Defaults */
    public $default_theme_id = 98;
    public $default_view_page_id = 55;

    public $default_items_view_template_id = 21;
    public $default_items_view_wrap_id = 61;
    public $default_item_view_template_id = 21;
    public $default_item_view_wrap_id = 61;
    public $default_edit_view_template_id = 19;
    public $default_edit_view_wrap_id = 61;

    /* Head */
    public $head_view_template_id = 23;
    public $head_view_wrap_id = 66;
    public $defer_view_template_id = 22;
    public $defer_view_wrap_id = 66;

    /* Message */
    public $message_view_template_id = 58;
    public $message_view_wrap_id = 81;

    /* Offline */
    public $offline = 0;
    public $offline_message = 'This site is not available.<br /> Please check back again soon.';
    public $offline_theme_id = 99;
    public $offline_view_page_id = 81;

    /* Error */
    public $error_404_message = 'Page not found';
    public $error_403_message = 'Not authorised';
    public $error_theme_id = 99;
    public $error_view_page_id = 81;

    /* Feed */
    public $feed_theme_id = 99;
    public $feed_view_page_id = 81;
    public $feed_limit = 10;
    public $feed_email = 'author';

    /* Priority */
    public $media_priority_site = 100;
    public $media_priority_application = 200;
    public $media_priority_user = 300;
    public $media_priority_other_extension = 400;
    public $media_priority_request_extension = 500;
    public $media_priority_theme = 600;
    public $media_priority_primary_category = 700;
    public $media_priority_menu_item = 800;
    public $media_priority_source_data = 900;
}
