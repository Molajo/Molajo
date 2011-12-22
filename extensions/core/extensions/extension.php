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
    protected $config = null;

    /**
     *  User
     *
     * @var string
     * @since 1.0
     */
    protected $user = null;

    /**
     * Template
     *
     * @var object
     * @since 1.0
     */
    protected $template = null;

    /**
     *  Page
     *
     * @var string
     * @since 1.0
     */
    protected $page = null;

    /**
     *  Buffered output
     *
     * @var string
     * @since 1.0
     */
    protected $buffered_output = array();

    /**
     *  Render Type
     *
     *  1: HTML Webpage
     *      a. parse template for doc statements
     *      b. process doc statements (render and buffer output) for:
     *          html-head
     *          message
     *          component
     *          module
     *          modules
     *
     *  2: feed
     *
     *  3: JSON Webpage
     *      a. process (render and buffer output) for:
     *          json-head
     *          component
     *
     *  4: raw
     *      a. process (render and buffer output) for:
     *          text-head
     *          component
     *
     *  5: xml
     *      a. process:
     *          xml-head
     *          component
     */

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param   array  $config  A configuration array
     *
     * @since  1.0
     */
    public function __construct($request = null, $asset_id = null, $config = array())
    {
        /** configuration */
        if ($config) {
            $this->config = $config;
        } else {
            $this->config = new JRegistry;
            $this->getConfig();
        }

        /** Retrieve Asset for Request */
        $this->asset = new MolajoAsset ($request = null, $asset_id = null);

        /** Redirected in Asset Class*/
        if ($this->asset->redirect_to_id == 0) {
        } else {
            return;
        }

        /** current user */
       $this->loadUser();

MolajoFactory::getApplication()->setBody('Here it is!');
        /** authorise */
        echo '<pre>';var_dump($this->user);'</pre>';
       $this->authorise();


        $this->executeComponent();

        /** todo: template */

        /** todo: page */

        /** Event */
     //   MolajoPlugin::importPlugin('system');
     //   MolajoFactory::getApplication()->triggerEvent('onAfterInitialise');
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
        /** trigger onAfterRoute Event */
        MolajoPlugin::importPlugin('system');
        MolajoFactory::getApplication()->triggerEvent('onAfterRoute');
    }

    /**
     *  loadUser
     */
    private function loadUser()
    {
        $this->user = MolajoFactory::getUser();
    }

    /**
     * Execute Extension
     *
     * @return  void
     *
     * @since   1.0
     */
    public function authorise()
    {
        if (in_array($this->asset->view_group_id, $this->user->view_groups)) {
        } else {
//            $this->redirect($url, MolajoTextHelper::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'));

            return false;
        }

//        MolajoError::raiseError(403, MolajoTextHelper::_('ERROR_NOT_AUTHORIZED'));
    }

    /**
     * Get Header information for Page
     *
     * @return  void
     *
     * @since   1.0
     */
    public function getHead()
    {

    }

    /**
     * Execute Extension
     *
     * @return  void
     *
     * @since   1.0
     */
    public function executeComponent()
    {
        MolajoFactory::getApplication()->triggerEvent('onBeforeExecute');

        $component = new MolajoComponent($this->asset);
        $component->getRequest();
        $component->renderComponent();

        MolajoFactory::getApplication()->triggerEvent('onAfterExecute');
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
     * getTemplate
     *
     * Get Template and parse doc statements
     *
     * @param $template
     * @return string
     * @since   1.0
     */
    function getTemplate()
    {
        return MolajoTemplate::getTemplate();
    }

    /**
     * Render Extensions
     *
     * @return  void
     *
     * @since   1.0
     */
    public function getTemplateFunctions()
    {
        MolajoFactory::getApplication()->triggerEvent('onBeforeRender');


        MolajoFactory::getApplication()->triggerEvent('onAfterRender');
    }

    private function executeHead ()
    {

    }

    private function executeMessage ()
    {

    }

    /**
     * Execute Position
     *
     * @return  void
     *
     * @since   1.0
     */
    public function executePosition()
    {
        MolajoFactory::getApplication()->triggerEvent('onBeforeExecute');

        MolajoFactory::getApplication()->triggerEvent('onAfterExecute');
    }

    /**
     *
     */
    public function executeModule ()
    {

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
        $configData = array();

        $file = MOLAJO_EXTENSIONS_CORE . '/core/configuration.php';
        if (file_exists($file)) {
            require_once $file;
        } else {
            throw new RuntimeException('Fatal error - Extension Configuration File does not exist');
        }

        $configData = new MolajoExtensionConfiguration();

        if (is_array($configData)) {
            $this->config->loadArray($configData);

        } elseif (is_object($configData)) {
            $this->config->loadObject($configData);
        }

        return;
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