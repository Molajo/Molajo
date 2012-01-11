<?php
class MolajoConfigApplication
{
    /* Application Settings */

    public $application_name = 'Molajo';

    /* Meta */
    public $metadata_description = 'Molajo - the Cats Meow';
    public $metadata_keywords = 'molajo, Molajo';
    public $metadata_author = '1';
    
    /* Cache */
    public $caching = '0';
    public $cache_time = '15';
    public $cache_handler = 'file';
    
    /* SEO */
    public $sef = '1';
    public $sef_rewrite = '0';
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

    /* Media */
    public $html5 = '1';
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
    public $home_asset_id = '296';

    /* Logon Requirement */
    public $logon_requirement = '0';

    /* Template and View Defaults */
    public $default_format = 'html';
    public $default_template = 'cleanslate';
    public $default_page = 'full';
    public $default_static_view = 'dashboard';
    public $default_static_wrap = 'div';
    public $default_items_view = 'items';
    public $default_items_wrap = 'section';
    public $default_item_view = 'item';
    public $default_item_wrap = 'article';
    public $default_edit_view = 'edit';
    public $default_edit_wrap = 'div';

    /* Offline */
    public $offline = '1';
    public $offline_message = 'This site is not available.<br /> Please check back again soon.';
    public $offline_format = 'static';
    public $offline_template = 'system';
    public $offline_page = 'full';
    public $offline_view = 'offline';
    public $offline_wrap = 'div';
    public $offline_asset_id = '0';

    /* Error */
    public $error_format = 'static';
    public $error_template = 'system';
    public $error_page = 'full';
    public $error_view = 'error';
    public $error_wrap = 'div';
    public $error_asset_id = '0';

    /* Feed */
    public $feed_format = 'feed';
    public $feed_template = 'system';
    public $feed_page = 'full';
    public $feed_view = 'feed';
    public $feed_wrap = 'div';
    public $feed_asset_id = '0';
    public $feed_limit = 10;
    public $feed_email = 'author';
}
