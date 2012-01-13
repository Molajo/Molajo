<?php
class MolajoConfigApplication
{
    /* Application Settings */
    public $application_name = 'Molajo';

    /* Meta */
    public $metadata_description = 'Molajo - the Cats Meow';
    public $metadata_keywords = 'molajo, Molajo';
    public $metadata_author = '1';
    public $metadata_content_rights = 'This work is licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.';
    public $metadata_robots = 'follow, index';

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
    public $home_asset_id = '267';

    /* Logon Requirement */
    public $logon_requirement = '267';

    /* Template and View Defaults */
    public $default_format = 'html';
    public $default_template = 'molajito';
    public $default_page = 'full';

    public $default_static_view = 'dashboard';
    public $default_static_wrap = 'div';
    public $default_items_view = 'items';
    public $default_items_wrap = 'section';
    public $default_item_view = 'item';
    public $default_item_wrap = 'article';
    public $default_edit_view = 'edit';
    public $default_edit_wrap = 'div';

    /* Head */
    public $head_view = 'head';
    public $head_wrap = 'none';
    public $defer_view = 'defer';
    public $defer_wrap = 'none';

    /* Message */
    public $message_view = 'messages';
    public $message_wrap = 'div';

    /* Offline */
    public $offline = '1';
    public $offline_message = 'This site is not available.<br /> Please check back again soon.';
    public $offline_template = 'system';
    public $offline_page = 'offline';

    /* Error */
    public $error_format = 'html';
    public $error_page = 'error';

    /* Feed */
    public $feed_format = 'feed';
    public $feed_template = 'feed';
    public $feed_page = 'feed';
    public $feed_limit = 10;
    public $feed_email = 'author';
}
