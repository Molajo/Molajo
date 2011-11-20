<?php
/**
 * @package     Molajo
 * @subpackage  Factory
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Joomla Framework Factory class
 *
 * @package Joomla.Platform
 * @since   11.1
 */
abstract class MolajoFactory
{
    public static $site = null;
    public static $application = null;
    public static $cache = null;
    public static $config = null;
    public static $siteConfig = null;
    public static $appConfig = null;
    public static $session = null;
    public static $language = null;
    public static $document = null;
    public static $acl = null;
    public static $database = null;
    public static $mailer = null;

    /**
     * Get a site object
     *
     * Returns the global Site object
     *
     * @param   mixed   $id     Site identifier or name
     * @param   array   $config Optional associative array of configuration settings
     * @param   string  $prefix Site prefix
     *
     * @return application    object
     */
    public static function getSite($id = null, $config = array(), $prefix = 'Molajo')
    {
        if (self::$site) {
        } else {
            if ($id) {
            } else {
                MolajoError::raiseError(500, 'Site Instantiation Error');
            }
            self::$site = MolajoSite::getInstance($id, $config, $prefix);
        }

        return self::$site;
    }

    /**
     * Get an application object
     *
     * Returns the global Application object, only creating it
     * if it doesn't already exist.
     *
     * @param   mixed   $id     Application identifier or name
     * @param   array   $config Optional associative array of configuration settings
     * @param   string  $prefix Application prefix
     *
     * @return application    object
     */
    public static function getApplication($id = null, $config = array(), $prefix = 'Molajo')
    {
        if (self::$application) {
        } else {
            if ($id) {
            } else {
                MolajoError::raiseError(500, 'Application Instantiation Error');
            }
            self::$application = MolajoApplication::getInstance($id, $config, $prefix);
        }

        return self::$application;
    }

    /**
     * Get the combined site and application configuration object
     *
     * Returns the global configuration object, creating it
     * if it doesn't already exist.
     *
     * @param string $file Path to the configuration file
     * @param string $type Type of the configuration file
     *
     * @return configuration object
     */
    public static function getConfig($file = null, $type = 'PHP')
    {
        if (self::$config) {
            return self::$config;
        }

        if ($file === null) {
            $file = MOLAJO_LIBRARY.'/configuration.php';
        }
        self::$config = self::_createConfig($file, $type, '');

        /** Site Config */
        self::getSiteConfig();

        /* Site */
        self::$config->set('offline', self::$siteConfig->get('offline', '0'));
        self::$config->set('offline_message', self::$siteConfig->get('offline_message', 'This site is not available.<br /> Please check back again soon.'));
        self::$config->set('sitename', self::$siteConfig->get('sitename', 'Molajo'));

        /* Database */
        self::$config->set('dbtype', self::$siteConfig->get('dbtype', 'mysqli'));
        self::$config->set('host', self::$siteConfig->get('host', 'localhost'));
        self::$config->set('user', self::$siteConfig->get('user', ''));
        self::$config->set('password', self::$siteConfig->get('password', ''));
        self::$config->set('db', self::$siteConfig->get('db', ''));
        self::$config->set('dbprefix', self::$siteConfig->get('dbprefix', ''));

        /* Server */
        self::$config->set('secret', self::$siteConfig->get('secret', ''));
        self::$config->set('gzip', self::$siteConfig->get('gzip', '0'));
        self::$config->set('error_reporting', self::$siteConfig->get('error_reporting', '-1'));
        self::$config->set('helpurl', self::$siteConfig->get('helpurl', 'http://help.molajo.org'));
        self::$config->set('ftp_host', self::$siteConfig->get('ftp_host', ''));
        self::$config->set('ftp_port', self::$siteConfig->get('ftp_port', ''));
        self::$config->set('ftp_user', self::$siteConfig->get('ftp_user', ''));
        self::$config->set('ftp_pass', self::$siteConfig->get('ftp_pass', ''));
        self::$config->set('ftp_root', self::$siteConfig->get('ftp_root', ''));
        self::$config->set('ftp_enable', self::$siteConfig->get('ftp_enable', ''));
        self::$config->set('cache_path', self::$siteConfig->get('cache_path', ''));
        self::$config->set('images_path', self::$siteConfig->get('images_path', ''));
        self::$config->set('logs_path', self::$siteConfig->get('logs_path', ''));
        self::$config->set('media_path', self::$siteConfig->get('media_path', ''));
        self::$config->set('tmp_path', self::$siteConfig->get('tmp_path', ''));
        self::$config->set('live_site', self::$siteConfig->get('live_site', ''));

        /* Session */
        self::$config->set('lifetime', self::$siteConfig->get('lifetime', 'none'));
        self::$config->set('session_handler', self::$siteConfig->get('session_handler', 'database'));

        self::$config->set('mailer', self::$siteConfig->get('mailer', 'mail'));
        self::$config->set('mail_from', self::$siteConfig->get('mailfrom', ''));
        self::$config->set('fromname', self::$siteConfig->get('fromname', ''));
        self::$config->set('sendmail', self::$siteConfig->get('sendmail', '/usr/sbin/sendmail'));
        self::$config->set('smtpauth', self::$siteConfig->get('smtpauth', '0'));
        self::$config->set('smtpuser', self::$siteConfig->get('smtpuser', ''));
        self::$config->set('smtppass', self::$siteConfig->get('smtppass', ''));
        self::$config->set('smtphost', self::$siteConfig->get('smtphost', ''));

        /* Debug */
        self::$config->set('debug', self::$siteConfig->get('debug', '0'));
        self::$config->set('debug_language', self::$siteConfig->get('debug_language', '0'));
        
        /** App Config */
        self::getApplicationConfig();

        self::$config->set('caching', self::$appConfig->get('caching', '0'));
        self::$config->set('cachetime', self::$appConfig->get('cachetime', '15'));
        self::$config->set('cache_handler', self::$appConfig->get('cache_handler', 'file'));

        self::$config->set('MetaDesc', self::$appConfig->get('MetaDesc', 'Molajo'));
        self::$config->set('MetaKeys', self::$appConfig->get('MetaKeys', 'molajo, Molajo'));
        self::$config->set('MetaAuthor', self::$appConfig->get('MetaAuthor', '1'));

        /* SEF */
        self::$config->set('sef', self::$appConfig->get('sef', '1'));
        self::$config->set('sef_rewrite', self::$appConfig->get('sef_rewrite', '0'));
        self::$config->set('sef_suffix', self::$appConfig->get('sef_suffix', '0'));
        self::$config->set('unicodeslugs', self::$appConfig->get('unicodeslugs', '0'));
        self::$config->set('force_ssl', self::$appConfig->get('force_ssl', ''));

        /* User */
        self::$config->set('editor', self::$appConfig->get('editor', 'none'));
        self::$config->set('access', self::$appConfig->get('access', '1'));

        /* Language */
        self::$config->set('language', self::$appConfig->get('language', 'en-GB'));
        self::$config->set('offset', self::$appConfig->get('offset', 'UTC'));
        self::$config->set('offset_user', self::$appConfig->get('offset_user', 'UTC'));

        /* Feed */
        self::$config->set('feed_limit', self::$appConfig->get('feed_limit', '10'));
        self::$config->set('feed_email', self::$appConfig->get('feed_email', 'site'));
        self::$config->set('list_limit', self::$appConfig->get('list_limit', '20'));

        /* Access */
        self::$config->set('application_logon_requirement', self::$appConfig->get('application_logon_requirement', '1'));
        self::$config->set('application_guest_option', self::$appConfig->get('application_guest_option', 'com_login'));
        self::$config->set('application_default_option', self::$appConfig->get('application_default_option', 'com_dashboard'));
        self::$config->set('default_template_extension_id', self::$appConfig->get('default_template_extension_id', '209'));

        /* Application */
        self::$config->set('html5', self::$appConfig->get('html5', '1'));
        self::$config->set('image_xsmall', self::$appConfig->get('image_xsmall', '50'));
        self::$config->set('image_small', self::$appConfig->get('image_small', '75'));
        self::$config->set('image_medium', self::$appConfig->get('image_medium', '150'));
        self::$config->set('image_large', self::$appConfig->get('image_large', '300'));
        self::$config->set('image_xlarge', self::$appConfig->get('image_xlarge', '500'));
        self::$config->set('image_folder', self::$appConfig->get('image_folder', 'images'));
        self::$config->set('thumb_folder', self::$appConfig->get('thumb_folder', 'thumbs'));

        return self::$config;
    }

    /**
     * Get the Site configuration object
     *
     * Returns the global configuration object, creating it
     * if it doesn't already exist.
     *
     * @param string $file Path to the configuration file
     * @param string $type Type of the configuration file
     *
     * @return configuration object
     */
    public static function getSiteConfig($file = null, $type = 'PHP')
    {
        if (self::$siteConfig) {
        } else {
            if ($file === null) {
                $file = MOLAJO_SITE_PATH.'/configuration.php';
            }
            self::$siteConfig = self::_createConfig($file, $type, 'Site');
        }

        return self::$siteConfig;
    }
    /**
     * Get the Application configuration object
     *
     * Returns the global configuration object, creating it
     * if it doesn't already exist.
     *
     * @param string $file Path to the configuration file
     * @param string $type Type of the configuration file
     *
     * @return configuration object
     */
    public static function getApplicationConfig($file = null, $type = 'PHP')
    {
        if (self::$appConfig) {
        } else {
            if ($file === null) {
               if (defined('MOLAJO_APPLICATION_PATH')) {
                   $file = MOLAJO_APPLICATION_PATH.'/configuration.php';
                } else {
                   $file = MOLAJO_APPLICATIONS_PATH.'/'.MOLAJO_APPLICATION;
                }                
            }
            self::$appConfig = self::_createConfig($file, $type, 'Application');
        }

        return self::$appConfig;
    }


    /**
     * Get a session object
     *
     * Returns the global session object, creating it
     * if it doesn't already exist.
     *
     * @param   array  $options  An array containing session options
     *
     * @return session object
     */
    public static function getSession($options = array())
    {
        if (self::$session) {
        } else {
            self::$session = self::_createSession($options);
        }

        return self::$session;
    }

    /**
     * Get a language object
     *
     * Returns the global language object, creating it
     * if it doesn't already exist.
     *
     * @return language object
     */
    public static function getLanguage()
    {
        if (self::$language) {
        } else {
            self::$language = self::_createLanguage();
        }

        return self::$language;
    }

    /**
     * Get a document object
     *
     * Returns the global document object
     *
     * @return document object
     */
    public static function getDocument()
    {
        if (self::$document) {
        } else {
            self::$document = self::_createDocument();
        }

        return self::$document;
    }

    /**
     * Get an user object
     *
     * Returns the global user object
     *
     * @param   integer  $id  The user to load - Can be an integer or string -
     *          If string, it is converted to ID automatically.
     *
     * @see MolajoUser
     *
     * @return user object
     */
    public static function getUser($id = null)
    {
        $id = 'admin';
        if (is_null($id)) {
            $instance = self::getSession()->get('user');

            if (($instance instanceof MolajoUser)) {
            } else {
                $instance = MolajoUser::getInstance();
            }
        } else {
            $instance = MolajoUser::getInstance($id);
        }
//echo '<pre>';var_dump($instance);'</pre>';
        return $instance;
    }

    /**
     * Get a cache object
     *
     * Returns the global cache object
     *
     * @param   string  $group    The cache group name
     * @param   string  $handler  The handler to use
     * @param   string  $storage  The storage method
     *
     * @return  cache object
     *
     * @see     JCache
     */
    public static function getCache($group = '', $handler = 'callback', $storage = null)
    {
        $hash = md5($group.$handler.$storage);
        if (isset(self::$cache[$hash])) {
            return self::$cache[$hash];
        }
        $handler = ($handler == 'function') ? 'callback' : $handler;

        $conf = self::getConfig();

        $options = array('defaultgroup' => $group);

        if (isset($storage)) {
            $options['storage'] = $storage;
        }

        $cache = JCache::getInstance($handler, $options);

        self::$cache[$hash] = $cache;

        return self::$cache[$hash];
    }

    /**
     * Get an authorization object
     *
     * Returns the global {@link JACL} object, only creating it
     * if it doesn't already exist.
     *
     * @deprecated
     */
    public static function getACL() {}

    /**
     * Get a database object
     *
     * Returns the global database object
     *
     * @return JDatabase object
     */
    public static function getDbo()
    {
        if (self::$database) {
        } else {
            $conf = self::getConfig();
            $debug = $conf->get('debug');

            self::$database = self::_createDbo();
            self::$database->debug($debug);
        }

        return self::$database;
    }

    /**
     * Get a mailer object
     *
     * Returns the global mail object
     *
     * @see MolajoMail
     *
     * @return mail object
     */
    public static function getMailer()
    {
        if (self::$mailer) {
        } else {
            self::$mailer = self::_createMailer();
        }
        $copy = clone self::$mailer;

        return $copy;
    }

    /**
     * Get a parsed XML Feed Source
     *
     * @param   string   $url         url for feed source
     * @param   integer  $cache_time  time to cache feed for (using internal cache mechanism)
     *
     * @return  mixed  SimplePie parsed object on success, false on failure
     * @since   1.0
     */
    public static function getFeedParser($url, $cache_time = 0)
    {
        $cache = self::getCache('feed_parser', 'callback');

        if ($cache_time > 0) {
            $cache->setLifeTime($cache_time);
        }

        $simplepie = new SimplePie(null, null, 0);

        $simplepie->enable_cache(false);
        $simplepie->set_feed_url($url);
        $simplepie->force_feed(true);

        $contents = $cache->get(array($simplepie, 'init'), null, false, false);

        if ($contents) {
            return $simplepie;
        }
        else {
            MolajoError::raiseWarning('SOME_ERROR_CODE', MolajoText::_('MOLAJO_UTIL_ERROR_LOADING_FEED_DATA'));
        }

        return false;
    }

    /**
     * Get an XML document
     *
     * @param   string  $type     The type of XML parser needed 'DOM', 'RSS' or 'Simple'
     * @param   array   $options  ['rssUrl'] the rss url to parse when using "RSS", ['cache_time'] with 'RSS' - feed cache time. If not defined defaults to 3600 sec
     *
     * @return  object  Parsed XML document object
     * @deprecated
     */
    public static function getXMLParser($type = '', $options = array())
    {
        $doc = null;

        switch (strtolower($type))
        {
            case 'rss' :
            case 'atom' :
                $cache_time = isset($options['cache_time']) ? $options['cache_time'] : 0;
                $doc = self::getFeedParser($options['rssUrl'], $cache_time);
                break;

            case 'dom':
                MolajoError::raiseWarning('SOME_ERROR_CODE', MolajoText::_('MOLAJO_UTIL_ERROR_DOMIT'));
                $doc = null;
                break;

            default :
                $doc = null;
        }

        return $doc;
    }

    /**
     * Reads a XML file.
     *
     * @param   string  $data   Full path and file name.
     * @param   boolean  $isFile true to load a file | false to load a string.
     *
     * @return  mixed    JXMLElement on success | false on error.
     * @todo This may go in a separate class - error reporting may be improved.
     */
    public static function getXML($data, $isFile = true)
    {
        // Disable libxml errors and allow to fetch error information as needed
        libxml_use_internal_errors(true);

        if ($isFile) {
            // Try to load the XML file
            $xml = simplexml_load_file($data, 'SimpleXMLElement');
        } else {
            // Try to load the XML string
            $xml = simplexml_load_string($data, 'SimpleXMLElement');
        }

        if (empty($xml)) {
            // There was an error
            MolajoError::raiseWarning(100, MolajoText::_('MOLAJO_UTIL_ERROR_XML_LOAD'));

            if ($isFile) {
                MolajoError::raiseWarning(100, $data);
            }

            foreach (libxml_get_errors() as $error)
            {
                MolajoError::raiseWarning(100, 'XML: '.$error->message);
            }
        }

        return $xml;
    }

    /**
     * Get an editor object
     *
     * @param   string  $editor The editor to load, depends on the editor plugins that are installed
     *
     * @return editor object
     */
    public static function getEditor($editor = null)
    {
        if (is_null($editor)) {
            $conf = self::getConfig();
            $editor = $conf->get('editor');
        }
        return MolajoEditor::getInstance($editor);
    }

    /**
     * Return a reference to the URI object
     *
     * @param   string  $uri uri name
     *
     * @see JURI
     *
     * @return JURI object
     * @since   1.0
     */
    public static function getURI($uri = 'SERVER')
    {
        return JURI::getInstance($uri);
    }

    /**
     * Return the {@link JDate} object
     *
     * @param   mixed  $time     The initial time for the JDate object
     * @param   mixed  $tzOffset The timezone offset.
     *
     * @see JDate
     *
     * @return JDate object
     * @since   1.0
     */
    public static function getDate($time = 'now', $tzOffset = null)
    {
        static $instances;
        static $classname;
        static $mainLocale;

        if (!isset($instances)) {
            $instances = array();
        }

        $language = self::getLanguage();
        $locale = $language->getTag();

        if (!isset($classname) || $locale != $mainLocale) {
            $mainLocale = $locale;

            if ($mainLocale !== false) {
                $classname = str_replace('-', '_', $mainLocale).'Date';

                if (class_exists($classname)) {
                } else {
                    $classname = 'JDate';
                }
            } else {
                $classname = 'JDate';
            }
        }
        $key = $time.'-'.$tzOffset;

        $tmp = new $classname($time, $tzOffset);
        return $tmp;
    }

    /**
     * Create a configuration object
     *
     * @param   string  $file       The path to the configuration file.
     * @param   string  $type       The type of the configuration file.
     * @param   string  $namespace  The namespace of the configuration file.
     *
     * @return  JRegistry
     *
     * @since   1.0
     */
    protected static function _createConfig($file, $type = 'PHP', $namespace = '')
    {
        if (is_file($file)) {
            include_once $file;
        }

        // Create the registry with a default namespace of config
        $registry = new JRegistry();

        // Sanitize the namespace.
        $namespace = ucfirst((string)preg_replace('/[^A-Z_]/i', '', $namespace));

        $name = 'MolajoConfig'.$namespace;

        if ($type == 'PHP' && class_exists($name)) {
            $config = new $name();
            $registry->loadObject($config);
        }

        return $registry;
    }

    /**
     * Create a session object
     *
     * @param   array  $options Session option array
     *
     * @return MolajoSession object
     * @since   1.0
     */
    protected static function _createSession($options = array())
    {
        $conf = self::getConfig();
        $handler = $conf->get('session_handler', 'none');

        $options['expire'] = ($conf->get('lifetime')) ? $conf->get('lifetime') * 60 : 900;

        $session = MolajoSession::getInstance($handler, $options);

        if ($session->getState() == 'expired') {
            $session->restart();
        }

        return $session;
    }

    /**
     * Create an database object
     *
     * @see JDatabase
     *
     * @return JDatabase object
     *
     * @since   1.0
     */
    protected static function _createDbo()
    {
        $conf = self::getConfig();

        $host = $conf->get('host');
        $user = $conf->get('user');
        $password = $conf->get('password');
        $database = $conf->get('db');
        $prefix = $conf->get('dbprefix');
        $driver = $conf->get('dbtype');
        $debug = $conf->get('debug');

        $options = array('driver' => $driver, 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix);

        $db = JDatabase::getInstance($options);

        if (MolajoError::isError($db)) {
            header('HTTP/1.1 500 Internal Server Error');
            jexit('Database Error: '.(string)$db);
        }

        if ($db->getErrorNum() > 0) {
            MolajoError::raiseError(500, MolajoText::sprintf('MOLAJO_UTIL_ERROR_CONNECT_DATABASE', $db->getErrorNum(), $db->getErrorMsg()));
        }

        $db->debug($debug);

        return $db;
    }

    /**
     * Create a mailer object
     *
     * @return  MolajoMail object
     * @since   1.0
     */
    protected static function _createMailer()
    {
        $conf = self::getConfig();

        $sendmail = $conf->get('sendmail');
        $smtpauth = ($conf->get('smtpauth') == 0) ? null : 1;
        $smtpuser = $conf->get('smtpuser');
        $smtppass = $conf->get('smtppass');
        $smtphost = $conf->get('smtphost');
        $smtpsecure = $conf->get('smtpsecure');
        $smtpport = $conf->get('smtpport');
        $mailfrom = $conf->get('mailfrom');
        $fromname = $conf->get('fromname');
        $mailer = $conf->get('mailer');

        $mail = MolajoMail::getInstance();
        $mail->setSender(array($mailfrom, $fromname));

        switch ($mailer)
        {
            case 'smtp' :
                $mail->useSMTP($smtpauth, $smtphost, $smtpuser, $smtppass, $smtpsecure, $smtpport);
                break;

            case 'sendmail' :
                $mail->IsSendmail();
                break;

            default :
                $mail->IsMail();
                break;
        }

        return $mail;
    }

    /**
     * Create a language object
     *
     * @see MolajoLanguage
     *
     * @return MolajoLanguage object
     * @since   1.0
     */
    protected static function _createLanguage()
    {
        $conf = self::getApplicationConfig();
        $locale = $conf->get('language');
        $debug = $conf->get('debug_language');
        $lang = MolajoLanguage::getInstance($locale, $debug);

        return $lang;
    }

    /**
     * Create a document object
     *
     * @see MolajoDocument
     *
     * @return MolajoDocument object
     * @since   1.0
     */
    protected static function _createDocument()
    {
        $lang = self::getLanguage();

        $type = JRequest::getWord('format', 'html');

        $attributes = array(
            'charset' => 'utf-8',
            'lineend' => 'unix',
            'tab' => '  ',
            'language' => $lang->getTag(),
            'direction' => $lang->isRTL() ? 'rtl' : 'ltr'
        );

        return MolajoDocument::getInstance($type, $attributes);
    }

    /**
     * Creates a new stream object with appropriate prefix
     *
     * @param   boolean  $use_prefix    Prefix the connections for writing
     * @param   boolean  $use_network    Use network if available for writing; use false to disable (e.g. FTP, SCP)
     * @param   string   $ua            UA User agent to use
     * @param   boolean  $uamask        User agent masking (prefix Mozilla)
     *
     * @see JStream
     *
     * @return  JStream
     * @since   1.0
     */
    public static function getStream($use_prefix = true, $use_network = true, $ua = null, $uamask = false)
    {
        // Setup the context; Molajo UA and overwrite
        $context = array();
        $version = new MolajoVersion;
        // set the UA for HTTP and overwrite for FTP
        $context['http']['user_agent'] = $version->getUserAgent($ua, $uamask);
        $context['ftp']['overwrite'] = true;

        if ($use_prefix) {
            $FTPOptions = JClientHelper::getCredentials('ftp');
            $SCPOptions = JClientHelper::getCredentials('scp');

            if ($FTPOptions['enabled'] == 1 && $use_network) {
                $prefix = 'ftp://'.$FTPOptions['user'].':'.$FTPOptions['pass'].'@'.$FTPOptions['host'];
                $prefix .= $FTPOptions['port'] ? ':'.$FTPOptions['port'] : '';
                $prefix .= $FTPOptions['root'];
            }
            else if ($SCPOptions['enabled'] == 1 && $use_network) {
                $prefix = 'ssh2.sftp://'.$SCPOptions['user'].':'.$SCPOptions['pass'].'@'.$SCPOptions['host'];
                $prefix .= $SCPOptions['port'] ? ':'.$SCPOptions['port'] : '';
                $prefix .= $SCPOptions['root'];
            }
            else {
                $prefix = MOLAJO_BASE_FOLDER.'/';
            }

            $retval = new JStream($prefix, MOLAJO_BASE_FOLDER, $context);
        }
        else {
            $retval = new JStream('', '', $context);
        }

        return $retval;
    }
}
