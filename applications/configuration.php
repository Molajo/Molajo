<?php
class MolajoConfig
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

    /* Access */
    public $application_logon_requirement = '1';
    public $application_guest_option = 'login';
    public $application_default_option = 'dashboard';
    public $default_template_extension = 'Molajito';

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

    /**
     *  Site Configuration Settings
     */
    /* Site Settings */
    public $sitename = 'Molajo';

    /* Offline Settings */
    public $offline = '0';
    public $offline_message = 'This site is not available.<br /> Please check back again soon.';

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
