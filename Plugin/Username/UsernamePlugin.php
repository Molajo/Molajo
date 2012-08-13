<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Username;

use Molajo\Plugin\Content\ContentPlugin;

defined('MOLAJO') or die;

/**
 * Username
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class UsernamePlugin extends ContentPlugin
{

	/**
	 * Pre-create processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeCreate()
	{
		return true;
	}

	/**
	 * Pre-update processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeUpdate()
	{
		return true;
	}

	/**
	 * Post-update processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterUpdate()
	{
		return true;
	}
}
