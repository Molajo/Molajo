<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo;

use Molajo\Service\Services;
use Molajo\MVC\Controller\DisplayController;

defined('MOLAJO') or die;

/**
 * Includer
 *
 * @package     Molajo
 * @subpackage  Extension
 * @since       1.0
 */
class Includer
{
    /**
     * $name
     *
     * @var    string
     * @since  1.0
     */
    protected $name = null;

    /**
     * $type: Head, Message, Profiler, Resource, Tag, Template, Theme, Wrap
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
     * Any defined parameter for the extension can be overridden on the include
     *
     * <include:extension statement attr1=x attr2=y attrN="and-so-on" />
     *
     * @var    array
     * @since  1.0
     */
    protected $attributes = array();

    /**
     * @param   string  $name
     * @param   string  $type
     *
     * @return  null
     * @since   1.0
     */
    public function __construct($name = null, $type = null)
    {
        $this->name = $name;
        $this->type = $type;

        Services::Registry()->createRegistry('Include');

        Services::Registry()->set('Parameters', 'includer_name', $this->name);
        Services::Registry()->set('Parameters', 'includer_type', $this->type);

        Services::Registry()->copy('RouteParameters', 'Parameters', 'Criteria*');
        Services::Registry()->copy('RouteParameters', 'Parameters', 'Enable*');
        Services::Registry()->copy('RouteParameters', 'Parameters', 'Request*');
        Services::Registry()->copy('RouteParameters', 'Parameters', 'Theme*');
        Services::Registry()->copy('RouteParameters', 'Parameters', 'Page*');

        return;
    }

    /**
     * process
     *
     * - Loads Metadata (only Theme Includer)
     * - Loads Assets for Extension
     * - Activates Controller for Task
     * - Returns Rendered Output to Parse for <include:type /> replacement
     *
     * @param   $attributes <include:type attr1=x attr2=y attr3=z ... />
     *
     * @return  mixed
     * @since   1.0
     */
    public function process($attributes = array())
    {
        Services::Registry()->deleteRegistry('Tempattributes');
        Services::Registry()->createRegistry('Tempattributes');
        $this->attributes = $attributes;
        $this->getAttributes();

        $this->getExtension();

        $results = $this->setRenderCriteria();
        if ($results === false) {
            return false;
        }

        $this->loadPlugins();

        $this->onBeforeIncludeEvent();

        $rendered_output = $this->invokeMVC();

        if ($rendered_output == ''
            && Services::Registry()->get('Parameters', 'criteria_display_view_on_no_results') == 0
        ) {
        } else {
            $this->loadMedia();
            $this->loadViewMedia();
        }

        $rendered_output = $this->onAfterIncludeEvent($rendered_output);
        return $rendered_output;
    }

    /**
     * Use the view and/or wrap criteria ife specified on the <include statement
     *
     * @return  null
     * @since   1.0
     */
    protected function getAttributes()
    {
        if (count($this->attributes) > 0) {
        } else {
            return;
        }

        //todo filter input appropriately
        //todo case statements
        foreach ($this->attributes as $name => $value) {

            $name = strtolower($name);
            if ($name == 'name' || $name == 'title') {

                if ($this->name == strtolower(CATALOG_TYPE_TEMPLATE_VIEW_LITERAL)) {

                    if ((int)$value > 0) {
                        $template_id = (int)$value;
                        $template_title = Helpers::Extension()->getExtensionNode($template_id);

                    } else {
                        $template_title = ucfirst(strtolower(trim($value)));
                        $template_id = Helpers::Extension()
                            ->getInstanceID(CATALOG_TYPE_TEMPLATE_VIEW, $template_title);
                    }

                    Services::Registry()->set('Parameters', 'template_view_id', $template_id);
                    Services::Registry()->set('Parameters', 'template_view_path_node', $template_title);
                    Services::Registry()->set('Parameters', 'extension_title', $template_title);
                    Services::Registry()->set('Parameters', 'template_view_title', $template_title);

                } else {

                    $value = ucfirst(strtolower(trim($value)));
                    Services::Registry()->set('Parameters', 'extension_title', $value);
                }

            } elseif ($name == 'tag') {
                $this->tag = $value;

            } elseif ($name == strtolower(CATALOG_TYPE_TEMPLATE_VIEW_LITERAL)
                || $name == 'template_view_title'
                || $name == 'template_view'
            ) {
                $value = ucfirst(strtolower(trim($value)));

                $template_id = Helpers::Extension()
                    ->getInstanceID(CATALOG_TYPE_TEMPLATE_VIEW, $value);

                if ((int)$template_id == 0) {
                } else {
                    Services::Registry()->set('Parameters', 'template_view_id', $template_id);
                    Services::Registry()->set('Parameters', 'template_view_path_node', $value);
                    Services::Registry()->set('Parameters', 'extension_title', $value);
                    Services::Registry()->set('Parameters', 'template_view_title', $value);
                }

            } elseif ($name == 'template_view_css_id'
                || $name == 'template_css_id'
                || $name == 'template_id'
                || $name == 'id') {
                Services::Registry()->set('Parameters', 'template_view_css_id', $value);

            } elseif ($name == 'template_view_css_class'
                || $name == 'template_css_class'
                || $name == 'template_class'
                || $name == 'class'
            ) {
                Services::Registry()->set('Parameters', 'template_view_css_class', str_replace(',', ' ', $value));

            } elseif ($name == strtolower(CATALOG_TYPE_WRAP_VIEW_LITERAL)
                || $name == 'wrap_view_title'
                || $name == 'wrap_view'
                || $name == 'wrap_title') {

                $value = ucfirst(strtolower(trim($value)));
                $wrap_id = Helpers::Extension()
                    ->getInstanceID(CATALOG_TYPE_WRAP_VIEW, $value);

                if ((int)$wrap_id == 0) {
                } else {
                    Services::Registry()->set('Parameters', 'wrap_view_path_node', $value);
                    Services::Registry()->set('Parameters', 'wrap_view_id', $wrap_id);
                }

            } elseif ($name == 'wrap_view_css_id'
                || $name == 'wrap_css_id'
                || $name == 'wrap_id') {
                Services::Registry()->set('Parameters', 'wrap_view_css_id', $value);

            } elseif ($name == 'wrap_view_css_class'
                || $name == 'wrap_css_class'
                || $name == 'wrap_class') {
                Services::Registry()->set('Parameters', 'wrap_view_css_class', str_replace(',', ' ', $value));

            } elseif ($name == 'wrap_view_role'
                || $name == 'wrap_role'
                || $name == 'role') {
                Services::Registry()->set('Parameters', 'wrap_view_role', str_replace(',', ' ', $value));

            } elseif ($name == 'wrap_view_property'
                || $name == 'wrap_property'
                || $name == 'property') {
                Services::Registry()->set('Parameters', 'wrap_view_property', str_replace(',', ' ', $value));

            } elseif ($name == 'datalist') {
                Services::Registry()->set('Parameters', 'datalist', $value);
                Services::Registry()->set('Parameters', 'model_type', 'datalist');
                Services::Registry()->set('Parameters', 'model_name', $value);
                Services::Registry()->set('Parameters', 'model_query_object', QUERY_OBJECT_LIST);

            } elseif ($name == 'model_name') {
                Services::Registry()->set('Parameters', 'model_name', $value);

            } elseif ($name == 'model_type') {
                Services::Registry()->set('Parameters', 'model_type', $value);

            } elseif ($name == 'model_parameter_np'
                || $name == 'parameter_np') {
                Services::Registry()->set('Parameters', 'model_type', 'PlugindataNoplugins');
                Services::Registry()->set('Parameters', 'model_name', $value);
                Services::Registry()->set('Parameters', 'model_query_object', QUERY_OBJECT_LIST);

            } elseif ($name == 'model_query_object'
                || $name == 'query_object') {
                Services::Registry()->set('Parameters', 'model_query_object', $value);

            } else {
                /** Todo: For security reasons: match field to model registry and filter first */
                Services::Registry()->set('Tempattributes', $name, $value);
            }
        }
    }

    /**
     * getExtension
     *
     * Retrieve extension information after looking up the ID in the extension-specific includer
     *
     * @return  bool
     * @since   1.0
     */
    protected function getExtension()
    {
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
        $temp = Services::Registry()->get('Parameters', 'template*');

        if (is_array($temp) && count($temp) > 0) {
            foreach ($temp as $key => $value) {

                if ($key == 'template_view_id'
                    || $key == 'template_view_path_node'
                    || $key == 'template_view_title') {

                } elseif (is_array($value)) {
                    $saveTemplate[$key] = $value;

                } elseif ($value === 0
                    || trim($value) == ''
                    || $value === null) {

                } else {
                    $saveTemplate[$key] = $value;
                }
            }
        }

        $saveWrap = array();
        $temp = Services::Registry()->get('Parameters', 'wrap*');
        $temp2 = Services::Registry()->get('Parameters', 'model*');
        $temp3 = array_merge($temp, $temp2);
        $temp2 = Services::Registry()->get('Parameters', 'data*');
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

        Services::Registry()->delete('Parameters', 'item*');
        Services::Registry()->delete('Parameters', 'list*');
        Services::Registry()->delete('Parameters', 'form*');
        Services::Registry()->delete('Parameters', 'menuitem*');

        Services::Registry()->sort('Parameters');

        $fields = Services::Registry()->get('Configuration', 'application*');
        if (count($fields) === 0 || $fields === false) {
        } else {
            foreach ($fields as $key => $value) {
                Services::Registry()->set('Parameters', $key, $value);
            }
        }

        $fields = Services::Registry()->getArray('Tempattributes');
        if (count($fields) === 0 || $fields === false) {
        } else {
            foreach ($fields as $key => $value) {
                Services::Registry()->set('Parameters', $key, $value);
            }
        }

        return true;
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
        $template_id = (int)Services::Registry()->get('Parameters', 'template_view_id');

        if ((int)$template_id == 0) {
            $template_title = Services::Registry()->get('Parameters', 'template_view_path_node');
            if (trim($template_title) == '') {
            } else {
                $template_id = Helpers::Extension()
                    ->getInstanceID(CATALOG_TYPE_TEMPLATE_VIEW, $template_title);
                Services::Registry()->set('Parameters', 'template_view_id', $template_id);
            }
        }

        if ((int)$template_id == 0) {
            $template_id = Helpers::View()->getDefault(CATALOG_TYPE_TEMPLATE_VIEW_LITERAL);
            Services::Registry()->set('Parameters', 'template_view_id', $template_id);
        }

        if ((int)$template_id == 0) {
            return false;
        }

        Helpers::View()->get($template_id, CATALOG_TYPE_TEMPLATE_VIEW_LITERAL);

        if (is_array($saveTemplate) && count($saveTemplate) > 0) {
            foreach ($saveTemplate as $key => $value) {
                Services::Registry()->set('Parameters', $key, $value);
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
                    Services::Registry()->set('Parameters', $key, $value);
                }
            }
        }

        $wrap_id = 0;
        $wrap_title = '';

        $wrap_id = (int)Services::Registry()->get('Parameters', 'wrap_view_id');

        if ((int)$wrap_id == 0) {
            $wrap_title = Services::Registry()->get('Parameters', 'wrap_view_path_node', '');
            if (trim($wrap_title) == '') {
                $wrap_title = 'None';
            }
            $wrap_id = Helpers::Extension()
                ->getInstanceID(CATALOG_TYPE_WRAP_VIEW, $wrap_title);
            Services::Registry()->set('Parameters', 'wrap_view_id', $wrap_id);
        }

        if (is_array($saveWrap) && count($saveWrap) > 0) {
            foreach ($saveWrap as $key => $value) {
                if ($key == 'wrap_view_id' || $key == 'wrap_view_path_node' || $key == 'wrap_view_title') {
                } else {
                    Services::Registry()->set('Parameters', $key, $value);
                }
            }
        }

        $saveWrap = array();
        $temp = Services::Registry()->get('Parameters', 'wrap*');
        $temp2 = Services::Registry()->get('Parameters', 'model*');
        $temp3 = array_merge($temp, $temp2);
        $temp2 = Services::Registry()->get('Parameters', 'data*');
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

        Helpers::View()->get($wrap_id, CATALOG_TYPE_WRAP_VIEW_LITERAL);

        if (is_array($saveWrap) && count($saveWrap) > 0) {
            foreach ($saveWrap as $key => $value) {
                if ($key == 'wrap_view_id' || $key == 'wrap_view_path_node' || $key == 'wrap_view_title') {
                } else {
                    Services::Registry()->set('Parameters', $key, $value);
                }
            }
        }

        if (Services::Registry()->exists('Parameters', 'wrap_view_role')) {
        } else {
            Services::Registry()->set('Parameters', 'wrap_view_role', '');
        }
        if (Services::Registry()->exists('Parameters', 'wrap_view_property')) {
        } else {
            Services::Registry()->set('Parameters', 'wrap_view_property', '');
        }
        if (Services::Registry()->exists('Parameters', 'wrap_view_header_level')) {
        } else {
            Services::Registry()->set('Parameters', 'wrap_view_header_level', '');
        }
        if (Services::Registry()->exists('Parameters', 'wrap_view_show_title')) {
        } else {
            Services::Registry()->set('Parameters', 'wrap_view_show_title', '');
        }
        if (Services::Registry()->exists('Parameters', 'wrap_view_show_subtitle')) {
        } else {
            Services::Registry()->set('Parameters', 'wrap_view_show_subtitle', '');
        }

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
        $templatePlugins = Services::Filesystem()->folderFolders(
            Services::Registry()->get('Parameters', 'template_view_path') . '/' . 'Plugin'
        );

        if (count($templatePlugins) == 0 || $templatePlugins === false) {
        } else {
            $this->registerPlugins(
                $templatePlugins,
                Services::Registry()->get('Parameters', 'template_view_namespace')
            );
        }

        $wrapPlugins = Services::Filesystem()->folderFolders(
            Services::Registry()->get('Parameters', 'wrap_view_path') . '/' . 'Plugin'
        );

        if (count($wrapPlugins) == 0 || $wrapPlugins === false) {
        } else {
            $this->registerPlugins(
                $wrapPlugins,
                Services::Registry()->get('Parameters', 'wrap_view_namespace')
            );
        }

        return;
    }

    /**
     * Iterate a set of Extension Plugins to Overrides Core and Plugin folders
     *
     * @param   $plugins array of folder names
     * @param   $path
     *
     * @return  void
     * @since   1.0
     */
    protected function registerPlugins($plugins, $path)
    {
        foreach ($plugins as $folder) {
            Services::Event()->registerPlugin(
                $folder . 'Plugin',
                $path . '\\Plugin\\' . $folder . '\\' . $folder . 'Plugin'
            );
        }
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
        $priority = Services::Registry()->get('Parameters', 'criteria_media_priority_other_extension', 400);

        $file_path = Services::Registry()->get('Parameters', 'template_view_path');
        $url_path = Services::Registry()->get('Parameters', 'template_view_path_url');

        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        $file_path = Services::Registry()->get('Parameters', 'wrap_view_path');
        $url_path = Services::Registry()->get('Parameters', 'wrap_view_path_url');

        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        return $this;
    }

    /**
     * Schedule Event onBeforeInclude Event
     *
     * @return  bool
     * @since   1.0
     */
    protected function onBeforeIncludeEvent()
    {
        Services::Profiler()->set('IncludeService onBeforeInclude', LOG_OUTPUT_PLUGINS, VERBOSE);

        $parameters = Services::Registry()->getArray('Parameters');

        $arguments = array(
            'parameters' => $parameters
        );

        $arguments = Services::Event()->scheduleEvent('onBeforeInclude', $arguments);

        if ($arguments === false) {
            Services::Registry()->set('Parameters', 'error_status', 1);
            Services::Profiler()->set('IncludeService onBeforeInclude failed', LOG_OUTPUT_PLUGINS);
            return false;
        }

        Services::Registry()->delete('Parameters');
        Services::Registry()->loadArray('Parameters', $arguments['parameters']);
        Services::Registry()->sort('Parameters');

        return true;
    }

    /**
     * Instantiate the Controller and execute action method, receive rendered output from Controller
     *
     * @return  mixed
     * @since   1.0
     */
    protected function invokeMVC()
    {
        Services::Registry()->sort('Parameters');

        $message = 'Includer->invokeMVC '
            . 'Name ' . $this->name
            . ' Type: ' . $this->type
            . ' Template: ' . Services::Registry()->get('Parameters', 'template_view_title');

        $message .= ' Parameters:<br />';
        ob_start();
        $message .= Services::Registry()->get('Parameters', '*');
        $message .= ob_get_contents();
        ob_end_clean();

        Services::Profiler()->set($message, LOG_OUTPUT_RENDERING, VERBOSE);

        $controller = new DisplayController();
        $controller->set('id', (int)Services::Registry()->get('Parameters', 'source_id'));
        $parms = Services::Registry()->getArray('Parameters');
        $cached_output = Services::Cache()->get(CATALOG_TYPE_TEMPLATE_VIEW_LITERAL, implode('', $parms));

        if ($cached_output === false) {
            if (count($parms) > 0) {
                foreach ($parms as $key => $value) {
                    $controller->set($key, $value);
                }
            }

            $results = $controller->execute();

            Services::Cache()->set(CATALOG_TYPE_TEMPLATE_VIEW_LITERAL, implode('', $parms), $results);
        } else {
            $results = $cached_output;
        }

        return $results;
    }

    /**
     * Schedule Event onAfterIncludeEvent Event
     *
     * @param   $rendered_output
     *
     * @return  bool
     * @since   1.0
     */
    protected function onAfterIncludeEvent($rendered_output)
    {
        Services::Profiler()->set('IncludeService onAfterInclude', LOG_OUTPUT_PLUGINS, VERBOSE);

        $parameters = Services::Registry()->getArray('Parameters');

        $arguments = array(
            'parameters' => $parameters,
            'rendered_output' => $rendered_output
        );

        $arguments = Services::Event()->scheduleEvent('onAfterInclude', $arguments);

        if ($arguments === false) {
            Services::Registry()->set('Parameters', 'error_status', 1);
            Services::Profiler()->set('IncludeService onAfterInclude failed', LOG_OUTPUT_PLUGINS);
            return false;
        }

        Services::Registry()->delete('Parameters');
        Services::Registry()->loadArray('Parameters', $arguments['parameters']);
        Services::Registry()->sort('Parameters');

        return $rendered_output;
    }
}
