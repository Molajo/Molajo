<?php
/**
 * Theme Service Includer Base Class
 *
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Theme;

use Molajo\Service\Services;
use Molajo\MVC\Controller\DisplayController;
use Molajo\Service\Services\Theme\Helper\ContentHelper;
use Molajo\Service\Services\Theme\Helper\ExtensionHelper;
use Molajo\Service\Services\Theme\Helper\ThemeHelper;
use Molajo\Service\Services\Theme\Helper\ViewHelper;

defined('NIAMBIE') or die;

/**
 * The Includer acts as the base class for a set of classes which gather the input parameters needed
 * to generate a specific <include:type name=statement/>, passing on the parameters to the MVC for rendering
 * and then returning the rendered results to the Theme Service.
 *
 * The Theme Service Includer schedules onBeforeInclude and onAfterInclude Events
 *
 * @author       Amy Stephen
 * @license      GPL v 2, or later and MIT
 * @copyright    2012 Amy Stephen. All rights reserved.
 * @since        1.0
 */
class Includer
{
    /**
     * Include Name
     *
     * Values include Head, Message, Page, Profiler, Tag, Template, Theme, and Wrap
     *
     * <include:head/>
     * <include:template name=template-name/>
     * <include:message/>
     *
     * @var    string
     * @since  1.0
     */
    protected $include_name = null;

    /**
     * Include Type
     *
     * Type is only different than name in type:name pairs where type is an alias of name
     * Asset and metadata types are an alias of template; defer type is an alias of head
     *
     * @var    string
     * @since  1.0
     */
    protected $include_type = null;

    /**
     * Name - from attributes
     *
     * <include:template name=this-value/>
     *
     * @var    string
     * @since  1.0
     */
    protected $name = null;

    /**
     * Attributes - extracted from include statement and placed into an array by parsing process
     *
     * <include:template name=this-value all=the rest=of-this goes=into-attributes as=named-pairs/>
     *
     * @var    string
     * @since  1.0
     */
    protected $attributes = array();

    /**
     * $tag
     *
     * @var    array
     * @since  1.0
     */
    protected $tag = array();

    /**
     * Parameters to pass on to the MVC for rendering the include statement
     *
     * @var    string
     * @since  1.0
     */
    protected $parameters = array();

    /**
     * Name of Model Registry used to generate input for the include
     *
     * @var    string
     * @since  1.0
     */
    protected $model_registry_name = null;

    /**
     * Model used to generate input for the include
     *
     * @var    string
     * @since  1.0
     */
    protected $model_registry = array();

    /**
     * Rendered by the Views and passed back through the Theme Includers to the Theme Service
     *
     * @var    string
     * @since  1.0
     */
    protected $rendered_output = null;

    /**
     * Used in editing get and set values
     *
     * @var    string
     * @since  1.0
     */
    protected $property_array = array(
        'include_name',
        'include_type',
        'name',
        'attributes',
        'tag',
        'parameters',
        'model_registry_name',
        'model_registry',
        'rendered_output'
    );

    /**
     * Content Helper
     *
     * @var    object
     * @since  1.0
     */
    protected $contentHelper;

    /**
     * Extension Helper
     *
     * @var    object
     * @since  1.0
     */
    protected $extensionHelper;

    /**
     * Theme Helper
     *
     * @var    object
     * @since  1.0
     */
    protected $themeHelper;

    /**
     * View Helper
     *
     * @var    object
     * @since  1.0
     */
    protected $viewHelper;

    /**
     * Class Constructor
     *
     * @param   string  $include_name
     * @param   string  $include_type
     * @param   string  $tag
     * @param   string  $parameters
     *
     * @return  object  Includer
     * @since   1.0
     */
    public function __construct($include_name = null, $include_type = null, $tag = null, $parameters = array())
    {
        $this->set('', 'include_name', $include_name);
        $this->set('', 'include_type', $include_type);
        $this->set('', 'name', null);
        $this->set('', 'attributes', array());
        $this->set('', 'tag', $tag);
        $this->set('', 'parameters', $parameters);
        $this->set('', 'model_registry_name', null);
        $this->set('', 'model_registry', null);
        $this->set('', 'rendered_output', null);

        $this->contentHelper = new ContentHelper();
        $this->extensionHelper = new ExtensionHelper();
        $this->themeHelper = new ThemeHelper();
        $this->viewHelper = new ViewHelper();

        return $this;
    }

    /**
     * Get the  value (or default) of the specified property and key
     *
     * @param   string  $property
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  mixed
     * @since   1.0
     */
    protected function get($property = '', $key, $default = null)
    {
//        echo 'GET $key ' . $key . ' ' . ' Property ' . $property . '<br />';

        if (in_array($key, $this->property_array) && $property == '') {
            $value = $this->$key;
            return $value;
        }

        if ($property == 'parameters') {
            if (isset($this->parameters[$key])) {
                return $this->parameters[$key];
            }
            $this->parameters[$key] = $default;
            return $this->parameters[$key];
        }

        if ($property == 'model_registry') {
            if (isset($this->model_registry[$key])) {
                return $this->model_registry[$key];
            }
            $this->model_registry[$key] = $default;
            return $this->model_registry[$key];
        }

        if ($property == 'attributes') {
            if (isset($this->attributes[$key])) {
                return $this->attributes[$key];
            }
            $this->attributes[$key] = $default;
            return $this->attributes[$key];
        }

        throw new \OutOfRangeException('Includer: get for unknown property: ' . $property . ' and key: ' . $key);
    }

    /**
     * Set the value of the specified property and key
     *
     * @param   string  $property
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed
     * @since   1.0
     */
    protected function set($property = '', $key, $value = null)
    {
//echo 'SET $key ' . $key . ' ' . ' Property ' . $property . '<br />';

        if (in_array($key, $this->property_array) && $property == '') {
            $this->$key = $value;
            return $this->$key;
        }

        if ($property == 'parameters') {
            $this->parameters[$key] = $value;
            return $this->parameters[$key];
        }

        if ($property == 'model_registry') {
            $this->model_registry[$key] = $value;
            return $this->model_registry[$key];
        }

        if ($property == 'attributes') {
            $this->attributes[$key] = $value;
            return $this->attributes[$key];
        }

        throw new \OutOfRangeException('Includer: set for unknown key: ' . $key . ' and property: ' . $property);
    }

    /**
     * Includer controller executes steps in sequence needed:
     *
     * - getAttributes - extracts extension name and other parameters defined on the <include:type/> statement
     * - setExtensionParameters - for the specific type of includer, retrieve parameters needed for rendering
     * - loadPlugins - load Plugin Overrides in Extension folder
     * - onBeforeIncludeEvent - Schedule on Before Include Event
     * - renderOutput - passes parameters to MVC and receives rendered output
     * - loadAssets - loads CSS and JS files for rendered output
     * - onAfterIncludeEvent - Schedule After Include Event
     * - Returns Rendered Output to the Theme Service which will parse output for additional <include:type />
     *
     * @param   array   $attributes <include:type name=x the=rest are=attributes/>
     *
     * @return  mixed
     * @since   1.0
     */
    public function process($attributes)
    {
        $this->getAttributes($attributes);

        $results = $this->setExtensionParameters();
        if ($results === false) {
            return false;
        }

        $this->onBeforeIncludeEvent();

        $this->loadPlugins();

        $this->renderOutput();

        $this->onAfterIncludeEvent();

        return $this->rendered_output;
    }

    /**
     * Use the view and/or wrap criteria ife specified on the <include statement
     *
     * @param   $attributes
     *
     * @return  void
     * @since   1.0
     */
    protected function getAttributes($attributes)
    {
        $this->attributes = array();
        $this->name = null;

        if (count($attributes) > 0) {
        } else {
            return;
        }

        foreach ($attributes as $key => $value) {

            if (strtolower($key) == 'name') {
                $this->name = strtolower(trim($value));
            } else {
                $this->attributes[$key] = $value;
            }
        }

        return;
    }

    /**
     * Uses Include Request and Attributes (overrides) to set Parameters for Rendering
     *
     * @return  bool
     * @since   1.0
     */
    protected function setRenderCriteria()
    {
        $template_id = 0;
        $template_title = '';

        $saveTemplate = array();
        $temp = Services::Registry()->get('parameters', 'template*');

        if (is_array($temp) && count($temp) > 0) {
            foreach ($temp as $key => $value) {

                if ($key == 'template_view_id'
                    || $key == 'template_view_path_node'
                    || $key == 'template_view_title'
                ) {

                } elseif (is_array($value)) {
                    $saveTemplate[$key] = $value;

                } elseif ($value === 0
                    || trim($value) == ''
                    || $value === null
                ) {

                } else {
                    $saveTemplate[$key] = $value;
                }
            }
        }

        $saveWrap = array();
        $temp = Services::Registry()->get('parameters', 'wrap*');
        $temp2 = Services::Registry()->get('parameters', 'model*');
        $temp3 = array_merge($temp, $temp2);
        $temp2 = Services::Registry()->get('parameters', 'data*');
        $temp = array_merge($temp2, $temp3);

        if (is_array($temp) && count($temp) > 0) {
            foreach ($temp as $key => $value) {

                if (is_array($value)) {
                    $saveWrap[$key] = $value;

                } elseif ($value === 0 || trim($value) == '' || $value === null) {

                } else {
                    $saveWrap[$key] = $value;
                }
            }
        }

        if ($this->type == CATALOG_TYPE_WRAP_VIEW_LITERAL) {
        } else {
            $results = $this->setTemplateRenderCriteria($saveTemplate);
            if ($results === false) {
                return false;
            }
        }

        $results = $this->setWrapRenderCriteria($saveWrap);
        if ($results === false) {
            return false;
        }

        Services::Registry()->delete('parameters', 'item*');
        Services::Registry()->delete('parameters', 'list*');
        Services::Registry()->delete('parameters', 'form*');
        Services::Registry()->delete('parameters', 'menuitem*');

        Services::Registry()->sort('parameters');

        $fields = Services::Registry()->get(CONFIGURATION_LITERAL, 'application*');
        if (count($fields) === 0 || $fields === false) {
        } else {
            foreach ($fields as $key => $value) {
                Services::Registry()->set('include', $key, $value);
            }
        }

        $fields = Services::Registry()->getArray('Tempattributes');
        if (count($fields) === 0 || $fields === false) {
        } else {
            foreach ($fields as $key => $value) {
                Services::Registry()->set('include', $key, $value);
            }
        }

        $message = 'Includer: Render Criteria '
            . 'Name ' . strtolower($this->name)
            . ' Type ' . $this->type
            . ' Template ' . Services::Registry()->get('parameters', 'template_view_title')
            . ' Model Type ' . Services::Registry()->get('parameters', 'model_type')
            . ' Model Name ' . Services::Registry()->get('parameters', 'model_name');

        Services::Profiler()->set($message, PROFILER_RENDERING, VERBOSE);

        return true;
    }

    /**
     * Retrieve extension information
     *
     * @return  bool
     * @since   1.0
     */
    protected function getExtension()
    {
        return;
    }

    /**
     * Process Template Options
     *
     * @param   string  $saveTemplate
     *
     * @return  bool
     * @since   1.0
     */
    protected function setTemplateRenderCriteria($saveTemplate)
    {
        $template_id = (int)Services::Registry()->get('parameters', 'template_view_id');

        if ((int)$template_id == 0) {
            $template_title = Services::Registry()->get('parameters', 'template_view_path_node');
            if (trim($template_title) == '') {
            } else {
                $template_id = $this->extensionHelper
                    ->getId(CATALOG_TYPE_TEMPLATE_VIEW, $template_title);
                Services::Registry()->set('include', 'template_view_id', $template_id);
            }
        }

        if ((int)$template_id == 0) {
            $template_id = $this->viewHelper->getDefault(CATALOG_TYPE_TEMPLATE_VIEW_LITERAL);
            Services::Registry()->set('include', 'template_view_id', $template_id);
        }

        if ((int)$template_id == 0) {
            return false;
        }

        $this->viewHelper->get($template_id, CATALOG_TYPE_TEMPLATE_VIEW_LITERAL);

        if (is_array($saveTemplate) && count($saveTemplate) > 0) {
            foreach ($saveTemplate as $key => $value) {
                Services::Registry()->set('include', $key, $value);
            }
        }

        return true;
    }

    /**
     * Process Wrap Options
     *
     * @param   string  @saveWrap
     *
     * @return  bool
     * @since   1.0
     */
    protected function setWrapRenderCriteria($saveWrap)
    {
        if (is_array($saveWrap) && count($saveWrap) > 0) {
            foreach ($saveWrap as $key => $value) {
                if (is_array($value)) {
                    $saveWrap[$key] = $value;

                } elseif ($value === 0 || trim($value) == '' || $value === null) {

                } else {
                    Services::Registry()->set('include', $key, $value);
                }
            }
        }

        $wrap_id = 0;
        $wrap_title = '';

        $wrap_id = (int)Services::Registry()->get('parameters', 'wrap_view_id');

        if ((int)$wrap_id == 0) {
            $wrap_title = Services::Registry()->get('parameters', 'wrap_view_path_node', '');
            if (trim($wrap_title) == '') {
                $wrap_title = 'None';
            }
            $wrap_id = $this->extensionHelper
                ->getId(CATALOG_TYPE_WRAP_VIEW, $wrap_title);
            Services::Registry()->set('include', 'wrap_view_id', $wrap_id);
        }

        if (is_array($saveWrap) && count($saveWrap) > 0) {
            foreach ($saveWrap as $key => $value) {
                if ($key == 'wrap_view_id' || $key == 'wrap_view_path_node' || $key == 'wrap_view_title') {
                } else {
                    Services::Registry()->set('include', $key, $value);
                }
            }
        }

        $saveWrap = array();
        $temp = Services::Registry()->get('parameters', 'wrap*');
        $temp2 = Services::Registry()->get('parameters', 'model*');
        $temp3 = array_merge($temp, $temp2);
        $temp2 = Services::Registry()->get('parameters', 'data*');
        $temp = array_merge($temp2, $temp3);

        if (is_array($temp) && count($temp) > 0) {
            foreach ($temp as $key => $value) {

                if (is_array($value)) {
                    $saveWrap[$key] = $value;

                } elseif ($value === 0 || trim($value) == '' || $value === null) {

                } else {
                    $saveWrap[$key] = $value;
                }
            }
        }

        $this->viewHelper->get($wrap_id, CATALOG_TYPE_WRAP_VIEW_LITERAL);

        if (is_array($saveWrap) && count($saveWrap) > 0) {
            foreach ($saveWrap as $key => $value) {
                if ($key == 'wrap_view_id' || $key == 'wrap_view_path_node' || $key == 'wrap_view_title') {
                } else {
                    Services::Registry()->set('include', $key, $value);
                }
            }
        }

        if (Services::Registry()->exists('parameters', 'wrap_view_role')) {
        } else {
            Services::Registry()->set('include', 'wrap_view_role', '');
        }
        if (Services::Registry()->exists('parameters', 'wrap_view_property')) {
        } else {
            Services::Registry()->set('include', 'wrap_view_property', '');
        }
        if (Services::Registry()->exists('parameters', 'wrap_view_header_level')) {
        } else {
            Services::Registry()->set('include', 'wrap_view_header_level', '');
        }
        if (Services::Registry()->exists('parameters', 'wrap_view_show_title')) {
        } else {
            Services::Registry()->set('include', 'wrap_view_show_title', '');
        }
        if (Services::Registry()->exists('parameters', 'wrap_view_show_subtitle')) {
        } else {
            Services::Registry()->set('include', 'wrap_view_show_subtitle', '');
        }

        Services::Registry()->sort('parameters');

        return true;
    }

    /**
     * Load Plugins Overrides from the Template and/or Wrap View folders
     *
     * @return  void
     * @since   1.0
     */
    protected function loadPlugins()
    {

        $node = Services::Registry()->get('parameters', 'extension_name_path_node');

        Services::Event()->registerPlugins(
            $this->extensionHelper->getPath(CATALOG_TYPE_RESOURCE, $node),
            $this->extensionHelper->getNamespace(CATALOG_TYPE_RESOURCE, $node)
        );

        $node = Services::Registry()->get('parameters', 'template_view_path_node');

        Services::Event()->registerPlugins(
            $this->extensionHelper->getPath(CATALOG_TYPE_TEMPLATE_VIEW, $node),
            $this->extensionHelper->getNamespace(CATALOG_TYPE_TEMPLATE_VIEW, $node)
        );

        $node = Services::Registry()->get('parameters', 'wrap_view_path_node');

        Services::Event()->registerPlugins(
            $this->extensionHelper->getPath(CATALOG_TYPE_WRAP_VIEW, $node),
            $this->extensionHelper->getNamespace(CATALOG_TYPE_WRAP_VIEW, $node)
        );

        return;
    }

    /**
     *  Instantiate Controller Class and pass in Parameters, Model Registry and Name and
     *  Include Name and Type. The MVC will render the output, and send it back to this method.
     *
     *  @return  void
     *  @since   1.0
     */
    protected function renderOutput()
    {
        $model_registry_name = ucfirst(strtolower(Services::Registry()->get('parameters', 'model_name')))
            . ucfirst(strtolower(Services::Registry()->get('parameters', 'model_type')));

        $controller = new DisplayController();

        $controller->set(
            'primary_key_value',
            (int)Services::Registry()->get('parameters', 'source_id'),
            'model_registry'
        );

        $controller->set('include', Services::Registry()->getArray('parameters'));
        $controller->set('model_registry', Services::Registry()->get($model_registry_name));
        $controller->set('model_registry_name', $model_registry_name);

        $cache_key = implode('', $controller->set('include', Services::Registry()->getArray('parameters')));
        $cached_output = Services::Cache()->get(CATALOG_TYPE_TEMPLATE_VIEW_LITERAL, $cache_key);

//@todo check parameter to see if individual item should be cached
        if ($cached_output === false) {

            $this->rendered_output = $controller->execute();

            $model_registry_name = $controller->get('model_registry_name');

            Services::Registry()->delete($model_registry_name);
            Services::Registry()->createRegistry($model_registry_name);
            Services::Registry()->loadArray($model_registry_name, $controller->get('model_registry'));

            Services::Registry()->delete('parameters');
            Services::Registry()->createRegistry('parameters');
            Services::Registry()->loadArray('parameters', $controller->get('parameters'));
            Services::Registry()->sort('parameters');

            Services::Cache()->set(CATALOG_TYPE_TEMPLATE_VIEW_LITERAL, $cache_key, $this->rendered_output);

        } else {
            $this->rendered_output = $cached_output;
        }

        if ($this->rendered_output == ''
            && Services::Registry()->get('parameters', 'criteria_display_view_on_no_results') == 0
        ) {
        } else {
            $this->loadMedia();
            $this->loadViewMedia();
        }

        return;
    }

    /**
     * Loads Media CSS and JS files for extension and related content
     *
     * @return  null
     * @since   1.0
     */
    protected function loadMedia()
    {
        return $this;
    }

    /**
     * Loads Media CSS and JS files for Template and Wrap Views
     *
     * @return  null
     * @since   1.0
     */
    protected function loadViewMedia()
    {
        $priority = Services::Registry()->get('parameters', 'criteria_media_priority_other_extension', 400);
        $file_path = Services::Registry()->get('parameters', 'template_view_path');
        $url_path = Services::Registry()->get('parameters', 'template_view_path_url');

        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        $file_path = Services::Registry()->get('parameters', 'wrap_view_path');
        $url_path = Services::Registry()->get('parameters', 'wrap_view_path_url');

        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        return $this;
    }

    /**
     * Schedule Event onBeforeIncludeEvent
     *
     * @return  boolean
     * @since   1.0
     */
    protected function onBeforeIncludeEvent()
    {
        return $this->triggerEvent('onBeforeInclude');
    }

    /**
     * Schedule Event onAfterParseEvent Event
     *
     * @return  boolean
     * @since   1.0
     */
    protected function onAfterIncludeEvent()
    {
        return $this->triggerEvent('onAfterInclude');
    }

    /**
     * Common Method for Includer Events
     *
     * @param   string  $event_name
     *
     * @return  string  void
     * @since   1.0
     */
    protected function triggerEvent($event_name)
    {
        $model_registry_name = ucfirst(strtolower(Services::Registry()->get('parameters', 'model_name')))
            . ucfirst(strtolower(Services::Registry()->get('parameters', 'model_type')));

        $arguments = array(
            'model' => null,
            'model_registry' => Services::Registry()->get($model_registry_name),
            'parameters' => Services::Registry()->get('parameters'),
            'query_results' => array(),
            'row' => null,
            'rendered_output' => $this->rendered_output,
            'include_parse_sequence' => array(),
            'include_parse_exclude_until_final' => array()
        );

        $arguments = Services::Event()->scheduleEvent(
            $event_name,
            $arguments,
            array()
        );

        if (isset($arguments['model_registry'])) {
            Services::Registry()->delete($model_registry_name);
            Services::Registry()->createRegistry($model_registry_name);
            Services::Registry()->loadArray($model_registry_name, $arguments['model_registry']);
        }

        if (isset($arguments['parameters'])) {
            Services::Registry()->delete('parameters');
            Services::Registry()->createRegistry('parameters');
            Services::Registry()->loadArray('parameters', $arguments['parameters']);
            Services::Registry()->sort('parameters');
        }

        if (isset($arguments['rendered_output'])) {
            $this->rendered_output = $arguments['rendered_output'];
        }

        return;
    }
}
