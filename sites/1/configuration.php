<?php
class MolajoConfigSite {

	/* Site Settings */
	public $offline = '0';
	public $offline_message = 'This site is not available.<br /> Please check back again soon.';
	public $sitename = 'Molajo';
	public $editor = 'none';
	public $list_limit = '20';
	public $access = '1';

	/* Database Settings */
	public $dbtype = 'mysqli';					// Normally mysql
	public $host = 'localhost';					// This is normally set to localhost
	public $user = 'root';						// MySQL username
	public $password = 'root';					// MySQL password
	public $db = 'molajo';						// MySQL database name
	public $dbprefix = 'molajo_';				// Prefix change to something unique

	/* Server Settings */
	public $secret = 'FBVtggIk5lAzEU9H'; 		// Change this to something more secure
	public $gzip = '0';
	public $error_reporting = '-1';
	public $helpurl = 'http://help.molajo.org/';
	public $ftp_host = '';
	public $ftp_port = '';
	public $ftp_user = '';
	public $ftp_pass = '';
	public $ftp_root = '';
	public $ftp_enable = '';
	public $cache_path = '/Users/amystephen/Sites/amy/1/cache';
	public $images_path = '/Users/amystephen/Sites/amy/1/images';
	public $logs_path = '/Users/amystephen/Sites/amy/1/logs';
	public $media_path = '/Users/amystephen/Sites/amy/1/media';
	public $tmp_path = '/Users/amystephen/Sites/amy/1/tmp';
	public $live_site = ''; 					// Optional, Full url to Joomla install.
	public $force_ssl = 0;						// Force areas of the site to be SSL ONLY.  0 = None, 1 = Administrator, 2 = Both Site and Administrator

	/* Locale Settings */
	public $offset = 'UTC';
	public $offset_user = 'UTC';

	/* Session settings */
	public $lifetime = '15';
	public $session_handler = 'database';

	/* Mail Settings */
	public $mailer = 'mail';
	public $mailfrom = '';
	public $fromname = '';
	public $sendmail = '/usr/sbin/sendmail';
	public $smtpauth = '0';
	public $smtpuser = '';
	public $smtppass = '';
	public $smtphost = 'localhost';

	/* Debug Settings */
	public $debug = '0';
	public $debug_lang = '0';

	/* Feed Settings */
	public $feed_limit = 10;
	public $feed_email = 'author';

	/* Other */
    public $html5 = '1';
    public $image_xsmall = '50';
    public $image_small = '75';
    public $image_medium = '150';
    public $image_large = '300';
    public $image_xlarge = '500';
    public $image_folder = 'images';
    public $thumb_folder = 'thumbs';
}
