<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Molajito;

use Molajo\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class MolajitoPlugin extends ContentPlugin
{
	/**
	 * Prepares data for the Molajito UI Views
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onBeforeViewRender()
	{

		if (strtolower($this->get('template_view_path_node')) == 'ui-navigation-tab-molajito') {
			$this->tab();

		} else {
			return true;
		}

		return true;
	}

	/**
	 * Molajito Tab Group
	 *
	 * @return boolean
	 * @since   1.0
	 */
	protected function tab()
	{
		$tab_class = str_replace(',', ' ', $this->data->tab_class);

		$tab_class = 'tabs ' . trim($tab_class);

		$tab_class = ' class="' . htmlspecialchars(trim($tab_class), ENT_NOQUOTES, 'UTF-8') . '"';
		$this->saveField(null, 'tab_class', $tab_class);

		$tab_array = $this->getTabs($this->data->tab_array);
		$this->saveField(null, 'tab_array', $tab_array);

		return true;
	}

	/**
	 * Get tab sections
	 *
	 * @param $tabs
	 * @return array
	 */
	protected function getTabs($tabs)
	{
		$tab_array = array();
		$temp_array = array();
		$temp = explode('}}', $tabs);

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
				$row->$key = $value;
			}
			$tab_array[] = $row;
		}

		return $tab_array;
	}
}
