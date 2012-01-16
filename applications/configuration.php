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
    public $application_name = 'Molajo';

    /** Meta */
    public $metadata_description = 'Molajo - the Cats Meow';
    public $metadata_keywords = 'molajo, Molajo';
    public $metadata_author = 'Person Name';
    public $metadata_content_rights = 'Creative Commons Attribution-ShareAlike 3.0 Unported License';
    public $metadata_robots = 'follow, index';

    /** Cache */
    public $caching = '0';
    public $cache_time = '15';
    public $cache_handler = 'file';

    /** SEO */
    public $sef = '1';
    public $sef_rewrite = '0';
    public $sef_suffix = '0';
    public $unicode_slugs = '0';
    public $force_ssl = '0';

    /** Locale */
    public $language = 'en-GB';
    public $offset = 'UTC';
    public $offset_user = 'UTC';
    public $multilingual = '0';

    /** Lists */
    public $list_limit = 20;

    /** HTML5 */
    public $html5 = '1';

    /** Media */
    public $image_xsmall = '50';
    public $image_small = '75';
    public $image_medium = '150';
    public $image_large = '300';
    public $image_xlarge = '500';
    public $image_folder = 'images';
    public $thumb_folder = 'thumbs';

    /** User Defaults */
    public $editor = 'none';

    /** ACL */
    public $view_access = '1';

    /** Home */
    public $home_asset_id = '267';

    /** Logon Requirement */
    public $logon_requirement = '267';

    /** Template and View Defaults */
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

    /** Head */
    public $head_view = 'head';
    public $head_wrap = 'none';
    public $defer_view = 'defer';
    public $defer_wrap = 'none';

    /** Message */
    public $message_view = 'messages';
    public $message_wrap = 'div';

    /** Offline */
    public $offline = '0';
    public $offline_message = 'This site is not available.<br /> Please check back again soon.';
    public $offline_template = 'system';
    public $offline_page = 'offline';

    /** Error */
    public $error_404_message = 'Page not found';
    public $error_403_message = 'Not authorised';
    public $error_template = 'system';
    public $error_page = 'error';

    /** Feed */
    public $feed_format = 'feed';
    public $feed_template = 'feed';
    public $feed_page = 'feed';
    public $feed_limit = 10;
    public $feed_email = 'author';

    /** Priority */
    public $media_priority_site = 100;
    public $media_priority_application = 200;
    public $media_priority_user = 300;
    public $media_priority_module = 400;
    public $media_priority_plugin = 400;
    public $media_priority_component = 500;
    public $media_priority_template = 600;
    public $media_priority_primary_category = 700;
    public $media_priority_menu_item = 800;
    public $media_priority_source_data = 900;
}
