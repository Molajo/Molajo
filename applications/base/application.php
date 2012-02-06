<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
// addCustomHTML

/**
 * Molajo Application Class
 *
 * Base class
 *
 * Combines original code, fork of JWebApplication and JDocument
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
    public $input;

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
    protected $dispatcher;

    /**
     * Session
     *
     * @var    object
     * @since  1.0
     */
    protected $session;

    /**
     * Messages
     *
     * @var    array
     * @since  1.0
     */
    protected $_messages = array();

    /**
     * Callback for escaping
     *
     * @var   string
     * @since 1.0
     */
    protected $_escapeFunction = 'htmlspecialchars';

    /**
     * getInstance
     *
     * @static
     * @param  null $id
     * @param  JInput|null $input
     * @param  JRegistry|null $config
     *
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance($id = null,
                                       JRegistry $config = null,
                                       JInput $input = null)
    {
        if ($id == null) {
            $id = MOLAJO_APPLICATION;
        }

        if (empty(self::$instance)) {

            if ($input instanceof JInput) {
            } else {
                $input = new JInput;
            }

            if ($config instanceof JRegistry) {
            } else {
                $config = new JRegistry;
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
    public function __construct(JRegistry $config = null,
                                JInput $input = null,
                                $_appQueryResults = null)
    {
        if ($input instanceof JInput) {
            $this->_input = $input;
        }

        if ($config instanceof JRegistry) {
            $this->_config = $config;
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
                $this->redirect((string)'https' .
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
         *    to collect task_request object for use by the MVC
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
        $this->_metadata = new JRegistry;
        $this->_metadata->loadString($this->_appQueryResults->metadata);

        $this->_custom_fields = new JRegistry;
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
     * to override this method and create session objects based on more specific needs.
     *
     * @return  void
     *
     * @since   1.0
     */
    protected function loadSession()
    {
        // Generate a session name.
        $name = md5($this->get('secret') .
            $this->get('session_name', get_class($this)));

        // Calculate the session lifetime.
        $lifetime = (($this->get('session_lifetime'))
            ? $this->get('session_lifetime') * 60 : 900);

        // Get the session handler from the configuration.
        $handler = $this->get('session_handler', 'none');

        // Initialize the options for Session.
        $options = array(
            'name' => $name,
            'expire' => $lifetime,
            'force_ssl' => $this->get('force_ssl')
        );

        // Instantiate the session object.
        $session = MolajoSession::getInstance($handler, $options);

        if ($session->getState() == 'expired') {
            $session->restart();
        }

        // If the session is new, load the user and registry objects.
        if ($session->isNew()) {
            $session->set('registry', new JRegistry);
            $session->set('user', new MolajoUser);
        }

        // Set the session object.
        $this->session = $session;
    }

    /**
     * getSession
     *
     * Method to get the application session object.
     *
     * @return  Session  The session object
     *
     * @since   1.0
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * What is this really for?
     * loadLanguage
     *
     * Load the core language files, set defaults, etc
     *
     * @return  Language object
     * @since   1.0
     */
    public function loadLanguage()
    {
        $locale = $this->get('language', 'en-gb');
        $this->_language = MolajoLanguage::getInstance($locale);
    }

    /**
     * loadDispatcher
     *
     * Method to create an event dispatcher for the Web application.  The logic and options for creating
     * this object are adequately generic for default cases but for many applications it will make sense
     * to override this method and create event dispatchers based on more specific needs.
     *
     * @return  void
     *
     * @since   1.0
     */
    protected function loadDispatcher()
    {
//        $this->dispatcher = JDispatcher::getInstance();
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
//        if ($this->dispatcher instanceof JDispatcher) {
//            $this->dispatcher->register($event, $handler);
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
     * @return  array   An array of results from each function call, or null if no dispatcher is defined.
     *
     * @since   1.0
     */
    public function triggerEvent($event, array $args = null)
    {
//        if ($this->dispatcher instanceof JDispatcher) {
//            return $this->dispatcher->trigger($event, $args);
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

        /** load session messages into application messages array */
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
     * Retrieve messages in session and load into Application messages array
     *
     * @return  void
     * @since   1.0
     */
    private function _sessionMessages()
    {
        $session = $this->getSession();
        $sessionMessages = $session->get('application.messages');

        if (count($sessionMessages) > 0) {
            $count = count($this->_messages);
            foreach ($sessionMessages as $sessionMessage) {
                $this->_messages[$count] = $sessionMessage;
                $count++;
            }
            $session->set('application.messages', null);
        }
    }

    /**
     * setEscape
     *
     * Sets the escape method
     *
     * @param  string
     *
     * @return  void
     */
    function setEscape($escapeFunction)
    {
        if (is_callable($escapeFunction)) {
            $this->_escapeFunction = $escapeFunction;
        }
    }

    /**
     * escape
     *
     * If escaping mechanism is either htmlspecialchars or htmlentities, uses encoding setting
     *
     * @param   mixed  $var  The output to escape.
     *
     * @return  mixed  The escaped value.
     * @since   1.0
     */
    function escape($var)
    {
        if (in_array($this->_escapeFunction, array('htmlspecialchars', 'htmlentities'))) {
            return call_user_func($this->_escapeFunction, $var, ENT_COMPAT, 'utf-8');
        }
        return call_user_func($this->_escapeFunction, $var);
    }

    /**
     * stringURLSafe
     *
     * This method transliterates a string into an URL
     * safe string or returns a URL safe UTF-8 string
     * based on the global configuration
     *
     * @param   string  $string  String to process
     *
     * @return  string  Processed string
     *
     * @since  1.0
     */
    static public function stringURLSafe($string)
    {
        if (self::get('unicode_slugs') == 1) {
            $output = JFilterOutput::stringURLUnicodeSlug($string);

        } else {
            $output = JFilterOutput::stringURLSafe($string);
        }

        return $output;
    }

    /**
     * getHash
     *
     * Provides a secure hash based on a seed
     *
     * @param   string   $seed  Seed string.
     *
     * @return  string   A secure hash
     *
     * @since  1.0
     */
    public static function getHash($seed)
    {
        return md5(self::get('secret') . $seed);
    }
}
