<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Mustache;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Mustache\Mustache;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class MustacheTrigger extends ContentTrigger
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * Parameters passed in by Controller when class instantiated
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $parameters;

	/**
	 * $data
	 *
	 * Allows collection of any set of data for a single $item
	 *
	 * @var    array
	 * @since  1.0
	 */
	public $data = array();

	/**
	 * $rows
	 *
	 * Retains pointer to current row contained within the $data array
	 *
	 * @var    int
	 * @since  1.0
	 */
	protected $rows = 0;

	/**
	 * getInstance
	 *
	 * @static
	 * @return bool|object
	 * @since  1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new MustacheTrigger();
		}

		return self::$instance;
	}

	/**
	 * Rendered results from Views can be further processed
	 *
	 * @return  void
	 * @since   1.0
	 */
	public function onAfterViewRender()
	{
		if ($this->rendered_output == '' || $this->rendered_output === null) {
			return;
		}

		if ($this->get('mustache', 0) == 1) {
		} else {
			return;
		}

		/** Quick check for Mustache Syntax */
		if (stripos($this->rendered_output, '}}') > 0) {
		} else {
			return;
		}

		/** Passes the rendered output and query_results into the Mustache Theme Helper for processing. */

		/** Instantiate Mustache before Theme Helper */
		$m = new Mustache;

		/** Theme Specific Mustache Helper or Molajo Mustache Helper */
		$helperClass = 'Molajo\\Extension\\Theme\\'
			. ucfirst(Services::Registry()->get('Parameters', 'theme_path_node')) . '\\Helper\\'
			. 'Theme' . ucfirst(Services::Registry()->get('Parameters', 'theme_path_node')) . 'Helper';

		if (\class_exists($helperClass)) {
		} else {
			return;
		}
		/** Instantiate Class */
		$h = new $helperClass();

		/** Push in Parameters */
		$h->parameters = $this->parameters;

		/** Push in Query Results */
		$h->items = $this->query_results;

		/** Push in model results */
		$totalRows = count($this->query_results);

		if (($this->query_results) == false) {
			$totalRows = 0;
		}

		if (is_object($this->query_results)) {

			if ($totalRows > 0) {
				foreach ($this->query_results as $this->row) {

					$item = new \stdClass ();
					$pairs = get_object_vars($this->row);
					foreach ($pairs as $key => $value) {
						$item->$key = $value;
					}

					$new_query_results[] = $item;
				}
			}

			/** Load -- Associative Array */
		} else {
			$new_query_results = $this->query_results;
		}

		/** Pass in Rendered Output and Helper Class Instance */
		ob_start();
		echo $h->render($this->rendered_output);
		$this->rendered_output = ob_get_contents();
		ob_end_clean();

		/** Complete */
		return;
	}
}
