<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Item;

use Molajo\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class ItemPlugin extends ContentPlugin
{
	/**
	 * Prepares Page Title and Buttons for Rendering
	 *
	 * @return  void
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		if (APPLICATION_ID == 2) {
		} else {
			return true;
		}

		if (strtolower($this->get('template_view_path_node')) == 'item') {
		} else {
			return true;
		}

		/** Edit Button */
		$buttonTitle  = str_replace(
			' ',
			'&nbsp;',
			htmlentities('Edit', ENT_COMPAT, 'UTF-8')
		);

		$buttonArray  = 'button_id:enable'
			. ',button_tag:button'
			. ',button_icon_prepend:icon-edit,'
			. 'button_title:' . $buttonTitle
			. ',' . 'button_type:secondary,'
			. ',' . 'button_link:' . Services::Registry()->get('Plugindata', 'page_url')
			. '/edit';

		$this->saveField(null, 'edit_button', '{{' . trim($buttonArray) . '}}');

		/** Delete Button */
		$buttonTitle = str_replace(
			' ',
			'&nbsp;',
			htmlentities('Delete', ENT_COMPAT, 'UTF-8')
		);
		$linkURL = Services::Registry()->get('Plugindata', 'page_url');

		$buttonArray = 'button_tag:button'
			. ',button_icon_prepend:icon-trash'
			. ',button_title:' . $buttonTitle
			. ',' . 'button_type:alert,'
			. ',' . 'button_link:' . Services::Registry()->get('Plugindata', 'page_url')
			. '/delete';

		$this->saveField(null, 'delete_button', '{{' . trim($buttonArray) . '}}');

		return true;
	}
}
