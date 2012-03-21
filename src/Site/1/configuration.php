<?php
Class SiteConfiguration
{
    /* Site Settings */
    public $site_name = 'Molajo';

    /* Database Settings */
    public $jdatabasefactory_dbtype = 'mysql';
    public $jdatabasefactory_host = 'localhost';
    public $jdatabasefactory_user = 'root';
    public $jdatabasefactory_password = 'root';
    public $jdatabasefactory_db = 'molajo';
    public $jdatabasefactory_dbprefix = 'molajo_';
    public $jdatabasefactory_namespace = 'Joomla\\database\\JDatabaseFactory';

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
    public $disable_sending = true;
    public $only_deliver_to = 'AmyStephen@gmail.com';
    public $mailer = 'mail';
    public $mail_from_email_address = '';
    public $mail_from_name = '';
    public $send_mail = '/usr/sbin/send_mail';
    public $smtpauth = '0';
    public $smtpuser = '';
    public $smtppass = '';
    public $smtphost = 'localhost';
    public $mail_class = 'JMail';

    /* Debug */
    public $debug = '1';
}
