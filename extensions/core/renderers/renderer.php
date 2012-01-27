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
     * $_name
     *
     * @var    string
     * @since  1.0
     */
    protected $_name = null;

    /**
     * $_type
     *
     * @var    string
     * @since  1.0
     */
    protected $_type = null;

    /**
     * $_initialize
     *
     * @var    string
     * @since  1.0
     */
    protected $_initialize = true;

    /**
     * $request
     *
     * @var    object
     * @since  1.0
     */
    public $request;

    /**
     * $_attributes
     *
     * Extracted in Document Class from Template/Page
     * <include:component statement attr1=x attr2=y attrN="and-so-on" />
     *
     * @var    array
     * @since  1.0
     */
    protected $_attributes = array();

    /**
     * $_position
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
    public function __construct($name = null, $request = array(), $type = null)
    {
        $this->_name = $name;
        $this->_type = $type;
        $this->request = $request;
    }

    /**
     * render
     *
     * Render the component.
     *
     * @param   $attributes <include:renderer attr1=x attr2=y attr3=z ... />
     *
     * @return  mixed
     * @since   1.0
     */
    public function render($attributes)
    {
        /** attributes come from <include:renderer statement */
        $this->_attributes = $attributes;

        /** reset MVC variables for extension */
        if ($this->_initialize === false) {

        } else {
            /** reset view and other MVC values for extension */
            $this->_initializeMVC();

            /** get extension values */
            $this->_getAsset();

            /** extension */
            if ($this->request->get('extension_instance_id', 0) == 0) {
                return $this->request->set('status_found', false);
            } else {
                $this->_getExtension();
            }

            /** establish values needed for MVC */
            $this->_setParameters();

            /** retrieves MVC defaults for application */
            $this->_getApplicationDefaults();

            /** lazy load paths for view files */
            $this->_setPaths();
        }

        /** import files and classes for extension */
        $this->_import();

        /** load language files for extension */
        $this->_loadLanguage();

        /** load css and js for extension */
        $this->_loadMedia();

        /** renders output */
        $renderedOutput = $this->_invokeMVC();
        return $renderedOutput;
    }

    /**
     * _initializeMVC
     *
     * Initialize the request object for MVC values
     *
     * @return mixed
     */
    protected function _initializeMVC()
    {
        /** extension */
        $this->request->set('extension_instance_id', 0);
        $this->request->set('extension_instance_name', '');
        $this->request->set('extension_asset_type_id', 0);
        $this->request->set('extension_asset_id', 0);
        $this->request->set('extension_view_group_id', 0);
        $this->request->set('extension_custom_fields', array());
        $this->request->set('extension_metadata', array());
        $this->request->set('extension_parameters', array());
        $this->request->set('extension_path', '');
        $this->request->set('extension_type', '');
        $this->request->set('extension_folder', '');
        $this->request->set('extension_event_type', '');

        /** view */
        $this->request->set('view_id', 0);
        $this->request->set('view_name', '');
        $this->request->set('view_css_id', '');
        $this->request->set('view_css_class', '');
        $this->request->set('view_asset_type_id', MOLAJO_ASSET_TYPE_EXTENSION_VIEW);
        $this->request->set('view_asset_id', 0);
        $this->request->set('view_path', '');
        $this->request->set('view_path_url', '');

        /** wrap */
        $this->request->set('wrap_id', 0);
        $this->request->set('wrap_name', '');
        $this->request->set('wrap_css_id', '');
        $this->request->set('wrap_css_class', '');
        $this->request->set('wrap_asset_type_id', MOLAJO_ASSET_TYPE_EXTENSION_VIEW);
        $this->request->set('wrap_asset_id', 0);
        $this->request->set('wrap_path', '');
        $this->request->set('wrap_path_url', '');

        /** mvc parameters */
        $this->request->set('mvc_controller', '');
        $this->request->set('mvc_task', '');
        $this->request->set('mvc_model', '');
        $this->request->set('mvc_id', 0);
        $this->request->set('mvc_category_id', 0);
        $this->request->set('mvc_suppress_no_results', false);

        return;
    }

    /**
     * _setParameters
     *
     *  Retrieve request information needed to execute extension
     *
     * @return  null
     * @since   1.0
     */
    protected function _setParameters($object = array())
    {
        foreach ($this->_attributes as $name => $value) {

            if ($name == 'name' || $name == 'title') {
                $this->request->set('extension_title', $value);

            } else if ($name == 'view') {
                $this->request->set('view_name', $value);

            } else if ($name == 'wrap') {
                $this->request->set('wrap_name', $value);

            } else if ($name == 'position') {
                $this->_position = $value;

            } else if ($name == 'view') {
                $this->request->set('view_name', $value);

            } else if ($name == 'view_css_id' || $name == 'view_id') {
                $this->request->set('view_css_id', $value);

            } else if ($name == 'view_css_class' || $name == 'view_class') {
                $this->request->set('view_css_class', $value);

            } else if ($name == 'wrap') {
                $this->request->set('wrap_name', $value);

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
     * _getAsset
     *
     * @return  mixed
     * @since   1.0
     */
    protected function _getAsset()
    {
        $results = MolajoAssetHelper::getAssetRequestObject($this->request);

        /** not found: exit */
        if ($results === false) {
            return $this->request->set('status_found', false);
        }
        $this->request = $results;
    }

    /**
     * _getExtension
     *
     * Retrieve Extension information using either the ID or Instance Name
     *
     * @return bool
     * @since 1.0
     */
    protected function _getExtension()
    {

    }

    /**
     *  _getApplicationDefaults
     *
     *  Retrieve default values, if not provided by extension
     *
     * @return bool
     * @since 1.0
     */
    protected function _getApplicationDefaults()
    {

        if ((int)$this->request->get('view_id', 0) == '') {
            $this->request->set('view_id',
                MolajoViewHelper::getViewDefaults('view',
                    $this->request->get('mvc_model'),
                    $this->request->get('mvc_task', ''),
                    (int)$this->request->get('mvc_id', 0))
            );
        }

        /** wrap */
        if ((int)$this->request->get('wrap_id', 0) == '') {
            $this->request->set('wrap_id',
                MolajoViewHelper::getViewDefaults('wrap',
                    $this->request->get('mvc_model'),
                    $this->request->get('mvc_task', ''),
                    (int)$this->request->get('mvc_id', 0))
            );
        }

        /** controller */
        if ($this->request->get('mvc_task', '') == 'add'
            || $this->request->get('mvc_task', '') == 'edit'
            || $this->request->get('mvc_task', '') == 'display'
        ) {
            $this->request->set('mvc_controller', 'display');

        } else if ((int)$this->request->get('mvc_task') == 'login') {
            $this->request->set('mvc_controller', 'login');

        } else {
            $this->request->set('mvc_controller', 'update');
        }

        return;
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

        if ((int)$this->request->get('view_id', 0) == 0) {
            $this->request->set('view_id',
                MolajoExtensionHelper::getInstanceID($this->request->get('view_name')));
        } else {

            $this->request->set('view_name',
                MolajoExtensionHelper::getInstanceTitle($this->request->get('view_id')));
        }

        $viewHelper = new MolajoViewHelper($this->request->get('view_name'),
            $this->request->get('view_type'),
            $this->request->get('extension_title'),
            $this->request->get('extension_instance_name'),
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
            $this->request->get('extension_instance_name'),
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
        ($this->request->get('extension_path'),
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

    /**
     * _invokeMVC
     *
     * Instantiate the Controller and fire off the task, returns rendered output
     *
     * @return mixed
     */
    protected function _invokeMVC()
    {
        /** ex. ExampleControllerDisplay or ExampleControllerModuleClass */
        $controllerClass = ucfirst($this->request->get('extension_instance_name'));
        if ($this->_name == 'module') {
            $controllerClass .= 'Module';
        }
        $controllerClass .= 'Controller' . ucfirst($this->request->get('mvc_controller'));

        if (class_exists($controllerClass)) {
        } else {

            /** ex. ExampleController */
            $controllerClass = ucfirst($this->request->get('extension_instance_name')) .
                'Controller';
            if (class_exists($controllerClass)) {

            } else {

                /** ex. MolajoControllerDisplay */
                $controllerClass = 'MolajoController' . ucfirst($this->request->get('mvc_controller'));
            }
        }
        $controller = new $controllerClass ($this->request);

        /** task: display, edit, or add  */
        $task = (string)$this->request->get('mvc_task', 'display');
        return $controller->$task();
    }
}
