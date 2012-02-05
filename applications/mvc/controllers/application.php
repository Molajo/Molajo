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
class MolajoApplicationController
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
    protected static $_config = null;

    /**
     * Application static database results
     *
     * @var    object
     * @since  1.0
     */
    protected static $_appQueryResults = null;

    /**
     * Application custom fields
     *
     * @var    object
     * @since  1.0
     */
    protected static $_custom_fields = null;

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
     * Client
     *
     * @var    object
     * @since  1.0
     */
    protected $_client;

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
     * Response Mimetype
     *
     * @var    string
     * @since  1.0
     */
    protected $_mimetype = 'text/html';

    /**
     * Links
     *
     * @var    string
     * @since  1.0
     */
    protected $_links;

    /**
     * Metadata
     *
     * @var    array
     * @since  1.0
     */
    protected $_page_view_metadata = array();

    /**
     * Stylesheet links
     *
     * @var    array
     * @since  1.0
     */
    protected $_style_links = array();

    /**
     * Style Declarations
     *
     * @var    array
     * @since  1.0
     */
    protected $_style_declarations = array();

    /**
     * Script Links
     *
     * @var    string
     * @since  1.0
     */
    protected $_script_links = array();

    /**
     * Script Declarations
     *
     * @var    array
     * @since  1.0
     */
    protected $_script_declarations = array();

    /**
     * Custom HTML
     *
     * @var    array
     * @since  1.0
     */
    protected $_custom_html = array();

    /**
     * Response
     *
     * @var    object
     * @since  1.0
     */
    protected $_response = array();

    /**
     * getInstance
     *
     * Returns a reference to the global Application object,
     *  only creating it if it doesn't already exist.
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

            $_appQueryResults = AppHelper::getApplicationInfo($id);
            if ($_appQueryResults === false) {
                return false;
            }

            if (defined('MOLAJO_APPLICATION_PATH')) {
            } else {
                define('MOLAJO_APPLICATION_PATH', MOLAJO_APPLICATIONS_CORE . '/applications/' . $_appQueryResults->path);
            }

            if (defined('MOLAJO_APPLICATION_ID')) {
            } else {
                define('MOLAJO_APPLICATION_ID', $_appQueryResults->id);
            }

            self::$instance = new MolajoApplicationController($config, $input, $_appQueryResults);
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
    public function __construct(JRegistry $config = null, JInput $input = null, $_appQueryResults = null)
    {
        if ($input instanceof JInput) {
            $this->_input = $input;
        } else {
            $this->_input = new JInput;
        }

        if ($config instanceof JRegistry) {
            $this->_config = $config;
        } else {
            $this->_config = new JRegistry;
        }

        /** Database results from application helpers */
        $this->_appQueryResults = $_appQueryResults;

        /** get configuration */
        $this->getConfig();

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

        /** response */
        $this->_response = new stdClass;
        $this->_response->cachable = false;
        $this->_response->headers = array();
        $this->_response->body = array();

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
        $sc = new MolajoSiteController ();
        $authorise = $sc->authorise(MOLAJO_APPLICATION_ID);
        if ($authorise === false) {
            $message = '304: ' . MOLAJO_BASE_URL;
            echo $message;
            die;
        }

        /** initialise */
        $this->loadSession();
        $this->loadLanguage();
        $this->loadDispatcher();

        /** request  */
        $rc = Molajo::Request();
        $rc->process();

        /** parser and renderer */
        if ($rc->get('mvc_task') == 'add'
            || $rc->get('mvc_task') == 'edit'
            || $rc->get('mvc_task') == 'display'
        ) {

            /** process include statements in theme and views */
            $pc = Molajo::Parser();
            $rc->process();

            /** action task: insert, update, or delete */
        } else {
            //$this->_processTask();
        }

        /** response */
        $this->respond();

        return;
    }

    /**
     * getConfig
     *
     * Creates the Application configuration object.
     *
     * @return  null
     * @since   1.0
     */
    public function getConfig()
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
        $this->_language = MolajoLanguageController::getInstance($locale);
    }

    /**
     * Sets the global document language declaration. Default is English (en-gb).
     *
     * @param   string  $language
     *
     * @return  void
     * @since   1.0
     */
    public function setLanguage($language = "en-gb")
    {
        $this->_language = strtolower($language);
    }

    /**
     * Returns the document language.
     *
     * @return  string
     * @since   1.0
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * setDirection
     *
     * Sets the global document direction declaration. Default is left-to-right (ltr).
     *
     * @param   string  $lang
     *
     * @return  void
     * @since   1.0
     */
    public function setDirection($direction = 'ltr')
    {
        if (strtolower($direction) == 'rtl') {
        } else {
            $direction = 'ltr';
        }
        $this->_direction = strtolower($direction);
    }

    /**
     * getDirection
     *
     * Returns the document direction declaration.
     *
     * @return string
     */
    public function getDirection()
    {
        return $this->_direction;
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
     * setMetadata
     *
     * @param   string  $name
     * @param   string  $content  Value of the content tag
     * @param   string  $context  True: http-equiv; False: standard; Otherise, provided
     * @param   bool    $sync     Should http-equiv="content-type" by synced with HTTP-header?
     *
     * @return  void
     * @since   1.0
     */
    public function setMetadata($name, $content, $context = false, $sync = true)
    {
        $name = strtolower($name);

        if (is_bool($context) && ($context === true)) {
            $this->_page_view_metadata['http-equiv'][$name] = $content;

            if ($sync && strtolower($name) == 'content-type') {
                $this->setMimeEncoding($content, false);
            }

        } else if (is_string($context)) {
            $result = $this->_page_view_metadata[$context][$name];

        } else {
            $this->_page_view_metadata['standard'][$name] = $content;
        }
    }

    /**
     * getMetadata
     *
     * Gets a metadata tag.
     *
     * @param   string  $name     Value of name or http-equiv tag
     * @param   bool    $context  true - http-equiv; false - standard; otherwise provided
     * @return  string
     * @since   1.0
     */
    public function getMetadata()
    {
        return $this->_page_view_metadata;
    }

    /**
     * setMimeEncoding
     *
     * Sets the document MIME encoding that is sent to the browser.
     *
     * This usually will be text/html because most browsers cannot yet
     * accept the proper mimetype settings for XHTML: application/xhtml+xml
     * and to a lesser extent application/xml and text/xml. See the W3C note
     * ({@link http://www.w3.org/TR/xhtml-media-types/
     * http://www.w3.org/TR/xhtml-media-types/}) for more details.
     *
     * @param   string  $format
     * @param   bool    $sync  Should the type be synced with HTML?
     *
     * @return  void
     * @since   1.0
     */
    public function setMimeEncoding($format = 'text/html', $sync = true)
    {
        $this->_mimetype = strtolower($format);

        if ($sync) {
            $this->setMetadata('content-type', $format, true, false);
        }
    }

    /**
     * getMimeEncoding
     *
     * Return the document MIME encoding that is sent to the browser.
     *
     * @return  string
     * @since   1.0
     */
    public function getMimeEncoding()
    {
        return $this->_mimetype;
    }

    /**
     * Adds <link> tags to the head of the document
     *
     * $relation_type defaults to 'rel' as it is the most common relation type used.
     * ('rev' refers to reverse relation, 'rel' indicates normal, forward relation.)
     * Typical tag: <link href="index.php" rel="Start">
     *
     * @param   string  $url           The link that is being related.
     * @param   string  $relation      Relation of link.
     * @param   string  $relation_type Relation type attribute. Either rel or rev (default: 'rel').
     * @param   array   $attributes    Associative array of remaining attributes.
     *
     * @param $url
     * @param $relation
     * @param string $relation_type
     * @param array $attributes
     * @return mixed
     */
    public function addHeadLink($url, $relation, $relation_type = 'rel', $attributes = array())
    {
        $count = count($this->_links);
        if ($count > 0) {
            foreach ($this->_links as $link) {
                if ($link['url'] == $url) {
                    return;
                }
            }
        }
        $this->_links[$count]['url'] = $url;
        $this->_links[$count]['relation'] = $relation;
        $this->_links[$count]['relation_type'] = $relation_type;
        $this->_links[$count]['attributes'] = trim(implode(' ', $attributes));
    }

    /**
     * getHeadLink
     *
     * @return array
     */
    public function getHeadLinks()
    {
        return $this->_links;
    }

    /**
     * addCustomHTML
     *
     * Adds a custom HTML string to the head block
     *
     * @param   string  $html  The HTML to add to the head
     * @return  void
     * @since   1.0
     */

    public function addCustomHTML($html)
    {
        $this->_custom_html[] = trim($html);
    }

    /**
     * getCustomHTML
     *
     * @return array
     * @since  1.0
     */
    public function getCustomHTML()
    {
        return $this->_custom_html;
    }

    /**
     * addStyleLinksFolder
     *
     * Loads the CS located within the folder, as specified by the filepath
     *
     * @param $filePath
     * @param $urlPath
     * @return void
     */
    public function addStyleLinksFolder($filePath, $urlPath, $priority = 500)
    {
        if (JFolder::exists($filePath . '/css')) {
        } else {
            return;
        }

        $files = JFolder::files($filePath . '/css', '\.css$', false, false);

        if (count($files) > 0) {
            foreach ($files as $file) {
                if (substr($file, 0, 4) == 'rtl_') {
                    if ($this->getDirection() == 'rtl') {
                        $this->addStyleLinks($urlPath . '/css/' . $file, $priority);
                    }
                } else {
                    $this->addStyleLinks($urlPath . '/css/' . $file, $priority);
                }
            }
        }
    }

    /**
     * addStyleLinks
     *
     * Adds a linked stylesheet to the page
     *
     * @param  string $url
     * @param  int    $priority
     * @param  string $mimetype
     * @param  null   $media
     * @param  array  $attributes
     *
     * @return mixed
     * @since  1.0
     */
    public function addStyleLinks($url, $priority = 500, $mimetype = 'text/css', $media = null, $attributes = array())
    {
        $count = count($this->_style_links);
        if ($count > 0) {
            foreach ($this->_style_links as $stylesheet) {
                if ($stylesheet['url'] == $url) {
                    return;
                }
            }
        }
        $this->_style_links[$count]['url'] = $url;
        $this->_style_links[$count]['mimetype'] = $mimetype;
        $this->_style_links[$count]['media'] = $media;
        $this->_style_links[$count]['attributes'] = trim(implode(' ', $attributes));
        $this->_style_links[$count]['priority'] = $priority;
    }

    /**
     * getStyleLinks
     *
     * @return array
     * @since  1.0
     */
    public function getStyleLinks()
    {
        return $this->_style_links;
    }

    /**
     * addStyleDeclaration
     *
     * Adds a stylesheet declaration to the page
     *
     * @param   string  $content  Style declarations
     * @param   string  $format   Type of stylesheet (defaults to 'text/css')
     *
     * @return  void
     * @since   1.0
     */
    public function addStyleDeclaration($content, $mimetype = 'text/css')
    {
        $count = count($this->_style_declarations);
        if ($count > 0) {
            foreach ($this->_style_declarations as $style) {
                if ($style['content'] == $content) {
                    return;
                }
            }
        }
        $this->_style_declarations[$count]['mimetype'] = $mimetype;
        $this->_style_declarations[$count]['content'] = $content;
    }

    /**
     * getStyleDeclarations
     *
     * @return array
     * @since  1.0
     */
    public function getStyleDeclarations()
    {
        return $this->_style_declarations;
    }

    /**
     * addScriptLinksFolder
     *
     * Loads the JS Files located within the folder specified by the filepath
     *
     * @param  $filePath
     * @param  $urlPath
     * @return void
     * @since  1.0
     */
    public function addScriptLinksFolder($filePath, $urlPath, $priority = 500, $defer = 0)
    {
        if ($defer == 1) {
            $extra = '/js/defer';
        } else {
            $extra = '/js';
            $defer = 0;
        }
        if (JFolder::exists($filePath . $extra)) {
        } else {
            return;
        }
        $files = JFolder::files($filePath . $extra, '\.js$', false, false);

        if (count($files) > 0) {
            foreach ($files as $file) {
                $this->addScriptLink($urlPath . $extra . '/' . $file, $priority, $defer, 'text/javascript');
            }
        }
    }

    /**
     * addScriptLink
     *
     * Adds a linked script to the page
     *
     * @param  $url
     * @param  int $priority
     * @param  string $mimetype
     * @param  bool $defer
     * @param  bool $async
     *
     * @return mixed
     * @since  1.0
     */
    public function addScriptLink($url, $priority = 500, $defer = 0, $mimetype = "text/javascript", $async = false)
    {
        if ($defer == 1) {
        } else {
            $defer = 0;
        }

        $count = count($this->_script_links);

        if ($count > 0) {
            foreach ($this->_script_links as $script) {
                if ($script['url'] == $url) {
                    return;
                }
            }
        }

        $this->_script_links[$count]['url'] = $url;
        $this->_script_links[$count]['mimetype'] = $mimetype;
        $this->_script_links[$count]['defer'] = $defer;
        $this->_script_links[$count]['async'] = $async;
        $this->_script_links[$count]['priority'] = $priority;
    }

    /**
     * getScriptLinks
     *
     * @return array
     */
    public function getScriptLinks($defer = 0)
    {
        if ($defer == 1) {
        } else {
            $defer = 0;
        }

        $results = array();

        $count = count($this->_script_links);

        if ($count > 0) {
            foreach ($this->_script_links as $script) {
                if ($script['defer'] == $defer) {
                    $results[] = $script;
                }
            }
        }
        return $results;
    }

    /**
     * addScriptDeclaration
     *
     * Adds a script to the page
     *
     * @param  string  $content    Script
     * @param  string  $format     Scripting mimetype (defaults to 'text/javascript')
     * @param  string  $defer
     *
     * @return  void
     * @since    1.0
     */
    public function addScriptDeclaration($content, $mimetype = 'text/javascript', $defer = 0)
    {
        if ($defer == 1) {
        } else {
            $defer = 0;
        }

        $count = count($this->_script_declarations);

        if ($count > 0) {
            foreach ($this->_script_declarations as $script) {
                if ($script['content'] == $script) {
                    return;
                }
            }
        }

        $this->_script_declarations[$count]['mimetype'] = $mimetype;
        $this->_script_declarations[$count]['content'] = $content;
        $this->_script_declarations[$count]['defer'] = $defer;
    }

    /**
     * getScriptDeclarations
     *
     * @param bool $defer
     * @return array
     */
    public function getScriptDeclarations($defer = 0)
    {
        if ($defer == 1) {
        } else {
            $defer = 0;
        }

        $results = array();

        $count = count($this->_script_declarations);

        if ($count > 0) {
            foreach ($this->_script_declarations as $script) {
                if ($script['defer'] == $defer) {
                    $results[] = $script;
                }
            }
        }
        return $results;
    }

    /**
     * respond
     *
     * Method to send the application response.  All headers sent before application output.
     *
     * @return  void
     * @since   1.0
     */
    public function respond()
    {
//        $this->triggerEvent('onBeforeRespond');

        // If gzip compression is enabled in configuration and the server is compliant, compress the output.
        if ($this->get('gzip')) {
            if (ini_get('zlib.output_compression')) {
            } elseif (ini_get('output_handler') == 'ob_gzhandler') {
            } else {
                $this->compress();
            }
        }

        // Send the content-type header.
        $this->setHeader('Content-Type', $this->_mimetype . '; charset=utf-8');

        if ($this->_response->cachable === true) {
            $this->setHeader('Expires', gmdate('D, d M Y H:i:s', time() + 900) . ' GMT');
            if ($this->last_modified instanceof JDate) {
                $this->setHeader('Last-Modified', $this->last_modified->format('D, d M Y H:i:s'));
            }
        } else {
            $this->setHeader('Expires', 'Fri, 6 Jan 1989 00:00:00 GMT', true);
            $this->setHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT', true);
            $this->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0', false);
            // HTTP 1.0
            $this->setHeader('Pragma', 'no-cache');
        }

        $this->sendHeaders();

        echo $this->getBody();

//        $this->triggerEvent('onAfterRespond');
    }

    /**
     * Checks the accept encoding of the browser and compresses the data before
     * sending it to the client if possible.
     *
     * @return  void
     *
     * @since   1.0
     */
    protected function compress()
    {
        // Supported compression encodings.
        $supported = array(
            'x-gzip' => 'gz',
            'gzip' => 'gz',
            'deflate' => 'deflate'
        );

        // Get the supported encoding.
        $encodings = array_intersect($this->_client->encodings, array_keys($supported));

        // If no supported encoding is detected do nothing and return.
        if (empty($encodings)) {
            return;
        }

        // Verify that headers have not yet been sent, and that our connection is still alive.
        if ($this->checkHeadersSent() || !$this->checkConnectionAlive()) {
            return;
        }

        // Iterate through the encodings and attempt to compress the data using any found supported encodings.
        foreach ($encodings as $encoding)
        {
            if (($supported[$encoding] == 'gz') || ($supported[$encoding] == 'deflate')) {
                // Verify that the server supports gzip compression before we attempt to gzip encode the data.
                // @codeCoverageIgnoreStart
                if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
                    continue;
                }
                // @codeCoverageIgnoreEnd

                // Attempt to gzip encode the data with an optimal level 4.
                $data = $this->getBody();

                $gzdata = gzencode($data, 4, ($supported[$encoding] == 'gz') ? FORCE_GZIP : FORCE_DEFLATE);

                // If there was a problem encoding the data just try the next encoding scheme.
                // @codeCoverageIgnoreStart
                if ($gzdata === false) {
                    continue;
                }
                // @codeCoverageIgnoreEnd

                // Set the encoding headers.
                $this->setHeader('Content-Encoding', $encoding);
                $this->setHeader('X-Content-Encoded-By', 'Molajo');

                // Replace the output with the encoded data.
                $this->setBody($gzdata);

                // Compression complete, let's break out of the loop.
                break;
            }
        }
    }

    /**
     * Redirect to the URL for a specified pageRequest value
     *
     * URL PHP Constants set in root index.php =>
     * MOLAJO_BASE_URL - protocol, host and path + / (ex. http://localhost/molajo/)
     * MOLAJO_APPLICATION_URL_PATH - slug for application (ex. administrator or '' for site)
     * .'/'.
     * MOLAJO_PAGE_REQUEST - remaining (ex. index.php?option=articles&view=display or edit)
     *
     * If the headers have not been sent the redirect will be accomplished using a "301 Moved Permanently"
     * or "303 See Other" code in the header pointing to the new location. If the headers have already been
     * sent this will be accomplished using a JavaScript statement.
     *
     * @param   string   $url    The URL to redirect to. Can only be http/https URL
     * @param   boolean  $moved  True if the page is 301 Permanently Moved, otherwise 303 See Other is assumed.
     *
     * 301 - Permanent move
     * 303 - Other
     *
     * @return  void
     *
     * @since   1.0
     */
    public function redirect($pageRequest, $code = 303)
    {
        /** sef url options */
        if ($this->get('sef', 1) == 1) {
            if ($this->get('sef_rewrite', 0) == 0) {
                $url = MOLAJO_BASE_URL . MOLAJO_APPLICATION_URL_PATH . 'index.php/' . $pageRequest;
            } else {
                $url = MOLAJO_BASE_URL . MOLAJO_APPLICATION_URL_PATH . $pageRequest;
            }

            if ((int)$this->get('sef_suffix', 0) == 1) {
                $url .= '.html';
            }
        }

        /** validate code */
        if ($code == 301) {
        } else {
            $code = 303;
        }

        $exception = false;

        /** IE */
        if (stripos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false
            || stripos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false
        ) {
            $exception = 'trident';
        }
        if (stripos($_SERVER['HTTP_USER_AGENT'], 'AppleWebKit') !== false
            || stripos($_SERVER['HTTP_USER_AGENT'], 'blackberry') !== false
        ) {
            $exception = 'webkit';
        }

        if ($this->checkHeadersSent()) {
            echo "<script>document.location.href='$url';</script>\n";
        } else {

            /** IE and UTF8 URLs */
            if (($exception == 'trident') && !utf8_is_ascii($url)) {
                $html = '<html><head>';
                $html .= '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
                $html .= '<script>document.location.href=\'' . $url . '\';</script>';
                $html .= '</head><body></body></html>';

                echo $html;
            }
            /*
             * For WebKit based browsers do not send a 303, as it causes subresource reloading.  You can view the
             * bug report at: https://bugs.webkit.org/show_bug.cgi?id=38690
             */
            elseif ($exception == 'webkit' && $code == 303)
            {
                $html = '<html><head>';
                $html .= '<meta http-equiv="refresh" content="0; url=' . $url . '" />';
                $html .= '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
                $html .= '</head><body></body></html>';

                echo $html;

            } else {

                /** normal */
                $this->header($code ? 'HTTP/1.1 301 Moved Permanently' : 'HTTP/1.1 303 See other');
                $this->header('Location: ' . $url);
                $this->header('Content-Type: text/html; charset=utf-8');
            }
        }

        /** close after redirect */
        $this->close();
    }

    /**
     * Exit the application.
     *
     * @param   integer  $code  The exit code (optional; default is 0).
     *
     * @return  void
     *
     * @codeCoverageIgnore
     * @since   1.0
     */
    public function close($code = 0)
    {
        exit($code);
    }

    /**
     * Set/get cachable state for the response.  If $allow is set, sets the cachable state of the
     * response.  Always returns the current state.
     *
     * @param   boolean  $allow  True to allow browser caching.
     *
     * @return  boolean
     *
     * @since   1.0
     */
    public function allowCache($allow = null)
    {
        if ($allow !== null) {
            $this->_response->cachable = (bool)$allow;
        }

        return $this->_response->cachable;
    }

    /**
     * Method to set a response header.  If the replace flag is set then all headers
     * with the given name will be replaced by the new one.  The headers are stored
     * in an internal array to be sent when the site is sent to the browser.
     *
     * @param   string   $name     The name of the header to set.
     * @param   string   $value    The value of the header to set.
     * @param   boolean  $replace  True to replace any headers with the same name.
     *
     * @return  Application  Instance of $this to allow chaining.
     *
     * @since   1.0
     */
    public function setHeader($name, $value, $replace = false)
    {
        $name = (string)$name;
        $value = (string)$value;

        // If the replace flag is set, unset all known headers with the given name.
        if ($replace) {
            foreach ($this->_response->headers as $key => $header)
            {
                if ($name == $header['name']) {
                    unset($this->_response->headers[$key]);
                }
            }

            // Clean up the array as unsetting nested arrays leaves some junk.
            $this->_response->headers = array_values($this->_response->headers);
        }

        // Add the header to the internal array.
        $this->_response->headers[] = array('name' => $name, 'value' => $value);

        return $this;
    }

    /**
     * Method to get the array of response headers to be sent when the response is sent
     * to the client.
     *
     * @return  array
     *
     * @since   1.0
     */
    public function getHeaders()
    {
        return $this->_response->headers;
    }

    /**
     * Method to clear any set response headers.
     *
     * @return  Application  Instance of $this to allow chaining.
     *
     * @since   1.0
     */
    public function clearHeaders()
    {
        $this->_response->headers = array();

        return $this;
    }

    /**
     * Send the response headers.
     *
     * @return  Application  Instance of $this to allow chaining.
     *
     * @since   1.0
     */
    public function sendHeaders()
    {
        if ($this->checkHeadersSent()) {
        } else {
            foreach ($this->_response->headers as $header) {
                if ('status' == strtolower($header['name'])) {
                    // 'status' headers indicate an HTTP status, and need to be handled slightly differently
                    $this->header(ucfirst(strtolower($header['name'])) . ': ' . $header['value'], null, (int)$header['value']);
                } else {
                    $this->header($header['name'] . ': ' . $header['value']);
                }
            }
        }
        return $this;
    }

    /**
     * Set body content.  If body content already defined, this will replace it.
     *
     * @param   string  $content  The content to set as the response body.
     *
     * @return  Application  Instance of $this to allow chaining.
     *
     * @since   1.0
     */
    public function setBody($content)
    {
        $this->_response->body = array((string)$content);

        return $this;
    }

    /**
     * Prepend content to the body content
     *
     * @param   string  $content  The content to prepend to the response body.
     *
     * @return  Application  Instance of $this to allow chaining.
     *
     * @since   1.0
     */
    public function prependBody($content)
    {
        array_unshift($this->_response->body, (string)$content);

        return $this;
    }

    /**
     * Append content to the body content
     *
     * @param   string  $content  The content to append to the response body.
     *
     * @return  Application  Instance of $this to allow chaining.
     *
     * @since   1.0
     */
    public function appendBody($content)
    {
        array_push($this->_response->body, (string)$content);

        return $this;
    }

    /**
     * Return the body content
     *
     * @param   boolean  $asArray  True to return the body as an array of strings.
     *
     * @return  mixed  The response body either as an array or concatenated string.
     *
     * @since   1.0
     */
    public function getBody($asArray = false)
    {
        if ($asArray === true) {
            return $this->_response->body;
        } else {
            return implode('', $this->_response->body);
        }
    }

    /**
     * Method to check the current client connection status to ensure that it is alive.  We are
     * wrapping this to isolate the connection_status() function from our code base for testing reasons.
     *
     * @return  boolean  True if the connection is valid and normal.
     *
     * @codeCoverageIgnore
     * @see     connection_status()
     * @since   1.0
     */
    protected function checkConnectionAlive()
    {
        return (connection_status() === CONNECTION_NORMAL);
    }

    /**
     * Method to check to see if headers have already been sent.  We are wrapping this to isolate the
     * headers_sent() function from our code base for testing reasons.
     *
     * @return  boolean  True if the headers have already been sent.
     *
     * @codeCoverageIgnore
     * @see     headers_sent()
     * @since   1.0
     */
    protected function checkHeadersSent()
    {
        return headers_sent();
    }

    /**
     * Method to send a header to the client.  We are wrapping this to isolate the header() function
     * from our code base for testing reasons.
     *
     * @param   string   $string   The header string.
     * @param   boolean  $replace  The optional replace parameter indicates whether the header should
     *                             replace a previous similar header, or add a second header of the same type.
     * @param   integer  $code     Forces the HTTP response code to the specified value. Note that
     *                             this parameter only has an effect if the string is not empty.
     *
     * @return  void
     *
     * @codeCoverageIgnore
     * @see     header()
     * @since   1.0
     */
    protected function header($string, $replace = true, $code = null)
    {
        header($string, $replace, $code);
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
