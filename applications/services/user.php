<?php
/**
 * @package     Molajo
 * @subpackage  Service
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * User Class
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
class MolajoUserService
{
    /**
     * Instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instances = array();

    /**
     * $model
     *
     * @since  1.0
     * @var object
     */
    protected $model = 'MolajoUsersModel';

    /**
     * $id
     *
     * @since  1.0
     * @var int
     */
    protected $id = null;

    /**
     * $asset_type_id
     *
     * @since  1.0
     * @var int
     */
    protected $asset_type_id = null;

    /**
     * $username
     *
     * @since  1.0
     * @var string
     */
    protected $username = null;

    /**
     * $first_name
     *
     * @since  1.0
     * @var string
     */
    protected $first_name = null;

    /**
     * $last_name
     *
     * @since  1.0
     * @var string
     */
    protected $last_name = null;

    /**
     * $name
     *
     * @since  1.0
     * @var string
     */
    protected $name = null;

    /**
     * $content_text
     *
     * @since  1.0
     * @var string
     */
    protected $content_text = null;

    /**
     * $email
     *
     * @since  1.0
     * @var string
     */
    protected $email = null;

    /**
     * $password
     *
     * @since  1.0
     * @var string
     */
    protected $password = null;

    /**
     * $block
     *
     * @since  1.0
     * @var int
     */
    protected $block = null;

    /**
     * $activation
     *
     * @since  1.0
     * @var string activation hash
     */
    protected $activation = null;

    /**
     * $send_email
     *
     * @since  1.0
     * @var int
     */
    protected $send_email = null;

    /**
     * $register_datetime
     *
     * @since  1.0
     * @var datetime
     */
    protected $register_datetime = null;

    /**
     * $last_visit_datetime
     *
     * @since  1.0
     * @var datetime
     */
    protected $last_visit_datetime = null;

    /**
     * $custom_fields
     *
     * @var string
     */
    protected $custom_fields = array();

    /**
     * $metadata
     *
     * @var string
     */
    protected $metadata = array();

    /**
     * $parameters
     *
     * @var string
     */
    protected $parameters = array();

    /**
     * $applications
     *
     * @since  1.0
     * @var    array
     */
    protected $applications = array();

    /**
     * $groups
     *
     * @since  1.0
     * @var    array
     */
    protected $groups = array();

    /**
     * $view_groups
     *
     * @since  1.0
     * @var    array
     */
    protected $view_groups = array();

    /**
     * $public
     *
     * @since  1.0
     * @var boolean
     */
    protected $public = null;

    /**
     * $guest
     *
     * @since  1.0
     * @var boolean
     */
    protected $guest = null;

    /**
     * $registered
     *
     * @since  1.0
     * @var boolean
     */
    protected $registered = null;

    /**
     * $administrator
     *
     * @since  1.0
     * @var boolean
     */
    protected $administrator = null;

    /**
     * getInstance
     *
     * @param   string $identifier  Requested User (id or username)
     *
     * @return  object  User
     * @since   1.0
     */
    public static function getInstance($id = 0)
    {
        if (is_numeric($id)) {
        } else {
            $id = MolajoUserHelper::getId($id);
        }
        if (empty(self::$instances[$id])) {
            $user = new MolajoUserService($id);
            self::$instances[$id] = $user;
        }
        return self::$instances[$id];
    }

    /**
     * __construct
     *
     * @param   integer  $identifier
     *
     * @return  object
     * @since   1.0
     */
    protected function __construct($id = 0)
    {
        $this->id = (int) $id;
        $this->read();
    }

    /**
     * read
     *
     * Retrieve User or Guest Information
     *
     * @param   mixed  $id either the numeric userid or character username
     *
     * @return  boolean
     * @since   1.0
     */
    protected function read()
    {
        $this->model = new $this->model ();
        $results = $this->model->read($this->id);
        $columns = $this->model->getFields('#__users', true);

        foreach ($results as $name => $value) {
            $this->set($name, $value);
        }

        $custom_fields = new Registry;
        $custom_fields->loadString($this->custom_fields);
        $this->set('custom_fields', $custom_fields);

        $metadata = new Registry;
        $metadata->loadString($this->metadata);
        $this->set('metadata', $metadata);

        $parameters = new Registry;
        $parameters->loadString($this->parameters);
        $this->set('parameters', $parameters);
/**
        echo '<pre>';
        var_dump($this->parameters);
        echo '</pre>';
*/
        return $this;
    }

    /**
     * get
     *
     * Retrieves values, or establishes the value with a default,
     * if not available
     *
     * @param  string  $key
     * @param  string  $default
     * @param  string  $type
     *
     * @return  mixed
     *
     * @since   1.0
     */
    public function get($key, $default = null, $type = null)
    {
        if ($type == 'custom') {
            return $this->custom_fields->get($key, $default);

        } else if ($type == 'metadata') {
            return $this->metadata->get($key, $default);

        } else if ($type == 'state') {
            $registry = Molajo::Application()
                ->getSession()
                ->get('registry');
            if (is_null($registry)) {
            } else {
                return $registry->get($key, $default);
            }
            return $default;

            /** combine with get getUserStateFromRequest
            $cur_state = $this->getUserState($key, $default);
            $new_state = JRequest::getVar($request, null, 'default', $type);

            // Save the new value only if it was set in this request.
            if ($new_state == null) {
            $new_state = $cur_state;
            } else {
            $this->setUserState($key, $new_state);
            }

            return $new_state;
             */
        } else {
            if (isset($this->$key)) {
                return $this->$key;
            }
            return false;
        }
    }

    /**
     * set
     *
     * Modifies a property, creating it and establishing
     * a default if not existing
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  string  $type
     *
     * @return  mixed
     * @since   1.0
     */
    public function set($key, $value = null, $type = 'config')
    {
        if ($type == 'custom') {
            return $this->custom_fields->set($key, $value);

        } else if ($type == 'metadata') {
            return $this->metadata->set($key, $value);

        } else if ($type == 'service') {
            return $this->service->set($key, $value);

        } else if ($type == 'visit') {
            return $this->model->setLastVisit();

        } else if ($type == 'state') {
            $registry = Molajo::Application()
                ->getSession()
                ->get('registry');
            if (is_null($registry)) {
            } else {
                return $registry->set($key, $value);
            }
            return null;

        } else {
            return $this->$key = $value;
        }
    }
}
