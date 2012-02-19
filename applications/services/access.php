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
 * Asset permissioning verification
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
class MolajoAccessService
{
    /**
     * $task_to_action
     *
     * Task to ACL Action list
     *
     * @var    Registry
     * @since  1.0
     */
    public $task_to_action;

    /**
     * $action_to_action_id
     *
     * ACL Action literal to database pk
     *
     * @var    Registry
     * @since  1.0
     */
    public $action_to_action_id;

    /**
     * $action_to_controller
     *
     * ACL Action to Molajo Controller list
     *
     * @var    Registry
     * @since  1.0
     */
    public $action_to_controller;

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
        $actionsList = $m->getData();
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
            return;
        }
        if ($asset_id == 0) {
            return;
        }

        $taskPermissions = array();
        foreach ($tasklist as $task) {
            $taskPermissions[$task] =
                Services::Access()
                    ->authoriseTask(
                    $task,
                    $asset_id
                );
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
            return Services::Access()
                ->authoriseTask('login', $asset_id);
        }

        /** Retrieve ACL Action for this Task */
        $action = $this->task_to_action->get($task);
        $action_id = (int) $this->action_to_action_id->get($action);

        if (trim($action) == '' || (int) $action_id == 0 || trim($action) == '') {
            echo 'Task: ' . $task . ' Action: ' . $action . ' Action ID: '. $action_id . ' (Message in Access)' . '<br />';
        }

        //todo: amy fill database with real actions

        /** check for permission */
        $action_id = 3;
        $m = new MolajoGroupPermissionsModel();
        $m->query->select('count(*) as count');
        $m->query->from($m->db->nq('#__group_permissions') . ' as a');
        $m->query->where('a.' . $m->db->nq('asset_id') . ' = ' . (int)$asset_id);
        $m->query->where('a.' . $m->db->nq('action_id') . ' = ' . (int)$action_id);
        $m->query->where('a.' . $m->db->nq('group_id')
                . ' IN (' . implode(',', Services::User()->get('groups')) . ')'
        );

        $results = $m->runQuery();
        foreach ($results as $result) {
        }

        if ($result->count > 0) {
            return true;
        } else {
            echo 'Task: ' . $task . ' Action: ' . $action . ' Action ID: '. $action_id . ' (Message in Access)' . '<br />';
            echo $result->count;
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
        $m->query->select('count(*) as count');
        $m->query->from('#__user_applications a');
        $m->query->where('application_id = ' . (int)MOLAJO_APPLICATION_ID);
        $m->query->where('user_id = ' . (int)$user_id);

        $results = $m->runQuery();
        foreach ($results as $result) {
        }

        if ($result->count > 0) {
            return true;
        } else {
            echo 'Task: login ' . ' User ID: '. $user_id . ' (Message in Access)' . '<br />';
            return false;
        }
    }

    /**
     *  setQueryViewAccess
     *
     *  Append criteria needed to implement view access for Query
     *
     * @param  string  $query
     * @param  string  $parameters
     *
     * @return     boolean
     * @since      1.0
     */
    public function setQueryViewAccess(
        $query = array(),
        $parameters = array())
    {
        $db = Services::DB();

        if ($parameters['select'] === true) {
            $query->select(
                $db->nq($parameters['asset_prefix']) .
                    '.' .
                    $db->nq('view_group_id')
            );

            $query->select(
                $db->nq($parameters['asset_prefix']) .
                    '.' .
                    $db->nq('id') .
                    ' as ' .
                    $db->nq('asset_id')
            );
        }

        $query->from(
            $db->nq('#__assets') .
                ' as ' .
                $db->nq($parameters['asset_prefix'])
        );

        $query->where(
            $db->nq($parameters['asset_prefix']) .
                '.' .
                $db->nq('source_id') .
                ' = ' .
                $db->nq($parameters['join_to_prefix']) .
                '.' .
                $db->nq($parameters['join_to_primary_key'])
        );

        $query->where(
            $db->nq($parameters['asset_prefix']) .
                '.' . $db->nq('asset_type_id') .
                ' = ' .
                $db->nq($parameters['join_to_prefix']) .
                '.' .
                $db->nq('asset_type_id')
        );

        $query->where(
            $db->nq($parameters['asset_prefix']) .
                '.' .
                $db->nq('view_group_id') .
                ' IN (' . implode(',',
                Services::User()
                    ->get('view_groups')) .
                ')'
        );

        $query->where(
            $db->nq($parameters['asset_prefix']) .
                '.' .
                $db->nq('redirect_to_id') .
                ' = 0');

        return $query;
    }
}
