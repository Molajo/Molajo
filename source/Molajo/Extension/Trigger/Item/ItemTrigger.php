<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Trigger\Item;

use Molajo\Service\Services;
use Molajo\Extension\Trigger\Content\ContentTrigger;

defined('MOLAJO') or die;

/**
 * Item
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class ItemTrigger extends ContentTrigger
{

	/**
	 * Prepares Data for non-menuitem single content item requests
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onBeforeParse()
	{
		if (Services::Registry()->exists('Parameters', 'menuitem_id')) {
			if ((int) Services::Registry()->get('Parameters', 'menuitem_id') == 0) {
			} else {
				return true;
			}
		}

		if (Services::Registry()->exists('Parameters', 'content_id')) {
			if ((int) Services::Registry()->get('Parameters', 'content_id') == 0) {
				return true; // request for list;
			} else {
				// request for item is handled by this method
			}
		}

		/** Sets primary request model to use the PrimaryRequestQueryResults (created in Route ContentHelper) */
		$this->set('model_name', 'Triggerdata');
		$this->parameters['model_name'] = 'Triggerdata';
		$this->set('model_type', 'dbo');
		$this->parameters['model_type'] = 'dbo';
		$this->set('model_query_object', 'getTriggerdata');
		$this->set('model_parameter', 'PrimaryRequestQueryResults');

		return true;
	}
}
