<?php
class MolajoConfig
{
    /**             */
    /** Site        */
    /**             */
    public $site_name = 'Molajo';

    /* Database Settings */
    public $dbtype = 'mysqli';
    public $host = 'localhost';
    public $user = 'root';
    public $password = 'root';
    public $db = 'molajo';
    public $dbprefix = 'molajo_';

    /* Server Settings */
    public $secret = 'FBVtggIk5lAzEU9H';
    public $gzip = '0';
    public $error_reporting = '-1';
    public $helpurl = 'http://help.molajo.org/';
    public $ftp_host = '';
    public $ftp_port = '';
    public $ftp_user = '';
    public $ftp_pass = '';
    public $ftp_root = '';
    public $ftp_enable = '';
    public $cache_path = 'cache';
    public $logs_path = 'logs';
    public $temp_path = 'temp';
    public $temp_url = 'temp';
    public $media_path = 'media';
    public $media_url = 'media';

    /* Session settings */
    public $lifetime = '15';
    public $session_handler = 'none';
    public $cookie_domain = '';
    public $cookie_path = '';

    /* Mail Settings */
    public $mailer = 'mail';
    public $mail_from = '';
    public $from_name = '';
    public $send_mail = '/usr/sbin/send_mail';
    public $smtpauth = '0';
    public $smtpuser = '';
    public $smtppass = '';
    public $smtphost = 'localhost';

    /* Debug */
    public $debug = '0';
    public $debug_language = '0';

    /**             */
    /** Application */
    /**             */

    /* Application Settings */
    public $application_name = 'Molajo';

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
    public $default_page_view_id = 55;

    public $default_items_template_view_id = 21;
    public $default_items_wrap_view_id = 61;
    public $default_item_template_view_id = 21;
    public $default_item_wrap_view_id = 61;
    public $default_edit_template_view_id = 19;
    public $default_edit_wrap_view_id = 61;

    /* Head */
    public $head_template_view_id = 23;
    public $head_wrap_view_id = 66;
    public $defer_template_view_id = 22;
    public $defer_wrap_view_id = 66;

    /* Message */
    public $message_template_view_id = 58;
    public $message_wrap_view_id = 81;

    /* Offline */
    public $offline = 0;
    public $offline_message = 'This site is not available.<br /> Please check back again soon.';
    public $offline_theme_id = 99;
    public $offline_page_view_id = 81;

    /* Error */
    public $error_404_message = 'Page not found';
    public $error_403_message = 'Not authorised';
    public $error_theme_id = 99;
    public $error_page_view_id = 81;

    /* Feed */
    public $feed_theme_id = 99;
    public $feed_page_view_id = 81;
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

