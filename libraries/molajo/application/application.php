<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
//todo: amy configuration option for applications
/**
 * MolajoApplication
 *
 * Acts as a Factory class for application specific objects and supporting API functions
 */
class MolajoApplication extends JObject
{
    /**
     * The application identifier.
     *
     * @var    integer
     * @since  1.0
     */
    protected $_applicationId = null;

    /**
     * The application message queue.
     *
     * @var    array
     * @since  1.0
     */
    protected $_messageQueue = array();

    /**
     * The name of the application.
     *
     * @var    array
     * @since  1.0
     */
    protected $_name = null;

    /**
     * The scope of the application.
     *
     * @var    string
     * @since  1.0
     */
    public $scope = null;

    /**
     * The time the request was made.
     *
     * @var    date
     * @since  1.0
     */
    public $requestTime = null;

    /**
     * The time the request was made, expressed as a Unix timestamp.
     *
     * @var    integer
     * @since  1.0
     */
    public $startTime = null;

    /**
     * The application input object.
     *
     * @var    integer
     * @since  1.0
     */
    public $input = null;

    /**
     * @var object $template
     *
     * @since 1.0
     */
    private $template = null;

    /**
     * @var bool $_language_filter
     *
     * @since 1.0
     */
    private $_language_filter = false;

    /**
     * @var bool $_detect_browser
     *
     * @since 1.0
     */
    private $_detect_browser = false;

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param   array  $config  A configuration array
     *
     * @since  1.0
     */
    public function __construct($config = array())
    {
        /** Application ID and Name */
        $config['applicationId'] = MOLAJO_APPLICATION_ID;
        $this->_applicationId = MOLAJO_APPLICATION_ID;
        $this->_name = MOLAJO_APPLICATION;

        /** Input Object */
        if (class_exists('JInput')) {
            $this->input = new JInput;
        }

        /** Session */
        if (isset($config['session'])) {
        } else {
            $config['session'] = true;
        }
        if (isset($config['session_name'])) {
        } else {
            $config['session_name'] = $this->_name;
        }
        if ($config['session'] === false) {
        } else {
            $sessionHelper = new MolajoSessionHelper ();
            $sessionHelper->createSession(MolajoUtility::getHash($config['session_name']));
        }

        /** Configuration File */
        if (isset($config['config_file'])) {
        } else {
            $config['config_file'] = 'configuration.php';
        }
        if ($this->_name == 'installation') {
            $this->_createConfiguration();
        } else {
            $this->_createConfiguration(MOLAJO_SITE_PATH . '/' . $config['config_file']);
        }

        /** Application URI Base */
        if (MOLAJO_APPLICATION == 'site') {
        } else {
            JURI::root(null, str_ireplace('/' . MOLAJO_APPLICATION, '', JURI::base(true)));
        }

        /** stats */
        $this->set('requestTime', gmdate('Y-m-d H:i'));
        $this->set('startTime', JProfiler::getmicrotime());
    }

    /**
     * getInstance
     *
     * Returns the global application object, creating if not existing
     *
     * @param   mixed   $application  A application identifier or name.
     * @param   array   $config       An optional associative array of configuration settings.
     * @param   strong  $prefix       A prefix for class names
     *
     * @return  application object
     *
     * @since  1.0
     */
    public static function getInstance($application, $config = array(), $prefix = 'Molajo')
    {
        static $instances;

        if (isset($instances)) {
        } else {
            $instances = array();
        }

        if (empty($instances[$application])) {

            $info = MolajoApplicationHelper::getApplicationInfo($application, true);
            if ($info === false) {
                return false;
            }

            if (defined('MOLAJO_APPLICATION_PATH')) {
            } else {
                define('MOLAJO_APPLICATION_PATH', MOLAJO_APPLICATIONS_PATH . '/' . $info->path);
            }

            if (defined('MOLAJO_APPLICATION_ID')) {
            } else {
                define('MOLAJO_APPLICATION_ID', $info->id);
            }

            /** verify site is authorised to access this application */
            $site = new MolajoSite ();
            $authorise = $site->authorise(MOLAJO_APPLICATION_ID);
            if ($authorise === false) {
                return MolajoError::raiseError(500, MolajoText::sprintf('MOLAJO_SITE_NOT_AUTHORISED_FOR_APPLICATION', MOLAJO_APPLICATION_ID));
            }

            $results = MolajoApplicationHelper::loadApplicationClasses();
            if ($results === false) {
                return false;
            }

            $classname = $prefix . ucfirst($application) . 'Application';
            if (class_exists($classname)) {
                $instance = new $classname($config);
            } else {
                return MolajoError::raiseError(500, MolajoText::sprintf('MOLAJO_APPLICATION_INSTANTIATION_ERROR', $classname));
            }
            $instances[$application] = &$instance;
        }

        return $instances[$application];
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
        /** Language Determination */
        $config = MolajoFactory::getConfig();

        /** 1. request */
        if (empty($options['language'])) {
            $language = JRequest::getString('language', null);
            if ($language && MolajoLanguage::exists($language)) {
                $options['language'] = $language;
            }
        }

        /** 2. user option for application */
        if (empty($options['language'])) {
            $language = MolajoFactory::getUser()->getParam('language');
            if ($language && MolajoLanguage::exists($language)) {
                $options['language'] = $language;
            }
        }

        /** 3. browser detection */
        if ($this->_detect_browser && empty($options['language'])) {
            $language = MolajoLanguageHelper::detectLanguage();
            if ($language && MolajoLanguage::exists($language)) {
                $options['language'] = $language;
            }
        }

        /** 4. site default for application */
        if (empty($options['language'])) {
            $options['language'] = $config->get('language', 'en-GB');
        }

        /** 5. default */
        if (MolajoLanguage::exists($options['language'])) {
        } else {
            $options['language'] = 'en-GB';
        }

        /** Load Library Language Files */
        $language = MolajoFactory::getLanguage();
        $language->load('lib_molajo', MOLAJO_BASE_FOLDER);

        /** Set Language in Configuration */
        $config->set('language', $options['language']);

        /** Set User Editor in Configuration */
        $editor = MolajoFactory::getUser()->getParam('editor', $this->getConfiguration('editor'));

        if (MolajoPluginHelper::isEnabled('editors', $editor)) {

        } else {
            $editor = $this->getConfiguration('editor');
            if (MolajoPluginHelper::isEnabled('editors', $editor)) {
            } else {
                $editor = 'none';
            }
        }
        $config->set('editor', $editor);

        $defaults = MolajoApplicationHelper::getApplicationDefaults();
        if ($defaults === false) {
            return false;
        }

        foreach ($defaults as $default) {
            $config->set('application_logon_requirement', true);
            $config->set('application_guest_option', 'com_login');
            $config->set('application_default_option', 'com_dashboard');
            $config->set('default_application_indicator', $default->default_application_indicator);
            $config->set('default_template_extension_id', $default->default_template_extension_id);
        }

        /** Trigger onAfterInitialise Event */
        MolajoPluginHelper::importPlugin('system');
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
        //        if ($itemid = JRequest::getInt('Itemid')) {
        //            $this->authorise($itemid);
        //        }
        $uri = JURI::getInstance();
        //        $router = $this->getRouter();
        //var_dump($router);
        //die;
        //        $result = $router->parse($uri);

        //		JRequest::set($result, 'get', false);
        ///** todo: amy configuration for ssl by application */
        //		if ($this->getConfiguration('force_ssl') >= 1
        ///          && strtolower($uri->getScheme()) != 'https') {
        //		$uri->setScheme('https');
        //		$this->redirect((string)$uri);
        //	}

        /** trigger onAfterRoute Event */
        MolajoPluginHelper::importPlugin('system');
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
            $url = 'index.php?option=com_users&view=login&return=' . $return;
            $url = MolajoRoute::_($url, false);
            $this->redirect($url, MolajoText::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'));

            return false;
        }

        MolajoError::raiseError(403, MolajoText::_('ERROR_NOT_AUTHORIZED'));
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
            $name = $this->_name;
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
     * @param   string  $component    The component to dispatch.
     *
     * @return  void
     * @since  1.0
     */
    public function dispatch($component = null)
    {
        try
        {
            if ($component === null) {
                $class = 'Molajo' . ucfirst(MOLAJO_APPLICATION) . 'ApplicationHelper';
                $helper = new $class ();
                $component = $helper->getDefaults();
            }

            $request = $this->getRequest($component);
            $document = MolajoFactory::getDocument();
            $user = MolajoFactory::getUser();

            switch ($document->getType()) {
                case 'html':
                    $document->setMetaData('keywords', $this->getConfiguration('MetaKeys'));
                    break;

                default:
                    break;
            }
            $document->setTitle($this->getConfiguration('sitename'));
            $document->setDescription($this->getConfiguration('MetaDesc'));

            $contents = MolajoComponentHelper::renderComponent($request);

            $document->setBuffer($contents, 'component');

            MolajoPluginHelper::importPlugin('system');
            $this->triggerEvent('onAfterDispatch');
        }

            // Uncaught exceptions.
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
     * the JResponse buffer.
     *
     * @return  void;
     * @since  1.0
     */
    public function render()
    {
        $session = MolajoFactory::getSession();
        $component = $session->get('page.option');
        $template = $this->getTemplate(true);
        $file = $session->get('page.layout');

        $params = array(
            'template' => $template[0]->name,
            'file' => $file . '.php',
            'directory' => MOLAJO_EXTENSION_TEMPLATES,
            'params' => $template[0]->parameters
        );

        $document = MolajoFactory::getDocument();
        $document->parse($params);

        $this->triggerEvent('onBeforeRender');

        JResponse::setBody($document->render(false, $params));

        $this->triggerEvent('onAfterRender');
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
     * getConfiguration
     *
     * Gets a configuration value.
     *
     * @param   string   The name of the value to get.
     * @param   string   Default value to return
     *
     * @return  mixed    The user state.
     *
     * @since  1.0
     */
    public function getConfiguration($varname, $default = null)
    {
        return MolajoFactory::getConfig()->get('' . $varname, $default);
    }

    /**
     * Enqueue a system message.
     *
     * @param   string   $msg   The message to enqueue.
     * @param   string   $type  The message type. Default is message.
     *
     * @return  void
     *
     * @since  1.0
     */
    public function enqueueMessage($msg, $type = 'message')
    {
        if (count($this->_messageQueue)) {
        } else {

            $session = MolajoFactory::getSession();
            $sessionQueue = $session->get('application.queue');

            if (count($sessionQueue)) {
                $this->_messageQueue = $sessionQueue;
                $session->set('application.queue', null);
            }
        }

        $this->_messageQueue[] = array('message' => $msg, 'type' => strtolower($type));
    }

    /**
     * Get the system message queue.
     *
     * @return  array  The system message queue.
     *
     * @since  1.0
     */
    public function getMessageQueue()
    {
        /** initialize */
        $tmpmsg = array();
        $tmpobj = new JObject();
        $count = 0;

        /** are there messages? */
        foreach ($this->_messageQueue as $msg) {
            if ($msg['message'] == '') {
            } else {
                $count++;
            }
        }

        /** pull in application session messages */
        if ($count == 0) {
            if (count(MolajoFactory::getSession()->get('application.queue'))) {
                $this->_messageQueue = MolajoFactory::getSession()->get('application.queue');
                MolajoFactory::getSession()->set('application.queue', null);
            }
            foreach ($this->_messageQueue as $msg) {
                if ($msg['message'] == '') {
                } else {
                    $count++;
                }
            }
        }

        /** exit if no messages */
        if ($count == 0) {
            $_messageQueue = array();
            return $_messageQueue;
        }

        /** edit message queue */
        foreach ($this->_messageQueue as $msg) {

            if ($msg['message'] == '') {
            } else {
                $tmpobj->set('message', $msg['message']);

                if ($msg['type'] == 'message'
                    || $msg['type'] == 'notice'
                    || $msg['type'] == 'warning'
                    || $msg['type'] == 'error'
                ) {

                } else {
                    $msg['type'] == 'message';
                }
                $tmpobj->set('type', $msg['type']);
                $tmpmsg[] = $tmpobj;
                $count++;
            }
        }
        $_messageQueue = $tmpmsg;

        return $_messageQueue;
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
        return MolajoTemplateHelper::getTemplate();
    }

    /**
     * Overrides the default template that would be used
     *
     * @param string The template name
     */
    public function setTemplate($template)
    {
        if (is_dir(MOLAJO_EXTENSION_TEMPLATES . '/' . $template)) {
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
     * @since   11.1
     */
    public function getPathway($name = null, $options = array())
    {
        if (isset($name)) {
        } else {
            $name = $this->_name;
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
     * @see     MolajoApplication::enqueueMessage()
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
     * @since    11.1
     */
    public function close($code = 0)
    {
        exit($code);
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
        if (self::getConfiguration('unicodeslugs') == 1) {
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
        return md5(MolajoFactory::getConfig()->get('secret') . $seed);
    }

    /**
     * _createConfiguration
     *
     * Create the configuration registry.
     *
     * @param   string  $file  The path to the configuration file
     *
     * return   object  A config object
     *
     * @since  1.0
     */
    protected function _createConfiguration($file = null)
    {
        if ($file == null) {
        } else {
            require_once $file;
        }

        // Create the MolajoConfig object.
        $config = new MolajoConfig();

        // Get the global configuration object.
        $registry = MolajoFactory::getConfig();

        // Load the configuration values into the registry.
        $registry->loadObject($config);

        return $config;
    }

    /**
     * Set the current state of the language filter.
     *
     * @return    boolean    The old state
     * @since    1.6
     */
    public function setLanguageFilter($state = false)
    {
        $old = $this->_language_filter;
        $this->_language_filter = $state;
        return $old;
    }

    /**
     * Return the current state of the language filter.
     *
     * @return    boolean
     * @since    1.6
     */
    public function getLanguageFilter()
    {
        return $this->_language_filter;
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
        return JResponse::toString($this->getConfiguration('gzip', false));
    }

    /**
     * getRequest
     *
     * Gets the Request Object and populates Page Session Variables for Component
     *
     * @return bool
     */
    protected function getRequest($option)
    {

        //todo: amy remove all the application-specific values

        /** initialization */
        $task = '';
        $view = '';
        $model = '';
        $layout = '';
        $format = '';
        $component_table = '';

        /** 1. Option */
        $molajoConfig = new MolajoModelConfiguration ($option);

        if ($option == null) {
            $option = $molajoConfig->getOptionValue(MOLAJO_CONFIG_OPTION_ID_DEFAULT_OPTION);
            if ($option === false) {
                $this->enqueueMessage(MolajoText::_('MOLAJO_NO_DEFAULT_OPTION_DEFINED'), 'error');
                return false;
            }
        }

        /** 2. Component Path */
        $component_path = MOLAJO_EXTENSION_COMPONENTS . '/' . $option;
        define('JPATH_COMPONENT', $component_path);

        /** 3. Task */
        $task = JRequest::getCmd('task', 'display');
        if (strpos($task, '.')) {
            $task = substr($task, (strpos($task, '.') + 1), 99);
        }

        /** 4. Controller */
        $controller = $molajoConfig->getOptionLiteralValue(MOLAJO_CONFIG_OPTION_ID_TASK_TO_CONTROLLER, $task);
        if ($controller === false) {
            MolajoError::raiseError(500, MolajoText::_('MOLAJO_INVALID_TASK_DISPLAY_CONTROLLER') . ' ' . $task);
            return false;
        }

        if ($task == 'display') {

            /** 5. View **/
            $view = JRequest::getCmd('view', null);
            if ($view == null) {
                $results = false;
            } else {
                $results = $molajoConfig->getOptionLiteralValue(MOLAJO_CONFIG_OPTION_ID_VIEWS, $view);
            }

            if ($results === false) {
                $view = $molajoConfig->getOptionValue(MOLAJO_CONFIG_OPTION_ID_DEFAULT_VIEW);
                if ($view === false) {
                    $this->enqueueMessage(MolajoText::_('MOLAJO_NO_DEFAULT_VIEW_DEFINED'), 'error');
                    return false;
                }
            }

            /** 7. Model **/
            $model = $molajoConfig->getOptionValue(MOLAJO_CONFIG_OPTION_ID_MODEL);
            if ($model === false) {
                $model = $view;
            }

            /** 8. Layout **/
            $layout = JRequest::getCmd('layout', null);
            if ($layout == null) {
                $results = false;
            } else {
                if ($view == 'edit') {
                    $results = $molajoConfig->getOptionLiteralValue(MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_LAYOUTS, $layout);
                } else {
                    $results = $molajoConfig->getOptionLiteralValue(MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_LAYOUTS, $layout);
                }
            }

            if ($results === false) {
                if ($view == 'edit') {
                    $layout = $molajoConfig->getOptionValue(MOLAJO_CONFIG_OPTION_ID_DEFAULT_EDIT_VIEW_LAYOUTS);
                } else {
                    $layout = $molajoConfig->getOptionValue(MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS);
                }
                if ($layout === false) {
                    $this->enqueueMessage(MolajoText::_('MOLAJO_NO_DEFAULT_LAYOUT_FOR_VIEW_DEFINED'), 'error');
                    return false;
                }
            }

            /** 9. Format */
            $format = JRequest::getCmd('format', null);
            if ($format == null) {
                $results = false;
            } else {
                if ($view == 'edit') {
                    $results = $molajoConfig->getOptionLiteralValue(MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_FORMATS, $format);
                } else {
                    $results = $molajoConfig->getOptionLiteralValue(MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_FORMATS, $format);
                }
            }

            if ($results === false) {
                if ($view == 'edit') {
                    $format = $molajoConfig->getOptionValue(MOLAJO_CONFIG_OPTION_ID_DEFAULT_EDIT_VIEW_FORMATS);
                } else {
                    $format = $molajoConfig->getOptionValue(MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_FORMATS);
                }
                if ($format === false) {
                    $format = 'html';
                }
            }
        } else {
            /** amy: come back and get redirect stuff later */
            $view = '';
            $layout = '';
            $format = '';
        }

        /** 10. id, cid and catid */
        $id = JRequest::getInt('id');
        $cids = JRequest::getVar('cid', array(), '', 'array');
        JArrayHelper::toInteger($cids);

        if ($task == 'add') {
            $id = 0;
            $cids = array();

        } else if ($task == 'edit' || $task == 'restore') {

            if ($id > 0 && count($cids) == 0) {
            } else if ($id == 0 && count($cids) == 1) {
                $id = $cids[0];
                $cids = array();

            } else if ($id == 0 && count($cids) == 0) {
                MolajoError::raiseError(500, MolajoText::_('MOLAJO_ERROR_TASK_MUST_HAVE_REQUEST_ID_TO_EDIT'));
                return false;
            } else if (count($cids) > 1) {
                MolajoError::raiseError(500, MolajoText::_('MOLAJO_ERROR_TASK_MAY_NOT_HAVE_MULTIPLE_REQUEST_IDS'));
                return false;
            }
        }
        $catid = JRequest::getInt('catid');

        /** 11. acl implementation */
        $acl_implementation = $molajoConfig->getOptionValue(MOLAJO_CONFIG_OPTION_ID_ACL_IMPLEMENTATION);
        if ($acl_implementation === false) {
            $acl_implementation = 'core';
        }

        /** 12. component table */
        $component_table = $molajoConfig->getOptionValue(MOLAJO_CONFIG_OPTION_ID_TABLE);
        if ($component_table === false) {
            $component_table = '_common';
        }

        /** 13. plugin helper */
        $plugin_type = $molajoConfig->getOptionValue(MOLAJO_CONFIG_OPTION_ID_PLUGIN_TYPE);
        if ($plugin_type === false) {
            $plugin_type = 'content';
        }

        /** 14. parameters */
        if ($this->getName() == 'site') {
            $params = MolajoComponentHelper::getParams($option);

            // Setup the category parameters.
            //		$cparams = $category->getParams();
            //		$category->params = clone($params);
            //		$category->params->merge($cparams);
            // $this->getState('request.option')->get('page_class_suffix', '') = htmlspecialchars($this->params->get('pageclass_sfx'));
        } else {
            $params = MolajoComponentHelper::getParams($option);

            // $this->_mergeParams ();
            // $this->getState('request.option')->get('page_class_suffix', '') = htmlspecialchars($this->params->get('pageclass_sfx'));
        }

        /** other */
        $extension = JRequest::getCmd('extension', '');
        $component_specific = JRequest::getCmd('component_specific', '');

        /** Request Object */
        JRequest::setVar('option', $option);
        JRequest::setVar('view', $view);
        JRequest::setVar('layout', $layout);
        JRequest::setVar('task', $task);
        JRequest::setVar('format', $format);

        JRequest::setVar('id', (int)$id);
        JRequest::setVar('cid', (array)$cids);

        /** Page Session Variables */
        $session = MolajoFactory::getSession();

        $session->set('page.application_id', MOLAJO_APPLICATION_ID);
        $session->set('page.current_url', MOLAJO_BASE_URL);
        $session->set('page.base_url', JURI::base());
        $session->set('page.item_id', JRequest::getInt('Itemid', 0));

        $session->set('page.controller', $controller);
        $session->set('page.extension_type', 'component');
        $session->set('page.option', $option);
        $session->set('page.no_com_option', substr($option, 4, strlen($option) - 4));
        $session->set('page.view', $view);
        $session->set('page.model', $model);
        $session->set('page.layout', $layout);
        //        $session->set('page.wrap', $params->def('wrap', 'none'));
        //        $session->set('page.wrap_id', $params->def('wrap_id', ''));
        //        $session->set('page.wrap_class', $params->def('wrap_class', ''));
        $session->set('page.wrap', 'none');
        $session->set('page.wrap_id', '');
        $session->set('page.wrap_class', '');

        $session->set('page.layout_type', 'extension');
        $session->set('page.task', $task);
        $session->set('page.format', $format);
        $session->set('page.plugin_type', $plugin_type);

        $session->set('page.id', (int)$id);
        $session->set('page.cid', (array)$cids);
        $session->set('page.catid', (int)$catid);

        $session->set('page.acl_implementation', $acl_implementation);
        $session->set('page.component_table', $component_table);
        $session->set('page.component_path', $component_path);
        $session->set('page.filter_fieldname', 'config_manager_list_filters');
        $session->set('page.select_fieldname', 'config_manager_grid_column');

        /** other */
        $session->set('page.extension', $extension);
        $session->set('page.component_specific', $component_specific);

        /** retrieve from db */
        if ($controller == 'display') {
            $this->getContentInfo();
        }

        /** load into $data array for creation of the request object */
        $request = array();

        $request['application_id'] = $session->get('page.application_id');
        $request['current_url'] = $session->get('page.current_url');
        $request['component_path'] = $session->get('page.component_path');
        $request['base_url'] = $session->get('page.base_url');
        $request['item_id'] = $session->get('page.item_id');

        $request['controller'] = $session->get('page.controller');
        $request['extension_type'] = $session->get('page.extension_type');
        $request['option'] = $session->get('page.option');
        $request['no_com_option'] = $session->get('page.no_com_option');
        $request['view'] = $session->get('page.view');
        $request['layout'] = $session->get('page.layout');
        $request['wrap'] = $session->get('page.wrap');
        $request['wrap_id'] = $session->get('page.wrap_id');
        $request['wrap_class'] = $session->get('page.wrap_class');

        $request['model'] = $session->get('page.model');
        $request['task'] = $session->get('page.task');
        $request['format'] = $session->get('page.format');
        $request['plugin_type'] = $session->get('page.plugin_type');

        $request['id'] = $session->get('page.id');
        $request['cid'] = $session->get('page.cid');
        $request['catid'] = $session->get('page.catid');
        $request['params'] = $session->get('page.params');
        $request['extension'] = $session->get('page.extension');
        $request['component_specific'] = $session->get('page.component_specific');

        $request['acl_implementation'] = $session->get('page.acl_implementation');
        $request['component_table'] = $session->get('page.component_table');
        $request['filter_fieldname'] = $session->get('page.filter_fieldname');
        $request['select_fieldname'] = $session->get('page.select_fieldname');

        $request['title'] = $session->get('page.title');
        $request['subtitle'] = $session->get('page.subtitle');
        $request['metakey'] = $session->get('page.metakey');
        $request['metadesc'] = $session->get('page.metadesc');
        $request['metadata'] = $session->get('page.metadata');
        $request['position'] = $session->get('page.position');

        $request['wrap_title'] = $request['title'];
        $request['wrap_subtitle'] = $request['subtitle'];
        $request['wrap_date'] = '';
        $request['wrap_author'] = '';
        $request['wrap_more_array'] = array();

        return $request;
    }

    /**
     * getContentInfo
     *
     * @return    array
     * @since    1.0
     */
    public function getContentInfo()
    {
        $session = MolajoFactory::getSession();

        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);
        $doquery = false;

        if ((int)$session->get('page.item_id') > 0) {
            $query->select('"" as ' . $db->namequote('metakey'));
            $query->select('"" as ' . $db->namequote('metadesc'));
            $query->select('"" as ' . $db->namequote('metadata'));
            $query->select($db->namequote('params'));
            $query->from($db->namequote($session->get('page.component_table')));
            $query->where($db->namequote('id') . ' = ' . (int)$session->get('page.id'));
            $doquery = true;

        } else if ((int)$session->get('page.id') > 0) {
            $query->select($db->namequote('metakey'));
            $query->select($db->namequote('metadesc'));
            $query->select($db->namequote('metadata'));
            $query->select($db->namequote('params'));
            $query->from($db->namequote($session->get('page.component_table')));
            $query->where($db->namequote('id') . ' = ' . (int)$session->get('page.id'));
            $doquery = true;

        } else if ((int)$session->get('page.cid') > 0) {
            $query->select($db->namequote('metakey'));
            $query->select($db->namequote('metadesc'));
            $query->select($db->namequote('metadata'));
            $query->select($db->namequote('params'));
            $query->from($db->namequote('#__categories'));
            $query->where($db->namequote('id') . ' > ' . (int)$session->get('page.catid'));
            $doquery = true;
        }

        if ($doquery === true) {
            $query->select($db->namequote('id'));
            $query->select($db->namequote('title'));
            $query->select('"" as ' . $db->namequote('subtitle'));

            $db->setQuery($query->__toString());

            $results = $db->loadObjectList();

        } else {
            $session->set('page.title', '');
            $session->set('page.subtitle', '');
            $session->set('page.metakey', '');
            $session->set('page.metadesc', '');
            $session->set('page.metadata', '');
            $session->set('page.params', '');
            $results = array();
        }

        if (count($results) > 0) {
            foreach ($results as $count => $item) {
                $session->set('page.title', $item->title);
                $session->set('page.subtitle', $item->subtitle);
                $session->set('page.metakey', $item->metakey);
                $session->set('page.metadesc', $item->metadesc);
                $session->set('page.metadata', $item->metadata);
                $session->set('page.params', $item->params);
            }
        } else {
            $session->set('page.title', $this->getConfiguration('sitename'));
            $session->set('page.subtitle', '');
            $session->set('page.metakey', '');
            $session->set('page.metadesc', '');
            $session->set('page.metadata', '');
            $session->set('page.params', '');
        }

        /** Set Document Information */
        $document = MolajoFactory::getDocument();
        $menus = $this->getMenu();
        if ($menus == null) {
            $menu = false;
            $id = 0;
        } else {
            $menu = $menus->getActive();
            $id = (int)@$menu->query['id'];
        }

        $pathway = $this->getPathway();
        $title = null;
        $this->params = MolajoComponentHelper::getParams($session->get('page.option'));

        //        $title = $this->params->get('page_title', '');

        if (empty($title)) {
            $title = $session->get('page.title');
        }
        if (empty($title)) {
            $title = $this->getConfiguration('sitename');
        }

        if ($this->getConfiguration('sitename_pagetitles', 0) == 1) {
            $title = MolajoText::sprintf('JPAGETITLE', $this->getConfiguration('sitename'), $title);

        } elseif ($this->getConfiguration('sitename_pagetitles', 0) == 2) {
            $title = MolajoText::sprintf('JPAGETITLE', $title, $this->getConfiguration('sitename'));
        }

        $document->setTitle($title);
        $document->setDescription($session->get('page.metadesc'));
        $document->setMetadata('keywords', $session->get('page.metakey'));
        $document->setMetadata('robots', $session->get('page.robots'));

        $metadata = explode(',', $session->get('page.metadata'));
        foreach ($metadata as $k => $v) {
            if ($v) {
                $document->setMetadata($k, $v);
            }
        }

        //        if ($this->params->get('show_feed_link', 1)) {
        //            $link = '&format=feed&limitstart=';
        //            $attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
        //            $document->addHeadLink(MolajoRoute::_($link.'&type=rss'), 'alternate', 'rel', $attribs);
        //            $attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
        //            $document->addHeadLink(MolajoRoute::_($link.'&type=atom'), 'alternate', 'rel', $attribs);
        //        }

        //        $session->set('page.params', $this->params);
        $session->set('page.params', array());
        $session->set('page.wrap', '');
        $session->set('page.position', 'component');

        // Load the parameters. Merge Global and Menu Item params into new object
        //		$params = $app->getParams();
        //		$menuParams = new JRegistry;

        //		if ($menu = $app->getMenu()->getActive()) {
        //			$menuParams->loadString($menu->params);
        //		}

        //		$mergedParams = clone $menuParams;
        //		$mergedParams->merge($params);

        return;
    }
}