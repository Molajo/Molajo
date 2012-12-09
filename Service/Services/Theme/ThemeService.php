<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Theme;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Theme
 *
 * @package     Molajo
 * @subpackage  Theme
 * @since       1.0
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
     * Load sequence.xml file contents into array for determining processing order
     *
     * Invoke Theme Includer to load page metadata, and theme language and media resources
     *
     * Retrieve Theme and Page View to initiate the iterative process of parsing rendered output
     * for <include:type/> statements and then looping through all include requests
     *
     * When no more <include:type/> statements are found in the rendered output,
     * process sets the Responder body and completes
     *
     * @return  string
     * @since   1.0
     */
    public function process()
    {
        Services::Profiler()->set('Theme: Started', PROFILER_RENDERING);

        $overrideIncludesPageXML = Services::Registry()->get(OVERRIDE_LITERAL, 'parse_sequence', false);
        $overrideIncludesFinalXML = Services::Registry()->get(OVERRIDE_LITERAL, 'parse_final', false);

        if ($overrideIncludesPageXML === false) {
            $sequence = Services::Configuration()->getFile(PARSE_LITERAL, 'Parse_sequence');
        } else {
            $sequence = $overrideIncludesPageXML;
        }

        foreach ($sequence->include as $next) {
            $this->sequence[] = (string)$next;
        }

        if ($overrideIncludesFinalXML === false) {
            $final = Services::Configuration()->getFile(PARSE_LITERAL, 'Parse_final');
        } else {
            $final = $overrideIncludesFinalXML;
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

        $this->final_indicator = false;

        $this->getResource();
        if ($this->rendered_output === false) {
        } else {
            return $this->rendered_output;
        }

        Services::Registry()->createRegistry(ROUTE_PARAMETERS_LITERAL);
        Services::Registry()->copy(PARAMETERS_LITERAL, ROUTE_PARAMETERS_LITERAL);

        $this->renderLoop();

        $this->sequence = $this->final;
        $this->exclude_until_final = array();
        $this->final_indicator = true;

        Services::Registry()->delete(PARAMETERS_LITERAL);
        Services::Registry()->createRegistry(PARAMETERS_LITERAL);
        Services::Registry()->copy(ROUTE_PARAMETERS_LITERAL, PARAMETERS_LITERAL);

        $this->onBeforeParseHeadEvent();

        $this->renderLoop();

        $this->onAfterParseEvent();

        return $this->rendered_output;
    }

    /**
     * Before parsing, determine rendering instructions for the routed requested
     *  and render the Theme which contains <include statements to be parsed
     *  then used to invoke includers and render output
     *
     * @returns  void
     * @since    1.0
     * @throws   \Exception
     */
    protected function getResource()
    {
        Services::Profiler()->set('Theme: Resource Includer started', PROFILER_RENDERING);

        //todo - how to override? should includers be pre-defined like plugins?
        $class = 'Molajo\\Service\\Services\\Theme\\Includer\\ResourceIncluder';
        if (class_exists($class)) {
            $rc = new $class ('Resource', 'Resource');

        } else {
            throw new \Exception('Theme: Includer Failed Instantiating Class' . $class);
        }

        $this->rendered_output = $rc->getPrimaryData();

        if ($this->rendered_output === false) {
        } else {
            Services::Profiler()->set('Theme: Page cache returned from Resource Includer', PROFILER_RENDERING);
            return;
        }

        $this->onBeforeParseEvent();

        $this->rendered_output = $rc->process(array());

        ob_start();
        Services::Profiler()->set('Theme: Resource ' . $this->rendered_output, PROFILER_RENDERING, VERBOSE);
        echo $this->rendered_output;
        $includeDisplay = ob_get_contents();
        ob_end_clean();

        return;
    }

    /**
     * renderLoop is executed two times:
     *
     * 1. Renders Document Body
     *
     *  - Parses Theme output rendered in getResource, looking for set of <include:type statements
     *  - Passes control to Theme Includer Type Class, capturing the rendered output from processing
     *  - Process is recursive until no more includes found
     *
     * 2. Renders Document Head
     *
     *  - Same process as is used for the document body with new set of defined <include:type statements
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

            $this->parseIncludeRequests();

            if (count($this->include_request) == 0) {
                break;
            }

            $this->callIncluder();

            /**
             *  Rendered output is parsed from $this->rendered_output until <include /> statements are
             *  no longer discovered. The STOP_LOOP value is a provision to ensure an endless loop does
             *  not result from including
             *  a view within itself.
             */
            if ($loop > STOP_LOOP) {
                break;
            }
            continue;
        }

        return;
    }

    /**
     * Parse rendered output for include statements
     *
     * Note: Attribute pairs may NOT contain spaces. To include multiple values, separate with a comma:
     *  ex. class=one,two,three
     *
     * @return  array
     * @since   1.0
     */
    protected function parseIncludeRequests()
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
        echo 'Theme: parseIncludeRequests found the following includes:<br />';
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
     *
     * @return  void
     * @since   1.0
     */
    protected function callIncluder($first = false)
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

                    Services::Registry()->deleteRegistry(PARAMETERS_LITERAL);
                    Services::Registry()->createRegistry(PARAMETERS_LITERAL);
//todo figureout overrides
                    $class = 'Molajo\\Service\\Services\\Theme\\Includer\\';
                    $class .= ucfirst($include_type) . 'Includer';

                    if (class_exists($class)) {
                        $rc = new $class ($include_type, $include_name);

                    } else {
                        Services::Profiler()->set(
                            'Theme: callIncluder failed instantiating class '
                                . $class,
                            PROFILER_RENDERING
                        );
                        throw new \Exception('Theme: Includer Failed Instantiating Class' . $class);
                    }

                    ob_start();
                    echo 'Theme: Includer Class ' . $class . ' Attributes: ' . '<br />';
                    echo '<pre>';
                    var_dump($attributes);
                    echo '</pre>';
                    $includeDisplay = ob_get_contents();
                    ob_end_clean();

                    Services::Profiler()->set($includeDisplay, PROFILER_RENDERING);
                    echo $includeDisplay;
                    $output = trim($rc->process($attributes));
                    Services::Profiler()->set('Theme: Rendered ' . $output, PROFILER_RENDERING);
                    echo $output;

                    $with[] = $output;
                }
            }
        }

        $this->rendered_output = str_replace($replace, $with, $this->rendered_output);

        return;
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

        return Services::Registry()->get(PRIMARY_LITERAL, DATA_LITERAL, $query_results);
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
     *
     * @return  void
     * @since   1.0
     */
    protected function triggerEvent($eventName, $query_results = null)
    {
        $model_registry = ucfirst(strtolower(Services::Registry()->get(PARAMETERS_LITERAL, 'model_name')))
            . ucfirst(strtolower(Services::Registry()->get(PARAMETERS_LITERAL, 'model_type')));

        $arguments = array(
            'model' => null,
            'model_registry' => Services::Registry()->get($model_registry),
            'parameters' => Services::Registry()->get(PARAMETERS_LITERAL),
            'query_results' => $query_results,
            'row' => array(),
            'rendered_output' => $this->rendered_output,
            'include_parse_sequence' => $this->sequence,
            'include_parse_exclude_until_final' => $this->exclude_until_final
        );

        $arguments = Services::Event()->scheduleEvent($eventName, $arguments, array());

        if (isset($arguments['model_registry'])) {
            Services::Registry()->delete($model_registry);
            Services::Registry()->createRegistry($model_registry);
            Services::Registry()->loadArray($model_registry, $arguments['model_registry']);
        }

        if (isset($arguments[PARAMETERS_LITERAL])) {
            Services::Registry()->delete(PARAMETERS_LITERAL);
            Services::Registry()->createRegistry(PARAMETERS_LITERAL);
            Services::Registry()->loadArray(PARAMETERS_LITERAL, $arguments[PARAMETERS_LITERAL]);
            Services::Registry()->sort(PARAMETERS_LITERAL);
        }

        if (isset($arguments['query_results'])) {
            Services::Registry()->delete(PRIMARY_LITERAL, DATA_LITERAL);
            Services::Registry()->createRegistry(PRIMARY_LITERAL, DATA_LITERAL);
            Services::Registry()->loadArray(PRIMARY_LITERAL, DATA_LITERAL, $arguments[$query_results]);
        }

        if (isset($arguments['rendered_output'])) {
            $this->rendered_output = $arguments['rendered_output'];
        }

        if (isset($arguments['include_parse_sequence'])) {
            $this->sequence = $arguments['include_parse_sequence'];
        }

        if (isset($arguments['include_parse_exclude_until_final'])) {
            $this->exclude_until_final = $arguments['include_parse_exclude_until_final'];
        }

        return $query_results;
    }
}
