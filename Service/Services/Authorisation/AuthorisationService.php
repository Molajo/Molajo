<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services\Authorisation;

use Molajo\Application;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Authorisation
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class AuthorisationService
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * Registry specific to the AuthorisationService class
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
			self::$instance = new AuthorisationService();
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
		$this->initialise();
	}

	/**
	 * Load ACL-related data for use with Authorisation
	 *
	 * @return null
	 * @since  1.0
	 */
	protected function initialise()
	{
		$actions = Services::Configuration()->getFile('actions', 'Application');
		if (count($actions) == 0) {
			return;
		}

		foreach ($actions->action as $t) {
			Services::Registry()->set('action_to_action', (string)$t['name'], (string)$t['action']);
			Services::Registry()->set('action_to_controller', (string)$t['action'], (string)$t['controller']);
		}

		/** retrieve action key pairs */
		$items = Application::Controller()->connect('Actions')->getData('loadObjectList');
		foreach ($items as $item) {
			Services::Registry()->set('action_to_action_id', $item->title, (int)$item->id);
		}

		return;
	}

	/**
	 * Check if the site is authorised for this application
	 *
	 * Usage:
	 * $results = Services::Authorisation()->authoriseSiteApplication();
	 *
	 * @param  mixed $application_id if valid, or false
	 * @return boolean
	 */
	public function authoriseSiteApplication()
	{
		$m = Application::Controller()->connect('SiteApplications');

		$m->model->query->select($m->model->db->qn('application_id'));
		$m->model->query->where($m->model->db->qn('site_id') . ' = ' . (int)SITE_ID);
		$m->model->query->where($m->model->db->qn('application_id') . ' = ' . (int)APPLICATION_ID);

		$application_id = $m->getData('loadResult');

		if ($application_id === false) {
			//todo: finish the response action/test

			Services::Response()->setHeader('Status', '403 Not Authorised', 'true');

			Services::Message()->set(
				Services::Registry()->get('Configuration', 'error_403_message', 'Not Authorised.'),
				MESSAGE_TYPE_ERROR,
				403
			);
		}

		return $application_id;
	}

	/**
	 * Using the Request Task, retrieve the Controller
	 *
	 * Example usage:
	 * $controller = Services::Authorisation()->getTaskController($action);
	 *
	 * @param $action
	 *
	 * @return string
	 * @since  1.0
	 */
	public function getTaskController($action)
	{
		$action = $this->request->get('action_to_action', $action);
		$controller = $this->request->get('action_to_controller', $action);

		return $controller;
	}

	/**
	 * For the list of actions (actions), determine if the user is authorised for the specific catalog id;
	 * Useful for button bars, links, and other User Interface Presentation Logic
	 *
	 * Example usage:
	 * $permissions = Services::Authorisation()->authoriseTaskList($actionsArray, $item->catalog_id);
	 *
	 * @param  array   $actionlist
	 * @param  string  $catalog_id
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function authoriseTaskList($actionlist = array(), $catalog_id = 0)
	{
		if (count($actionlist) == 0) {
			return false;
		}
		if ($catalog_id == 0) {
			return false;
		}

		$actionPermissions = array();

		foreach ($actionlist as $action) {
			$actionPermissions[$action] = $this->authoriseTask($action, $catalog_id);
		}

		return $actionPermissions;
	}

	/**
	 * Verify user authorization for the Request Action and Catalog ID
	 *
	 * Example usage:
	 * $permissions = Services::Authorisation()->authoriseAction();
	 *
	 * @return   boolean
	 * @since    1.0
	 */
	public function authoriseAction()
	{
		/** 403: authoriseTask handles redirecting to error page */
		if (in_array(Services::Registry()->get('Route', 'catalog_view_group_id'),
			Services::Registry()->get('User', 'ViewGroups'))
			&& in_array(Services::Registry()->get('Route', 'extension_view_group_id'),
				Services::Registry()->get('User', 'ViewGroups'))) {

			Services::Registry()->set('Route', 'status_authorised', true);

		} else {
			return Services::Registry()->set('Route', 'status_authorised', false);
		}

		/** display view verified in getCatalog */
		if (Services::Registry()->get('Route', 'request_action', 'display') == 'display'
			&& Services::Registry()->get('Route', 'status_authorised') == true
		) {
			return true;
		}

		/** verify other actions */
		$authorised = $this->authoriseTask(
			Services::Registry()->get('Route', 'request_action'),
			Services::Registry()->get('Route', 'request_catalog_id')
		);

		Services::Registry()->set('Route', 'status_authorised', $authorised);

		if (Services::Registry()->get('Route', 'status_authorised') === true) {
			return true;

		} else {
			Services::Error()->set(403);
			return false;
		}
	}

	/**
	 * Verifies permission for a user to perform a specific action on a specific catalog number
	 * Could be used to determine if an "Edit Article" link is warranted.
	 *
	 * Example usage:
	 * Services::Authorisation()->authoriseTask($action, $catalog_id);
	 *
	 * @param  string  $action
	 * @param  string  $catalog_id
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function authoriseTask($action, $catalog_id)
	{

		if ($action == 'login') {
			return $this->authoriseLogin('login', $catalog_id);
		}

		/** Retrieve ACL Action for this Task */
		$action = Services::Registry()->get('action_to_action', $action);
		$action_id = Services::Registry()->get('action_to_action_id', $action);

		if (trim($action) == '' || (int)$action_id == 0 || trim($action) == '') {
			Services::Debug()->set(
				'AuthorisationServices::authoriseTask '
					. ' Task: ' . $action
					. ' Action: ' . $action
					. ' Action ID: ' . $action_id
			);
		}

		//todo: amy fill database with real sample action permissions

		/** check for permission */
		$action_id = 3;

		$m = Application::Controller()->connect('GroupPermissions');

		$m->model->query->where($m->model->db->qn('catalog_id') . ' = ' . (int)$catalog_id);
		$m->model->query->where($m->model->db->qn('action_id') . ' = ' . (int)$action_id);
		$m->model->query->where($m->model->db->qn('group_id')
				. ' IN (' . implode(', ', Services::Registry()->get('User', 'Groups')) . ')'
		);

		$count = $m->model->getData('loadResult');

		if ($count > 0) {
			return true;

		} else {
			Services::Debug()->set(
				'AuthorisationServices::authoriseTask No Query Results  '
					. ' Task: ' . $action
					. ' Action: ' . $action
					. ' Action ID: ' . $action_id
			);
			return false;
		}
	}

	/**
	 * authoriseLogin
	 *
	 * Verifies permission for a user to logon to a specific application
	 *
	 * Example usage:
	 * Services::Authorisation()->authoriseLogin('login', $catalog_id);
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

		$m = Application::Controller()->connect('UserApplications');

		$m->model->query->where('application_id = ' . (int)APPLICATION_ID);
		$m->model->query->where('user_id = ' . (int)$user_id);

		$count = $m->model->getData('loadResult');

		if ($count > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 *
	 * Used by queries to append criteria needed to implement view access
	 *
	 * Example usage:
	 *  Services::Authorisation()->setQueryViewAccess(
	 *     $this->query,
	 *     $this->db,
	 *     array('join_to_prefix' => $this->primary_prefix,
	 *         'join_to_primary_key' => Services::Registry()->get($this->table_registry_name, 'primary_key'),
	 *         'catalog_prefix' => $this->primary_prefix . '_catalog',
	 *         'select' => true
	 *     )
	 * );
	 *
	 * @param  array       $query
	 * $param  array       $db
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
	 * $userHTMLFilter = Services::Authorisation()->setHTMLFilter();
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
