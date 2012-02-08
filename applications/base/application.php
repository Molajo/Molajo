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
    protected $_config = null;

    /**
     * Input Object
     *
     * @var    object
     * @since  1.0
     */
    protected $_input;

    /**
     * getInstance
     *
     * @static
     * @param  null $id
     * @param  Registry|null $config
     * @param  Input|null $input
     *
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance(Registry $config = null,
                                       Input $input = null)
    {
        if (empty(self::$instance)) {

            if ($input instanceof Input) {
            } else {
                $input = new Input;
            }

            if ($config instanceof Registry) {
            } else {
                $config = new Registry;
            }

            self::$instance = new MolajoApplication($config, $input);
        }

        return self::$instance;
    }

    /**
     * Class constructor.
     *
     * @param  mixed   $config
     * @param  mixed   $input
     *
     * @return  null
     * @since   1.0
     */
    public function __construct(Registry $config = null,
                                Input $input = null)
    {
        if ($config instanceof Registry) {
            $this->_config = $config;
        } else {
            //error
        }

        if ($input instanceof Input) {
            $this->_input = $input;
        }

        /** ssl check for application */
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

        return;
    }

    /**
     * load
     *
     * Controls Page Rendering and Task Logic Flow
     *
     * @return  mixed
     * @since   1.0
     */
    public function load()
    {
        /** is site authorised? */
        $sc = new MolajoSite ();
        $authorise = $sc->authorise(MOLAJO_APPLICATION_ID);
        if ($authorise === false) {
            $message = '304: ' . MOLAJO_BASE_URL;
            echo $message;
            die;
        }

        /** Application Services */
        //        $services = Services::getInstance();
        $language = Services::connect('language', array('language', 'en-GB'));
        //        $results = Services::connect('language', array('language', 'en-GB'));


        var_dump($language);
        die;
        Molajo::Dispatcher();

        Molajo::Language();

        Molajo::Session();

        Molajo::User();

        /** responder: instantiate class to listen for output */
        $res = Molajo::Responder();

        /** request: build page_request object with processing instructions */
        $req = Molajo::Request();
        $req->process();

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
        if ($req->get('mvc_task') == 'add'
            || $req->get('mvc_task') == 'edit'
            || $req->get('mvc_task') == 'display'
        ) {
            Molajo::Parser();

            /**
             * Action Task
             */

        } else {

            //$this->_processTask();
        }

        /** responder: process rendered output */
        $res->respond();

        return;
    }

    /**
     * get
     *
     * Retrieves values, or establishes the value with a default, if not available
     *
     * @param  string  $key      The name of the property.
     * @param  string  $default  The default value (optional) if none is set.
     * @param  string  $type     custom, metadata, languageObject, config
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

        } else if ($key == 'logging') {
            return $this->_input;

        } else if ($key == 'input') {
            return $this->_input;

        } else {
            return $this->_config->get($key, $default);
        }
    }

    /**
     * set
     *
     * Modifies a property, creating it and establishing a default if not existing
     *
     * @param  string  $key    The name of the property.
     * @param  mixed   $value  The default value to use if not set (optional).
     * @param  string  $type   Custom, metadata, config
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

        } else if ($type == 'logging') {
            return $this->_metadata->set($key, $value);

        } else {
            return $this->_config->set($key, $value);
        }
    }

    /**
     * loadSession
     *
     * Method to create a session for the Web application.  The logic and options for creating this
     * object are adequately generic for default cases but for many applications it will make sense
     * to override this method and create _session objects based on more specific needs.
     *
     * @return  void
     *
     * @since   1.0
     */
    protected function loadSession()
    {
        // Generate a _session name.
        $name = md5($this->get('secret') .
            $this->get('_session_name', get_class($this)));

        // Calculate the _session lifetime.
        $lifetime = (($this->get('_session_lifetime'))
            ? $this->get('_session_lifetime') * 60 : 900);

        // Get the _session handler from the configuration.
        $handler = $this->get('_session_handler', 'none');

        // Initialize the options for Session.
        $options = array(
            'name' => $name,
            'expire' => $lifetime,
            'force_ssl' => $this->get('force_ssl')
        );

        // Instantiate the _session object.
        $_session = MolajoSession::getInstance($handler, $options);

        if ($_session->getState() == 'expired') {
            $_session->restart();
        }

        // If the _session is new, load the user and registry objects.
        if ($_session->isNew()) {
            $_session->set('registry', new Registry);
            $_session->set('user', new MolajoUser);
        }

        // Set the _session object.
        $this->_session = $_session;
    }

    /**
     * getSession
     *
     * Method to get the application _session object.
     *
     * @return  Session  The _session object
     *
     * @since   1.0
     */
    public function getSession()
    {
        return $this->_session;
    }


    /**
     * getHash
     *
     * Provides a secure hash based on a seed
     *
     * @param   string   $seed  Seed string.
     *
     * @return  string   A secure hash
     * @since  1.0
     */
    public static function getHash($seed)
    {
        return md5(self::get('secret') . $seed);
    }
}
