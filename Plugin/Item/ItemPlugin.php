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
	 * Prepares Buttons for Item Type Page
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

		return true;
	}
}
