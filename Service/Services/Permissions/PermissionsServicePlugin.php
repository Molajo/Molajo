<?php
/**
 * Permissions Service Plugin
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Permissions;

use Molajo\Service\ServicesPlugin;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Permissions Service Plugin
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class PermissionsServicePlugin extends ServicesPlugin
{
    /**
     * on Before Startup Event
     *
     * Follows instantiation of the service class and before the method identified as the "start" method
     *
     * @return  void
     * @since   1.0
     * @throws  \RuntimeException
     * @throws  \Exception
     */
    public function onBeforeServiceInitialise()
    {
        $controllerClass = CONTROLLER_CLASS_NAMESPACE;
        $controller      = new $controllerClass();
        $controller->getModelRegistry('Datasource', 'Actions', 1);

        $items = $controller->getData(QUERY_OBJECT_LIST);

        if ($items === false) {
            throw new \RuntimeException
            ('Permissions: getActions Query failed.');
        }

        $permission_actions    = array();
        $permission_action_ids = array();

        $title                         = 'none';
        $permission_actions[0]         = $title;
        $permission_action_ids[$title] = 0;
        foreach ($items as $item) {
            $title                         = strtolower($item->title);
            $permission_actions[$item->id] = $title;
            $permission_action_ids[$title] = $item->id;
        }

        $this->service_class_instance->set('actions', $permission_actions);

        /** Verb Actions (Order Up, Order Down, Feature) to Permission Actions */
        $actions = Services::Configuration()->getFile('Application', 'Actions');
        if (count($actions) == 0) {
            throw new \Exception('Permissions: Actions Table not found.');
        }

        $tasks                   = array();
        $action_to_authorisation = array();
        $action_to_controller    = array();

        foreach ($actions->action as $t) {
            $name                           = (string)$t['name'];
            $tasks[]                        = $name;
            $action_to_authorisation[$name] = (string)$t['authorisation'];
            $action_to_controller[$name]    = (string)$t['controller'];
        }

        $this->service_class_instance->set('action_to_authorisation', $action_to_authorisation);
        $this->service_class_instance->set('action_to_controller', $action_to_controller);
        sort($tasks);
        $this->service_class_instance->set('tasks', $tasks);

        /** Bridges the Verb Action (Order Up, Order Down) to the Permission Action (Read, Update) to the ID (1, 2, etc.) */
        $action_to_authorisation_id = array();
        foreach ($action_to_authorisation as $action => $authorisation) {
            $action_to_authorisation_id[$action] = $permission_action_ids[$authorisation];
        }
        $this->service_class_instance->set('action_to_authorisation_id', $action_to_authorisation_id);

        /** Not sure where else to place this */
        $filtersFile = Services::Configuration()->getFile('Application', 'Filters');
        if (count($filtersFile) == 0) {
            throw new \Exception('Permissions: Filters Table not found.');
        }

        $filters = array();
        foreach ($filtersFile->filter as $f) {
            $name      = (string)$f['name'];
            $filters[] = $name;
        }
        sort($filters);
        $this->service_class_instance->set('filters', $filters);

        $this->service_class_instance->set('user_view_groups', Services::User()->get('view_groups'));

        $this->service_class_instance->set('user_groups', Services::User()->get('groups'));

        $this->service_class_instance->set(
            'disable_filter_for_groups',
            explode(',', Services::Application()->get('user_disable_filter_for_groups'))
        );

        return;
    }

    /**
     * On After Startup Event
     *
     * Follows the completion of the start method defined in the configuration
     *
     * @return  void
     * @since   1.0
     */
    public function onAfterServiceInitialise()
    {

    }
}
