<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Menuitemtypes;

use Molajo\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class MenuitemtypesPlugin extends ContentPlugin
{
	/**
	 * Generates list of Datalists for use in defining Custom Fields of Type Selectlist
	 *
	 * This can be moved to onBeforeParse when Plugin ordering is in place
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRoute()
	{
		$folders = Services::Filesystem()->folderFolders(
			EXTENSIONS . '/Menuitem'
		);

		if (count($folders) === 0 || $folders === false) {
			$menuitemLists = array();
		} else {
			$menuitemsLists = $folders;
		}

		$resourceFolders = Services::Filesystem()->folderFolders(
			Services::Registry()->get('Parameters', 'extension_path') . '/Menuitem'
		);

		if (count($resourceFolders) === 0 || $resourceFolders === false) {
			$resourceLists = array();
		} else {
			$resourceLists = $resourceFolders;
		}

		$new = array_merge($menuitemsLists, $resourceLists);

		$newer = array_unique($new);
		sort($newer);

		$menuitems = array();
		foreach ($newer as $item) {
			$row = new \stdClass();
			$row->value = $item;
			$row->id = $item;
			$menuitems[] = $row;
		}

		Services::Registry()->set('Datalist', 'Menuitemtypes', $menuitems);

		return true;
	}
}
