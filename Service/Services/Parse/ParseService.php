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
     * $sequence
     *
     * System defined order for processing includes
     * stored in the sequence.xml file
     *
     * @var array
     * @since 1.0
     */
    protected $sequence = array();

    /**
     * $final
     *
     * Final include types -- used to for final iteration of parsing
     *
     * @var array
     * @since 1.0
     */
    protected $final = array();

    /**
     * $exclude_until_final
     *
     * Used to exclude from parsing for all iterations except the final processing
     *
     * @var array
     * @since 1.0
     */
    protected $exclude_until_final = array();

    /**
     * $final_indicator
     *
     * Indicator of final processing for includes
     *
     * @var boolean
     * @since 1.0
     */
    protected $final_indicator = false;

    /**
     * $include_request
     *
     * Include Statement Includer requests extracted from the
     * theme (initially) and then the rendered output
     *
     * @var array
     * @since 1.0
     */
    protected $include_request = array();

    /**
     * $includes
     *
     * Parsing process retrieves include statements from the theme and rendered output
     *
     * @var string
     * @since 1.0
     */
    protected $includes = array();

    /**
     * $parameters
     *
     * Application parameters for sharing with the Routed Extension, Metadata, Theme and Page View
     *
     * @var string
     * @since 1.0
     */
    protected $parameters = array();

    /**
     * $user
     *
     * User object for sharing with the Theme and Page View
     *
     * @var string
     * @since 1.0
     */
    protected $user = array();

    /**
     * $configuration
     *
     * Configuration object for sharing with the Theme and Page View
     *
     * @var string
     * @since 1.0
     */
    protected $configuration = array();

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
     * @return string
     * @since   1.0
     */
    public function process()
    {
        Services::Profiler()->set('ParseService->process Started', LOG_OUTPUT_RENDERING);

        /** Retrieve overrides (could be passed in and are set in the AjaxPlugin, too) */
        $overrideIncludesPageXML = Services::Registry()->get('Override', 'sequence_xml', false);
        $overrideIncludesFinalXML = Services::Registry()->get('Override', 'final_xml', false);

        /**
         *  Body Includers: processed recursively until no more <include: are found
         *      for the set of includes defined in the includes-page.xml
         */
        if ($overrideIncludesPageXML === false) {
            $sequence = Services::Configuration()->getFile('Parse', 'Includespage');
        } else {
            $sequence = $overrideIncludesPageXML;
        }

        foreach ($sequence->include as $next) {
            $this->sequence[] = (string) $next;
        }

        /** Load final xml in order to remove from search for loop during initial runs */
        if ($overrideIncludesFinalXML === false) {
            $final = Services::Configuration()->getFile('Parse', 'Includesfinal');
        } else {
            $final = $overrideIncludesFinalXML;
        }

        foreach ($final->include as $next) {
            $sequence = (string) $next;
            $this->final[] = (string) $next;

            if (stripos($sequence, ':')) {
                $includeName = substr($sequence, 0, strpos($sequence, ':'));
            } else {
                $includeName = $sequence;
            }

            $this->exclude_until_final[] = $includeName;
        }

        $this->final_indicator = false;

        if (file_exists(Services::Registry()->get('Parameters', 'theme_path_include'))) {
        } else {
            Services::Error()->set(500, 'Theme not found');

            return false;
        }

        /** Load Theme, Page, and Request Extension Plugins */
        $themePlugins = Services::Filesystem()->folderFolders(
            Services::Registry()->get('Parameters', 'theme_path') . '/' . 'Plugin'
        );

        if (count($themePlugins) == 0 || $themePlugins === false) {
        } else {
            $this->processPlugins(
                $themePlugins,
                Services::Registry()->get('Parameters', 'theme_namespace')
            );
        }

        $pagePlugins = Services::Filesystem()->folderFolders(
            Services::Registry()->get('Parameters', 'page_view_path') . '/' . 'Plugin'
        );

        if (count($pagePlugins) == 0 || $pagePlugins === false) {
        } else {
            $this->processPlugins(
                $pagePlugins,
                Services::Registry()->get('Parameters', 'page_view_namespace')
            );
        }

        $extensionPlugins = Services::Filesystem()->folderFolders(
            Services::Registry()->get('Parameters', 'extension_path') . '/' . 'Plugin'
        );

        if (count($extensionPlugins) == 0 || $extensionPlugins === false) {
        } else {
            $this->processPlugins(
                $extensionPlugins,
                Services::Registry()->get('Parameters', 'extension_namespace')
            );
        }

        /** OnBeforeParse Plugins */
        if (Services::Registry()->get('Parameters', 'error_status', 0) == 1) {
        } else {
            //todo - pass in lists of includes to the plugins for possible change
            $this->onBeforeParseEvent();
        }

        /** Save Route Parameters */
        Services::Registry()->createRegistry('RouteParameters');
        Services::Registry()->copy('Parameters', 'RouteParameters');

        $renderedOutput = $this->renderLoop();

        /** onAfterParsebody Plugin */
        if (Services::Registry()->get('Parameters', 'error_status', 0) == 1) {
        } else {
            Services::Registry()->delete('Parameters');
            Services::Registry()->createRegistry('Parameters');
            Services::Registry()->copy('RouteParameters', 'Parameters');
            $renderedOutput = $this->onAfterParsebodyEvent($renderedOutput);
        }

        /** Final Includers: Process theme, head, messages, and defer <includes />  */
        $this->sequence = $this->final;

        /** initialise so it is no longer used to exclude this set of include values */
        $this->exclude_until_final = array();

        /** Saved from route */
        Services::Registry()->delete('Parameters');
        Services::Registry()->createRegistry('Parameters');
        Services::Registry()->copy('RouteParameters', 'Parameters');

        $bodyOutput = $renderedOutput;

        /** theme: load template media files */
        $class = 'Molajo\\Includer\\ThemeIncluder';

        if (class_exists($class)) {
            $rc = new $class ('theme');
            $results = $rc->process();
        } else {
            // fail
        }

		/** onBeforeParsehead Plugin */
		if (Services::Registry()->get('Parameters', 'error_status', 0) == 1) {
		} else {
			$renderedOutput = $this->onBeforeParseheadEvent($renderedOutput);
		}

        $renderedOutput = $this->renderLoop($renderedOutput);

        /** onAfterParsehead Plugin */
        if (Services::Registry()->get('Parameters', 'error_status', 0) == 1) {
        } else {
            $renderedOutput = $this->onAfterParseheadEvent($renderedOutput);
        }

        /** onAfterParse */
		if (Services::Registry()->get('Parameters', 'error_status', 0) == 1) {
		} else {
			Services::Registry()->delete('Parameters');
			Services::Registry()->createRegistry('Parameters');
			Services::Registry()->copy('RouteParameters', 'Parameters');
			$renderedOutput = $this->onAfterParseEvent($renderedOutput);
		}

		return $renderedOutput;
    }

    /**
     * processPlugins for Theme, Page, and Request Extension (overrides Core and Plugin folder)
     *
     * @param  $plugins array of folder names
     * @param  $path
     *
     * @return void
     * @since  1.0
     */
    protected function processPlugins($plugins, $path)
    {
        foreach ($plugins as $folder) {

            Services::Event()->process_events(
                $folder . 'Plugin',
                $path . '\\Plugin\\' . $folder . '\\' . $folder . 'Plugin'
            );
        }
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
     * @return string $renderedOutput  Rendered output for the Response Head and Body
     * @since   1.0
     */
    protected function renderLoop($renderedOutput = null)
    {
        if ($renderedOutput == null) {
            $first = true;
            Services::Profiler()->set(
                'ParseService renderLoop Parse Body using Theme:'
                    . Services::Registry()->get('Parameters', 'theme_path_include')
                    . ' and Page View: '
                    . Services::Registry()->get('Parameters', 'page_view_path_include'),
                LOG_OUTPUT_RENDERING
            );

            ob_start();
            require Services::Registry()->get('Parameters', 'theme_path_include');
            $renderedOutput = ob_get_contents();
            ob_end_clean();

        } else {

            $first = false;
            $final = true;
            Services::Profiler()->set('ParseService renderLoop Parse Document Head ',
                LOG_OUTPUT_RENDERING
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
     * parseIncludeRequests
     *
     * Parse the theme (first) and then rendered output (subsequent calls) for include statements
     *
     * Note: Attribute pairs may NOT contain spaces. To include multiple values, separate with a comma:
     *  ex. class=one,two,three
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

        Services::Profiler()->set($includeDisplay, LOG_OUTPUT_RENDERING);

        return;
    }

    /**
     * callIncluder
     *
     * Invoke extension-specific includer for include statement
     *
     * @return  string rendered output
     * @since   1.0
     */
    protected function callIncluder($first = false, $renderedOutput)
    {
        $replace = array();
        $with = array();

        /** 1. process extension includers in order defined by includespage.xml and includesfinal */
        foreach ($this->sequence as $sequence) {

            /** 2. if necessary, split includer name and type (ex. request:resource and defer:head) */
            if (stripos($sequence, ':')) {
                $includeName = substr($sequence, 0, strpos($sequence, ':'));
                $includerType = substr($sequence, strpos($sequence, ':') + 1, 999);
            } else {
                $includeName = $sequence;
                $includerType = $sequence;
            }

            /** 3. loop thru parsed include requests for match */
            for ($i = 0; $i < count($this->include_request); $i++) {

                $parsedRequests = $this->include_request[$i];

                if ($includeName == $parsedRequests['name']) {

                    /** 4. place attribute pairs into variable */
                    if (isset($parsedRequests['attributes'])) {
                        $attributes = $parsedRequests['attributes'];
                    } else {
                        $attributes = array();
                    }

                    /** 5. store the "replace this" value */
                    $replace[] = "<include:" . $parsedRequests['replace'] . "/>";

                    /** 6. initialise registry */
                    Services::Registry()->deleteRegistry('Parameters');
                    Services::Registry()->createRegistry('Parameters');

                    if ($includeName == 'request') {
                        Services::Registry()->copy('RouteParameters', 'Parameters');
                        Services::Registry()->set('Parameters', 'extension_primary', true);
                    } else {
                        Services::Registry()->set('Parameters', 'extension_primary', false);
                    }

                    /** 7. call the includer class */
                    $class = 'Molajo\\Includer\\';
                    $class .= ucfirst($includerType) . 'Includer';
                    if (class_exists($class)) {
                        $rc = new $class ($includerType, $includeName);

                    } else {
                        Services::Profiler()->set('ParseService callIncluder failed instantiating class ' . $class,
                            LOG_OUTPUT_RENDERING);
                        // ERROR
                    }

                    /** 8. render output and store results as "replace with" */
                    ob_start();
                    echo 'ParseService->callIncluder invoking class ' . $class . ' Attributes: ' . '<br />';
                    echo '<pre>';
                    var_dump($attributes);
                    echo '</pre>';
                    $includeDisplay = ob_get_contents();
                    ob_end_clean();

                    Services::Profiler()->set($includeDisplay,
                        LOG_OUTPUT_RENDERING,
                        VERBOSE
                    );

                    $output = trim($rc->process($attributes));

//echo '<br />';
//echo $output;
//echo '<br />';

                    Services::Profiler()->set('ParseService->callIncluder rendered output ' . $output, LOG_OUTPUT_RENDERING, VERBOSE);

                    $with[] = $output;
                }
            }
        }

        /** 9. replace it */
        $renderedOutput = str_replace($replace, $with, $renderedOutput);

        return $renderedOutput;
    }

    /**
     * Schedule onBeforeParseEvent Event
     *
     * @return  boolean
     * @since   1.0
     */
    protected function onBeforeParseEvent()
    {
        Services::Profiler()->set('ParseService onBeforeParse', LOG_OUTPUT_PLUGINS, VERBOSE);

        $arguments = array(
            'parameters' => Services::Registry()->getArray('Parameters'),
            'model_type' => Services::Registry()->get('Parameters', 'model_type'),
            'model_name' => Services::Registry()->get('Parameters', 'model_name'),
            'data' => array()
        );

        $arguments = Services::Event()->schedule('onBeforeParse', $arguments);

        if ($arguments === false) {
            Services::Registry()->set('Parameters', 'error_status', 1);
            Services::Profiler()->set('ParseService onBeforeParsebody failed', LOG_OUTPUT_PLUGINS);
            return false;
        }

        /** Process results */
        Services::Registry()->delete('Parameters');
        Services::Registry()->createRegistry('Parameters');
        Services::Registry()->loadArray('Parameters', $arguments['parameters']);
        Services::Registry()->sort('Parameters');

        return true;
    }

    /**
     * Schedule onAfterParseBody Event
     *
     * @param   string  $renderedOutput
     *
     * @return  string  rendered output
     * @since   1.0
     */
    protected function onAfterParsebodyEvent($renderedOutput)
    {
        Services::Profiler()->set('ParseService onAfterParsebody', LOG_OUTPUT_PLUGINS, VERBOSE);

        $parameters = Services::Registry()->getArray('Parameters');

        $arguments = array(
            'parameters' => $parameters,
            'rendered_output' => $renderedOutput
        );

        $arguments = Services::Event()->schedule('onAfterParsebody', $arguments);

        if ($arguments === false) {
            Services::Registry()->set('Parameters', 'error_status', 1);
            Services::Profiler()->set('ParseService onAfterParsebody failed', LOG_OUTPUT_PLUGINS);
            return false;
        }

        Services::Registry()->delete('Parameters');
        Services::Registry()->loadArray('Parameters', $arguments['parameters']);
        Services::Registry()->sort('Parameters');

        $renderedOutput = $arguments['rendered_output'];

        return $renderedOutput;
    }

	/**
	 * Schedule onBeforeParsehead Event
	 *
     * @param   string  $renderedOutput
     *
     * @return  string  rendered output
     * @since   1.0
     */
	protected function onBeforeParseheadEvent($renderedOutput)
	{
        Services::Profiler()->set('ParseService onBeforeParsehead', LOG_OUTPUT_PLUGINS, VERBOSE);

        $parameters = Services::Registry()->getArray('Parameters');

        $arguments = array(
            'parameters' => $parameters,
            'rendered_output' => $renderedOutput
        );

        $arguments = Services::Event()->schedule('onBeforeParsehead', $arguments);

        if ($arguments === false) {
            Services::Registry()->set('Parameters', 'error_status', 1);
            Services::Profiler()->set('ParseService onBeforeParsehead failed', LOG_OUTPUT_PLUGINS);
            return false;
        }

        Services::Registry()->delete('Parameters');
        Services::Registry()->loadArray('Parameters', $arguments['parameters']);

        $renderedOutput = $arguments['rendered_output'];

        return $renderedOutput;
	}

    /**
     * Schedule onAfterParseheadEvent Event
     *
     * @return boolean
     * @since   1.0
     */
    protected function onAfterParseheadEvent($renderedOutput)
    {
        Services::Profiler()->set('ParseService onAfterParsehead', LOG_OUTPUT_PLUGINS, VERBOSE);

        $parameters = Services::Registry()->getArray('Parameters');

        $arguments = array(
            'parameters' => $parameters,
            'rendered_output' => $renderedOutput
        );

        $arguments = Services::Event()->schedule('onAfterParsehead', $arguments);

        if ($arguments === false) {
            Services::Registry()->set('Parameters', 'error_status', 1);
            Services::Profiler()->set('ParseService onAfterParsehead failed', LOG_OUTPUT_PLUGINS);
            return false;
        }

        Services::Registry()->delete('Parameters');
        Services::Registry()->loadArray('Parameters', $arguments['parameters']);

        $renderedOutput = $arguments['rendered_output'];

        return $renderedOutput;
    }

    /**
     * Schedule onAfterParseEvent Event
     *
     * @return boolean
     * @since   1.0
     */
    protected function onAfterParseEvent($renderedOutput)
    {
        Services::Profiler()->set('ParseService onAfterParse', LOG_OUTPUT_PLUGINS, VERBOSE);

        $parameters = Services::Registry()->getArray('Parameters');

        $arguments = array(
            'parameters' => $parameters,
            'rendered_output' => $renderedOutput
        );

        $arguments = Services::Event()->schedule('onAfterParse', $arguments);

        if ($arguments === false) {
            Services::Registry()->set('Parameters', 'error_status', 1);
            Services::Profiler()->set('ParseService onAfterParse failed', LOG_OUTPUT_PLUGINS);
            return false;
        }

        Services::Registry()->delete('Parameters');
        Services::Registry()->loadArray('Parameters', $arguments['parameters']);

        $renderedOutput = $arguments['rendered_output'];

        return $renderedOutput;
    }
}
