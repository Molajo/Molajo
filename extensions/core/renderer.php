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
class MolajoRendererController
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
     * $_attributes
     *
     * Extracted in Document Class from Theme/Page
     * <include:component statement attr1=x attr2=y attrN="and-so-on" />
     *
     * @var    array
     * @since  1.0
     */
    protected $_attributes = array();

    /**
     * $_extension_required
     *
     * @var    bool
     * @since  1.0
     */
    protected $_extension_required = true;

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
     * $parameters
     *
     * Parameters which have been retrieved using default sequence to be used for rendering output
     *
     * @var    object
     * @since  1.0
     */
    public $parameters;

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param  string $name
     * @param  string $type
     *
     * @return  null
     * @since   1.0
     */
    public function __construct($name = null, $type = null)
    {
        $this->_name = $name;
        $this->_type = $type;

        $this->parameters = new JRegistry;
        $this->parameters->set('extension_suppress_no_results', 0);
    }

    /**
     * render
     *
     * Sets MVC request
     * Establishes extension classes, language files, and media
     * Instantiates mvc controller and executes task
     *
     * @param   $attributes <include:renderer attr1=x attr2=y attr3=z ... />
     *
     * @return  mixed
     * @since   1.0
     */
    public function render($attributes = array())
    {
        /** attributes from <include:renderer */
        $this->_attributes = $attributes;

        /** initializes and populates the MVC request */
        $this->_setRequest();
        if ($this->_extension_required === true) {
            if ($this->mvc->get('extension_instance_id', 0) == 0) {
                return $this->mvc->set('status_found', false);
            }
        }

        /** extension MVC classes are loaded */
        $this->_importClasses();

        /** extension language files */
        $this->_loadLanguage();

        /** css and js media files for extension and related entities */
        $this->_loadMedia();

        /** instantiate MVC and render output */
        return $this->_invokeMVC();
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

        if ($this->_extension_required === true) {
            if ($this->mvc->get('extension_instance_id', 0) == 0) {
                return $this->mvc->set('status_found', false);
            }
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
        $this->mvc->set('template_view_id', 0);
        $this->mvc->set('template_view_name', '');
        $this->mvc->set('template_view_css_id', '');
        $this->mvc->set('template_view_css_class', '');
        $this->mvc->set('template_view_asset_type_id',
            MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE_VIEW);
        $this->mvc->set('template_view_asset_id', 0);
        $this->mvc->set('template_view_path', '');
        $this->mvc->set('template_view_path_url', '');

        /** wrap */
        $this->mvc->set('wrap_view_id', 0);
        $this->mvc->set('wrap_view_name', '');
        $this->mvc->set('wrap_view_css_id', '');
        $this->mvc->set('wrap_view_css_class', '');
        $this->mvc->set('wrap_view_asset_type_id',
            MOLAJO_ASSET_TYPE_EXTENSION_WRAP_VIEW);
        $this->mvc->set('wrap_view_asset_id', 0);
        $this->mvc->set('wrap_view_path', '');
        $this->mvc->set('wrap_view_path_url', '');

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

            if ($name == 'name'
                || $name == 'title'
            ) {
                $this->mvc->set('extension_instance_name', $value);

            } else if ($name == 'tag') {
                $this->_tag = $value;


            } else if ($name == 'template') {
                $this->mvc->set('template_view_name', $value);

            } else if ($name == 'template_view_css_id'
                || $name == 'template_view_id'
            ) {
                $this->mvc->set('template_view_css_id', $value);

            } else if ($name == 'template_view_css_class'
                || $name == 'view_class'
            ) {
                $this->mvc->set('template_view_css_class', $value);


            } else if ($name == 'wrap') {
                $this->mvc->set('wrap_view_name', $value);

            } else if ($name == 'wrap_view_css_id'
                || $name == 'wrap_view_id'
            ) {
                $this->mvc->set('wrap_view_css_id', $value);

            } else if ($name == 'wrap_view_css_class'
                || $name == 'wrap_view_class'
            ) {
                $this->mvc->set('wrap_view_css_class', $value);
            }
            //todo: amy merge other parameters into $this->parameters $this->mvc->set('other_parameters') = $other_parameters;
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
        $results = ExtensionHelper::getExtensionRequestObject($this->mvc);

        if ($results === false) {
            return false;
        }

        $this->mvc = $results;
        return $this->mvc->set('status_found', true);
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
        if ((int)$this->mvc->get('template_view_id', 0) == 0) {
            $this->mvc->set('template_view_id',
                ViewHelper::getViewDefaults('view',
                    $this->mvc->get('mvc_model'),
                    $this->mvc->get('mvc_task', ''),
                    (int)$this->mvc->get('mvc_id', 0))
            );
        }

        /** wrap */
        if ((int)$this->mvc->get('wrap_view_id', 0) == 0) {
            $this->mvc->set('wrap_view_id',
                ViewHelper::getViewDefaults('wrap',
                    $this->mvc->get('mvc_model'),
                    $this->mvc->get('mvc_task', ''),
                    (int)$this->mvc->get('mvc_id', 0))
            );
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
        /** template view */

        /** retrieve id or name */
        if ((int)$this->mvc->get('template_view_id', 0) == 0) {
            $this->mvc->set('template_view_id',
                ExtensionHelper::getInstanceID(
                    MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE_VIEW,
                    $this->mvc->get('template_view_name'),
                    'templates'
                )
            );
        } else {
            $this->mvc->set('template_view_name',
                ExtensionHelper::getInstanceTitle(
                    $this->mvc->get('template_view_id')
                )
            );
        }

        /** retrieve paths */
        $tc = new MolajoViewHelper(
            $this->mvc->get('template_view_name'),
            'templates',
            $this->mvc->get('extension_instance_name'),
            $this->mvc->get('extension_type'),
            ' ',
            $this->mvc->get('theme_name')
        );
        $this->mvc->set('template_view_path', $tc->view_path);
        $this->mvc->set('template_view_path_url', $tc->view_path_url);

        /** wrap view */

        /** retrieve id or name */
        if ((int)$this->mvc->get('wrap_view_id', 0) == 0) {
            $this->mvc->set('wrap_view_id',
                ExtensionHelper::getInstanceID(
                    MOLAJO_ASSET_TYPE_EXTENSION_WRAP_VIEW,
                    $this->mvc->get('wrap_view_name'),
                    'wraps'
                )
            );
        } else {
            $this->mvc->set('wrap_view_name',
                ExtensionHelper::getInstanceTitle(
                    $this->mvc->get('wrap_view_id')
                )
            );
        }

        /** retrieve paths */
        $wc = new MolajoViewHelper(
            $this->mvc->get('wrap_view_name'),
            'wraps',
            $this->mvc->get('extension_instance_name'),
            $this->mvc->get('extension_type'),
            ' ',
            $this->mvc->get('theme_name')
        );
        $this->mvc->set('wrap_view_path', $wc->view_path);
        $this->mvc->set('wrap_view_path_url', $wc->view_path_url);

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
        return ExtensionHelper::loadLanguage(
            $this->mvc->get('extension_path')
        );
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
        /** model */
        $model = (string)$this->_setModel();
        $this->mvc->set('mvc_model', $model);

        /** controller */
        $cc = (string)$this->_setController();
        $this->mvc->set('mvc_controller', $cc);

        /** task */
        $task = (string)$this->mvc->get('mvc_task', 'display');
        $this->mvc->set('mvc_task', $task);

        /** instantiate controller  */
        $controller = new $cc($this->mvc, $this->parameters);

        /** execute task: display, edit, or add  */
        return $controller->$task();
    }

    /**
     * _setModel
     *
     * Set the name of the Model
     *
     * @return  string
     * @since   1.0
     */
    protected function _setModel()
    {
        /** 1. Specifically Named Model */
        if ($this->mvc->get('mvc_model', '') == '') {
        } else {
            $mc = (string)ucfirst($this->mvc->get('mvc_model'));
            if (class_exists($mc)) {
                return $mc;
            }
        }

        /** 2. Extension Name (+ non-component name, if appropriate) + Model */
        $mc = (string)ucfirst($this->mvc->get('extension_instance_name'));
        $mc = str_replace(array('-', '_'), '', $mc);
        if ($this->mvc->get('extension_type') == 'component') {
        } else {
            $mc .= ucfirst(trim($this->mvc->get('extension_type', 'Module')));
        }
        $mc .= 'Model';
        if (class_exists($mc)) {
            return $mc;
        }

        /** 3. Molajo + Task Name + Model */
        if ($this->mvc->get('mvc_model', '') == '') {
        } else {
            $mc = 'Molajo' .
                (string)ucfirst(strtolower($this->mvc->get('mvc_model')) .
                        'Model'
                );
            if (class_exists($mc)) {
                return $mc;
            }
        }

        /** 4. Base Class (no query) */
        return 'MolajoModel';
    }

    /**
     * _setController
     *
     * Set the name of the Controller
     *
     * @return  string
     * @since   1.0
     */
    protected function _setController()
    {
        /** 1. Specifically Named Controller */
        if ($this->mvc->get('mvc_controller', '') == '') {
        } else {
            $cc = (string)ucfirst($this->mvc->get('mvc_controller'));
            if (class_exists($cc)) {
                return $cc;
            }
        }

        /** 2. Extension Name (+ Module, if appropriate) + Controller */
        $cc = (string)ucfirst($this->mvc->get('extension_instance_name'));
        $cc = str_replace(array('-', '_'), '', $cc);
        if ($this->mvc->get('extension_type') == 'component') {
        } else {
            $cc .= ucfirst(trim($this->mvc->get('extension_type', 'Module')));
        }
        $cc .= 'Controller';
        if (class_exists($cc)) {
            return $cc;
        }

        /** 3. Molajo + Task Name + Controller */
        if ($this->mvc->get('mvc_controller', '') == '') {
            $cc = 'Molajo' .
                (string)ucfirst(strtolower($this->mvc->get('mvc_task', 'display')) .
                        'Controller'
                );
            if (class_exists($cc)) {
                return $cc;
            }
        }

        /** 4. Base Class (no query) */
        return 'MolajoController';
    }
}
