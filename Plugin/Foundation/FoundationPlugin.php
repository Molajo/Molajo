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

		} else {
			return true;
		}

		return true;
	}

	/**
	 * Add CSS Class to each row for Button Options
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
	 * Add CSS Class to each row for Button Options
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

		$button_array = array();
		$temp = explode('{{', $this->data->button_group_array);
		foreach ($temp as $set) {
			$set = str_replace(',', ' ', $set);
			$set = str_replace(':', '=', $set);
			$set = str_replace('}}', '', $set);
			if (trim($set) == '') {
			} else {
				$button_array[] = trim($set);
			}
		}

		$this->saveField(null, 'button_group_array', $button_array);

		return true;
	}
}
