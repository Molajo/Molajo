<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Individual Molajo Contributors. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Adapter Class
 *
 * @package     Molajo
 * @subpackage  Installer
 * @since       1.0
 */
class MolajoAdapter
{
    /**
     * Associative array of adapters
     *
     * @var    array
     * @since  1.0
     */
    protected $_adapters = array();

    /**
     * Database Connector Object
     *
     * @var    object
     * @since  1.0
     */
    protected $_db;

    /**
     * Constructor
     *
     * @return  MolajoAdapter  object
     *
     * @since   1.0
     */
    public function __construct()
    {
        $this->db = Services::DB();
    }

    /**
     * setAdapter
     *
     * Set an adapter by name
     *
     * @param   string  $name
     * @param   object  $adapter
     * @param   array   $options
     *
     * @return  boolean
     * @since   1.0
     */
    public function setAdapter($name, $adapter = null, $options = array())
    {
        $class = 'Molajo'. ucfirst($name);
        if (class_exists($class)) {
            return false;
        } else {
            //serious error
        }

        $adapter = new $class($this, $this->db, $options);
        $this->_adapters[$name] = $adapter;

        return true;
    }

    /**
     * getAdapter
     *
     * Return an adapter.
     *
     * @param   string  $name
     * @param   array   $options  Adapter options
     *
     * @return  object
     *
     * @since   1.0
     */
    public function getAdapter($name, $options = array())
    {
        if (array_key_exists($name, $this->_adapters)) {
        } else {
            if ($this->setAdapter($name, $options)) {
            } else {
                $false = false;
                return $false;
            }
        }

        return $this->_adapters[$name];
    }
}
