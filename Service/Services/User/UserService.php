<?php
/**
 * User Service
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\User;

use Molajo\Service\Services\Theme\Helper\ExtensionHelper;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * User Service
 *
 * 1. Gets User Data
 * 2. Loads arrays for authorised applications, groups, view_groups, extensions, extension_titles
 * 3. Loads arrays for parameters, customfields, and metadata
 *
 * @author     Amy Stephen
 * @license    MIT
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 *
 * Usage
 *  -- for current visitor, whether they are logged in, or not
 *  -- automatically run during service startup
 *
 *  Services::User()->get($key);
 *  Services::User()->set($key, $value);
 *
 * Usage
 *  -- for any user
 *  -- new instance
 *
 *  $userInstance = UserService ($id);
 *  $userInstance->load();
 *
 *  echo $userInstance->get('username');
 *
 *  $userInstance->set($key, $value);
 */
Class UserService
{
    /**
     * ID for visitor
     *
     * @var    string
     * @since  1.0
     */
    protected $id;

    /**
     * Parameters for User
     *
     * @var    array
     * @since  1.0
     */
    protected $password;

    /**
     * Parameters for User
     *
     * @var    array
     * @since  1.0
     */
    protected $parameters = array();

    /**
     * Custom Fields for User
     *
     * @var    array
     * @since  1.0
     */
    protected $customfields = array();

    /**
     * Metadata for User
     *
     * @var    array
     * @since  1.0
     */
    protected $metadata = array();

    /**
     * Authorised Applications for User
     *
     * @var    string
     * @since  1.0
     */
    protected $applications = array();

    /**
     * Authorised Groups for User
     *
     * @var    string
     * @since  1.0
     */
    protected $groups = array();

    /**
     * Authorised View Groups for User
     *
     * @var    array
     * @since  1.0
     */
    protected $view_groups = array();

    /**
     * Authorised Extensions for User
     *
     * @var    array
     * @since  1.0
     */
    protected $authorised_extensions = array();

    /**
     * Authorised Extension Titles for User
     *
     * @var    array
     * @since  1.0
     */
    protected $authorised_extension_titles = array();

    /**
     * List of Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $property_array = array(
        'id',
        'site_id',
        'catalog_type_id',
        'username',
        'first_name',
        'last_name',
        'full_name',
        'alias',
        'content_text',
        'email',
        'password',
        'block',
        'register_datetime',
        'activation_datetime',
        'last_visit_datetime',
        'catalog_types_title',
        'catalog_types_alias',
        'customfields',
        'parameters',
        'metadata',
        'applications',
        'groups',
        'view_groups',
        'password',
        'properties',
        'authorised_extensions',
        'authorised_extension_titles'
    );

    /**
     * Construct
     *
     * @param   $id
     *
     * @return  void
     * @since   1.0
     */
    public function __construct($id)
    {
        $this->id = int($id);

        return;
    }

    /**
     * Retrieve User Information (both authenticated and guest)
     *
     * @return  void
     * @since   1.0
     */
    public function initialise()
    {
        $this->parameters                  = array();
        $this->metadata                    = array();
        $this->customfields                = array();
        $this->applications                = array();
        $this->groups                      = array();
        $this->view_groups                 = array();
        $this->authorised_extensions       = array();
        $this->authorised_extension_titles = array();

        $this->load();

        return;
    }

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  mixed
     * @since   1.0
     * @throws  \OutOfRangeException
     */
    public function get($key = null, $default = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {

        } else {
            throw new \OutOfRangeException('User Service: is attempting to get value for unknown key: ' . $key);
        }

        if ($this->$key === null) {
            $this->$key = $default;
        }

        return $this->$key;
    }

    /**
     * Set the value of a specified key
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed
     * @since   1.0
     * @throws  \OutOfRangeException
     */
    public function set($key, $value = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
        } else {
            throw new \OutOfRangeException('User Service: is attempting to set value for unknown key: ' . $key);
        }

        $this->$key = $value;

        return $this->$key;
    }

    /**
     * Retrieve User Information (both authenticated and guest)
     *
     * @return  object  UserService
     * @since   1.0
     * @throws  \RuntimeException
     */
    protected function load()
    {
        $item = $this->getUserData();

        $item = $this->setApplications($item);

        $item = $this->setGroups($item);

        $item = $this->setViewgroups($item);

        foreach (get_object_vars($item) as $key => $value) {
            $this->set($key, $value);
        }

        if ($this->get('id', 0) == 0) {
            $this->set('public', 1);
            $this->set('guest', 1);
            $this->set('registered', 0);

        } else {
            $this->set('public', 1);
            $this->set('guest', 0);
            $this->set('registered', 1);
        }

        $this->setAuthorisedExtensions();


        return $this;
    }

    /**
     * Get data for site visitor (user or guest)
     *
     * @returns  array
     * @since    1.0
     * @throws   \RuntimeException
     */
    protected function getUserData()
    {
        $controllerClass = CONTROLLER_CLASS_NAMESPACE;
        $controller      = new $controllerClass();
        $controller->getModelRegistry('Datasource', 'User', 1);

        $controller->set('primary_key_value', (int)$this->get('id'), 'model_registry');
        $controller->set('get_customfields', 2, 'model_registry');
        $controller->set('use_special_joins', 1, 'model_registry');
        $controller->set('process_plugins', 1, 'model_registry');

        $item = $controller->getData(QUERY_OBJECT_ITEM);
        if (is_array($item)) {
        } else {
            $item = array();
        }
        if (count($item) == 0) {
            throw new \RuntimeException ('User Service: Load User Query Failed');
        }

        unset($item->customfields);
        unset($item->metadata);
        unset($item->parameters);

        $this->set('password', $item->password);

        return $item;
    }

    /**
     * Set Applications for which User is Authorised to Login
     *
     * @param   $item
     *
     * @return  array  $item
     * @since   1.0
     * @throws  \RuntimeException
     */
    protected function setApplications($item)
    {
        if (is_array($item)) {
        } else {
            $item = array();
        }

        $this->applications = array();

        if (count($item) == 0) {
        } else {
            $x = $item->Userapplications;
            if (count($x) > 0) {
                foreach ($x as $app) {
                    $this->applications[] = $app->application_id;
                }
            }
        }

        array_unique($this->applications);

        if (count($this->applications) == 0) {
            throw new \RuntimeException ('User Service: User is not authorised for any applications.');
        }

        unset($item->Userapplications);

        return $item;
    }

    /**
     * Set Groups the User is authorised for
     *
     * @param   $item
     *
     * @return  array
     * @since   1.0
     * @throws  \RuntimeException
     */
    protected function setGroups($item)
    {
        if (is_array($item)) {
        } else {
            $item = array();
        }

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

        if (in_array(SYSTEM_GROUP_ADMINISTRATOR, $temp)) {
            $this->set('administrator', 1);
            $this->set('authorised_for_offline_access', 1);
        } else {
            $this->set('administrator', 0);
            $this->set('authorised_for_offline_access', 0);
        }

        $temp2 = array_unique($temp);

        $this->set('groups', $temp2);

        return $item;
    }

    /**
     * Set View Groups the User is authorised for
     *
     * @param   $item
     *
     * @return  array  $item
     * @since   1.0
     * @throws  \RuntimeException
     */
    protected function setViewgroups($item)
    {
        if (is_array($item)) {
        } else {
            $item = array();
        }

        $temp = array();
        $x    = $item->Userviewgroups;
        if (count($x) > 0) {
            foreach ($x as $vg) {
                $temp[] = $vg->view_group_id;
            }
        }

        $temp[] = SYSTEM_GROUP_PUBLIC;

        if (in_array(SYSTEM_GROUP_REGISTERED, $temp)) {
        } else {
            $temp[] = SYSTEM_GROUP_GUEST;
        }

        unset($item->Userviewgroups);

        $temp2 = array_unique($temp);

        $this->set('view_groups', $temp2);

        return $item;
    }

    /**
     * Retrieve all Extensions the logged on User is authorised to use. The Extension Helper will use this
     *  registry to avoid a new read when processing requests for Themes, Views, Plugins, Services, etc.
     *
     * @return  bool
     * @since   1.0
     * @throws  \Exception
     */
    protected function setAuthorisedExtensions()
    {
        $this->extensionHelper = new ExtensionHelper();
        $results               = $this->extensionHelper->get(0, null, null, null, 1);
        if ($results === false || count($results) == 0) {
            throw new \Exception('User Service: No authorised Extension Instances.');
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

        return true;
    }
}
