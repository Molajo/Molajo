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
     * Get a application object
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
     * Get a configuration object
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
        } else {
            if ($file === null) {
                $file = MOLAJO_SITE_PATH . '/configuration.php';
            }

            self::$config = self::_createConfig($file, $type);
        }

        return self::$config;
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
        $hash = md5($group . $handler . $storage);
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
    public static function getACL()
    {
    }

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
                MolajoError::raiseWarning(100, 'XML: ' . $error->message);
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
                $classname = str_replace('-', '_', $mainLocale) . 'Date';

                if (class_exists($classname)) {
                } else {
                    $classname = 'JDate';
                }
            } else {
                $classname = 'JDate';
            }
        }
        $key = $time . '-' . $tzOffset;

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

        $name = 'MolajoConfig' . $namespace;
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
            jexit('Database Error: ' . (string)$db);
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
        $conf = self::getConfig();
        $locale = $conf->get('language');
        $debug = $conf->get('debug_lang');
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
                $prefix = 'ftp://' . $FTPOptions['user'] . ':' . $FTPOptions['pass'] . '@' . $FTPOptions['host'];
                $prefix .= $FTPOptions['port'] ? ':' . $FTPOptions['port'] : '';
                $prefix .= $FTPOptions['root'];
            }
            else if ($SCPOptions['enabled'] == 1 && $use_network) {
                $prefix = 'ssh2.sftp://' . $SCPOptions['user'] . ':' . $SCPOptions['pass'] . '@' . $SCPOptions['host'];
                $prefix .= $SCPOptions['port'] ? ':' . $SCPOptions['port'] : '';
                $prefix .= $SCPOptions['root'];
            }
            else {
                $prefix = MOLAJO_BASE_FOLDER . '/';
            }

            $retval = new JStream($prefix, MOLAJO_BASE_FOLDER, $context);
        }
        else {
            $retval = new JStream('', '', $context);
        }

        return $retval;
    }
}
