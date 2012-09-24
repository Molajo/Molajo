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
	 * Cache Pages
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $cache_pages = '';

	/**
	 * Cache Templates
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $cache_templates = '';

	/**
	 * Cache Queries
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $cache_queries = '';

	/**
	 * Cache Queries
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $cache_models = '';

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
		$this->cache_service = false;

		$this->cache_models = false;
		$this->cache_pages = false;
		$this->cache_queries = false;
		$this->cache_templates = false;

		return $this;
	}

	/**
	 * Start Cache - if so configured
	 *
	 * @since  1.0
	 */
	public function startCache()
	{
		if (Services::Registry()->get('Configuration', 'cache_service') == 0) {
			$this->cache_service = false;
			return false;
		}
		$this->cache_service = true;

		if (Services::Registry()->get('Configuration', 'cache_handler', 'file') == 'file') {
			$this->system_cache_folder = SITE_BASE_PATH . '/'
				. Services::Registry()->get('Configuration', 'system_cache_folder');
		} else {
			return false;
		}

		$this->cache_service_time = (int)Services::Registry()->get('Configuration', 'cache_time', 900);
		if ($this->cache_service_time == 0) {
			$this->cache_service_time = 900;
		}

		$this->cache_models = Services::Registry()->get('Configuration', 'cache_models');
		$this->cache_pages = Services::Registry()->get('Configuration', 'cache_pages');
		$this->cache_queries = Services::Registry()->get('Configuration', 'cache_queries');
		$this->cache_templates = Services::Registry()->get('Configuration', 'cache_templates');

		Services::Registry()->createRegistry('Cachekeys');
		$this->loadCacheKeys();

		return $this;
	}

	/**
	 * Load cache keys
	 *
	 * @return object
	 * @since   1.0
	 */
	protected function loadCacheKeys()
	{
		if (is_dir($this->system_cache_folder)) {
		} else {
			return false;
			// error
		}

		$files = Services::Filesystem()->folderFiles($this->system_cache_folder);
		foreach ($files as $file) {
			Services::Registry()->set('Cachekeys', $file, 1);
		}

		if (count($files) == 0
			|| $files === false
		) {
			return $this;
		}

		foreach ($files as $file) {
			Services::Registry()->set('Cachekeys', $file, 1);
		}

		return $this;
	}

	/**
	 * Create a cache entry
	 *
	 * @param string $type  Page, Template, Query, Model
	 * @param string $key   md5 name uniquely identifying content
	 * @param mixed  $value Data to be serialized and then saved as cache
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function set($type, $key, $value)
	{
		$continue = $this->verify_cache($type);
		if ($continue === true) {
		} else {
			return false;
		}

		$key = md5($key);
		if ($this->exists($key) === true) {
			return $this;
		}

		file_put_contents($this->system_cache_folder . '/' . $key, serialize($value));

		Services::Registry()->set('Cachekeys', $key, 1);

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
	public function get($type, $key)
	{
		$continue = $this->verify_cache($type);
		if ($continue === true) {
		} else {
			return false;
		}

		$key = md5($key);
		if ($this->exists($key) === true) {
			return unserialize(file_get_contents($this->system_cache_folder . '/' . $key));
		}

		return false;
	}

	/**
	 * Determine if cache exists for this object
	 *
	 * @param string $key md5 name uniquely identifying content
	 *
	 * @return boolean The option value.
	 * @since   1.0
	 */
	protected function exists($key)
	{
		if ($this->cache_service === true) {
		} else {
			return false;
		}

		$exists = Services::Registry()->exists('Cachekeys', $key);
		if ($exists === true) {
		} else {
			return false;
		}

		return $this->checkExpired($key);
	}

	/**
	 * Remove cache for specified $key value
	 *
	 * @param string $key md5 name uniquely identifying content
	 *
	 * @return object
	 * @since   1.0
	 */
	protected function checkExpired($key)
	{

		if (file_exists($this->system_cache_folder . '/' . $key)) {
		} else {
			$this->delete($key);

			return false;
		}

		if (filemtime($this->system_cache_folder . '/' . $key) < (time() - $this->cache_service_time)) {
			return true;
		}

		$this->delete($key);

		return false;
	}

	/**
	 * Flush all cache
	 *
	 * @return object
	 * @since   1.0
	 */
	public function flush_cache($type)
	{
		$files = Services::Filesystem()->folderFiles($this->system_cache_folder);
		foreach ($files as $file) {
			$this->delete($file);
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
	protected function delete($key)
	{
		if (file_exists($this->system_cache_folder . '/' . $key)) {
			unlink($this->system_cache_folder . '/' . $key);
		}

		Services::Registry()->delete('Cachekeys', $key);

		return $this;
	}

	/**
	 * Verify type of cache
	 *
	 * @param $type
	 * @return bool
	 */
	public function verify_cache($type)
	{
		if ($this->cache_service === true) {
		} else {
			return false;
		}

		switch (strtolower($type)) {
			case 'page':
				if ($this->cache_pages == 1) {
					return true;
				} else {
					return false;
				}

			case 'template':
				if ($this->cache_templates == 1) {
					return true;
				} else {
					return false;
				}

			case 'query':
				if ($this->cache_queries == 1) {
					return true;
				} else {
					return false;
				}

			case 'model':
				if ($this->cache_models == 1) {
					return true;
				} else {
					return false;
				}
		}

		return false;
	}
}
