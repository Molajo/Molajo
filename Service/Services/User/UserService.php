<?php
/**
 * User Service
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\User;

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
     * Language
     *
     * @var    array
     * @since  1.0
     */
    protected $language;

    /**
     * Administrator
     *
     * @var    string
     * @since  1.0
     */
    protected $administrator;

    /**
     * Authorised for Offline Access
     *
     * @var    string
     * @since  1.0
     */
    protected $authorised_for_offline_access;

    /**
     * Public
     *
     * @var    string
     * @since  1.0
     */
    protected $public;

    /**
     * Guest
     *
     * @var    string
     * @since  1.0
     */
    protected $guest;

    /**
     * Public
     *
     * @var    string
     * @since  1.0
     */
    protected $registered;

    /**
     * Model Registry
     *
     * @var    array
     * @since  1.0
     */
    protected $model_registry;

    /**
     * Data
     *
     * @var    array
     * @since  1.0
     */
    protected $data;

    /**
     * Parameters for User
     *
     * @var    array
     * @since  1.0
     */
    protected $parameters = array();

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
        'password',
        'language',
        'authorised_for_offline_access',
        'public',
        'guest',
        'registered',
        'administrator',
        'model_registry',
        'data',
        'parameters',
        'metadata',
        'applications',
        'groups',
        'view_groups',
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
    public function __construct($id = 0)
    {
        $this->id = (int)$id;

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
        $this->applications                = array();
        $this->authorised_extension_titles = array();
        $this->authorised_extensions       = array();
        $this->data                        = array();
        $this->groups                      = array();
        $this->metadata                    = array();
        $this->model_registry              = array();
        $this->parameters                  = array();
        $this->view_groups                 = array();

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
            if (isset($this->$key)) {
            } else {
                $this->$key = $default;
            }

            return $this->$key;
        }

        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        if (isset($this->metadata[$key])) {
            return $this->metadata[$key];
        }

        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }

        $this->parameters[$key] = $default;

        return $this->parameters[$key];
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
            $this->$key = $value;

            return $this->$key;
        }

        if (isset($this->data[$key])) {
            $this->data[$key] = $value;

            return $this->data[$key];
        }

        if (isset($this->metadata[$key])) {
            $this->metadata[$key] = $value;

            return $this->metadata[$key];
        }

        $this->parameters[$key] = $value;

        return $this->parameters[$key];
    }

    /**
     * Checks to see that the user is authorised to use this extension
     *
     * @param   string  $extension_instance_id
     *
     * @return  bool
     * @since   1.0
     */
    public function checkAuthorised($extension_instance_id)
    {
        if (in_array($extension_instance_id, $this->authorised_extensions)) {
            return $this->authorised_extensions[$extension_instance_id];
        }

        return false;
    }

    /**
     * Get data for site visitor (user or guest)
     *
     * @returns  void
     * @since    1.0
     * @throws   \RuntimeException
     */
    public function getUserData()
    {
        if (is_object($this->data)) {
        } else {
            throw new \RuntimeException ('User Service: Load User Query Failed');
        }

        $this->language = $this->data->language;
        if ($this->language == '') {
            $this->language = 'en-GB';
        }
        $this->setApplications();

        $this->setGroups();

        $this->setViewgroups();

        if ($this->get('id', 0) == 0) {
            $this->set('public', 1);
            $this->set('guest', 1);
            $this->set('registered', 0);

        } else {
            $this->set('public', 1);
            $this->set('guest', 0);
            $this->set('registered', 1);
        }

        return;
    }

    /**
     * Set Applications for which User is Authorised to Login
     *
     * @return  void
     * @since   1.0
     * @throws  \RuntimeException
     */
    protected function setApplications()
    {
        $this->applications = array();

        $x = $this->data->Userapplications;

        if (count($x) > 0) {
            foreach ($x as $app) {
                $this->applications[] = $app->application_id;
            }
        }

        array_unique($this->applications);

        if (count($this->applications) == 0) {
            throw new \RuntimeException ('User Service: User is not authorised for any applications.');
        }

        unset($this->data->Userapplications);

        return;
    }

    /**
     * Set Groups the User is authorised for
     *
     * @return  array
     * @since   1.0
     * @throws  \RuntimeException
     */
    protected function setGroups()
    {
        $temp = array();

        $x = $this->data->Usergroups;

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

        unset($this->data->Usergroups);

        if (in_array(SYSTEM_GROUP_ADMINISTRATOR, $temp)) {
            $this->set('administrator', 1);
            $this->set('authorised_for_offline_access', 1);

        } else {
            $this->set('administrator', 0);
            $this->set('authorised_for_offline_access', 0);
        }

        $temp2 = array_unique($temp);

        $this->set('groups', $temp2);

        return;
    }

    /**
     * Set View Groups the User is authorised for
     *
     * @return  array
     * @since   1.0
     * @throws  \RuntimeException
     */
    protected function setViewgroups()
    {
        $temp = array();
        $x    = $this->data->Userviewgroups;
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

        unset($this->data->Userviewgroups);

        $temp2 = array_unique($temp);

        $this->set('view_groups', $temp2);

        return $this->data;
    }
}
