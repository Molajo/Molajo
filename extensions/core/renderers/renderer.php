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
    public $request;

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
     * Position
     *
     * <include:module position=save-this-value ... />
     *
     * @var    null
     * @since  1.0
     */
    protected $_position = null;

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
        $this->request = $request;
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
        echo '<pre>';
        var_dump($this->request);
        echo '</pre>';

        /** attributes come from <include:xyz statement */
        $this->_attributes = $attributes;

        /** establish values needed for MVC */
        $this->_setParameters();

        /** load media, language files and mvc task */
        $this->_executeMVC();
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
                $this->request->set('extension_title', $value);

            } else if ($name == 'wrap') {
                $this->request->set('wrap', $value);

            } else if ($name == 'position') {
                $this->_position = $value;

            } else if ($name == 'view') {
                $this->request->set('view', $value);

            } else if ($name == 'view_css_id' || $name == 'view_id') {
                $this->request->set('view_css_id', $value);

            } else if ($name == 'view_css_class' || $name == 'view_class') {
                $this->request->set('view_css_class', $value);

            } else if ($name == 'wrap') {
                $this->request->set('wrap', $value);

            } else if ($name == 'wrap_css_id' || $name == 'wrap_id') {
                $this->request->set('wrap_css_id', $value);

            } else if ($name == 'wrap_css_class' || $name == 'wrap_class') {
                $this->request->set('wrap_css_class', $value);
            }
            // $this->request->set('other_parameters') = $other_parameters;
        }

        return;

        //NOT USED
        $this->request = MolajoExtensionHelper::getOptions($this->request);
        if ($this->request->get('results') === false) {
            echo 'failed getOptions';
        }
    }

    /**
     * _executeMVC
     *
     * Instantiate Controller for the Display View
     *
     * @return mixed
     * @since  1.0
     */
    protected function _executeMVC()
    {
        /** lazy load paths for extension files */
        $this->_setPaths();

        /** import files and classes for extension */
        $this->_import();

        /** load language files for extension */
        $this->_loadLanguage();

        /** load css and js for extension */
        $this->_loadMedia();

        $controllerClass = ucfirst($this->request->get('mvc_extension_instance_name')) . 'Controller';
        if (ucfirst($this->request->get('mvc_controller')) == 'Display') {
        } else {
            $controllerClass .= $this->request->get('mvc_controller');
        }
        $controller = new $controllerClass ($this->request);

        /** execute task: display, edit, or add  */
        $task = (string)$this->request->get('mvc_task');
        return $controller->$task();
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
        $this->request->set('view_type', 'extensions');

        $this->request->set('view_name',
            MolajoExtensionHelper::getInstanceTitle($this->request->get('view_id')));

        $viewHelper = new MolajoViewHelper($this->request->get('view_name'),
            $this->request->get('view_type'),
            $this->request->get('extension_title'),
            $this->request->get('mvc_extension_instance_name'),
            ' ',
            $this->request->get('template_name'));
        $this->request->set('view_path', $viewHelper->view_path);
        $this->request->set('view_path_url', $viewHelper->view_path_url);

        /** Wrap Path */
        $this->request->set('wrap_name',
            MolajoExtensionHelper::getInstanceTitle($this->request->get('wrap_id')));

        $wrapHelper = new MolajoViewHelper($this->request->get('wrap_name'),
            'wraps',
            $this->request->get('extension_title'),
            $this->request->get('mvc_extension_instance_name'),
            ' ',
            $this->request->get('template_name'));
        $this->request->set('wrap_path', $wrapHelper->view_path);
        $this->request->set('wrap_path_url', $wrapHelper->view_path_url);
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
        ($this->request->get('mvc_extension_path'),
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
