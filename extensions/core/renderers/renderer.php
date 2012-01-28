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
     * $request
     *
     * The page request established in MolajoRequest
     *
     * @var    object
     * @since  1.0
     */
    public $request;

    /**
     * $_type
     *
     * @var    string
     * @since  1.0
     */
    protected $_type = null;

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
     * $mvc
     *
     * The MVC Request for this specific extension
     *
     * @var    object
     * @since  1.0
     */
    public $mvc;

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
     * Sets mvc request
     * Establishes extension classes, language files, and media
     * Instantiates mvc controller and executes task
     *
     * @param   $attributes <include:renderer attr1=x attr2=y attr3=z ... />
     *
     * @return  mixed
     * @since   1.0
     */
    public function render($attributes)
    {
        /** attributes from <include:renderer */
        $this->_attributes = $attributes;

        /** initializes and populates the MVC request */
        $this->_setRequest();
        if ($this->mvc->get('extension_instance_id', 0) == 0) {
            return $this->mvc->set('status_found', false);
        }

        /** extension MVC classes are loaded */
        $this->_importClasses();

        /** extension language files */
        $this->_loadLanguage();

        /** css and js media files for extension and related entities */
        $this->_loadMedia();

        /** instantiate MVC and render output */
        $renderedOutput = $this->_invokeMVC();
        return $renderedOutput;
    }

    /**
     * _setRequest
     *
     * Initialize the request object for MVC values
     *
     * @return  bool
     * @since   1.0
     */
    protected function _setRequest()
    {
        /** creates mvc object and initializes settings */
        $this->_initializeRequest();

        /** establish values needed for MVC */
        $this->_getAttributes();

        /** retrieves extension and populates related mvc object values */
        $this->_getExtension();
        if ($this->mvc->get('extension_instance_id', 0) == 0) {
            return $this->mvc->set('status_found', false);
        }

        /** retrieves MVC defaults for application */
        $this->_getApplicationDefaults();

        /** lazy load paths for view files */
        $this->_setPaths();

        return $this->mvc->set('status_found', true);
    }

    /**
     * _initializeRequest
     *
     * Initialize the request object for MVC values
     *
     * @return  null
     * @since   1.0
     */
    protected function _initializeRequest()
    {
        $this->mvc = new JObject();

        /** extension */
        $this->mvc->set('extension_instance_id', 0);
        $this->mvc->set('extension_instance_name', '');
        $this->mvc->set('extension_asset_type_id', 0);
        $this->mvc->set('extension_asset_id', 0);
        $this->mvc->set('extension_view_group_id', 0);
        $this->mvc->set('extension_custom_fields', array());
        $this->mvc->set('extension_metadata', array());
        $this->mvc->set('extension_parameters', array());
        $this->mvc->set('extension_path', '');
        $this->mvc->set('extension_type', $this->_name);
        $this->mvc->set('extension_folder', '');
        $this->mvc->set('extension_event_type', '');

        /** view */
        $this->mvc->set('view_id', 0);
        $this->mvc->set('view_name', '');
        $this->mvc->set('view_css_id', '');
        $this->mvc->set('view_css_class', '');
        $this->mvc->set('view_asset_type_id',
            MOLAJO_ASSET_TYPE_EXTENSION_VIEW);
        $this->mvc->set('view_asset_id', 0);
        $this->mvc->set('view_path', '');
        $this->mvc->set('view_path_url', '');

        /** wrap */
        $this->mvc->set('wrap_id', 0);
        $this->mvc->set('wrap_name', '');
        $this->mvc->set('wrap_css_id', '');
        $this->mvc->set('wrap_css_class', '');
        $this->mvc->set('wrap_asset_type_id',
            MOLAJO_ASSET_TYPE_EXTENSION_VIEW);
        $this->mvc->set('wrap_asset_id', 0);
        $this->mvc->set('wrap_path', '');
        $this->mvc->set('wrap_path_url', '');

        /** mvc parameters */
        $this->mvc->set('mvc_controller', '');
        $this->mvc->set('mvc_task', '');
        $this->mvc->set('mvc_model', '');
        $this->mvc->set('mvc_id', 0);
        $this->mvc->set('mvc_category_id', 0);
        $this->mvc->set('mvc_suppress_no_results', false);

        return;
    }


    /**
     * _getAttributes
     *
     *  Retrieve request information needed to execute extension
     *
     * @return  bool
     * @since   1.0
     */
    protected function _getAttributes()
    {
        foreach ($this->_attributes as $name => $value) {

            if ($name == 'name' || $name == 'title') {
                $this->mvc->set('extension_instance_name', $value);

            } else if ($name == 'position') {
                $this->_tag = $value;

            } else if ($name == 'view') {
                $this->mvc->set('view_name', $value);

            } else if ($name == 'view_css_id' || $name == 'view_id') {
                $this->mvc->set('view_css_id', $value);

            } else if ($name == 'view_css_class' || $name == 'view_class') {
                $this->mvc->set('view_css_class', $value);

            } else if ($name == 'wrap') {
                $this->mvc->set('wrap_name', $value);

            } else if ($name == 'wrap_css_id' || $name == 'wrap_id') {
                $this->mvc->set('wrap_css_id', $value);

            } else if ($name == 'wrap_css_class' || $name == 'wrap_class') {
                $this->mvc->set('wrap_css_class', $value);
            }
            // $this->mvc->set('other_parameters') = $other_parameters;
        }

        return true;
    }

    /**
     * _getExtension
     *
     * Retrieve extension information using either the ID or the name
     *
     * @return bool
     * @since 1.0
     */
    protected function _getExtension()
    {
        if ((int)$this->mvc->get('extension_instance_id', 0) == 0) {
            $this->mvc->set('extension_instance_id',
                MolajoExtensionHelper::getInstanceID(
                    $this->mvc->get('extension_asset_type_id'),
                    $this->mvc->get('extension_instance_name'),
                    $this->mvc->get('extension_subtype')
                ));
        }
        if ((int)$this->mvc->get('extension_instance_id', 0) == 0) {
            return false;
        }

        $results = MolajoExtensionHelper::getExtensionRequestObject($this->mvc);
        if ($results === false) {
            return false;
        }
        $this->mvc = $results;

        return true;
    }

    /**
     *  _getApplicationDefaults
     *
     *  Retrieve default values, if not provided by extension
     *
     * @return  bool
     * @since   1.0
     */
    protected function _getApplicationDefaults()
    {
        if ((int)$this->mvc->get('view_id', 0) == '') {
            $this->mvc->set('view_id',
                MolajoViewHelper::getViewDefaults('view',
                    $this->mvc->get('mvc_model'),
                    $this->mvc->get('mvc_task', ''),
                    (int)$this->mvc->get('mvc_id', 0))
            );
        }

        /** wrap */
        if ((int)$this->mvc->get('wrap_id', 0) == '') {
            $this->mvc->set('wrap_id',
                MolajoViewHelper::getViewDefaults('wrap',
                    $this->mvc->get('mvc_model'),
                    $this->mvc->get('mvc_task', ''),
                    (int)$this->mvc->get('mvc_id', 0))
            );
        }

        /** controller */
        if ($this->mvc->get('mvc_task', '') == 'add'
            || $this->mvc->get('mvc_task', '') == 'edit'
            || $this->mvc->get('mvc_task', '') == 'display'
        ) {
            $this->mvc->set('mvc_controller', 'display');

        } else if ((int)$this->mvc->get('mvc_task') == 'login') {
            $this->mvc->set('mvc_controller', 'login');

        } else {
            $this->mvc->set('mvc_controller', 'update');
        }

        return true;
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
        $this->mvc->set('view_type', 'extensions');

        if ((int)$this->mvc->get('view_id', 0) == 0) {
            $this->mvc->set('view_id',
                MolajoExtensionHelper::getInstanceID(
                    MOLAJO_ASSET_TYPE_EXTENSION_VIEW,
                    $this->mvc->get('view_name'),
                    'extensions'
                ));
        } else {
            $this->mvc->set('view_name',
                MolajoExtensionHelper::getInstanceTitle($this->mvc->get('view_id')));
        }

        $viewHelper = new MolajoViewHelper($this->mvc->get('view_name'),
            $this->mvc->get('view_type'),
            $this->mvc->get('extension_instance_name'),
            $this->mvc->get('extension_type'),
            ' ',
            $this->mvc->get('template_name'));
        $this->mvc->set('view_path', $viewHelper->view_path);
        $this->mvc->set('view_path_url', $viewHelper->view_path_url);

        /** Wrap Path */
        if ((int)$this->mvc->get('wrap_id', 0) == 0) {
            $this->mvc->set('wrap_id',
                MolajoExtensionHelper::getInstanceID(
                    MOLAJO_ASSET_TYPE_EXTENSION_VIEW,
                    $this->mvc->get('wrap_name'),
                    'wraps'
                ));
        } else {
            $this->mvc->set('wrap_name',
                MolajoExtensionHelper::getInstanceTitle($this->mvc->get('wrap_id')));
        }

        $wrapHelper = new MolajoViewHelper($this->mvc->get('wrap_name'),
            'wraps',
            $this->mvc->get('extension_instance_name'),
            $this->mvc->get('extension_type'),
            ' ',
            $this->mvc->get('template_name'));
        $this->mvc->set('wrap_path', $wrapHelper->view_path);
        $this->mvc->set('wrap_path_url', $wrapHelper->view_path_url);

        return true;
    }

    /**
     * _importClasses
     *
     * imports extension classes and files
     *
     * @return  null
     * @since   1.0
     */
    protected function _importClasses()
    {
    }

    /**
     * _loadLanguage
     *
     * Loads Language Files for extension
     *
     * @return  null
     * @since   1.0
     */
    protected function _loadLanguage()
    {
        MolajoController::getApplication()->getLanguage()->load
        ($this->mvc->get('extension_path'),
            MolajoController::getApplication()->getLanguage()->getDefault(),
            false,
            false);
    }

    /**
     * _loadMedia
     *
     * Loads Media CSS and JS files for extension and related content
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
        $controllerClass = ucfirst($this->mvc->get('extension_instance_name'));
        if ($this->_name == 'module') {
            $controllerClass .= 'Module';
        }
        $controllerClass .= 'Controller' . ucfirst($this->mvc->get('mvc_controller'));

        if (class_exists($controllerClass)) {
        } else {

            /** ex. ExampleController */
            $controllerClass = ucfirst($this->mvc->get('extension_instance_name')) .
                'Controller';
            if (class_exists($controllerClass)) {

            } else {

                /** ex. MolajoControllerDisplay */
                $controllerClass = 'MolajoController' . ucfirst($this->mvc->get('mvc_controller'));
            }
        }
        $controller = new $controllerClass ($this->mvc);

        /** task: display, edit, or add  */
        $task = (string)$this->mvc->get('mvc_task', 'display');
        return $controller->$task();
    }
}
