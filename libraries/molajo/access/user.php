<?php
/**
 * @package     Molajo
 * @subpackage  User
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * User class
 *
 * @package    Molajo
 * @subpackage  User
 * @since      11.1
 */
class MolajoUser extends JObject
{
    /**
     * A cached switch for if this user has root access rights.
     *
     * @var    boolean
     */
    protected $isRoot = null;

    /**
     * $id
     * @var int
     */
    public $id = null;

    /**
     * $username
     * @var string
     */
    public $username = null;

    /**
     * $first_name
     * @var string
     */
    public $first_name = null;

    /**
     * $last_name
     * @var string
     */
    public $last_name = null;

    /**
     * $name
     * @var string
     */
    public $name = null;

    /**
     * $content_text
     * @var string
     */
    public $content_text = null;

    /**
     * $email
     * @var string
     */
    public $email = null;

    /**
     * $password
     * MD5 encrypted password
     * @var string
     */
    public $password = null;

    /**
     * $password_clear
     * Clear password, only available when a new password is set for a user
     * @var string
     */
    public $password_clear = '';

    /**
     * $block
     * @var int
     */
    public $block = null;

    /**
     * $send_email
     * Send Email switch
     * @var int
     */
    public $send_email = null;

    /**
     * $register_datetime
     * @var datetime
     */
    public $register_datetime = null;

    /**
     * $last_visit_datetime
     * @var datetime
     */
    public $last_visit_datetime = null;

    /**
     * $activated
     * @var string activated hash
     */
    public $activated = null;

    /**
     * $parameters
     * @var string
     */
    public $parameters = null;

    /**
     * $custom_fields
     * @var string
     */
    public $custom_fields = null;

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
     * @var boolean
     */
    public $guest = null;

    /**
     * User parameters
     * @var object
     */
    protected $_parameters = null;

    /**
     * Error message
     * @var string
     */
    protected $_errorMsg = null;

    /**
     * getInstance
     *
     * Returns the global User object, only creating it if it doesn't already exist.
     *
     * @param   integer  $identifier    The user to load - Can be an integer or string - If string, it is converted to ID automatically.
     *
     * @return  object   MolajoUser    The User object.
     * @since   1.0
     */
    public static function getInstance($identifier = 0)
    {
        static $instances;

        if (!isset ($instances)) {
            $instances = array();
        }

        if (is_numeric($identifier)) {
            $id = $identifier;

        } else {
            if ($id = MolajoUserHelper::getUserId($identifier)) {

            } else {
                MolajoError::raiseWarning('SOME_ERROR_CODE', MolajoText::sprintf('JLIB_USER_ERROR_ID_NOT_EXISTS', $identifier));
                $retval = false;
                return $retval;
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
        // Create the user parameters object
        $this->_parameters = new JRegistry;

        // Load the user if it exists
        if (empty($identifier)) {
            $this->id = 0;
            $this->send_email = 0;
            $this->guest = 1;
        } else {
            $this->load($identifier);
        }
    }

    /**
     * getParam
     *
     * Method to get a parameter value
     *
     * @param   string   $key        Parameter key
     * @param   mixed    $default    Parameter default value
     *
     * @return  mixed    The value or the default if it did not exist
     * @since   1.0
     */
    public function getParam($key, $default = null)
    {
        return $this->_parameters->get($key, $default);
    }

    /**
     * setParam
     *
     * Method to set a parameter
     *
     * @param   string   $key    Parameter key
     * @param   mixed    $value    Parameter value
     *
     * @return  mixed    Set parameter value
     * @since   1.0
     */
    public function setParam($key, $value)
    {
        return $this->_parameters->set($key, $value);
    }

    /**
     * defParam
     *
     * Method to set a default parameter if it does not exist
     *
     * @param   string   $key    Parameter key
     * @param   mixed    $value    Parameter value
     *
     * @return  mixed    Set parameter value
     * @since   1.0
     */
    public function defParam($key, $value)
    {
        return $this->_parameters->def($key, $value);
    }

    /**
     * Pass through method to the table for setting the last visit date
     *
     * @param   integer  $timestamp    The timestamp, defaults to 'now'.
     *
     * @return  boolean  True on success.
     * @since   1.0
     */
    public function setLastVisit($timestamp = null)
    {
        $table = $this->getTable();
        $table->load($this->id);
        return $table->setLastVisit($timestamp);
    }

    /**
     * getParameters
     *
     * Method to get the user parameters
     *
     * This function tries to load an XML file based on the users usertype. The filename of the xml
     * file is the same as the usertype. The functionals has a static variable to store the parameters
     * setup file base path. You can call this function statically to set the base path if needed.
     *
     * @param   boolean  $loadsetupfile    If true, loads the parameters setup file. Default is false.
     * @param   path    $path            Set the parameters setup file base path to be used to load the user parameters.
     *
     * @return  object   The user parameters object.
     * @since   1.0
     */
    public function getParameters($loadsetupfile = false, $path = null)
    {
        static $parampath;

        // Set a custom parampath if defined
        if (isset($path)) {
            $parampath = $path;
        }

        // Set the default parampath if not set already
        if (isset($parampath)) {
        } else {
            $parampath = MOLAJO_EXTENSION_COMPONENTS.'/com_users/models';
        }

        if ($loadsetupfile) {
            $type = str_replace(' ', '_', strtolower($this->usertype));

            $file = $parampath.'/'.$type.'.xml';
            if (file_exists($file)) {
            } else {
                $file = $parampath.'/'.'user.xml';
            }

            $this->_parameters->loadSetupFile($file);
        }

        return $this->_parameters;
    }

    /**
     * setParameters
     *
     * Method to get the user parameters
     *
     * @param   object   $parameters    The user parameters object
     *
     * @return  void
     * @since   1.0
     */
    public function setParameters($parameters)
    {
        $this->_parameters = $parameters;
    }

    /**
     * getTable
     *
     * Method to get the user table object
     *
     * This function uses a static variable to store the table name of the user table to
     * it instantiates. You can call this function statically to set the table name if
     * needed.
     *
     * @param   string   $type    The user table name to be used
     * @param   string   $prefix    The user table prefix to be used
     *
     * @return  object   The user table object
     * @since   1.0
     */
    public static function getTable($type = null, $prefix = 'MolajoTable')
    {
        static $tabletype;

        // Set the default tabletype;
        if (isset($tabletype)) {

        } else {
            $tabletype['name'] = 'User';
            $tabletype['prefix'] = 'MolajoTable';
        }

        // Set a custom table type is defined
        if (isset($type)) {
            $tabletype['name'] = $type;
            $tabletype['prefix'] = $prefix;
        }

        // Create the user table object
        return MolajoTable::getInstance($tabletype['name'], $tabletype['prefix']);
    }

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
                $array['password'] = MolajoUserHelper::genRandomPassword();
                $array['password2'] = $array['password'];
            }

            if (isset($array['password2']) && $array['password'] != $array['password2']) {
                $this->setError(MolajoText::_('MOLAJO_USER_ERROR_PASSWORD_NOT_MATCH'));
                return false;
            }

            $this->password_clear = JArrayHelper::getValue($array, 'password', '', 'string');

            $salt = MolajoUserHelper::genRandomPassword(32);
            $crypt = MolajoUserHelper::getCryptedPassword($array['password'], $salt);
            $array['password'] = $crypt.':'.$salt;

            // Set the registration timestamp
            $this->set('register_datetime', MolajoFactory::getDate()->toMySQL());

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
            // Updating an existing user
            if (empty($array['password'])) {
                $array['password'] = $this->password;

            } else {
                if ($array['password'] == $array['password2']) {
                } else {
                    $this->setError(MolajoText::_('MOLAJO_USER_ERROR_PASSWORD_NOT_MATCH'));
                    return false;
                }

                $this->password_clear = JArrayHelper::getValue($array, 'password', '', 'string');

                $salt = MolajoUserHelper::genRandomPassword(32);
                $crypt = MolajoUserHelper::getCryptedPassword($array['password'], $salt);
                $array['password'] = $crypt.':'.$salt;
            }
        }

        $db = MolajoFactory::getDbo();

        if (array_key_exists('parameters', $array)) {
            $parameters = '';

            $this->_parameters->loadArray($array['parameters']);

            if (is_array($array['parameters'])) {
                $parameters = (string)$this->_parameters;
            } else {
                $parameters = $array['parameters'];
            }

            $this->parameters = $parameters;
        }

        // Bind the array
        if ($this->setProperties($array)) {
        } else {
            $this->setError(MolajoText::_('MOLAJO_USER_ERROR_BIND_ARRAY'));
            return false;
        }

        $this->id = (int)$this->id;

        return true;
    }

    /**
     * Method to save the MolajoUser object to the database
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
        $table = $this->getTable();
        $this->parameters = (string)$this->_parameters;
        $table->bind($this->getProperties());

        // Allow an exception to be thrown.
        try
        {
            // Check and store the object.
            if ($table->check()) {
            } else {
                $this->setError($table->getError());
                return false;
            }

            // If user is made a Super Admin group and user is NOT a Super Admin
            //
            // @todo ACL - this needs to be acl checked
            //
            $my = MolajoFactory::getUser();

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

            $iAmSuperAdmin = $acl->checkPermissions('user', $my->id, 'admin', '', '');

            // We are only worried about edits to this account if I am not a Super Admin.
            if ($iAmSuperAdmin != true) {
                if ($isNew) {
                    // Check if the new user is being put into a Super Admin group.
                    foreach ($this->groups as $key => $groupId) {
                        if ($acl->checkPermissions('group', $groupId, 'admin', '', '')) {
                            throw new Exception(MolajoText::_('MOLAJO_USER_ERROR_NOT_SUPERADMIN'));
                        }
                    }
                } else {
                    // I am not a Super Admin, and this one is, so fail.
                    if ($acl->checkPermissions('user', $this->id, 'admin', '', '')) {
                        throw new Exception(MolajoText::_('MOLAJO_USER_ERROR_NOT_SUPERADMIN'));
                    }

                    if ($this->groups != null) {
                        // I am not a Super Admin and I'm trying to make one.
                        foreach ($this->groups as $groupId) {
                            if ($acl->checkPermissions('group', $groupId, 'admin', '', '')) {
                                throw new Exception(MolajoText::_('MOLAJO_USER_ERROR_NOT_SUPERADMIN'));
                            }
                        }
                    }
                }
            }

            // Fire the onUserBeforeSave event.
            MolajoPluginHelper::importPlugin('user');
            $dispatcher = JDispatcher::getInstance();

            $result = $dispatcher->trigger('onUserBeforeSave', array($oldUser->getProperties(), $isNew, $this->getProperties()));
            if (in_array(false, $result, true)) {
                // Plugin will have to raise it's own error or throw an exception.
                return false;
            }

            // Store the user data in the database
            if (!($result = $table->store())) {
                throw new Exception($table->getError());
            }

            // Set the id for the MolajoUser object in case we created a new user.
            if (empty($this->id)) {
                $this->id = $table->get('id');
            }

            if ($my->id == $table->id) {
                $registry = new JRegistry;
                $registry->loadJSON($table->parameters);
                $my->setParameters($registry);
            }

            // Fire the onAftereStoreUser event
            $dispatcher->trigger('onUserAfterSave', array($this->getProperties(), $isNew, $result, $this->getError()));
        }
        catch (Exception $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

        return $result;
    }

    /**
     * Method to delete the MolajoUser object from the database
     *
     * @return  boolean  True on success
     * @since   1.0
     */
    public function delete()
    {
        MolajoPluginHelper::importPlugin('user');

        // Trigger the onUserBeforeDelete event
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onUserBeforeDelete', array($this->getProperties()));

        // Create the user table object
        $table = $this->getTable();

        $result = false;
        if (!$result = $table->delete($this->id)) {
            $this->setError($table->getError());
        }

        // Trigger the onUserAfterDelete event
        $dispatcher->trigger('onUserAfterDelete', array($this->getProperties(), $result, $this->getError()));

        return $result;
    }

    /**
     * load
     *
     * Method to load a MolajoUser object by user id number
     *
     * @param   mixed  $id  The user id of the user to load
     *
     * @return  boolean  True on success
     * @since   1.0
     */
    public function load($id)
    {
        $table = $this->getTable();
        if ($table->load($id)) {
        } else {
            MolajoError::raiseWarning('SOME_ERROR_CODE', MolajoText::sprintf('MOLAJO_USER_ERROR_UNABLE_TO_LOAD_USER', $id));
            return false;
        }
        $this->_parameters->loadJSON($table->parameters);

        $this->setProperties($table->getProperties());

        return true;
    }

    /**
     * @deprecated 1.6    Use MolajoACL::checkPermissions method instead.
     */
    public function authorize($action, $assetname = null)
    {
        $this->authorise($action, $assetname);
    }

    /**
     * @deprecated 1.6    Use MolajoACL::checkPermissions method instead.
     */
    public function authorise($action, $assetname = null)
    {
        $acl = new MolajoACL ();
        return $acl->checkPermissions('user', $userId = '', $action, $assetname, '');
    }

    /**
     * @deprecated 1.6    Use the getList method instead.
     */
    public function authorisedLevels()
    {
        $acl = new MolajoACL ();
        return $acl->getList('Usergroupings', $userId = '', $option = '', $task = '', $parameters = array());
    }

    /**
     * @deprecated 1.6    Use the getList method instead.
     */
    public function getAuthorisedCategories($component, $action)
    {
        $acl = new MolajoACL ();
        return $acl->getList('Usercategories', $userId = '', $option = '', $task = '', $parameters = array());
    }

    /**
     * @deprecated 1.6    Use the getList method instead.
     */
    public function getAuthorisedViewLevels()
    {
        $acl = new MolajoACL();
        return $acl->getList('viewaccess', $userId = '', $option = '', $task = '', $parameters = array());
    }

    /**
     * @deprecated 1.6    Use the getList method instead.
     */
    public function getAuthorisedGroups()
    {
        $acl = new MolajoACL ();
        return $acl->getList('Usergroups’', $userId = '', $option = '', $task = '', $parameters = array());
    }
}
