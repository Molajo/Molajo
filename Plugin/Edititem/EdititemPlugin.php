<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Edititem;

use Molajo\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class EdititemPlugin extends ContentPlugin
{
	/**
	 * Prepares data for the Edit View
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeParse()
	{

		if (APPLICATION_ID == 2) {
		} else {
			return true;
		}

		if (strtolower($this->get('template_view_path_node')) == 'edit') {
		} else {
			return true;
		}

		/** Not authorised and not found */
		if ($this->get('model_type') == ''
			|| $this->get('model_name') == '') {
			return true;
		}
	}
}
