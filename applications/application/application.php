<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo Application Class
 *
 * Base class
 */
class MolajoApplication
{
    /**
     * @var    JInput  The application input object.
     * @since  11.3
     */
    public $input;

    /**
     * @var    string  Character encoding string.
     * @since  11.3
     */
    public $charSet = 'utf-8';

    /**
     * @var    string  Response mime type.
     * @since  11.3
     */
    public $mimeType = 'text/html';

    /**
     * @var    JDate  The body modified date for response headers.
     * @since  11.3
     */
    public $modifiedDate;

    /**
     * @var    JWebClient  The application client object.
     * @since  11.3
     */
    public $client;

    /**
     * @var    JRegistry  The application configuration object.
     * @since  11.3
     */
    protected $config;

    /**
     * @var    JDispatcher  The application dispatcher object.
     * @since  11.3
     */
    protected $dispatcher;

    /**
     * @var    MolajoLanguage  The application language object.
     * @since  11.3
     */
    protected $language;

    /**
     * @var    MolajoLanguage  The application language object.
     * @since  11.3
     */
    protected $direction;

    /**
     * @var    Session  The application session object.
     * @since  11.3
     */
    protected $session;

    /**
     * @var    object  The application response object.
     * @since  11.3
     */
    protected $response;

    /**
     * @var    Application  The application instance.
     * @since  11.3
     */
    protected static $instance;

    /**
     * Class constructor.
     *
     * @param   mixed  $input   An optional argument to provide dependency injection for the application's
     *                          input object.  If the argument is a JInput object that object will become
     *                          the application's input object, otherwise a default input object is created.
     * @param   mixed  $config  An optional argument to provide dependency injection for the application's
     *                          config object.  If the argument is a JRegistry object that object will become
     *                          the application's config object, otherwise a default config object is created.
     * @param   mixed  $client  An optional argument to provide dependency injection for the application's
     *                          client object.  If the argument is a JWebClient object that object will become
     *                          the application's client object, otherwise a default client object is created.
     *
     * @since   11.3
     */
    public function __construct(JInput $input = null, JRegistry $config = null, JWebClient $client = null)
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

        $this->getConfig();

        /** now */
        $this->set('execution.datetime', gmdate('Y-m-d H:i:s'));
        $this->set('execution.timestamp', time());

        /** ssl check for application */
        if ($this->get('force_ssl') >= 1) {
            if (isset($_SERVER['HTTPS'])) {
            } else {
                $this->redirect((string)'https'.substr(MOLAJO_BASE_URL, 4, strlen(MOLAJO_BASE_URL) - 4).MOLAJO_APPLICATION_URL_PATH.'/'.MOLAJO_PAGE_REQUEST);
            }
        }

        /** response */
        $this->response = new stdClass;
        $this->response->cachable = false;
        $this->response->headers = array();
        $this->response->body = array();

        //echo '<pre>';var_dump($this);'</pre>';
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
     * @since   11.3
     */
    public static function getInstance($id = null, $config = array(), $prefix = 'Molajo')
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

            self::$instance = new MolajoApplication();
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
        $site = new MolajoSite ();
        $authorise = $site->authorise(MOLAJO_APPLICATION_ID);
        if ($authorise === false) {
            return MolajoError::raiseError(500, MolajoTextHelper::sprintf('MOLAJO_SITE_NOT_AUTHORISED_FOR_APPLICATION', MOLAJO_APPLICATION_ID));
        }

        /** initialize */
        $this->loadSession();
        $this->loadLanguage();
        $this->loadDispatcher();

        /** extension layer */
        $extension = new MolajoExtension();
        $extension->load();

        /** response */
        $this->respond();

        //echo '<pre>';var_dump($this);'</pre>';
    }

    /**
     * getMetaData
     *
     * Gets a meta tag.
     *
     * @param   string  $name        Value of name or http-equiv tag
     * @param   bool    $http_equiv  META type "http-equiv" defaults to null
     *
     * @return  string
     * @since   1.0
     */
    public function getMetaData($name, $http_equiv = false)
    {
        $result = '';
        $name = strtolower($name);
        if ($name == 'generator') {
            $result = $this->getGenerator();
        }
        else if ($name == 'description') {
            $result = $this->getDescription();

        } else {
            if ($http_equiv == true) {
                $result = @$this->_metaTags['http-equiv'][$name];
            } else {

                $result = @$this->_metaTags['standard'][$name];
            }
        }

        return $result;
    }

    /**
     * setMetaData
     *
     * Sets or alters a meta tag.
     *
     * @param   string   $name        Value of name or http-equiv tag
     * @param   string   $content     Value of the content tag
     * @param   bool     $http_equiv  META type "http-equiv" defaults to null
     * @param   bool     $sync        Should http-equiv="content-type" by synced with HTTP-header?
     *
     * @return  void
     * @since   1.0
     */
    public function setMetaData($name, $content, $http_equiv = false, $sync = true)
    {
        $name = strtolower($name);

        if ($name == 'generator') {
            $this->setGenerator($content);

        } else if ($name == 'description') {
            $this->setDescription($content);

        } else {
            if ($http_equiv == true) {
                $this->_metaTags['http-equiv'][$name] = $content;

                // Syncing with HTTP-header
                if ($sync && strtolower($name) == 'content-type') {
                    $this->setMimeEncoding($content, false);
                }

            } else {
                $this->_metaTags['standard'][$name] = $content;
            }
        }
    }

    /**
     * addScript
     *
     * Adds a linked script to the page
     *
     * @param   string  $url        URL to the linked script
     * @param   string  $format     Type of script. Defaults to 'text/javascript'
     * @param   bool    $defer      Adds the defer attribute.
     * @param   bool    $async      Adds the async attribute.
     * @return
     * @since    1.0
     */
    public function addScript($url, $format = "text/javascript", $defer = false, $async = false)
    {
        $this->_scripts[$url]['mime'] = $format;
        $this->_scripts[$url]['defer'] = $defer;
        $this->_scripts[$url]['async'] = $async;
    }

    /**
     * addScriptDeclaration
     *
     * Adds a script to the page
     *
     * @param   string  $content    Script
     * @param   string  $format     Scripting mime (defaults to 'text/javascript')
     *
     * @return  void
     * @since    1.0
     */
    public function addScriptDeclaration($content, $format = 'text/javascript')
    {
        if (!isset($this->_script[strtolower($format)])) {
            $this->_script[strtolower($format)] = $content;

        } else {
            $this->_script[strtolower($format)] .= chr(13) . $content;
        }
    }

    /**
     * addStyleSheet
     *
     * Adds a linked stylesheet to the page
     *
     * @param   string  $url      URL to the linked style sheet
     * @param   string  $format   Mime encoding type
     * @param   string  $media    Media type that this stylesheet applies to
     * @param   array   $attribs  Array of attributes
     *
     * @return  void
     * @since    1.0
     */
    public function addStyleSheet($url, $format = 'text/css', $media = null, $attribs = array())
    {
        $this->_styleSheets[$url]['mime'] = $format;
        $this->_styleSheets[$url]['media'] = $media;
        $this->_styleSheets[$url]['attribs'] = $attribs;
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
        if (!isset($this->_style[strtolower($format)])) {
            $this->_style[strtolower($format)] = $content;

        } else {
            $this->_style[strtolower($format)] .= chr(13) . $content;
        }
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
    public function setLanguage($lang = "en-gb")
    {
        $this->language = strtolower($lang);
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
    public function setDirection($dir = "ltr")
    {
        $this->direction = strtolower($dir);
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
        $this->_generator = $generator;
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
        return $this->_generator;
    }

    /**
     * setModifiedDate
     *
     * Sets the document modified date
     *
     * @param  string
     *
     * @return  void
     */
    public function setModifiedDate($date)
    {
        $this->_mdate = $date;
    }

    /**
     * getModifiedDate
     *
     * Returns the document modified date
     *
     * @return string
     */
    public function getModifiedDate()
    {
        return $this->_mdate;
    }

    /**
     * setMimeEncoding
     *
     * Sets the document MIME encoding that is sent to the browser.
     *
     * This usually will be text/html because most browsers cannot yet
     * accept the proper mime settings for XHTML: application/xhtml+xml
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
        $this->_mime = strtolower($format);

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
        return $this->_mime;
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
                $this->_lineEnd = "\15\12";
                break;
            case 'unix':
                $this->_lineEnd = "\12";
                break;
            case 'mac':
                $this->_lineEnd = "\15";
                break;
            default:
                $this->_lineEnd = $style;
        }
    }

    /**
     * _getLineEnd
     *
     * Returns the lineEnd
     *
     * @return  string
     */
    public function _getLineEnd()
    {
        return $this->_lineEnd;
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
        $this->_tab = $string;
    }

    /**
     * _getTab
     *
     * Returns a string containing the unit for indenting HTML
     *
     * @return  string
     */
    public function _getTab()
    {
        return $this->_tab;
    }

    /**
     * Method to send the application response to the client.  All headers will be sent prior to the main
     * application output data.
     *
     * @return  void
     *
     * @since   11.3
     */
    public function respond()
    {
        $this->triggerEvent('onBeforeRespond');

        // If gzip compression is enabled in configuration and the server is compliant, compress the output.
        if ($this->get('gzip')
            && !ini_get('zlib.output_compression')
            && (ini_get('output_handler') != 'ob_gzhandler')) {
            $this->compress();
        }

        // Send the content-type header.
        $this->setHeader('Content-Type', $this->mimeType . '; charset=' . $this->charSet);

        // If the response is set to uncachable, we need to set some appropriate headers so browsers don't cache the response.
        if ($this->response->cachable) {
            $this->setHeader('Expires', gmdate('D, d M Y H:i:s', time() + 900) . ' GMT');
            // Last modified.
            if ($this->modifiedDate instanceof JDate) {
                $this->setHeader('Last-Modified', $this->modifiedDate->format('D, d M Y H:i:s'));
            }

        } else {
            // Expires in the past.
            $this->setHeader('Expires', 'Mon, 1 Jan 2001 00:00:00 GMT', true);
            // Always modified.
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
     * @since   11.3
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
                $this->setHeader('X-Content-Encoded-By', 'Joomla');

                // Replace the output with the encoded data.
                $this->setBody($gzdata);

                // Compression complete, let's break out of the loop.
                break;
            }
        }
    }

    /**
     * Redirect to the URL for a specified asset ID
     *
     * URL PHP Constants set in root index.php =>
     * MOLAJO_BASE_URL - protocol, host and path + / (ex. http://localhost/molajo/)
     * MOLAJO_APPLICATION_URL_PATH - slug for application (ex. administrator or '' for site)
     * .'/'.
     * MOLAJO_PAGE_REQUEST - remaining (ex. index.php?option=articles&view=article&layout=default or articles)
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
     * @since   11.3
     */
    public function redirect($asset_id, $code = 303)
    {
        /** retrieve url */
        $url = MOLAJO_BASE_URL.MOLAJO_APPLICATION_URL_PATH.MolajoAsset::getRedirectURL((int) $asset_id);

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
                $html .= '<meta http-equiv="content-type" content="text/html; charset=' . $this->charSet . '" />';
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
                $html .= '<meta http-equiv="content-type" content="text/html; charset=' . $this->charSet . '" />';
                $html .= '</head><body></body></html>';

                echo $html;
            }
            else
            {
                // All other cases use the more efficient HTTP header for redirection.
                $this->header($code ? 'HTTP/1.1 301 Moved Permanently' : 'HTTP/1.1 303 See other');
                $this->header('Location: ' . $url);
                $this->header('Content-Type: text/html; charset=' . $this->charSet);
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
     * @since   11.3
     */
    public function close($code = 0)
    {
        exit($code);
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
     * @since   11.3
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
     * @since   11.3
     */
    public function set($key, $value = null)
    {
        $this->config->set($key, $value);
    }

    /**
     * Registers a handler to a particular event group.
     *
     * @param   string    $event    The event name.
     * @param   callback  $handler  The handler, a function or an instance of a event object.
     *
     * @return  Application  Instance of $this to allow chaining.
     *
     * @since   11.3
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
     * @since   11.3
     */
    public function triggerEvent($event, array $args = null)
    {
        if ($this->dispatcher instanceof JDispatcher) {
            return $this->dispatcher->trigger($event, $args);
        }

        return null;
    }

    /**
     * Set/get cachable state for the response.  If $allow is set, sets the cachable state of the
     * response.  Always returns the current state.
     *
     * @param   boolean  $allow  True to allow browser caching.
     *
     * @return  boolean
     *
     * @since   11.3
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
     * @since   11.3
     */
    public function setHeader($name, $value, $replace = false)
    {
        // Sanitize the input values.
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
     * @since   11.3
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
     * @since   11.3
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
     * @since   11.3
     */
    public function sendHeaders()
    {
        if (!$this->checkHeadersSent()) {
            foreach ($this->response->headers as $header)
            {
                if ('status' == strtolower($header['name'])) {
                    // 'status' headers indicate an HTTP status, and need to be handled slightly differently
                    $this->header(ucfirst(strtolower($header['name'])) . ': ' . $header['value'], null, (int)$header['value']);
                }
                else
                {
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
     * @since   11.3
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
     * @since   11.3
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
     * @since   11.3
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
     * @since   11.3
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
     * @since   11.3
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Method to check the current client connnection status to ensure that it is alive.  We are
     * wrapping this to isolate the connection_status() function from our code base for testing reasons.
     *
     * @return  boolean  True if the connection is valid and normal.
     *
     * @codeCoverageIgnore
     * @see     connection_status()
     * @since   11.3
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
     * @since   11.3
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
     * @since   11.3
     */
    protected function header($string, $replace = true, $code = null)
    {
        header($string, $replace, $code);
    }

    /**
     * Method to create an event dispatcher for the Web application.  The logic and options for creating
     * this object are adequately generic for default cases but for many applications it will make sense
     * to override this method and create event dispatchers based on more specific needs.
     *
     * @return  void
     *
     * @since   11.3
     */
    protected function loadDispatcher()
    {
        $this->dispatcher = JDispatcher::getInstance();
    }

    /**
     * Method to create a language for the Web application.  The logic and options for creating this
     * object are adequately generic for default cases but for many applications it will make sense
     * to override this method and create language objects based on more specific needs.
     *
     * @return  void
     *
     * @since   11.3
     */
    protected function loadLanguage()
    {
        $this->language = JFactory::getLanguage();
    }

    /**
     * Method to create a session for the Web application.  The logic and options for creating this
     * object are adequately generic for default cases but for many applications it will make sense
     * to override this method and create session objects based on more specific needs.
     *
     * @return  void
     *
     * @since   11.3
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
