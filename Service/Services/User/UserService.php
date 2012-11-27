<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\User;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * User
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class UserService
{
    /**
     * load
     *
     * Retrieve User Information (both authenticated and guest)
     *
     * @return User
     * @since   1.0
     */
    public function load($id = 0)
    {
        $this->id = 1;

        /** Retrieve User Data  */
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();

        $results = $controller->getModelRegistry(DATASOURCE_LITERAL, 'User');
        if ($results === false) {
            return false;
        }

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }

        $controller->set('id', $this->id);
		$controller->set('get_customfields', 2);
		$controller->set('use_special_joins', 1);
		$controller->set('process_plugins', 1);

        $item = $controller->getData(QUERY_OBJECT_ITEM);
        if ($item === false || count($item) == 0) {
            throw new \RuntimeException ('User load() query problem');
        }

		unset($item->customfields);
		unset($item->metadata);
		unset($item->parameters);
		unset($item->password);

        /** User Applications */
        $applications = array();
        $x = $item->Userapplications;
        foreach ($x as $app) {
            $applications[] = $app->application_id;
        }
		array_unique($applications);

        unset($item->Userapplications);

        /** User Groups */
        $temp = array();
        $x = $item->Usergroups;
        foreach ($x as $group) {
            $temp[] = $group->group_id;
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

        /** User View Groups */
        $temp = array();
        $x = $item->Userviewgroups;
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

        /** Initialize */
        Services::Registry()->createRegistry('User');

        /** Retrieve each field */
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
        } else {
            Services::Registry()->set('User', 'administrator', 0);
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

        return $this;
    }
}
