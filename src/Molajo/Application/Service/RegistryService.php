<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

use Joomla\registry\Registry;

defined('MOLAJO') or die;

/**
 * Request
 *
 * @package   Molajo
 * @subpackage  Services
 * @since        1.0
 */
Class RegistryService
{
    /**
     * @instance
     *
     * @var        object
     * @since   1.0
     */
    protected static $instance;

    /**
     * $parameters
     *
     * @var     array
     * @since   1.0
     */
    protected $parameters;

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new RegistryService();
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * Class constructor.
     *
     * @since  1.0
     */
    public function __construct()
    {
        $this->parameters = array();
    }

    /**
     * initialise
     *
     * Create new Registry object that can be used locally
     *
     * Usage:
     * $local = Services::Registry()->initalise();
     *
     *
     * @return \Joomla\registry\Registry
     */
    public function initialise()
    {
        return new Registry();
    }

    /**
     * create
     *
     * Create new parameter set that is stored within the RegistryService
     * class and can accessed globally throughout the application
     *
     * Usage:
     * Services::Registry()->create('request');
     *
     * @param   string  $name
     *
     * @return  null
     * @since   1.0
     */
    public function create($name)
    {
        $this->parameters[$name] = new Registry();

        return;
    }

    /**
     * set
     *
     * Sets a Parameter property for a specific item and parameter set
     *
     * Usage:
     * Services::Registry()->set('request\\parameter_name', $value);
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed
     * @since   1.0
     */
    public function set($key, $value = null)
    {
        $split = explode('\\', $key);
        if (isset($this->parameters[$split[0]])) {
        } else {
            $this->create($split[0]);
        }
        return $this->parameters[$split[0]]->set($split[1], $value);
    }

    /**
     * get
     *
     * Returns a Parameter property for a specific item and parameter set
     *
     * Usage:
     * Services::Registry()->get('request\\parameter_name');
     *
     * @param   string  $key
     * @param   mixed   $default
     * @param    string    $type
     *
     * @return  mixed
     * @since   1.0
     */
    public function get($key, $default = null)
    {
        $split = explode('\\', $key);
        return $this->parameters[$split[0]]->get($split[1], $default);
    }

    /**
     * merge
     *
     * Sets a Parameter property for a specific item and parameter set
     *
     * Usage:
     * Services::Registry()->set('request\\parameter_name', $value);
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed
     * @since   1.0
     */
    public function merge($set1, $set2)
    {
        $mergeInto = array();
        if ($set1 instanceof Registry) {
            $mergeInto = $this->getArray($set1);
        } else {
            //error
        }
        $mergeIn = array();
        if ($set2 instanceof Registry) {
        } else {
            $mergeIn = $this->getArray($set2);
        }

        foreach ($mergeIn as $k => $v) {
            if ($v == null) {
            } else {
                $mergeInto->$k = $v;
            }
        }

        $temp = $this->initialise();
        foreach ($mergeInto as $key => $value) {
            $this->set($set1 . '//' . $key, $value = null);
        }
    }

    /**
     * loadArray
     *
     * Returns an array containing key and name pairs for a specified parameter set
     *
     * Usage:
     * Services::Registry()->loadArray('request', $array);
     *
     * @param   string  $name  name of registry to use or create
     * @param   boolean $array key and value pairs to load
     *
     * @return  array
     * @since   1.0
     */
    public function loadArray($name, $array = array())
    {
        var_dump($array);
        foreach ($array as $key => $value) {
            if ($value === null) {
            } else {
                echo $key . ' ' . $value . '<br />';
                $this->set($name . '//' . $key, $value);
            }
        }
        return;
    }

    /**
     * getArray
     *
     * Returns an array containing key and name pairs for a specified parameter set
     *
     * Usage:
     * Services::Registry()->getArray('request');
     *
     * @param   string  $name
     * @param   boolean @keyOnly set to true to retrieve keynames
     *
     * @return  array
     * @since   1.0
     */
    public function getArray($name, $keyOnly = false)
    {
        $a = array();
        while (list($k, $v) = each($this->parameters[$name])) {
            while (list($key, $value) = each($v)) {
                if ($keyOnly === false) {
                    $a[$key] = $value;
                } else {
                    $a[] = $key;
                }
            }
        }
        return $a;
    }

    /**
     * getKeys
     *
     * Returns all key names for the specified parameter set
     *
     * Usage:
     * Services::Registry()->getKeys('request');
     *
     * @param   string  $name
     *
     * @return  mixed
     * @since   1.0
     */
    public function getKeys($name)
    {
        return $this->getArray($name, true);
    }
}
