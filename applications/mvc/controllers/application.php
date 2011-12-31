<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
// addCustomTag
/**
 * Molajo Application Class
 *
 * Base class
 */
class MolajoControllerApplication
{
    /**
     * @var    object  The application static instance.
     * @since  1.0
     */
    protected static $instance;

    /**
     * @var    object  The application configuration object.
     * @since  1.0
     */
    public $config = null;

    /**
     * @var    Client  The application client object.
     * @since  1.0
     */
    public $client;

    /**
     * @var    object  The application dispatcher object.
     * @since  1.0
     */
    public $dispatcher;

    /**
     * @var    object  The application session object.
     * @since  1.0
     */
    public $session;

    /**
     * @var    string
     * @since  1.0
     */
    public $line_end;

    /**
     * @var    string
     * @since  1.0
     */
    public $tab;

    /**
     * @var    string
     * @since  1.0
     */
    public $title;

    /**
     * @var    string
     * @since  1.0
     */
    public $description;

    /**
     * @var    string
     * @since  1.0
     */
    public $base;

    /**
     * @var    string
     * @since  1.0
     */
    public $link;

    /**
     * @var    string
     * @since  1.0
     */
    public $links;

    /**
     * @var    Date   Response last modified value
     * @since  1.0
     */
    public $last_modified;

    /**
     * @var    object  The application language object.
     * @since  1.0
     */
    public $language;

    /**
     * @var    string  Language direction
     * @since  1.0
     */
    public $direction;

    /**
     * @var    string
     * @since  1.0
     */
    public $generator;


    /**
     * @var    string
     * @since  1.0
     */
    public $format;

    /**
     * @var    array
     * @since  1.0
     */
    public $metadata = array();

    /**
     * @var    string  Response mimetype type.
     * @since  1.0
     */
    public $mimetype = 'text/html';

    /**
     * @var    string  Character encoding
     * @since  1.0
     */
    public $charset = 'utf-8';

    /**
     * Callback for escaping.
     *
     * @var string
     */
    protected $escape = 'htmlspecialchars';

    /**
     * @var    array
     * @since  1.0
     */
    public $scripts = array();

    /**
     * @var    array
     * @since  1.0
     */
    public $script = array();

    /**
     * @var    array
     * @since  1.0
     */
    public $stylesheets = array();

    /**
     * @var    array
     * @since  1.0
     */
    public $style = array();

    /**
     * @var    array
     * @since  1.0
     */
    public $messages = array();

    /**
     * @var    object  The application response object.
     * @since  1.0
     */
    protected $response = array();

    /**
     * Class constructor.
     *
     * @param   mixed  $config  An optional argument to provide dependency injection for the application's
     *                          config object.  If the argument is a JRegistry object that object will become
     *                          the application's config object, otherwise a default config object is created.
     * @param   mixed  $client  An optional argument to provide dependency injection for the application's
     *                          client object.  If the argument is a JWebClient object that object will become
     *                          the application's client object, otherwise a default client object is created.
     *
     * @since   1.0
     */
    public function __construct(JRegistry $config = null, JWebClient $client = null, $options = array())
    {
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

        if (array_key_exists('line_end', $options)) {
            $this->setLineEnd($options['line_end']);
        }

        if (array_key_exists('charset', $options)) {
            $this->setCharset($options['charset']);
        }

        if (array_key_exists('language', $options)) {
            $this->setLanguage($options['language']);
        }

        if (array_key_exists('direction', $options)) {
            $this->setDirection($options['direction']);
        }

        if (array_key_exists('tab', $options)) {
            $this->setTab($options['tab']);
        }

        if (array_key_exists('link', $options)) {
            $this->setLink($options['link']);
        }

        if (array_key_exists('base', $options)) {
            $this->setBase($options['base']);
        }

        $this->getConfig();
        echo '<pre>';
        var_dump($this->config);
        '</pre>';
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
        $this->response = new stdClass;
        $this->response->cachable = false;
        $this->response->headers = array();
        $this->response->body = array();

        //echo '<pre>';var_dump($this->config);'</pre>';
    }

    /**
     * Returns a reference to the global Application object, only creating it if it doesn't already exist.
     *
     * This method must be invoked as: $web = Application::getInstance();
     *
     * @param   string  $name  The name (optional) of the Application class to instantiate.
     *
     * @return  Application
     *
     * @since   1.0
     */
    public static function getInstance($id = null, $config = null, $prefix = 'Molajo')
    {
        if ($id == null) {
            $id = MOLAJO_APPLICATION;
        }

        if (empty(self::$instance)) {

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

            self::$instance = new MolajoControllerApplication();
        }

        return self::$instance;
    }

    /**
     * Load the application.
     *
     * @return  nothing
     *
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

        /** execute the extension layer */
        $extension = new MolajoExtension();
        $extension->load();

        /** response */
        $this->respond();

        //        echo '<pre>';
        //        var_dump($extension);
        //        '</pre>';
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
        $configClass = new MolajoConfiguration();
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
     * setLineEnd
     *
     * Sets the line end style to Windows, Mac, Unix or a custom string.
     *
     * @param   string  $style  "win", "mac", "unix" or custom string.
     *
     * @return  void
     */
    public function setLineEnd($style)
    {
        switch ($style)
        {
            case 'win':
                $this->line_end = "\15\12";
                break;
            case 'unix':
                $this->line_end = "\12";
                break;
            case 'mac':
                $this->line_end = "\15";
                break;
            default:
                $this->line_end = $style;
        }
    }

    /**
     * getLineEnd
     *
     * Returns the lineEnd
     *
     * @return  string
     */
    public function getLineEnd()
    {
        return $this->line_end;
    }

    /**
     * setTab
     *
     * Sets the string used to indent HTML
     *
     * @param   string  $string  String used to indent ("\11", "\t", '  ', etc.).
     *
     * @return  void
     */
    public function setTab($string)
    {
        $this->tab = $string;
    }

    /**
     * getTab
     *
     * Returns a string containing the unit for indenting HTML
     *
     * @return  string
     */
    public function getTab()
    {
        return $this->tab;
    }

    /**
     * Adds a shortcut icon (favicon)
     *
     * This adds a link to the icon shown in the favorites list or on
     * the left of the url in the address bar. Some browsers display
     * it on the tab, as well.
     *
     * @param   string  $href      The link that is being related.
     * @param   string  $type      File type
     * @param   string  $relation  Relation of link
     *
     * @return  JDocumentHTML instance of $this to allow chaining
     *
     * @since   11.1
     */
    public function addFavicon($href, $type = 'image/vnd.microsoft.icon', $relation = 'shortcut icon')
    {
        $href = str_replace('\\', '/', $href);
        $this->addHeadLink($href, $relation, 'rel', array('type' => $type));
    }

    /**
     * Adds <link> tags to the head of the document
     *
     * $relType defaults to 'rel' as it is the most common relation type used.
     * ('rev' refers to reverse relation, 'rel' indicates normal, forward relation.)
     * Typical tag: <link href="index.php" rel="Start">
     *
     * @param   string  $href      The link that is being related.
     * @param   string  $relation  Relation of link.
     * @param   string  $relType   Relation type attribute.  Either rel or rev (default: 'rel').
     * @param   array   $attribs   Associative array of remaining attributes.
     *
     * @return  JDocumentHTML instance of $this to allow chaining
     *
     * @since   11.1
     */
    public function addHeadLink($href, $relation, $relType = 'rel', $attribs = array())
    {
        $this->links[$href]['relation'] = $relation;
        $this->links[$href]['relType'] = $relType;
        $this->links[$href]['attribs'] = $attribs;
    }

    /**
     * setFormat
     *
     * Sets the format of the response
     *
     * @param   string    $title
     *
     * @return  void
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * getFormat
     *
     * Return the format of the response.
     *
     * @return  string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * setTitle
     *
     * Sets the title of the document
     *
     * @param   string    $title
     *
     * @return  void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * getTitle
     *
     * Return the title of the document.
     *
     * @return  string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * setDescription
     *
     * Sets the description of the document
     *
     * @param  string  $title
     *
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * getDescription
     *
     * Return the title of the page.
     *
     * @return  string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * setBase
     *
     * Sets the base URI of the document
     *
     * @param  string  $base
     *
     * @return void
     */
    public function setBase($base)
    {
        $this->base = $base;
    }

    /**
     * getBase
     *
     * Return the base URI of the document.
     *
     * @return  string
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * setLink
     *
     * Sets the document link
     *
     * @param  string  $url  A url
     *
     * @return  void
     */
    public function setLink($url)
    {
        $this->link = $url;
    }

    /**
     * getLink
     *
     * Returns the document base url
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * setLastModified
     *
     * Sets the document modified date
     *
     * @param  string
     *
     * @return  void
     */
    public function setLastModified($date)
    {
        $this->last_modified = $date;
    }

    /**
     * getLastModified
     *
     * Returns the document modified date
     *
     * @return string
     */
    public function getLastModified()
    {
        return $this->last_modified;
    }

    /**
     * setLanguage
     *
     * Sets the global document language declaration. Default is English (en-gb).
     *
     * @param   string    $lang
     *
     * @return  void
     */
    public function setLanguage($language = 'en-gb')
    {
        $this->language = strtolower($language);
    }

    /**
     * getLanguage
     *
     * Returns the document language.
     *
     * @return string
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
     * setGenerator
     *
     * Sets the document generator
     *
     * @param  string
     *
     * @return  void
     */
    public function setGenerator($generator)
    {
        $this->generator = $generator;
    }

    /**
     * getGenerator
     *
     * Returns the document generator
     *
     * @return string
     */
    public function getGenerator()
    {
        return $this->generator;
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
            $result = $this->metaTags[$context][$name];

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
     * https://github.com/bigbangireland/joomla-platform/commit/7a89d3dfd78047d53cdbd5ccbfeeb5cc44d599d7#L0R395
     * @return  string
     * @since   1.0
     */
    public function getMetaData($name, $context = false)
    {
        $name = strtolower($name);

        if (is_bool($context) && ($context == true)) {
            $result = $this->metadata['http-equiv'][$name];

        } else if (is_string($context)) {
            $result = $this->metaTags[$context][$name];

        } else {
            $result = $this->metadata['standard'][$name];
        }

        return $result;
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
     */
    public function getMimeEncoding()
    {
        return $this->mimetype;
    }

    /**
     * setCharset
     *
     * Sets the charset
     *
     * @param  string
     *
     * @return  void
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * getCharset
     *
     * Returns the charset
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }


    /**
     * loadMediaJS
     *
     * Loads the JS located within the folder specified by the filepath
     *
     * @param $filePath
     * @param $urlPath
     * @return void
     */
    public function loadMediaJS($filePath, $urlPath)
    {
        if (JFolder::exists($filePath . '/js')) {
        } else {
            return;
        }
        //todo: differentiate between script and scripts
        $files = JFolder::files($filePath . '/js', '\.js$', false, false);

        if (count($files) > 0) {
            foreach ($files as $file) {
                $this->addScript($urlPath . '/js/' . $file);
            }
        }
    }

    /**
     * addScript
     *
     * Adds a linked script to the page
     *
     * @param string $url
     * @param string $format
     * @param bool   $defer
     * @param bool   $async
     * @param int    $priority
     *
     * @return
     * @since   1.0
     */
    public function addScript($url, $format = "text/javascript", $defer = false, $async = false, $priority = 500)
    {
        $this->scripts[$url]['mimetype'] = $format;
        $this->scripts[$url]['defer'] = $defer;
        $this->scripts[$url]['async'] = $async;
        $this->scripts[$url]['priority'] = $priority;
    }

    /**
     * addScriptDeclaration
     *
     * Adds a script to the page
     *
     * @param   string  $content    Script
     * @param   string  $format     Scripting mimetype (defaults to 'text/javascript')
     *
     * @return  void
     * @since    1.0
     */
    public function addScriptDeclaration($content, $format = 'text/javascript')
    {
        if (isset($this->script[strtolower($format)])) {
            $this->script[strtolower($format)] .= chr(13) . $content;
        } else {
            $this->script[strtolower($format)] = $content;
        }
    }

    /**
     * loadMediaCSS
     *
     * Loads the CS located within the folder, as specified by the filepath
     *
     * @param $filePath
     * @param $urlPath
     * @return void
     */
    public function loadMediaCSS($filePath, $urlPath)
    {
        if (JFolder::exists($filePath . '/css')) {
        } else {
            return;
        }

        $files = JFolder::files($filePath . '/css', '\.css$', false, false);

        if (count($files) > 0) {
            foreach ($files as $file) {
                if (substr($file, 0, 4) == 'rtl_') {
                    //                    if ($this->direction == 'rtl') {
                    //                        $this->addStyleSheet($urlPath . '/css/' . $file);
                    //                    }
                } else {
                    $this->addStyleSheet($urlPath . '/css/' . $file);
                }
            }
        }
    }

    /**
     * addStyleSheet
     *
     * Adds a linked stylesheet to the page
     *
     * @param string  $url
     * @param string  $format
     * @param null    $media
     * @param array   $attribs
     * @param int     $priority
     *
     * @return  void
     * @since    1.0
     */
    public function addStyleSheet($url, $format = 'text/css', $media = null, $attribs = array(), $priority = 500)
    {
        $this->stylesheets[$url]['mimetype'] = $format;
        $this->stylesheets[$url]['media'] = $media;
        $this->stylesheets[$url]['attribs'] = $attribs;
        $this->stylesheets[$url]['priority'] = $priority;
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
        if (isset($this->style[strtolower($format)])) {
            $this->style[strtolower($format)] .= chr(13) . $content;

        } else {
            $this->style[strtolower($format)] = $content;
        }
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

        if ($this->response->cachable === true) {
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
     * setMessage
     *
     * Set the system message.
     *
     * @param   string  $message
     * @param   string  $type      message, notice, warning, and error
     *
     * @return  void
     * @since   1.0
     */
    public function setMessage($message = null, $type = 'message')
    {
        /** $message */
        if ($message == null) {
            $message = 'Unknown message';
        }

        /** $type */
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

        /** store the latest message */
        $this->messages[] = array('message' => $message, 'type' => $type);
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
        $session = MolajoFactory::getSession();
        $sessionMessages = $session->get('application.messages');

        if (count($sessionMessages) > 0) {
            foreach ($sessionMessages as $sessionMessage) {
                $this->messages[] = $sessionMessage;
            }
            $session->set('application.messages', null);
        }
    }

    /**
     * Redirect to the URL for a specified asset ID
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
    public function redirect($asset_id, $code = 303)
    {
        /** todo: remove asset_id - should not be known in the application layer */
        /** retrieve url */
        $url = MOLAJO_BASE_URL . MOLAJO_APPLICATION_URL_PATH . MolajoAsset::getRedirectURL((int)$asset_id);

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
            $this->response->cachable = (bool)$allow;
        }

        return $this->response->cachable;
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
            foreach ($this->response->headers as $key => $header)
            {
                if ($name == $header['name']) {
                    unset($this->response->headers[$key]);
                }
            }

            // Clean up the array as unsetting nested arrays leaves some junk.
            $this->response->headers = array_values($this->response->headers);
        }

        // Add the header to the internal array.
        $this->response->headers[] = array('name' => $name, 'value' => $value);

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
        return $this->response->headers;
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
        $this->response->headers = array();

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
            foreach ($this->response->headers as $header) {
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
        $this->response->body = array((string)$content);

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
        array_unshift($this->response->body, (string)$content);

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
        array_push($this->response->body, (string)$content);

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
            return $this->response->body;
        } else {
            return implode('', $this->response->body);
        }
    }

    /**
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
     * Method to create a language for the Web application.  The logic and options for creating this
     * object are adequately generic for default cases but for many applications it will make sense
     * to override this method and create language objects based on more specific needs.
     *
     * @return  void
     *
     * @since   1.0
     */
    protected function loadLanguage()
    {
        $this->language = MolajoFactory::getLanguage();
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
        if (self::get('unicodeslugs') == 1) {
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
