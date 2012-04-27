<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */

namespace Molajo\Service\Services;

use Molajo\Service\Services;

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
	 * Array containing the key to each $registry object
	 *
	 * @var    Object Array
	 * @since  1.0
	 */
	protected $registryKeys = array();

	/**
	 * Array containing all globally defined $registry objects
	 *
	 * @var    Object Array
	 * @since  1.0
	 */
	protected $registry = array();

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
			self::$instance = new RegistryService(true);
		}

		return self::$instance;
	}

	/**
	 * Initialise known namespaces for application
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function __construct($global = false)
	{
		/** store all registries in this object  */
		$this->registry = array();
		$this->registryKeys = array();

		if ($global == true) {
			$this->createGlobalRegistry();
		}

		return $this;
	}

	/**
	 * Global
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function createGlobalRegistry()
	{
		/** initialise known namespaces for application */
		$xml = CONFIGURATION_FOLDER . '/Application/registry.xml';
		if (is_file($xml)) {
		} else {
			return false;
		}

		$list = simplexml_load_file($xml);

		foreach ($list->registry as $item) {
			$reg = $this->createRegistry((string)$item);
		}
	}

	/**
	 * Create a Registry array for specified Namespace
	 *
	 * Usage:
	 * Services::Registry()->createRegistry('namespace');
	 *
	 * @param $namespace
	 *
	 * @return array
	 */
	public function createRegistry($namespace)
	{
		if (in_array($namespace, $this->registryKeys)) {
			return $this->registry[$namespace];
		}

		/** Keys array */
//		$i = count($this->registryKeys);
		$this->registryKeys[] = $namespace;

		/** Namespace array */
		$this->registry[$namespace] = array();

		return $this->registry[$namespace];
	}

	/**
	 * Delete a Registry for specified Namespace
	 *
	 * Usage:
	 * Services::Registry()->deleteRegistry('namespace');
	 *
	 * @param $namespace
	 *
	 * @return array
	 */
	public function deleteRegistry($namespace)
	{
		if (isset($this->registryKeys[$namespace])) {
			unset($this->registryKeys[$namespace]);
		}
		if (isset($this->registry[$namespace])) {
			unset($this->registry[$namespace]);
		}

		return $this;
	}

	/**
	 * Returns the entire registry for the specified namespace
	 *
	 * Usage:
	 * Services::Registry()->getRegistry('namespace');
	 *
	 * @param $namespace
	 *
	 * @return array
	 */
	public function getRegistry($namespace)
	{
		if (in_array($namespace, $this->registryKeys)) {
			return $this->registry[$namespace];
		} else {
			return $this->createRegistry($namespace);
		}
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
	 * @return  mixed    registry value
	 * @since   1.0
	 */
	public function get($namespace, $key = null, $default = null)
	{
		if ($key == null) {
			return $this->getRegistry($namespace);
		}

		$key = strtolower($key);

		/** Does registry exist? If not, create it. */
		if (in_array($namespace, $this->registryKeys)) {
		} else {
			$this->createRegistry($namespace);
		}

		/** should exist */
		if (isset($this->registry[$namespace])) {
		} else {
			//throw error
			echo $namespace . ' Blow up in RegistryService';
			die;
		}

		/** Retrieve the namespace */
		$array = $this->registry[$namespace];
		if (is_array($array)) {
		} else {
			$array = array();
		}

		/** Is there a match? */
		$found = false;
		while (list($existingKey, $existingValue) = each($array)) {
			if (strtolower($existingKey) == strtolower($key)) {
				$found = true;
				break;
			}
		}

		if ($found == true) {

		} else {
			$array[$key] = $default;
			$this->registry[$namespace] = $array;
		}

		return $array[$key];
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
	 * @return  Registry
	 * @since   1.0
	 */
	public function set($namespace, $key, $value = '')
	{
		$key = strtolower($key);

		/** Get the Registry (or create it) */
		$array = $this->getRegistry($namespace);

		/** Set the value for the key */
		$array[$key] = $value;

		/** Save the registry */
		$this->registry[$namespace] = $array;

		return $this;
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
		/** Get the Registry that will be copied */
		$copy = $this->getRegistry($copyThis);

		/** Get the Registry that will be copied into */
		$into = $this->getRegistry($intoThis);

		/** Save the new registry */
		$this->registry[$intoThis] = $copy;

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
		/** Get the Registry */
		$array = $this->getRegistry($namespace);

		if ($keyOnly == false) {
			return $array;
		}

		/* Key only */
		$keyArray = array();
		while (list($key, $value) = each($array)) {
			$keyArray[] = $key;
		}

		return $keyArray;
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
		/** Get the Registry that will be copied */
		$this->getRegistry($namespace);

		/** Save the new registry */
		$this->registry[$namespace] = $array;

		return $this;
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
		/** Get the Registry that will be copied */
		$this->getRegistry($namespace);

		$nsArray = array();

		/** Decode JSON into object */
		$jsonObject = json_decode($data);

		/** Place field names into named pair array */
		$lookup = array();

		if (count($jsonObject) > 0) {
			foreach ($jsonObject as $key => $value) {
				$lookup[$key] = $value;
			}
		}

		/** Load data for defined Fields, only */
		if (isset($xml->$field_name)) {

			foreach ($xml->$field_name as $cf) {

				$name = (string)$cf['name'];
				$name = strtolower($name);
				$dataType = (string)$cf['filter'];
				$null = (string)$cf['null'];
				$default = (string)$cf['default'];
				$values = (string)$cf['values'];

				if ($default == '') {
					$default = null;
				}

				/** Use value, if exists, or defined default */
				if (isset($lookup[$name])) {
					$set = $lookup[$name];
				} else {
					$set = $default;
				}
				/** Filter Input and Save the Registry */
		//$set = $this->filterInput($name, $set, $dataType);
				$nsArray[$name] = $set;
				$this->registry[$namespace] = $nsArray;
			}
		}

		return $this;
	}

	/**
	 * Retrieves a list of namespaced registries and optionally keys/values
	 *
	 * Usage:
	 * Services::Registry()->listRegistry(1);
	 *
	 * @param   boolean $all true - returns the entire list and each registry
	 *                         false - returns a list of registry names, only
	 *
	 * @return  mixed|boolean or array
	 * @since   1.0
	 *
	 *
	 */
	public function listRegistry($include_entries = false)
	{
		if ($include_entries == false) {
			return $this->registryKeys;
		}

		$nsArray = array();

		while (list($nsName, $nsValue) = each($this->registryKeys)) {
			$nsArray['namespace'] = $nsValue;
			$nsArray['registry'] = $this->registry[$nsValue];
		}

		return $nsArray;
	}

	/**
	 * loadFile
	 *
	 * add php spl priority for loading
	 *
	 * @return  object
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public static function loadFile($file, $type = 'Application')
	{
		if ($type == 'Application' || $type == 'Table') {
			$path_and_file = CONFIGURATION_FOLDER . '/' . $type . '/' . $file . '.xml';
		} else {
			$path_and_file = $type . '/' . $file . '.xml';
		}

		if (file_exists($path_and_file)) {
		} else {
			throw new \RuntimeException('File not found: ' . $path_and_file);
		}

		try {
			return simplexml_load_file($path_and_file);

		} catch (\Exception $e) {

			throw new \RuntimeException ('Failure reading XML File: ' . $path_and_file . ' ' . $e->getMessage());
		}
	}

	/**
	 * filterInput
	 *
	 * @param   string  $name         Name of input field
	 * @param   string  $field_value  Value of input field
	 * @param   string  $dataType     Datatype of input field
	 * @param   int     $null         0 or 1 - is null allowed
	 * @param   string  $default      Default value, optional
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	protected function filterInput(
		$name, $value, $dataType, $null = null, $default = null)
	{

		try {
			$value = Services::Filter()
				->filter(
				$value,
				$dataType,
				$null,
				$default
			);

		} catch (\Exception $e) {
			//todo: errors
			echo $e->getMessage() . ' ' . $name;
		}

		return $value;
	}
}
