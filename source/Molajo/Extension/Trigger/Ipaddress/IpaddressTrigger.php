<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see license folder
 */
namespace Molajo\Extension\Trigger\Ipaddress;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Controller\CreateController;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * IP Address
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class IpaddressTrigger extends ContentTrigger
{
	/**
	 * Pre-create processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeCreate()
	{
		$fields = $this->retrieveFieldsByType('ip_address');

		if (is_array($fields) && count($fields) > 0) {
			foreach ($fields as $field) {
				$this->saveField($field, $field->name, Services::Registry()->get('Client', 'ip_address', ''));
			}
		}

		return true;
	}

	/**
	 * Pre-update processing
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onBeforeUpdate()
	{
		// No updates allowed for activity
		return true;
	}
}
