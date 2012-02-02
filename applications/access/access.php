<?php
/**
 * @package     Molajo
 * @subpackage  Access
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Access Control
 *
 * Various queries for Permission Verification
 *
 * @package     Molajo
 * @subpackage  Access Control
 * @since       1.0
 */
abstract class MolajoAccess
{
    /**
     *  authoriseTask
     *
     * @param  string  $task
     * @param  string  $asset_id
     *
     * @return  boolean
     * @since   1.0
     */
    static public function authoriseTask($task = 'login', $asset_id = 0)
    {
        /** need task to action mapping in a) site ini or b) application parameters */
        $action = 'view';
        $action_id = 3;

        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        $query->select('count(*)');
        $query->from($db->nameQuote('#__group_permissions') . ' as a');
        $query->where('a.' . $db->nameQuote('asset_id') . ' = ' . (int)$asset_id);
        $query->where('a.' . $db->nameQuote('action_id') . ' = ' . (int)$action_id);
        $query->where('a.' . $db->nameQuote('group_id') .
            ' IN (' . implode(',', MolajoController::getUser()->groups) . ')');

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
     *  authoriseTaskList
     *
     * @param  array  $tasklist
     * @param  string  $asset_id
     * @param  string  $row
     *
     * @return  boolean
     * @since   1.0
     */
    static public function authoriseTaskList($tasklist = array(), $asset_id = 0, $row = array())
    {
    }

    /**
     * checkUserPermissionsLogin
     *
     * @param $key
     * @param $action
     * @param null $asset
     * @return void
     */
    public function checkUserPermissionsLogin($user_id)
    {
        $db = MolajoController::getDbo();
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
        $db = MolajoController::getDbo();

        if ($parameters['select'] === true) {
            $query->select($parameters['asset_prefix'] .
                    '.' . $db->namequote('view_group_id')
            );

            $query->select($parameters['asset_prefix'] .
                    '.' . $db->namequote('id') .
                    ' as ' . $db->namequote('asset_id')
            );
        }

        $query->from($db->namequote('#__assets') .
                ' as ' . $parameters['asset_prefix']
        );

        $query->where($parameters['asset_prefix'] . '.source_id = ' .
                $parameters['join_to_prefix'] .
                '.' . $db->namequote($parameters['join_to_primary_key'])
        );

        $query->where($parameters['asset_prefix'] .
                '.' . $db->namequote('view_group_id') .
                ' IN (' . implode(',', MolajoController::getUser()->view_groups) . ')'
        );

        return $query;
    }


    /**
     *  TYPE 3 --> MolajoACL::getList -> getActionsList
     */
    static public function getActionsList($id, $option, $task, $parameters = array())
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
