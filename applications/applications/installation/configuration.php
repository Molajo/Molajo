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
    public $logon_requirement = '0';
    public $not_logged_on_redirect_asset_id = '0';
    public $default_template_name = 'molajito';
    public $application_home_asset_id = 0;
    public $application_offline = '0';
    public $application_offline_asset_id = 0;
    public $application_error_asset_id = 0;

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
