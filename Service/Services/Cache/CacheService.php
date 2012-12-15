<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Cache;

use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Cache
 *
 * @package     Niambie
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
	 * Cache Queries
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
	 * Class constructor
	 *
	 * @since   1.0
     * @return  object
	 */
	public function __construct()
	{
		$this->cache_service = false;

		return $this;
	}

	/**
	 * Initialise Cache when activated
	 *
	 * @since   1.0
     * @return  bool|CacheService
     */
    public function initialise()
	{
//@todo remove hardcoded types and make it configurable
//@todo add classes other than file for caching

		if (Services::Registry()->get(CONFIGURATION_LITERAL, 'cache_handler', 'file') == 'file') {
			$this->system_cache_folder = SITE_BASE_PATH . '/'
				. Services::Registry()->get(CONFIGURATION_LITERAL, 'system_cache_folder');
		} else {
			return false;
		}

		$this->cache_service_time = (int)Services::Registry()->get(CONFIGURATION_LITERAL, 'cache_time', 900);

		if ($this->cache_service_time == 0) {
			$this->cache_service_time = 900;
		}

		if (Services::Registry()->get(CONFIGURATION_LITERAL, 'cache_service') == 0) {
			$this->cache_service = false;
			$this->cache_type_model = 0;
			$this->cache_type_page = 0;
			$this->cache_type_query = 0;
			$this->cache_type_template = 0;
		} else {
			$this->cache_service = true;
			$this->cache_type_model = Services::Registry()->get(CONFIGURATION_LITERAL, 'cache_type_model');
			$this->cache_type_page = Services::Registry()->get(CONFIGURATION_LITERAL, 'cache_type_page');
			$this->cache_type_query = Services::Registry()->get(CONFIGURATION_LITERAL, 'cache_type_query');
			$this->cache_type_template = Services::Registry()->get(CONFIGURATION_LITERAL, 'cache_type_template');
		}

		Services::Registry()->set('cache_service', 'on' , $this->cache_service);

		$this->valid_types = array();
		$this->valid_types[] = 'type_model';
		$this->valid_types[] = 'type_page';
		$this->valid_types[] = 'type_query';
		$this->valid_types[] = 'type_template';

		foreach ($this->valid_types as $type) {
			$this->initialise_folders($type);
		}

		foreach ($this->valid_types as $type) {
			$this->prune_cache($type);
		}

		Services::Registry()->createRegistry('Cachekeys');
		foreach ($this->valid_types as $type) {
			$this->loadCacheKeys($type);
		}

		Services::Registry()->set('cache_service', 'on', true);

		return $this;
	}

	/**
	 * Create a cache entry
	 *
	 * @param   string  $type  Page, Template, Query, Model
	 * @param   string  $key   md5 name uniquely identifying content
	 * @param   mixed   $value Data to be serialized and then saved as cache
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
		if ($this->exists($type, $key) === true) {
			return $this;
		}

		file_put_contents($this->system_cache_folder . '/' . $type . '/' . $key, serialize($value));

		Services::Registry()->set('Cachekeys', $key, 1);

		return $this;
	}

	/**
	 * Return cached value
	 *
	 * @param   string $key md5 name uniquely identifying content
	 *
	 * @return  mixed unserialized cache for this key
	 * @since   1.0
	 */
	public function get($type, $key)
	{
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

		$exists = Services::Registry()->get('Cachekeys', $key);
		if ($exists === false) {
			return false;
		}

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

            $files = Services::Filesystem()->folderFiles($this->system_cache_folder . '/' . $type);

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
	 * @param  string  $key md5 name uniquely identifying content
	 *
	 * @return  object
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
	 * @return object
	 * @since   1.0
	 */
	protected function flush_cache($type = '')
	{
		foreach ($this->valid_types as $t) {

			if ($type == '' || $type == $t) {

				$files = Services::Filesystem()->folderFiles($this->system_cache_folder . '/' . $t . '/');

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

		Services::Registry()->delete('Cachekeys', $key);

		return $this;
	}

	/**
	 * Create cache folders, if needed
	 *
	 * @return object
	 * @since   1.0
	 */
	protected function initialise_folders($type = '')
	{
		$exists = Services::Filesystem()->folderExists($this->system_cache_folder . '/' . ucfirst(strtolower($type)));
		if ($exists === true) {
			return true;
		}

		$results = Services::Filesystem()->folderCreate($this->system_cache_folder . '/' . ucfirst(strtolower($type)));
		chmod(($this->system_cache_folder . '/' . ucfirst(strtolower($type))), 0755);

		return $results;
	}
}
