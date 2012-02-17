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
 * Various methods for Asset permissioning verification
 *
 * @package     Molajo
 * @subpackage  Access Control
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
                    ->authoriseTask($task, $asset_id);
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

        /** need task to action mapping in a) site ini or b) application parameters */
        $action = 'view';
        $action_id = 3;

        $db = Services::DB();
        $query = $db->getQuery(true);

        $query->select('count(*)');
        $query->from($db->nq('#__group_permissions') . ' as a');
        $query->where('a.' . $db->nq('asset_id') . ' = ' . (int)$asset_id);
        $query->where('a.' . $db->nq('action_id') . ' = ' . (int)$action_id);
        $query->where('a.' . $db->nq('group_id') .
            ' IN (' .
            implode(',',
                Services::User()
                    ->get('groups')) . ')'
                );

        $db->setQuery($query->__toString());
        $count = $db->loadResult();

        if ($db->getErrorNum()) {
            $this->setError($db->getErrorMsg());
            return false;
        }

        if ($count > 0) {
            return true;
        } else {
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

        $db = Services::DB();
        $query = $db->getQuery(true);

        $query->select('count(*) as count');
        $query->from('#__user_applications a');
        $query->where('application_id = ' . (int)MOLAJO_APPLICATION_ID);
        $query->where('user_id = ' . (int)$user_id);

        $db->setQuery($query->__toString());
        $result = $db->loadResult();

        if ($db->getErrorNum()) {
            $this->setError($db->getErrorMsg());
            return false;
        }

        if ($result == 1) {
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
     * @param  string  $query
     * @param  string  $parameters
     *
     * @return     boolean
     * @since      1.0
     */
    static public function setQueryViewAccess($query = array(),
                                              $parameters = array())
    {
        $db = Services::DB();

        if ($parameters['select'] === true) {
            $query->select($parameters['asset_prefix'] .
                    '.' . $db->nq('view_group_id')
            );

            $query->select($parameters['asset_prefix'] .
                    '.' . $db->nq('id') .
                    ' as ' . $db->nq('asset_id')
            );
        }

        $query->from($db->nq('#__assets') .
                ' as ' . $parameters['asset_prefix']
        );

        $query->where($parameters['asset_prefix'] . '.source_id = ' .
                $parameters['join_to_prefix'] .
                '.' . $db->nq($parameters['join_to_primary_key'])
        );

        $query->where($parameters['asset_prefix'] . '.asset_type_id = ' .
                $parameters['join_to_prefix'] .
                '.' . $db->nq('asset_type_id')
        );

        $query->where($parameters['asset_prefix'] .
                '.' . $db->nq('view_group_id') .
                ' IN (' . implode(',',
                Services::User()
                    ->get('view_groups')) .
                ')'
        );


        $query->where($parameters['asset_prefix'] . '.redirect_to_id = 0');

        return $query;
    }


    /**
     *  TYPE 3 --> MolajoACL::getList -> getActionsList
     */
    public function getActionsList($id, $option, $task, $parameters = array())
    {
        $actions = array();

        $component = $parameters[0];
        $section = $parameters[1];

        if (is_file(MOLAJO_EXTENSIONS_COMPONENTS . '/' . $component . '/access.xml')) {
            $xml = simplexml_load_file(MOLAJO_EXTENSIONS_COMPONENTS . '/' . $component . '/access.xml');

            foreach ($xml->children() as $child)
            {
                if ($section == (string)$child['name']) {
                    foreach ($child->children() as $action) {
                        $actions[] = (object)array('name' => (string)$action['name'],
                            'title' => (string)$action['title'],
                            'description' => (string)$action['description']);
                    }
                    break;
                }
            }
        }
        return $actions;
    }
}
