<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Configuration
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
class MolajoConfigurationHelper
{
    /**
     * Combined Site and Application Configuration Object
     *
     * @var    object
     * @since  1.0
     */
    public $config;

    /**
     * Site Configuration Object from fine
     *
     * @var    object
     * @since  1.0
     */
    public $siteConfig;

    /**
     * Application Configuration Object from database
     *
     * @var    object
     * @since  1.0
     */
    public $appConfig;

    /**
     * __construct
     *
     * Class constructor.
     *
     * @since  1.0
     */
    public function __construct($appConfig = null)
    {
        $this->config = new Registry;
        $this->siteConfig = new Registry;
        $this->appConfig = $appConfig;
    }

    /**
     * get
     *
     * Retrieves and combines site and application configuration objects
     *
     * @return object
     * @throws RuntimeException
     * @since  1.0
     */
    public function getConfig()
    {
        /** Site Configuration: php file */
        $configData = $this->site();
        foreach ($configData as $key => $value) {
            $this->set($key, $value);
        }

        /** Application Configuration: DB */
        $temp = substr($this->appConfig, 1, strlen($this->appConfig) - 2);
        $tempArray = array();
        $tempArray = explode(',', $temp);
        foreach ($tempArray as $entry) {
            $pair = explode(':', $entry);
            $key = substr(trim($pair[0]), 1, strlen(trim($pair[0])) - 2);
            if (trim($pair[0]) == '') {
            } else {
                $value = substr(trim($pair[1]), 1, strlen(trim($pair[1])) - 2);
                $this->set($key, $value);
            }
        }

        /** combined populated */
        return $this->config;
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
    public function site()
    {
        $siteConfigData = array();

        $file = MOLAJO_SITE_FOLDER_PATH . '/configuration.php';
        if (file_exists($file)) {
            require_once $file;
        } else {
            throw new RuntimeException('Fatal error - Application-Site Configuration File does not exist');
        }

        $siteConfigData = new MolajoSiteConfiguration();
        return $siteConfigData;
    }

    /**
     * get
     *
     * Returns a property of the Configuration object
     *
     * @param   string  $key      The name of the property.
     * @param   mixed   $default  The default value (optional) if none is set.
     * @param   string  $configFile Either site or application
     *
     * @return  mixed   The value of the configuration.
     *
     * @since   1.0
     */
    public function get($key, $default = null, $type = null)
    {
        if ($type == 'site') {
            return $this->siteConfig->get($key, $default);
        } else {
            return $this->config->get($key, $default);
        }
    }

    /**
     * set
     *
     * Modifies a property of the configuration object
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
        $this->config->set($key, $value);
    }

    /**
     * getDB
     *
     * Retrieve the Database for MolajoController Static Connection
     *
     * @static
     * @return JDatabase
     */
    public static function getDB()
    {
        $site = MolajoConfigurationHelper::site();

        $options = array('driver' => $site->dbtype,
            'host' => $site->host,
            'user' => $site->user,
            'password' => $site->password,
            'database' => $site->db,
            'prefix' => $site->dbprefix);

        $db = JDatabase::getInstance($options);

        if (MolajoError::isError($db)) {
            header('HTTP/1.1 500 Internal Server Error');
            jexit('Database Error: ' . (string)$db);
        }

        if ($db->getErrorNum() > 0) {
            MolajoError::raiseError(500, TextHelper::sprintf('MOLAJO_UTIL_ERROR_CONNECT_db', $db->getErrorNum(), $db->getErrorMsg()));
        }

        $db->debug($site->debug);

        return $db;
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
        $site = MolajoConfigurationHelper::site();

        $send_mail = $site->send_mail;
        $smtpauth = $site->smtpauth;
        $smtpuser = $site->smtpuser;
        $smtppass = $site->smtppass;
        $smtphost = $site->smtphost;
        $smtpsecure = $site->smtpsecure;
        $smtpport = $site->smtpport;
        $mail_from = $site->mail;
        $from_name = $site->from_name;
        $mailer = $site->mailer;

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
     *  getDoctrine
     *
     * Get a database object for Doctrine
     *
     * @return Database object
     * @since 1.0
     */
    public static function getDoctrine()
    {
        $doctrineProxy = new DoctrineBootstrapper(1);

        $doctrineProxy->setEntityLibrary(MOLAJO_DOCTRINE_MODELS . '/models');
        $doctrineProxy->setProxyLibrary(MOLAJO_DOCTRINE_PROXIES . '/proxies');
        $doctrineProxy->setProxyNamespace('Proxies');
        $doctrineProxy->setConnectionOptions(
            array(
                'driver' => 'pdo_mysql',
                'path' => 'database.mysql',
                'dbname' => Molajo::Application()->get('db'),
                'user' => Molajo::Application()->get('user'),
                'password' => Molajo::Application()->get('password')
            )
        );
        $entityManager = $doctrineProxy->bootstrap();

        return $entityManager;
    }
}
