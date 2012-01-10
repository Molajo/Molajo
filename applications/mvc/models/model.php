<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Model
 *
 * Base Molajo Model
 *
 * @package       Molajo
 * @subpackage    Model
 * @since 1.0
 */
class MolajoModel
{
    /**
     * Name
     *
     * @var    string
     * @since  1.0
     */
    protected $_name = 'MolajoModel';

    /**
     * Configuration
     *
     * @var    object
     * @since  1.0
     */
    protected $_config;

    /**
     * Database Connector
     *
     * @var    object
     * @since  1.0
     */
    protected $_db;

    /**
     * Model State
     *
     * @var    object
     * @since  1.0
     */
    protected $_state;

    /**
     * $request
     *
     * @var    object
     * @since  1.0
     */
    public $request;

    /**
     * $parameters
     *
     * @var    array
     * @since  1.0
     */
    public $parameters = array();

    /**
     * $items
     *
     * @var    array
     * @since  1.0
     */
    public $items = array();

    /**
     * $pagination
     *
     * @var    array
     * @since  1.0
     */
    public $pagination = array();

    /**
     * $context
     *
     * @var    string
     * @since  1.0
     */
    public $context = null;

    /**
     * $task
     *
     * @var    string
     * @since  1.0
     */
    public $task = null;

    /**
     * Constructor
     *
     * @param   array  $config  An array of configuration options
     *
     * @since   1.0
     */
    public function __construct($config = array())
    {
        $this->config = $config;

        if (array_key_exists('dbo', $this->config)) {
            $this->_db = $this->config['dbo'];
        } else {
            $this->_db = MolajoController::getDbo();
        }
    }

    /**
     * get
     *
     * Returns a property of the Model object
     * or the default value if the property is not set.
     *
     * @param   string  $key      The name of the property.
     * @param   mixed   $default  The default value (optional) if none is set.
     *
     * @return  mixed   The value of the configuration.
     *
     * @since   1.0
     */
    public function get($key, $default = null)
    {
        return $this->_state->get($key, $default);
    }

    /**
     * set
     *
     * Modifies a property of the Model object, creating it if it does not already exist.
     *
     * @param   string  $key    The name of the property.
     * @param   mixed   $value  The value of the property to set (optional).
     *
     * @return  mixed   Previous value of the property
     *
     * @since   1.0
     */
    public function set($key, $value = null)
    {
        $this->_state->set($key, $value);
    }

    /**
     * populateState
     *
     * Method to auto-populate the model state.
     *
     * @return    void
     * @since    1.0
     */
    protected function populateState()
    {

    }

    /**
     * getRequest
     *
     * @return    array    An empty array
     *
     * @since    1.0
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * getParameters
     *
     * @return    array    An empty array
     *
     * @since    1.0
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * getItems
     *
     * @return    array    An empty array
     *
     * @since    1.0
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * getPagination
     *
     * @return    array    An empty array
     *
     * @since    1.0
     */
    public function getPagination()
    {
        return $this->pagination;
    }
}
