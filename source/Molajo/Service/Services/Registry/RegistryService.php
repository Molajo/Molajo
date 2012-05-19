<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services\Registry;

use Molajo\Service\Services;

defined('MOLAJO') or die;

//todo: consider namespace reuse - intentional and otherwise
//todo: Lock from change
//

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
	 * The debug service is activated after the registry and therefore cannot be used
	 * to log system activity immediately. Once Services::Debug()->on = true this indicator
	 * is set to true, existing registries are logged, and individual creates are logged
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $debug_available;

	/**
	 * Array containing registry keys
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
			self::$instance = new RegistryService();
		}

		return self::$instance;
	}

	/**
	 * Initialise known namespaces for application
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function __construct()
	{
		/** store all registries in this object  */
		$this->registry = array();
		$this->registryKeys = array();

		return $this;
	}

	/**
	 * Checks to see if the specified namespace exists
	 *
	 * Services::Registry()->exists('Name Space');
	 *
	 * @param $namespace
	 *
	 * @return array
	 */
	public function exists($namespace)
	{
		$namespaces = $this->registryKeys;
		if (is_array($namespaces)) {
		} else {
			return false;
		}

		if (in_array($namespace, $namespaces)) {
		}  else {
			return false;
		}

		$thisNamespace = $this->registry[$namespace];

		if (count($thisNamespace) == 0) {
			return false;
		}

		return true;
	}

	/**
	 * Create a Registry array for specified Namespace
	 *
	 * This is useful if you want to create your registry during the class startup processed
	 * and provide a class property to the connection.
	 *
	 * However, it is NOT required in most situations as the get or set creates the registry
	 * during first use
	 *
	 * Usage:
	 * Services::Registry()->createRegistry('Name Space');
	 *
	 * @param $namespace
	 *
	 * @return array
	 */
	public function createRegistry($namespace)
	{

		if (isset($this->registryKeys[$namespace])) {
			return $this->registry[$namespace];
		}

		/** Keys array */
		$this->registryKeys[] = $namespace;

		/** Namespace array */
		$this->registry[$namespace] = array();

		/** Log it */
		if (in_array('DebugService', $this->registryKeys)) {

			if (Services::Registry()->get('DebugService', 'on') === true) {

				if ($this->debug_available === false) {
					$this->debug_available = true;
					/* Catch up logging Registries created before Debug Service started */
					foreach ($this->registryKeys as $ns) {
						Services::Debug()->set('Create Registry ' . $ns);
					}
				} else {
					Services::Debug()->set('Create Registry ' . $namespace);
				}
			}
		}

		/** Return new registry */
		return $this->registry[$namespace];
	}

	/**
	 * Returns a Parameter property for a specific item and namespace registry
	 *   Alias for JFactory::getConfig, returning full registry set for local use
	 *
	 * Usage:
	 * Services::Registry()->get('Name Space', 'key value');
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
		/** Get without a key returns the entire namespace (Like JFactory::getConfig)  */
		if ($key == null) {
			return $this->getRegistry($namespace);
		}

		/** No sense in fighting it. */
		$key = strtolower($key);

		/** Does registry exist? If not, create it. */
		if (in_array($namespace, $this->registryKeys)) {
		} else {
			$this->createRegistry($namespace);
		}

		/** If it doesn't exist, we have problems. */
		if (isset($this->registry[$namespace])) {
		} else {
			//todo: throw error
			echo $namespace . ' Blow up in RegistryService';
			die;
		}

		/** Retrieve the registry for the namespace */
		$array = $this->registry[$namespace];
		if (is_array($array)) {
		} else {
			$array = array();
		}

		/** Look for the key value requested */
		if (isset($array[$key])) {

		} else {
			/** Create the entry, if not found, and set it to default */
			$array[$key] = $default;
			$this->registry[$namespace] = $array;
		}

		/** That's what you wanted, right? */
		return $array[$key];
	}

	/**
	 * Sets a Parameter property for a specific item and parameter set
	 *
	 * Usage:
	 * Services::Registry()->set('Name Space', 'key_name', $value);
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
		if ($key == '') {
			return;       //error
		}

		/** keep it all on the down-low */
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
	 * Deletes a Parameter property
	 *
	 * Usage:
	 * Services::Registry()->delete('Name Space', 'key_name');
	 *
	 * @param  string  $namespace
	 * @param  string  $key
	 *
	 * @return  Registry
	 * @since   1.0
	 */
	public function delete($namespace, $key)
	{
		/** Retrieve the registry for the namespace */
		$array = $this->registry[$namespace];
		if (is_array($array)) {
		} else {
			$array = array();
		}

		/** Look for the key value requested */
		if (isset($array[$key])) {

			/** Remove entry */
			unset($array[$key]);
		}

		/** Replace namespace */
		$this->registry[$namespace] = $array;

		return $this;
	}

	/**
	 * Copy one namespace registry to another
	 * Note: this is a merge if there are existing registry values
	 * If that is not desired, delete the registry prior to the copy
	 *
	 * Usage:
	 * Services::Registry()->copy('namespace-x', 'to-namespace-y');
	 *
	 * @param  $copyThis
	 * @param  $intoThis
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function copy($copyThis, $intoThis)
	{
		/** Get (or create) the Registry that will be copied */
		$copy = $this->getRegistry($copyThis);

		/** Merge */
		if (count($copy > 0)) {
			foreach ($copy as $key => $value) {
				$this->set($intoThis, $key, $value);
			}
		}

		return $this;
	}

	/**
	 * Merge on namespace into another -- existing values are NOT overwritten
	 *
	 * Usage:
	 * Services::Registry()->merge('namespace-x', 'to-namespace-y');
	 *
	 * @param  $mergeThis
	 * @param  $intoThis
	 * @param  $matching - merge for matching keys and remove from original
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function merge($mergeThis, $intoThis, $matching = false)
	{
		/** Get (or create) the Registry that will be merged into the other */
		$mergeArray = $this->getRegistry($mergeThis);

		/** Get (or create) the Registry that will be copied to */
		$intoArray = $this->getRegistry($intoThis);

		/** Merge */
		if (count($mergeArray > 0)) {
		} else {
			return $this;
		}

		foreach ($mergeArray as $key => $value) {

			if ($matching == true) {

				if (isset($intoArray[$key])) {
					$merge = true;
				} else {
					$merge = false;
				}

			}  else {
				$merge = true;
			}

			if ($merge == true) {
				$existingValue = $this->get($intoThis, $key, '');

				if (trim($existingValue) == '') {
//					echo $mergeThis.' '.$intoThis.' '.$key.' '.$value.'<br />';
					$this->set($intoThis, $key, $value);
				}

				if ($matching == true) {
					$this->delete($mergeThis, $key);
				}
			}
		}

		return $this;
	}

	/**
	 * Sort Namespace array
	 *
	 * Usage:
	 * Services::Registry()->sort('namespace');
	 *
	 * @param  namespace
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function sort($namespace)
	{
		/** Get (or create) the Registry that will be merged into the other */
		$sort = $this->getRegistry($namespace);

		ksort($sort);

		$this->registry[$namespace] = $sort;

		return $this->get($namespace);
	}

	/**
	 * Delete a Registry for specified Namespace
	 *
	 * @param $namespace
	 *
	 * @return array
	 */
	public function deleteRegistry($namespace)
	{
		if (in_array($namespace, $this->registryKeys)) {
			$this->registryKeys[$namespace] = array();
			$this->registry[$namespace] = array();
		}

		return $this;
	}

	/**
	 * Rename a namespace (deletes existing, creates new)
	 *
	 * Usage:
	 * Services::Registry()->rename($namespace);
	 *
	 * @param  $namespace
	 * @param  $newname
	 *
	 * @return  Registry
	 * @since   1.0
	 */
	public function rename($namespace, $newname)
	{

		/** Retrieve existing contents, sort it. */
		$existing = $this->getRegistry($namespace);
		ksort($existing);

		/** Delete the new and old namespaces */
		$this->deleteRegistry($namespace);
		$this->deleteRegistry($newname);

		/** create the new namespace */
		$this->createRegistry($newname);

		/** Populate the new namespace with saved data */
		$this->registry[$newname] = $existing;

		return $this;
	}

	/**
	 * Returns an array containing key and name pairs for a namespace registry
	 *
	 * Usage:
	 * Services::Registry()->getArray('Name Space');
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

		/** full registry array requested */
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
	 * Retrieves a list of ALL namespaced registries and optionally keys/values
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
	 * Returns the entire registry for the specified namespace
	 *
	 * This is protected as the class will retrieve the registry with a get on namespace, only
	 *
	 * Services::Registry()->get('Name Space');
	 *
	 * @param $namespace
	 *
	 * @return array
	 */
	protected function getRegistry($namespace)
	{
		if (in_array($namespace, $this->registryKeys)) {
			return $this->registry[$namespace];

		} else {
			return $this->createRegistry($namespace);
		}
	}
}
