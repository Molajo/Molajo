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
     * @var    Request
     * @since  1.0
     */
    public static $request = null;

    /**
     * @var    Database
     * @since  1.0
     */
    public static $db = null;

    /**
     * @var    Doctrine Entity Manager
     * @since  1.0
     */
    public static $entityManager = null;

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
     * @var    Dates
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
            self::$site = MolajoSiteController::getInstance($id, $config, $prefix);
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
     * @param JRegistry|null $config
     * @param JInput|null $input
     *
     * @return Application|null
     * @since 1.0
     */
    public static function getApplication($id = null, JRegistry $config = null, JInput $input = null)
    {
        if (self::$application) {
        } else {
            self::$application = MolajoApplicationController::getInstance($id, $config, $input);
        }
        return self::$application;
    }

    /**
     * getRequest
     *
     * Get the Request Controller Object
     *
     * @static
     * @param JRegistry|null $config
     * @param string $override_request_url
     * @param string $override_asset_id
     *
     * @return Request|null
     * @since 1.0
     */
    public static function getRequest(JRegistry $request = null, $override_request_url = null, $override_asset_id = null)
    {
        if (self::$request) {
        } else {
            self::$request = MolajoRequestController::getInstance($request, $override_request_url, $override_asset_id);
        }
        return self::$request;
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
     * getDbo
     *
     * Get a database object
     *
     * @return Database object
     * @since 1.0
     */
    public static function getDbo()
    {
        if (self::$db) {
        } else {
            $debug = self::get('debug', '', 'site');
            self::$db = self::_createDbo();
            self::$db->debug($debug);
        }
        return self::$db;
    }

    /**
     *  getDoctrine
     *
     * Get a database object for Doctrine
     *
     * @return Database object
     * @since 1.0
     */
    public static function getDoctrine()
    {
        if (self::$entityManager) {
        } else {
            $doctrineProxy = new DoctrineBootstrapper(1);
            var_dump($doctrineProxy);
            $doctrineProxy->setEntityLibrary(MOLAJO_DOCTRINE_MODELS . '/models');
            $doctrineProxy->setProxyLibrary(MOLAJO_DOCTRINE_PROXIES . '/proxies');
            $doctrineProxy->setProxyNamespace('Proxies');
            $doctrineProxy->setConnectionOptions(
                array(
                    'driver' => 'pdo_mysql',
                    'path' => 'database.mysql',
                    'dbname' => Molajo::App()->get('db'),
                    'user' => Molajo::App()->get('user'),
                    'password' => Molajo::App()->get('password')
                )
            );
            self::$entityManager = $doctrineProxy->bootstrap();
        }

        return self::$entityManager;
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
     * todo: amy Move into MODEL
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
        $simplepie = new SimplePie(null, null, 0);

        $simplepie->enable_cache(false);
        $simplepie->set_feed_url($url);
        $simplepie->force_feed(true);

        $contents = $simplepie->init(null, false, false);

        if ($contents) {
            return $simplepie;
        } else {
            // error
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
            MolajoError::raiseWarning(100, TextHelper::_('MOLAJO_UTIL_ERROR_XML_LOAD'));

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
        $db = self::get('db', '', 'site');
        $prefix = self::get('dbprefix', '', 'site');
        $driver = self::get('dbtype', '', 'site');
        $debug = self::get('debug', '', 'site');

        $options = array('driver' => $driver,
            'host' => $host,
            'user' => $user,
            'password' => $password,
            'database' => $db,
            'prefix' => $prefix);

        $db = JDatabase::getInstance($options);

        if (MolajoError::isError($db)) {
            header('HTTP/1.1 500 Internal Server Error');
            jexit('Database Error: ' . (string)$db);
        }

        if ($db->getErrorNum() > 0) {
            MolajoError::raiseError(500, TextHelper::sprintf('MOLAJO_UTIL_ERROR_CONNECT_db', $db->getErrorNum(), $db->getErrorMsg()));
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
        $send_mail = self::get('send_mail', '', 'site');
        $smtpauth = (self::get('smtpauth', '', 'site') == 0) ? null : 1;
        $smtpuser = self::get('smtpuser', '', 'site');
        $smtppass = self::get('smtppass', '', 'site');
        $smtphost = self::get('smtphost', '', 'site');
        $smtpsecure = self::get('smtpsecure', '', 'site');
        $smtpport = self::get('smtpport', '', 'site');
        $mail_from = self::get('mail_from', '', 'site');
        $from_name = self::get('from_name', '', 'site');
        $mailer = self::get('mailer', '', 'site');

        $mail = MolajoMail::getInstance();
        $mail->setSender(array($mail_from, $from_name));

        switch ($mailer)
        {
            case 'smtp' :
                $mail->useSMTP($smtpauth, $smtphost, $smtpuser, $smtppass, $smtpsecure, $smtpport);
                break;

            case 'send_mail' :
                $mail->IsSendmail();
                break;

            default :
                $mail->IsMail();
                break;
        }

        return $mail;
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

/**
 *  Molajo Class for shortcuts
 */
class Molajo extends MolajoController
{
    public static function Site($id = null, $config = array(), $prefix = 'Molajo')
    {
        return MolajoController::getSite($id, $config, $prefix);
    }

    public static function App($id = null, JRegistry $config = null, JInput $input = null)
    {
        return MolajoController::getApplication($id, $config, $input);
    }

    public static function Request(JRegistry $request = null, $override_request_url = null, $override_asset_id = null)
    {
        return MolajoController::getRequest($request, $override_request_url, $override_asset_id);
    }

    public static function User($id = null)
    {
        return MolajoController::getUser($id);
    }

    public static function DB()
    {
        return MolajoController::getDbo();
    }

    public static function XML($data, $isFile = true)
    {
        return MolajoController::getXML($data, $isFile);
    }

    public static function Mailer()
    {
        return MolajoController::getMailer();
    }

    public static function FeedParser($url, $cache_time = 0)
    {
        return MolajoController::getFeedParser($url, $cache_time);
    }

    public static function Editor($editor = null)
    {
        return MolajoController::getEditor($editor);
    }

    public static function Date($time = 'now', $tzOffset = null)
    {
        return MolajoController::getDate($time, $tzOffset);
    }
}
