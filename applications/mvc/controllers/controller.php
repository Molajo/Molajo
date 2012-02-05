<?php
/**
 * @package     Molajo
 * @subpackage  Controller
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
     * @var    Parser
     * @since  1.0
     */
    public static $parser = null;

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
    public static function getSite($id = null,
                                   $config = array(),
                                   $prefix = 'Molajo')
    {
        if (self::$site) {
        } else {
            self::$site = MolajoSiteController::getInstance(
                $id,
                $config,
                $prefix
            );
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
    public static function getApplication($id = null,
                                          JRegistry $config = null,
                                          JInput $input = null)
    {
        if (self::$application) {
        } else {
            self::$application =
                MolajoApplicationController::getInstance(
                    $id,
                    $config,
                    $input
                );
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
    public static function getRequest(JRegistry $request = null,
                                      $override_request_url = null,
                                      $override_asset_id = null)
    {
        if (self::$request) {
        } else {
            self::$request =
                MolajoRequestController::getInstance(
                    $request,
                    $override_request_url,
                    $override_asset_id
                );
        }
        return self::$request;
    }

    /**
     * getParser
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
    public static function getParser(JRegistry $config = null)
    {
        if (self::$parser) {
        } else {
            self::$parser =
                MolajoParserController::getInstance(
                    $config
                );
        }
        return self::$parser;
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
            self::$db = MolajoConfigurationHelper::getDB();
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

    public static function Parser(JRegistry $config = null)
    {
        return MolajoController::getParser($config);
    }

    public static function User($id = null)
    {
        return MolajoController::getUser($id);
    }

    public static function DB()
    {
        return MolajoController::getDbo();
    }



    public static function Mailer()
    {
        return MolajoController::getMailer();
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
