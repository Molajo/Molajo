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
	 * Before the Query results are injected into the View
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onBeforeViewRender()
	{
		$count = count($this->data);

		if ((int)$count == 0
			|| $this->data == false
			|| $this->data == null
		) {
			return true;
		}

		$i = 1;
		$even_or_odd = 'odd';
		foreach ($this->data as $item) {

			if (is_object($item)) {
			} else {
				return true;
			}

			if ($i == 1) {
				$item->first_row = 'first';
			} else {
				$item->first_row = '';
			}

			if ($i == $count) {
				$item->last_row = 'last';
			} else {
				$item->last_row = '';
			}

			$item->total_records = $count;

			$item->even_or_odd_row = $even_or_odd;
			if ($even_or_odd == 'odd') {
				$even_or_odd = 'even';
			} else {
				$even_or_odd = 'odd';
			}

			$item->grid_row_class = ' class="' .
				trim(trim($item->first_row) . ' ' . trim($item->even_or_odd_row) . ' ' . trim($item->last_row))
				. '"';

			$item->row_number = $i++;
		}

		return true;
	}
}
