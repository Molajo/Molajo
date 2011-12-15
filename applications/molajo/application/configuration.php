<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * MolajoConfiguration
 *
 * @package     Molajo
 * @subpackage  Application
 * @since       1.0
 */
class MolajoConfiguration
{

    /**
     * Application Configuration Object
     *
     * @var    object
     * @since  1.0
     */
    public static $application;

    /**
     * Site Configuration Object
     *
     * @var    object
     * @since  1.0
     */
    public static $site;

    /**
     * Combined Site and Application Configuration Object
     *
     * @var    object
     * @since  1.0
     */
    public static $config;

    /**
     * get
     *
     * Retrieves and combines site and application configuration objects
     *
     * Returns the global configuration object, creating it
     * if it doesn't already exist.
     *
     * @return configuration object
     * @throws RuntimeException
     * @since  1.0
     */
    public static function getConfig()
    {
        if (self::$config) {
            return self::$config;
        }

        /** Combined */
        self::$config = self::_createConfig();

        /** Site Config */
        self::site();

        /* Site */
        self::$config->set('offline', self::$site->get('offline', '0'));
        self::$config->set('offline_message', self::$site->get('offline_message', 'This site is not available.<br /> Please check back again soon.'));
        self::$config->set('sitename', self::$site->get('sitename', 'Molajo'));

        /* Database */
        self::$config->set('dbtype', self::$site->get('dbtype', 'mysqli'));
        self::$config->set('host', self::$site->get('host', 'localhost'));
        self::$config->set('user', self::$site->get('user', ''));
        self::$config->set('password', self::$site->get('password', ''));
        self::$config->set('db', self::$site->get('db', ''));
        self::$config->set('dbprefix', self::$site->get('dbprefix', ''));

        /* Server */
        self::$config->set('secret', self::$site->get('secret', ''));
        self::$config->set('gzip', self::$site->get('gzip', '0'));
        self::$config->set('error_reporting', self::$site->get('error_reporting', '-1'));
        self::$config->set('helpurl', self::$site->get('helpurl', 'http://help.molajo.org'));
        self::$config->set('ftp_host', self::$site->get('ftp_host', ''));
        self::$config->set('ftp_port', self::$site->get('ftp_port', ''));
        self::$config->set('ftp_user', self::$site->get('ftp_user', ''));
        self::$config->set('ftp_pass', self::$site->get('ftp_pass', ''));
        self::$config->set('ftp_root', self::$site->get('ftp_root', ''));
        self::$config->set('ftp_enable', self::$site->get('ftp_enable', ''));
        self::$config->set('cache_path', self::$site->get('cache_path', ''));
        self::$config->set('images_path', self::$site->get('images_path', ''));
        self::$config->set('logs_path', self::$site->get('logs_path', ''));
        self::$config->set('media_path', self::$site->get('media_path', ''));
        self::$config->set('tmp_path', self::$site->get('tmp_path', ''));
        self::$config->set('live_site', self::$site->get('live_site', ''));

        /* Session */
        self::$config->set('lifetime', self::$site->get('lifetime', 'none'));
        self::$config->set('session_handler', self::$site->get('session_handler', 'database'));

        self::$config->set('mailer', self::$site->get('mailer', 'mail'));
        self::$config->set('mail_from', self::$site->get('mailfrom', ''));
        self::$config->set('fromname', self::$site->get('fromname', ''));
        self::$config->set('sendmail', self::$site->get('sendmail', '/usr/sbin/sendmail'));
        self::$config->set('smtpauth', self::$site->get('smtpauth', '0'));
        self::$config->set('smtpuser', self::$site->get('smtpuser', ''));
        self::$config->set('smtppass', self::$site->get('smtppass', ''));
        self::$config->set('smtphost', self::$site->get('smtphost', ''));

        /* Debug */
        self::$config->set('debug', self::$site->get('debug', '0'));
        self::$config->set('debug_language', self::$site->get('debug_language', '0'));

        /** App Config */
        self::application();

        self::$config->set('caching', self::$application->get('caching', '0'));
        self::$config->set('cachetime', self::$application->get('cachetime', '15'));
        self::$config->set('cache_handler', self::$application->get('cache_handler', 'file'));

        self::$config->set('MetaDesc', self::$application->get('MetaDesc', 'Molajo'));
        self::$config->set('MetaKeys', self::$application->get('MetaKeys', 'molajo, Molajo'));
        self::$config->set('MetaAuthor', self::$application->get('MetaAuthor', '1'));

        /* SEF */
        self::$config->set('sef', self::$application->get('sef', '1'));
        self::$config->set('sef_rewrite', self::$application->get('sef_rewrite', '0'));
        self::$config->set('sef_suffix', self::$application->get('sef_suffix', '0'));
        self::$config->set('unicodeslugs', self::$application->get('unicodeslugs', '0'));
        self::$config->set('force_ssl', self::$application->get('force_ssl', ''));

        /* User */
        self::$config->set('editor', self::$application->get('editor', 'none'));
        self::$config->set('access', self::$application->get('access', '1'));

        /* Language */
        self::$config->set('language', self::$application->get('language', 'en-GB'));
        self::$config->set('offset', self::$application->get('offset', 'UTC'));
        self::$config->set('offset_user', self::$application->get('offset_user', 'UTC'));

        /* Feed */
        self::$config->set('feed_limit', self::$application->get('feed_limit', '10'));
        self::$config->set('feed_email', self::$application->get('feed_email', 'site'));
        self::$config->set('list_limit', self::$application->get('list_limit', '20'));

        /* Access */
        self::$config->set('application_logon_requirement', self::$application->get('application_logon_requirement', '1'));
        self::$config->set('application_guest_option', self::$application->get('application_guest_option', 'login'));
        self::$config->set('application_default_option', self::$application->get('application_default_option', 'dashboard'));
        self::$config->set('default_template_extension_id', self::$application->get('default_template_extension_id', '209'));

        /* Application */
        self::$config->set('html5', self::$application->get('html5', '1'));
        self::$config->set('image_xsmall', self::$application->get('image_xsmall', '50'));
        self::$config->set('image_small', self::$application->get('image_small', '75'));
        self::$config->set('image_medium', self::$application->get('image_medium', '150'));
        self::$config->set('image_large', self::$application->get('image_large', '300'));
        self::$config->set('image_xlarge', self::$application->get('image_xlarge', '500'));
        self::$config->set('image_folder', self::$application->get('image_folder', 'images'));
        self::$config->set('thumb_folder', self::$application->get('thumb_folder', 'thumbs'));

        return self::$config;
    }

    /**
     * site
     *
     * retrieve site configuration object
     *
     * @return bool
     * @throws RuntimeException
     * @since  1.0
     */
    public static function site()
    {
        if (self::$site) {
            return self::$site;
        }

        $file = MOLAJO_SITE_PATH . '/configuration.php';
        if (file_exists($file)) {
            require_once $file;
        } else {
            throw new RuntimeException('Fatal error - Application-Site Configuration File does not exist');
        }

        self::$site = new MolajoConfigSite();

        return self::$site;
    }

    /**
     * application
     *
     * retrieve application configuration object
     *
     * @return bool
     * @throws RuntimeException
     * @since  1.0
     */
    protected function _application()
    {
        if (self::$application) {
            return self::$application;
        }

        $file = MOLAJO_APPLICATION_PATH . '/configuration.php';
        if (file_exists($file)) {
            require_once $file;
        } else {
            throw new RuntimeException('Fatal error - Application Configuration File does not exist');
        }

        self::$application = new MolajoConfigApplication();

        return true;
    }

    /**
     * _createConfig
     *
     * Create a configuration object that will store the combined Site and Application objects
     *
     * @return bool
     * @throws RuntimeException
     * @since   1.0
     */
    protected static function _createConfig()
    {
        if (self::$config) {
            return self::$config;
        }

        $file = MOLAJO_APPLICATIONS_CORE . '/configuration.php';
        if (is_file($file)) {
            include_once $file;
        } else {
            throw new RuntimeException('Fatal error - Configuration File does not exist '.$file);
        }

        self::$config = new MolajoConfig();

        return true;
    }

    /**
     * get
     *
     * Returns a property of the Application object
     * or the default value if the property is not set.
     *
     * @param   string  $key      The name of the property.
     * @param   mixed   $default  The default value (optional) if none is set.
     *
     * @return  mixed   The value of the configuration.
     *
     * @since   11.3
     */
    public function get($key, $default = null)
    {
        return $this->config->get($key, $default);
    }

    /**
     * set
     *
     * Modifies a property of the Application object, creating it if it does not already exist.
     *
     * @param   string  $key    The name of the property.
     * @param   mixed   $value  The value of the property to set (optional).
     *
     * @return  mixed   Previous value of the property
     *
     * @since   11.3
     */
    public function set($key, $value = null)
    {
        $previous = $this->config->get($key);
        $this->config->set($key, $value);

        return $previous;
    }
}
