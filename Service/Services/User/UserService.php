<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\User;

use Molajo\Helpers;
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
     * @return  User
     * @since   1.0
     */
    public function load($id = 0)
    {
        $this->id = 1;

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(DATA_SOURCE_LITERAL, USER_LITERAL, 1);

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
        $x = $item->Userapplications;
        if (count($x) > 0) {
            foreach ($x as $app) {
                $applications[] = $app->application_id;
            }
        }
		array_unique($applications);

        unset($item->Userapplications);

        $temp = array();
        $x = $item->Usergroups;
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

        Services::Registry()->createRegistry(USER_LITERAL);

        foreach (get_object_vars($item) as $key => $value) {
            Services::Registry()->set(USER_LITERAL, $key, $value);
        }
        Services::Registry()->sort(USER_LITERAL);

        if ($this->id == 0) {
            Services::Registry()->set(USER_LITERAL, 'public', 1);
            Services::Registry()->set(USER_LITERAL, 'guest', 1);
            Services::Registry()->set(USER_LITERAL, 'registered', 0);
        } else {
            Services::Registry()->set(USER_LITERAL, 'public', 1);
            Services::Registry()->set(USER_LITERAL, 'guest', 0);
            Services::Registry()->set(USER_LITERAL, 'registered', 1);
        }

        if (in_array(SYSTEM_GROUP_ADMINISTRATOR, $groups)) {
            Services::Registry()->set(USER_LITERAL, 'administrator', 1);
        } else {
            Services::Registry()->set(USER_LITERAL, 'administrator', 0);
        }

        Services::Registry()->set(USER_LITERAL, 'Applications', $applications);
        Services::Registry()->set(USER_LITERAL, 'Groups', $groups);
        Services::Registry()->set(USER_LITERAL, 'ViewGroups', $viewGroups);

		unset($m);
		unset($item);
		unset($applications);
		unset($groups);
		unset($viewGroups);

		Services::Registry()->sort(USER_LITERAL);

        Helpers::Extension()->setAuthorisedExtensions();

        return $this;
    }
}
