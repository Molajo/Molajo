<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\ItemUserPermissions;

use Molajo\Extension\Trigger\Content\ContentTrigger;

defined('MOLAJO') or die;

/**
 * Item Snippet
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class ItemUserPermissionsTrigger extends ContentTrigger
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * getInstance
	 *
	 * @static
	 * @return bool|object
	 * @since  1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new ItemUserPermissionsTrigger();
		}
		return self::$instance;
	}

	/**
	 * After-read processing
	 *
	 * Use with Grid to determine permissions for buttons and items
	 * Validate task-level user permissions on each row - relies upon catalog_id
	 *
	 * @param   $data
	 * @param   $model
	 *
	 * @return  $data
	 * @since   1.0
	 */
	public function onAfterRead($data, $model)
	{
		if (isset($data->catalog_id)) {
		} else {
			return $data;
		}

		/** Component Buttons */
		$tasks = Services::Registry() - get('ExtensionParameters', 'toolbar_buttons');

		$tasksArray = explode(',', $tasks);

		/** User Permissions */
		$permissions = Services::Authorisation()
			->authoriseTaskList($tasksArray, $data->catalog_id);

		/** Append onto row */
		foreach ($tasksArray as $task) {
			if ($permissions[$task] === true) {
				$field = $task . 'Permission';
				$data->$field = $permissions[$task];
			}
		}

		return;
	}
}
