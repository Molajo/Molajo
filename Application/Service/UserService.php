<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

use Molajo\Application\Services;
use Molajo\Application\MVC\Model\ItemModel;

defined('MOLAJO') or die;

/**
 * User Class
 *
 * @package   Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class UserService extends BaseService
{
	/**
	 * Instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instances = array();

	/**
	 * getInstance
	 *
	 * @param   string $identifier  Requested User (id or username or 0 for guest)
	 *
	 * @return  object  User
	 * @since   1.0
	 */
	public static function getInstance($id = 0)
	{
		$id = 42;
		if (empty(self::$instances[$id])) {
			$user = new UserService($id);
			self::$instances[$id] = $user;
		}
		return self::$instances[$id];
	}

	/**
	 * __construct
	 *
	 * @param   integer  $identifier
	 *
	 * @return  object
	 * @since   1.0
	 */
	protected function __construct($id = 0)
	{
		$this->id = (int)$id;
		return $this->load();
	}

	/**
	 * load
	 *
	 * Retrieve User Information (both authenticated and guest)
	 *
	 * @return  User
	 * @since   1.0
	 */
	protected function load()
	{
		$m = new ItemModel ('Users', $this->id);
		$results = $m->load();

		$first_name = '';
		$last_name = '';

		while (list($name, $value) = each($results)) {

			if ($name == 'parameters'
				|| $name == 'custom_fields'
				|| $name == 'metadata'
				|| substr($name, 0, 5) == 'Model'
			) {
			} else {
				Services::Registry()->set('User\\' . $name, $value);
				if ($name == 'first_name') {
					$first_name = $value;
				} elseif ($name == 'last_name') {
					$last_name = $value;
				}
			}
		}

		Services::Registry()->set('User\\name', $first_name . ' ' . $last_name);
		Services::Registry()->set('User\\administrator', 0);

		if ($this->id == 0) {
			Services::Registry()->set('User\\public', 1);
			Services::Registry()->set('User\\guest', 1);
			Services::Registry()->set('User\\registered', 0);
		} else {
			Services::Registry()->set('User\\public', 1);
			Services::Registry()->set('User\\guest', 0);
			Services::Registry()->set('User\\registered', 1);
		}

		$v = simplexml_load_file(
			APPLICATIONS_MVC
				. '/Model/Table/'
				. substr($m->table_name, 3, 99)
				. '.xml'
		);

		$this->registry('UserCustomFields\\', $results, 'custom_fields', 'custom_field', $v);
		$this->registry('UserMetadata\\', $results, 'metadata', 'meta', $v);
		$this->registry('UserParameters\\', $results, 'parameters', 'parameter', $v);

		/** User Applications */
		$temp = array();
		$applications = $results['Model\\UserApplications'];
		while (list($name, $value) = each($applications)) {
			if ($name == 'application_id') {
				$temp[] = $value;
			}
		}
		Services::Registry()->set('User\\applications', $temp);

		/** User Groups */
		$temp = array();
		$groups = $results['Model\\UserGroups'];
		while (list($name, $value) = each($groups)) {
			if ($name == 'group_id') {
				$temp[] = $value;
			}
		}

		if (in_array(SYSTEM_GROUP_PUBLIC, $temp)) {
		} else {
			$temp[] = SYSTEM_GROUP_PUBLIC;
		}

		if ($this->id == 0) {
			$temp[] = SYSTEM_GROUP_GUEST;
		} else {
			if (in_array(SYSTEM_GROUP_REGISTERED, $temp)) {
			} else {
				$temp[] = SYSTEM_GROUP_REGISTERED;
			}
		}

		Services::Registry()->set('User\\groups', $temp);

		if (in_array(SYSTEM_GROUP_ADMINISTRATOR, $temp)) {
			Services::Registry()->set('User\\administrator', 1);
		} else {
			Services::Registry()->set('User\\administrator', 0);
		}

		/** User View Groups */
		$temp = array();
		$vgroups = $results['Model\\UserViewGroups'];
		while (list($name, $value) = each($vgroups)) {
			if ($name == 'view_group_id') {
				$temp[] = $value;
			}
		}

		if (count($temp) == 0) {
			$temp = array(SYSTEM_GROUP_PUBLIC, SYSTEM_GROUP_GUEST);
		}
		Services::Registry()->set('User\\view_groups', $temp);

		$temp = Services::Registry()->getArray('User');

		return $this;
	}

	/**
	 * registry
	 *
	 * @param $namespace
	 * @param $source
	 * @param $field_group
	 * @param $field_name
	 * @param $v
	 */
	protected function registry($namespace, $source, $field_group, $field_name, $v)
	{
		$registry = Services::Registry()->initialise();
		$registry->loadString($source[$field_group], 'JSON');

		if (isset($v->$field_group->$field_name)) {
			foreach ($v->$field_group->$field_name as $cf) {

				$name = (string)$cf['name'];
				$dataType = (string)$cf['filter'];
				$null = (string)$cf['null'];
				$default = (string)$cf['default'];
				$values = (string)$cf['values'];

				if ($default == '') {
					$val = $registry->get($name, null);
				} else {
					$val = $registry->get($name, $default);
				}

				Services::Registry()->set($namespace . $name, $val);
			}
		}
	}
}
