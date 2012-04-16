<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

use Joomla\registry\JRegistry;

defined('MOLAJO') or die;

/**
 * Request
 *
 * @package   	Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class RegistryService
{
	/**
	 * $instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * $parameters
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $parameters;

	/**
	 * getInstance
	 *
	 * @static
	 * @return  bool|object
	 * @since   1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new RegistryService();
		}
		return self::$instance;
	}

	/**
	 * Class constructor.
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function __construct()
	{
		$this->parameters = array();

		return $this;
	}

	/**
	 * Loads JSON data from a field given the field xml definition
	 *     (can be used for fields like parameters, custom fields, metadata, etc.)
	 *
	 * Usage:
	 * Services::Registry()->loadField('Namespace\\', 'field_name', $results['field_name'], $xml->field_group);
	 *
	 * @param $namespace
	 * @param $field_name
	 * @param $data
	 * @param $xml
	 *
	 * @return null
	 * @since  1.0
	 */
	public function loadField($namespace, $field_name, $data, $xml)
	{
		$temp = $this->initialise();
		$temp->loadString($data, 'JSON');

		if (isset($xml->$field_name)) {

			foreach ($xml->$field_name as $cf) {

				$name = (string)$cf['name'];
				$dataType = (string)$cf['filter'];
				$null = (string)$cf['null'];
				$default = (string)$cf['default'];
				$values = (string)$cf['values'];

				//todo: filter given XML field definitions

				if ($default == '') {
					$val = $temp->get($name, null);
				} else {
					$val = $temp->get($name, $default);
				}

				$this->set($namespace . $name, $val);
			}
		}
	}

	/**
	 * Create new JRegistry object that can be used locally
	 *
	 * Usage:
	 * $local = Services::Registry()->initialise();
	 *
	 * @return JRegistry
	 * @since  1.0
	 */
	public function initialise()
	{
		return new JRegistry();
	}

	/**
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
		$this->parameters[$name] = new JRegistry();

		return $this;
	}

	/**
	 * Sets a Parameter property for a specific item and parameter set
	 *
	 * Usage:
	 * Services::Registry()->set('Request\\parameter_name', $value);
	 *
	 * @param  string  $key
	 * @param  mixed   $value
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
	 * Returns a Parameter property for a specific item and parameter set
	 *
	 * Usage:
	 * Services::Registry()->get('Request\\parameter_name');
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @param  string  $type
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
	 * Sets a Parameter property for a specific item and parameter set
	 *
	 * Usage:
	 * Services::Registry()->merge('Menu', 'Component');
	 *
	 * @param $set1
	 * @param $set2
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function merge($set1, $set2)
	{
		$mergeInto = array();
		if ($set1 instanceof JRegistry) {
			$mergeInto = $this->getArray($set1);
		} else {
			//error
		}
		$mergeIn = array();
		if ($set2 instanceof JRegistry) {
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
		foreach ($array as $key => $value) {
			if ($value === null) {
			} else {
				$this->set($name . '//' . $key, $value);
			}
		}
		return;
	}

	/**
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
