<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service;

use Joomla\registry\JRegistry;

defined('MOLAJO') or die;

/**
 * Registry
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 *
 * listRegistry - retrieves a list of Registries and optionally keys and/or values
 *   Usage: Service::Registry()->listRegistry(true);
 * 		true - lists all registries and key/value pairs
 * 		false - only lists names of registries
 *
 * initialise - create a new Registry to be used locally by the caller
 *   Usage: $local = Service::Registry()->initialise();
 *
 * create - Create new global parameter set stored within RegistryService
 *   Usage:  Service::Registry()->create('request');
 *
 * get - returns a Parameter property for a specific item and parameter set
 *   Alias for JFactory::getConfig, returning full registry set for local use
 *   Usage: Service::Registry()->get('Request', 'parameter_name');
 *
 * set - sets a Parameter property for a specific item and parameter set
 *   Usage: Service::Registry()->set('Request', 'parameter_name', $value);
 *
 * copy - Copy one global registry to a new registry
 *   Usage: Service::Registry()->copy('x', 'y');
 *
 * getArray - Returns an array containing key and name pairs for a registry
 *   Usage: Service::Registry()->getArray('request');
 *
 * loadArray - Populates a registry with an array of key and name pairs
 *   Usage: Service::Registry()->loadArray('request', $array);
 *
 * getKeys - Returns all key names for the specified parameter set
 *   Usage: Service::Registry()->getKeys('request');
 *
 * loadField - Loads JSON data from a field given the field xml definition
 *   (used for fields like parameters, custom fields, metadata, etc.)
 *   Usage: Service::Registry()->loadField('Namespace\\', 'field_name',
 *         $results['field_name'], $xml->field_group);
 *
 * listRegistry
 *
 * Standard Molajo Registry namespaces:
 *
 *  Application
 *  .. Configuration
 *  .. Site
 *  .. .. SiteCustomfields
 *  .. .. SiteMetadata
 *  .. Application
 *  .. .. ApplicationCustomfields
 *  .. .. ApplicationMetadata
 *
 *  User
 *  .. UserCustomfields
 *  .. UserMetadata
 *  .. UserParameters
 *
 *  Document
 *  .. DocumentCustomfields
 *  .. DocumentMetadata
 *  .. DocumentParameters
 *  .. DocumentLanguage
 *  .. Menu
 *  .. .. MenuCustomfields
 *  .. .. MenuMetadata
 *  .. .. MenuParameters
 *  .. Override
 *  .. Request
 *  .. .. RequestCatalog
 *  .. .. RequestExtension
 *  .. .. .. RequestExtensionCustomfields
 *  .. .. .. RequestExtensionMetadata
 *  .. .. .. RequestExtensionParameters
 *  .. .. RequestSource
 *  .. .. .. RequestSourceCustomfields
 *  .. .. .. RequestSourceMetadata
 *  .. .. .. RequestSourceParameters
 *  .. .. RequestMVC
 *  .. .. RequestTemplate
 *  .. .. RequestWrap
 *  .. Theme
 *  .. .. ThemeCatalog
 *  .. .. ThemeCustomfields
 *  .. .. ThemeMetadata
 *  .. .. ThemeParameters
 *  .. Page
 *
 * Include
 *  .. Catalog
 *  .. Extension
 *  .. .. ExtensionCustomfields
 *  .. .. ExtensionMetadata
 *  .. .. ExtensionParameters
 *  .. Source
 *  .. .. SourceCustomfields
 *  .. .. SourceMetadata
 *  .. .. SourceParameters
 *  .. MVC
 *  .. .. MVCParameters
 *  .. Template
 *  .. Wrap
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
	 * $parameters
	 *
	 * Array of all registries
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $parameters;

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
		$this->parameters = array();

		/** initialise known namespaces for application */
		$xml = CONFIGURATION_FOLDER . '/registry.xml';
		if (is_file($xml)) {
		} else {
			return false;
		}

		$list = simplexml_load_file($xml);

		foreach ($list->registry as $item) {
			$ns = (string)$item;
			$this->create($ns, true);
		}

		return $this;
	}

	/**
	 * Create new Registry object to be used locally
	 *
	 * Usage:
	 * $local = Service::Registry()->initialise();
	 *
	 * @return JRegistry
	 * @since  1.0
	 */
	public function initialise()
	{
		return new JRegistry();
	}

	/**
	 * Create new global parameter set stored within RegistryService
	 *
	 * Usage:
	 * Service::Registry()->create('request');
	 *
	 * @param   string  $name
	 * @param    boolean $force recreate, if already exists
	 *
	 * @return  null
	 * @since   1.0
	 */
	public function create($name, $force = false)
	{
		if (isset($this->parameters[$name]))  {
			if ($force === false) {
				return $this;
			}
		}
		$this->parameters[$name] = new JRegistry();
		return $this;
	}

	/**
	 * Retrieves a list of Registries and optionally keys and/or values
	 *
	 * Usage:
	 * Service::Registry()->listRegistry(1);
	 *
	 * @param   boolean $all true - returns the entire list and each registry
	 *                         false - returns a list of registry names, only
	 *
	 * @return  mixed|boolean or array
	 * @since   1.0
	 */
	public function listRegistry($all = false)
	{
		if (count($this->parameters) == 0) {
			return false;
		}

		$registry = array();

		foreach ($this->parameters as $name => $object) {
			$registry[] = $name;
		}

		asort($registry);

			return $registry;
	}

	/**
	 * Returns a Parameter property for a specific item and parameter set
	 *   Alias for JFactory::getConfig, returning full registry set for local use
	 *
	 * Usage:
	 * Service::Registry()->get('Request', 'parameter_name');
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @param  string  $type
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function get($key, $default = null)
	{
		$split = explode('\\', $key);

		if (count($split) == 1) {
			$local = $this->initialise();

			$array = $this->getArray($split[0]);

			foreach ($array as $key => $value) {


				if ($value === null) {
				} else {
					$local->set($key, $value);
				}
			}

			return $local;
		}

		/** Normal single value get */
		if (isset($this->parameters[$split[0]])) {
		} else {
			$this->parameters[$split[0]]->set($split[1], $default);
		}
		return $this->parameters[$split[0]]->get($split[1], $default);
	}

	/**
	 * Sets a Parameter property for a specific item and parameter set
	 *
	 * Usage:
	 * Service::Registry()->set('Request', 'parameter_name', $value);
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function set($key, $value = null)
	{
		$split = explode('\\', $key);

		if (isset($this->parameters[$split[0]])) {
		} else {
			$this->create($split[0]);
		}

		return $this->parameters[$split[0]]->set($split[1], $value);
	}

	/**
	 * Copy one global registry to another
	 *
	 * Usage:
	 * Service::Registry()->copy('x', 'y');
	 *
	 * @param $set1
	 * @param $set2
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function copy($copyThis, $intoThis)
	{
		$this->create($intoThis);

		$a = array();
		while (list($k, $v) = each($this->parameters[$copyThis])) {
			while (list($key, $value) = each($v)) {
				$this->set($intoThis . '\\' . $key, $value);
			}
		}
		return $this;
	}

	/**
	 * Returns an array containing key and name pairs for a specified parameter set
	 *
	 * Usage:
	 * Service::Registry()->getArray('request');
	 *
	 * @param   string  $name
	 * @param   boolean @keyOnly set to true to retrieve keynames
	 *
	 * @return  array
	 * @since   1.0
	 */
	public function getArray($name, $keyOnly = false)
	{
		$a = array();

		while (list($k, $v) = each($this->parameters[$name])) {

			while (list($key, $value) = each($v)) {
				if ($keyOnly === false) {
					$a[$key] = $value;
				} else {
					$a[] = $key;
				}
			}
		}

		if (count($a) == 0) {
			return array();
		} else {
			return $a;
		}
	}

	/**
	 * Populates a registry with an array of key and name pairs
	 *
	 * Usage:
	 * Service::Registry()->loadArray('request', $array);
	 *
	 * @param   string  $name  name of registry to use or create
	 * @param   boolean $array key and value pairs to load
	 *
	 * @return  array
	 * @since   1.0
	 */
	public function loadArray($name, $array = array())
	{
		foreach ($array as $key => $value) {
			if ($value === null) {
			} else {
				$this->set($name . '//' . $key, $value);
			}
		}
		return;
	}

	/**
	 * Returns all key names for the specified parameter set
	 *
	 * Usage:
	 * Service::Registry()->getKeys('request');
	 *
	 * @param   string  $name
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function getKeys($name)
	{
		return $this->getArray($name, true);
	}

	/**
	 * Loads JSON data from a field given the field xml definition
	 *     (can be used for fields like parameters, custom fields, metadata, etc.)
	 *
	 * Usage:
	 * Service::Registry()->loadField('Namespace\\', 'field_name', $results['field_name'], $xml->field_group);
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
		$temp = $this->initialise();
		$temp->loadString($data, 'JSON');

		if (isset($xml->$field_name)) {

			foreach ($xml->$field_name as $cf) {

				$name = (string)$cf['name'];
				$dataType = (string)$cf['filter'];
				$null = (string)$cf['null'];
				$default = (string)$cf['default'];
				$values = (string)$cf['values'];

				//todo: filter given XML field definitions

				if ($default == '') {
					$val = $temp->get($name, null);
				} else {
					$val = $temp->get($name, $default);
				}

				$this->set($namespace . $name, $val);
			}
		}
	}
}
