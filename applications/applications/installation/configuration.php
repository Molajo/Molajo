<?php
class MolajoConfigApplication
{

    /* Cache */
    public $caching = '0';
    public $cachetime = '15';
    public $cache_handler = 'file';

    /* Meta */
    public $MetaDesc = 'Molajo - the Cats Meow';
    public $MetaKeys = 'molajo, Molajo';
    public $MetaAuthor = '1';

    /* SEO */
    public $sef = '1';
    public $sef_rewrite = '0';
    public $sef_suffix = '0';
    public $unicodeslugs = '0';
    public $force_ssl = '0';

    /* User Defaults */
    public $editor = 'none';
    public $access = '1';

    /* Home and Logon Requirements */
    public $logon_requirement = '1';
    public $not_logged_on_redirect_asset_id = '0';
    public $application_home_asset_id = 296;
    public $application_error_asset_id = 0;

    public $offline_template = 'system';
    public $offline_template_page = 'full';
    public $offline_layout = 'offline';
    public $offline_wrap = 'div';

    public $error_template = 'system';
    public $error_template_page = 'full';
    public $error_layout = 'error';
    public $error_wrap = 'div';

    public $default_format = 'html';
    public $default_template_name = 'molajito';
    public $default_template_page = 'full';
    public $default_layout_static = 'page';
    public $default_wrap_static = 'div';
    public $default_layout_items = 'items';
    public $default_wrap_items = 'section';
    public $default_layout_item = 'item';
    public $default_wrap_item = 'article';
    public $default_layout_edit = 'edit';
    public $default_wrap_edit = 'div';

    /* Locale */
    public $language = 'en-GB';
    public $offset = 'UTC';
    public $offset_user = 'UTC';

    /* Feed */
    public $feed_limit = 10;
    public $feed_email = 'author';
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
}
