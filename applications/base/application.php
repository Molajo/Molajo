<?php
/**
 * @package     Molajo
 * @subpackage  Base
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Application
 *
 * @package     Molajo
 * @subpackage  Base
 * @since       1.0
 */
class MolajoApplication
{
    /**
     * Application static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * Configuration
     *
     * @var    object
     * @since  1.0
     */
    protected $_configuration = null;

    /**
     * Metadata
     *
     * @var object
     * @since 1.0
     */
    protected $_metadata;

    /**
     * Custom Fields
     *
     * @var object
     * @since 1.0
     */
    protected $_custom_fields;

    /**
     * Services Loaded
     *
     * @static
     * @var    $services
     * @since  1.0
     */
    protected $_languages;

    /**
     * Log
     *
     * @var object
     * @since 1.0
     */
    protected $_log;

    /**
     * Services Loaded
     *
     * @static
     * @var    $services
     * @since  1.0
     */
    protected $_services;

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new MolajoApplication ();
        }
        return self::$instance;
    }

    /**
     * Class constructor.
     *
     * @return  null
     * @since   1.0
     */
    public function __construct()
    {
        return;
    }

    /**
     * initialize
     *
     * Load application services and verify required settings
     *
     * @return  mixed
     * @since   1.0
     */
    public function initialize()
    {
        /** Services: initiate Application Services */
        $this->_services = new Registry();
        $sv = Molajo::Services()->initiateServices();

        /** configuration: ssl check for application */
        if ($this->get('force_ssl') >= 1) {
            if (isset($_SERVER['HTTPS'])) {
            } else {
                Molajo::Responder()->redirect((string)'https' .
                        substr(MOLAJO_BASE_URL, 4, strlen(MOLAJO_BASE_URL) - 4) .
                        MOLAJO_APPLICATION_URL_PATH .
                        '/' .
                        MOLAJO_PAGE_REQUEST
                );
            }
        }

        /** return to Molajo::Site */
        return;
    }

    /**
     * process
     *
     * Primary Application Logic Flow
     *
     * @return  mixed
     * @since   1.0
     */
    public function process()
    {

        echo $this->get('language','','language');
        echo $this->get('rtl','','language');
        /** responder: prepare for output */
        Molajo::Responder();

        /** request: define processing instructions in page_request object */
        Molajo::Request()->process();

        /**
         * Display Task
         *
         * Input Statement Loop until no more <input statements found
         *
         * 1. Parser: parses theme and rendered output for <input:renderer statements
         *
         * 2. Renderer: each input statement processed by extension renderer in order
         *    to collect task object for use by the MVC
         *
         * 3. MVC: executes task/controller which handles model processing and
         *    renders template and wrap views
         */

        if (Molajo::Request()->get('mvc_task') == 'add'
            || Molajo::Request()->get('mvc_task') == 'edit'
            || Molajo::Request()->get('mvc_task') == 'display'
        ) {

            Molajo::Parser()->processTheme();

        } else {

            /**
             * Action Task
             */
            //$this->_processTask();
        }

        /** responder: process rendered output */
        Molajo::Responder()->respond();

        return;
    }

    /**
     * setApplicationProperties
     *
     * Called from Services after Application Configuration
     *
     * @param   $configuration
     * @return  mixed
     * @since   1.0
     */
    public function setApplicationProperties($configuration)
    {
        $this->_metadata = $configuration->metadata;
        $this->_custom_fields = $configuration->custom_fields;
        $this->_configuration = $configuration->configuration;
        return;
    }

    /**
     * setLanguageProperties
     *
     * Called from Services after Language started
     *
     * @param   $configuration
     * @return  mixed
     * @since   1.0
     */
    public function setLanguageProperties($configuration)
    {
        $this->_language = new Registry();
        
        $this->set('language', $configuration->language, 'language');
        $this->set('name', $configuration->name, 'language');
        $this->set('tag', $configuration->tag, 'language');
        $this->set('rtl', $configuration->rtl, 'language');
        $this->set('locale', $configuration->locale, 'language');
        $this->set('first_day', $configuration->first_day, 'language');

        return true;
    }

    /**
     * get
     *
     * @param  string  $key
     * @param  string  $default
     * @param  string  $type
     *
     * @return  mixed
     *
     * @since   1.0
     */
    public function get($key, $default = null, $type = 'config')
    {
        if ($type == 'custom') {
            return $this->_custom_fields->get($key, $default);

        } else if ($type == 'metadata') {
            return $this->_metadata->get($key, $default);

        } else if ($type == 'log') {
            return $this->_log->get($key, $default);

        } else if ($type == 'services') {
            return $this->_services->get($key, false);

        } else if ($type == 'language') {
            return $this->_language->get($key);

        } else {
            //echo $key.' '.$default.' '.$type;
            return $this->_configuration->get($key, $default);
        }
    }

    /**
     * set
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  string  $type
     *
     * @return  mixed
     * @since   1.0
     */
    public function set($key, $value = null, $type = 'config')
    {
        if ($type == 'custom') {
            return $this->_custom_fields->set($key, $value);

        } else if ($type == 'metadata') {
            return $this->_metadata->set($key, $value);

        } else if ($type == 'log') {
            return $this->_log->set($key, $value);

        } else if ($type == 'services') {
            return $this->_services->set($key, true);

        } else if ($type == 'language') {
            return $this->_language->set($key, $value);

        } else {
            return $this->_configuration->set($key, $value);
        }
    }
}
