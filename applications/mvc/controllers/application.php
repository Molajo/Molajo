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
class MolajoControllerApplication
{
    /**
     * The application static instance.
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * The application input object.
     * @var    object
     * @since  1.0
     */
    public $input;

    /**
     * The application configuration object.
     * @var    object
     * @since  1.0
     */
    public static $config = null;

    /**
     * The application client object.
     * @var    object
     * @since  1.0
     */
    public $client;

    /**
     * Character encoding
     * @var    string
     * @since  1.0
     */
    public $charset = 'utf-8';

    /**
     * The application language object.
     * @var    object
     * @since  1.0
     */
    public $language;

    /**
     * Language direction
     * @var    string
     * @since  1.0
     */
    public $direction = 'ltr';

    /**
     * Callback for escaping.
     * @var   string
     * @since 1.0
     */
    protected $_escapeFunction = 'htmlspecialchars';

    /**
     * Response mimetype type.
     * @var    string
     * @since  1.0
     */
    public $mimetype = 'text/html';

    /**
     * Messages
     * @var    array
     * @since  1.0
     */
    public $messages = array();

    /**
     * Dispatcher
     * @var    object
     * @since  1.0
     */
    public $dispatcher;

    /**
     * Session
     * @var    object
     * @since  1.0
     */
    public $session;

    /**
     * Metadata
     * @var    array
     * @since  1.0
     */
    public $metadata = array();

    /**
     * Links
     * @var    string
     * @since  1.0
     */
    public $links;

    /**
     * stylesheet_links
     * @var    array
     * @since  1.0
     */
    public $stylesheet_links = array();

    /**
     * Style
     * @var    array
     * @since  1.0
     */
    public $stylesheet_declarations = array();

    /**
     * Script Links
     * @var    string
     * @since  1.0
     */
    public $script_links;

    /**
     * Script Declarations
     * @var    array
     * @since  1.0
     */
    public $script_declarations = array();

    /**
     * Custom
     * @var    array
     * @since  1.0
     */
    public $custom_html = array();

    /**
     * Request
     * @var    object
     * @since  1.0
     */
    protected $_request = null;

    /**
     * Response
     * @var    object
     * @since  1.0
     */
    protected $_response = array();

    /**
     * getInstance
     *
     * Returns a reference to the global Application object, only creating it if it doesn't already exist.
     *
     * @static
     * @param  null $id
     * @param  JInput|null $input
     * @param  JRegistry|null $config
     * @param  JWebClient|null $client
     * @param  array $options
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance($id = null, JInput $input = null, JRegistry $config = null, JWebClient $client = null, $options = array())
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

            if ($client instanceof JWebClient) {
            } else {
                $client = new JWebClient;
            }

            $info = MolajoApplicationHelper::getApplicationInfo($id, true);
            if ($info === false) {
                return false;
            }

            if (defined('MOLAJO_APPLICATION_PATH')) {
            } else {
                define('MOLAJO_APPLICATION_PATH', MOLAJO_APPLICATIONS_CORE . '/applications/' . $info->path);
            }

            if (defined('MOLAJO_APPLICATION_ID')) {
            } else {
                define('MOLAJO_APPLICATION_ID', $info->id);
            }

            self::$instance = new MolajoControllerApplication($input, $config, $client, $options);
        }

        return self::$instance;
    }

    /**
     * Class constructor.
     *
     * @param   mixed  $input
     * @param   mixed  $config
     * @param   mixed  $client
     * @param   mixed  $options
     *
     * @since   1.0
     */
    public function __construct(JInput $input = null, JRegistry $config = null, JWebClient $client = null, $options = array())
    {
        if ($input instanceof JInput) {
            $this->input = $input;
        } else {
            $this->input = new JInput;
        }

        if ($config instanceof JRegistry) {
            $this->config = $config;
        } else {
            $this->config = new JRegistry;
        }

        if ($client instanceof JWebClient) {
            $this->client = $client;
        } else {
            $this->client = new JWebClient;
        }

        /** get configuration */
        $this->getConfig();

        /** now */
        $this->set('execution.datetime', gmdate('Y-m-d H:i:s'));
        $this->set('execution.timestamp', time());

        /** ssl check for application */
        if ($this->get('force_ssl') >= 1) {
            if (isset($_SERVER['HTTPS'])) {
            } else {
                $this->redirect((string)'https' . substr(MOLAJO_BASE_URL, 4, strlen(MOLAJO_BASE_URL) - 4) . MOLAJO_APPLICATION_URL_PATH . '/' . MOLAJO_PAGE_REQUEST);
            }
        }

        /** response */
        $this->_response = new stdClass;
        $this->_response->cachable = false;
        $this->_response->headers = array();
        $this->_response->body = array();

        //echo '<pre>';var_dump($this->config);'</pre>';
    }

    /**
     * Sets the document link
     *
     * @param   string  $url  A url
     *
     * @return  JDocument instance of $this to allow chaining
     *
     * @since   11.1
     */
    public function setLink($url)
    {
        $this->link = $url;

        return $this;
    }

    /**
     * Returns the document link
     *
     * @return string
     *
     * @since   11.1
     */
    public function getLink()
    {
        return $this->link;
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
     * escapeOutput
     *
     * If escaping mechanism is either htmlspecialchars or htmlentities, uses encoding setting
     *
     * @param   mixed  $var  The output to escape.
     *
     * @return  mixed  The escaped value.
     * @since   1.0
     */
    function escapeOutput($var)
    {
        if (in_array($this->_escapeFunction, array('htmlspecialchars', 'htmlentities'))) {
            return call_user_func($this->_escapeFunction, $var, ENT_COMPAT, $this->charset);
        }
        return call_user_func($this->_escapeFunction, $var);
    }

    /**
     * getConfig
     *
     * Creates the Application configuration object.
     *
     * return   object  A config object
     *
     * @since  1.0
     */
    public function getConfig()
    {
        $configClass = new MolajoConfigurationHelper();
        $this->config = $configClass->getConfig();
        return $this->config;
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
    public function get($key, $default = null)
    {
        return $this->config->get($key, $default);
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
    public function set($key, $value = null)
    {
        $this->config->set($key, $value);
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
        $site = new MolajoControllerSite ();
        $authorise = $site->authorise(MOLAJO_APPLICATION_ID);
        if ($authorise === false) {
            return MolajoError::raiseError(500, MolajoTextHelper::sprintf('MOLAJO_SITE_NOT_AUTHORISED_FOR_APPLICATION', MOLAJO_APPLICATION_ID));
        }

        /** initialise */
        $this->loadSession();

        $this->loadLanguage();

        $this->loadDispatcher();

        /**        $this->setMessage('Test message', MOLAJO_MESSAGE_TYPE_WARNING);

        /** request and rendering  */
        $requestClass = new MolajoRequest();
        $this->_request = $requestClass->load();

        /** send response */
        $this->respond();

        return true;
        //        echo '<pre>';
        //        var_dump($request);
        //        '</pre>';
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
        $name = md5($this->get('secret') . $this->get('session_name', get_class($this)));

        // Calculate the session lifetime.
        $lifetime = (($this->get('session_lifetime')) ? $this->get('session_lifetime') * 60 : 900);

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
            $session->set('user', new JUser);
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
     * Create a language object
     *
     * @see MolajoLanguageHelper
     *
     * @return MolajoLanguageHelper object
     * @since   1.0
     */
    public function loadLanguage()
    {
        $locale = $this->get('language', 'en-gb');
        $debug = $this->get('debug_language', '');
        $this->language = MolajoLanguageHelper::getInstance($locale, $debug);
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
        $this->language = strtolower($language);
    }

    /**
     * Returns the document language.
     *
     * @return  string
     * @since   1.0
     */
    public function getLanguage()
    {
        return $this->language;
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
        $this->direction = strtolower($direction);
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
        return $this->direction;
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
        $this->dispatcher = JDispatcher::getInstance();
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
        if ($this->dispatcher instanceof JDispatcher) {
            $this->dispatcher->register($event, $handler);
        }

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
        if ($this->dispatcher instanceof JDispatcher) {
            return $this->dispatcher->trigger($event, $args);
        }

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
    public function setMessage($message = null, $type = 'message')
    {
        if ($message == null) {
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
        /** todo: amy - see if sessionMessages are actually set anywhere or where this should be done */
        /** load session messages into application messages array */
        $this->_sessionMessages();

        /** add new message */
        $this->messages[] = array('message' => $message, 'type' => $type);

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
        return $this->messages;
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
            foreach ($sessionMessages as $sessionMessage) {
                $this->messages[] = $sessionMessage;
            }
            $session->set('application.messages', null);
        }
    }

    /**
     * setMetaData
     *
     * Sets or alters a meta tag.
     *
     * @param   string   $name     Value of name or http-equiv tag
     * @param   string   $content  Value of the content tag
     * @param   string   $context  true - http-equiv; false - standard; otherwise provided
     * @param   bool     $sync     Should http-equiv="content-type" by synced with HTTP-header?
     *
     * @return  void
     * @since   1.0
     */
    public function setMetaData($name, $content, $context = false, $sync = true)
    {
        $name = strtolower($name);

        if (is_bool($context) && ($context === true)) {
            $this->metadata['http-equiv'][$name] = $content;

            if ($sync && strtolower($name) == 'content-type') {
                $this->setMimeEncoding($content, false);
            }

        } else if (is_string($context)) {
            $result = $this->metadata[$context][$name];

        } else {
            $this->metadata['standard'][$name] = $content;
        }
    }

    /**
     * getMetaData
     *
     * Gets a metadata tag.
     *
     * @param   string  $name     Value of name or http-equiv tag
     * @param   bool    $context  true - http-equiv; false - standard; otherwise provided
     * @return  string
     * @since   1.0
     */
    public function getMetaData()
    {
        return $this->metadata;
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
        $this->mimetype = strtolower($format);

        if ($sync) {
            $this->setMetaData('content-type', $format, true, false);
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
        return $this->mimetype;
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
        $count = count($this->links);
        if ($count > 0) {
            foreach ($this->links as $link) {
                if ($link['url'] == $url) {
                    return;
                }
            }
        }
        $this->links[$count]['url'] = $url;
        $this->links[$count]['relation'] = $relation;
        $this->links[$count]['relation_type'] = $relation_type;
        $this->links[$count]['attributes'] = trim(implode(' ', $attributes));
    }

    /**
     * getHeadLink
     *
     * @return array
     */
    public function getHeadLinks()
    {
        return $this->links;
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
        $this->custom_html[] = trim($html);
    }

    /**
     * getCustomHTML
     *
     * @return array
     * @since  1.0
     */
    public function getCustomHTML()
    {
        return $this->custom_html;
    }

    /**
     * addStylesheetLinksFolder
     *
     * Loads the CS located within the folder, as specified by the filepath
     *
     * @param $filePath
     * @param $urlPath
     * @return void
     */
    public function addStylesheetLinksFolder($filePath, $urlPath)
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
                        $this->addStylesheetLinks($urlPath . '/css/' . $file);
                    }
                } else {
                    $this->addStylesheetLinks($urlPath . '/css/' . $file);
                }
            }
        }
    }

    /**
     * addStylesheetLinks
     *
     * Adds a linked stylesheet to the page
     *
     * @param $url
     * @param string $mimetype
     * @param null $media
     * @param array $attributes
     * @param int $priority
     * @return mixed
     */
    public function addStylesheetLinks($url, $mimetype = 'text/css', $media = null, $attributes = array(), $priority = 500)
    {
        $count = count($this->stylesheet_links);
        if ($count > 0) {
            foreach ($this->stylesheet_links as $stylesheet) {
                if ($stylesheet['url'] == $url) {
                    return;
                }
            }
        }
        $this->stylesheet_links[$count]['url'] = $url;
        $this->stylesheet_links[$count]['mimetype'] = $mimetype;
        $this->stylesheet_links[$count]['media'] = $media;
        $this->stylesheet_links[$count]['attributes'] = trim(implode(' ', $attributes));
        $this->stylesheet_links[$count]['priority'] = $priority;
    }

    /**
     * getStylesheetLinks
     *
     * @return array
     */
    public function getStylesheetLinks()
    {
        return $this->stylesheet_links;
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
     */
    public function addStyleDeclaration($content, $format = 'text/css')
    {
        if (isset($this->style_declarations[strtolower($format)])) {
            $this->style_declarations[strtolower($format)] .= chr(13) . $content;

        } else {
            $this->style_declarations[strtolower($format)] = $content;
        }
    }

    /**
     * getStyleDeclarations
     *
     * @return array
     */
    public function getStyleDeclarations()
    {
        return $this->style_declarations;
    }


    /**
     * addJavascriptLinksFolder
     *
     * Loads the JS Files located within the folder specified by the filepath
     *
     * @param  $filePath
     * @param  $urlPath
     * @return void
     * @since  1.0
     */
    public function addJavascriptLinksFolder($filePath, $urlPath)
    {
        if (JFolder::exists($filePath . '/js')) {
        } else {
            return;
        }
        $files = JFolder::files($filePath . '/js', '\.js$', false, false);
        if (count($files) > 0) {
            foreach ($files as $file) {
                $this->addJavascriptLink($urlPath . '/js/' . $file);
            }
        }
    }

    /**
     * addJavascriptLink
     *
     * Adds a linked script to the page
     *
     * @param $url
     * @param string $mimetype
     * @param bool $defer
     * @param bool $async
     * @param int $priority
     * @return mixed
     */
    public function addJavascriptLink($url, $mimetype = "text/javascript", $defer = false, $async = false, $priority = 500)
    {
        $count = count($this->script_links);
        if ($count > 0) {
            foreach ($this->script_links as $script) {
                if ($script['url'] == $url) {
                    return;
                }
            }
        }
        $this->script_links[$count]['url'] = $url;
        $this->script_links[$count]['mimetype'] = $mimetype;
        $this->script_links[$count]['defer'] = $defer;
        $this->script_links[$count]['async'] = $async;
        $this->script_links[$count]['priority'] = $priority;
    }

    /**
     * getJavascriptLinks
     *
     * @return array
     */
    public function getJavascriptLinks()
    {
        return $this->script_links;
    }

    /**
     * addJavascriptDeclaration
     *
     * Adds a script to the page
     *
     * @param   string  $content    Script
     * @param   string  $format     Scripting mimetype (defaults to 'text/javascript')
     *
     * @return  void
     * @since    1.0
     */
    public function addJavascriptDeclaration($content, $format = 'text/javascript')
    {
        if (isset($this->script_declarations[strtolower($format)])) {
            $this->script_declarations[strtolower($format)] .= chr(13) . $content;
        } else {
            $this->script_declarations[strtolower($format)] = $content;
        }
    }

    /**
     * getJavascriptDeclarations
     *
     * @return array
     */
    public function getJavascriptDeclarations()
    {
        return $this->script_declarations;
    }

    /**
     * Method to send the application response to the client.  All headers will be sent prior to the main
     * application output data.
     *
     * @return  void
     *
     * @since   1.0
     */
    public function respond()
    {
        $this->triggerEvent('onBeforeRespond');

        // If gzip compression is enabled in configuration and the server is compliant, compress the output.
        if ($this->get('gzip')) {
            if (ini_get('zlib.output_compression')) {
            } elseif (ini_get('output_handler') == 'ob_gzhandler') {
            } else {
                $this->compress();
            }
        }

        // Send the content-type header.
        $this->setHeader('Content-Type', $this->mimetype . '; charset=' . $this->charset);

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

        $this->triggerEvent('onAfterRespond');
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
        $encodings = array_intersect($this->client->encodings, array_keys($supported));

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
        /** retrieve url */
        $url = MOLAJO_BASE_URL . MOLAJO_APPLICATION_URL_PATH . $pageRequest;

        /** validate code */
        if ($code == 301) {
        } else {
            $code = 303;
        }

        // If the headers have already been sent we need to send the redirect statement via JavaScript.
        if ($this->checkHeadersSent()) {
            echo "<script>document.location.href='$url';</script>\n";
        } else {

            // We have to use a JavaScript redirect here because MSIE doesn't play nice with utf-8 URLs.
            if (($this->client->engine == JWebClient::TRIDENT) && !utf8_is_ascii($url)) {
                $html = '<html><head>';
                $html .= '<meta http-equiv="content-type" content="text/html; charset=' . $this->charset . '" />';
                $html .= '<script>document.location.href=\'' . $url . '\';</script>';
                $html .= '</head><body></body></html>';

                echo $html;
            }
            /*
             * For WebKit based browsers do not send a 303, as it causes subresource reloading.  You can view the
             * bug report at: https://bugs.webkit.org/show_bug.cgi?id=38690
             */
            elseif ($code == 303 and ($this->client->engine == JWebClient::WEBKIT))
            {
                $html = '<html><head>';
                $html .= '<meta http-equiv="refresh" content="0; url=' . $url . '" />';
                $html .= '<meta http-equiv="content-type" content="text/html; charset=' . $this->charset . '" />';
                $html .= '</head><body></body></html>';

                echo $html;

            } else {
                // All other cases use the more efficient HTTP header for redirection.
                $this->header($code ? 'HTTP/1.1 301 Moved Permanently' : 'HTTP/1.1 303 See other');
                $this->header('Location: ' . $url);
                $this->header('Content-Type: text/html; charset=' . $this->charset);
            }
        }

        // Close the application after the redirect.
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
