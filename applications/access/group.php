<?php
/**
 * @package     Molajo
 * @subpackage  Group
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo Group
 *
 * Provides CRUD API to group table
 *
 * @package     Molajo
 * @subpackage  Group
 * @since       1.0
 */
class MolajoGroup
{
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
     * Returns Global Group object, creating it if it doesn't already exist.
     *
     * @param   strjng $identifier  Requested Group (id or name)
     *
     * @return  object  Group
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
            if ($id = MolajoGroupHelper::get($identifier)) {

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
     * @return  boolean  True on success
     * @since   1.0
     */
    protected function _load($id)
    {
        $table = $this->_getModel();

        $results = $table->load($id);

        //  MolajoError::raiseWarning('SOME_ERROR_CODE', TextHelper::sprintf('MOLAJO_USER_ERROR_UNABLE_TO_LOAD_USER', $id));
        $columns = $this->_database->getFields('#__users', true);

        foreach ($columns as $name => $value) {
            $this->$name = $table->$name;
        }

        /** extra fields */
        $this->name = trim($this->first_name . ' ' . $this->last_name);

        $this->_loadCustomFields($table->custom_fields);

        $this->_loadParameters($table->parameters);

        $this->applications = $table->applications;

        $this->groups = $table->groups;

        $this->view_groups = $table->view_groups;

        $this->guest = 0;

        return true;
    }

    /**
     * _getModel
     *
     * Method to get the user table object
     *
     * This function uses a static variable to store the user table name
     *
     * @param   string   $type    The user table name to be used
     * @param   string   $prefix  The user table prefix to be used
     *
     * @return  object   The user table object
     * @since   1.0
     */
    protected function _getModel($type = 'Groups', $prefix = 'Molajo')
    {
        static $tabletype;

        if (isset($tabletype)) {
        } else {
            $tabletype['name'] = 'Groups';
            $tabletype['prefix'] = 'Molajo';
        }

        if (isset($type)) {
            $tabletype['name'] = $type;
            $tabletype['prefix'] = $prefix;
        }

        return MolajoModel::getInstance($tabletype['name'], $tabletype['prefix']);
    }

    public function action()
    {

    }

    private function create()
    {

    }

    private function update()
    {

    }

    private function delete()
    {

    }
}
