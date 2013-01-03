<?php
/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Service\Services\User;

use Molajo\Service\Services\Theme\Helper\ExtensionHelper;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * User
 *
 * @package     Niambie
 * @subpackage  Service
 * @since       1.0
 */
Class UserService
{
    /**
     * Retrieve User Information (both authenticated and guest)
     *
     * @return  User
     * @since   1.0
     */
    public function initialise($id = 0)
    {
        $this->id = 1;

        $controllerClass = CONTROLLER_CLASS_NAMESPACE;
        $controller      = new $controllerClass();
        $controller->getModelRegistry('datasource', 'User', 1);

        $controller->set('primary_key_value', $this->id, 'model_registry');
        $controller->set('get_customfields', 2, 'model_registry');
        $controller->set('use_special_joins', 1, 'model_registry');
        $controller->set('process_plugins', 1, 'model_registry');

        $item = $controller->getData(QUERY_OBJECT_ITEM);
        if ($item === false || count($item) == 0) {
            throw new \RuntimeException ('User: Load User Query Failed');
        }

        unset($item->customfields);
        unset($item->metadata);
        unset($item->parameters);
        unset($item->password);

        $applications = array();
        $x            = $item->Userapplications;
        if (count($x) > 0) {
            foreach ($x as $app) {
                $applications[] = $app->application_id;
            }
        }
        array_unique($applications);

        unset($item->Userapplications);

        $temp = array();
        $x    = $item->Usergroups;
        if (count($x) > 0) {
            foreach ($x as $group) {
                $temp[] = $group->group_id;
            }
        }

        if (in_array(SYSTEM_GROUP_PUBLIC, $temp)) {
        } else {
            $temp[] = SYSTEM_GROUP_PUBLIC;
        }

        if ($this->id == 0) {
            $temp[] = SYSTEM_GROUP_GUEST;
        } else {
            if (in_array(SYSTEM_GROUP_REGISTERED, $temp)) {
            } else {
                $temp[] = SYSTEM_GROUP_REGISTERED;
            }
        }
        unset($item->Usergroups);
        sort($temp);
        $groups = array_unique($temp);

        $temp = array();
        $x    = $item->Userviewgroups;
        foreach ($x as $vg) {
            $temp[] = $vg->view_group_id;
        }

        $temp[] = SYSTEM_GROUP_PUBLIC;

        if (in_array(SYSTEM_GROUP_REGISTERED, $temp)) {
        } else {
            $temp[] = SYSTEM_GROUP_GUEST;
        }
        sort($temp);
        $viewGroups = array_unique($temp);

        unset($item->Userviewgroups);

        Services::Registry()->createRegistry('User');

        foreach (get_object_vars($item) as $key => $value) {
            Services::Registry()->set('User', $key, $value);
        }
        Services::Registry()->sort('User');

        if ($this->id == 0) {
            Services::Registry()->set('User', 'public', 1);
            Services::Registry()->set('User', 'guest', 1);
            Services::Registry()->set('User', 'registered', 0);
        } else {
            Services::Registry()->set('User', 'public', 1);
            Services::Registry()->set('User', 'guest', 0);
            Services::Registry()->set('User', 'registered', 1);
        }

        if (in_array(SYSTEM_GROUP_ADMINISTRATOR, $groups)) {
            Services::Registry()->set('User', 'administrator', 1);
            Services::Registry()->set('User', 'authorised_for_offline_access', 1);
        } else {
            Services::Registry()->set('User', 'administrator', 0);
            Services::Registry()->set('User', 'authorised_for_offline_access', 0);
        }

        Services::Registry()->set('User', 'Applications', $applications);
        Services::Registry()->set('User', 'Groups', $groups);
        Services::Registry()->set('User', 'ViewGroups', $viewGroups);

        unset($m);
        unset($item);
        unset($applications);
        unset($groups);
        unset($viewGroups);

        Services::Registry()->sort('User');

        $this->setAuthorisedExtensions();

        return $this;
    }

    /**
     * Retrieve all Extensions the logged on User is authorised to use. The Extension Helper will use this
     *  registry to avoid a new read when processing requests for Themes, Views, Plugins, Services, etc.
     *
     * @return  bool
     * @since   1.0
     */
    protected function setAuthorisedExtensions()
    {
        $this->extensionHelper = new ExtensionHelper();
        $results               = $this->extensionHelper->get(0, null, null, null, 1);
        if ($results === false || count($results) == 0) {
            throw new \Exception('User: No authorised Extension Instances.');
        }

        Services::Registry()->createRegistry('AuthorisedExtensions');
        Services::Registry()->createRegistry('AuthorisedExtensionsByInstanceTitle');

        foreach ($results as $extension) {

            Services::Registry()->set('AuthorisedExtensions', $extension->id, $extension);

            if ($extension->catalog_type_id == CATALOG_TYPE_MENUITEM) {
            } else {
                $key = trim($extension->title) . $extension->catalog_type_id;
                Services::Registry()->set('AuthorisedExtensionsByInstanceTitle', $key, $extension->id);
            }
        }

        Services::Registry()->sort('AuthorisedExtensions');
        Services::Registry()->sort('AuthorisedExtensionsByInstanceTitle');

        Services::Registry()->lock('AuthorisedExtensions');
        Services::Registry()->lock('AuthorisedExtensionsByInstanceTitle');

        return true;
    }
}
