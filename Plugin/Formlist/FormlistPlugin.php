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

		echo 'ayeafasdfas';
		die;
		if (strtolower($this->get('template_view_path_node')) == 'formlist') {
			echo 'yes - i am in formlistplugin';
			die;
		} else {
			return true;
		}

		$datalist = $this->get('Parameters', 'datalist');
		echo  $datalist;
		die;
		$items = Services::Text()->getList($datalist, $this->parameters);

		if ($items == false) {
		} else {
			return true;
		}

		$query_results = Services::Text()->buildSelectlist($datalist, $items, 0, 5);

		$row = new \stdClass();
		$row->listname = $datalist;
		$lists[] = $row;


		$row = new \stdClass();
		foreach ($pairs as $key=>$value) {
			$row->key = $key;
			$row->value = $value;
		}



		$this->data = $datalist_array;

		return true;
	}
}
