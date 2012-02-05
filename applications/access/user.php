<?php
/**
 * @package     Molajo
 * @subpackage  User
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
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
class MolajoUser extends JObject
{
    /**
     * $id
     *
     * @since  1.0
     * @var int
     */
    public $id = null;

    /**
     * $asset_type_id
     *
     * @since  1.0
     * @var int
     */
    public $asset_type_id = null;

    /**
     * $username
     *
     * @since  1.0
     * @var string
     */
    public $username = null;

    /**
     * $first_name
     *
     * @since  1.0
     * @var string
     */
    public $first_name = null;

    /**
     * $last_name
     *
     * @since  1.0
     * @var string
     */
    public $last_name = null;

    /**
     * $name
     *
     * @since  1.0
     * @var string
     */
    public $name = null;

    /**
     * $content_text
     *
     * @since  1.0
     * @var string
     */
    public $content_text = null;

    /**
     * $email
     *
     * @since  1.0
     * @var string
     */
    public $email = null;

    /**
     * $password
     *
     * @since  1.0
     * @var string
     */
    public $password = null;

    /**
     * $block
     *
     * @since  1.0
     * @var int
     */
    public $block = null;

    /**
     * $activation
     *
     * @since  1.0
     * @var string activation hash
     */
    public $activation = null;

    /**
     * $send_email
     *
     * @since  1.0
     * @var int
     */
    public $send_email = null;

    /**
     * $register_datetime
     *
     * @since  1.0
     * @var datetime
     */
    public $register_datetime = null;

    /**
     * $last_visit_datetime
     *
     * @since  1.0
     * @var datetime
     */
    public $last_visit_datetime = null;

    /**
     * $custom_fields
     *
     * @var string
     */
    public $custom_fields = array();

    /**
     * $parameters
     *
     * @var string
     */
    public $parameters = array();

    /**
     * Associative array of user => applications
     *
     * @since  1.0
     * @var    array
     */
    public $applications = array();

    /**
     * Associative array of user => group ids
     *
     * @since  1.0
     * @var    array
     */
    public $groups = array();

    /**
     * Associative array of user => view group ids
     *
     * @since  1.0
     * @var    array
     */
    public $view_groups = array();

    /**
     * $guest
     *
     * @since  1.0
     * @var boolean
     */
    public $guest = null;

    /**
     * getInstance
     *
     * Returns Global User object, creating it if it doesn't already exist.
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
            if ($id = UserHelper::getUserId($identifier)) {

            } else {
                MolajoError::raiseWarning('SOME_ERROR_CODE', TextHelper::sprintf('MOLAJO_ERROR_USER_DOES_NOT_EXISTS', $identifier));
                return false;
            }
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
    public function __construct($identifier = 0)
    {
        if (empty($identifier)) {
            $this->id = 0;
            $this->send_email = 0;
            $this->guest = 1;
            // shouldn't we load guest groups, etc?
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
        /** retrieve data for user */
        $model = $this->_getModel();
        $results = $model->load($id);
        $columns = $model->getFields('#__users', true);
        foreach ($results as $name => $value) {
            $this->$name = $results[$name];
        }

        /** extra fields */
        $this->_loadCustomFields($this->custom_fields);

        $this->_loadParameters($this->parameters);

        return true;
    }

    /**
     * query
     *
     * @param null $id
     * @param bool $reset
     */
    public function query($id = null, $reset = true)
    {
        parent::query($this->id, $reset);

        /** guest */
        $this->guest = 0;
    }

    /**
     * _getModel
     *
     * Method to get the user model object
     *
     * @param   string   $name
     * @param   string   $prefix
     *
     * @return  object   user model
     * @since   1.0
     */
    protected function _getModel($name = 'Users', $prefix = 'Molajo')
    {
        static $modeltype;

        if (isset($modeltype)) {

        } else {
            $modeltype['name'] = $name;
            $modeltype['prefix'] = $prefix;
        }

        $className = $modeltype['prefix'] . $modeltype['name'] . 'Model';

        return $className::getInstance(
            $modeltype['name'],
            $modeltype['prefix'],
            array()
        );
    }

    /**
     * setLastVisit
     *
     * Pass through method to the model for setting the last visit date
     *
     * @param   integer  $timestamp    The timestamp, defaults to 'now'.
     *
     * @return  boolean  True on success.
     * @since   1.0
     */
    public function setLastVisit($timestamp = null)
    {
        $model = $this->getModel();
        $model->load($this->id);
        return $model->setLastVisit($timestamp);
    }

    /**
     * _getCustomField
     *
     * Method to get a CustomField value
     *
     * @param   string   $key        CustomField key
     * @param   mixed    $default    CustomField default value
     *
     * @return  mixed    The value or the default if it did not exist
     * @since   1.0
     */
    public function _getCustomField($key, $default = null)
    {
        return $this->custom_fields->get($key, $default);
    }

    /**
     * _setCustomField
     *
     * Method to set a CustomField value
     *
     * @param   string   $key    CustomField key
     * @param   mixed    $value    CustomField value
     *
     * @return  mixed    Set CustomField value
     * @since   1.0
     */
    public function _setCustomField($key, $value)
    {
        return $this->custom_fields->set($key, $value);
    }

    /**
     * _loadCustomFields
     *
     * Loads user CustomFields JSON field into an array
     *
     * @since  1.0
     */
    public function _loadCustomFields($custom_fields)
    {
        $this->custom_fields = new JRegistry;
        $this->custom_fields->loadString($custom_fields, 'JSON');
        $this->custom_fields->toArray();
    }

    /**
     * getParameter
     *
     * Method to get a parameter value
     *
     * @param   string   $key        Parameter key
     * @param   mixed    $default    Parameter default value
     *
     * @return  mixed    The value or the default if it did not exist
     * @since   1.0
     */
    public function getParameter($key, $default = null)
    {
        return $this->parameters->get($key, $default);
    }

    /**
     * setParameter
     *
     * Method to set a parameter value
     *
     * @param   string   $key    Parameter key
     * @param   mixed    $value    Parameter value
     *
     * @return  mixed    Set parameter value
     * @since   1.0
     */
    public function setParameter($key, $value)
    {
        return $this->parameters->set($key, $value);
    }

    /**
     * _loadParameters
     *
     * Loads user parameters JSON field into an array
     *
     * @since  1.0
     */
    public function _loadParameters($parameters)
    {
        $this->parameters = new JRegistry;
        $this->parameters->loadString($parameters, 'JSON');
        $this->parameters->toArray();
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
        $registry = Molajo::App()->getSession()->get('registry');
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
        $registry = Molajo::App()->getSession()->get('registry');
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
     * @param   string   $type     Filter for the variable, for valid values see {@link JFilterInput::clean()}. Optional.
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

class save_user_crud
{
    /**
     * bind
     *
     * Method to bind an associative array of data to a user object
     *
     * @param   array  $array    The associative array to bind to the object
     *
     * @return  boolean  True on success
     * @since   1.0
     */
    public function bind($array)
    {
        // Let's check to see if the user is new or not
        if (empty($this->id)) {

            // Check the password and create the crypted password
            if (empty($array['password'])) {
                $array['password'] = UserHelper::genRandomPassword();
                $array['password2'] = $array['password'];
            }

            if (isset($array['password2']) && $array['password'] != $array['password2']) {
                $this->setError(TextHelper::_('MOLAJO_USER_ERROR_PASSWORD_NOT_MATCH'));
                return false;
            }

            $salt = UserHelper::genRandomPassword(32);
            $crypt = UserHelper::getCryptedPassword($array['password'], $salt);
            $array['password'] = $crypt . ':' . $salt;

            $this->set('register_datetime', Molajo::Date()->toMySQL());

            $username = $this->get('username');
            if (strlen($username) > 250) {
                $username = substr($username, 0, 250);
                $this->set('username', $username);
            }

            $password = $this->get('password');
            if (strlen($password) > 100) {
                $password = substr($password, 0, 100);
                $this->set('password', $password);
            }

        } else {
            if (empty($array['password'])) {
                $array['password'] = $this->password;

            } else {
                if ($array['password'] == $array['password2']) {
                } else {
                    $this->setError(TextHelper::_('MOLAJO_USER_ERROR_PASSWORD_NOT_MATCH'));
                    return false;
                }

                $salt = UserHelper::genRandomPassword(32);
                $crypt = UserHelper::getCryptedPassword($array['password'], $salt);
                $array['password'] = $crypt . ':' . $salt;
            }
        }


        return true;
    }

    /**
     * Method to save the User object to the database
     *
     * @param   boolean  $updateOnly    Save the object only if not a new user
     *
     * @return  boolean  True on success
     * @since   1.0
     */
    public function save($updateOnly = false)
    {
        // NOTE: $updateOnly is currently only used in the user reset password method.
        // Create the user table object
        $model = $this->getModel();
        $this->parameters = (string)$this->parameters;
        $model->bind($this->getProperties());

        // Allow an exception to be thrown.
        try
        {
            // Check and store the object.
            if ($model->check()) {
            } else {
                $this->setError($model->getError());
                return false;
            }

            // If user is made a Super Admin group and user is NOT a Super Admin
            //
            // @todo ACL - this needs to be acl checked
            //
            $my = Molajo::User();

            //are we creating a new user
            $isNew = empty($this->id);

            // If we aren't allowed to create new users return
            if ($isNew && $updateOnly) {
                return true;
            }

            // Get the old user
            $oldUser = new MolajoUser($this->id);

            //
            // Access Checks
            //

            // The only mandatory check is that only Super Admins can operate on other Super Admin accounts.
            // To add additional business rules, use a user plugin and throw an Exception with onUserBeforeSave.

            // Check if I am a Super Admin
            $acl = new MolajoACL ();

            $iAmSuperAdmin = $acl->checkPermissions('user', $my->id, 'administer', '', '');

            // We are only worried about edits to this account if I am not a Super Admin.
            if ($iAmSuperAdmin != true) {
                if ($isNew) {
                    // Check if the new user is being put into a Super Admin group.
                    foreach ($this->groups as $key => $groupId) {
                        if ($acl->checkPermissions('group', $groupId, 'administer', '', '')) {
                            throw new MolajoException(TextHelper::_('MOLAJO_USER_ERROR_NOT_SUPERADMIN'));
                        }
                    }
                } else {
                    // I am not a Super Admin, and this one is, so fail.
                    if ($acl->checkPermissions('user', $this->id, 'administer', '', '')) {
                        throw new MolajoException(TextHelper::_('MOLAJO_USER_ERROR_NOT_SUPERADMIN'));
                    }

                    if ($this->groups != null) {
                        // I am not a Super Admin and I'm trying to make one.
                        foreach ($this->groups as $groupId) {
                            if ($acl->checkPermissions('group', $groupId, 'administer', '', '')) {
                                throw new MolajoException(TextHelper::_('MOLAJO_USER_ERROR_NOT_SUPERADMIN'));
                            }
                        }
                    }
                }
            }

            // Fire the onUserBeforeSave event.
//            MolajoPluginHelper::importPlugin('user');
//            $dispatcher = JDispatcher::getInstance();

//            $result = $dispatcher->trigger('onUserBeforeSave', array($oldUser->getProperties(), $isNew, $this->getProperties()));
//            if (in_array(false, $result, true)) {
                // Plugin will have to raise it's own error or throw an exception.
//                return false;
//            }

            // Store the user data in the database
            if (!($result = $model->store())) {
                throw new MolajoException($model->getError());
            }

            // Set the id for the User object in case we created a new user.
            if (empty($this->id)) {
                $this->id = $model->get('id');
            }

            if ($my->id == $model->id) {
                $registry = new JRegistry;
                $registry->loadJSON($model->parameters);
                $my->setParameters($registry);
            }

            // Fire the onAftereStoreUser event
//            $dispatcher->trigger('onUserAfterSave', array($this->getProperties(), $isNew, $result, $this->getError()));
        }
        catch (Exception $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

        return $result;
    }

    /**
     * Method to delete the User object from the database
     *
     * @return  boolean  True on success
     * @since   1.0
     */
    public function delete()
    {
//        MolajoPluginHelper::importPlugin('user');

//        $dispatcher = JDispatcher::getInstance();
//        $dispatcher->trigger('onUserBeforeDelete', array($this->getProperties()));

        $model = $this->getModel();

        $result = $model->delete($this->id);
        // $this->setError($model->getError());

        // Trigger the onUserAfterDelete event
//        $dispatcher->trigger('onUserAfterDelete', array($this->getProperties(), $result, $this->getError()));

        return $result;
    }

}
