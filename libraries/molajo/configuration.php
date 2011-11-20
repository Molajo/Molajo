<?php
class MolajoConfig {

/**
 *  Application Configuration
 */
	/* Cache Settings */
	public $caching;
	public $cachetime;
	public $cache_handler;

	/* Meta Settings */
	public $MetaDesc;
	public $MetaKeys;
	public $MetaAuthor;

	/* SEO Settings */
	public $sef;
	public $sef_rewrite;
	public $sef_suffix;
	public $unicodeslugs;

    /* Application Access */
    public $application_logon_requirement;
    public $application_guest_option;
    public $application_default_option;
    public $default_template_extension_id;

/**
 *  Site Configuration Settings
 */
	/* Site Settings */
	public $offline;
	public $offline_message;
	public $sitename;
	public $editor;
	public $list_limit;
	public $access;

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
	public $force_ssl;

	/* Locale Settings */
	public $offset;
	public $offset_user;

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

	/* Debug Settings */
	public $debug;
	public $debug_language;

	/* Feed Settings */
	public $feed_limit;
	public $feed_email;

	/* Other */
    public $html5;
    public $image_xsmall;
    public $image_small;
    public $image_medium;
    public $image_large;
    public $image_xlarge;
    public $image_folder;
    public $thumb_folder;
}
