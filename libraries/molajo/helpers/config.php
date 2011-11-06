<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * MolajoConfigHelper
 *
 * @package     Molajo
 * @subpackage  Config Helper
 * @since       1.0
 */
class MolajoConfigHelper
{
    /**
     * Merges Site and Application configuration data
     *
     * @static
     * @param $config
     * @param $siteConfig
     * @param $appConfig
     * @return void
     */
    public static function populateConfig($config, $siteConfig, $appConfig)
    {
        /** App Config */
        $config->set('caching', $appConfig->get('caching', '0'));
        $config->set('cachetime', $appConfig->get('cachetime', '15'));
        $config->set('cache_handler', $appConfig->get('cache_handler', 'file'));

        $config->set('MetaDesc', $appConfig->get('MetaDesc', 'Molajo'));
        $config->set('MetaKeys', $appConfig->get('MetaKeys', 'molajo, Molajo'));
        $config->set('MetaAuthor', $appConfig->get('MetaAuthor', '1'));

        $config->set('sef', $appConfig->get('sef', '1'));
        $config->set('sef_rewrite', $appConfig->get('sef_rewrite', '0'));
        $config->set('sef_suffix', $appConfig->get('sef_suffix', '0'));
        $config->set('unicodeslugs', $appConfig->get('unicodeslugs', '0'));

        $config->set('application_logon_requirement', $appConfig->get('application_logon_requirement', '1'));
        $config->set('application_guest_option', $appConfig->get('application_guest_option', 'com_login'));
        $config->set('application_default_option', $appConfig->get('application_default_option', 'com_dashboard'));
        $config->set('default_template_extension_id', $appConfig->get('default_template_extension_id', '209'));

        /** Site Config */
        $config->set('offline', $siteConfig->get('offline', '0'));
        $config->set('offline_message', $siteConfig->get('offline_message', 'This site is not available.<br /> Please check back again soon.'));
        $config->set('sitename', $siteConfig->get('sitename', 'Molajo'));
        $config->set('editor', $siteConfig->get('editor', 'none'));
        $config->set('list_limit', $siteConfig->get('list_limit', '20'));
        $config->set('access', $siteConfig->get('access', '1'));

        $config->set('dbtype', $siteConfig->get('dbtype', 'mysqli'));
        $config->set('host', $siteConfig->get('host', 'localhost'));
        $config->set('user', $siteConfig->get('user', ''));
        $config->set('password', $siteConfig->get('password', ''));
        $config->set('db', $siteConfig->get('db', ''));
        $config->set('dbprefix', $siteConfig->get('dbprefix', ''));

        $config->set('secret', $siteConfig->get('secret', ''));
        $config->set('gzip', $siteConfig->get('gzip', '0'));
        $config->set('error_reporting', $siteConfig->get('error_reporting', '-1'));
        $config->set('helpurl', $siteConfig->get('helpurl', 'http://help.molajo.org'));

        $config->set('ftp_host', $siteConfig->get('ftp_host', ''));
        $config->set('ftp_port', $siteConfig->get('ftp_port', ''));
        $config->set('ftp_user', $siteConfig->get('ftp_user', ''));
        $config->set('ftp_pass', $siteConfig->get('ftp_pass', ''));
        $config->set('ftp_root', $siteConfig->get('ftp_root', ''));
        $config->set('ftp_enable', $siteConfig->get('ftp_enable', ''));

        $config->set('cache_path', $siteConfig->get('cache_path', ''));
        $config->set('images_path', $siteConfig->get('images_path', ''));
        $config->set('logs_path', $siteConfig->get('logs_path', ''));
        $config->set('media_path', $siteConfig->get('media_path', ''));
        $config->set('tmp_path', $siteConfig->get('tmp_path', ''));
        $config->set('live_site', $siteConfig->get('live_site', ''));
        $config->set('force_ssl', $siteConfig->get('force_ssl', ''));

        $config->set('offset', $siteConfig->get('offset', 'UTC'));
        $config->set('offset_user', $siteConfig->get('offset_user', 'UTC'));

        $config->set('lifetime', $siteConfig->get('lifetime', 'none'));
        $config->set('session_handler', $siteConfig->get('session_handler', 'database'));

        $config->set('mailer', $siteConfig->get('mailer', 'mail'));
        $config->set('mail_from', $siteConfig->get('mailfrom', ''));
        $config->set('fromname', $siteConfig->get('fromname', ''));
        $config->set('sendmail', $siteConfig->get('sendmail', '/usr/sbin/sendmail'));
        $config->set('smtpauth', $siteConfig->get('smtpauth', '0'));
        $config->set('smtpuser', $siteConfig->get('smtpuser', ''));
        $config->set('smtppass', $siteConfig->get('smtppass', ''));
        $config->set('smtphost', $siteConfig->get('smtphost', ''));

        $config->set('debug', $siteConfig->get('debug', '0'));
        $config->set('debug_lang', $siteConfig->get('debug_lang', '0'));

        $config->set('feed_limit', $siteConfig->get('feed_limit', '10'));
        $config->set('feed_email', $siteConfig->get('feed_email', 'author'));

        $config->set('html5', $siteConfig->get('html5', '1'));
        $config->set('image_xsmall', $siteConfig->get('image_xsmall', '50'));
        $config->set('image_small', $siteConfig->get('image_small', '75'));
        $config->set('image_medium', $siteConfig->get('image_medium', '150'));
        $config->set('image_large', $siteConfig->get('image_large', '300'));
        $config->set('image_xlarge', $siteConfig->get('image_xlarge', '500'));
        $config->set('image_folder', $siteConfig->get('image_folder', 'images'));
        $config->set('thumb_folder', $siteConfig->get('thumb_folder', 'thumbs'));
    }
}