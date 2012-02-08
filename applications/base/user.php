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
class MolajoUser
{
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
    public static function getInstance($identifier = 0)
    {
        static $instances;
        if (isset ($instances)) {
        } else {
            $instances = array();
        }

        if (is_numeric($identifier)) {
            $id = $identifier;
        } else {
            $id = UserServices::getUserId($identifier);
        }
        if (is_numeric($identifier)) {
        } else {
            MolajoError::raiseWarning('SOME_ERROR_CODE', TextServices::sprintf('MOLAJO_ERROR_USER_DOES_NOT_EXISTS', $identifier));
            return false;
        }

        if (empty($instances[$id])) {
            $user = new MolajoUser($id);
            $instances[$id] = $user;
        }
        return $instances[$id];
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
    protected function __construct($identifier = 0)
    {
        if (empty($identifier)) {
            $this->_id = 0;
            $this->_send_email = 0;
            $this->_guest = 1;
            //todo: amy shouldn't we load guest groups, etc?
        } else {
            $this->_load($identifier);
        }
    }

    /**
     * _load
     *
     * Method to load a User object by user id number
     *
     * @param   mixed  $id  The user id of the user to load
     *
     * @return  boolean
     * @since   1.0
     */
    protected function _load($id)
    {
        $this->_id = $id;

        /** session */

        /** retrieve data for user */
        $model = $this->_getModel();
        $results = $model->load($this->_id);
        $columns = $model->getFields('#__users', true);

        foreach ($results as $name => $value) {
            $protected_name = '_' . $name;
            $this->$protected_name = $results[$name];
        }

        /** extra fields */
        $this->_loadCustomFields($this->_custom_fields);

        $this->_loadParameters($this->_parameters);

        return true;
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

        } else {
            $protected_name = '_' . $key;
            return $this->$protected_name;
        }
    }

    /**
     * setLastVisit
     *
     * @param   $timestamp
     *
     * @return  boolean
     * @since   1.0
     */
    public function setLastVisit($timestamp = null)
    {
        $model = $this->_getModel();
        $model->load($this->_id);
        return $model->setLastVisit($timestamp);
    }

    /**
     * _loadCustomFields
     *
     * Loads user CustomFields JSON field into an array
     *
     * @since  1.0
     */
    protected function _loadCustomFields($_custom_fields)
    {
        $this->custom_fields = new Registry;
        $this->custom_fields->loadString($_custom_fields, 'JSON');
        $this->custom_fields->toArray();
    }

    /**
     * _loadParameters
     *
     * Loads user parameters JSON field into an array
     *
     * @since  1.0
     */
    protected function _loadParameters($parameters)
    {
        $this->_parameters = new Registry;
        $this->_parameters->loadString($parameters, 'JSON');
        $this->_parameters->toArray();
    }

    /**
     * _getModel
     *
     * @return  object
     * @since   1.0
     */
    protected function _getModel()
    {
        return new MolajoUsersModel ();
    }

    /**
     * getUserState
     *
     * Gets a user state.
     *
     * @param   string  The path of the state.
     * @param   mixed   Optional default value, returned if the internal value is null.
     *
     * @return  mixed  The user state or null.
     *
     * @since  1.0
     */
    public function getUserState($key, $default = null)
    {
        $registry = Molajo::Application()->getSession()->get('registry');
        if (is_null($registry)) {
        } else {
            return $registry->get($key, $default);
        }
        return $default;
    }

    /**
     * setUserState
     *
     * Sets the value of a user state variable.
     *
     * @param   string  The path of the state.
     * @param   string  The value of the variable.
     *
     * @return  mixed   The previous state, if one existed.
     *
     * @since  1.0
     */
    public function setUserState($key, $value)
    {
        $registry = Molajo::Application()->getSession()->get('registry');
        if (is_null($registry)) {
        } else {
            return $registry->set($key, $value);
        }
        return null;
    }

    /**
     * getUserStateFromRequest
     *
     * Gets the value of a user state variable.
     *
     * @param   string   $key      The key of the user state variable.
     * @param   string   $request  The name of the variable passed in a request.
     * @param   string   $default  The default value for the variable if not found. Optional.
     * @param   string   $type     Filter for the variable, for valid values see {@link FilterInput::clean()}. Optional.
     *
     * @return  The request user state.
     *
     * @since  1.0
     */
    public function getUserStateFromRequest($key, $request, $default = null, $type = 'none')
    {
        $cur_state = $this->getUserState($key, $default);
        $new_state = JRequest::getVar($request, null, 'default', $type);

        // Save the new value only if it was set in this request.
        if ($new_state == null) {
            $new_state = $cur_state;
        } else {
            $this->setUserState($key, $new_state);
        }

        return $new_state;
    }
}
