<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Trigger\Collection;

use Molajo\Extension\Trigger\Content\ContentTrigger;

defined('MOLAJO') or die;

/**
 * Full name
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class CollectionTrigger extends ContentTrigger
{

	/**
	 * Adds full_name to recordset containing first_name and last_name
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		return true;
	}
}
