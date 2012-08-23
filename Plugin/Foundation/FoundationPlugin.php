<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Foundation;

use Molajo\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class FoundationPlugin extends ContentPlugin
{
	/**
	 * Prepares data for the Foundation UI Views
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeViewRender()
	{
		if (strtolower($this->get('template_view_path_node')) == 'ui-button-foundation') {
			$this->button_general();

		} elseif (strtolower($this->get('template_view_path_node')) == 'ui-button-group-foundation') {
			$this->button_group();

		} elseif (strtolower($this->get('template_view_path_node')) == 'ui-button-dropdown-foundation') {
			$this->button_dropdown();

		} else {
			return true;
		}

		return true;
	}

	/**
	 * Foundation Buttons
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function button_general()
	{

		$button_type = $this->data->button_type;
		$button_size = $this->data->button_size;
		$button_shape = $this->data->button_shape;

		$button_class = trim($button_type);
		$button_class = trim($button_class) . ' ' . trim($button_shape);
		$button_class = trim($button_class) . ' ' . trim($button_size);
		$button_class = trim($button_class) . ' ' . 'button';

		$button_class = ' class="' . htmlspecialchars(trim($button_class), ENT_NOQUOTES, 'UTF-8') . '"';

		$this->saveField(null, 'button_class', $button_class);

		return true;
	}

	/**
	 * Foundation Button Group and Button Bar
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function button_group()
	{

		$button_type = $this->data->button_group_type;
		$button_size = $this->data->button_group_size;
		$button_shape = $this->data->button_group_shape;
		$button_class = str_replace(',', ' ', $this->data->button_group_class);

		$button_group_class = trim($button_type);
		$button_group_class = trim($button_group_class) . ' ' . trim($button_shape);
		$button_group_class = trim($button_group_class) . ' ' . trim($button_size);
		$button_group_class = trim($button_group_class) . ' ' . trim($button_class);
		$button_group_class = trim($button_group_class) . ' ' . 'button-group';

		$button_group_class = ' class="' . htmlspecialchars(trim($button_group_class), ENT_NOQUOTES, 'UTF-8') . '"';
		$this->saveField(null, 'button_group_class', $button_group_class);

		$button_array = $this->getButtons($this->data->button_group_array);
		$this->saveField(null, 'button_group_array', $button_array);

		return true;
	}

	/**
	 * Foundation Dropdown buttons
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function button_dropdown()
	{
		$button_type = $this->data->button_dropdown_type;
		$button_size = $this->data->button_dropdown_size;
		$button_shape = $this->data->button_dropdown_shape;
		$button_class = str_replace(',', ' ', $this->data->button_dropdown_class);

		$button_dropdown_class = trim($button_type);
		$button_dropdown_class = trim($button_dropdown_class) . ' ' . trim($button_shape);
		$button_dropdown_class = trim($button_dropdown_class) . ' ' . trim($button_size);
		$button_dropdown_class = trim($button_dropdown_class) . ' ' . trim($button_class);
		$button_dropdown_class = trim($button_dropdown_class) . ' ' . 'button';

		$button_dropdown_class = ' class="' . htmlspecialchars(trim($button_dropdown_class), ENT_NOQUOTES, 'UTF-8') . '"';

		$this->saveField(null, 'button_dropdown_class', $button_dropdown_class);

		$button_array = $this->getButtons($this->data->button_group_array);
		$this->saveField(null, 'button_group_array', $button_array);

		return true;
	}

	/**
	 * Get individual buttons
	 *
	 * @param $buttons
	 * @return array
	 */
	public function getButtons($buttons)
	{
		$button_array = array();
		$temp = explode('}}', $buttons);

		foreach ($temp as $set) {
			$set = str_replace(',', ' ', $set);
			$set = str_replace(':', '=', $set);
			$set = str_replace('{{', '', $set);
			$set = str_replace('http=', 'http:', $set);
			if (trim($set) == '') {
			} else {
				$button_array[] = trim($set);
			}
		}
		return $button_array;
	}
}
