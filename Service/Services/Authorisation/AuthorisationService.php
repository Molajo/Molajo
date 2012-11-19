<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
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
     * Load ACL-related data for use with Authorisation
     *
     * @return null
     * @since   1.0
     */
    public function initialise()
    {
        $actions = Services::Configuration()->getFile('Application', 'Actions');
        if (count($actions) == 0) {
            //echo '<br />Error in AuthorisationService -- Application Actions table returned no rows <br />';
            //error
        }

        $tempActions = array();
        foreach ($actions->action as $t) {
            $tempActions[] = (string) $t['name'];
            Services::Registry()->set('action_to_authorisation', (string) $t['name'], (string) $t['authorisation']);
            Services::Registry()->set('action_to_controller', (string) $t['name'], (string) $t['controller']);
        }

        sort($tempActions);
        Services::Registry()->loadArray('urlActions', $tempActions);

        $items = Services::Registry()->get('Actions');
        foreach ($items as $title => $id) {
            Services::Registry()->set('action_to_authorisation_id', $title, (int) $id);
        }

        return;
    }

    /**
     * Check if the Site is authorised for this Application
     *
     * Usage:
     * $results = Services::Authorisation()->verifySiteApplication();
     *
     * @param mixed $application_id if valid, or false
     *
     * @return boolean
     * @since  1.0
     */
    public function verifySiteApplication()
    {
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();

        $results = $controller->getModelRegistry('Datasource', 'Siteapplications');
        if ($results === false) {
            return false;
        }

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }

        $controller->model->query->select($controller->model->db->qn('a.application_id'));
        $controller->model->query->where($controller->model->db->qn('a.site_id') . ' = ' . (int) SITE_ID);
        $controller->model->query->where($controller->model->db->qn('a.application_id') . ' = ' . (int) APPLICATION_ID);

        $application_id = $controller->getData(QUERY_OBJECT_RESULT);

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
        $action = $this->request->get('action_to_authorisation', $action);
        $controller = $this->request->get('action_to_controller', $action);

        return $controller;
    }

    /**
     * For the list of actions (actions), determine if the user is authorised for the specific catalog id;
     * Useful for button bars, links, and other User Interface Presentation Logic
     *
     * Example usage:
     * $permissions = Services::Authorisation()->verifyTaskList($actionsArray, $item->catalog_id);
     *
     * @param array  $actionlist
     * @param string $catalog_id
     *
     * @return boolean
     * @since   1.0
     */
    public function verifyTaskList($actionlist = array(), $catalog_id = 0)
    {
        if (count($actionlist) == 0) {
            return false;
        }
        if ($catalog_id == 0) {
            return false;
        }

        $actionPermissions = array();

        foreach ($actionlist as $action) {
            $actionPermissions[$action] = $this->verifyTask($action, $catalog_id);
        }

        return $actionPermissions;
    }

    /**
     * Verify user authorization for the Request Action and Catalog ID
     *
     * Example usage:
     * $permissions = Services::Authorisation()->verifyAction();
     *
     * @return boolean
     * @since    1.0
     */
    public function verifyAction()
    {
        /** 403: verifyTask handles redirecting to error page */
        if (in_array(Services::Registry()->get('Parameters', 'catalog_view_group_id'),
            Services::Registry()->get('User', 'ViewGroups'))
        ) {
            Services::Registry()->set('Parameters', 'status_authorised', true);

        } else {
            return Services::Registry()->set('Parameters', 'status_authorised', false);
        }

        /** display view verified in getCatalog */
        if (Services::Registry()->get('Parameters', 'request_action', ACTION_VIEW) == ACTION_VIEW
            && Services::Registry()->get('Parameters', 'status_authorised') === true
        ) {
            return true;
        }

        /** verify other actions */
        $authorised = $this->verifyTask(
            Services::Registry()->get('Parameters', 'request_action'),
            Services::Registry()->get('Parameters', 'request_catalog_id')
        );

        Services::Registry()->set('Parameters', 'status_authorised', $authorised);

        if (Services::Registry()->get('Parameters', 'status_authorised') === true) {
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
     * Services::Authorisation()->verifyTask($action, $catalog_id);
     *
     * @param string $action
     * @param string $catalog_id
     *
     * @return boolean
     * @since   1.0
     */
    public function verifyTask($action, $catalog_id)
    {
        return true;

        if ($action == 'login') {
            return $this->verifyLogin('login', $catalog_id);
        }

        /** Retrieve ACL Action for this Task */
        $action = Services::Registry()->get('action_to_authorisation', $action);
        $action_id = Services::Registry()->get('action_to_authorisation_id', $action);

        if (trim($action) == '' || (int) $action_id == 0 || trim($action) == '') {
            //Echo 'AuthorisationServices::verifyTask '
            //         . ' Task: ' . $action
            //         . ' Action: ' . $action
            //         . ' Action ID: ' . $action_id;
            //throw error
        }

        //todo: amy fill database with real sample action permissions

        /** check for permission */
        $action_id = 3;

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $results = $controller->getModelRegistry('Datasource', 'Grouppermissions');
        if ($results === false) {
            return false;
        }

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }
        $controller->model->query->select($controller->model->db->qn('a.id'));
        $controller->model->query->where($controller->model->db->qn('a.catalog_id') . ' = ' . (int) $catalog_id);
        $controller->model->query->where($controller->model->db->qn('a.action_id') . ' = ' . (int) $action_id);
        $controller->model->query->where($controller->model->db->qn('a.group_id')
                . ' IN (' . implode(', ', Services::Registry()->get('User', 'Groups')) . ')'
        );

        $count = $controller->getData(QUERY_OBJECT_RESULT);
        if ($count > 0) {
            return true;

        } else {
            //echo 'AuthorisationServices::verifyTask No Query Results  '
            //       . ' Task: ' . $action
            //       . ' Action: ' . $action
            //       . ' Action ID: ' . $action_id;
            //throwerrror
            return false;
        }
    }

    /**
     * verifyLogin
     *
     * Verifies permission for a user to logon to a specific application
     *
     * Example usage:
     * Services::Authorisation()->verifyLogin('login', $catalog_id);
     *
     * @param $key
     * @param $action
     *
     * @param  null $catalog
     * @return bool
     */
    public function verifyLogin($user_id)
    {
        if ((int) $user_id == 0) {
            return false;
        }

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $results = $controller->getModelRegistry('Datasource', 'Userapplications');
        if ($results === false) {
            return false;
        }

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }

        $controller->model->query->where('a.application_id = ' . (int) APPLICATION_ID);
        $controller->model->query->where('a.user_id = ' . (int) $user_id);

        $count = $controller->model->getData(QUERY_OBJECT_RESULT);

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
     *         'join_to_primary_key' => Services::Registry()->get($this->model_registry, 'primary_key'),
     *         'catalog_prefix' => $this->primary_prefix . '_catalog',
     *         'select' => true
     *     )
     * );
     *
     * @param array $query
     * $param  array       $db
     * @param Registry $parameters
     *
     * @return boolean
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

        $query->where(
            $db->qn($parameters['catalog_prefix']) .
                '.' . $db->qn('application_id') .
                ' = ' .
                APPLICATION_ID
        );

        $vg = implode(',', array_unique(Services::Registry()->get('User', 'ViewGroups')));

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
        $groups = Services::Registry()->get('Configuration', 'user_disable_filter_for_groups');
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
