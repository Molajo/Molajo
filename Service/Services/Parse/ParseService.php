<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
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
     * Final include types -- used to for final iteration of parsing
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
        Services::Profiler()->set('ParseService->process Started', PROFILER_RENDERING);

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
                $includeName = substr($sequence, 0, strpos($sequence, ':'));
            } else {
                $includeName = $sequence;
            }

            $this->exclude_until_final[] = $includeName;
        }

        $this->final_indicator = false;

        if (file_exists(Services::Registry()->get(PARAMETERS_LITERAL, 'theme_path_include'))) {
        } else {
            Services::Error()->set(500, 'Theme not found');
            return false;
        }

        if (Services::Registry()->get(PARAMETERS_LITERAL, ERROR_STATUS_LITERAL, 0) == 1) {
        } else {
            //todo - pass in lists of includes to the plugins for possible change
            $this->onBeforeParseEvent();
        }

        /** Save Route Parameters */
        Services::Registry()->createRegistry(ROUTE_PARAMETERS_LITERAL);
        Services::Registry()->copy(PARAMETERS_LITERAL, ROUTE_PARAMETERS_LITERAL);

        $renderedOutput = $this->renderLoop();

        if (Services::Registry()->get(PARAMETERS_LITERAL, ERROR_STATUS_LITERAL, 0) == 1) {
        } else {
            Services::Registry()->delete(PARAMETERS_LITERAL);
            Services::Registry()->createRegistry(PARAMETERS_LITERAL);
            Services::Registry()->copy(ROUTE_PARAMETERS_LITERAL, PARAMETERS_LITERAL);

            $renderedOutput = $this->onAfterParsebodyEvent($renderedOutput);
        }

        $this->sequence = $this->final;
        $this->exclude_until_final = array();

        Services::Registry()->delete(PARAMETERS_LITERAL);
        Services::Registry()->createRegistry(PARAMETERS_LITERAL);
        Services::Registry()->copy(ROUTE_PARAMETERS_LITERAL, PARAMETERS_LITERAL);

        $bodyOutput = $renderedOutput;

        $class = 'Molajo\\Includer\\ThemeIncluder';

        if (class_exists($class)) {
            $rc = new $class (THEME_LITERAL);
            $results = $rc->process();
        } else {
            throw new \Exception('Parse: Instantiating ThemeIncluder Class failed');
        }

        if (Services::Registry()->get(PARAMETERS_LITERAL, ERROR_STATUS_LITERAL, 0) == 1) {
        } else {
            $renderedOutput = $this->onBeforeDocumentheadEvent($renderedOutput);
        }

        $renderedOutput = $this->renderLoop($renderedOutput);

        if (Services::Registry()->get(PARAMETERS_LITERAL, ERROR_STATUS_LITERAL, 0) == 1) {
        } else {
            $renderedOutput = $this->onAfterDocumentheadEvent($renderedOutput);
        }

        if (Services::Registry()->get(PARAMETERS_LITERAL, ERROR_STATUS_LITERAL, 0) == 1) {
        } else {
            Services::Registry()->delete(PARAMETERS_LITERAL);
            Services::Registry()->createRegistry(PARAMETERS_LITERAL);
            Services::Registry()->copy(ROUTE_PARAMETERS_LITERAL, PARAMETERS_LITERAL);

            $renderedOutput = $this->onAfterParseEvent($renderedOutput);
        }

        return $renderedOutput;
    }

    /**
     * renderLoop
     *
     * 1. Renders Document Body
     *
     * Initiates by including the Theme and Page View
     * Parses output, looking for set of defined <include:type statements
     * Passes control to Includer type and captures rendered output
     * After all <include:type statements have been processed, rendered output is parsed for new statements
     * This loop continues where new <include:type statements are processed until none are found.
     *
     * 2. Renders Document Head
     *
     * Same process as is used for the document body with new set of defined <include:type statements
     *
     * @param   string  $renderedOutput
     *
     * @return  string  $renderedOutput  Rendered output for the Response Head and Body
     * @since   1.0
     */
    protected function renderLoop($renderedOutput = null)
    {
        if ($renderedOutput == null) {
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
            $renderedOutput = ob_get_contents();
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
            $this->parseIncludeRequests($renderedOutput);

            if (count($this->include_request) == 0) {
                break;
            }

            $renderedOutput = $this->callIncluder($first, $renderedOutput);

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

        return $renderedOutput;
    }

    /**
     * Parse the theme (first) and then rendered output (subsequent calls) for include statements
     *
     * Note: Attribute pairs may NOT contain spaces. To include multiple values, separate with a comma:
     *  ex. class=one,two,three
     *
     * @param   string  $renderedOutput
     *
     * @return  array
     * @since   1.0
     */
    protected function parseIncludeRequests($renderedOutput)
    {
        $matches = array();
        $this->include_request = array();
        $i = 0;

        preg_match_all('#<include:(.*)\/>#iU', $renderedOutput, $matches);

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
     * @param   string  $renderedOutput
     *
     * @return  string  rendered output
     * @since   1.0
     */
    protected function callIncluder($first = false, $renderedOutput)
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

        $renderedOutput = str_replace($replace, $with, $renderedOutput);

        return $renderedOutput;
    }

    /**
     * Schedule Event onBeforeParseEvent Event
     *
     * @return  boolean
     * @since   1.0
     */
    protected function onBeforeParseEvent()
    {
        Services::Profiler()->set('ParseService onBeforeParse', PROFILER_PLUGINS, VERBOSE);

        $arguments = array(
            'parameters' => Services::Registry()->get(PARAMETERS_LITERAL),
            'model_type' => Services::Registry()->get(PARAMETERS_LITERAL, 'model_type'),
            'model_name' => Services::Registry()->get(PARAMETERS_LITERAL, 'model_name'),
            'data' => array()
        );

        $arguments = Services::Event()->scheduleEvent('onBeforeParse', $arguments);

        if ($arguments === false) {
            Services::Registry()->set(PARAMETERS_LITERAL, ERROR_STATUS_LITERAL, 1);
            Services::Profiler()->set('ParseService onBeforeParsebody failed', PROFILER_PLUGINS);
            return false;
        }

        Services::Registry()->delete(PARAMETERS_LITERAL);
        Services::Registry()->createRegistry(PARAMETERS_LITERAL);
        Services::Registry()->loadArray(PARAMETERS_LITERAL, $arguments[strtolower(PARAMETERS_LITERAL)]);
        Services::Registry()->sort(PARAMETERS_LITERAL);

        return true;
    }

    /**
     * Schedule Event onAfterParseBody Event
     *
     * @param   string  $renderedOutput
     *
     * @return  string  rendered output
     * @since   1.0
     */
    protected function onAfterParsebodyEvent($renderedOutput)
    {
        Services::Profiler()->set('ParseService onAfterParsebody', PROFILER_PLUGINS, VERBOSE);

        $parameters = Services::Registry()->getArray(PARAMETERS_LITERAL);

        $arguments = array(
            'parameters' => $parameters,
            'rendered_output' => $renderedOutput
        );

        $arguments = Services::Event()->scheduleEvent('onAfterParsebody', $arguments);

        if ($arguments === false) {
            Services::Registry()->set(PARAMETERS_LITERAL, ERROR_STATUS_LITERAL, 1);
            Services::Profiler()->set('ParseService onAfterParsebody failed', PROFILER_PLUGINS);
            return false;
        }

        Services::Registry()->delete(PARAMETERS_LITERAL);
        Services::Registry()->loadArray(PARAMETERS_LITERAL, $arguments[strtolower(PARAMETERS_LITERAL)]);
        Services::Registry()->sort(PARAMETERS_LITERAL);

        $renderedOutput = $arguments['rendered_output'];

        return $renderedOutput;
    }

    /**
     * Schedule Event onBeforeDocumenthead Event
     *
     * @param   string  $renderedOutput
     *
     * @return  string  rendered output
     * @since   1.0
     */
    protected function onBeforeDocumentheadEvent($renderedOutput)
    {
        Services::Profiler()->set('ParseService onBeforeDocumenthead', PROFILER_PLUGINS, VERBOSE);

        $parameters = Services::Registry()->getArray(PARAMETERS_LITERAL);

        $arguments = array(
            'parameters' => $parameters,
            'rendered_output' => $renderedOutput
        );

        $arguments = Services::Event()->scheduleEvent('onBeforeDocumenthead', $arguments);

        if ($arguments === false) {
            Services::Registry()->set(PARAMETERS_LITERAL, ERROR_STATUS_LITERAL, 1);
            Services::Profiler()->set('ParseService onBeforeDocumenthead failed', PROFILER_PLUGINS);
            return false;
        }

        Services::Registry()->delete(PARAMETERS_LITERAL);
        Services::Registry()->loadArray(PARAMETERS_LITERAL, $arguments[strtolower(PARAMETERS_LITERAL)]);

        $renderedOutput = $arguments['rendered_output'];

        return $renderedOutput;
    }

    /**
     * Schedule Event onAfterDocumentheadEvent Event
     *
     * @return  boolean
     * @since   1.0
     */
    protected function onAfterDocumentheadEvent($renderedOutput)
    {
        Services::Profiler()->set('ParseService onAfterDocumenthead', PROFILER_PLUGINS, VERBOSE);

        $parameters = Services::Registry()->getArray(PARAMETERS_LITERAL);

        $arguments = array(
            'parameters' => $parameters,
            'rendered_output' => $renderedOutput
        );

        $arguments = Services::Event()->scheduleEvent('onAfterDocumenthead', $arguments);

        if ($arguments === false) {
            Services::Registry()->set(PARAMETERS_LITERAL, ERROR_STATUS_LITERAL, 1);
            Services::Profiler()->set('ParseService onAfterDocumenthead failed', PROFILER_PLUGINS);
            return false;
        }

        Services::Registry()->delete(PARAMETERS_LITERAL);
        Services::Registry()->loadArray(PARAMETERS_LITERAL, $arguments[strtolower(PARAMETERS_LITERAL)]);

        $renderedOutput = $arguments['rendered_output'];

        return $renderedOutput;
    }

    /**
     * Schedule Event onAfterParseEvent Event
     *
     * @return  boolean
     * @since   1.0
     */
    protected function onAfterParseEvent($renderedOutput)
    {
        Services::Profiler()->set('ParseService onAfterParse', PROFILER_PLUGINS, VERBOSE);

        $parameters = Services::Registry()->getArray(PARAMETERS_LITERAL);

        $arguments = array(
            'parameters' => $parameters,
            'rendered_output' => $renderedOutput
        );

        $arguments = Services::Event()->scheduleEvent('onAfterParse', $arguments);

        if ($arguments === false) {
            Services::Registry()->set(PARAMETERS_LITERAL, ERROR_STATUS_LITERAL, 1);
            Services::Profiler()->set('ParseService onAfterParse failed', PROFILER_PLUGINS);
            return false;
        }

        Services::Registry()->delete(PARAMETERS_LITERAL);
        Services::Registry()->loadArray(PARAMETERS_LITERAL, $arguments[strtolower(PARAMETERS_LITERAL)]);

        $renderedOutput = $arguments['rendered_output'];

        return $renderedOutput;
    }
}
