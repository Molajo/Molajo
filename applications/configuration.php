<?php
class MolajoConfig
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
    public $home_asset_id = '267';

    /* Logon Requirement */
    public $logon_requirement = '267';

    /* Template and View Defaults */
    public $default_format = 'html';
    public $default_template = 'molajito';
    public $default_page = 'full';
    public $default_layout_static = 'dashboard';
    public $default_wrap_static = 'div';
    public $default_layout_items = 'items';
    public $default_wrap_items = 'section';
    public $default_layout_item = 'item';
    public $default_wrap_item = 'article';
    public $default_layout_edit = 'edit';
    public $default_wrap_edit = 'div';

    /* Offline */
    public $offline = '1';
    public $offline_message = 'This site is not available.<br /> Please check back again soon.';
    public $offline_format = 'static';
    public $offline_template = 'system';
    public $offline_page = 'full';
    public $offline_layout = 'offline';
    public $offline_wrap = 'div';
    public $offline_asset_id = '0';

    /* Error */
    public $error_format = 'static';
    public $error_template = 'system';
    public $error_page = 'full';
    public $error_layout = 'error';
    public $error_wrap = 'div';
    public $error_asset_id = '0';

    /* Feed */
    public $feed_format = 'feed';
    public $feed_template = 'system';
    public $feed_page = 'full';
    public $feed_layout = 'feed';
    public $feed_wrap = 'div';
    public $feed_asset_id = '0';
    public $feed_limit = 10;
    public $feed_email = 'author';

    /**
     *  Site Configuration Settings
     */

    /* Site Settings */
    public $sitename = 'Molajo';

    /* Database Settings */
    public $dbtype = 'mysqli'; // Normally mysql
    public $host = 'localhost'; // This is normally set to localhost
    public $user = 'root'; // MySQL username
    public $password = 'root'; // MySQL password
    public $db = 'molajo'; // MySQL database name
    public $dbprefix = 'molajo_'; // Prefix change to something unique

    /* Server Settings */
    public $secret = 'FBVtggIk5lAzEU9H'; // Change this to something more secure
    public $gzip = '0';
    public $error_reporting = '-1';
    public $helpurl = 'http://help.molajo.org/';
    public $ftp_host = '';
    public $ftp_port = '';
    public $ftp_user = '';
    public $ftp_pass = '';
    public $ftp_root = '';
    public $ftp_enable = '';
    public $cache_path = '/Users/amystephen/Sites/Molajo/sites/1/cache';
    public $logs_path = '/Users/amystephen/Sites/Molajo/sites/1/logs';
    public $temp_path = '/Users/amystephen/Sites/Molajo/sites/1/temp';
    public $media_path = '/Users/amystephen/Sites/Molajo/sites/1/media';
    public $media_uri_path = 'sites/1/media';
    public $live_site = ''; // Optional, Full url to installation.

    /* Session settings */
    public $lifetime = '15';
    public $session_handler = 'database';
    public $cookie_domain = '';
    public $cookie_path = '';

    /* Mail Settings */
    public $mailer = 'mail';
    public $mailfrom = '';
    public $fromname = '';
    public $sendmail = '/usr/sbin/sendmail';
    public $smtpauth = '0';
    public $smtpuser = '';
    public $smtppass = '';
    public $smtphost = 'localhost';

    /* Debug */
    public $debug = '0';
    public $debug_language = '0';
}
