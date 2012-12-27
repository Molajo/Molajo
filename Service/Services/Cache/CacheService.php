<?php
/**
 * Cache Service
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Cache;

use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Cache Service
 *
 * Files 644
 * Folders 755
 * Configuration 444
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class CacheService
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * Cache
     *
     * @var    string
     * @since  1.0
     */
    protected $cache = false;

    /**
     * Cache Path
     *
     * @var    string
     * @since  1.0
     */
    protected $system_cache_folder = '';

    /**
     * Cache Handler
     *
     * @var    string
     * @since  1.0
     */
    protected $cache_handler = '';

    /**
     * Cache Time
     *
     * @var    Integer
     * @since  1.0
     */
    protected $cache_time = 900;

    /**
     * Cache Pages
     *
     * @var    string
     * @since  1.0
     */
    protected $cache_type_page = '';

    /**
     * Cache Templates
     *
     * @var    string
     * @since  1.0
     */
    protected $cache_type_template = '';

    /**
     * Cache Queries
     *
     * @var    string
     * @since  1.0
     */
    protected $cache_type_query = '';

    /**
     * Cache Model
     *
     * @var    string
     * @since  1.0
     */
    protected $cache_type_model = '';

    /**
     * Count Queries
     *
     * @var    string
     * @since  1.0
     */
    protected $count_queries = '';

    /**
     * Cache Keys
     *
     * @var    array
     * @since  1.0
     */
    protected $cache_keys = array();

    /**
     * List of Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $parameter_properties_array = array(
        'cache_service',
        'system_cache_folder',
        'cache_handler',
        'cache_time',
        'cache_type_page',
        'cache_type_template',
        'cache_type_query',
        'cache_type_model',
        'count_queries'
    );

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
            self::$instance = new CacheService();
        }

        return self::$instance;
    }

    /**
     * Initialise Cache when activated
     *
     * @since   1.0
     * @return  bool|CacheService
     */
    public function initialise()
    {
//@todo add classes other than file for caching

        foreach ($this->valid_types as $type) {
            $this->initialise_folders($type);
        }

        foreach ($this->valid_types as $type) {
            $this->prune_cache($type);
        }

        foreach ($this->valid_types as $type) {
            $this->loadCacheKeys($type);
        }

        return $this;
    }

    /**
     * Create a cache entry or set a parameter value
     *
     * @param   string  $type   Parameter, Page, Template, Query, Model
     * @param   string  $key    md5 name uniquely identifying content
     * @param   mixed   $value  Data to be serialized and then saved as cache
     *
     * @return  mixed
     * @since   1.0
     */
    public function set($type, $key, $value)
    {
        if ($type == 'Parameter') {

            $key = strtolower($key);

            if (in_array($key, $this->parameter_properties_array)) {
            } else {
                throw new \OutOfRangeException
                ('Cache Service: attempting to set value for unknown key: ' . $key);
            }

            $this->$key = $value;
        }

        $continue = $this->verify_cache($type);
        if ($continue === true) {
        } else {
            return false;
        }

        $key = md5($key);
        if ($this->exists($type, $key) === true) {
            return $this;
        }

        file_put_contents($this->system_cache_folder . '/' . $type . '/' . $key, serialize($value));
        chmod(($this->system_cache_folder . '/' . $type . '/' . $key), 0644);

        $this->cache_keys[$key] = 1;

        return $this;
    }

    /**
     * Return cached or parameter value
     *
     * @param   string  $type
     * @param   string  $key md5 name uniquely identifying content
     * @param   null    $default
     *
     * @return  bool|mixed  unserialized cache for this key
     * @since   1.0
     */
    public function get($type, $key, $default = null)
    {
        if ($type == 'Parameter') {

            $key = strtolower($key);

            if (in_array($key, $this->parameter_properties_array)) {
            } else {
                throw new \OutOfRangeException
                ('Cache Service: attempting to set value for unknown key: ' . $key);
            }

            if ($this->$key === null) {
                $this->$key = $default;
            }

            return $this->$key;
        }

        if (strtolower($type) == 'query') {
            $this->count_queries++;
        }

        $continue = $this->verify_cache($type);
        if ($continue === true) {
        } else {
            return false;
        }

        $key = md5($key);
        if ($this->exists($type, $key) === true) {
            return unserialize(file_get_contents($this->system_cache_folder . '/' . $type . '/' . $key));
        }

        return false;
    }

    /**
     * Load cache keys
     *
     * @return  object
     * @since   1.0
     */
    protected function loadCacheKeys($type)
    {
        if (is_dir($this->system_cache_folder)) {
        } else {
            return false;
            // error
        }

        $files = \files($this->system_cache_folder);

        if (count($files) > 0) {
            foreach ($files as $file) {
                $this->cache_keys[$file] = 1;
            }
        }

        return $this;
    }

    /**
     * Determine if cache exists for this object
     *
     * @param   string  $key md5 name uniquely identifying content
     *
     * @return  boolean The option value.
     * @since   1.0
     */
    protected function exists($type, $key)
    {
        if ($this->cache_service === true) {
        } else {
            return false;
        }

        if (isset($this->cache_keys[$key])) {
            return false;
        }
        $this->cache_keys[$key] = 1;

        return $this->checkExpired($type, $key);
    }

    /**
     * Verify type of cache
     *
     * @param   string $type
     *
     * @return  bool
     * @since   1.0
     */
    protected function verify_cache($type)
    {
        if ($this->cache_service === true) {
        } else {
            return false;
        }

        $cache_type = 'cache_' . strtolower($type);

        if ($this->$cache_type == 1) {
            return true;
        }

        return false;
    }

    /**
     * Flush all cache
     *
     * @return  object
     * @since   1.0
     */
    protected function prune_cache($type = '')
    {
        $cache_type = 'cache_' . strtolower($type);

        if ($this->$cache_type == 0) {
            $this->flush_cache($type);

        } else {

            $files = \files($this->system_cache_folder . '/' . $type);

            if (count($files) > 0) {
                foreach ($files as $file) {
                    $this->checkExpired($type, $file);
                }
            }
        }

        return $this;
    }

    /**
     * Remove cache for specified $key value
     *
     * @param   string  $key md5 name uniquely identifying content
     *
     * @return  bool    true (expired) false (did not expire)
     * @since   1.0
     */
    protected function checkExpired($type, $key)
    {
        if (file_exists($this->system_cache_folder . '/' . $type . '/' . $key)) {
        } else {
            $this->delete($type, $key);

            return false;
        }

        if (filemtime($this->system_cache_folder . '/' . $type . '/' . $key) < (time() - $this->cache_service_time)) {
            return true;
        }

        $this->delete($type, $key);

        return false;
    }

    /**
     * Flush all cache
     *
     * @return  object
     * @since   1.0
     */
    protected function flush_cache($type = '')
    {
        foreach ($this->valid_types as $t) {

            if ($type == '' || $type == $t) {

                $files = \files($this->system_cache_folder . '/' . $t . '/');

                if (count($files) == 0 || $files === false) {
                } else {
                    foreach ($files as $file) {
                        $this->delete($t, $file);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Remove cache for specified $key value
     *
     * @param   string  $key md5 name uniquely identifying content
     *
     * @return  object
     * @since   1.0
     */
    protected function delete($type, $key)
    {
        if (file_exists($this->system_cache_folder . '/' . $type . '/' . $key)) {
            unlink($this->system_cache_folder . '/' . $type . '/' . $key);
        }

        $temp = $this->cache_keys;
        foreach ($temp as $k => $v) {
            if ($key == $k) {
            } else {
                $temp[$k] = $v;
            }
        }
        $this->cache_keys = $temp;

        return $this;
    }

    /**
     * Create cache folders, if needed
     *
     * @return  object
     * @since   1.0
     */
    protected function initialise_folders($type = '')
    {
        if (is_dir($this->system_cache_folder . '/' . ucfirst(strtolower($type)))) {
            return true;
        }

        mkdir($this->system_cache_folder . '/' . ucfirst(strtolower($type)));
        chmod(($this->system_cache_folder . '/' . ucfirst(strtolower($type))), 0755);

        return is_dir($this->system_cache_folder . '/' . ucfirst(strtolower($type)));
    }
}
