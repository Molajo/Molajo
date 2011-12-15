<?php
class MolajoConfig
{

    /**
     *  Application Configuration
     */

    /* Cache Settings */
    public $caching = '';
    public $cachetime = '';
    public $cache_handler = '';

    /* Meta Settings */
    public $MetaDesc = '';
    public $MetaKeys = '';
    public $MetaAuthor = '';

    /* SEO Settings */
    public $sef = '1';
    public $sef_rewrite = '0';
    public $sef_suffix = '0';
    public $unicodeslugs = '0';
    public $force_ssl = '0';

    /* User Defaults */
    public $editor = 'none';
    public $access = '1';

    /* Application Access */
    public $application_logon_requirement = '';
    public $application_guest_option = '';
    public $application_default_option = '';
    public $default_template_extension = '';

    /* Locale Settings */
    public $language = 'en-GB';
    public $offset = 'UTC';
    public $offset_user = 'UTC';

    /* Debug Settings */
    public $debug = '0';
    public $debug_language = '0';

    /* Feed Settings */
    public $feed_limit = '10';
    public $feed_email = 'site';
    public $list_limit = '20';

    /* Other */
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
    public $offline;
    public $offline_message;
    public $sitename;

    /* Database Settings */
    public $dbtype;
    public $host;
    public $user;
    public $password;
    public $db;
    public $dbprefix;

    /* Server Settings */
    public $secret;
    public $gzip;
    public $error_reporting;
    public $helpurl;
    public $ftp_host;
    public $ftp_port;
    public $ftp_user;
    public $ftp_pass;
    public $ftp_root;
    public $ftp_enable;
    public $cache_path;
    public $images_path;
    public $logs_path;
    public $media_path;
    public $tmp_path;
    public $live_site;

    /* Session settings */
    public $lifetime;
    public $session_handler;

    /* Mail Settings */
    public $mailer;
    public $mailfrom;
    public $fromname;
    public $sendmail;
    public $smtpauth;
    public $smtpuser;
    public $smtppass;
    public $smtphost;
}
