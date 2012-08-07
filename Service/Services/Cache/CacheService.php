<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Cache;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Cache
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
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
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new CacheService();
        }

        return self::$instance;
    }

    /**
     * Class constructor
     *
     * @since  1.0
     */
    public function __construct()
    {
        $this->cache = false;

        return $this;
    }

    /**
     * Start Cache - if so configured
     *
     * @since  1.0
     */
    public function startCache()
    {
        if (Services::Registry()->get('Configuration', 'cache') == 0) {
            $this->cache = false;

            return false;
        }

        $this->cache = true;

        if (Services::Registry()->get('Configuration', 'cache_handler', 'file') == 'file') {
            $this->system_cache_folder = SITE_BASE_PATH . '/'
                . Services::Registry()->get('Configuration', 'system_cache_folder');
        } else {
            return false;
        }

        $this->cache_time = (int) Services::Registry()->get('Configuration', 'cache_time', 900);
        if ($this->cache_time == 0) {
            $this->cache_time = 900;
        }

        Services::Registry()->createRegistry('Cachekeys');
        $this->loadCacheKeys();

        return $this;
    }

    /**
     * Determine if cache exists for this object
     *
     * @param string $key md5 name uniquely identifying content
     *
     * @return boolean The option value.
     * @since   1.0
     */
    public function exists($key)
    {
        if ($this->cache == true) {
            $exists = Services::Registry()->exists('Cachekeys', $key);
            if ($exists == false) {
                return false;
            }

            return $this->checkExpired($key);
        }
    }

    /**
     * Create a cache entry
     *
     * @param string $key   md5 name uniquely identifying content
     * @param mixed  $value Data to be serialized and then saved as cache
     *
     * @return object
     * @since   1.0
     */
    public function set($key, $value)
    {
        file_put_contents($this->system_cache_folder . '/' . $key, serialize($value));

        Services::Registry()->set('Cachekeys', $key);

        return $this;
    }

    /**
     * Return cached value
     *
     * @param string $key md5 name uniquely identifying content
     *
     * @return mixed unserialized cache for this key
     * @since   1.0
     */
    public function get($key)
    {
        if ($key == 'on') {
            return $this->cache;
        }

        if ($this->cache == true) {
            return unserialize(file_get_contents($this->system_cache_folder . '/' . $key));
        }

        return false;
    }

    /**
     * Load cache keys
     *
     * @return object
     * @since   1.0
     */
    public function loadCacheKeys()
    {
        if (is_dir($this->system_cache_folder)) {
        } else {
            return false;
        }

        $files = Services::Filesystem()->folderFiles($this->system_cache_folder);
        if (count($files) > 0
            || $files === false
        ) {
            return $this;
        }

        foreach ($files as $file) {
            $results = Services::Registry()->delete('Cachekeys', $file);
            if ($results === false) {
            } else {
                Services::Registry()->set('Cachekeys', $file);
            }
        }

        return $this;
    }

    /**
     * Remove cache for specified $key value
     *
     * @param string $key md5 name uniquely identifying content
     *
     * @return object
     * @since   1.0
     */
    public function checkExpired($key)
    {

        if (file_exists($this->system_cache_folder . '/' . $key)) {
        } else {
            $this->delete($key);

            return false;
        }

        if (filemtime($this->system_cache_folder . '/' . $key) < (time() - $this->cache_time)) {
            return true;
        }

        $this->delete($key);
    }

    /**
     * Remove cache for specified $key value
     *
     * @param string $key md5 name uniquely identifying content
     *
     * @return object
     * @since   1.0
     */
    public function delete($key)
    {
        if (file_exists($this->system_cache_folder . '/' . $key)) {
            unlink($this->system_cache_folder . '/' . $key);
        }

        Services::Registry()->delete('Cachekeys', $key);

        return $this;
    }
}
