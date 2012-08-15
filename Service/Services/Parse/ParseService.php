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
	 * $instance
	 *
	 * Parse static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

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
	 * getInstance
	 *
	 * @return bool|object
	 * @since  1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new ParseService();
		}

		return self::$instance;
	}

	/**
	 * __construct
	 *
	 * @return boolean
	 * @since  1.0
	 */
	public function __construct()
	{
		return $this;
	}

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

		/** OnBeforeParse Plugins */
		if (Services::Registry()->get('Parameters', 'error_status', 0) == 1) {
		} else {
			$this->onBeforeParseEvent();
		}

		/** Retrieve overrides (could be passed in and are set in the AjaxPlugin, too) */
		$overrideIncludesPageXML = Services::Registry()->get('Override', 'sequence_xml', false);
		$overrideIncludesFinalXML = Services::Registry()->get('Override', 'final_xml', false);

		/**
		 *  Body Includers: processed recursively until no more <include: are found
		 *      for the set of includes defined in the includes-page.xml
		 */
		if ($overrideIncludesPageXML === false) {
			$sequence = Services::Configuration()->getFile('Application', 'Includespage');
		} else {
			$sequence = $overrideIncludesPageXML;
		}

		foreach ($sequence->include as $next) {
			$this->sequence[] = (string)$next;
		}

		/** Load final xml in order to remove from search for loop during initial runs */
		if ($overrideIncludesFinalXML === false) {
			$final = Services::Configuration()->getFile('Application', 'Includesfinal');
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

		/** Using file_exists or is_file with git submodule returns false */
		if (file_exists(Services::Registry()->get('Parameters', 'theme_path_include'))) {
		} else {
			Services::Error()->set(500, 'Theme not found');

			return false;
		}

		/** Save Route Parameters move to after route */
		Services::Registry()->copy('Parameters', 'RouteParameters');
		Services::Registry()->get('Parameters', '*');
		die;
		$renderedOutput = $this->renderLoop();

		/** Final Includers: Now, the theme, head, messages, and defer <includes /> run */
		$this->sequence = $this->final;

		/** initialize so it is no longer used to exclude this set of include values */
		$this->exclude_until_final = array();

		/** Saved from route */
		Services::Registry()->copy('RouteParameters', 'Parameters');

		/** theme: load template media and language files */
		$class = 'Molajo\\Includer\\ThemeIncluder';

		if (class_exists($class)) {
			$rc = new $class ('theme');
			$results = $rc->process();

		} else {
			echo 'failed include = ' . 'IncluderTheme' . '<br />';
			// ERROR
		}

		$renderedOutput = $this->renderLoop($renderedOutput);
		echo $renderedOutput;
		die;
		/** onAfterParse Plugin */
		if (Services::Registry()->get('Parameters', 'error_status', 0) == 1) {
		} else {
			Services::Registry()->copy('RouteParameters', 'Parameters');
			$renderedOutput = $this->onAfterParseEvent($renderedOutput);
		}

		return $renderedOutput;
	}

	/**
	 * renderLoop
	 *
	 * Parse the Theme and Page View, and then rendered output, for <include:type statements
	 *
	 * @return string $renderedOutput  Rendered output for the Response Head and Body
	 * @since   1.0
	 */
	protected function renderLoop($renderedOutput = null)
	{
		/** initial run: start with theme and page */
		if ($renderedOutput == null) {

			$first = true;

			Services::Profiler()->set(
				'ParseService->renderLoop include Theme:'
					. Services::Registry()->get('Parameters', 'theme_path_include')
					. ' which includes Page: '
					. Services::Registry()->get('Parameters', 'page_view_path_include'),
				LOG_OUTPUT_RENDERING,
				VERBOSE
			);

			ob_start();
			require Services::Registry()->get('Parameters', 'theme_path_include');
			$renderedOutput = ob_get_contents();
			ob_end_clean();

		} else {

			/* final run (for page head): start with rendered body */
			$first = false;
			$final = true;
			Services::Profiler()->set('ParseService->renderLoop Final Run ',
				LOG_OUTPUT_RENDERING,
				VERBOSE
			);
		}

		/** process all input for include: statements  */
		$complete = false;
		$loop = 0;
		while ($complete === false) {

			$loop++;

			/** Retrieve <include /> Statements in body */
			$this->include_request = array();
			$this->parseIncludeRequests($renderedOutput);

			/** When no other <include /> statements are found, end recursive processing */
			if (count($this->include_request) == 0) {
				break;
			}

			/** Render output for each discovered <include /> statement */
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
	 * Parse the theme (first) and then rendered output (subsequent calls)
	 * in search of include statements
	 *
	 * Note: Neither attribute pair may contain spaces.
	 * To include multiple class value overrides, separate each element with a comma
	 *
	 * @return array
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

						/** Exclude the final include types */
						if (in_array($part, $this->exclude_until_final)) {
							$skipped_final_include_type = true;

						} else {
							$this->include_request[$i]['name'] = $includerType;
							$this->include_request[$i]['replace'] = $includeStatement;
							$skipped_final_include_type = false;
						}

					} elseif ($skipped_final_include_type == false) {

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

				if ($skipped_final_include_type == false) {

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
		echo 'ParseService->parseIncludeRequests identified the following includes:<br />';
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
	 * @return string rendered output
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

					/** 6. initialize registry */
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
						Services::Profiler()->set('ParseService->callIncluder failed instantiating class ' . $class,
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
	 * Schedule onBeforeParseEvent Event - could update parameter values
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	protected function onBeforeParseEvent()
	{
		Services::Profiler()->set(
			'ParseService->process Schedules onBeforeParse',
			LOG_OUTPUT_PLUGINS,
			VERBOSE
		);

		/** Schedule onBeforeParse Event */
		$arguments = array(
			'parameters' => Services::Registry()->getArray('Parameters'),
			'data' => array()
		);

		Services::Profiler()->set(
			'ParseService->onBeforeParseEvent ' . ' Schedules onBeforeParse',
			LOG_OUTPUT_PLUGINS,
			VERBOSE
		);

		$arguments = Services::Event()->schedule('onBeforeParse', $arguments);

		if ($arguments == false) {
			Services::Profiler()->set('ParseService->onBeforeParseEvent ' . ' failure ',
				LOG_OUTPUT_PLUGINS
			);

			return false;
		}

		Services::Profiler()->set(
			'ParseService->onBeforeParseEvent ' . ' successful ',
			LOG_OUTPUT_PLUGINS,
			VERBOSE
		);

		/** Process results */
		Services::Registry()->delete('Parameters');
		Services::Registry()->loadArray('Parameters', $arguments['parameters']);
		Services::Registry()->sort('Parameters');

		return true;
	}

	/**
	 * Schedule onAfterParseEvent Event - could update rendered output
	 *
	 * @return boolean
	 * @since   1.0
	 */
	protected function onAfterParseEvent($renderedOutput)
	{
		Services::Profiler()->set('ParseService->process Schedules onAfterParse',
			LOG_OUTPUT_PLUGINS,
			VERBOSE
		);

		$parameters = Services::Registry()->getArray('Parameters');

		/** Schedule onAfterRead Event */
		$arguments = array(
			'parameters' => $parameters,
			'rendered_output' => $renderedOutput
		);

		Services::Profiler()->set('ParseService->onAfterParseEvent ' . ' Schedules onAfterParse',
			LOG_OUTPUT_PLUGINS,
			VERBOSE
		);

		$arguments = Services::Event()->schedule('onAfterParse', $arguments);
		if ($arguments == false) {
			Services::Profiler()->set('ParseService->onAfterParseEvent ' . ' failure ',
				LOG_OUTPUT_PLUGINS
			);
			return false;
		}

		Services::Profiler()->set('ParseService->onAfterParseEvent ' . ' successful ',
			LOG_OUTPUT_PLUGINS,
			VERBOSE
		);

		/** Process results */
		Services::Registry()->delete('Parameters');
		Services::Registry()->loadArray('Parameters', $arguments['parameters']);

		$renderedOutput = $arguments['rendered_output'];

		return $renderedOutput;
	}
}
