<?php
class MolajoSiteConfiguration
{
    /* Site Settings */
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
    public $media_path = 'media';

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
}
