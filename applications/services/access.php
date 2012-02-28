<?php
/**
 * @package     Molajo
 * @subpackage  Services
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Access
 *
 * Permissioning
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
class MolajoAccessService
{

    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * $action_to_action_id
     *
     * ACL Action literal to database pk
     *
     * @var    Registry
     * @since  1.0
     */
    protected $action_to_action_id;

    /**
     * $task_to_action
     *
     * Task to ACL Action list
     *
     * @var    Registry
     * @since  1.0
     */
    protected $task_to_action;

    /**
     * $action_to_controller
     *
     * ACL Action to Molajo Controller list
     *
     * @var    Registry
     * @since  1.0
     */
    protected $action_to_controller;

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
            self::$instance = new MolajoAccessService();
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * Class constructor.
     *
     * @since  1.0
     */
    public function __construct()
    {
        $this->_initialize();
    }

    /**
     * _initialize
     *
     * Load lists of ACL-related data needed by this method
     * and other classes within the application
     *
     * @return null
     * @since  1.0
     */
    protected function _initialize()
    {
        /** load task to action and controller data */
        $tasks = simplexml_load_file(
            MOLAJO_APPLICATIONS . '/options/tasks.xml'
        );
        if (count($tasks) == 0) {
            return;
        }

        $this->task_to_action = new Registry();
        $this->action_to_controller = new Registry();

        foreach ($tasks->task as $t) {
            $this->task_to_action
                ->set(
                (string)$t['name'],
                (string)$t['action']
            );
            $this->action_to_controller
                ->set(
                (string)$t['action'],
                (string)$t['controller']
            );
        }

        /** action text to database key */
        $this->action_to_action_id = new Registry();

        /** retrieve database keys for actions */
        $m = new MolajoActionTypesModel();
        $actionsList = $m->loadObjectList();

        foreach ($actionsList as $actionDefinition) {

            $this->action_to_action_id
                ->set(
                $actionDefinition->title,
                (int) $actionDefinition->id
            );
        }

        return;
    }

    /**
     * getTaskController
     *
     * Using the Task, retrieve the Controller
     *
     * @param $task
     *
     * @return string
     * @since  1.0
     */
    public function getTaskController ($task)
    {
        $action = $this->task_to_action->get($task);
        $controller = $this->action_to_controller->get($action);
        return $controller;
    }

    /**
     *  authoriseTaskList
     *
     * @param  array   $tasklist
     * @param  string  $asset_id
     *
     * @return  boolean
     * @since   1.0
     */
    public function authoriseTaskList($tasklist = array(), $asset_id = 0)
    {
        if (count($tasklist) == 0) {
            return false;
        }
        if ($asset_id == 0) {
            return false;
        }

        $taskPermissions = array();
        foreach ($tasklist as $task) {
            $taskPermissions[$task] =
                Services::Access()->authoriseTask($task, $asset_id);
        }
        return $taskPermissions;
    }

    /**
     *  authoriseTask
     *
     * @param  string  $task
     * @param  string  $asset_id
     *
     * @return  boolean
     * @since   1.0
     */
    public function authoriseTask($task, $asset_id)
    {
        if ($task == 'login') {
            return Services::Access()->authoriseLogin('login', $asset_id);
        }

        /** Retrieve ACL Action for this Task */
        $action = $this->task_to_action->get($task);
        $action_id = (int) $this->action_to_action_id->get($action);

        if (trim($action) == '' || (int) $action_id == 0 || trim($action) == '') {
            if (Services::Configuration()->get('debug', 0) == 1) {
                debug('MolajoAccessService::authoriseTask Task: ' . $task . ' Action: ' . $action . ' Action ID: '. $action_id);
            }
        }

        //todo: amy fill database with real actions

        /** check for permission */
        $action_id = 3;

        $m = new MolajoGroupPermissionsModel();

        $m->query->where($m->db->qn('asset_id') . ' = ' . (int)$asset_id);
        $m->query->where($m->db->qn('action_id') . ' = ' . (int)$action_id);
        $m->query->where($m->db->qn('group_id')
                . ' IN (' . implode(',', Services::User()->get('groups')) . ')'
        );

        $count = $m->loadResult();

        if ($count > 0) {
            return true;
        } else {
            if (Services::Configuration()->get('debug', 0) == 1) {
                debug('MolajoAccessService::authoriseTask No query results for Task: ' . $task . ' Action: ' . $action . ' Action ID: '. $action_id);
            }
            return false;
        }
    }

    /**
     * authoriseLogin
     *
     * @param $key
     * @param $action
     *
     * @param null $asset
     * @return void
     */
    public function authoriseLogin($user_id)
    {
        if ((int)$user_id == 0) {
            return false;
        }

        $m = new MolajoUserApplicationsModel();

        $m->query->where('application_id = ' . (int)MOLAJO_APPLICATION_ID);
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
     * @param  array  $query
     * $param  array  $db
     * @param  Registry  $parameters
     *
     * @return     boolean
     * @since      1.0
     */
    public function setQueryViewAccess(
        $query = array(),
        $db = array(),
        $parameters = array())
    {
        if ($parameters['select'] === true) {
            $query->select(
                $db->qn($parameters['asset_prefix']) .
                    '.' .
                    $db->qn('view_group_id')
            );

            $query->select(
                $db->qn($parameters['asset_prefix']) .
                    '.' .
                    $db->qn('id') .
                    ' as ' .
                    $db->qn('asset_id')
            );
        }

        $query->from(
            $db->qn('#__assets') .
                ' as ' .
                $db->qn($parameters['asset_prefix'])
        );

        $query->where(
            $db->qn($parameters['asset_prefix']) .
                '.' .
                $db->qn('source_id') .
                ' = ' .
                $db->qn($parameters['join_to_prefix']) .
                '.' .
                $db->qn($parameters['join_to_primary_key'])
        );

        $query->where(
            $db->qn($parameters['asset_prefix']) .
                '.' . $db->qn('asset_type_id') .
                ' = ' .
                $db->qn($parameters['join_to_prefix']) .
                '.' .
                $db->qn('asset_type_id')
        );

        $query->where(
            $db->qn($parameters['asset_prefix']) .
                '.' .
                $db->qn('view_group_id') .
                ' IN (' . implode(',',
                Services::User()
                    ->get('view_groups')) .
                ')'
        );

        $query->where(
            $db->qn($parameters['asset_prefix']) .
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
     * @return bool
     * @since  1.0
     */
    public function setHTMLFilter ()
    {
        $groups = Services::Configuration()->get('disable_filter_for_groups');
        $groupArray = explode(',', $groups);
        $userGroups = Services::User()->get('groups');

        foreach ($groupArray as $single) {

            if (in_array($single, $userGroups)) {
                return false;
                break;
            }
        }
        return true;
    }
}
