<?php
/**
 * @package     Molajo
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\MVC\Model;

namespace Molajo\Extension\Module;

defined('MOLAJO') or die;

/**
 * AdminToolbar
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
Class ModuleAdminToolbarModel extends DisplayModel
{
	/**
	 * __construct
	 *
	 * Constructor.
	 *
	 * @param  $config
	 * @since  1.0
	 */
	public function __construct($table = null, $id = null, $path = null)
	{
		$this->name = get_class($this);

		return parent::__construct($table, $id, $path);
	}

	/**
	 * getData
	 *
	 * @return   array
	 * @since    1.0
	 */
	public function getData()
	{
		/** Component Buttons */
		$buttons = Services::Registry()->get('ExtensionParameters', 'toolbar_buttons');
		$buttonArray = explode(',', $buttons);

		/** User Permissions */
		$permissions = Services::Authorisation()
			->authoriseTaskList(
			$buttonArray,
			Services::Registry()->parameters->get('ExtensionParameters', 'display_extension_catalog_id')
		);

		$this->data = array();

		foreach ($buttonArray as $buttonname) {

			if ($permissions[$buttonname] === true) {

				$row = new \stdClass();

				$row->title = Services::Registry()->get('ExtensionParameters', 'display_title');

				$row->name = Services::Language()->translate(strtoupper('TASK_' . $buttonname . '_BUTTON'));

				$row->option = Services::Registry()->get('ExtensionParameters', 'display_extension_option');

				$row->task = $buttonname;

				$row->link = 'index.php?id=' . (int)$row->catalog_id . '&task=' . $row->task;

				$this->data[] = $row;
			}
		}
		return $this->data;
	}
}
