<?php
/**
 * @package     Molajo
 * @subpackage  Extension
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo Extension Class
 *
 * Base class
 */
class MolajoExtension
{
    /**
     * Configuration
     *
     * @var    integer
     * @since  1.0
     */
    private $_config = null;

    /**
     * Template
     *
     * @var object
     * @since 1.0
     */
    private $_template = null;

    /**
     *  Page
     *
     * @var string
     * @since 1.0
     */
    private $_page = null;

    /**
     * Site
     *
     * @var object
     * @since  1.0
     */
    protected $_site;

    /**
     * Application
     *
     * @var object
     * @since  1.0
     */
    protected $_app;

    /**
     * Instance
     *
     * @var string
     * @since  1.0
     */
    protected static $instance;

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
    public static function getInstance($name = 'MolajoExtension')
    {
        if (empty(self::$instance)) {
            if (class_exists($name)) {
                self::$instance = new $name;
            } else {
                self::$instance = new MolajoExtension;
            }
        }

        return self::$instance;
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
    public function __construct($config = null)
    {
        if ($config) {
            $this->config = $config;
        } else {
            $this->config = new JRegistry;
        }

        $this->getConfig();
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
    public function initialise($site = null, $app = null, $options = array())
    {
        $this->_site = $site;
        $this->_app = $app;

        /** todo: user */

        /** todo: asset record */

        /** todo: primary category */

        /** todo: component */

        /** todo: authorized? */

        /** todo: template */

        /** todo: page */

        /** todo: menu item */

        /** Site authorisation */
        $site = new MolajoSite ();
        $authorise = $site->authorise(MOLAJO_EXTENSION_ID);
        if ($authorise === false) {
            return MolajoError::raiseError(500, MolajoTextHelper::sprintf('MOLAJO_SITE_NOT_AUTHORISED_FOR_EXTENSION', MOLAJO_EXTENSION_ID));
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
            $name = MOLAJO_EXTENSION;
        }

        $router = MolajoRouter::getInstance($name, $options);
        if (MolajoError::isError($router)) {
            return null;
        }

        return $router;
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
            $name = MOLAJO_EXTENSION;
        }

        $menu = MolajoMenu::getInstance($name, $options);

        if (MolajoError::isError($menu)) {
            return null;
        }
        return $menu;
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
        if (is_dir(MOLAJO_CMS_TEMPLATES . '/' . $template)) {
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
            $name = MOLAJO_EXTENSION;
        }

        $pathway = MolajoPathway::getInstance($name, $options);

        if (MolajoError::isError($pathway)) {
            return null;
        }

        return $pathway;
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
     *  CONFIGURATION
     */

    /**
     * getConfig
     *
     * Creates the Extension configuration object.
     *
     * return   object  A config object
     *
     * @since  1.0
     */
    public function getConfig()
    {
        $configClass = new MolajoExtensionConfiguration();
        $data = $configClass->getConfig();

        if (is_array($data)) {
            $this->config->loadArray($data);

        } elseif (is_object($data)) {
            $this->config->loadObject($data);
        }

        return $this->config;
    }

    /**
     * get
     *
     * Returns a property of the Extension object
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
     * Modifies a property of the Extension object, creating it if it does not already exist.
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
}