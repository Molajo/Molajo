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
     * Application configuration object.
     *
     * @var    integer
     * @since  1.0
     */
    static public $config = null;

    /**
     * Application input object.
     *
     * @var    integer
     * @since  1.0
     */
    public $input = null;

    /**
     * Application Template
     *
     * @var object
     * @since 1.0
     */
    private $template = null;

    /**
     * getInstance
     *
     * Returns the global application object, creating if not existing
     *
     * @param   mixed   $application  A application identifier or name.
     * @param   strong  $prefix       A prefix for class names
     *
     * @return  application object
     *
     * @since  1.0
     */
    public static function getInstance($prefix = 'Molajo')
    {
        static $instances;

        if (isset($instances)) {
        } else {
            $instances = array();
        }

        if (empty($instances[MOLAJO_APPLICATION])) {

            $info = MolajoApplicationHelper::getApplicationInfo(MOLAJO_APPLICATION, true);
            if ($info === false) {
                return false;
            }

            if (defined('MOLAJO_APPLICATION_PATH')) {
            } else {
                define('MOLAJO_APPLICATION_PATH', MOLAJO_APPLICATIONS_CORE . '/' . $info->path);
            }

            if (defined('MOLAJO_APPLICATION_ID')) {
            } else {
                define('MOLAJO_APPLICATION_ID', $info->id);
            }

            MolajoApplicationHelper::loadApplicationClasses();

            $classname = $prefix . ucfirst(MOLAJO_APPLICATION) . 'Application';
            if (class_exists($classname)) {
                $instance = new $classname();
            } else {
                return MolajoError::raiseError(500, MolajoTextHelper::sprintf('MOLAJO_APPLICATION_INSTANTIATION_ERROR', $classname));
            }
            $instances[MOLAJO_APPLICATION] = &$instance;
        }

        return $instances[MOLAJO_APPLICATION];
    }

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param   array  $config  A configuration array
     *
     * @since  1.0
     */
    public function __construct($input = null, $config = null)
    {
        /** Input */
        if ($input) {
            $this->input = $input;
        } else {
            $this->input = new JInput;
        }

        /** Configuration */
        if ($config) {
            $this->config = $config;
        } else {
            $this->config = new JRegistry;
        }

        $this->getConfig();

        // Set the execution datetime and timestamp;
        $this->set('execution.datetime', gmdate('Y-m-d H:i:s'));
        $this->set('execution.timestamp', time());

        // Setup the response object.
        $this->response = new stdClass;
        $this->response->cachable = false;
        $this->response->headers = array();
        $this->response->body = array();

        // Set the system URIs.
        $this->loadSystemUris();

        /** Session */
        if (isset($config['session'])) {
        } else {
            $config['session'] = true;
        }
        if (isset($config['session_name'])) {
        } else {
            $config['session_name'] = MOLAJO_APPLICATION;
        }
        if ($config['session'] === false) {
        } else {
            $sessionHelper = new MolajoSessionHelper ();
            $sessionHelper->createSession(MolajoUtility::getHash($config['session_name']));
        }

        /** Application URI Base */
        if (MOLAJO_APPLICATION == 'site') {
        } else {
            JURI::root(null, str_ireplace('/' . MOLAJO_APPLICATION, '', JURI::base(true)));
        }
    }

    /**
     * initialise
     *
     * Retrieves the configuration information, loads language files, editor, triggers onAfterInitialise
     *
     * @param    array
     *
     * @since 1.0
     */
    public function initialise($options = array())
    {
        /** Language */
        MolajoLanguageHelper::getLanguage($options);
        $this->set('language', $options['language']);

        $language = MolajoFactory::getLanguage();
        $results = $language->load('base', MOLAJO_EXTENSIONS_LANGUAGES);

        /** Editor */
        $editor = MolajoFactory::getUser()->getParameter('editor', $this->get('editor', 'none'));
        if (MolajoPlugin::isEnabled('editors', $editor)) {

        } else {
            $editor = $this->get('editor');
            if (MolajoPlugin::isEnabled('editors', $editor)) {
            } else {
                $editor = 'none';
            }
        }
        $this->set('editor', $editor);

        /** todo: amy get the user's template */

        /** Site authorisation */
        $site = new MolajoSite ();
        $authorise = $site->authorise(MOLAJO_APPLICATION_ID);
        if ($authorise === false) {
            return MolajoError::raiseError(500, MolajoTextHelper::sprintf('MOLAJO_SITE_NOT_AUTHORISED_FOR_APPLICATION', MOLAJO_APPLICATION_ID));
        }

        /** Event */
        MolajoPlugin::importPlugin('system');
        $this->triggerEvent('onAfterInitialise');
    }

    /**
     * route
     *
     * Route the application.
     *
     * Routing is the process of examining the request environment to determine which
     * component should receive the request. The component optional parameters
     * are then set in the request object to be processed when the application is being
     * dispatched.
     *
     * @return  void;
     * @since  1.0
     */
    public function route()
    {
        /** todo: amy 404 processing */
        if ($itemid = JRequest::getInt('Itemid')) {
            $this->authorise($itemid);
        }
        $uri = JURI::getInstance();

        $router = $this->getRouter();
        $result = $router->parse($uri);

        JRequest::set($result, 'get', false);

        if ($this->get('force_ssl') >= 1
            && strtolower($uri->getScheme()) != 'https'
        ) {
            $uri->setScheme('https');
            $this->redirect((string)$uri);
        }

        /** trigger onAfterRoute Event */
        MolajoPlugin::importPlugin('system');
        $this->triggerEvent('onAfterRoute');
    }

    /**
     * authorise
     *
     * Check if the user can access the application
     *
     * @param $itemid
     * @return booleon
     */
    public function authorise($itemid)
    {
        $menus = $this->getMenu();

        if ($menus == null) {
            return false;
        }

        if ($menus->authorise($itemid)) {
            return true;
        }

        /** Not authorized */
        if (MolajoFactory::getUser()->get('guest')) {
            $uri = MolajoFactory::getURI();
            $return = (string)$uri;
            $url = 'index.php?option=users&view=login&return=' . $return;
            $url = MolajoRouteHelper::_($url, false);
            $this->redirect($url, MolajoTextHelper::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'));

            return false;
        }

        MolajoError::raiseError(403, MolajoTextHelper::_('ERROR_NOT_AUTHORIZED'));
        return false;
    }

    /**
     * getMenu
     *
     * Returns the Menu object.
     *
     * @param   string  $name     The name of the application
     * @param   array   $options  An optional associative array of configuration settings.
     *
     * @return  menu object.
     *
     * @since  1.0
     */
    public function getMenu($name = null, $options = array())
    {
        if (isset($name)) {
        } else {
            $name = MOLAJO_APPLICATION;
        }

        $menu = MolajoMenu::getInstance($name, $options);

        if (MolajoError::isError($menu)) {
            return null;
        }
        return $menu;
    }

    /**
     * getRouter
     *
     * Returns the application router object.
     *
     * @param   string  $name     The name of the application.
     * @param   array   $options  An optional associative array of configuration settings.
     *
     * @return  router object
     *
     * @since  1.0
     */
    static public function getRouter($name = null, array $options = array())
    {
        if (isset($name)) {
        } else {
            $name = MOLAJO_APPLICATION;
        }

        $router = MolajoRouter::getInstance($name, $options);
        if (MolajoError::isError($router)) {
            return null;
        }

        return $router;
    }

    /**
     * Dispatch
     *
     * Dispatching is rendering a component, buffering output in the document, and triggering onAfterDispatch
     *
     * @param   string  $option    The component to dispatch.
     *
     * @return  void
     * @since  1.0
     */
    public function dispatch($option = null)
    {
        try
        {
            /** Option */
            $helper = new MolajoComponent ();
            $option = $helper->verifyComponent($option);

            /** Request */
            $request = $helper->getRequest($option);
            //echo '<pre>';var_dump($request);'</pre>';

            /** Document */
            $document = MolajoFactory::getDocument();
            switch ($document->getType()) {
                case 'html':
                    $document->setMetaData('keywords', $this->get('MetaKeys'));
                    break;

                default:
                    break;
            }
            $document->setTitle($this->get('sitename'));
            $document->setDescription($this->get('MetaDesc'));

            /** Render */
            $contents = MolajoComponent::renderComponent($request);

            /** Buffer */
            $document->setBuffer($contents, 'component');

            /** Events */
            MolajoPlugin::importPlugin('system');
            $this->triggerEvent('onAfterDispatch');
        }

        catch (Exception $e)
        {
            $code = $e->getCode();
            MolajoError::raiseError($code ? $code : 500, $e->getMessage());
        }
    }

    /**
     * Render the application.
     *
     * Rendering is the process of pushing the document buffers into the template
     * placeholders, retrieving data from the document and pushing it into
     * the MolajoApplication buffer.
     *
     * @return  void;
     * @since  1.0
     */
    public function render()
    {
        MolajoTemplate::renderTemplate();
    }

    /**
     * getName
     *
     * Method to get the application name.
     *
     * @return  string  The name of the application.
     *
     * @since  1.0
     */
    public function getName()
    {
        return MOLAJO_APPLICATION;
    }

    /**
     * getUserState
     *
     * Gets a user state.
     *
     * @param   string  The path of the state.
     * @param   mixed   Optional default value, returned if the internal value is null.
     *
     * @return  mixed  The user state or null.
     *
     * @since  1.0
     */
    public function getUserState($key, $default = null)
    {
        $session = MolajoFactory::getSession();

        $registry = $session->get('registry');

        if (is_null($registry)) {
        } else {
            return $registry->get($key, $default);
        }

        return $default;
    }

    /**
     * setUserState
     *
     * Sets the value of a user state variable.
     *
     * @param   string  The path of the state.
     * @param   string  The value of the variable.
     *
     * @return  mixed   The previous state, if one existed.
     *
     * @since  1.0
     */
    public function setUserState($key, $value)
    {
        $session = MolajoFactory::getSession();
        $registry = $session->get('registry');

        if (is_null($registry)) {
        } else {
            return $registry->set($key, $value);
        }

        return null;
    }

    /**
     * getUserStateFromRequest
     *
     * Gets the value of a user state variable.
     *
     * @param   string   $key      The key of the user state variable.
     * @param   string   $request  The name of the variable passed in a request.
     * @param   string   $default  The default value for the variable if not found. Optional.
     * @param   string   $type     Filter for the variable, for valid values see {@link JFilterInput::clean()}. Optional.
     *
     * @return  The request user state.
     *
     * @since  1.0
     */
    public function getUserStateFromRequest($key, $request, $default = null, $type = 'none')
    {
        $cur_state = $this->getUserState($key, $default);
        $new_state = JRequest::getVar($request, null, 'default', $type);

        // Save the new value only if it was set in this request.
        if ($new_state == null) {
            $new_state = $cur_state;
        } else {
            $this->setUserState($key, $new_state);
        }

        return $new_state;
    }

    /**
     * getTemplate
     *
     * return the folder name of the template
     * @param $template
     * @return string
     */
    function getTemplate()
    {
        return MolajoTemplate::getTemplate();
    }

    /**
     * Overrides the default template that would be used
     *
     * @param string The template name
     */
    public function setTemplate($template)
    {
        if (is_dir(MOLAJO_EXTENSIONS_TEMPLATES . '/' . $template)) {
            $this->template = new stdClass();
            $this->template->parameters = new JRegistry;
            $this->template->template = $template;
        }
    }

    /**
     * Returns the application pathway object.
     *
     * @param   string    $name     The name of the application.
     * @param   array     $options  An optional associative array of configuration settings.
     *
     * @return  object  A pathway object
     *
     * @since   1.0
     */
    public function getPathway($name = null, $options = array())
    {
        if (isset($name)) {
        } else {
            $name = MOLAJO_APPLICATION;
        }

        $pathway = MolajoPathway::getInstance($name, $options);

        if (MolajoError::isError($pathway)) {
            return null;
        }

        return $pathway;
    }

    /**
     * Redirect to another URL.
     *
     * Optionally enqueues a message in the system message queue (which will be displayed
     * the next time a page is loaded) using the enqueueMessage method. If the headers have
     * not been sent the redirect will be accomplished using a "301 Moved Permanently"
     * code in the header pointing to the new location. If the headers have already been
     * sent this will be accomplished using a JavaScript statement.
     *
     * @param   string   $url      The URL to redirect to. Can only be http/https URL
     * @param   string   $msg      An optional message to display on redirect.
     * @param   string   $msgType  An optional message type. Defaults to message.
     * @param   boolean  $moved    True if the page is 301 Permanently Moved, otherwise 303 See Other is assumed.
     *
     * @return  void  Calls exit().
     *
     * @see     MolajoFactory::getApplication()->enqueueMessage()
     * @since  1.0
     */
    public function redirect($url, $msg = '', $msgType = 'message', $moved = false)
    {
        // Check for relative internal links.
        if (preg_match('#^index2?\.php#', $url)) {
            $url = JURI::base() . $url;
        }

        // Strip out any line breaks.
        $url = preg_split("/[\r\n]/", $url);
        $url = $url[0];

        // If we don't start with a http we need to fix this before we proceed.
        // We could validly start with something else (e.g. ftp), though this would
        // be unlikely and isn't supported by this API.
        if (preg_match('#^http#i', $url)) {
        } else {
            $uri = JURI::getInstance();
            $prefix = $uri->toString(Array('scheme', 'user', 'pass', 'host', 'port'));

            if ($url[0] == '/') {
                // We just need the prefix since we have a path relative to the root.
                $url = $prefix . $url;
            }
            else {
                // It's relative to where we are now, so lets add that.
                $parts = explode('/', $uri->toString(Array('path')));
                array_pop($parts);
                $path = implode('/', $parts) . '/';
                $url = $prefix . $path . $url;
            }
        }

        // If the message exists, enqueue it.
        if (trim($msg)) {
            $this->enqueueMessage($msg, $msgType);
        }

        // Persist messages if they exist.
        if (count($this->_messageQueue)) {
            $session = MolajoFactory::getSession();
            $session->set('application.queue', $this->_messageQueue);
        }

        // If the headers have been sent, then we cannot send an additional location header
        // so we will output a javascript redirect statement.
        if (headers_sent()) {
            echo "<script>document.location.href='$url';</script>\n";

        } else {
            $document = MolajoFactory::getDocument();
            $navigator = JBrowser::getInstance();
            if ($navigator->isBrowser('msie')) {
                // MSIE type browser and/or server cause issues when url contains utf8 character,so use a javascript redirect method
                echo '<html><head><meta http-equiv="content-type" content="text/html; charset=' . $document->getCharset() . '" /><script>document.location.href=\'' . $url . '\';</script></head><body></body></html>';

            } elseif (!$moved and $navigator->isBrowser('konqueror')) {
                // WebKit browser (identified as konqueror by Molajo) - Do not use 303, as it causes subresources reload (https://bugs.webkit.org/show_bug.cgi?id=38690)
                echo '<html><head><meta http-equiv="refresh" content="0; url=' . $url . '" /><meta http-equiv="content-type" content="text/html; charset=' . $document->getCharset() . '" /></head><body></body></html>';
            } else {
                // All other browsers, use the more efficient HTTP header method
                header($moved ? 'HTTP/1.1 301 Moved Permanently' : 'HTTP/1.1 303 See other');
                header('Location: ' . $url);
                header('Content-Type: text/html; charset=' . $document->getCharset());
            }
        }

        $this->close();
    }

    /**
     * Exit the application.
     *
     * @param    integer  $code  Exit code
     *
     * @return   void     Exits the application.
     *
     * @since    1.0
     */
    public function close($code = 0)
    {
        exit($code);
    }

    /**
     *  CONFIGURATION
     */

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
        $data = $configClass->getConfig();

        if (is_array($data)) {
            $this->config->loadArray($data);

        } elseif (is_object($data)) {
            $this->config->loadObject($data);
        }

        return true;
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
        $previous = $this->config->get($key);
        $this->config->set($key, $value);

        return $previous;
    }

    /**
     *  Events
     */

    /**
     * registerEvent
     *
     * Registers a handler to a particular event group.
     *
     * @param   string  $event    The event name.
     * @param   mixed   $handler  The handler, a function or an instance of a event object.
     *
     * @return  void
     *
     * @since  1.0
     */
    public static function registerEvent($event, $handler)
    {
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->register($event, $handler);
    }

    /**
     * triggerEvent
     *
     * Calls all handlers associated with an event group.
     *
     * @param   string  $event  The event name.
     * @param   array   $args   An array of arguments.
     *
     * @return  array  An array of results from each function call.
     *
     * @since  1.0
     */
    function triggerEvent($event, $args = null)
    {
        return JDispatcher::getInstance()->trigger($event, $args);
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

    /**
     * isWinOS
     *
     * Method to determine if the host OS is Windows
     *
     * @return  boolean  True if Windows OS
     *
     * @since  1.0
     */
    static function isWinOS()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    /**
     * __toString
     *
     * Returns the response as a string.
     *
     * @return  string  The response
     *
     * @since  1.0
     */
    public function __toString()
    {
        return MolajoFactory::getApplication()->toString($this->get('gzip', false));
    }
}