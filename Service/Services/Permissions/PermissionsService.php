<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Permissions;

use Molajo\Application;
use Molajo\Service\Services;

defined('MOLAJO') or die;

//todo: remove hard-coded prefixes and replace with prefixes defined in model

/**
 * Permissions
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class PermissionsService
{
    /**
     * Get action ids and values to load into registry
     *
     * @return  null
     * @since   1.0
     * @throws  \Exception
     */
    public function initialise()
    {
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(DATASOURCE_LITERAL, 'Actions');
        $controller->setDataobject();

        $items = $controller->getData(QUERY_OBJECT_LIST);
        if ($items === false) {
            throw new \RuntimeException ('Permissions: getActions Query failed.');
        }

        $actions = array();
        foreach ($items as $item) {
            $actions[$item->title] = (int)$item->id;
        }
        Services::Registry()->set(PERMISSIONS_LITERAL, 'actions', $actions);

        $actions = Services::Configuration()->getFile('Application', 'Actions');

        if (count($actions) == 0) {
            throw new \Exception('Permissions: Actions Table not found.');
        }

        $urlActions = array();
        $action_to_authorisation = array();
        $action_to_controller = array();

        foreach ($actions->action as $t) {
            $urlActions[] = (string)$t['name'];
            $action_to_authorisation[(string)$t['name']] = (string)$t['authorisation'];
            $action_to_controller[(string)$t['name']] = (string)$t['controller'];
        }

        Services::Registry()->set(PERMISSIONS_LITERAL, 'action_to_authorisation', $action_to_authorisation);
        Services::Registry()->set(PERMISSIONS_LITERAL, 'action_to_controller', $action_to_controller);

        sort($urlActions);
        Services::Registry()->loadArray(PERMISSIONS_LITERAL, 'urlActions', $urlActions);

        $action_to_authorisation_id = array();
        foreach ($actions as $title => $id) {
            $action_to_authorisation_id[$title] = (int)$id;
        }

        Services::Registry()->set(PERMISSIONS_LITERAL, 'action_to_authorisation_id', $action_to_authorisation_id);

        return true;
    }

    /**
     * Check if the Site is authorised for this Application
     *
     * Usage:
     * $results = Services::Permissions()->verifySiteApplication();
     *
     * @param   mixed $application_id if valid, or false
     *
     * @return  boolean
     * @since   1.0
     */
    public function verifySiteApplication()
    {
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(DATASOURCE_LITERAL, 'Siteapplications');
        $controller->setDataobject();

        $controller->model->query->select($controller->model->db->qn('a.application_id'));
        $controller->model->query->where($controller->model->db->qn('a.site_id') . ' = ' . (int)SITE_ID);
        $controller->model->query->where($controller->model->db->qn('a.application_id') . ' = ' . (int)APPLICATION_ID);

        $application_id = $controller->getData(QUERY_OBJECT_RESULT);

        if ($application_id === false) {
            Services::Response()->setHeader(
                'Status',
                Services::Registry()->get(CONFIGURATION_LITERAL, 'error_403_message', 'Not Authorised.'),
                403
            );
        }

        return $application_id;
    }

    /**
     * Using the Request Task, retrieve the Controller
     *
     * Example usage:
     * $controller = Services::Permissions()->getTaskController($action);
     *
     * @param   $action
     *
     * @return  string
     * @since   1.0
     */
    public function getTaskController($action)
    {
        $actionArray = $this->request->get(PERMISSIONS_LITERAL, 'action_to_authorisation');
        $controller = $this->request->get(PERMISSIONS_LITERAL, 'action_to_controller');

        if (isset($actionArray[$action]) && isset($controller[$actionArray[$action]])) {
            return $controller[$actionArray[$action]];
        } else {
            throw new \Exception(PERMISSIONS_LITERAL . ': Action ' . $action . ' and associated controller not defined');
        }
    }

    /**
     * Verifies Permissions for a set of Actions for the specified Catalog ID
     *      Useful for question "What can the logged on User do with this set of Articles (or Article)?"
     *
     * Example usage:
     * $permissions = Services::Permissions()->verifyTaskList($actionsArray, $item->catalog_id);
     *
     * @param   array   $actionlist
     * @param   string  $catalog_id
     *
     * @return  boolean
     * @since   1.0
     */
    public function verifyTaskList($actionlist = array(), $catalog_id = 0)
    {
        if (count($actionlist) == 0) {
            throw new \Exception(PERMISSIONS_LITERAL . ': Empty Action List sent into verifyTasklist');
        }
        if ($catalog_id == 0) {
            throw new \Exception(PERMISSIONS_LITERAL . ': No Catalog ID sent into verifyTaskList');
        }

        $actionPermissions = array();

        foreach ($actionlist as $action) {
            $actionPermissions[$action] = $this->verifyTask($action, $catalog_id);
        }

        return $actionPermissions;
    }

    /**
     * Verify User Permissions for the Action and Catalog ID
     *
     * Example usage:
     *  $permissions = Services::Permissions()->verifyAction();
     *
     * @return  boolean
     * @since   1.0
     */
    public function verifyAction()
    {
        if (in_array(
            Services::Registry()->get(PARAMETERS_LITERAL, 'catalog_view_group_id'),
            Services::Registry()->get('User', 'ViewGroups')
        )
        ) {
            Services::Registry()->set(PARAMETERS_LITERAL, 'status_authorised', true);

        } else {
            return Services::Registry()->set(PARAMETERS_LITERAL, 'status_authorised', false);
        }

        if (Services::Registry()->get(PARAMETERS_LITERAL, 'request_action', ACTION_VIEW) == ACTION_VIEW
            && Services::Registry()->get(PARAMETERS_LITERAL, 'status_authorised') === true
        ) {
            return true;
        }

        $authorised = $this->verifyTask(
            Services::Registry()->get(PARAMETERS_LITERAL, 'request_action'),
            Services::Registry()->get(PARAMETERS_LITERAL, 'request_catalog_id')
        );

        Services::Registry()->set(PARAMETERS_LITERAL, 'status_authorised', $authorised);

        if (Services::Registry()->get(PARAMETERS_LITERAL, 'status_authorised') === true) {
            return true;

        } else {
            Services::Error()->set(403);

            return false;
        }
    }

    /**
     * Verifies permission for a user to perform a specific action on a specific catalog id
     *      Useful for question "Can the logged on User Edit this Article (or content in this Resource)?"
     *
     * Example usage:
     * Services::Permissions()->verifyTask($action, $catalog_id);
     *
     * @param   string  $action
     * @param   string  $catalog_id
     *
     * @return  boolean
     * @since   1.0
     */
    public function verifyTask($action, $catalog_id)
    {
        if ($action == 'login') {
            return $this->verifyLogin('login', $catalog_id);
        }
//todo: hash store results for later reuse
        $authorisationArray = $this->request->get(PERMISSIONS_LITERAL, 'action_to_authorisation');
        $authorisationIdArray = $this->request->get(PERMISSIONS_LITERAL, 'action_to_authorisation_id');

        $action = $authorisationArray[$action];
        $action_id = $authorisationIdArray[$action];

        if (trim($action) == '' || (int)$action_id == 0 || trim($action) == '') {
            throw new \Exception(PARAMETERS_LITERAL . ': Required value Action not provided for verifyTask method.');
        }

        if (trim($catalog_id) == '' || (int)$catalog_id == 0 || trim($catalog_id) == '') {
            throw new \Exception(PARAMETERS_LITERAL . ': Required value Catalog ID not provided for verifyTask method.');
        }

        $action_id = 3;

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(DATASOURCE_LITERAL, 'Grouppermissions');
        $controller->setDataobject();

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
                . ' IN (' . implode(', ', Services::Registry()->get('User', 'Groups')) . ')'
        );

        $count = $controller->getData(QUERY_OBJECT_RESULT);
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Verifies permission for a user to logon to a specific application
     *
     * Example usage:
     * Services::Permissions()->verifyLogin('login', $catalog_id);
     *
     * @param   $key
     * @param   $action
     *
     * @param   null  $catalog
     * @return  bool
     */
    public function verifyLogin($user_id)
    {
        if ((int)$user_id == 0) {
            return false;
        }

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(DATASOURCE_LITERAL, 'Userapplications');
        $controller->setDataobject();

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
     * @param   array $query
     * @param   array $db
     * @param   array $parameters
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
     * @return  bool
     * @since   1.0
     */
    public function setHTMLFilter()
    {
        $groups = Services::Registry()->get(CONFIGURATION_LITERAL, 'user_disable_filter_for_groups');
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
