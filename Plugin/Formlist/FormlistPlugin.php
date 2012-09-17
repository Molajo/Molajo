<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Formlist;

use Molajo\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class FormlistPlugin extends ContentPlugin
{

	/**
	 * Prepares listbox contents
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterReadall()
	{

		if (strtolower($this->get('template_view_path_node')) == 'formlist') {
		} else {
			return true;
		}

		$selected = $this->get('selected');
		$selected = str_replace(
			'&nbsp;',
			' ',
			html_entity_decode($selected, ENT_COMPAT, 'UTF-8')
		);
		$selectedArray = explode(',', $selected);

		$datalist = $this->get('datalist');
		$items = Services::Text()->getList($datalist, $this->parameters);
		if ($items == false) {
			return true;
		}

		$query_results = array();

		foreach ($items as $item) {

			$row = new \stdClass();

			$row->id = $item->id;
			$row->value = $item->value;

			if (in_array($row->id, $selectedArray)) {
				$row->selected = ' selected ';
			} else {
				$row->selected = '';
			}

			$query_results[] = $row;
		}


		$this->data = $query_results;

		return true;
	}
}
