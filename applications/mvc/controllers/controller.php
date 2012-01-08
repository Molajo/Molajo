<?php
/**
 * @package     Molajo
 * @subpackage  Controller
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  Primary Controller
 *
 *  Initiates Site and Application Controllers
 *  Acts as the factory class for various application objects
 */
class MolajoController
{
    /**
     * @var    Site
     * @since  1.0
     */
    public static $site = null;

    /**
     * @var    Site Configuration
     * @since  1.0
     */
    public static $siteConfig = null;

    /**
     * @var    Application
     * @since  1.0
     */
    public static $application = null;

    /**
     * @var    Application Configuration
     * @since  1.0
     */
    public static $config = null;

    /**
     * @var    Database
     * @since  1.0
     */
    public static $database = null;

    /**
     * @var    Cache
     * @since  1.0
     */
    public static $cache = null;

    /**
     * @var    Mailer
     * @since  1.0
     */
    public static $mailer = null;

    /**
     * @var    array
     * @since  1.0
     */
    public static $dates = array();

    /**
     * getSite
     *
     * Get a site object
     *
     * @static
     * @param null $id
     * @param array $config
     * @param string $prefix
     * @return null|Site
     * @since 1.0
     */
    public static function getSite($id = null, $config = array(), $prefix = 'Molajo')
    {
        if (self::$site) {
        } else {
            self::$site = MolajoControllerSite::getInstance($id, $config, $prefix);
        }

        return self::$site;
    }

    /**
     * getApplication
     *
     * Get an Application object
     *
     * @static
     * @param null $id
     * @param JInput|null $input
     * @param JRegistry|null $config
     * @param JWebClient|null $client
     * @param array $options
     * @return Application|null
     * @since 1.0
     */
    public static function getApplication($id = null, JInput $input = null, JRegistry $config = null, JWebClient $client = null, $options = array())
    {
        if (self::$application) {
        } else {
            self::$application = MolajoControllerApplication::getInstance($id, $input, $config, $client, $options);
        }

        return self::$application;
    }

    /**
     * Get an user object.
     *
     * Returns the global User object, only creating it if it doesn't already exist.
     *
     * @static
     * @param null $id
     * @return object|User
     */
    public static function getUser($id = null)
    {
        $id = 42;
        //        if (is_null($id)) {
        //            $instance = self::getSession()->get('user');
        //            if ($instance instanceof MolajoUser) {
        //            } else {
        //                $instance = MolajoUser::getInstance();
        //            }
        //        } else {
        //            $current = self::getSession()->get('user');
        //            var_dump($current);
        //            if ($current->id = $idxxxxxx) {
        //                $instance = self::getSession()->get('user');
        //            } else {
        $instance = MolajoUser::getInstance($id);
        //            }
        //        }
        //        echo '<pre>';var_dump($instance);'</pre>';
        return $instance;
    }

    /**
     * getCache
     *
     * Get a cache object
     *
     * @static
     * @param string $group
     * @param string $handler
     * @param null $storage
     * @return mixed
     * @since 1.0
     */
    public static function getCache($group = '', $handler = 'callback', $storage = null)
    {
        $hash = md5($group . $handler . $storage);
        if (isset(self::$cache[$hash])) {
            return self::$cache[$hash];
        }
        $handler = ($handler == 'function') ? 'callback' : $handler;

        //        $conf = self::getConfig();

        $options = array('defaultgroup' => $group);

        if (isset($storage)) {
            $options['storage'] = $storage;
        }

        $cache = JCache::getInstance($handler, $options);

        self::$cache[$hash] = $cache;

        return self::$cache[$hash];
    }

    /**
     * getDbo
     *
     * Get a database object
     *
     * @return Database object
     * @since 1.0
     */
    public static function getDbo()
    {
        if (self::$database) {
        } else {
            $debug = self::get('debug', '', 'site');
            self::$database = self::_createDbo();
            self::$database->debug($debug);
        }

        return self::$database;
    }

    /**
     * getMailer
     *
     * Get a mailer object
     *
     * @static
     * @return Mailer|null
     * @since 1.0
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
     * getFeedParser
     *
     * Get a parsed XML Feed Source
     *
     * @static
     * @param $url
     * @param int $cache_time
     * @return bool|SimplePie
     * @since 1.0
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
        } else {
            MolajoError::raiseWarning('SOME_ERROR_CODE', MolajoTextHelper::_('MOLAJO_UTIL_ERROR_LOADING_FEED_DATA'));
        }

        return false;
    }

    /**
     * Reads a XML file.
     *
     * @param   string  $data   Full path and file name.
     * @param   boolean  $isFile true to load a file | false to load a string.
     *
     * @return  mixed    SimpleXMLElement on success | false on error.
     * @todo This may go in a separate class - error reporting may be improved.
     */
    public static function getXML($data, $isFile = true)
    {
        // Disable libxml errors and allow to fetch error information as needed
        libxml_use_internal_errors(false);
        if ($isFile) {
            $xml = simplexml_load_file($data, 'SimpleXMLElement');
        } else {
            $xml = simplexml_load_string($data, 'SimpleXMLElement');
        }

        if (empty($xml)) {
            MolajoError::raiseWarning(100, MolajoTextHelper::_('MOLAJO_UTIL_ERROR_XML_LOAD'));

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
     * getEditor
     *
     * Get an editor object
     *
     * @param   string  $editor The editor to load, depends on the editor plugins that are installed
     *
     * @return editor object
     * @since   1.0
     */
    public static function getEditor($editor = null)
    {
        if (is_null($editor)) {
            $editor = self::get('editor', 'none');
        }

        return MolajoEditor::getInstance($editor);
    }

    /**
     * getDate
     *
     * Return the Date object
     *
     * @param   mixed  $time     The initial time for the JDate object
     * @param   mixed  $tzOffset The timezone offset.
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

        $language = self::getApplication()->getLanguage();
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
     * createSession
     *
     * Create a session object
     *
     * @return object
     * @since   1.0

    protected static function createSession($options = array())
    {
        $handler = self::get('session_handler', 'none', 'site');
        $lifetime = self::get('lifetime', '15', 'site');
        if ((int)$lifetime > 0) {
        } else {
            $lifetime = 15;
        }
        $options['expire'] = (int)$lifetime * 60;

        $session = MolajoSession::getInstance($handler, $options);

        if ($session->getState() == 'expired') {
            $session->restart();
        }

        return $session;
    }
     */
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
        $host = self::get('host', '', 'site');
        $user = self::get('user', '', 'site');
        $password = self::get('password', '', 'site');
        $database = self::get('db', '', 'site');
        $prefix = self::get('dbprefix', '', 'site');
        $driver = self::get('dbtype', '', 'site');
        $debug = self::get('debug', '', 'site');

        $options = array('driver' => $driver,
            'host' => $host,
            'user' => $user,
            'password' => $password,
            'database' => $database,
            'prefix' => $prefix);

        $db = JDatabase::getInstance($options);

        if (MolajoError::isError($db)) {
            header('HTTP/1.1 500 Internal Server Error');
            jexit('Database Error: ' . (string)$db);
        }

        if ($db->getErrorNum() > 0) {
            MolajoError::raiseError(500, MolajoTextHelper::sprintf('MOLAJO_UTIL_ERROR_CONNECT_DATABASE', $db->getErrorNum(), $db->getErrorMsg()));
        }

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
        $sendmail = self::get('sendmail', '', 'site');
        $smtpauth = (self::get('smtpauth', '', 'site') == 0) ? null : 1;
        $smtpuser = self::get('smtpuser', '', 'site');
        $smtppass = self::get('smtppass', '', 'site');
        $smtphost = self::get('smtphost', '', 'site');
        $smtpsecure = self::get('smtpsecure', '', 'site');
        $smtpport = self::get('smtpport', '', 'site');
        $mailfrom = self::get('mailfrom', '', 'site');
        $fromname = self::get('fromname', '', 'site');
        $mailer = self::get('mailer', '', 'site');

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
     * @see MolajoLanguageHelper
     *
     * @return MolajoLanguageHelper object
     * @since   1.0

    protected static function _createLanguage()
    {
        $locale = self::get('language', '', 'site');
        $debug = self::get('debug_language', '', 'site');
        $lang = MolajoLanguageHelper::getInstance($locale, $debug);

        return $lang;
    }
*/
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
        $context = array();
        $version = new MolajoVersion;

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

    /**
     * getSiteConfig
     *
     * Retrieve the Site configuration object
     *
     * @return  config object
     * @since   1.0
     */
    public static function getSiteConfig()
    {
        if (self::$siteConfig) {
        } else {
            self::$siteConfig = new JRegistry;
            $config = new MolajoConfigurationHelper ();
            $data = $config->site();

            if (is_array($data)) {
                self::$siteConfig->loadArray($data);

            } elseif (is_object($data)) {
                self::$siteConfig->loadObject($data);
            }
        }

        return self::$siteConfig;
    }

    /**
     * getConfig
     *
     * Retrieve the Application configuration object
     *
     * @return  config object
     * @since   1.0
     */
    public static function getConfig()
    {
        if (self::$config) {
        } else {
            self::$config = new JRegistry;
            $configInstance = new MolajoConfigurationHelper ();
            $data = $configInstance->getConfig();

            if (is_array($data)) {
                self::$config->loadArray($data);

            } elseif (is_object($data)) {
                self::$config->loadObject($data);
            }
        }

        return self::$config;
    }

    /**
     * get
     *
     * Returns a property of the Site, Application, and Extension objects
     * or the default value if the property is not set.
     *
     * @param   string  $key      The name of the property.
     * @param   mixed   $default  The default value (optional) if none is set.
     * @param   mixed   $type     The type of configuration data
     *
     * @return  mixed   The value of the configuration.
     *
     * @since   11.3
     */
    public function get($key, $default = null, $type = 'config')
    {
        if ($type == 'site') {
            if (self::$siteConfig) {
            } else {
                self::getSiteConfig();
            }
            return self::$siteConfig->get($key, $default);
        } else {
            if (self::$config) {
            } else {
                self::getConfig();
            }
            return self::$config->get($key, $default);
        }
    }
}
