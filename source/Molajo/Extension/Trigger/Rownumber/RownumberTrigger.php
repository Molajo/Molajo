<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Rownumber;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class RownumberTrigger extends ContentTrigger
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

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
			self::$instance = new RownumberTrigger();
		}

		return self::$instance;
	}

	/**
	 * Before the Query results are injected into the View
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeViewRender()
	{
		$count = count($this->query_results);

		if ((int) $count == 0
			|| $this->query_results == false
			|| $this->query_results == null) {
			return true;
		}

		$i = 1;
		$even_or_odd = 'odd';
		foreach ($this->query_results as $item) {

			if ($i == 1) {
				$item->first_row = 1;
			} else {
				$item->first_row = 0;
			}

			if ($i == $count) {
				$item->last_row = 1;
			} else {
				$item->last_row = 0;
			}

			$item->total_records = $count;

			$item->even_or_odd_row = $even_or_odd;
			if ($even_or_odd == 'odd') {
				$even_or_odd = 'even';
			} else {
				$even_or_odd = 'odd';
			}

			$item->row_number = $i++;
		}

		return true;
	}
}
