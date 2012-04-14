<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application;

use Molajo\Extension\Helper\ExtensionHelper;

defined('MOLAJO') or die;

/**
 * Includer
 *
 * @package   Molajo
 * @subpackage  Application
 * @since       1.0
 */
class Includer extends Molajo
{
    /**
     * $name
     *
     * @var    string
     * @since  1.0
     */
    protected $name = null;

    /**
     * $type
     *
     * @var    string
     * @since  1.0
     */
    protected $type = null;

    /**
     * $tag
     *
     * @var    string
     * @since  1.0
     */
    protected $tag = null;

    /**
     * $attributes
     *
     * Extracted in Parser Class from Theme/Rendered output
     * <include:extension statement attr1=x attr2=y attrN="and-so-on" />
     *
     * @var    array
     * @since  1.0
     */
    protected $attributes = array();

    /**
     * $extension_required
     *
     * Some includes (ex. head, messages, defer), do not require
     * an extension for further processing. In those cases, this
     * indicator is set to false.
     *
     * @var    bool
     * @since  1.0
     */
    protected $extension_required = true;

    /**
     * $task_request
     *
     * Building this object is the primary purpose of the Includers
     * classes. It contains the instructions needed for the MVC to
     * fulfill this include request
     *
     * @var    object
     * @since  1.0
     */
    protected $task_request;

    /**
     * $parameters
     *
     * Parameters merged using default sequence
     *
     * @var    object
     * @since  1.0
     */
    protected $parameters;

    /**
     * $rendered_output
     *
     * Rendered output resulting from MVC processing
     *
     * @var    object
     * @since  1.0
     */
    protected $rendered_output;

    /**
     * $items
     *
     * Used only for event processing and will be passed into the
     * MVC to serve as the Model data source
     *
     * @var    object
     * @since  1.0
     */
    protected $items;

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param  string $name
     * @param  string $type
     * @param  array  $items (used for event processing includes, only)
     *
     * @return  null
     * @since   1.0
     */
    public function __construct($name = null, $type = null, $items = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->items = $items;

        $this->parameters = Services::Registry()->initialise();
        $this->parameters->set('suppress_no_results', 0);
    }

    /**
     * process
     *
     * Imports extension classes, if existing
     * Loads page metadata (only invoked for Theme Includer)
     * Loads language files for extension.
     * Loads media files for extension
     * Calls the controller->task which processes include request
     * Captures rendered output for possible post-processing
     * Returns rendered output to Molajo::Parse to use for replacing
     *  with <include:type />
     *
     * @param   $attributes <include:type attr1=x attr2=y attr3=z ... />
     *
     * @return  mixed
     * @since   1.0
     */
    public function process($attributes = array())
    {
        /** attributes from <include:type */
        $this->attributes = $attributes;

        /** initialises and populates the MVC request */
        $this->setRenderCriteria();
        if ($this->extension_required === true) {
            if ($this->get('extension_instance_id', 0) == 0) {
                return $this->set('status_found', false);
            }
        }

        /** extension MVC classes are loaded */
        $this->_importClasses();

        /** theme include, only - loads metadata for the page */
        $this->_loadMetadata();

        /** language must be there before the extension runs */
        $this->_loadLanguage();

        /** instantiate MVC and render output */
        $this->rendered_output = $this->invokeMVC();

        /** only load media if there was rendered output */
        if ($this->rendered_output == ''
            && $this->parameters->get('suppress_no_results') == 0
        ) {
        } else {
            $this->_loadMedia();
            $this->_loadViewMedia();
        }

        /** used by events to update $items, if necessary */
        $this->_postMVCProcessing();

        return $this->rendered_output;
    }

    /**
     * get
     *
     * Returns a property of the Task Request object
     * or the default value if the property is not set.
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @since   1.0
     */
    public function get($key, $default = null)
    {
        return $this->task_request->get($key, $default);
    }

    /**
     * set
     *
     * Modifies a property of the Task Request object,
     * creating it if it does not already exist.
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @since   1.0
     */
    public function set($key, $value = null)
    {
        return $this->task_request->set($key, $value);
    }

    /**
     * setRenderCriteria
     *
     * Initialize the request object for MVC values
     *
     * @return  bool
     * @since   1.0
     */
    protected function setRenderCriteria()
    {
        /** creates mvc object and initialises settings */
        $this->_initialiseRequest();

        /** establish values needed for MVC */
        $this->_getAttributes();

        /** retrieves extension and populates related mvc object values */
        if ($this->extension_required === false) {
        } else {
            $this->_getExtension();
            if ($this->get('extension_instance_id', 0) == 0) {
                return $this->set('status_found', false);
            }
        }

        /** retrieves MVC defaults for extension */
        if ($this->get('extension_parameters', '') == '') {
        } else {
            $this->_getExtensionDefaults();
        }

        /** retrieves MVC defaults for application object */
        $this->_getApplicationDefaults();

        /** gets paths for template and wrap views */
        $this->setPaths();

        return $this->set('status_found', true);
    }

    /**
     * _initialiseRequest
     *
     * Initialize the request object for MVC values
     *
     * @return  null
     * @since   1.0
     */
    protected function _initialiseRequest()
    {
        $this->task_request = Services::Registry()->initialise();

        /** extension */
        $this->set('extension_instance_id', 0);
        $this->set('extension_instance_name', '');
        $this->set('extension_asset_type_id', 0);
        $this->set('extension_asset_id', 0);
        $this->set('extension_view_group_id', 0);
        $this->set('extension_path', '');
        $this->set('extension_type', $this->name);
        if ($this->type == 'request') {
            $this->set('extension_primary', true);
        } else {
            $this->set('extension_primary', false);
        }
        $this->set('extension_event_type', '');

        /** view */
        $this->set('template_view_id', 0);
        $this->set('template_view_name', '');
        $this->set('template_view_css_id', '');
        $this->set('template_view_css_class', '');
        $this->set('template_view_asset_type_id',
            ASSET_TYPE_EXTENSION_TEMPLATE_VIEW);
        $this->set('template_view_asset_id', 0);
        $this->set('template_view_path', '');
        $this->set('template_view_path_url', '');

        /** wrap */
        $this->set('wrap_view_id', 0);
        $this->set('wrap_view_name', '');
        $this->set('wrap_view_css_id', '');
        $this->set('wrap_view_css_class', '');
        $this->set('wrap_view_asset_type_id',
            ASSET_TYPE_EXTENSION_WRAP_VIEW);
        $this->set('wrap_view_asset_id', 0);
        $this->set('wrap_view_path', '');
        $this->set('wrap_view_path_url', '');

        /** mvc parameters */
        $this->set('source_asset_type_id', 0);
        $this->set('controller', '');
        $this->set('task', '');
        $this->set('model', '');
        $this->set('table', '');
        $this->set('id', 0);
        $this->set('category_id', 0);
        $this->set('suppress_no_results', false);

        /** for event processing includes, only */
        $this->set('event_items', 0);

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
        foreach ($this->attributes as $name => $value) {

            if ($name == 'name' || $name == 'title') {
                $this->set('extension_instance_name', $value);

            } else if ($name == 'tag') {
                $this->tag = $value;


            } else if ($name == 'template') {
                $this->set('template_view_name', $value);

            } else if ($name == 'template_view_css_id'
                || $name == 'template_view_id'
            ) {
                $this->set('template_view_css_id', $value);

            } else if ($name == 'template_view_css_class'
                || $name == 'view_class'
            ) {
                $this->set('template_view_css_class', $value);


            } else if ($name == 'wrap') {
                $this->set('wrap_view_name', $value);

            } else if ($name == 'wrap_view_css_id'
                || $name == 'wrap_view_id'
            ) {
                $this->set('wrap_view_css_id', $value);

            } else if ($name == 'wrap_view_css_class'
                || $name == 'wrap_view_class'
            ) {
                $this->set('wrap_view_css_class', $value);
            }
        }

        /** Retrieve Template View Primary Key */
        if ($this->get('template_view_id', 0) == 0) {
            if ($this->get('template_view_name', '') == '') {
            } else {
                $this->set('template_view_id',
                    ExtensionHelper::getInstanceID(
                        ASSET_TYPE_EXTENSION_TEMPLATE_VIEW,
                        $this->get('template_view_name')
                    )
                );
            }
        }

        /** Retrieve Wrap View Primary Key */
        if ($this->get('wrap_view_id', 0) == 0) {
            if ($this->get('wrap_view_name', '') == '') {
            } else {
                $this->set('wrap_view_id',
                    ExtensionHelper::getInstanceID(
                        ASSET_TYPE_EXTENSION_WRAP_VIEW,
                        $this->get('wrap_view_name')
                    )
                );
            }
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
        if ($this->get('extension_instance_id', 0) == 0) {
            $rows = ExtensionHelper::get(
                (int)$this->get('extension_asset_type_id'),
                $this->get('extension_instance_name')
            );

        } else {
            $rows = ExtensionHelper::get(
                (int)$this->get('extension_asset_type_id'),
                (int)$this->get('extension_instance_id')
            );
        }

        /** Extension not found */
        if (($this->get('extension_instance_id', 0) == 0)
            && (count($rows) == 0)
        ) {
            return $this->set('status_found', false);
        }

        /** Process Results */
        $row = array();
        foreach ($rows as $row) {
        }

        $this->set('extension_instance_id', $row->extension_instance_id);
        $this->set('extension_instance_name', $row->title);
        $this->set('extension_asset_id', $row->asset_id);
        $this->set('extension_asset_type_id', $row->asset_type_id);
        $this->set('extension_view_group_id', $row->view_group_id);
        $this->set('extension_type', $row->asset_type_title);

        $this->parameters = Services::Registry()->initialise();
        $this->parameters->loadString($row->parameters);

        $this->set('source_asset_type_id',
            $this->parameters->get('source_asset_type_id'));

        if ((int)$this->get('template_view_id', 0) == 0) {
            $this->set('template_view_id',
                $this->parameters->get('template_view_id')
            );
        }

        /** wrap */
        if ((int)$this->get('wrap_view_id', 0) == 0) {
            $this->set('wrap_view_id',
                $this->parameters->get('wrap_view_id')
            );
        }

        /** mvc */
        if ($this->get('controller', '') == '') {
            $this->set('controller',
                $this->parameters->get('controller', ''));
        }
        if ($this->get('task', '') == '') {
            $this->set('task',
                $this->parameters->get('task', 'display'));
        }
        if ($this->get('model', '') == '') {
            $this->set('model',
                $this->parameters->get('model', ''));
        }
        if ((int)$this->get('id', 0) == 0) {
            $this->set('id',
                $this->parameters->get('id', 0));
        }
        if ((int)$this->get('category_id', 0) == 0) {
            $this->set('category_id',
                $this->parameters->get('category_id', 0));
        }
        if ((int)$this->get('suppress_no_results', 0) == 0) {
            $this->set('suppress_no_results',
                $this->parameters->get('suppress_no_results', 0));
        }

        $this->set('extension_event_type',
            $this->parameters->get('plugin_type', array('content'))
        );

        $this->set('extension_path',
            ExtensionHelper::getPath(
                $this->get('extension_asset_type_id'),
                $this->get('extension_instance_name')
            )
        );

        return $this->set('status_found', true);
    }

    /**
     *  _getExtensionDefaults
     *
     *  Retrieve default values, if needed, for the extension
     *
     * @return  bool
     * @since   1.0
     */
    protected function _getExtensionDefaults()
    {
        if ((int)$this->get('template_view_id', 0) == 0) {

            $this->set('template_view_id',
                ViewHelper::getViewDefaultsOther('view',
                    $this->get('model'),
                    $this->get('task', ''),
                    (int)$this->get('id', 0),
                    $this->get('extension_parameters', '')
                )
            );
        }

        /** wrap */
        if ((int)$this->get('wrap_view_id', 0) == 0) {

            $this->set('wrap_view_id',
                ViewHelper::getViewDefaultsOther(
                    'wrap',
                    $this->get('model'),
                    $this->get('task', ''),
                    (int)$this->get('id', 0),
                    $this->get('extension_parameters', '')
                )
            );
        }

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
        if ((int)$this->get('template_view_id', 0) == 0) {

            $this->set('template_view_id',
                ViewHelper::getViewDefaultsApplication('view',
                    $this->get('model'),
                    $this->get('task', ''),
                    (int)$this->get('id', 0))
            );

        }

        /** wrap */
        if ((int)$this->get('wrap_view_id', 0) == 0) {

            $this->set('wrap_view_id',
                ViewHelper::getViewDefaultsApplication('wrap',
                    $this->get('model'),
                    $this->get('task', ''),
                    (int)$this->get('id', 0))
            );

        }
        return true;
    }

    /**
     *  setPaths
     *
     *  Using default ordering (Theme, Extension, View, Core MVC)
     *  this method identifies the file and URL paths for
     *  both the Template and Wrap Views
     *
     * @return  null
     * @since   1.0
     */
    protected function setPaths()
    {
        /** template view */

        /** retrieve id or name */
        if ((int)$this->get('template_view_id', 0) == 0) {
            $this->set('template_view_id',
                ExtensionHelper::getInstanceID(
                    ASSET_TYPE_EXTENSION_TEMPLATE_VIEW,
                    $this->get('template_view_name'),
                    'Template'
                )
            );
        } else {
            $this->set('template_view_name',
                ExtensionHelper::getInstanceTitle(
                    $this->get('template_view_id')
                )
            );
        }

        /** retrieve paths */
        $tc = new ViewHelper(
            $this->get('template_view_name'),
            'Template',
            $this->get('extension_instance_name'),
            $this->get('extension_type'),
            $this->get('theme_name')
        );
        $this->set('template_view_path', $tc->view_path);
        $this->set('template_view_path_url', $tc->view_path_url);

        /** wrap view */

        /** retrieve id or name */
        if ((int)$this->get('wrap_view_id', 0) == 0) {
            $this->set('wrap_view_id',
                ExtensionHelper::getInstanceID(
                    ASSET_TYPE_EXTENSION_WRAP_VIEW,
                    $this->get('wrap_view_name'),
                    'Wrap'
                )
            );
        } else {
            $this->set('wrap_view_name',
                ExtensionHelper::getInstanceTitle(
                    $this->get('wrap_view_id')
                )
            );
        }

        /** retrieve paths */
        $wc = new ViewHelper(
            $this->get('wrap_view_name'),
            'Wrap',
            $this->get('extension_instance_name'),
            $this->get('extension_type'),
            $this->get('theme_name')
        );
        $this->set('wrap_view_path', $wc->view_path);
        $this->set('wrap_view_path_url', $wc->view_path_url);

        return true;
    }

    /**
     * _importClasses
     *
     * lazy load import for extension classes and files
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
     * Theme Includer use, only, loads the page metadata
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
            $this->get('extension_path')
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
     * _loadViewMedia
     *
     * Loads Media CSS and JS files for Template and Wrap Views
     *
     * @return  null
     * @since   1.0
     */
    protected function _loadViewMedia()
    {
        $priority = Services::Registry()->get('Configuration\\media_priority_other_extension', 400);

        $file_path = $this->get('template_view_path');
        $url_path = $this->get('template_view_path_url');
        $css = Services::Document()->add_css_folder($file_path, $url_path, $priority);
        $js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 0);
        $defer = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);

        $file_path = $this->get('wrap_view_path');
        $url_path = $this->get('wrap_view_path_url');
        $css = Services::Document()->add_css_folder($file_path, $url_path, $priority);
        $js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 0);
        $defer = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);
    }

    /**
     * invokeMVC
     *
     * Instantiate the Controller and fire off the task, returns rendered output
     *
     * @return mixed
     */
    protected function invokeMVC()
    {
        $model = (string)$this->setModel();
        $this->set('model', $model);

        $cc = (string)$this->setController();
        $this->set('controller', $cc);

        $task = (string)$this->get('task', 'display');
        $this->set('task', $task);

        if (Services::Registry()->get('Configuration\\debug', 0) == 1) {
            Services::Debug()->set(' ');
            Services::Debug()->set('Includer::invokeMVC');
            Services::Debug()->set('Controller: ' . $cc . ' Task: ' . $task . ' Model: ' . $model . ' ');
            Services::Debug()->set('Extension: ' . $this->get('extension_instance_name') . ' ID: ' . $this->get('id') . '');
            Services::Debug()->set('Template: ' . $this->get('template_view_path') . '');
            Services::Debug()->set('Wrap: ' . $this->get('wrap_view_path') . '');
        }

        /** instantiate controller  */
        $controller = new $cc($this->task_request, $this->parameters);

        /** execute task: display, edit, or add  */
        $results = $controller->$task();

        /** html display filters */
        $this->parameters->set('html_display_filter', false);
        if ($this->parameters->get('html_display_filter', true) == false) {
            return $results;
        } else {
            return Services::Security()->filter_html($results);
        }
    }

    /**
     * setModel
     *
     * Set the name of the Model
     *
     * @return  string
     * @since   1.0
     */
    protected function setModel()
    {
        /** Extension Name */
        $extension = (string)ucfirst($this->get('extension_instance_name'));
        $extension = str_replace(array('-', '_'), '', $extension);

        /** 1. Specifically Named Model */
        if ($this->get('model', '') == '') {

        } else {
            $mc = (string)ucfirst($this->get('model'));
            if (class_exists($mc)) {
                return $mc;
            }
            $mc = 'Molajo' . ucfirst($this->get('model')) . 'Model';
            if (class_exists($mc)) {
                return $mc;
            }
        }

        /** 2. Extension Name (+ non-component name, if appropriate) + Model */
        $mc = 'Molajo';
        $mc .= $extension;
        if ($this->get('extension_type') == 'component') {
        } else {
            $mc .= ucfirst(trim($this->get('extension_type', 'Module')));
        }
        $mc .= 'Model';
        if (class_exists($mc)) {
            return $mc;
        }

        /** 3. Molajo + Task Name + Model */
        if ($this->get('model', '') == '') {
        } else {
            $mc = 'Molajo' .
                (string)ucfirst(strtolower($this->get('model')) .
                        'Model'
                );
            if (class_exists($mc)) {
                return $mc;
            }
        }

        /** 4. Base Class (no query) */
        return 'Model';
    }

    /**
     * setController
     *
     * Set the name of the Controller
     *
     * @return  string
     * @since   1.0
     */
    protected function setController()
    {
        /** Extension Name */
        $extension = (string)ucfirst($this->get('extension_instance_name'));
        $extension = str_replace(array('-', '_'), '', $extension);

        /** 1. Specifically Named Controller */
        if ($this->get('controller', '') == '') {
        } else {
            $cc = (string)ucfirst($this->get('controller'));
            if (class_exists($cc)) {
                return $cc;
            }
            $cc = 'Molajo' . ucfirst($this->get('controller')) . 'Controller';
            if (class_exists($cc)) {
                return $cc;
            }
        }

        /** 2. Extension Name (+ Module, if appropriate) + Controller */
        $cc = (string)ucfirst($this->get('extension_instance_name'));
        $cc = str_replace(array('-', '_'), '', $cc);
        $cc = 'Molajo';
        $cc .= $extension;
        if ($this->get('extension_type') == 'component') {
        } else {
            $cc .= ucfirst(trim($this->get('extension_type', 'Module')));
        }
        $cc .= 'Controller';
        if (class_exists($cc)) {
            return $cc;
        }

        /** 3. Molajo + Task Name + Controller */
        if ($this->get('controller', '') == '') {
            $cc = 'Molajo' .
                (string)ucfirst(strtolower($this->get('task', 'display')) .
                        'Controller'
                );
            if (class_exists($cc)) {
                return $cc;
            }
        }

        /** 4. Base Class (no query) */
        return 'Controller';
    }

    /**
     * _postMVCProcessing
     * @return bool
     */
    protected function _postMVCProcessing()
    {

    }
}
