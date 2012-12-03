<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Parse;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Parse
 *
 * @package     Molajo
 * @subpackage  Parse
 * @since       1.0
 */
Class ParseService
{
    /**
     * System defined order for processing includes stored in the sequence.xml file
     *
     * @var    array
     * @since  1.0
     */
    protected $sequence = array();

    /**
     * Final include types -- used to indicate it is the final iteration of parsing
     *
     * @var    array
     * @since  1.0
     */
    protected $final = array();

    /**
     * Exclude from parsing for all iterations except the final processing
     *
     * @var    array
     * @since  1.0
     */
    protected $exclude_until_final = array();

    /**
     * Indicator of final processing for includes
     *
     * @var    boolean
     * @since  1.0
     */
    protected $final_indicator = false;

    /**
     * Include Statement literals to be extracted from the theme (initially) and rendered output
     *
     * @var    array
     * @since  1.0
     */
    protected $include_request = array();

    /**
     * Accumulated rendered output from parsing, includer, MVC process - repeatedly parsed until no more includes found
     *
     * @var    array
     * @since  1.0
     */
    protected $rendered_output = null;

    /**
     * process
     *
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
        Services::Profiler()->set('Parse Started', PROFILER_RENDERING);

        $overrideIncludesPageXML = Services::Registry()->get(OVERRIDE_LITERAL, 'parse_sequence', false);
        $overrideIncludesFinalXML = Services::Registry()->get(OVERRIDE_LITERAL, 'parse_final', false);

        if ($overrideIncludesPageXML === false) {
            $this->sequence = Services::Configuration()->getFile(PARSE_LITERAL, 'Parse_sequence');
        } else {
            $this->sequence = $overrideIncludesPageXML;
        }

        if ($overrideIncludesFinalXML === false) {
            $hold_final = Services::Configuration()->getFile(PARSE_LITERAL, 'Parse_final');
        } else {
            $hold_final = $overrideIncludesFinalXML;
        }
        $this->exclude_until_final = $hold_final;
        $this->final = false;

        if (file_exists(Services::Registry()->get(PARAMETERS_LITERAL, 'theme_path_include'))) {
        } else {
            Services::Error()->set(500, 'Theme not found');
            return false;
        }

        $this->onBeforeParseEvent();

        foreach ($this->sequence->include as $next) {
            $this->sequence[] = (string)$next;
        }

        foreach ($this->final->include as $next) {
            $this->sequence = (string)$next;
            $this->final[] = (string)$next;

            if (stripos($this->sequence, ':')) {
                $includeName = substr($this->sequence, 0, strpos($this->sequence, ':'));
            } else {
                $includeName = $this->sequence;
            }

            $this->exclude_until_final[] = $includeName;
        }

        $this->final_indicator = false;

        /** Save Route Parameters */
        Services::Registry()->createRegistry(ROUTE_PARAMETERS_LITERAL);
        Services::Registry()->copy(PARAMETERS_LITERAL, ROUTE_PARAMETERS_LITERAL);

        $this->renderLoop();

        Services::Registry()->delete(PARAMETERS_LITERAL);
        Services::Registry()->createRegistry(PARAMETERS_LITERAL);
        Services::Registry()->copy(ROUTE_PARAMETERS_LITERAL, PARAMETERS_LITERAL);
        $this->sequence = array();
        $this->final = array();

        $this->onAfterParsebodyEvent();

        $this->sequence = $this->hold_final;
        $this->final = array();
        $this->exclude_until_final = array();

        Services::Registry()->delete(PARAMETERS_LITERAL);
        Services::Registry()->createRegistry(PARAMETERS_LITERAL);
        Services::Registry()->copy(ROUTE_PARAMETERS_LITERAL, PARAMETERS_LITERAL);

        $class = 'Molajo\\Includer\\ThemeIncluder';

        if (class_exists($class)) {
            $rc = new $class (THEME_LITERAL);
            $results = $rc->process();
        } else {
            throw new \Exception('Parse: Instantiating ThemeIncluder Class failed');
        }

        $this->onBeforeDocumentheadEvent();

        $this->renderLoop();

        $this->onAfterDocumentheadEvent();

        Services::Registry()->delete(PARAMETERS_LITERAL);
        Services::Registry()->createRegistry(PARAMETERS_LITERAL);
        Services::Registry()->copy(ROUTE_PARAMETERS_LITERAL, PARAMETERS_LITERAL);

        $this->onAfterParseEvent();

        return $this->rendered_output;
    }

    /**
     * renderLoop
     *
     * 1. Renders Document Body
     *
     *  - Initiates by including the Theme and Page View
     *  - Parses output, looking for set of defined <include:type statements
     *  - Passes control to Includer type and captures rendered output
     *  - After all <include:type statements have been processed, rendered output is parsed for new statements
     *  - This loop continues where new <include:type statements are processed until none are found.
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
        if ($this->rendered_output === null) {

            $first = true;

            Services::Profiler()->set(
                'ParseService renderLoop Parse Body using Theme:'
                    . Services::Registry()->get(PARAMETERS_LITERAL, 'theme_path_include')
                    . ' and Page View: '
                    . Services::Registry()->get(PARAMETERS_LITERAL, 'page_view_path_include'),
                PROFILER_RENDERING
            );

            ob_start();
            require Services::Registry()->get(PARAMETERS_LITERAL, 'theme_path_include');
            $this->rendered_output = ob_get_contents();
            ob_end_clean();

        } else {

            $first = false;
            $final = true;
            Services::Profiler()->set(
                'ParseService renderLoop Parse Document Head ',
                PROFILER_RENDERING
            );
        }

        $complete = false;
        $loop = 0;
        while ($complete === false) {

            $loop++;
            $this->include_request = array();
            $this->parseIncludeRequests();

            if (count($this->include_request) == 0) {
                break;
            }

            $this->rendered_output = $this->callIncluder($first);

            $first = false;

            /**
             *    Rendered output will be parsed until no more <include /> statements are discovered.
             *    An endless loop could be created if frontend developers include a template that
             *    includes the same template. This is a stop-gap measure to prevent that from happening.
             */
            if ($loop > STOP_LOOP) {
                break;
            }
            continue;
        }

        return;
    }

    /**
     * Parse the theme (first) and then rendered output (subsequent calls) for include statements
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
            $includerType = '';

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

                $includerType = '';
                foreach ($parts as $part) {

                    /** 1st part is the Includer Command */
                    if ($includerType == '') {
                        $includerType = $part;

                        /** Exclude the final include types (emptied before document head parsing) */
                        if (in_array($part, $this->exclude_until_final)) {
                            $skipped_final_include_type = true;

                        } else {
                            $this->include_request[$i]['name'] = $includerType;
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
        echo 'ParseService parseIncludeRequests found the following includes:<br />';
        foreach ($this->include_request as $request) {
            echo $request['replace'] . '<br />';
        }
        $includeDisplay = ob_get_contents();
        ob_end_clean();

        Services::Profiler()->set($includeDisplay, PROFILER_RENDERING);

        return;
    }

    /**
     * Invoke extension-specific includer for include statement
     *
     * @param   bool    $first
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
                $includeName = substr($sequence, 0, strpos($sequence, ':'));
                $includerType = substr($sequence, strpos($sequence, ':') + 1, 999);
            } else {
                $includeName = $sequence;
                $includerType = $sequence;
            }

            for ($i = 0; $i < count($this->include_request); $i++) {

                $parsedRequests = $this->include_request[$i];

                if ($includeName == $parsedRequests['name']) {

                    if (isset($parsedRequests['attributes'])) {
                        $attributes = $parsedRequests['attributes'];
                    } else {
                        $attributes = array();
                    }

                    $replace[] = "<include:" . $parsedRequests['replace'] . "/>";

                    Services::Registry()->deleteRegistry(PARAMETERS_LITERAL);
                    Services::Registry()->createRegistry(PARAMETERS_LITERAL);

                    if ($includeName == 'request') {
                        Services::Registry()->copy(ROUTE_PARAMETERS_LITERAL, PARAMETERS_LITERAL);
                        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_primary', true);
                    } else {
                        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_primary', false);
                    }

                    $class = 'Molajo\\Includer\\';
                    $class .= ucfirst($includerType) . 'Includer';

                    if (class_exists($class)) {
                        $rc = new $class ($includerType, $includeName);

                    } else {
                        Services::Profiler()->set(
                            'ParseService callIncluder failed instantiating class ' . $class,
                            PROFILER_RENDERING
                        );
                        throw new \Exception('Parse: Includer Failed Instantiating Class' . $class);
                    }

                    ob_start();
                    echo 'Parse Includer invoking class ' . $class . ' Attributes: ' . '<br />';
                    echo '<pre>';
                    var_dump($attributes);
                    echo '</pre>';
                    $includeDisplay = ob_get_contents();
                    ob_end_clean();

                    echo '<br />';
                    echo $includeDisplay;
                    echo '<br />';

                    $output = trim($rc->process($attributes));
                    Services::Profiler()->set($includeDisplay, PROFILER_RENDERING);

                    echo '<br />';
                    echo $output;
                    echo '<br />';

                    Services::Profiler()->set(
                        'ParseService->callIncluder rendered output ' . $output,
                        PROFILER_RENDERING,
                        VERBOSE
                    );

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
     * @return  void
     * @since   1.0
     */
    protected function onBeforeParseEvent()
    {
        return $this->triggerEvent('onBeforeParse');
    }

    /**
     * Schedule Event onAfterParseBodyEvent
     *
     * @return  void
     * @since   1.0
     */
    protected function onAfterParseBodyEvent()
    {
        return $this->triggerEvent('onAfterParseBody');
    }

    /**
     * Schedule Event onBeforeDocumentHeadEvent
     *
     * @return  void
     * @since   1.0
     */
    protected function onBeforeDocumentHeadEvent()
    {
        return $this->triggerEvent('onBeforeDocumentHead');
    }

    /**
     * Schedule Event onAfterDocumentHeadEvent
     *
     * @return  void
     * @since   1.0
     */
    protected function onAfterDocumentHeadEvent()
    {
        return $this->triggerEvent('onAfterDocumentHead');
    }

    /**
     * Schedule Event onAfterParseEvent Event
     *
     * @return  void
     * @since   1.0
     */
    protected function onAfterParseEvent()
    {
        return $this->triggerEvent('onAfterParse');
    }

    /**
     * Common Method for all Parse Events
     *
     * @param   string  $event_name
     *
     * @return  void
     * @since   1.0
     */
    protected function triggerEvent($eventName)
    {
        $model_registry = ucfirst(strtolower(Services::Registry()->get(PARAMETERS_LITERAL, 'model_name')))
            . ucfirst(strtolower(Services::Registry()->get(PARAMETERS_LITERAL, 'model_type')));

        $arguments = array(
            'model' => null,
            'model_registry' => $model_registry,
            PARAMETERS_LITERAL => Services::Registry()->get(PARAMETERS_LITERAL),
            'query_results' => array(),
            'data' => array(),
            'rendered_output' => $this->rendered_output,
            'include_parse_sequence' => $this->sequence,
            'include_parse_exclude_until_final' => $this->exclude_until_final
        );

echo '<pre>';
var_dump($arguments);
echo '</pre>';
die;
        $arguments = Services::Event()->scheduleEvent($eventName, $arguments, array());

        if (isset($arguments['include_parse_sequence'])) {
            $this->sequence = $arguments['include_parse_sequence'];
        }

        if (isset($arguments['include_parse_exclude_until_final'])) {
            $this->exclude_until_final = $arguments['include_parse_exclude_until_final'];
        }

        if (isset($arguments[PARAMETERS_LITERAL])) {
            Services::Registry()->delete(PARAMETERS_LITERAL);
            Services::Registry()->createRegistry(PARAMETERS_LITERAL);
            Services::Registry()->loadArray(PARAMETERS_LITERAL, $arguments[PARAMETERS_LITERAL]);
            Services::Registry()->sort(PARAMETERS_LITERAL);
        }

        if (isset($arguments['rendered_output'])) {
            $this->rendered_output = $arguments['rendered_output'];
        }

        return;
    }
}
