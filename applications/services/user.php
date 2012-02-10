<?php
/**
 * @package     Molajo
 * @subpackage  Base
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * User Class
 *
 * @package     Molajo
 * @subpackage  User
 * @since       1.1
 */
class MolajoUserService
{
    /**
     * Instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance = array();

    /**
     * $_model
     *
     * @since  1.0
     * @var object
     */
    protected $_model = 'MolajoUserModel';

    /**
     * $_id
     *
     * @since  1.0
     * @var int
     */
    protected $_id = null;

    /**
     * $asset_type_id
     *
     * @since  1.0
     * @var int
     */
    protected $_asset_type_id = null;

    /**
     * $_username
     *
     * @since  1.0
     * @var string
     */
    protected $_username = null;

    /**
     * $_first_name
     *
     * @since  1.0
     * @var string
     */
    protected $_first_name = null;

    /**
     * $_last_name
     *
     * @since  1.0
     * @var string
     */
    protected $_last_name = null;

    /**
     * $_name
     *
     * @since  1.0
     * @var string
     */
    protected $_name = null;

    /**
     * $_content_text
     *
     * @since  1.0
     * @var string
     */
    protected $_content_text = null;

    /**
     * $_email
     *
     * @since  1.0
     * @var string
     */
    protected $_email = null;

    /**
     * $_password
     *
     * @since  1.0
     * @var string
     */
    protected $_password = null;

    /**
     * $_block
     *
     * @since  1.0
     * @var int
     */
    protected $_block = null;

    /**
     * $_activation
     *
     * @since  1.0
     * @var string activation hash
     */
    protected $_activation = null;

    /**
     * $_send_email
     *
     * @since  1.0
     * @var int
     */
    protected $_send_email = null;

    /**
     * $_register_datetime
     *
     * @since  1.0
     * @var datetime
     */
    protected $_register_datetime = null;

    /**
     * $_last_visit_datetime
     *
     * @since  1.0
     * @var datetime
     */
    protected $_last_visit_datetime = null;

    /**
     * $_custom_fields
     *
     * @var string
     */
    protected $_custom_fields = array();

    /**
     * $_parameters
     *
     * @var string
     */
    protected $_parameters = array();

    /**
     * $_applications
     *
     * @since  1.0
     * @var    array
     */
    protected $_applications = array();

    /**
     * $_groups
     *
     * @since  1.0
     * @var    array
     */
    protected $_groups = array();

    /**
     * $_view_groups
     *
     * @since  1.0
     * @var    array
     */
    protected $_view_groups = array();

    /**
     * $_public
     *
     * @since  1.0
     * @var boolean
     */
    protected $_public = null;

    /**
     * $_guest
     *
     * @since  1.0
     * @var boolean
     */
    protected $_guest = null;

    /**
     * $_registered
     *
     * @since  1.0
     * @var boolean
     */
    protected $_registered = null;

    /**
     * $_administrator
     *
     * @since  1.0
     * @var boolean
     */
    protected $_administrator = null;

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
        if (empty(self::$instances[$id])) {
            $user = new MolajoUser($id);
            self::$instances[$id] = $user;
        }
        return self::$instances[$id];
    }

    /**
     * __construct
     *
     * Constructor activating the default information of the language
     *
     * @param   integer  $identifier  The primary key of the user to load (optional).
     *
     * @return  object  user
     * @since   1.0
     */
    public function __construct($identifier = 0)
    {
        if (is_numeric($identifier)) {
            $id = $identifier;
        } else {
            $id = MolajoUserHelper::getUserInstanceID($identifier);
        }
        $this->_id = (int) $id;
    }

    /**
     * _load
     *
     * Retrieve User or Guest Information
     *
     * @param   mixed  $id either the numeric userid or character username
     *
     * @return  boolean
     * @since   1.0
     */
    public function connect()
    {
        /** retrieve data for user */
        $this->model = new MolajoUsersModel ();
        $results = $this->model->load($this->_id);
        $columns = $this->model->getFields('#__users', true);

        foreach ($results as $name => $value) {
            $protected_name = '_' . $name;
            $this->$protected_name = $results[$name];
        }

        $this->custom_fields = new Registry;
        $this->custom_fields->loadString($this->_custom_fields, 'JSON');
        $this->custom_fields->toArray();

        $this->_parameters = new Registry;
        $this->_parameters->loadString($this->_parameters, 'JSON');
        $this->_parameters->toArray();

        return $this;
    }

    /**
     * get
     *
     * Retrieves values, or establishes the value with a default, if not available
     *
     * @param  string  $key      The name of the property.
     * @param  string  $default  The default value (optional) if none is set.
     * @param  string  $type     custom, metadata, languageObject, config
     *
     * @return  mixed
     *
     * @since   1.0
     */
    public function get($key, $default = null, $type = null)
    {
        if ($type == 'custom') {
            return $this->_custom_fields->get($key, $default);

        } else if ($type == 'metadata') {
            return $this->_metadata->get($key, $default);

        } else if ($type == 'state') {
            $registry = Molajo::Application()->getSession()->get('registry');
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
            $protected_name = '_' . $key;
            return $this->$protected_name;
        }
    }

    /**
     * set
     *
     * Modifies a property, creating it and establishing a default if not existing
     *
     * @param  string  $key    The name of the property.
     * @param  mixed   $value  The default value to use if not set (optional).
     * @param  string  $type   Custom, metadata, config
     *
     * @return  mixed
     * @since   1.0
     */
    public function set($key, $value = null, $type = 'config')
    {
        if ($type == 'custom') {
            return $this->_custom_fields->set($key, $value);

        } else if ($type == 'metadata') {
            return $this->_metadata->set($key, $value);

        } else if ($type == 'service') {
            return $this->_service->set($key, $value);

        } else if ($type == 'visit') {
            return $this->model->setLastVisit();

        } else if ($type == 'state') {
            $registry = Molajo::Application()->getSession()->get('registry');
            if (is_null($registry)) {
            } else {
                return $registry->set($key, $value);
            }
            return null;

        } else {
            return $this->_configuration->set($key, $value);
        }
    }
}
