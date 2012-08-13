<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Ajax;

use Molajo\Helpers;
use Molajo\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class AjaxPlugin extends ContentPlugin
{
	/**
	 * Identify Ajax Request (run last in onBeforeParse):
	 *    Adapt the Parse Include File Parameters to only generate the Request
	 *     Adapt the Template and Wrap Parameters to generate consumable output
	 *
	 * @return void
	 * @since   1.0
	 */
	public function onBeforeParse()
	{
		if (APPLICATION_ID == 2) {
		} else {
			return true;
		}

		if (Services::Registry()->get('Client', 'Ajax') == 0) {
			return true;
		}

		/** Template  */
		Services::Registry()->set('Parameters', 'template_view_id', 1342);
		Helpers::View()->get(1342, 'Template');

		/** Wrap  */
		Services::Registry()->set('Parameters', 'wrap_view_id', 2090);
		Helpers::View()->get(2090, 'Wrap');

		/** Ajax Parser */
		Services::Registry()->set('Override', 'sequence_xml', 'Ajaxpage');
		Services::Registry()->set('Override', 'final_xml', 'Ajaxfinal');

		return true;
	}
}
