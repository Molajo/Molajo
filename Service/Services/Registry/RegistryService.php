<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Registry;

use Molajo\Service\Services;

defined('MOLAJO') or die;

//todo: consider API and minimize interface points
//todo: limit access by class
/**
 * Registry
 * Named pair storage with local or global persistence
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class RegistryService
{
    /**
     * The profiler service is activated after the registry and therefore cannot be used
     * to log system activity immediately. Once Services::Profiler()->on = true this indicator
     * is set to true, data temporarily stored is logged
     *
     * @var    object
     * @since  1.0
     */
    protected $profiler_available;

    /**
     * Array containing namespace registry keys
     *
     * @var    array
     * @since  1.0
     */
    protected $registryKeys = array();

    /**
     * Array containing namespace locks
     *
     * @var    array
     * @since  1.0
     */
    protected $registryLocks = array();

    /**
     * Array containing all namespace registries and associated data
     *
     * @var    array
     * @since  1.0
     */
    protected $registry = array();

    protected $reserved_names = array();
    protected $namespace_permissions = array();

    /**
     * $instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

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
     * Class constructor
     *
     * @since   1.0
     * @return  object
     */
    public function __construct()
    {
        $this->initialise();
        return $this;
    }

    /**
     * Initialise Registry Arrays
     *
     * @return  object
     * @since   1.0
     */
    protected function initialise()
    {
        $this->registry = array();
        $this->registryKeys = array();
        $this->registryLocks = array();

        return $this;
    }

    /**
     *  Exists Unit Tests
     *
     *
    Services::Registry()->createRegistry('Unit');
    Services::Registry()->set('Unit', 'Test', 'Value');
    echo Services::Registry()->get('Unit', 'Test');

    $results = Services::Registry()->exists('Unit');
    if ($results === true) {
    echo 'Success - Unit Registry Exists';
    } else {
    echo 'Failure';
    }
    $results = Services::Registry()->exists('NotUnit');

    if ($results === true) {
    echo 'Failure';
    } else {
    echo 'Success - NotUnit Registry does not exist';
    }
     */

    /**
     * Does it exist? Useful for verifying existence of namespace and/or namespace element.
     *  Note: Does not create the namespace or member, simply tests if it has already been created.
     *
     * Usage:
     * Services::Registry()->exists('Namespace', 'Optional member');
     *
     * @param   string       $namespace
     * @param   null|string  $key (optional)
     *
     * @return  bool
     * @since   1.0
     */
    public function exists($namespace, $key = null)
    {
        $namespace = $this->editNamespace($namespace);

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
        }

        return false;
    }

    /**
     *  Test Lock

    Services::Registry()->createRegistry('Unit');
    Services::Registry()->set('Unit', 'Test', 'Value');
    Services::Registry()->get('Unit', '*');
    Services::Registry()->lock('Unit');
    Services::Registry()->set('Unit', 'Test', 'Change Value'); //should fail
    Services::Registry()->get('Unit', '*');
     */


    /**
     * Lock registry from update.
     *
     * Usage:
     * Services::Registry()->lock('Namespace');
     *
     * @param   string       $namespace
     *
     * @return  bool
     * @since   1.0
     */
    public function lock($namespace)
    {
        $namespace = $this->editNamespace($namespace);

        if ($this->exists($namespace)) {
        } else {
            throw new \RuntimeException ('Registry: Namespace in Lock Request does not exist.');
        }

        $namespaces = $this->registryLocks;

        if (in_array($namespace, $namespaces)) {
            return true;
        }

        $this->registryLocks[$namespace] = true;

        return true;
    }

    /**
     * Check to see if a registry is locked
     *
     * Usage:
     * Services::Registry()->checkLock('Namespace');
     *
     * @param   string  $namespace
     *
     * @return  bool    true - lock is on
     *                  false - there is no lock (and possibly no registry, either)
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function checkLock($namespace)
    {
        $namespace = $this->editNamespace($namespace);
        if ($this->exists($namespace)) {
        } else {
            return false;
        }

        $namespaces = $this->registryLocks;

        if (in_array($namespace, $namespaces)) {
            return true;
        }

        return false;
    }

    /**
     * Testing for Create Registry
     *
     *   Services::Registry()->createRegistry('Unit');
    Services::Registry()->set('Unit', 'Test', 'Value');
    Services::Registry()->get('Unit', '*');
    Services::Registry()->createRegistry('Unit');
    Services::Registry()->get('Unit', '*');
     */

    /**
     * Create a registry for the specified namespace
     *
     * Notes:
     * - All namespaces are set to lowercase to remove case sensitivity
     * - Throws exception if Registry Namespace is reserved
     * - Returns Namespace if already existing (use 'exists' if verification is needed prior to createRegistry)
     * - Called automatically when needed by a Set Request
     *
     * Usage:
     *  Services::Registry()->createRegistry('Name Space');
     *
     * @param   string  $namespace
     *
     * @return  mixed|bool|array
     * @since   1.0
     * @throws  \Exception
     */
    public function createRegistry($namespace)
    {
        $namespace = $this->editNamespace($namespace);

        if ($this->exists($namespace)) {

            if (isset($this->registryKeys[$namespace])) {
                throw new \RuntimeException
                    ('Registry: Cannot create Namespace ' . $namespace . ' because it already exists.');
            } else {
                return $this->registry[$namespace];
            }
        }

        if ($namespace == 'db' || $namespace == '*') {
            throw new \Exception ('Registry: Namespace ' . $namespace . ' is a reserved word.');
        }

        if (isset($this->registryKeys[$namespace])) {
            return $this->registry[$namespace];
        }

        $this->registryKeys[] = $namespace;

        $this->registry[$namespace] = array();

        /** Profiler Startup and Normal Logging */
        if ($this->exists(PROFILER_LITERAL)) {
        } else {

            if (Services::Registry()->get(PROFILER_LITERAL, 'on') === true) {

                if ($this->profiler_available === false) {
                    $this->profiler_available = true;
                    foreach ($this->registryKeys as $ns) {
                        Services::Profiler()->set('Registry: Create Namespace ' . $ns, 'Registry');
                    }
                } else {
                    Services::Profiler()->set('Registry: Create Namespace ' . $namespace, 'Registry');
                }
            }
        }

        /** Returns new registry */
        return $this->registry[$namespace];
    }

    /** Unit testing
     *
    Services::Registry()->createRegistry('Unit');
    Services::Registry()->set('Unit', 'Test1', 'Value1');
    Services::Registry()->set('Unit', 'Test2', 'Value2');
    Services::Registry()->set('Unit', 'Dog4', 'Dog2');

    Services::Registry()->createRegistry('Dog');


    echo Services::Registry()->get('Unit', 'Test2');
    $array = Services::Registry()->get('Unit', 'Test*');
    var_dump($array);

    Services::Registry()->get('Unit', '*');
    $array = Services::Registry()->get('Unit');
    var_dump($array);

    Services::Registry()->get('*');
    Services::Registry()->get('*', '*');
    Services::Registry()->get('Pork');
    Services::Registry()->get('Pork', 'X'); */

    /**
     * Returns Registry Data
     *
     * Notes:
     * - Creates registry member using default if not existing and default provided
     * - Creates registry if not existing (whether or not a member was created)
     *
     * Usage:
     * Services::Registry()->get('Name Space', 'key value');
     *
     * List names of existing registry namespaces:
     * echo Services::Registry()->get('*');
     *
     * ... include a formatted dump of namespace contents
     * echo Services::Registry()->get('*', '*');
     *
     * List all entries in the specified registry namespace
     * $array = Services::Registry()->get('Name space');
     *
     * List only those namespace entries beginning with the wildcard value:
     * echo Services::Registry()->get('Name space', 'theme*');
     *
     * @param   string  $namespace
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  array|bool|mixed
     * @since   1.0
     */
    public function get($namespace = null, $key = null, $default = null)
    {
        $namespace = $this->editNamespace($namespace);
        $key = $this->editNamespaceKey($namespace, $key);

        if ($this->exists($namespace) === true) {
        } else {
            return false;
        }

        if ($namespace == '*') {
            if ($key === null) {
                return $this->listRegistry(false);
            } else {
                return $this->listRegistry(true);
            }

        } elseif ($key == null) {
            return $this->getRegistry($namespace);

        } elseif ($key == '*' || strrpos($key, '*')) {
            $sort = $this->getRegistry($namespace);

            if ($key == '*') {
                $selected = $sort;
            } else {
                //todo - combine all the wildcard logic
                if (substr($key, 0, 1) == '*') {
                    $selected = array();
                    $searchfor = substr($key, 1, (strrpos($key, '*') - 1));
                    foreach ($sort as $key => $value) {
                        if ($key == $searchfor) {
                            $match = true;
                        } else {
                            $match = strpos($key, $searchfor);
                        }
                        if ($match) {
                            $selected[$key] = $value;
                        }
                    }

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

        if (in_array($namespace, $this->registryKeys)) {
            $array = $this->registry[$namespace];
            $namespace_exists = true;

        } else {
            $array = array();
            $namespace_exists = false;
        }

        /** Existing named pair returned */
        if (isset($array[$key])) {
            return $array[$key];
        }

        /** Not found and no create member requested */
        if ($default === null) {
            return false;
        }

        /** Create Registry and Member if needed and member default provided */
        if ($namespace_exists) {
        } else {
            $this->createRegistry($namespace);
        }

        $array[$key] = $default;
        $this->registry[$namespace] = $array;

        return $array[$key];
    }

    /**
     * Sets the value for a specific namespace item
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
        $namespace = $this->editNamespace($namespace);

        if ($this->checkLock($namespace)) {
            throw new \RuntimeException ('Registry: Namespace is locked. Updates are not allowed.');
        }

        $key = $this->editNamespaceKey($namespace, $key);

        if ($namespace == '') {
            throw new \RuntimeException ('Registry: Namespace is required for Set.');
        }
        if ($key == '') {
           // throw new \RuntimeException ('Registry: Key is required for Set. Namespace: ' . $namespace);
            echo 'Registry: Key is required for Set. Namespace: ' . $namespace;
            return;
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
     * Copy key values from one namespace registry into another, overwriting existing values
     *
     * Note:
     * If target_registry already exists, source_registry values replace existing values for matching keys
     * Key pairs on target registry remain unchanged if there are no matching pairs. Use Delete first, if desired.
     * Use merge when target registry values should remain -- not be overwritten.
     *
     * Usage:
     * Services::Registry()->copy('namespace-x', 'to-namespace-y');
     *
     * To copy only certain values:
     * Services::Registry()->copy('namespace-x', 'to-namespace-y', 'wildcard*');
     *
     * @param   string  $source_registry
     * @param   string  $target_registry
     * @param   null    $filter
     *
     * @return  RegistryService
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function copy($source_registry, $target_registry, $filter = null)
    {
        $source_registry = $this->editNamespace($source_registry);
        $target_registry = $this->editNamespace($target_registry);

        if ($this->checkLock($target_registry)) {
            throw new \RuntimeException
            ('Registry: Target Namespace: ' . $target_registry . ' is locked. May not copy into it.');
        }

        if ($this->exists($source_registry)) {
        } else {
            throw new \RuntimeException
            ('Registry: Namespace ' . $source_registry . ' requested as source of copy does not exist.');
        }

        $copy = $this->getRegistry($source_registry);

        if ($filter == null || $filter == '*') {
            if (count($copy) > 0) {
                foreach ($copy as $key => $filter) {
                    $this->set($target_registry, $key, $filter);
                }
            }
            return $this;
        }

        $searchfor = '';
        if (strpos($filter, '*')) {
            $searchfor = substr($filter, 0, strrpos($filter, '*'));
            $exactMatch = false;

        } else {
            $searchfor = $filter;
            $exactMatch = true;
        }

        if (count($copy > 0)) {

            foreach ($copy as $key => $filter) {
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
                    $this->set($target_registry, $key, $filter);
                }
            }
        }

        return $this;
    }

    /**
     * Merge one Namespace into another.
     *
     *  - When keys match, target value is retained
     *  - When key does not exist on the target, it is copied in
     *      In either of the above cases, when "remove_from_source" is 1, the source entry is removed
     *  - If no entries remain in the source after the merge, the registry is removed, too
     *
     * Usage:
     * Services::Registry()->merge('namespace-x', 'to-namespace-y');
     *
     * Merge a subset of source using wildcard:
     * Services::Registry()->merge('namespace-x', 'to-namespace-y', 'Only These*');
     *
     * Merge a subset of source using wildcard, and then delete the source merged in:
     * Services::Registry()->merge('namespace-x', 'to-namespace-y', 'Only These*', 1);
     *
     * @param   string  $source_registry
     * @param   string  $target_registry
     * @param   string  $filter - merge for matching keys
     * @param   string  $remove_from_source
     *
     * @return  bool
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function merge($source_registry, $target_registry, $filter = false, $remove_from_source = 0)
    {
        $source_registry = $this->editNamespace($source_registry);
        $target_registry = $this->editNamespace($target_registry);

        if ($this->exists($source_registry)) {
        } else {
            throw new \RuntimeException
            ('Registry: Namespace ' . $source_registry . ' requested as a source for merging does not exist.');
        }

        if ($this->exists($target_registry)) {
        } else {
            throw new \RuntimeException
            ('Registry: Namespace ' . $target_registry . ' does not exist, was requested as target of merge.');
        }

        if ($remove_from_source == 1) {
            if ($this->checkLock($source_registry)) {
                throw new \RuntimeException
                ('Registry: Source Namespace: ' . $target_registry . ' for Merge is locked. May not remove entries.');
            }
        }

        $target_registry = $this->editNamespace($target_registry);
        if ($this->checkLock($target_registry)) {
            throw new \RuntimeException
            ('Registry: Target Namespace: ' . $target_registry . ' for Merge is locked. May not add entries.');
        }


        $searchfor = '';
        if ($filter == null || trim($filter) == '' || $filter == '*') {
        } else {
            $searchfor = substr($filter, 0, strrpos($filter, '*'));
            $searchfor = strtolower(trim($searchfor));
        }

        $target = $this->getRegistry($target_registry);
        $source = $this->getRegistry($source_registry);
        foreach ($source as $key => $value) {

            $match = 0;

            if (is_null($value)) {
                //skip it.
            } elseif ($searchfor == '') {
               $match = 1;

            } elseif (trim(substr(strtolower($key), 0, strlen(strtolower($searchfor)))) == trim($searchfor)) {
                $match = 1;
            }

            if ($match == 1) {
                if (isset($target[$key])) {
                    if ($target[$key] === null) {
                        $this->set($target_registry, $key, $value);
                    }
                } else {
                    $this->set($target_registry, $key, $value);
                }
            }

            if ($remove_from_source == 1) {
                $this->delete($source_registry, $key);
            }
        }

        if (count($this->getRegistry($source_registry)) > 0) {
        } else {
            return $this->deleteRegistry($source_registry);
        }

        return true;
    }

    /**
     * Sort Namespace
     *
     * Usage:
     * Services::Registry()->sort('namespace');
     *
     * @param   namespace
     *
     * @return  mixed
     * @since   1.0
     * @return  RegistryService
     */
    public function sort($namespace)
    {
        $namespace = $this->editNamespace($namespace);

        if ($this->exists($namespace)) {
        } else {
            throw new \RuntimeException
            ('Registry: Cannot sort Namespace ' . $namespace . ' since it does not exist.');
        }

        $sort = $this->getRegistry($namespace);
        ksort($sort);
        $this->registry[$namespace] = $sort;

        return $this;
    }

    /**
     * Deletes a registry or registry entry
     *
     * Usage:
     * Services::Registry()->delete('Name Space', 'key_name');
     *
     * @param   string  $namespace
     * @param   string  $key
     *
     * @return  object
     * @since   1.0
     */
    public function delete($namespace, $key = null)
    {
        $namespace = $this->editNamespace($namespace);

        if ($this->exists($namespace)) {
            return $this;
        }

        if ($this->checkLock($namespace)) {
            throw new \RuntimeException
            ('Registry: Cannot delete an entry from Namespace: ' . $namespace . ' since it has been locked.');
        }

        $key = strtolower($key);
        if ($key == '') {
            return $this->deleteRegistry($namespace);
        }

        $searchfor = '';
        if ($key == null || trim($key) == '' || $key == '*') {
        } else {
            $searchfor = substr($key, 0, strrpos($key, '*'));
            $searchfor = strtolower(trim($searchfor));
        }

        $copy = $this->getRegistry($namespace);
        if (count($copy > 0)) {
        } else {
            return $this; //nothing to delete
        }

        $new = array();
        foreach ($copy as $key => $value) {

            $match = 0;

            if ($searchfor == '') {
                $match = 1;

            } elseif (trim(substr(strtolower($key), 0, strlen(strtolower($searchfor)))) == trim($searchfor)) {
                $match = 1;
            }

            if ($match == 1) {
            } else {
                $new[$key] = $value;
            }
        }

        $this->deleteRegistry($namespace);

        if (count($new) > 0) {
        } else {
            return $this;
        }

        $this->createRegistry($namespace);
        $this->registry[$namespace] = $new;

        return $this;
    }

    /**
     * Delete a Registry for specified Namespace
     *
     * @param   string  $namespace
     *
     * @return  array
     * @since   1.0
     */
    public function deleteRegistry($namespace)
    {
        $namespace = $this->editNamespace($namespace);

        if ($this->exists($namespace)) {
        } else {
            return $this;
        }

        $namespace = strtolower($namespace);

        $existing = $this->registryKeys;
        $keep = array();
        $deleted = false;
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
     * Rename a namespace (deletes existing, creates new)
     *
     * Usage:
     * Services::Registry()->rename($namespace);
     *
     * @param   $namespace
     * @param   $new_namespace
     *
     * @return  Registry
     * @since   1.0
     */
    public function rename($namespace, $new_namespace)
    {
        $namespace = $this->editNamespace($namespace);

        if ($this->exists($namespace)) {
        } else {
            throw new \RuntimeException
            ('Registry: Cannot rename Namespace ' . $namespace . ' since it does not exist.');
        }

        if ($this->checkLock($namespace)) {
            throw new \RuntimeException
            ('Registry: Cannot rename Namespace: ' . $namespace . ' since it has been locked.');
        }

        if ($this->exists($new_namespace)) {
        } else {
            throw new \RuntimeException
            ('Registry: Cannot rename ' . $namespace . ' to an existing registry ' . $new_namespace);
        }

        $existing = $this->getRegistry($namespace);
        ksort($existing);
        $this->deleteRegistry($namespace);
        $this->createRegistry($new_namespace);
        $this->registry[$new_namespace] = $existing;

        return $this;
    }

    /**
     * Returns an array containing key and name pairs for a namespace registry
     *
     * Usage:
     * Services::Registry()->getArray('Name Space');
     *
     * To retrieve only the key field names, not the values:
     * Services::Registry()->getArray('Name Space', true);
     *
     * @param   string $namespace
     * @param   boolean @$key_only set to true to retrieve key names
     *
     * @return  array
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function getArray($namespace, $key_only = false)
    {
        $namespace = $this->editNamespace($namespace);

        if ($this->exists($namespace)) {
        } else {
            throw new \RuntimeException
            ('Registry: Cannot retrieve array from Namespace ' . $namespace . ' since it does not exist.');
        }

        $array = $this->getRegistry($namespace);

        if ($key_only === false) {
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
     * Services::Registry()->loadArray('Namespace', $array);
     *
     * @param   string   $name   name of registry to use or create
     * @param   boolean  $array  key and value pairs to load
     *
     * @return  array
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function loadArray($namespace, $array = array())
    {
        if (is_array($array) && count($array) > 0) {
        } else {
            throw new \RuntimeException
            ('Registry: Empty or missing input array provided to loadArray.');
        }

        $namespace = $this->editNamespace($namespace);

        //if ($this->exists($namespace)) {
        //    throw new \RuntimeException
        //    ('Registry: Namespace ' . $namespace . ' already exists. Cannot use existing namespace with loadArray.');
       // }

        $this->getRegistry($namespace);

        $this->registry[$namespace] = $array;

        return $this;
    }

    /**
     * Returns the registry as an array for the specified namespace
     *
     * This is a private method used within the registry class, use get to retrieve Registry
     *
     * Services::Registry()->get('Name Space');
     *
     * @param   $namespace
     *
     * @return  array
     * @since   1.0
     */
    private function getRegistry($namespace)
    {
        $namespace = $this->editNamespace($namespace);

        if ($this->exists($namespace)) {
        } else {
            $this->createRegistry($namespace);
        }

        return $this->registry[$namespace];
    }

    /**
     * Retrieves a list of ALL namespace registries and optionally keys/values
     *
     * Usage:
     * Services::Registry()->listRegistry();
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

        $temp_row = new \stdClass();
        if (count($results) > 0) {
            foreach ($results as $key => $value) {
                $temp_row->$key = $value;
            }
        }
        $query_results[] = $temp_row;
        return $query_results;
    }

    /**
     * Used internally for data validation of namespace element
     *
     * @param    string  $namespace
     * @param    string  $operation
     *
     * @return   string
     * @throws   \RuntimeException
     */
    private function editNamespace($namespace)
    {
        if ($namespace === null) {
            $namespace = '*';

        } elseif (is_string($namespace) || is_numeric($namespace)) {
            $namespace = strtolower($namespace);
            $namespace = trim($namespace);

        } else {
            throw new \RuntimeException
            ('Registry: Namespace: is not a string.');
        }

        return $namespace;
    }

    /**
     * Used internally for data validation of namespace key element
     *
     * @param    string  $namespace
     * @param    string  $key
     *
     * @return   string
     * @throws   \RuntimeException
     */
    private function editNamespaceKey($namespace, $key = null)
    {
        if ($key === null) {

        } elseif (is_string($key) || is_numeric($key)) {
            $key = strtolower($key);
            $key = trim($key);

        } else {
            echo '<pre>';
            var_dump($key);
            echo '</pre>';
            throw new \RuntimeException
            ('Registry: Key associated with Namespace: ' . $namespace . ' is not a string.');
        }

        return $key;
    }

}
