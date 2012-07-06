<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Trigger\Username;

use Molajo\Extension\Trigger\Content\ContentTrigger;

defined('MOLAJO') or die;

/**
 * Username
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class UsernameTrigger extends ContentTrigger
{

	/**
	 * Pre-create processing
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onBeforeCreate()
	{
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
		return true;
	}

	/**
	 * Post-update processing
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onAfterUpdate()
	{
		return true;
	}
}
