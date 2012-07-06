<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Registry;

use Molajo\Service\Services;

defined('MOLAJO') or die;

//todo: consider namespace reuse - intentional and otherwise
//todo: Lock from change
//todo: consider API and minimize interface points

/**
 * Registry
 *
 * Profilerging support
 *
 * Services::Registry()->listRegistry();
 *   No parameter - returns an array of all registries by names
 *   * - Formatted <pre>var_dump</pre> of results
 *
 * Services::Registry()->get('Name Space', 'key value');
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
	 * The profiler service is activated after the registry and therefore cannot be used
	 * to log system activity immediately. Once Services::Profiler()->on = true this indicator
	 * is set to true, existing registries are logged, and individual creates are logged
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $profiler_available;

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
	 * @return bool|object
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
	 * @return object
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
	 * Checks to see if the specified namespace - or namespace-item - exist
	 *
	 * usage:
	 * Services::Registry()->exists('Namespace');
	 *
	 * @param $namespace
	 * @param $key (optional)
	 *
	 * @return boolean
	 */
	public function exists($namespace, $key = null)
	{
		$namespace = strtolower($namespace);

		$namespaces = $this->registryKeys;
		if (is_array($namespaces)) {
		} else {
			return false;
		}

		if (in_array($namespace, $namespaces)) {
		} else {
			return false;
		}

		/** Namespace check that makes it to this point is true */
		if ($key === null) {
			return true;
		}

		/** Request for element within namespace */
		$thisNamespace = $this->registry[$namespace];

		if (count($thisNamespace) == 0) {
			return false;
		}

		/** Look for the key value requested */
		$key = strtolower($key);
		if (isset($thisNamespace[$key])) {
			return true;
		} else {
			return false;
		}
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
		$namespace = strtolower($namespace);

		if ($namespace == 'db') {
			return false;
			// reserved word -- throw error
		}

		if (isset($this->registryKeys[$namespace])) {
			return $this->registry[$namespace];
		}

		$array = $this->registryKeys;
		if (in_array($namespace, $array)) {
			return $this->registry[$namespace];
		}

		/** Keys array */
		$this->registryKeys[] = $namespace;

		/** Namespace array */
		$this->registry[$namespace] = array();

		/** Log it */
		if ($this->exists('ProfilerService')) {
		} else {

			if (Services::Registry()->get('ProfilerService', 'on') === true) {

				if ($this->profiler_available === false) {

					$this->profiler_available = true;
					/* Catch up logging Registries created before Profiler Service started */
					foreach ($this->registryKeys as $ns) {
						Services::Profiler()->set('Create Registry ' . $ns, 'Registry');
					}
				} else {
					Services::Profiler()->set('Create Registry ' . $namespace, 'Registry');
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
	 * Returns a list of all registries:
	 * echo Services::Registry()->get('*');
	 *
	 * Returns a formatted dump of all registries:
	 * echo Services::Registry()->get('Configuration', '*');
	 *
	 * Returns all entries that begin with Theme:
	 * echo Services::Registry()->get('Configuration', 'theme*');
	 *
	 * @param string $namespace
	 * @param string $key
	 * @param mixed  $default
	 *
	 * @return mixed
	 * @since   1.0
	 */
	public function get($namespace = null, $key = null, $default = null)
	{
		$namespace = strtolower($namespace);
		$key = strtolower($key);

		if ($namespace == 'db') {
			return $this;
		}

		if ($namespace == '*') {

			return $this->listRegistry('*');

		} elseif ($key == null) {

			return $this->getRegistry($namespace);

		} elseif ($key == '*' || strpos($key, '*')) {

			$sort = $this->getRegistry($namespace);

			if ($key == '*') {
				$selected = $sort;
			} else {
				$selected = array();

				$searchfor = substr($key, 0, strrpos($key, '*'));

				foreach ($sort as $key => $value) {
					$match = substr($key, 0, strlen($searchfor));
					if (strtolower($match) == strtolower($searchfor)) {
						$selected[$key] = $value;
					}
				}
			}
			echo '<pre>';
			var_dump($selected);
			echo '</pre>';

			return true;
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
			echo '<br />Registry: '. $namespace . ' could not be created.';
			return false;
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
	 * @param string  $namespace
	 * @param string  $key
	 * @param mixed   $default
	 * @param boolean $match     - used as a security precaution to ensure only named parameters
	 *                             are updated via <include /> statement overrides
	 *
	 * @return Registry
	 * @since   1.0
	 */
	public function set($namespace, $key, $value = null, $match = false)
	{
		$namespace = strtolower($namespace);
		$key = strtolower($key);

		if ($key == '') {
			return; //error
		}

		/** Match requirement for security to ensure only named parameters are updated */
		if ($match === true) {
			$exists = $this->exists($namespace, $key);
			if ($exists == false) {
				return false;
			}
		}

		/** keep it all on the down-low */
		$key = strtolower($key);

		/** Get the Registry (or create it) */
		$array = $this->getRegistry($namespace);

		/** Set the value for the key */
		$array[$key] = $value;

//echo $namespace.' '.$key.'<br />';

		/** Save the registry */
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
	 * @return mixed
	 * @since   1.0
	 */
	public function copy($copyThis, $intoThis, $value = null)
	{
		$copyThis = strtolower($copyThis);
		$intoThis = strtolower($intoThis);

		/** Get (or create) the Registry that will be copied */
		$copy = $this->getRegistry($copyThis);

		/** Copy all */
		if ($value == null) {
			if (count($copy) > 0) {
				foreach ($copy as $key => $value) {
					$this->set($intoThis, $key, $value);
				}
			}

			return $this;
		}

		/** Copy a single item or prefix copy */
		$searchfor = '';
		if ($value == '*' || strpos($value, '*')) {
			$searchfor = substr($value, 0, strrpos($value, '*'));
			$exactMatch = false;
		} else {
			$searchfor = $value;
			$exactMatch = true;
		}

		if (count($copy > 0)) {
			foreach ($copy as $key => $value) {
				$use = false;
				$test = substr($key, 0, strlen($searchfor));
				if (strtolower($test) == strtolower($searchfor)) {
					if ($exactMatch == true) {
						if (strtolower($key) == strtolower($searchfor)) {
							$use = true;
						}
					} else {
						$use = true;
					}
				}
				if ($use == true) {
					$this->set($intoThis, $key, $value);
				}
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
	 * @return mixed
	 * @since   1.0
	 */
	public function merge($mergeThis, $intoThis, $matching = false)
	{
		$mergeThis = strtolower($mergeThis);
		$intoThis = strtolower($intoThis);

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

			} else {
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
	 * @return mixed
	 * @since   1.0
	 */
	public function sort($namespace)
	{
		$namespace = strtolower($namespace);

		/** Get (or create) the Registry that will be merged into the other */
		$sort = $this->getRegistry($namespace);

		ksort($sort);

		$this->registry[$namespace] = $sort;

		return $this->get($namespace);
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
	 * @return Registry
	 * @since   1.0
	 */
	public function rename($namespace, $newname)
	{
		$namespace = strtolower($namespace);
		$newname = strtolower($newname);

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
	 * Deletes a Parameter property
	 *
	 * Usage:
	 * Services::Registry()->delete('Name Space', 'key_name');
	 *
	 * @param string $namespace
	 * @param string $key
	 *
	 * @return Registry
	 * @since   1.0
	 */
	public function delete($namespace, $key)
	{
		$key = strtolower($key);
		$namespace = strtolower($namespace);

		if ($key == '') {
			return false;
		}

		/** Get the registry */
		$nsArray = $this->getRegistry($namespace);

		/** Can't delete if it doesn't exist */
		if (count($nsArray > 0)) {
		} else {
			return $this;
		}

		/** Delete it */
		$this->deleteRegistry($namespace);

		/** Recreate it */
		$this->createRegistry($namespace);

		$searchfor = '';
		if ($key == '*' || strpos($key, '*')) {
			$searchfor = substr($key, 0, strrpos($key, '*'));
			$exactMatch = false;
		} else {
			$searchfor = $key;
			$exactMatch = true;
		}

		foreach ($nsArray as $newKey => $newValue) {

			$delete = false;

			$test = substr($newKey, 0, strlen($searchfor));

			if (strtolower($test) == strtolower($searchfor)) {

				if ($exactMatch == true) {
					if (strtolower($newKey) == strtolower($searchfor)) {
						$delete = true;
					}
				} else {
					$delete = true;
				}
			}
			if ($delete == false) {
				$this->set($namespace, $newKey, $newValue);
			}
		}

		return $this;
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
		$deleted = false;

		$namespace = strtolower($namespace);

		/** retrieve existing keys */
		$existing = $this->registryKeys;
		$keep = array();
		foreach ($existing as $key => $value) {

			if ($value === $namespace) {
				$deleted = true;
			} else {
				$keep[] = $value;
			}
		}

		if ($deleted === false) {
			return $this;
		}

		sort($keep);

		/** save all but deleted registry */
		$tempRegistry = $this->registry;

		$this->registry = array();
		$this->registryKeys = array();

		foreach ($keep as $key => $value) {
			$this->registryKeys[] = $value;
			$this->registry[$value] = $tempRegistry[$value];
		}

		return $this;
	}

	/**
	 * Returns an array containing key and name pairs for a namespace registry
	 *
	 * Usage:
	 * Services::Registry()->getArray('Name Space');
	 *
	 * @param string $namespace
	 * @param   boolean @keyOnly set to true to retrieve key names
	 *
	 * @return array
	 * @since   1.0
	 */
	public function getArray($namespace, $keyOnly = false)
	{
		/** Get the Registry */
		$namespace = strtolower($namespace);
		$array = $this->getRegistry($namespace);

		/** full registry array requested */
		if ($keyOnly == false) {
			return $array;
		}

		/* Key only */
		$keyArray = array();
		foreach ($array as $key => $value) {
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
	 * @param string  $name  name of registry to use or create
	 * @param boolean $array key and value pairs to load
	 *
	 * @return array
	 * @since   1.0
	 */
	public function loadArray($namespace, $array = array())
	{
		/** Get the Registry that will be copied */
		$namespace = strtolower($namespace);
		$this->getRegistry($namespace);

		/** Save the new registry */
		$this->registry[$namespace] = $array;

		return $this;
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
		$namespace = strtolower($namespace);

		if (in_array($namespace, $this->registryKeys)) {
			return $this->registry[$namespace];
		}

		return $this->createRegistry($namespace);
	}

	/**
	 * Retrieves a list of ALL namespaced registries and optionally keys/values
	 *
	 * Usage:
	 * Services::Registry()->listRegistry(1);
	 *
	 * @param boolean $all true - returns the entire list and each registry
	 *                         false - returns a list of registry names, only
	 *
	 * @return mixed|boolean or array
	 * @since   1.0
	 */
	public function listRegistry($include_entries = false)
	{
		if ($include_entries == true) {
			echo '<pre>';
			var_dump($this->registryKeys);
			echo '</pre>';

			return;
		}

		echo '<pre>';
		var_dump($this->registry);
		echo '</pre>';

		return;
	}

	/**
	 *     Dummy functions to pass service off as a DBO to interact with model
	 */
	public function getNullDate()
	{
		return $this;
	}

	public function getQuery()
	{
		return $this;
	}

	public function toSql()
	{
		return $this;
	}

	public function clear()
	{
		return $this;
	}

	/**
	 * getData - simulates DBO - interacts with the Model getParameters method
	 *
	 * @param $registry
	 * @param $element
	 * @param $single_result
	 *
	 * @return array
	 * @since    1.0
	 */
	public function getData($namespace, $key = null, $single_result = false)
	{
		$namespace = strtolower($namespace);
		$key = strtolower($key);

		$query_results = array();

		/** Retrieve Parameter Registry and return as a result */
		if ($single_result == true) {
			return $this->get($namespace, $key);
		}

		/** Retrieve registry and return as a recordset */
		if ($key == null) {
			$results = $this->get($namespace);

		} else {

			if ($key == '*' || strpos($key, '*')) {

				if ($key == '*') {
					$results = $this->getRegistry($namespace);

				} else {
					$key = substr($key, 0, strlen($key) - 1);
					return $this->get($namespace, $key);
				}

			} else {
				$results = $this->get($namespace, $key);
			}
		}

		if (is_object($results)) {
			$query_results = $results;
			return $query_results;
		}

		/** Simulate a recordset */
		$row = new \stdClass();

		/** Process all parameters as fields */
		if (count($results) > 0) {
			foreach ($results as $key => $value) {
				$row->$key = $value;
			}
		}

		/** Place all fields into a row */
		$query_results[] = $row;

		/** Return results to Model */
		return $query_results;
	}
}
