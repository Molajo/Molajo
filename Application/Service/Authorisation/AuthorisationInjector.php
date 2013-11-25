<?php
/**
 * Authorisation Dependency Injector
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Authorisation;

use stdClass;
use Molajo\IoC\Handler\AbstractInjector;
use CommonApi\IoC\ServiceHandlerInterface;
use CommonApi\Exception\RuntimeException;

/**
 * Authorisation Services
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class AuthorisationInjector extends AbstractInjector implements ServiceHandlerInterface
{
    /**
     * Constructor
     *
     * @param  array $options
     *
     * @since  1.0
     */
    public function __construct(array $options = array())
    {
        $options['service_name']             = basename(__DIR__);
        $options['store_instance_indicator'] = true;
        $options['service_namespace']        = 'Molajo\\Authorisation\\Adapter';

        parent::__construct($options);
    }

    /**
     * Instantiate a new handler and inject it into the Adapter for the ServiceHandlerInterface
     * Retrieve a list of Interface dependencies and return the data ot the controller.
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function setDependencies(array $reflection = null)
    {
        $this->reflection   = array();
        $this->dependencies = array();

        $this->dependencies['Runtimedata']  = array();
        $this->dependencies['Resources']    = array();
        $this->dependencies['User']         = array();
        $this->dependencies['Database']     = array();
        $this->dependencies['Fieldhandler'] = array();

        return $this->dependencies;
    }

    /**
     * Set Dependency values
     *
     * @param   array $dependency_instances (ignored in Service Item Adapter, based in from handler)
     *
     * @return  $this
     * @since   1.0
     */
    public function processFulfilledDependencies(array $dependency_instances = null)
    {
        parent::processFulfilledDependencies($dependency_instances);

        $this->options['config']
            = $this->dependencies['Resources']->get('xml:///Molajo//Application//Actions.xml');

        $this->dependencies['site_id']                   = $this->dependencies['Runtimedata']->site->id;
        $this->dependencies['application_id']            = $this->dependencies['Runtimedata']->application->id;
        $this->dependencies['task_to_action']            = $this->getTaskToAction();
        $this->dependencies['task_to_controller']        = $this->getTaskToController();
        $this->dependencies['disable_filter_for_groups'] = array(5, 6);
        $this->options['offline_switch']
            = $this->dependencies['Runtimedata']->application->parameters->offline_switch;

        $userData = $this->dependencies['User']->getUserData('*');

        $permissions                                = new stdClass();
        $permissions->id                            = $userData->id;
        $permissions->username                      = $userData->username;
        $permissions->email                         = $userData->email;
        $permissions->administrator                 = $userData->administrator;
        $permissions->authorised_for_offline_access = $userData->authorised_for_offline_access;
        $permissions->public                        = $userData->public;
        $permissions->guest                         = $userData->guest;
        $permissions->registered                    = $userData->registered;
        $permissions->html_filtering_required       = $userData->html_filtering_required;
        $permissions->sites                         = $userData->sites;
        $permissions->applications                  = $userData->applications;
        $permissions->groups                        = $userData->groups;
        $permissions->view_groups                   = $userData->view_groups;
        $this->dependencies['Permissions']          = $permissions;

        return $this;
    }

    /**
     * IoC Controller triggers the DI Handler to create the Class for the Service
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function instantiateService()
    {
        $handler = $this->getAdapterHandler();

        $this->service_instance = $this->getAdapter($handler);

        return $this;
    }

    /**
     * Get the Filesystem specific Adapter Handler
     *
     * @param   string $adapter_handler
     *
     * @return  object
     * @since   1.0
     * @throws  ServiceHandlerInterface
     */
    protected function getAdapterHandler()
    {
        $class = 'Molajo\\Authorisation\\Handler\\Database';

        try {
            return new $class(
                $this->dependencies['site_id'],
                $this->dependencies['application_id'],
                $this->dependencies['task_to_action'],
                $this->dependencies['task_to_controller'],
                $this->options['offline_switch'],
                $this->dependencies['Permissions'],
                $this->dependencies['Database'],
                $this->dependencies['Fieldhandler']
            );
        } catch (Exception $e) {
            throw new RuntimeException
            ('Authorisation: Could not instantiate Handler: ' . $class);
        }
    }

    /**
     * Get Filesystem Adapter, inject with specific Filesystem Adapter Handler
     *
     * @param   object $handler
     *
     * @return  object
     * @since   1.0
     * @throws  ServiceHandlerInterface
     */
    protected function getAdapter($handler)
    {
        $class = 'Molajo\\Authorisation\\Adapter';

        try {
            return new $class($handler,
                $this->dependencies['Permissions']
            );
        } catch (Exception $e) {

            throw new RuntimeException
            ('Authorisation: Could not instantiate Adapter');
        }
    }

    /**
     * Action to Authorisation
     *
     * @return  array
     * @since   1.0
     */
    public function getTaskToAction()
    {
        $task_to_action = array();
        foreach ($this->options['config']->action as $t) {
            $name                  = (string)$t['name'];
            $task_to_action[$name] = (string)$t['authorisation'];
        }

        return $task_to_action;
    }

    /**
     * Action to Controller
     *
     * @return  array
     * @since   1.0
     */
    public function getTaskToController()
    {
        $task_to_controller = array();
        foreach ($this->options['config']->action as $t) {
            $name                      = (string)$t['name'];
            $task_to_controller[$name] = (string)$t['controller'];
        }

        return $task_to_controller;
    }
}
