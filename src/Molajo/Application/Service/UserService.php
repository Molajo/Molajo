<?php
/**
 * @package     Molajo
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

use Molajo\Application\Services;

defined('MOLAJO') or die;

/**
 * User Class
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
     * $model
     *
     * @since  1.0
     * @var object
     */
    protected $model = 'Molajo\\Application\\MVC\\Model\\UsersModel';

    /**
     * getInstance
     *
     * @param   string $identifier  Requested User (id or username or 0 for guest)
     *
     * @return  object  User
     * @since   1.0
     */
    public static function getInstance($id = 0, $model = null)
    {
        $id = 42;
        if (empty(self::$instances[$id])) {
            $user = new UserService($id, $model);
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
    protected function __construct($id = 0, $model = null)
    {
        $this->id = (int)$id;
        if ($model === null) {
        } else {
            $this->model = $model;
        }
//        $this->storage = Services::Request()->getSession();

        if ((int)$this->id == 0) {
            return $this->_loadGuest();
        } else {
            return $this->_load();
        }
    }

    /**
     * load
     *
     * Retrieve User or Guest Information
     *
     * @param   mixed  $id either the numeric userid or character username
     *
     * @return  boolean
     * @since   1.0
     */
    protected function _load()
    {
        $m = new $this->model ($this->id);

        $results = $m->load();
        if ($results == false) {
            $this->guest = true;
            return $this->_loadGuest();
        }

        /** User Table Columns */
        $columns = $m->getFieldNames();

        for ($i=0; $i < count($columns); $i++) {

            if ($columns[$i] == 'parameters'
                || $columns[$i] == 'custom_fields'
                || $columns[$i] == 'metadata')  {

            } else {
                Services::Registry()->set('User\\' . $columns[$i], $results[$columns[$i]]);
            }
        }

        /** Validations Table */
        $v = simplexml_load_file(
            MOLAJO_APPLICATIONS_MVC
                . '/Model/Table/'
                . substr($m->table_name, 3, 99)
                . '.xml'
        );

        /** $custom_fields */
       $custom_fields = Services::Registry()->initialise();
       $custom_fields->loadJSON($results['custom_fields'], array());

        if (isset($v->custom_fields->custom_field)) {
            foreach ($v->custom_fields->custom_field as $cf) {

                $name = (string)$cf['name'];
                $dataType = (string)$cf['filter'];
                $null = (string)$cf['null'];
                $default = (string)$cf['default'];
                $values = (string)$cf['values'];

                if ($default == '') {
                    $val = $custom_fields->get($name, null);
                } else {
                    $val = $custom_fields->get($name, $default);
                }

//                $val = Services::Security()
//                ->filter(
//                    $val, $dataType, $null, $default, $values);

                Services::Registry()->set('UserCustomFields\\' . $name, $v);
            }
        }

        $metadata = Services::Registry()->initialise();
        $metadata->loadString($this->get('metadata', array()));
        $this->set('metadata', $metadata);

        $parameters = Services::Registry()->initialise();
        $parameters->loadString($this->get('parameters'));

        var_dump($this->get('parameters'));
        die;

        $this->set('parameters', $parameters);

        return $this;
    }

    /**
     * _loadGuest
     *
     * Set values for visitor not logged on
     *
     * @return  boolean
     * @since   1.0
     */
    protected function _loadGuest()
    {
        $m = new $this->model (0);

        $columns = $m->getFieldNames();

        foreach ($columns as $name => $value) {
            $this->set($name, '');
        }
        $this->set('id', 0);
        $this->set('asset_type_id', MOLAJO_ASSET_TYPE_USER);

        $parameters = Services::Registry()->initialise();
        $parameters->loadString(
            Services::Configuration()->get('guest_parameters', '{}')
        );

        $this->set('applications', array());
        $this->set('groups', array(MOLAJO_SYSTEM_GROUP_PUBLIC, MOLAJO_SYSTEM_GROUP_GUEST));
        $this->set('view_groups', array(MOLAJO_SYSTEM_GROUP_PUBLIC, MOLAJO_SYSTEM_GROUP_GUEST));
        $this->set('public', 1);
        $this->set('guest', 1);
        $this->set('registered', 0);
        $this->set('administrator', 0);

        return $this;
    }
}
