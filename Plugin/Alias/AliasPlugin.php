<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Alias;

use Molajo\Plugin\Plugin\Plugin;

defined('MOLAJO') or die;

/**
 * Alias
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class AliasPlugin extends Plugin
{
	/**
	 * Pre-create processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeCreate()
	{
		//unique
		return true;
	}

	/**
	 * Pre-update processing
	 *
	 * @param   $this->data
	 * @param   $model
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeUpdate()
	{
		//reserved words - /edit
		return true;
	}
}
