<?php
Class SiteConfiguration
{
    /* Site Settings */
    public $site_name = 'Molajo';

    /* JDatabaseFactory Settings */
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

    /* Session settings */
    public $lifetime = '15';
    public $session_handler = 'database';
    public $cookie_domain = '';
    public $cookie_path = '';

    /* Mail Settings */
    public $disable_sending = 0;
    public $only_deliver_to = 'AmyStephen@gmail.com';
    public $mailer = 'mail';
    public $mail_from_email_address = 'AmyStephen@gmail.com';
    public $mail_from_name = 'Amy Stephen';
    public $send_mail = '/usr/sbin/send_mail';
    public $smtpauth = '0';
    public $smtpuser = '';
    public $smtppass = '';
    public $smtphost = 'localhost';
    public $mail_class = 'JMail';
}
