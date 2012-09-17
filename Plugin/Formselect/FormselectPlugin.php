<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Formselect;

use Molajo\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class FormselectPlugin extends ContentPlugin
{

	/**
	 * Prepares listbox contents
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterReadall()
	{
		return true;
		$formattributes = Services::Registry()->get('Parameters', 'formattributes');

		$att_array = array();
		$temp_array = array();
		$temp = explode('}}', $formattributes);

		foreach ($temp as $set) {
			$set = str_replace(',', ' ', $set);
			$set = str_replace(':', '=', $set);
			$set = str_replace('{{', '', $set);
			$set = str_replace('http=', 'http:', $set);
			if (trim($set) == '') {
			} else {
				$temp_array[] = trim($set);
			}
		}

		foreach ($temp_array as $set) {

			$fields = explode(' ', $set);
			foreach ($fields as $field) {
				$temp = explode('=', $field);
				$pairs[$temp[0]] = $temp[1];
			}

			$row = new \stdClass();
			foreach ($pairs as $key=>$value) {
				$row->key = $key;
				$row->value = $value;
			}
			$att_array[] = $row;
		}

		$this->data = $att_array;

		return true;
	}
}
