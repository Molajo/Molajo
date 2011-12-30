<?php
/**
 * @package     Molajo
 * @subpackage  Renderers
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Component Renderer
 *
 * @package     Molajo
 * @subpackage  Renderers
 * @since       1.0
 */
class MolajoComponentRenderer
{
    /**
     * Name - from MolajoExtension
     *
     * @var    string
     * @since  1.0
     */
    protected $name = null;

    /**
     * Config - from MolajoExtension
     *
     * @var    array
     * @since  1.0
     */
    protected $config = array();

    /**
     * Option - extracted from config
     *
     * @var    string
     * @since  1.0
     */
    protected $option = null;

    /**
     *  Template folder name - extracted from config
     *
     * @var string
     * @since 1.0
     */
    protected $template = null;

    /**
     *  Page include file - extracted from config
     *
     * @var string
     * @since 1.0
     */
    protected $page = null;

    /**
     *  View include file - extracted from config
     *
     * @var string
     * @since 1.0
     */
    protected $view = null;

    /**
     *  Wrap for View - extracted from config
     *
     * @var string
     * @since 1.0
     */
    protected $wrap = null;

    /**
     *  Template Parameters - extracted from config
     *
     * @var string
     * @since 1.0
     */
    protected $parameters = null;

    /**
     * Attributes - from the Molajo Format Class <include:component statement>
     *
     * @var    array
     * @since  1.0
     */
    protected $attributes = array();

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param null $name
     * @param array $config
     * @since 1.0
     */
    public function __construct($name = null, $config = array())
    {
        /**
        echo '<pre>';
        var_dump($config);
        '</pre>';
         **/
        /** set class properties */
        $this->name = $name;

        $this->config = $config;
        $this->option = $config->option;
        $this->template = $config->template;
        $this->page = $config->page;
        $this->view = $config->view;
        $this->wrap = $config->wrap;
    }

    /**
     * render
     *
     * Render the component.
     *
     * @return  object
     * @since  1.0
     */
    public function render($attributes)
    {
        /** renderer $attributes from template */
        $this->attributes = $attributes;

        /** set up request for MVC */
        $request = $this->request();

        /** Before Rendering */
        MolajoFactory::getApplication()->registerEvent ('onBeforeRender', 'system');
        MolajoFactory::getApplication()->triggerEvent ('onBeforeRender', $this);

        /** path */
        $path = MOLAJO_EXTENSIONS_COMPONENTS . '/' . $this->option . '/' . $this->option . '.php';

        /** installation */
        if (MOLAJO_APPLICATION_ID == 0
            && file_exists($path)
        ) {

        /** language */
        } elseif (file_exists($path)) {
            MolajoFactory::getLanguage()->load($this->option, $path, MolajoFactory::getLanguage()->getDefault(), false, false);

        } else {
            MolajoError::raiseError(404, MolajoTextHelper::_('MOLAJO_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
        }

        /** component => MVC */
        ob_start();
        require_once $path;
        $output = ob_get_contents();
        ob_end_clean();

        /** After Rendering */
        MolajoFactory::getApplication()->registerEvent ('onAfterRender', 'system');
        MolajoFactory::getApplication()->triggerEvent ('onAfterRender', array($this, $output));

        /** Return output */
        return $output;
    }
}
