<?php
class MolajoConfigApplication
{
    /* Application Settings */
    public $applicationname = 'Molajo';

    /* Meta */
    public $MetaDesc = 'Molajo - the Cats Meow';
    public $MetaKeys = 'molajo, Molajo';
    public $MetaAuthor = '1';

    /* Cache */
    public $caching = '0';
    public $cachetime = '15';
    public $cache_handler = 'file';

    /* SEO */
    public $sef = '1';
    public $sef_rewrite = '0';
    public $sef_suffix = '0';
    public $unicodeslugs = '0';
    public $force_ssl = '0';

    /* Locale */
    public $language = 'en-GB';
    public $offset = 'UTC';
    public $offset_user = 'UTC';
    public $multi_lingual = '0';

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

    public $default_view_static = 'dashboard';
    public $default_wrap_static = 'div';
    public $default_view_items = 'items';
    public $default_wrap_items = 'section';
    public $default_view_item = 'item';
    public $default_wrap_item = 'article';
    public $default_view_edit = 'edit';
    public $default_wrap_edit = 'div';

    /* Head */
    public $head_view = 'head';
    public $head_wrap = 'none';

    /* Message */
    public $message_view = 'messages';
    public $message_wrap = 'div';

    /* Offline */
    public $offline = '1';
    public $offline_message = 'This site is not available.<br /> Please check back again soon.';
    public $offline_format = 'html';
    public $offline_template = 'system';
    public $offline_page = 'offline';

    /* Error */
    public $error_format = 'html';
    public $error_template = 'system';
    public $error_page = 'error';

    /* Feed */
    public $feed_format = 'feed';
    public $feed_template = 'feed';
    public $feed_page = 'feed';
    public $feed_limit = 10;
    public $feed_email = 'author';
}
