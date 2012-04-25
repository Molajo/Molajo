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
 * Registry
 *
 * @package     Molajo
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
	 * Array containing all globally defined $registry objects
	 *
	 * @var    Object Registry
	 * @since  1.0
	 */
	protected $registry;

	/**
	 * Array containing the key to each $registry object
	 *
	 * @var    Object Registry
	 * @since  1.0
	 */
	protected $registryKeys = array();

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
		/** store all registries in this object  */
		$this->registry = array();
		$this->registryKeys = array();

		/** initialise known namespaces for application */
		$xml = CONFIGURATION_FOLDER . '/registry.xml';
		if (is_file($xml)) {
		} else {
			return false;
		}

		$list = simplexml_load_file($xml);

		foreach ($list->registry as $item) {
			$this->create( (string)$item );
		}

		return $this;
	}

	/**
	 * Create new Registry object to be used locally
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
	 * getRegistry
	 *
	 * @param $namespace
	 *
	 * @return \Joomla\registry\JRegistry|mixed
	 */
	public function getRegistry($namespace)
	{
		if (in_array($namespace, $this->registryKeys)) {
			return $this->registry[$namespace];
		}  else {
			return $this->create($namespace);
		}
	}


	/**
	 * create registry for namespace
	 *
	 * @param $namespace
	 * @return RegistryService
	 */
	public function create($namespace)
	{
		$new = new JRegistry();
		$this->registryKeys[] = $namespace;
		$this->registry[$namespace] = $new;

		return $this->registry[$namespace];
	}

	/**
	 * Retrieves a list of named spaced registries and optionally keys/values
	 *
	 * Usage:
	 * Services::Registry()->listRegistry(1);
	 *
	 * @param   boolean $all true - returns the entire list and each registry
	 *                         false - returns a list of registry names, only
	 *
	 * @return  mixed|boolean or array
	 * @since   1.0
	 */
	public function listRegistry()
	{
		return $this->registryKeys;
	}

	/**
	 * Returns a Parameter property for a specific item and namespace registry
	 *   Alias for JFactory::getConfig, returning full registry set for local use
	 *
	 * Usage:
	 * Services::Registry()->get('Request', 'parameter_name');
	 *
	 * @param  string  $namespace
	 * @param  string  $key
	 * @param  mixed   $default
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function get($namespace, $key, $default = null)
	{
		$temp = $this->getRegistry($namespace);
		return $temp->get($key, $default);
	}

	/**
	 * Sets a Parameter property for a specific item and parameter set
	 *
	 * Usage:
	 * Services::Registry()->set('Request', 'parameter_name', $value);
	 *
	 * @param  string  $namespace
	 * @param  string  $key
	 * @param  mixed   $default
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function set($namespace, $key, $value = '')
	{
		$temp = $this->getRegistry($namespace);
		return $temp->set($key, $value);
	}

	/**
	 * Copy one global registry to another
	 *
	 * Usage:
	 * Services::Registry()->copy('x', 'y');
	 *
	 * @param  $copyThis
	 * @param  $intoThis
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function copy($copyThis, $intoThis)
	{
		$into = $this->getRegistry($intoThis);
		if ($into instanceof JRegistry) {
		} else {
			$into = $this->create($intoThis);
		}

		$copy = $this->registry->get($copyThis);

		$a = array();

		while (list($k, $v) = each($copy)) {
			while (list($key, $value) = each($v)) {
				$into->set($key, $value);
			}
		}

		return $this;
	}

	/**
	 * Returns an array containing key and name pairs for a namespace registry
	 *
	 * Usage:
	 * Services::Registry()->getArray('request');
	 *
	 * @param   string  $namespace
	 * @param   boolean @keyOnly set to true to retrieve key names
	 *
	 * @return  array
	 * @since   1.0
	 */
	public function getArray($namespace, $keyOnly = false)
	{
		$a = array();

		$temp = $this->create($namespace);

		if ($temp instanceof JRegistry) {
		} else {
			return $a;
		}

		while (list($key, $value) = each($temp)) {

			if ($keyOnly === false) {
				$a[$key] = $value;
			} else {
				$a[] = $key;
			}
		}

		return $a;
	}

	/**
	 * Populates a registry with an array of key and name pairs
	 *
	 * Usage:
	 * Services::Registry()->loadArray('Request', $array);
	 *
	 * @param   string  $name  name of registry to use or create
	 * @param   boolean $array key and value pairs to load
	 *
	 * @return  array
	 * @since   1.0
	 */
	public function loadArray($namespace, $array = array())
	{
		$load = $this->create($namespace);

		if ($load instanceof JRegistry) {
		} else {
			$load = new JRegistry();
		}

		foreach ($array as $key => $value) {
			if ($value === null) {
			} else {
				$load->set($key, $value);
			}
		}
		return;
	}

	/**
	 * Returns all key names for the specified parameter set
	 *
	 * Usage:
	 * Services::Registry()->getKeys('Request');
	 *
	 * @param   string  $name
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function getKeys($namespace)
	{
		$load = $this->create($namespace);

		if ($load instanceof JRegistry) {
		} else {
			$load = new JRegistry();
		}

		return $load->getArray($namespace, true);
	}

	/**
	 * Loads JSON data from a field given the field xml definition
	 *     (can be used for fields like registry, custom fields, metadata, etc.)
	 *
	 * Usage:
	 * Services::Registry()->loadField('Namespace', 'field_name', $results['field_name'], $xml->field_group);
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
		$load = $this->create($namespace);

		if ($load instanceof JRegistry) {
		} else {
			$load = $this->create($namespace);
		}

		$load->loadString($data, 'JSON');

		if (isset($xml->$field_name)) {

			foreach ($xml->$field_name as $cf) {

				$name = (string)$cf['name'];
				$dataType = (string)$cf['filter'];
				$null = (string)$cf['null'];
				$default = (string)$cf['default'];
				$values = (string)$cf['values'];

				//todo: filter given XML field definitions

				if ($default == '') {
					$val = $load->get($name, null);
				} else {
					$val = $load->get($name, $default);
				}
			}
		}

		return $this;
	}
}
