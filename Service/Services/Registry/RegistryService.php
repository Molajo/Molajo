<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
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
    public function initialise()
    {
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

        if ($key === null) {
            return true;
        }

        $thisNamespace = $this->registry[$namespace];
        if (count($thisNamespace) == 0) {
            return false;
        }

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
     * @param   $namespace
     *
     * @return  array
     */
    public function createRegistry($namespace)
    {
        $namespace = strtolower($namespace);
        if ($namespace == 'db') {
            throw new \Exception ('Registry: Namespace ' . $namespace . ' is a reserved word.');
        }

        if (isset($this->registryKeys[$namespace])) {
            return $this->registry[$namespace];
        }

        $array = $this->registryKeys;
        if (in_array($namespace, $array)) {
            return $this->registry[$namespace];
        }

        $this->registryKeys[] = $namespace;

        $this->registry[$namespace] = array();

        if ($this->exists(PROFILER_LITERAL)) {
        } else {

            if (Services::Registry()->get(PROFILER_LITERAL, 'on') === true) {

                if ($this->profiler_available === false) {

                    $this->profiler_available = true;
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
     *
     * Usage:
     * Services::Registry()->get('Name Space', 'key value');
     *
     * Returns a list of all registries:
     * echo Services::Registry()->get('*');
     *
     * Returns a formatted dump of all registries:
     * echo Services::Registry()->get(CONFIGURATION_LITERAL, '*');
     *
     * Returns all entries that begin with Theme:
     * echo Services::Registry()->get(CONFIGURATION_LITERAL, 'theme*');
     *
     * @param   string  $namespace
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  mixed
     * @since   1.0
     */
    public function get($namespace = null, $key = null, $default = null)
    {
        $namespace = strtolower($namespace);

        if ($key === null) {

        } elseif (is_array($key)) {
            throw new \RuntimeException
            ('Registry: Key associated with Namespace: ' . $namespace . ' is array, must be string.');
        } elseif (is_object($key)) {
            throw new \RuntimeException
            ('Registry: Key associated with Namespace: ' . $namespace . ' is an object, must be string.');
        } else {
            $key = strtolower($key);
        }

        if ($namespace == 'db') {
            return $this;
        }

        if ($namespace == '*') {
            if ($key === null) {
                return $this->listRegistry(false);
            } else {
                return $this->listRegistry(true);
            }


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

            if ($key == '*') {
                echo '<pre>';
                var_dump($selected);
                echo '</pre>';
            } else {
                return $selected;
            }

            return true;
        }

        $key = strtolower($key);

        if (in_array($namespace, $this->registryKeys)) {
        } else {
            $this->createRegistry($namespace);
        }

        if (isset($this->registry[$namespace])) {
        } else {
            throw new \Exception('Registry: Namespace ' . $namespace . ' could not be created.');
        }

        $array = $this->registry[$namespace];
        if (is_array($array)) {
        } else {
            $array = array();
        }

        if (isset($array[$key])) {

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
     * Services::Registry()->set('Name Space', 'key_name', $value);
     *
     * @param   string   $namespace
     * @param   string   $key
     * @param   mixed    $default
     * @param   boolean  $match     - used as a security precaution to ensure only named parameters
     *                             are updated via <include /> statement overrides
     *
     * @return  Registry
     * @since   1.0
     */
    public function set($namespace, $key, $value = null, $match = false)
    {
        $namespace = strtolower($namespace);

        if (is_string($key)) {
            $key = strtolower($key);

        } elseif (is_bool($key)) {
        } else {
            echo '<pre>';
            var_dump($key);
            return false;
            throw new \Exception ('Registry: Namespace ' . $namespace. ' Invalid Key Value - must be a string.');
        }

        if ($key == '') {
            return false;
        }

        /** Match requirement for security to ensure only named parameters are updated */
        if ($match === true) {
            $exists = $this->exists($namespace, $key);
            if ($exists === false) {
                return false;
            }
        }

        $array = $this->getRegistry($namespace);

        $array[$key] = $value;

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
     * @param   $copyThis
     * @param   $intoThis
     *
     * @return  mixed
     * @since   1.0
     */
    public function copy($copyThis, $intoThis, $value = null)
    {
        $copyThis = strtolower($copyThis);
        $intoThis = strtolower($intoThis);

        $copy = $this->getRegistry($copyThis);

        if ($value == null) {
            if (count($copy) > 0) {
                foreach ($copy as $key => $value) {
                    $this->set($intoThis, $key, $value);
                }
            }

            return $this;
        }

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
                    if ($exactMatch === true) {
                        if (strtolower($key) == strtolower($searchfor)) {
                            $use = true;
                        }
                    } else {
                        $use = true;
                    }
                }
                if ($use === true) {
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

        $mergeArray = $this->getRegistry($mergeThis);

        $intoArray = $this->getRegistry($intoThis);

        if (count($mergeArray > 0)) {
        } else {
            return $this;
        }

        foreach ($mergeArray as $key => $value) {

            if ($matching === true) {

                if (isset($intoArray[$key])) {
                    $merge = true;
                } else {
                    $merge = false;
                }

            } else {
                $merge = true;
            }

            if ($merge === true) {
                $existingValue = $this->get($intoThis, $key, '');

                if (trim($existingValue) == '') {
                    $this->set($intoThis, $key, $value);
                }

                if ($matching === true) {
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
     * @param   namespace
     *
     * @return  mixed
     * @since   1.0
     */
    public function sort($namespace)
    {
        $namespace = strtolower($namespace);

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
     * @param   $namespace
     * @param   $newname
     *
     * @return  Registry
     * @since   1.0
     */
    public function rename($namespace, $newname)
    {
        $namespace = strtolower($namespace);

        $newname = strtolower($newname);

        $existing = $this->getRegistry($namespace);

        ksort($existing);

        $this->deleteRegistry($namespace);

        $this->deleteRegistry($newname);

        $this->createRegistry($newname);

        $this->registry[$newname] = $existing;

        return $this;
    }

    /**
     * Deletes a Parameter property
     *
     * Usage:
     * Services::Registry()->delete('Name Space', 'key_name');
     *
     * @param   string  $namespace
     * @param   string  $key
     *
     * @return  Registry
     * @since   1.0
     */
    public function delete($namespace, $key = null)
    {
        $key = strtolower($key);
        $namespace = strtolower($namespace);

        if ($key == '') {
            return false;
        }

        $nsArray = $this->getRegistry($namespace);

        if (count($nsArray > 0)) {
        } else {
            return $this;
        }

        $this->deleteRegistry($namespace);

        $this->createRegistry($namespace);

        $searchfor = '';
        if ($key == '*' || strpos($key, '*') || $key == null) {
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

                if ($exactMatch === true) {
                    if (strtolower($newKey) == strtolower($searchfor)) {
                        $delete = true;
                    }
                } else {
                    $delete = true;
                }
            }
            if ($delete === false) {
                $this->set($namespace, $newKey, $newValue);
            }
        }

        return $this;
    }

    /**
     * Delete a Registry for specified Namespace
     *
     * @param  $namespace
     *
     * @return array
     * @since  1.0
     */
    public function deleteRegistry($namespace)
    {
        $deleted = false;

        $namespace = strtolower($namespace);

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
     * @param   string $namespace
     * @param   boolean @keyOnly set to true to retrieve key names
     *
     * @return  array
     * @since   1.0
     */
    public function getArray($namespace, $keyOnly = false)
    {
        $namespace = strtolower($namespace);
        $array = $this->getRegistry($namespace);

        if ($keyOnly === false) {
            return $array;
        }

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
        $namespace = strtolower($namespace);
        $this->getRegistry($namespace);

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
     * @param   $namespace
     *
     * @return  array
     * @since   1.0
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
     * @return  mixed|boolean or array
     * @since   1.0
     */
    public function listRegistry($expand = false)
    {
        if ($expand === false) {
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
     * getData - returns Registry (comes from $model_name) as Query Results (array of objects)
     *
     * Data can be requested as a result - provide $registry, $element and true for $single result
     *
     * Use '*' in the key to retrieve all values starting with a specific phrase (ex. 'model')
     *
     * @param   string  $registry      Name of registry, for the MVC this is the $model_name
     * @param   string  $key           Key of the named pair
     * @param   string  $query_object  Result, Item, or List
     *
     * @return  array
     * @since   1.0
     */
    public function getData($registry, $key = null, $query_object = false)
    {
        $registry = strtolower($registry);

        $key = strtolower($key);
        $query_results = array();

        if ($key === null || $key == '*') {
            $results = $this->get($registry);

        } elseif ($query_object == QUERY_OBJECT_RESULT) {
            return $this->get($registry, $key);

        } else {
            $results = $this->get($registry, $key);
        }

        if (is_array($results)) {
            if (isset($results[0])) {
                if (is_object($results[0])) {
                    return $results;
                }
            }
        }

        $row = new \stdClass();
        if (count($results) > 0) {
            foreach ($results as $key => $value) {
                $row->$key = $value;
            }
        }
        $query_results[] = $row;
        return $query_results;
    }
}
