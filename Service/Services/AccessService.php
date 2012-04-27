<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services;

use Molajo\Service\Services;

use Molajo\MVC\Model\TableModel;

defined('MOLAJO') or die;

/**
 * Access
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class AccessService
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * Registry specific to the AccessService class
	 *
	 * @var    Registry
	 * @since  1.0
	 */
	protected $registry;

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
			self::$instance = new AccessService();
		}
		return self::$instance;
	}

	/**
	 * Class constructor.
	 *
	 * @since  1.0
	 */
	public function __construct()
	{
		$this->_initialise();
	}

	/**
	 * Load lists of ACL-related data needed by this method
	 * and other classes within the application
	 *
	 * @return null
	 * @since  1.0
	 */
	protected function _initialise()
	{
		$this->registry = new RegistryService();

		$tasks = $this->registry->loadFile('tasks');
		if (count($tasks) == 0) {
			return;
		}

		$this->registry->createRegistry('task_to_action');
		$this->registry->createRegistry('action_to_controller');

		foreach ($tasks->task as $t) {
			$this->registry->set('task_to_action', (string)$t['name'], (string)$t['action']);
			$this->registry->set('action_to_controller', (string)$t['action'], (string)$t['controller']);
		}

		/** action text to database key */
		$this->registry->createRegistry('action_to_action_id');

		/** retrieve database keys for actions */
		$m = new TableModel('Actions');
		$actionsList = $m->loadObjectList();

		foreach ($actionsList as $actionDefinition) {
			$this->registry->set('task_to_action', $actionDefinition->title, (int)$actionDefinition->id);
		}

		return;
	}

	/**
	 * authorise
	 *
	 * Check if the site is authorized for this application
	 *
	 * @param  mixed $application_id if valid, or false
	 * @return boolean
	 */
	public function authoriseSiteApplication()
	{
		$m = new TableModel('SiteApplications');

		$m->query->select($m->db->qn('application_id'));
		$m->query->where($m->db->qn('site_id') . ' = ' . (int)SITE_ID);
		$m->query->where($m->db->qn('application_id') . ' = ' . (int)APPLICATION_ID);

		$application_id = $m->loadResult();

		if ($application_id === false) {
			//todo: finish the response action/test

			Services::Response()
				->setHeader('Status', '403 Not Authorised', 'true');

			Services::Message()->set(
				Services::Registry()
					->get('Configuration', 'error_403_message', 'Not Authorised.'),
							MESSAGE_TYPE_ERROR,
							403
						);
		}

		return $application_id;
	}

	/**
	 * getTaskController
	 *
	 * Using the Task, retrieve the Controller
	 *
	 * Example usage:
	 * Services::Access()->getTaskController($this->get('mvc_task')
	 *
	 * @param $task
	 *
	 * @return string
	 * @since  1.0
	 */
	public function getTaskController($task)
	{
		$action = $this->request->get('task_to_action', $task);
		$controller = $this->request->get('action_to_controller', $action);

		return $controller;
	}

	/**
	 * authoriseTaskList
	 *
	 * Example usage:
	 * $permissions = Services::Access()->authoriseTaskList($tasksArray, $item->catalog_id);
	 *
	 * @param  array   $tasklist
	 * @param  string  $catalog_id
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function authoriseTaskList($tasklist = array(), $catalog_id = 0)
	{
		if (count($tasklist) == 0) {
			return false;
		}
		if ($catalog_id == 0) {
			return false;
		}

		$taskPermissions = array();

		foreach ($tasklist as $task) {
			$taskPermissions[$task] =
				Services::Access()
					->authoriseTask($task, $catalog_id);
		}
		return $taskPermissions;
	}

	/**
	 * authoriseTask
	 *
	 * Verifies permission for a user to perform a specific task
	 * on a specific catalog
	 *
	 * Example usage:
	 * Services::Access()->authoriseTask($task, $catalog_id);
	 *
	 * @param  string  $task
	 * @param  string  $catalog_id
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function authoriseTask($task, $catalog_id)
	{
		if ($task == 'login') {
			return Services::Access()->authoriseLogin('login', $catalog_id);
		}

		/** Retrieve ACL Action for this Task */
		$action = $this->registry->get('task_to_action', $task);
		$action_id = $this->registry->get('action_to_action_id', $action);

		if (trim($action) == '' || (int)$action_id == 0 || trim($action) == '') {
			if (Services::Registry()->get('Configuration', 'debug', 0) == 1) {
				Services::Debug()
					->set('AccessServices::authoriseTask Task: ' . $task
					. ' Action: ' . $action . ' Action ID: ' . $action_id);
			}
		}
		//todo: amy fill database with real sample action permissions

		/** check for permission */
		$action_id = 3;

		$m = new TableModel('GroupPermissions');

		$m->query->where($m->db->qn('catalog_id') . ' = ' . (int)$catalog_id);
		$m->query->where($m->db->qn('action_id') . ' = ' . (int)$action_id);
		$m->query->where($m->db->qn('group_id')
				. ' IN (' . implode(', ', Services::Registry()->get('User', 'Groups')) . ')'
				);

		$count = $m->loadResult();

		if ($count > 0) {
			return true;

		} else {
			if (Services::Registry()->get('Configuration', 'debug', 0) == 1) {
				Services::Debug()->set('AccessServices::authoriseTask No query results for Task: ' . $task
					. ' Action: ' . $action . ' Action ID: ' . $action_id);
			}
			return false;
		}
	}

	/**
	 * authoriseLogin
	 *
	 * Verifies permission for a user to logon to a specific application
	 *
	 * Example usage:
	 * Services::Access()->authoriseLogin('login', $catalog_id);
	 *
	 * @param $key
	 * @param $action
	 *
	 * @param null $catalog
	 * @return bool
	 */
	public function authoriseLogin($user_id)
	{
		if ((int)$user_id == 0) {
			return false;
		}

		$m = new TableModel('UserApplications');

		$m->query->where('application_id = ' . (int)APPLICATION_ID);
		$m->query->where('user_id = ' . (int)$user_id);

		$count = $m->loadResult();

		if ($count > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 *  setQueryViewAccess
	 *
	 *  Append criteria needed to implement view access for Query
	 *
	 * Example usage:
	 *  Services::Access()->setQueryViewAccess(
	 *     $this->query,
	 *     $this->db,
	 *     array('join_to_prefix' => $this->primary_prefix,
	 *         'join_to_primary_key' => $this->primary_key,
	 *         'catalog_prefix' => $this->primary_prefix . '_catalog',
	 *         'select' => true
	 *     )
	 * );
	 *
	 * @param  array  $query
	 * $param  array  $db
	 * @param  Registry  $parameters
	 *
	 * @return     boolean
	 * @since      1.0
	 */
	public function setQueryViewAccess($query = array(), $db = array(), $parameters = array())
	{
		if ($parameters['select'] === true) {
			$query->select(
				$db->qn($parameters['catalog_prefix']) .
					'.' .
					$db->qn('view_group_id')
			);

			$query->select(
				$db->qn($parameters['catalog_prefix']) .
					'.' .
					$db->qn('id') .
					' as ' .
					$db->qn('catalog_id')
			);
		}

		$query->from(
			$db->qn('#__catalog') .
				' as ' .
				$db->qn($parameters['catalog_prefix'])
		);

		$query->where(
			$db->qn($parameters['catalog_prefix']) .
				'.' .
				$db->qn('source_id') .
				' = ' .
				$db->qn($parameters['join_to_prefix']) .
				'.' .
				$db->qn($parameters['join_to_primary_key'])
		);

		$query->where(
			$db->qn($parameters['catalog_prefix']) .
				'.' . $db->qn('catalog_type_id') .
				' = ' .
				$db->qn($parameters['join_to_prefix']) .
				'.' .
				$db->qn('catalog_type_id')
		);

		$vg = implode(', ', Services::Registry()->get('User', 'ViewGroups'));
		$query->where(
			$db->qn($parameters['catalog_prefix']) .
				'.' .
				$db->qn('view_group_id') . ' IN (' . $vg . ')'
		);

		$query->where(
			$db->qn($parameters['catalog_prefix']) .
				'.' .
				$db->qn('redirect_to_id') .
				' = 0');

		return $query;
	}

	/**
	 * setHTMLFilter
	 *
	 * Returns false if there is one group that the user belongs to
	 *  authorized to save content without an HTML filter, otherwise
	 *  it returns true
	 *
	 * Example usage:
	 * $userHTMLFilter = Services::Access()->setHTMLFilter();
	 *
	 * @return bool
	 * @since  1.0
	 */
	public function setHTMLFilter()
	{
		$groups = Services::Registry()->get('Configuration', 'disable_filter_for_groups');
		$groupArray = explode(',', $groups);
		$userGroups = Services::Registry()->get('User', 'groups');

		foreach ($groupArray as $single) {

			if (in_array($single, $userGroups)) {
				return false;
				break;
			}
		}
		return true;
	}
}
