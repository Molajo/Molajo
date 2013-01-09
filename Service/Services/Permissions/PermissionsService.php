<?php
/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Service\Services\Permissions;

use Molajo\Frontcontroller;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

//@todo remove hard-coded prefixes (a.) and replace with prefixes defined in model

/**
 * Permissions
 *
 * @package     Niambie
 * @subpackage  Services
 * @since       1.0
 */
Class PermissionsService
{
    /**
     * Actions used to establish permissions
     *
     *  [0]=> "none" [1]=> "login" [2]=> "create" [3]=> "read"
     *  [4]=> "update" [5]=> "publish" [6]=> "delete" [7]=> "administer"
     *
     * @var    bool
     * @since  1.0
     */
    protected $actions;

    /**
     * Action to Authorisation
     *
     * @var    bool
     * @since  1.0
     */
    protected $action_to_authorisation;

    /**
     * Action to Controller
     *
     * @var    bool
     * @since  1.0
     */
    protected $action_to_controller;

    /**
     * Tasks (Order up, Order down, Feature, etc.)
     *
     * @var    bool
     * @since  1.0
     */
    protected $task;

    /**
     * Action to Authorisation ID
     *
     * @var    bool
     * @since  1.0
     */
    protected $action_to_authorisation_id;

    /**
     * Filter Authorisation
     *
     * @var    bool
     * @since  1.0
     */
    protected $filters;

    /**
     * User View Groups
     *
     * @var    bool
     * @since  1.0
     */
    protected $user_view_groups;

    /**
     * User Groups
     *
     * @var    bool
     * @since  1.0
     */
    protected $user_groups;

    /**
     * Disable Filter for Groups
     *
     * @var    bool
     * @since  1.0
     */
    protected $disable_filter_for_groups;

    /**
     * List of Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $property_array = array(
        'actions',
        'action_to_authorisation',
        'action_to_controller',
        'tasks',
        'action_to_authorisation_id',
        'filters',
        'site_application',
        'task_action',
        'action_to_controller',
        'user_view_groups',
        'user_groups',
        'disable_filter_for_groups'
    );

    /**
     * Get language property
     *
     * @param   string  $key
     * @param   string  $default
     *
     * @return  array|mixed|string
     * @throws  \OutOfRangeException
     * @since   1.0
     */
    public function get($key, $default = '')
    {
        $key = strtolower($key);

        if ($key == 'site_application') {
            return $this->verifySiteApplication();
        }

        if ($key == 'task_action') {
            return $this->getTaskAction($default);
        }

        if ($key == 'action_to_controller') {
            return $this->getTaskController($default);
        }

        if (in_array($key, $this->property_array)) {

            if (isset($this->$key)) {
            } else {
                $this->$key = $default;
            }

            return $this->$key;
        }

        throw new \OutOfRangeException
        ('Permissions Service: attempting to get value for unknown property: ' . $key);
    }

    /**
     * Set the value of the specified key
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed
     * @since   1.0
     * @throws  \OutOfRangeException
     */
    public function set($key, $value = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
            $this->$key = $value;

            return $this->$key;
        }

        throw new \OutOfRangeException
        ('Permissions Service: attempting to get value for unknown property: ' . $key);
    }

    /**
     * Verifies Permissions for a set of Actions for the specified Catalog ID
     *      Useful for question "What can the logged on User do with this set of Articles (or Article)?"
     *
     * Example usage:
     * $permissions = Services::Permissions()->verifyTaskList($actionsArray, $item->catalog_id);
     *
     * @param   array  $actionlist
     * @param   int    $catalog_id
     *
     * @return  array
     * @since   1.0
     * @throws  \Exception
     */
    public function verifyTaskList($actionlist = array(), $catalog_id = 0)
    {
        if (count($actionlist) == 0) {
            throw new \Exception('Permissions: Empty Action List sent into verifyTasklist');
        }
        if ($catalog_id == 0) {
            throw new \Exception('Permissions: No Catalog ID sent into verifyTaskList');
        }

        $actionPermissions = array();
        foreach ($actionlist as $action) {
            $actionPermissions[$action] = $this->verifyTask($action, $catalog_id);
        }

        return $actionPermissions;
    }

    /**
     * Check if the Site has permission to utilise this Application
     *
     * Usage:
     * $results = Services::Permissions()->get('site_application');
     *
     * @return  boolean
     * @since   1.0
     */
    protected function verifySiteApplication()
    {
        $controllerClass = CONTROLLER_CLASS_NAMESPACE;
        $controller      = new $controllerClass();
        $controller->getModelRegistry('Datasource', 'Siteapplications', 1);

        $controller->model->query->
            select($controller->model->db->qn('a.application_id'));
        $controller->model->query->
            where($controller->model->db->qn('a.site_id') . ' = ' . (int)SITE_ID);
        $controller->model->query->
            where($controller->model->db->qn('a.application_id') . ' = ' . (int)APPLICATION_ID);

        return $controller->getData(QUERY_OBJECT_RESULT);
    }

    /**
     * Using the Request Task (Verb Action, like Tag, or Order Up), retrieve the Permissions Action (ex. Update)
     *
     * Example usage:
     *  $results = Services::Permissions()->get('task_action', $task);
     *
     * @param   string  $task
     *
     * @return  string
     * @since   1.0
     * @throws  \Exception
     */
    protected function getTaskAction($task)
    {
        if (isset($this->action_to_authorisation[$task])) {
            return $this->action_to_authorisation[$task];
        }

        throw new \Exception ('Permissions: Task not defined by Permission Registry');
    }

    /**
     * Using the Request Task (Verb Action, like Tag, or Order Up), retrieve the Controller
     *
     * Example usage:
     *  $results = Services::Permissions()->get('action_to_controller', $action);
     *
     * @param   string  $action
     *
     * @return  string
     * @since   1.0
     * @throws  \Exception
     */
    protected function getTaskController($action)
    {
        if (isset($this->action_to_controller[$action])) {
            return $this->action_to_controller[$action];
        }

        throw new \Exception ('Permissions: Action not defined by Permission Registry');
    }

    /**
     * Verify User Permissions for the Action and Catalog ID
     *
     * Example usage:
     *  $permissions = Services::Permissions()->verifyAction($view_group_id, $request_action, $catalog_id);
     *
     * @param   string  $view_group_id
     * @param   string  $request_action
     * @param   string  $catalog_id
     *
     * @return  bool
     * @since   1.0
     * @throws  \Exception
     */
    public function verifyAction($view_group_id, $request_action, $catalog_id)
    {
        $status_authorised = true;

        if (in_array($view_group_id, $this->get('user_view_groups'))) {

        } else {
            $status_authorised = false;
        }

        if ($request_action == ACTION_READ) {

        } elseif ($this->verifyTask($request_action, $catalog_id) === false) {
            $status_authorised = false;
        }

        if ($status_authorised === true) {
            return true;
        }

        return false;
    }

    /**
     * Verifies permission for a user to perform a specific action on a specific catalog id
     *      Useful for question "Can the logged on User Edit this Article (or content in this Resource)?"
     *
     * Example usage:
     *  Services::Permissions()->verifyTask($action, $catalog_id);
     *
     * @param   string  $action
     * @param   string  $catalog_id
     *
     * @return  boolean
     * @since   1.0
     * @throws  \Exception
     */
    public function verifyTask($action, $catalog_id)
    {
        if ($action == 'login') {
            return $this->verifyLogin('login', $catalog_id);
        }

        //@todo
        return true;

//@todo hash store results for later reuse

        $action_to_authorisation    = $this->get('action_to_authorisation');
        $action_to_authorisation_id = $this->get('action_to_authorisation_id');

        $action    = $action_to_authorisation[$action];
        $action_id = $action_to_authorisation_id[$action];

        if (trim($action) == '' || (int)$action_id == 0 || trim($action) == '') {
            throw new \Exception
            ('Permission Services: Required value Action not provided for verifyTask method.');
        }

        if (trim($catalog_id) == '' || (int)$catalog_id == 0 || trim($catalog_id) == '') {
            throw new \Exception
            ('Permission Services: Required value Catalog ID not provided for verifyTask method.');
        }

        $action_id = 3;

        $controllerClass = CONTROLLER_CLASS_NAMESPACE;
        $controller      = new $controllerClass();
        $controller->getModelRegistry('Datasource', 'Grouppermissions', 1);

        $controller->model->query->select(
            $controller->model->db->qn('a.id')
        );
        $controller->model->query->where(
            $controller->model->db->qn('a.catalog_id') . ' = ' . (int)$catalog_id
        );
        $controller->model->query->where(
            $controller->model->db->qn('a.action_id') . ' = ' . (int)$action_id
        );
        $controller->model->query->where(
            $controller->model->db->qn('a.group_id')
                . ' IN (' . implode(', ', $this->get('user_groups')) . ')'
        );

        $count = $controller->getData(QUERY_OBJECT_RESULT);
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Verifies permission for a user to login to a specific application
     *
     * Example usage:
     *  Services::Permissions()->verifyLogin($user_id);
     *
     * @param   int  $user_id
     *
     * @return  bool
     * @since   1.0
     */
    public function verifyLogin($user_id)
    {
        if ((int)$user_id == 0) {
            return false;
        }

        $controllerClass = CONTROLLER_CLASS_NAMESPACE;
        $controller      = new $controllerClass();
        $controller->getModelRegistry('Datasource', 'Userapplications', 1);

        $controller->model->query->where('a.application_id = ' . (int)APPLICATION_ID);
        $controller->model->query->where('a.user_id = ' . (int)$user_id);

        $count = $controller->model->getData(QUERY_OBJECT_RESULT);

        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Appends View Access criteria to Query when Model check_view_level_access is set to 1
     *
     * Example usage:
     *  Services::Permissions()->setQueryViewAccess(
     *     $this->query,
     *     $this->db,
     *     array('join_to_prefix' => $this->primary_prefix,
     *         'join_to_primary_key' => Services::Registry()->get($this->model_registry, 'primary_key'),
     *         'catalog_prefix' => $this->primary_prefix . '_catalog',
     *         'select' => true
     *     )
     * );
     *
     * @param   array  $query
     * @param   array  $db
     * @param   array  $parameters
     *
     * @return  array
     * @since   1.0
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

        $query->where(
            $db->qn($parameters['catalog_prefix']) .
                '.' . $db->qn('application_id') .
                ' = ' .
                APPLICATION_ID
        );

        $vg = implode(',', array_unique($this->get('user_view_groups')));

        $query->where(
            $db->qn($parameters['catalog_prefix']) .
                '.' .
                $db->qn('view_group_id') . ' IN (' . $vg . ')'
        );

        $query->where(
            $db->qn($parameters['catalog_prefix']) .
                '.' .
                $db->qn('redirect_to_id') .
                ' = 0'
        );

        return $query;
    }

    /**
     * Determines if User Content must be filtered
     *
     * Example usage:
     * $userHTMLFilter = Services::Permissions()->setHTMLFilter();
     *
     *  True => disable filter
     *
     *  False => Filter is not required
     *
     * @return  bool
     * @since   1.0
     */
    public function setHTMLFilter()
    {
        foreach ($this->get('disable_filter_for_groups') as $single) {
            if (in_array($single, $this->get('user_groups'))) {
                return false;
                break;
            }
        }

        return true;
    }
}
