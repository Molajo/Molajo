<?php
/**
 * @package     Molajo
 * @subpackage  Base
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
     * $_attributes
     *
     * Extracted in Parser Class from Theme/Rendered output
     * <include:extension statement attr1=x attr2=y attrN="and-so-on" />
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
     * $task
     *
     * The Task Request for this specific extension include statement
     *
     * @var    object
     * @since  1.0
     */
    public $task;

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

        $this->parameters = new Registry;
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
            if ($this->task->get('extension_instance_id', 0) == 0) {
                return $this->task->set('status_found', false);
            }
        }

        /** extension MVC classes are loaded */
        $this->_importClasses();

        /** load Metadata (theme renderer, only) */
        $this->_loadMetadata();

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
            if ($this->task->get('extension_instance_id', 0) == 0) {
                return $this->task->set('status_found', false);
            }
        }

        /** retrieves MVC defaults for application */
        $this->_getApplicationDefaults();

        /** lazy load paths for view files */
        $this->_setPaths();

        return $this->task->set('status_found', true);
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
        $this->task = new Registry();

        /** extension */
        $this->task->set('extension_instance_id', 0);
        $this->task->set('extension_instance_name', '');
        $this->task->set('extension_asset_type_id', 0);
        $this->task->set('extension_asset_id', 0);
        $this->task->set('extension_view_group_id', 0);
        $this->task->set('extension_custom_fields', array());
        $this->task->set('extension_metadata', array());
        $this->task->set('extension_parameters', array());
        $this->task->set('extension_path', '');
        $this->task->set('extension_type', $this->_name);
        $this->task->set('extension_folder', '');
        $this->task->set('extension_event_type', '');

        /** view */
        $this->task->set('template_view_id', 0);
        $this->task->set('template_view_name', '');
        $this->task->set('template_view_css_id', '');
        $this->task->set('template_view_css_class', '');
        $this->task->set('template_view_asset_type_id',
            MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE_VIEW);
        $this->task->set('template_view_asset_id', 0);
        $this->task->set('template_view_path', '');
        $this->task->set('template_view_path_url', '');

        /** wrap */
        $this->task->set('wrap_view_id', 0);
        $this->task->set('wrap_view_name', '');
        $this->task->set('wrap_view_css_id', '');
        $this->task->set('wrap_view_css_class', '');
        $this->task->set('wrap_view_asset_type_id',
            MOLAJO_ASSET_TYPE_EXTENSION_WRAP_VIEW);
        $this->task->set('wrap_view_asset_id', 0);
        $this->task->set('wrap_view_path', '');
        $this->task->set('wrap_view_path_url', '');

        /** mvc parameters */
        $this->task->set('controller', '');
        $this->task->set('task', '');
        $this->task->set('model', '');
        $this->task->set('table', '');
        $this->task->set('id', 0);
        $this->task->set('category_id', 0);
        $this->task->set('suppress_no_results', false);

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
                $this->task->set('extension_instance_name', $value);


            } else if ($name == 'tag') {
                $this->_tag = $value;


            } else if ($name == 'template') {
                $this->task->set('template_view_name', $value);

            } else if ($name == 'template_view_css_id'
                || $name == 'template_view_id'
            ) {
                $this->task->set('template_view_css_id', $value);

            } else if ($name == 'template_view_css_class'
                || $name == 'view_class'
            ) {
                $this->task->set('template_view_css_class', $value);


            } else if ($name == 'wrap') {
                $this->task->set('wrap_view_name', $value);

            } else if ($name == 'wrap_view_css_id'
                || $name == 'wrap_view_id'
            ) {
                $this->task->set('wrap_view_css_id', $value);

            } else if ($name == 'wrap_view_css_class'
                || $name == 'wrap_view_class'
            ) {
                $this->task->set('wrap_view_css_class', $value);
            }
            //todo: amy merge other parameters into $this->parameters $this->task->set('other_parameters') = $other_parameters;
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
        $results = ExtensionHelper::getExtensionRequestObject($this->task);

        if ($results === false) {
            return false;
        }

        $this->task = $results;
        return $this->task->set('status_found', true);
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
        if ((int)$this->task->get('template_view_id', 0) == 0) {
            $this->task->set('template_view_id',
                ViewHelper::getViewDefaults('view',
                    $this->task->get('model'),
                    $this->task->get('task', ''),
                    (int)$this->task->get('id', 0))
            );
        }

        /** wrap */
        if ((int)$this->task->get('wrap_view_id', 0) == 0) {
            $this->task->set('wrap_view_id',
                ViewHelper::getViewDefaults('wrap',
                    $this->task->get('model'),
                    $this->task->get('task', ''),
                    (int)$this->task->get('id', 0))
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
        if ((int)$this->task->get('template_view_id', 0) == 0) {
            $this->task->set('template_view_id',
                ExtensionHelper::getInstanceID(
                    MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE_VIEW,
                    $this->task->get('template_view_name'),
                    'templates'
                )
            );
        } else {
            $this->task->set('template_view_name',
                ExtensionHelper::getInstanceTitle(
                    $this->task->get('template_view_id')
                )
            );
        }

        /** retrieve paths */
        $tc = new MolajoViewHelper(
            $this->task->get('template_view_name'),
            'templates',
            $this->task->get('extension_instance_name'),
            $this->task->get('extension_type'),
            ' ',
            $this->task->get('theme_name')
        );
        $this->task->set('template_view_path', $tc->view_path);
        $this->task->set('template_view_path_url', $tc->view_path_url);

        /** wrap view */

        /** retrieve id or name */
        if ((int)$this->task->get('wrap_view_id', 0) == 0) {
            $this->task->set('wrap_view_id',
                ExtensionHelper::getInstanceID(
                    MOLAJO_ASSET_TYPE_EXTENSION_WRAP_VIEW,
                    $this->task->get('wrap_view_name'),
                    'wraps'
                )
            );
        } else {
            $this->task->set('wrap_view_name',
                ExtensionHelper::getInstanceTitle(
                    $this->task->get('wrap_view_id')
                )
            );
        }

        /** retrieve paths */
        $wc = new MolajoViewHelper(
            $this->task->get('wrap_view_name'),
            'wraps',
            $this->task->get('extension_instance_name'),
            $this->task->get('extension_type'),
            ' ',
            $this->task->get('theme_name')
        );
        $this->task->set('wrap_view_path', $wc->view_path);
        $this->task->set('wrap_view_path_url', $wc->view_path_url);

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
     * _loadMetadata
     *
     * Theme Renderer use, only
     *
     * @return  null
     * @since   1.0
     */
    protected function _loadMetadata()
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
            $this->task->get('extension_path')
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
     * _test
     *
     * Use to verify MVC elements
     *
     * @param $test
     * @return bool
     */
    protected function _test($test, $type)
    {
        if (class_exists($test)){
            return 0;
        } else {
            return 1;
        }
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
        $this->task->set('model', $model);

        /** controller */
        $cc = (string)$this->_setController();
        $this->task->set('controller', $cc);

        /** task */
        $task = (string)$this->task->get('task', 'display');
        $this->task->set('task', $task);

        /** verify all values required are available */
        if ($this->task->get('status_found') === false) {
            return $this->task->get('status_found');
        }

        /** instantiate controller  */
        $controller = new $cc($this->task, $this->parameters);

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
        if ($this->task->get('model', '') == '') {
        } else {
            $mc = (string)ucfirst($this->task->get('model'));
            if (class_exists($mc)) {
                return $mc;
            }
        }

        /** 2. Extension Name (+ non-component name, if appropriate) + Model */
        $mc = (string)ucfirst($this->task->get('extension_instance_name'));
        $mc = str_replace(array('-', '_'), '', $mc);

        if ($this->task->get('extension_type') == 'component') {
        } else {
            $mc .= ucfirst(trim($this->task->get('extension_type', 'Module')));
        }
        $mc .= 'Model';
        if (class_exists($mc)) {
            return $mc;
        }

        /** 3. Molajo + Task Name + Model */
        if ($this->task->get('model', '') == '') {
        } else {
            $mc = 'Molajo' .
                (string)ucfirst(strtolower($this->task->get('model')) .
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
        if ($this->task->get('controller', '') == '') {
        } else {
            $cc = (string)ucfirst($this->task->get('controller'));
            if (class_exists($cc)) {
                return $cc;
            }
        }

        /** 2. Extension Name (+ Module, if appropriate) + Controller */
        $cc = (string)ucfirst($this->task->get('extension_instance_name'));
        $cc = str_replace(array('-', '_'), '', $cc);
        if ($this->task->get('extension_type') == 'component') {
        } else {
            $cc .= ucfirst(trim($this->task->get('extension_type', 'Module')));
        }
        $cc .= 'Controller';
        if (class_exists($cc)) {
            return $cc;
        }

        /** 3. Molajo + Task Name + Controller */
        if ($this->task->get('controller', '') == '') {
            $cc = 'Molajo' .
                (string)ucfirst(strtolower($this->task->get('task', 'display')) .
                        'Controller'
                );
            if (class_exists($cc)) {
                return $cc;
            }
        }

        /** 4. Base Class (no query) */
        return 'MolajoController';
    }

    /**
     * _verifyMVC
     * @return bool
     */
    protected function _verifyMVC()
    {
        $test1 = (int)$this->_test($this->task->get('controller'), 'controller');
        $test2 = (int)$this->_test($this->task->get('model'), 'model');
        $test3 = (int)$this->_test($this->task->get('task'), 'task');

        if (($test1 + $test2 + $test3) > 0) {
            echo 'Error Count: '.($test1 + $test2 + $test3).'<br />';
            echo 'Controller '.$this->task->get('controller').'<br />';
            echo 'Model '.$this->task->get('model').'<br />';
            echo 'Task '.$this->task->get('task').'<br />';
            echo '<pre>';
            var_dump($this->task);
            echo '</pre>';
            return $this->task->set('status_found', false);
        } else {
            return true;
        }
    }
}
