<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Extension\Plugin\Menuitemtype;

use Molajo\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class MenuitemtypePlugin extends ContentPlugin
{
	/**
	 * Prepares data for the Administrator Grid  - run MenuitemtypePlugin after AdminmenuPlugin
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterReadall()
	{

		return true;

		$folders = Services::Filesystem()->folders(BASE_FOLDER . 'Extension\Menuitem');

echo '<pre>';
var_dump($folders);
echo '</pre>';
		 die;
		return true;
	}
}
