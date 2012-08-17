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
		} else {
			return true;
		}

		return true;
	}

	/**
	 * Add CSS Class and ID to each row
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

		$this->saveField(null, 'button_class', trim($button_class));

		return true;
	}
}
