<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
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
	 * Indicator of final processing for includes
	 *
	 * @var boolean
	 * @since 1.0
	 */
	protected $final = false;

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
	 * $rendered_output
	 *
	 * Collects output rendered by MVC after Includer Processing
	 *
	 * @var string
	 * @since 1.0
	 */
	protected $rendered_output = array();

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
	 * @return  string
	 * @since   1.0
	 */
	public function process()
	{
		/** Retrieve overrides */
		$sequenceXML = Services::Registry()->get('Override', 'sequence_xml', '');
		$finalXML = Services::Registry()->get('Override', 'final_xml', '');

		/**
		 *  Body Includers: processed recursively until no more <include: are found
		 *      for the set of includes defined in the includes-page.xml
		 */
		$sequence = Services::Configuration()->getFile('includes-page', 'Application');

		foreach ($sequence->include as $next) {
			$this->sequence[] = (string)$next;
		}

		/** Before Event */
		Services::Event()->schedule('onBeforeRender');

		$this->final = false;

		/** Save parameters for the primary route so that Includer / MVC can use Parameters Registry */
		Services::Registry()->copy('Parameters', 'RouteParameters');

		/** Start parsing and processing page include for Theme */
		$body = $this->renderLoop();

		/**
		 *  Final Includers: Now, the theme, head, messages, and defer includes run
		 *      This process also removes <include values not found
		 */
		$sequence = Services::Configuration()->getFile('includes-final', 'Application');

		$this->sequence = array();

		foreach ($sequence->include as $next) {
			if ($next == 'message') {
				$messages = Services::Message()->get();
				if (count($messages) == 0) {
				} else {
					$this->sequence[] = (string)$next;
				}
			} else {
				$this->sequence[] = (string)$next;
			}
		}

		/** theme: load template media and language files */
		$class = 'Molajo\\Extension\\Includer\\ThemeIncluder';

		if (class_exists($class)) {
			$rc = new $class ('theme');
			$results = $rc->process();

		} else {
			echo 'failed include = ' . 'IncluderTheme' . '<br />';
			// ERROR
		}

		$this->final = true;
		$body = $this->renderLoop($body);

		/** after rendering */
		$body = Services::Event()->schedule('onAfterRender', $body);

		return $body;
	}

	/**
	 * renderLoop
	 *
	 * Parse the Theme and Page View, and then rendered output, for <include:type statements
	 *
	 * @return  string  $body  Rendered output for the Response Head and Body
	 * @since   1.0
	 */
	protected function renderLoop($body = null)
	{
		/** initial run: start with theme and page */
		if ($body == null) {
			ob_start();
			require Services::Registry()->get('Parameters', 'theme_path_include');
			$this->rendered_output = ob_get_contents();
			ob_end_clean();

		} else {
			/* final run (for page head): start with rendered body */
			$this->rendered_output = $body;
		}

		/** process all input for include: statements  */
		$complete = false;
		$loop = 0;
		while ($complete === false) {

			$loop++;

			$this->parseIncludeRequests();

			if (count($this->include_request) == 0) {
				break;
			} else {
				$this->rendered_output = $this->callIncluder();
			}

			if ($loop > STOP_LOOP) {
				break;
			}
			continue;
		}
		return $this->rendered_output;
	}

	/**
	 * parseIncludeRequests
	 *
	 * Parse the theme (first) and then rendered output (subsequent calls)
	 * in search of include statements
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

		if (count($matches) == 0) {
			return;
		}

		foreach ($matches[1] as $includeStatement) {

			$parts = array();
			$parts = explode(' ', $includeStatement);
			$includerType = '';

			foreach ($parts as $part) {

				/** 1st part is the Includer Command */
				if ($includerType == '') {
					$includerType = $part;
					$this->include_request[$i]['name'] = $includerType;
					$this->include_request[$i]['replace'] = $includeStatement;

				} else {

					/** Includer Attributes */
					$attributes = str_replace('"', '', $part);

					if (trim($attributes) == '') {
					} else {

						/** Associative array of attributes */
						$pair = array();
						$pair = explode('=', $attributes);
						$this->include_request[$i]['attributes'][$pair[0]] = $pair[1];
					}
				}
			}
			$i++;
		}
	}

	/**
	 * callIncluder
	 *
	 * Invoke extension-specific includer for include statement
	 *
	 * @return  string rendered output
	 * @since   1.0
	 */
	protected function callIncluder()
	{
		$replace = array();
		$with = array();
		$first = true;

		/** 1. process extension includers in order defined by sequence.xml */
		foreach ($this->sequence as $sequence) {

			/** 2. if necessary, split includer name and type     */
			/** (ex. request:component and defer:head)            */
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
					if ($first) {
					} else {
						Services::Registry()->createRegistry('Include');
					}

					/** 7. call the includer class */
					$first = false;
					$class = 'Molajo\\Extension\\Includer\\';
					$class .= ucfirst($includerType) . 'Includer';

					if (class_exists($class)) {
						$rc = new $class ($includerType, $includeName);
					} else {
						echo 'failed includer = ' . $class . '<br />';
						die;
						// ERROR
					}

					/** 8. render output and store results as "replace with" */
					$with[] = $rc->process($attributes);
				}
			}
		}

		/** 9. replace it */
		$this->rendered_output = str_replace($replace, $with, $this->rendered_output);

		/** 10. make certain all <include:xxx /> literals are removed on final */
		if ($this->final === true) {
			$replace = array();
			$with = array();
			for ($i = 0; $i < count($this->include_request); $i++) {
				$replace[] = "<include:" . $this->include_request[$i]['replace'] . "/>";
				$with[] = '';
			}

			$this->rendered_output = str_replace($replace, $with, $this->rendered_output);
		}

		return $this->rendered_output;
	}
}
