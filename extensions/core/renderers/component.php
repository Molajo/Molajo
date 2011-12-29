<?php
/**
 * @package     Molajo
 * @subpackage  Component
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Component
 *
 * @package     Molajo
 * @subpackage  Application
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
     *  Layout include file - extracted from config
     *
     * @var string
     * @since 1.0
     */
    protected $layout = null;

    /**
     *  Wrap for Layout - extracted from config
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
        $this->layout = $config->layout;
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

        /** Events */
        MolajoPluginHelper::importPlugin('system');
        MolajoFactory::getApplication()->triggerEvent('onBeforeComponentRender');

        /** path */
        $path = MOLAJO_EXTENSIONS_COMPONENTS . '/' . $this->option . '/' . $this->option . '.php';

        /** installation */
        if (MOLAJO_APPLICATION_ID == 0
            && file_exists($path)
        ) {

            /** language */
        } elseif (file_exists($path)) {
            //            MolajoFactory::getLanguage()->load($this->option, $path, MolajoFactory::getLanguage()->getDefault(), false, false);

        } else {
            MolajoError::raiseError(404, MolajoTextHelper::_('MOLAJO_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
        }
/**
        echo '<pre>';
        var_dump($request);
        '</pre>';
*/
        /** execute the component */
        ob_start();
        require_once $path;
        $output = ob_get_contents();
        ob_end_clean();

        /** Events */
        MolajoPluginHelper::importPlugin('system');
        MolajoFactory::getApplication()->triggerEvent('onAfterComponentRender');

        /** Return output */
        return $output;
    }
}
