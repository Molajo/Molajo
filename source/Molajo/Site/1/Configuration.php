<?php
Class SiteConfiguration
{
	/* Site Settings */
	public $site_name = 'Site 2';

	/* JDatabaseDriver Settings */
	public $jdatabase_dbtype = 'mysqli';
	public $jdatabase_host = 'localhost';
	public $jdatabase_user = 'root';
	public $jdatabase_password = 'root';
	public $jdatabase_db = 'molajo';
	public $jdatabase_dbprefix = 'molajo_';
	public $jdatabase_debug = '0';

	/** disable html filters */
	public $disable_filter_for_groups = '4';

	/* Server Settings */
	public $secret = 'FBVtggIk5lAzEU9H';
	public $gzip = '0';
	public $error_reporting = '-1';
	public $cache_path = 'cache';
	public $logs_path = 'logs';
	public $temp_path = 'temp';
	public $temp_url = 'temp';
	public $media_path = 'media';
	public $media_url = 'media';

	/* FTP Settings */
	public $ftp_host = '';
	public $ftp_port = '';
	public $ftp_user = '';
	public $ftp_pass = '';
	public $ftp_root = '';

	/* Session settings */
	public $lifetime = '15';
	public $session_handler = 'file';
	public $cookie_domain = '';
	public $cookie_path = '';

	/* Mail Settings */
	public $mailer = 'mail';
	public $mailer_mode = 'text';
	public $disable_sending = 0;
	public $only_deliver_to = 'AmyStephen@gmail.com,Amy Stephen';
	public $mail_from = 'AmyStephen@gmail.com,From Amy Stephen';
	public $mail_reply_to = 'AmyStephen@gmail.com,Reply to Amy Stephen';
	/* mailer: mail */
	public $send_mail = '/usr/sbin/send_mail';
	/* mailer: smtp */
	public $smtpauth = '0';
	public $smtpuser = '';
	public $smtppass = '';
	public $smtphost = 'localhost';

	/* Debug Logging */
	public $profiler_log = 'profiler_log';
	/* Text */
	public $profiler_text_file = 'profiler.php';
	public $profiler_text_file_path = 'SITE_LOGS_FOLDER';
	public $profiler_text_file_no_php = false;
	/* Database */
	public $profiler_database_table = '#__log';
	/* Messages */
	public $profiler_messages_namespace = 'profiler';
	/* Email */
	public $profiler_email_subject = 'Debug Messages';
	public $profiler_email_to = 'AmyStephen@gmail.com';

}
