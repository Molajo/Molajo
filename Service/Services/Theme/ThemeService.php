<?php
/**
 * Theme Service
 *
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Service\Services\Theme;

use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * The Theme Service passes the information retrieved in Route to the Content Helper
 * which retrieves additional information about the request, including the Theme,
 * Page, Template, and Wrap Views associated with the primary request.
 *
 * Next, the Theme Includer passes the request to the MVC which renders the Theme Index.php file.
 * The output from that process is used as initial input to the parsing process in this class
 * which parses rendered output for <include:type/> statements.
 *
 * Each include statement is processed by its associated Includer class in order to assemble the
 * parameter values needed by the MVC to render the output. After rendering the MVC passes the
 * rendered output back to the Includer, which passes it back to this class.
 *
 * Once returned, the rendered output is again parsed for possible new <include:type/> statements.
 * This recursive rendering and parsing process continues until no more includes are found.
 *
 * Once complete, the Theme Service passes the rendered output back to the Application Front
 * Controller class, which sends the results as an HTTP Response back to the requester, thus
 * concluding the request to response task.
 *
 * The Theme Service schedules onBeforeParse, onBeforeParseHead, and onAfterParse Events.
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class ThemeService
{
    /**
     * System defined order for processing includes
     *
     * @var    array
     * @since  1.0
     */
    protected $sequence = array();

    /**
     * Final include types
     *
     * @var    array
     * @since  1.0
     */
    protected $final = array();

    /**
     * Exclude from parsing
     *
     * @var    array
     * @since  1.0
     */
    protected $exclude_until_final = array();

    /**
     * Final processing for includes
     *
     * @var    boolean
     * @since  1.0
     */
    protected $final_indicator = false;

    /**
     * Include Statements discovered during parsing
     *
     * @var    array
     * @since  1.0
     */
    protected $include_request = array();

    /**
     * Accumulated rendered output
     *
     * @var    array
     * @since  1.0
     */
    protected $rendered_output = null;

    /**
     * Stores an array of key/value Parameters settings
     *
     * @var    array
     * @since  1.0
     */
    protected $parameters = array();

    /**
     * List of Known, Expected Properties for Parameters
     *
     * @var    object
     * @since  1.0
     */
    protected $parameter_property_array = array();

    /**
     * List of Classes
     *
     * @var    object
     * @since  1.0
     */
    protected $class_array = array();

    /**
     * Load sequence.xml file contents into array for determining processing order
     *
     * Invoke Theme Includer to load page metadata, and theme language and media resources
     *
     * Render Theme, parse output for <include:type/> statements, pass to
     *  include renderer, continuing until no more <include:type/> statements are found
     *
     * @param  array       $parameters                  key value pairs
     * @param  array       $parameter_property_array    valid parameter properties from route
     * @param  array       $class_array                 array of classes with namespaces
     * @param  null|array  $override_parse_sequence     override file with body include statements
     * @param  null|array  $override_parse_final        override file with head include statements
     *
     * @return  string
     * @since   1.0
     */
    public function process($parameters, $parameter_property_array,
        $class_array = array(), $override_parse_sequence = null, $override_parse_final = null)
    {
        Services::Profiler()->set('Theme Service: Started', PROFILER_RENDERING);

        $this->parameters = $parameters;

        $this->parameter_property_array = $parameter_property_array;

        $this->class_array = $class_array;

        if ($override_parse_sequence === null) {
            $sequence = Services::Configuration()->getFile(PARSE_LITERAL, 'Parse_sequence');
        } else {
            $sequence = Services::Configuration()->getFile(PARSE_LITERAL, $override_parse_sequence);
        }

        foreach ($sequence->include as $next) {
            $this->sequence[] = (string)$next;
        }

        if ($override_parse_final === null) {
            $final = Services::Configuration()->getFile(PARSE_LITERAL, 'Parse_final');
        } else {
            $final = Services::Configuration()->getFile(PARSE_LITERAL, $override_parse_final);
        }

        foreach ($final->include as $next) {
            $sequence = (string)$next;
            $this->final[] = (string)$next;

            if (stripos($sequence, ':')) {
                $include_name = substr($sequence, 0, strpos($sequence, ':'));
            } else {
                $include_name = $sequence;
            }

            $this->exclude_until_final[] = $include_name;
        }

        /** Theme Include */
        $cache = $this->getIncluderClass('Theme', 'Theme', array());
        if ($cache === true) {
            return $this->rendered_output;
        }

        /** Body */
        $this->final_indicator = false;

        $this->onBeforeParseEvent();

        $this->renderLoop();

        /** Head */
        $this->final_indicator = true;
        $this->sequence = $this->final;
        $this->exclude_until_final = array();

        $this->onBeforeParseHeadEvent();

        $this->renderLoop();

        /** Rendering is complete */
        $this->onAfterParseEvent();

        return $this->rendered_output;
    }

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  mixed
     * @since   1.0
     */
    protected function get($key = null, $default = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->parameter_property_array)) {
        } else {
            throw new \OutOfRangeException('Theme Service: is attempting to get value for unknown key: ' . $key);
        }

        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }
        $this->parameters[$key] = $default;
        return $this->parameters[$key];
    }

    /**
     * Set the value of a specified key
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed
     * @since   1.0
     */
    protected function set($key, $value = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->parameter_property_array)) {
        } else {
            throw new \OutOfRangeException('Theme Service: is attempting to set value for unknown key: ' . $key);
        }

        $this->parameters[$key] = $value;
        return $this->parameters[$key];
    }

    /**
     * renderLoop is initiated twice:
     *
     * 1. Renders Document Body
     *
     *  - Theme output rendered in renderTheme is parsed for <include:type/> statements
     *  - Control passed to Includer Type Class which determines parameters for processing and passes
     *      control to the MVC for rendering output
     *  - Process is recursive until no more includes found
     *
     * 2. Renders Document Head
     *
     *  - Same process using a new set of defined <include:type/> statements for the document head
     *
     *  Since rendered output is parsed until no more <include:type/> statements are discovered, the
     *  STOP_LOOP value is a precaution against an endless loop in the event a view includes itself.
     *
     * @return  void
     * @since   1.0
     */
    protected function renderLoop()
    {
        $complete = false;
        $loop = 0;
        while ($complete === false) {

            $loop++;

            $this->include_request = array();

            $this->getIncludeRequests();

            if (count($this->include_request) == 0) {
                break;
            }

            $this->processIncludeRequests();

            if ($loop > STOP_LOOP) {
                break;
            }
            continue;
        }

        return;
    }

    /**
     * Parses the rendered output, looking for <include:type/> statements.
     *
     * Note: Attribute pairs may NOT contain spaces. Escape, if needed: ex. value=This&nbsp;thing
     *  To include multiple values, separate with a comma: ex. class=one,two,three
     *
     * @return  array
     * @since   1.0
     */
    protected function getIncludeRequests()
    {
        $matches = array();
        $this->include_request = array();
        $i = 0;

        preg_match_all('#<include:(.*)\/>#iU', $this->rendered_output, $matches);

        $skipped_final_include_type = false;

        if (count($matches) == 0) {
            return;
        }

        foreach ($matches[1] as $includeStatement) {
            $include_type = '';

            $parts = array();
            $temp = explode(' ', $includeStatement);
            if (count($temp) > 0) {
                foreach ($temp as $item) {
                    if (trim($item) == '') {
                    } else {
                        $parts[] = $item;
                    }
                }
            }

            $countAttributes = 0;

            if (count($parts) > 0) {

                $include_type = '';
                foreach ($parts as $part) {

                    /** 1st part is the Includer Command */
                    if ($include_type == '') {
                        $include_type = $part;

                        /** Exclude the final include types (will be empty during document head processing) */
                        if (in_array($part, $this->exclude_until_final)) {
                            $skipped_final_include_type = true;

                        } else {
                            $this->include_request[$i]['name'] = $include_type;
                            $this->include_request[$i]['replace'] = $includeStatement;
                            $skipped_final_include_type = false;
                        }

                    } elseif ($skipped_final_include_type === false) {

                        /** Includer Attributes */
                        $attributes = str_replace('"', '', $part);

                        if (trim($attributes) == '') {
                        } else {

                            /** Associative array of attributes */
                            $pair = array();
                            $pair = explode('=', $attributes);

                            $countAttributes++;

                            $this->include_request[$i]['attributes'][$pair[0]] = $pair[1];
                        }
                    }
                }

                if ($skipped_final_include_type === false) {

                    /** Add empty array entry when no attributes */
                    if ($countAttributes == 0) {
                        $this->include_request[$i]['attributes'] = array();
                    }

                    /** Increment count for next */
                    $i++;
                }
            }
        }

        ob_start();
        echo 'Theme Service: processIncludeRequestsRequests found the following includes:<br />';
        foreach ($this->include_request as $request) {
            echo $request['replace'] . '<br />';
        }
        $includeDisplay = ob_get_contents();
        ob_end_clean();

        Services::Profiler()->set($includeDisplay, PROFILER_RENDERING);

        return;
    }

    /**
     * Instantiate Theme Includer Class and pass in attributes for rendering of include
     *  and replaces the <include:type/> with output results in $this->rendered_output
     *
     * @return  void
     * @since   1.0
     */
    protected function processIncludeRequests()
    {
        $replace = array();
        $with = array();

        foreach ($this->sequence as $sequence) {

            if (stripos($sequence, ':')) {
                $include_name = substr($sequence, 0, strpos($sequence, ':'));
                $include_type = substr($sequence, strpos($sequence, ':') + 1, 999);
            } else {
                $include_name = $sequence;
                $include_type = $sequence;
            }

            for ($i = 0; $i < count($this->include_request); $i++) {

                $parsed = $this->include_request[$i];

                if ($include_name == $parsed['name']) {

                    if (isset($parsed['attributes'])) {
                        $attributes = $parsed['attributes'];
                    } else {
                        $attributes = array();
                    }

                    $replace[] = "<include:" . $parsed['replace'] . "/>";

                    $output = $this->getIncluderClass ($include_type, $include_name, $attributes);

                    $with[] = $output;
                }
            }
        }

        $this->rendered_output = str_replace($replace, $with, $this->rendered_output);

        return;
    }

    /**
     * Pass control to Includer Class to render <include:type/>
     *
     * @param   $include_type
     * @param   $include_name
     * @param   $attributes
     *
     * @return  string
     * @since   1.0
     * @throws  \Exception
     */
    protected function getIncluderClass($include_type, $include_name, $attributes)
    {
        if (defined(PROFILER_ON)) {
            Services::Profiler()->set('Theme Service: getIncluderClass ' .
                ' include_type: ' . $include_type .
                ' include_name: ' . $include_name .
                ' attributes: ' . implode(' ', $attributes) .
                PROFILER_RENDERING);
        }

        $class = $this->class_array[ucfirst(strtolower($include_type)) . 'Includer'];

        if (class_exists($class)) {
            $rc = new $class ($this->parameter_property_array, $this->parameters, $include_type, $include_name, $attributes);

        } else {
            throw new \Exception('Theme Service: Includer Failed Instantiating Class' . $class);
        }

        $rendered_output = trim($rc->process());

        if (defined(PROFILER_ON)) {
            Services::Profiler()->set('Theme Service: Rendered ' . $rendered_output, PROFILER_RENDERING, VERBOSE);
        }

        return $rendered_output;
    }

    /**
     * Schedule Event onBeforeParseEvent
     *
     * Event runs before any output is rendered (including the Theme file),
     *
     *  The include and exclude values that will be processed by the parsing/rendering process are available
     *  to the plugin, as are the parameters for the primary resource, theme, page, template, etc., and
     *  and the Primary Data Registry, all of which can be modified or used by plugins.
     *
     * Page Type Plugins are scheduled for this event (List, Item, Edit, and the Menu Item Page Types)
     *
     * In general, this event is good for building data that is relevant to the entire page,
     *  like the Application Plugin which sets Page Registry data (ex. current and home URL, menu, metadata, etc.)
     *
     * @return  void
     * @since   1.0
     */
    protected function onBeforeParseEvent()
    {
        $query_results = $this->triggerEvent('onBeforeParse', Services::Registry()->get(PRIMARY_LITERAL, DATA_LITERAL));

        Services::Registry()->delete(PRIMARY_LITERAL, DATA_LITERAL);
        Services::Registry()->createRegistry(PRIMARY_LITERAL);
        Services::Registry()->set(PRIMARY_LITERAL, DATA_LITERAL, $query_results);

        return Services::Registry()->get(PRIMARY_LITERAL, DATA_LITERAL);
    }

    /**
     * Schedule Event onBeforeParseHeadEvent
     *
     * Event runs after the body of the document is fully developed.
     *
     * The include and exclude values that will be processed by the parsing/rendering process are available
     *  to the plugin. All metadata, document links, and assets have been defined and can be modified by plugins.
     *  Rendered content for the document body is available to event plugins.
     *
     * @return  void
     * @since   1.0
     */
    protected function onBeforeParseHeadEvent()
    {
        return $this->triggerEvent('onBeforeParseHead', array());
    }

    /**
     * Schedule Event onAfterParseEvent Event
     *
     * Event runs after the entire document has been rendered. The rendered content is available to event plugins.
     *
     * @return  void
     * @since   1.0
     */
    protected function onAfterParseEvent()
    {
        return $this->triggerEvent('onAfterParse', array());
    }

    /**
     * Common Method for all Theme Service Events
     *
     * @param   string  $event_name
     * @param   string  $query_results
     *
     * @return  array|null
     * @since   1.0
     */
    protected function triggerEvent($event_name, $query_results = null)
    {
        if ($query_results === null) {
            $query_results = array();
        }

        $arguments = array(
            'model' => null,
            'model_registry' => Services::Registry()->get($this->get('model_registry_name')),
            'parameters' => $this->parameters,
            'parameter_property_array' => $this->parameter_property_array,
            'query_results' => $query_results,
            'row' => array(),
            'rendered_output' => $this->rendered_output,
            'class_array' => $this->class_array,
            'include_parse_sequence' => $this->sequence,
            'include_parse_exclude_until_final' => $this->exclude_until_final
        );

        $arguments = Services::Event()->scheduleEvent($event_name, $arguments, $this->getPluginList());

        echo $event_name .'<br />';
        echo '<pre>';
        var_dump($arguments);
        echo '</pre>';
        $arguments = Services::Event()->scheduleEvent(
            $event_name,
            $arguments,
            $this->getPluginList()
        );

        if (isset($arguments['class_array'])) {
            $this->parameters = $arguments['class_array'];
        }

        if (isset($arguments['parameters'])) {
            $this->parameters = $arguments['parameters'];
        }

        if (isset($arguments['parameter_properties_array'])) {
            $this->parameters = $arguments['parameter_properties_array'];
        }

        if (isset($this->parameters['model_registry_name'])) {

            $model_registry_name = $this->parameters['model_registry_name'];

            if (isset($arguments['model_registry'])) {
                Services::Registry()->delete($model_registry_name);
                Services::Registry()->createRegistry($this->get('model_registry_name'));
                Services::Registry()->loadArray($this->get('model_registry_name'), $arguments['model_registry']);
            }
        }

        if (isset($arguments['query_results'])) {
           $query_results = $arguments['query_results'];
        }

        if (isset($arguments['include_parse_sequence'])) {
            $this->sequence = $arguments['include_parse_sequence'];
        }

        if (isset($arguments['include_parse_exclude_until_final'])) {
            $this->exclude_until_final = $arguments['include_parse_exclude_until_final'];
        } else {
            $this->exclude_until_final = array();
        }

        if (isset($arguments['rendered_output'])) {
            $this->rendered_output = $arguments['rendered_output'];
        }

        return $query_results;
    }

    /**
     * Get the list of potential plugins identified with this model registry
     *
     * @return  array
     * @since   1.0
     */
    protected function getPluginList()
    {
        $model_registry_name = $this->get('model_registry_name');

        $plugins = array();

        $modelPlugins = array();

        if ((int) Services::Registry()->get($model_registry_name, 'process_plugins') > 0) {
            $modelPlugins = Services::Registry()->get($model_registry_name, 'plugins');

            if (is_array($modelPlugins)) {
            } else {
                $modelPlugins = array();
            }
        }

        $templatePlugins = array();

        if ((int)Services::Registry()->get($model_registry_name, 'process_template_plugins') > 0) {
            $name = Services::Registry()->get($model_registry_name, 'template_view_path_node');
            if ($name == '') {
            } else {
                $templatePlugins = Services::Registry()->get(ucfirst(strtolower($name)).'Templates', 'plugins');

                if (is_array($templatePlugins)) {
                } else {
                    $templatePlugins = array();
                }
            }
        }

        $plugins = array_merge($modelPlugins, $templatePlugins);
        if (is_array($plugins)) {
        } else {
            $plugins = array();
        }

        $page_type = $this->get('catalog_page_type');
        if ($page_type == '') {
        } else {
            $plugins[] = 'Pagetype' . strtolower($page_type);
        }

        $template = $this->get('template_view_path_node');
        if ($template == '') {
        } else {
            $plugins[] = $template;
        }

        if ((int)Services::Registry()->get($model_registry_name, 'process_plugins') == 0
            && count($plugins) == 0) {
            $this->get('plugins', array());
            return;
        }

        $plugins[] = 'Application';

        return $plugins;
    }
}
