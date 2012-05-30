<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
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
     * Instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instances = array();

    /**
     * getInstance
     *
     * @param string $identifier Requested User (id or username or 0 for guest)
     *
     * @return object User
     * @since   1.0
     */
    public static function getInstance($id = 0)
    {
        $id = 42;
        if (empty(self::$instances[$id])) {
            $user = new UserService($id);
            self::$instances[$id] = $user;
        }

        return self::$instances[$id];
    }

    /**
     * __construct
     *
     * @param integer $identifier
     *
     * @return object
     * @since   1.0
     */
    protected function __construct($id = 0)
    {
        $this->id = (int) $id;

        return $this->load();
    }

    /**
     * load
     *
     * Retrieve User Information (both authenticated and guest)
     *
     * @return User
     * @since   1.0
     */
    protected function load()
    {
        /** Initialize */
        Services::Registry()->deleteRegistry('User');

        /** Retrieve User Data  */
        $controllerClass = 'Molajo\\MVC\\Controller\\ModelController';
        $m = new $controllerClass();
        $m->connect('Users');

        $m->set('id', $this->id);
        $item = $m->getData('item');

        if ($item == false || count($item) == 0) {
            throw new \RuntimeException ('User load() query problem');
        }

        /** User Applications */
        $applications = array();
        $x = $item->Userapplications;
        foreach ($x as $app) {
            $applications[] = $app->application_id;
        }
        unset($item->Userapplications);

        /** User Groups */
        $groups = array();
        $x = $item->Usergroups;
        foreach ($x as $group) {
            $groups[] = $group->group_id;
        }

        if (in_array(SYSTEM_GROUP_PUBLIC, $groups)) {
        } else {
            $groups[] = SYSTEM_GROUP_PUBLIC;
        }

        if ($this->id == 0) {
            $groups[] = SYSTEM_GROUP_GUEST;
        } else {
            if (in_array(SYSTEM_GROUP_REGISTERED, $groups)) {
            } else {
                $groups[] = SYSTEM_GROUP_REGISTERED;
            }
        }
        unset($item->Usergroups);

        /** User View Groups */
        $viewGroups = array();
        $x = $item->Userviewgroups;
        foreach ($x as $vg) {
            $viewGroups[] = $vg->view_group_id;
        }

        if (count($viewGroups) == 0) {
            $viewGroups = array(SYSTEM_GROUP_PUBLIC, SYSTEM_GROUP_GUEST);
        }
        unset($item->Userviewgroups);

        /** User Activity */
        $activity = $item->Useractivity;
        unset($item->Useractivity);

        /** User Object */
        Services::Registry()->set('User', 'id', $item->id);
        Services::Registry()->set('User', 'catalog_type_id', $item->catalog_type_id);
        Services::Registry()->set('User', 'username', $item->username);
        Services::Registry()->set('User', 'first_name', $item->first_name);
        Services::Registry()->set('User', 'last_name', $item->last_name);
        Services::Registry()->set('User', 'content_text', $item->content_text);
        Services::Registry()->set('User', 'email', $item->email);
        Services::Registry()->set('User', 'block', $item->block);
        Services::Registry()->set('User', 'activation', $item->activation);
        Services::Registry()->set('User', 'send_email', $item->send_email);
        Services::Registry()->set('User', 'register_datetime', $item->register_datetime);
        Services::Registry()->set('User', 'last_visit_datetime', $item->last_visit_datetime);

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
        Services::Registry()->set('User', 'Activity', $activity);

        Services::Registry()->rename('UsersCustomfields', 'UserCustomfields');
        Services::Registry()->rename('UsersParameters', 'UserParameters');
        Services::Registry()->rename('UsersMetadata', 'UserMetadata');
/**
        echo '<pre>';
        echo 'User<br />';
        Services::Registry()->get('User', '*');
        echo '</pre>';

        echo '<pre>';
        echo 'User Customfields<br />';
        var_dump(Services::Registry()->get('UserCustomfields'));
        echo '</pre>';

        echo '<pre>';
        echo 'User Parameters<br />';
        var_dump(Services::Registry()->get('UserParameters'));
        echo '</pre>';

        echo '<pre>';
        echo 'User Metadata<br />';
        var_dump(Services::Registry()->get('UserMetadata'));
        echo '</pre>';
die;
*/

        return $this;
    }
}
