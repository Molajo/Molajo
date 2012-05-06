<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services\User;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * User
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class UserService
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
		$m = Services::Model()->connect('Users');

		$m->model->set('id', $this->id);

		$m->model->set('get_special_fields', 1);
		$m->model->set('use_special_joins', false);
		$m->model->set('add_acl_check', false);

		$results = $m->execute('load');

		if ($results === false) {
			throw new \RuntimeException ('Application setSiteData() query problem');
		}

		$first_name = '';
		$last_name = '';

		while (list($name, $value) = each($results)) {

			if (substr($name, 0, 5) == 'Model') {

			} else {

				Services::Registry()->set('User', $name, $value);

				if ($name == 'first_name') {
					$first_name = $value;

				} elseif ($name == 'last_name') {
					$last_name = $value;
				}
			}
		}

		Services::Registry()->set('User', 'name', $first_name . ' ' . $last_name);
		Services::Registry()->set('User', 'administrator', 0);

		if ($this->id == 0) {
			Services::Registry()->set('User', 'public', 1);
			Services::Registry()->set('User', 'guest', 1);
			Services::Registry()->set('User', 'registered', 0);
		} else {
			Services::Registry()->set('User', 'public', 1);
			Services::Registry()->set('User', 'guest', 0);
			Services::Registry()->set('User', 'registered', 1);
		}

		/** User Applications */
		$temp = array();
		$applications = $results['Model\\UserApplications'];
		foreach ($applications as $app) {
			$temp[] = $app->application_id;
		}
		Services::Registry()->set('User', 'Applications', $temp);

		/** User Groups */
		$temp = array();
		$groups = $results['Model\\UserGroups'];
		foreach ($groups as $group) {
			$temp[] = $group->group_id;
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

		Services::Registry()->set('User', 'Groups', $temp);

		if (in_array(SYSTEM_GROUP_ADMINISTRATOR, $temp)) {
			Services::Registry()->set('User', 'administrator', 1);
		} else {
			Services::Registry()->set('User', 'administrator', 0);
		}

		/** User View Groups */
		$temp = array();
		$viewGroups = $results['Model\\UserViewGroups'];
		foreach ($viewGroups as $vg) {
			$temp[] = $vg->view_group_id;
		}

		if (count($temp) == 0) {
			$temp = array(SYSTEM_GROUP_PUBLIC, SYSTEM_GROUP_GUEST);
		}

		Services::Registry()->set('User', 'ViewGroups', $temp);

		/** User Activity */
		$activity = $results['Model\\UserActivity'];
		Services::Registry()->set('User', 'UserActivity', $activity);
		/**
		echo '<pre>';
		echo 'User<br />';
		var_dump(Services::Registry()->get('User'));
		echo '</pre>';

		echo '<pre>';
		echo 'User Customfields<br />';
		var_dump(Services::Registry()->get('UserCustomfields'));
		echo '</pre>';

		echo '<pre>';
		echo 'User Parameters<br />';
		var_dump(Services::Registry()->get('UserParameters'));
		echo '</pre>';

		echo '<pre>';
		echo 'User Metadata<br />';
		var_dump(Services::Registry()->get('UserMetadata'));
		echo '</pre>';
		 **/
		return $this;
	}
}
