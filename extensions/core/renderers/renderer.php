<?php
/**
 * @package     Molajo
 * @subpackage  Renderer
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Renderer
 *
 * @package     Molajo
 * @subpackage  Renderer
 * @since       1.0
 */
class MolajoRenderer
{
    /**
     * Name
     *
     * @var    string
     * @since  1.0
     */
    protected $_name = null;

    /**
     * Request
     *
     * @var    object
     * @since  1.0
     */
    protected $_request;

    /**
     * Attributes
     * Extracted in Document Class from Template/Page
     * <include:component statement attr1=x attr2=y attrN="and-so-on" />
     *
     * @var    array
     * @since  1.0
     */
    protected $_attributes = array();

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param  null $name
     * @param  array $request
     *
     * @return  null
     * @since   1.0
     */
    public function __construct($name = null, $request = array())
    {
        $this->_name = $name;
        $this->_request = $request;
    }

    /**
     * render
     *
     * Render the component.
     *
     * @param   $attributes <include:xyz attr1=x attr2=y attr3=z ... />
     *
     * @return  mixed
     * @since   1.0
     */
    public function render($attributes)
    {
        /** attributes come from <include:xyz statement */
        $this->_attributes = $attributes;

        /** establish values needed for MVC */
        $this->_setParameters();

        /** lazy load paths for extension files */
        $this->_setPaths();

        /** import files and classes for extension */
        $this->_import();

        /** load language files for extension */
        $this->_loadLanguage();

        /** load css and js for extension */
        $this->_loadMedia();

        /** instantiate controller */
        $controllerClass = ucfirst($this->_request->get('mvc_extension_instance_name')) . 'Controller';
        if (ucfirst($this->_request->get('mvc_controller')) == 'Display') {
        } else {
            $controllerClass .= $this->_request->get('mvc_controller');
        }
        $controller = new $controllerClass ($this->_request);

        /** execute task: display, edit, or add  */
        $task = (string)$this->_request->get('mvc_task');
        return $controller->$task();
    }

    /**
     * _setParameters
     *
     *  Retrieve request information needed to execute extension
     *
     * @return  null
     * @since   1.0
     */
    protected function _setParameters()
    {
        foreach ($this->_attributes as $name => $value) {

            if ($name == 'name' || $name == 'title') {
                $this->_request->set('extension_title', $value);

            } else if ($name == 'wrap') {
                $this->_request->set('wrap', $value);

            } else if ($name == 'view') {
                $this->_request->set('view', $value);

            } else if ($name == 'view_css_id' || $name == 'view_id') {
                $this->_request->set('view_css_id', $value);

            } else if ($name == 'view_css_class' || $name == 'view_class') {
                $this->_request->set('view_css_class', $value);

            } else if ($name == 'wrap') {
                $this->_request->set('wrap', $value);

            } else if ($name == 'wrap_css_id' || $name == 'wrap_id') {
                $this->_request->set('wrap_css_id', $value);

            } else if ($name == 'wrap_css_class' || $name == 'wrap_class') {
                $this->_request->set('wrap_css_class', $value);
            }
            // $this->_request->set('other_parameters') = $other_parameters;
        }

        return;

        //NOT USED
        $this->_request = MolajoExtensionHelper::getOptions($this->_request);
        if ($this->_request->get('results') === false) {
            echo 'failed getOptions';
        }
    }

    /**
     *  _setPaths
     *
     *  Lazy load extension files
     *
     * @return  null
     * @since   1.0
     */
    protected function _setPaths()
    {
        $this->_request->set('view_type', 'extensions');

        $this->_request->set('view_name',
            MolajoExtensionHelper::getInstanceTitle($this->_request->get('view_id')));

        $viewHelper = new MolajoViewHelper($this->_request->get('view_name'),
            $this->_request->get('view_type'),
            $this->_request->get('extension_title'),
            $this->_request->get('extension_type'),
            ' ',
            $this->_request->get('template_name'));
        $this->_request->set('view_path', $viewHelper->view_path);
        $this->_request->set('view_path_url', $viewHelper->view_path_url);

        /** Wrap Path */
        $this->_request->set('wrap_name',
            MolajoExtensionHelper::getInstanceTitle($this->_request->get('wrap_id')));

        $wrapHelper = new MolajoViewHelper($this->_request->get('wrap_name'),
            'wraps',
            $this->_request->get('extension_title'),
            $this->_request->get('extension_type'),
            ' ',
            $this->_request->get('template_name'));
        $this->_request->set('wrap_path', $wrapHelper->view_path);
        $this->_request->set('wrap_path_url', $wrapHelper->view_path_url);
    }

    /**
     * _import
     *
     * imports extension folders and files
     *
     * @return  null
     * @since   1.0
     */
    protected function _import()
    {
    }

    /**
     * _loadLanguage
     *
     * Loads Language Files
     *
     * @return  null
     * @since   1.0
     */
    protected function _loadLanguage()
    {
        MolajoController::getApplication()->getLanguage()->load
        ($this->_request->get('mvc_extension_path'),
            MolajoController::getApplication()->getLanguage()->getDefault(), false, false);
    }

    /**
     * _loadMedia
     *
     * Loads Media Files for Extension
     *
     * @return  null
     * @since   1.0
     */
    protected function _loadMedia()
    {
    }
}
