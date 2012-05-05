<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger;

defined('MOLAJO') or die;

/**
 * Item Author
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class ListTrigger extends ContentTrigger
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
			self::$instance = new ListTrigger();
		}
		return self::$instance;
	}

	/**
     * After-read processing
	 *
	 * Retrieves Author Information for Item
     *
     * @param   $data
     * @param   $model
     *
     * @return  $data
     * @since   1.0
     */
    public function onAfterRead($data, $model)
    {
		$lists = Services::Registry()->get('ExtensionParameters', 'Lists');

		if ($lists === null
			|| count($lists) == 0) {
			return;
		}

		foreach ($lists as $item) {

			$list = Services::Configuration()->loadFile($item);

			$name = (string) $list->name;
			$table = (string) $list->table;
			$key = (string) $list->key;
			$value = (string) $list->value;
			$ordering = (string) $list->ordering;
			$selected = (string) $list->selected;
			$catalog_type_id = (string) $list->catalog_type_id;
			$extension_catalog_type_id = (string) $list->extension_catalog_type_id;
			$view_access = (string) $list->view_access;
			$registry = (string) $list->registry;
			$userid = (string) $list->userid;
			$trigger = (string) $list->trigger;

			if (trim($trigger) == '') {
				$m = Services::Model()->connect($table);

				$m->model->set('id', $data->created_by);

				$m->model->set('get_special_fields', 2);
				$m->model->set('use_special_joins', false);
				$m->model->set('add_acl_check', false);
				$m->model->set('get_item_children', false);

				$results = $m->execute('load');

			} else {
				$results = Services::Trigger()->get($trigger);
			}



		}

		return;
    }
}
