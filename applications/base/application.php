<?php
/**
 * @package     Molajo
 * @subpackage  Base
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
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
     * Application static database results
     *
     * @var    object
     * @since  1.0
     */
    protected $_appQueryResults = null;

    /**
     * Application custom fields
     *
     * @var    object
     * @since  1.0
     */
    protected $_custom_fields = null;

    /**
     * Application Metadata
     *
     * @var    array
     * @since  1.0
     */
    protected $_metadata = array();

    /**
     * Application Parameters
     *
     * @var    array
     * @since  1.0
     */
    protected $_parameters = array();

    /**
     * Input Object
     *
     * @var    object
     * @since  1.0
     */
    protected $_input;

    /**
     * Language
     *
     * @var    object
     * @since  1.0
     */
    protected $_language;

    /**
     * Language direction
     *
     * @var    string
     * @since  1.0
     */
    protected $_direction = 'ltr';

    /**
     * Dispatcher
     *
     * @var    object
     * @since  1.0
     */
    protected $_dispatcher;

    /**
     * Session
     *
     * @var    object
     * @since  1.0
     */
    protected $_session;

    /**
     * Messages
     *
     * @var    array
     * @since  1.0
     */
    protected $_messages = array();

    /**
     * getInstance
     *
     * @static
     * @param  null $id
     * @param  Input|null $input
     * @param  Registry|null $config
     *
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance($id = null,
                                       Registry $config = null,
                                       Input $input = null)
    {
        if ($id == null) {
            $id = MOLAJO_APPLICATION;
        }

        if (empty(self::$instance)) {

            if ($input instanceof Input) {
            } else {
                $input = new Input;
            }
            if ($config instanceof Registry) {
            } else {
                $config = new Registry;
            }
            $_appQueryResults = ApplicationHelper::getApplicationInfo($id);
            if ($_appQueryResults === false) {
                return false;
            }
            if (defined('MOLAJO_APPLICATION_PATH')) {
            } else {
                define('MOLAJO_APPLICATION_PATH',
                    MOLAJO_APPLICATIONS_CORE . '/applications/' . $_appQueryResults->path
                );
            }
            if (defined('MOLAJO_APPLICATION_ID')) {
            } else {
                define('MOLAJO_APPLICATION_ID', $_appQueryResults->id);
            }

            self::$instance = new MolajoApplication(
                $config,
                $input,
                $_appQueryResults
            );
        }

        return self::$instance;
    }

    /**
     * Class constructor.
     *
     * @param  mixed   $input
     * @param  mixed   $config
     * @param  object  $_appQueryResults
     *
     * @return  null
     * @since   1.0
     */
    public function __construct(Registry $config = null,
                                Input $input = null,
                                $_appQueryResults = null)
    {
        if ($input instanceof Input) {
            $this->_input = $input;
        }

        if ($config instanceof Registry) {
            $this->_config = $config;
        } else {
            $this->_config = new Registry();
        }
        /** Database results from application helpers */
        if ($_appQueryResults == null) {
        } else {
            $this->_appQueryResults = $_appQueryResults;
            $this->loadConfig();
        }

        /** now */
        $this->set('execution.datetime', gmdate('Y-m-d H:i:s'));
        $this->set('execution.timestamp', time());

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
     * Load the application.
     *
     * @return  mixed
     * @since   1.0
     */
    public function load()
    {
        /** Site authorisation */
        $sc = new MolajoSite ();
        $authorise = $sc->authorise(MOLAJO_APPLICATION_ID);
        if ($authorise === false) {
            $message = '304: ' . MOLAJO_BASE_URL;
            echo $message;
            die;
        }

        /** initialise application */
        $this->loadLanguage();
        $this->loadSession();
        $this->loadDispatcher();

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

            /** action task: insert, update, or delete */
        } else {
            //$this->_processTask();
        }

        /** responder: process rendered output */
        $res->respond();

        return;
    }

    /**
     * setConfig
     *
     * Creates the Application configuration object.
     *
     * @return  null
     * @since   1.0
     */
    public function loadConfig()
    {
        $this->_metadata = new Registry;
        $this->_metadata->loadString($this->_appQueryResults->metadata);

        $this->_custom_fields = new Registry;
        $this->_custom_fields->loadString($this->_appQueryResults->custom_fields);

        $cc = new MolajoConfigurationHelper($this->_appQueryResults->parameters);
        $this->_config = $cc->getConfig();

        return;
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
     * @since   1.0
     */
    public function get($key, $default = null, $type = 'config')
    {
        if ($type == 'custom') {
            return $this->_custom_fields->get($key, $default);

        } else if ($type == 'metadata') {
            return $this->_metadata->get($key, $default);

        } else if ($key == 'languageObject') {
            return $this->_language;

        } else {
            return $this->_config->get($key, $default);
        }
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
     * @since   1.0
     */
    public function set($key, $value = null, $type = 'config')
    {
        if ($type == 'custom') {
            return $this->_custom_fields->set($key, $value);

        } else if ($type == 'metadata') {
            return $this->_metadata->set($key, $value);

        } else {
            return $this->_config->set($key, $value);
        }
    }

    /**
     * getInput
     *
     * Returns Application Input object
     *
     * @return  object
     * @since   1.0
     */
    public function getInput()
    {
        return $this->_input;
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
     * loadLanguage
     *
     * Load the core language files, set defaults, etc
     *
     * @return  Language object
     * @since   1.0
     */
    public function loadLanguage()
    {
        $locale = $this->get('language', 'en-GB');
        $this->_language = MolajoLanguage::getInstance($locale);
    }

    /**
     * loadDispatcher
     *
     * Method to create an event _dispatcher for the Web application.  The logic and options for creating
     * this object are adequately generic for default cases but for many applications it will make sense
     * to override this method and create event _dispatchers based on more specific needs.
     *
     * @return  void
     *
     * @since   1.0
     */
    protected function loadDispatcher()
    {
        //        $this->_dispatcher = JDispatcher::getInstance();
    }

    /**
     * registerEvent
     *
     * Registers a handler to a particular event group.
     *
     * @param   string    $event    The event name.
     * @param   callback  $handler  The handler, a function or an instance of a event object.
     *
     * @return  Application  Instance of $this to allow chaining.
     *
     * @since   1.0
     */
    public function registerEvent($event, $handler)
    {
        //        if ($this->_dispatcher instanceof JDispatcher) {
        //            $this->_dispatcher->register($event, $handler);
        //        }

        return $this;
    }

    /**
     * triggerEvent
     *
     * Calls all handlers associated with an event group.
     *
     * @param   string  $event  The event name.
     * @param   array   $args   An array of arguments (optional).
     *
     * @return  array   An array of results from each function call, or null if no _dispatcher is defined.
     *
     * @since   1.0
     */
    public function triggerEvent($event, array $args = null)
    {
        //        if ($this->_dispatcher instanceof JDispatcher) {
        //            return $this->_dispatcher->trigger($event, $args);
        //        }

        return null;
    }

    /**
     * setMessage
     *
     * Set the system message.
     *
     * @param   string  $message
     * @param   string  $type      message, notice, warning, and error
     *
     * @return  bool
     * @since   1.0
     */
    public static function setMessage($message = null,
                                      $type = 'message',
                                      $code = null,
                                      $debug_location = null,
                                      $debug_object = null)
    {
        if ($message == null
            && $code == null
        ) {
            return false;
        }

        $type = strtolower($type);
        if ($type == MOLAJO_MESSAGE_TYPE_NOTICE
            || $type == MOLAJO_MESSAGE_TYPE_WARNING
            || $type == MOLAJO_MESSAGE_TYPE_ERROR
        ) {
        } else {
            $type = MOLAJO_MESSAGE_TYPE_MESSAGE;
        }

        /** load _session messages into application messages array */
        $this->_sessionMessages();

        /** add new message */
        $count = count($this->_messages);

        $this->_messages[$count]['message'] = $message;
        $this->_messages[$count]['type'] = $type;
        $this->_messages[$count]['code'] = $code;
        $this->_messages[$count]['debug_location'] = $debug_location;
        $this->_messages[$count]['debug_object'] = $debug_object;

        return true;
    }

    /**
     * getMessages
     *
     * Get system messages
     *
     * @return  array  System messages
     * @since   1.0
     */
    public function getMessages()
    {
        $this->_sessionMessages();
        return $this->_messages;
    }

    /**
     *  _sessionMessages
     *
     * Retrieve messages in _session and load into Application messages array
     *
     * @return  void
     * @since   1.0
     */
    private function _sessionMessages()
    {
        $_session = $this->getSession();
        $_sessionMessages = $_session->get('application.messages');

        if (count($_sessionMessages) > 0) {
            $count = count($this->_messages);
            foreach ($_sessionMessages as $_sessionMessage) {
                $this->_messages[$count] = $_sessionMessage;
                $count++;
            }
            $_session->set('application.messages', null);
        }
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
