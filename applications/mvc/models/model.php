<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoModel
 *
 * Component Model for Display Views
 *
 * @package        Molajo
 * @subpackage    Model
 * @since 1.0
 */
class MolajoModel
{
    /**
     * Indicates if the internal state has been set
     *
     * @var    boolean
     * @since  11.1
     */
    protected $__state_set = null;

    /**
     * Database Connector
     *
     * @var    object
     * @since  11.1
     */
    protected $db;

    /**
     * The model (base) name
     *
     * @var    string
     * @note   Replaces _name variable in 11.1
     * @since  11.1
     */
    protected $name;

    /**
     * The URL option for the component.
     *
     * @var    string
     * @since  11.1
     */
    protected $option = null;

    /**
     * A state object
     *
     * @var    string
     * @note   Replaces _state variable in 11.1
     * @since  11.1
     */
    protected $state;


    /**
     * Constructor
     *
     * @param   array  $config  An array of configuration options (name, state, dbo, table_path, ignore_request).
     *
     * @since   1.0
     */
    public function __construct($config = array())
    {
        if (empty($this->option)) {
            $r = null;
            if (preg_match('/(.*)Model/i', get_class($this), $r)) {
            } else {
                MolajoError::raiseError(500, MolajoText::_('JLIB_APPLICATION_ERROR_MODEL_GET_NAME'));
            }
            $this->option = strtolower($r[1]);
        }

        if (empty($this->name)) {
            if (array_key_exists('name', $config)) {
                $this->name = $config['name'];
            } else {
                $this->name = $this->getName();
            }
        }

        if (array_key_exists('state', $config)) {
            $this->state = $config['state'];
        } else {
            $this->state = new JObject;
        }

        if (array_key_exists('dbo', $config)) {
            $this->db = $config['dbo'];
        } else {
            $this->db = MolajoController::getDbo();
        }
    }

    /**
     * Returns a Model object, always creating it
     *
     * @param   string  $type    The model type to instantiate
     * @param   string  $prefix  Prefix for the model class name. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  mixed   A model object or false on failure
     *
     * @since   1.0
     */
    public static function getInstance($type, $prefix = '', $config = array())
    {
        $modelClass = $prefix . ucfirst($type);
        return new $modelClass($config);
    }

    /**
     * Method to get the model name
     *
     * The model name. By default parsed using the classname or it can be set
     * by passing a $config['name'] in the class constructor
     *
     * @return  string  The name of the model
     *
     * @since   1.0
     */
    public function getName()
    {
        if (empty($this->name)) {
            $r = null;
            if (!preg_match('/Model(.*)/i', get_class($this), $r)) {
                MolajoError::raiseError(500, 'JLIB_APPLICATION_ERROR_MODEL_GET_NAME');
            }
            $this->name = strtolower($r[1]);
        }

        return $this->name;
    }

    /**
     * Method to get model state variables
     *
     * @param   string  $property  Optional parameter name
     * @param   mixed   $default   Optional default value
     *
     * @return  object  The property where specified, the state object where omitted
     *
     * @since   1.0
     */
    public function getState($property = null, $default = null)
    {
        if ($this->__state_set) {
        } else {
            // Protected method to auto-populate the model state.
            $this->populateState();

            // Set the model state set flag to true.
            $this->__state_set = true;
        }

        return $property === null ? $this->state : $this->state->get($property, $default);
    }

    /**
     * Method to set model state variables
     *
     * @param   string  $property    The name of the property
     * @param   mixed   $value        The value of the property to set
     *
     * @return  mixed   The previous value of the property
     * @since   1.0
     */
    public function setState($property, $value = null)
    {
        return $this->state->set($property, $value);
    }
}
