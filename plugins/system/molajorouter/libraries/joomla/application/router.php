<?php
/**
 * @version		$Id:router.php 8876 2007-09-13 22:54:03Z jinx $
 * @package		Joomla.Framework
 * @subpackage	Application
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_BASE') or die;

/**
 * Set the available masks for the routing mode
 */
define('JROUTER_MODE_RAW', 0);
define('JROUTER_MODE_SEF', 1);

define('JROUTER_MODE', 1);

/**
 * Class to create and parse routes
 *
 * @package		Joomla.Framework
 * @subpackage	Application
 * @since		1.5
 */
class JRouter extends JObject
{
	public $_vars = array();
	
	/**
	 * Array of buildrules
	 * 
	 * @var array
	 */
	protected $buildrules = array();
	
	/**
	 * Array of parserules
	 * 
	 * @var array
	 */
	protected $parserules = array();
	
	/**
	 * Router-Options
	 * 
	 * @var array
	 */
	protected $options = array();

	/**
	 * Returns the global JRouter object, only creating it if it
	 * doesn't already exist.
	 *
	 * @param	string	The name of the client
	 * @param	array	An associative array of options
	 * @return	JRouter	A router object.
	 */
	public static function getInstance($client, $options = array())
	{
		static $instances;

		if (!isset($instances)) {
			$instances = array();
		}

		if (empty($instances[$client])) {
			//Load the router object
			$info = JApplicationHelper::getClientInfo($client, true);

			if(!class_exists('JRouter'.ucfirst($client))) {
				$path = $info->path.'/includes/router.php';
				if (file_exists($path)) {
					require_once $path;
				
					// Create a JRouter object
					$classname = 'JRouter'.ucfirst($client);
					$instance = new $classname($options);
				} else {
					$error = JError::raiseError(500, JText::sprintf('JLIB_APPLICATION_ERROR_ROUTER_LOAD', $client));
					return $error;		
				}
			} else {
				// Create a JRouter object
				$classname = 'JRouter'.ucfirst($client);
				$instance = new $classname($options);
			}

			$instances[$client] = & $instance;
		}

		return $instances[$client];
	}

	/**
	 *  Function to convert a route to an internal URI
	 */
	public function parse(&$uri)
	{
		// Process the parsed variables based on custom defined rules
		foreach($this->parserules as $rule) {
			call_user_func_array($rule, array(&$this, &$uri));
		}
		$this->setVars($uri->getQuery(true));

		return $uri->getQuery(true);
	}

	/**
	 * Function to convert an internal URI to a route
	 *
	 * @param	string	The internal URL
	 * @return	string	The absolute search engine friendly URL
	 */
	public function build($url)
	{
		if(!is_array($url))
		{
			//Read the URL into an array
			$temp = array();
			if (strpos($url, '&amp;') !== false) {
				$url = str_replace('&amp;','&',$url);
			}
			
			if(substr($url,0,10) == 'index.php?') {
				$url = substr($url, 10);
			}

			parse_str($url, $temp);

			foreach($temp as $key => $var) {
				if ($var == "") {
					unset($temp[$key]);
				}
			}
			$url = $temp;
		}
		
		$key = md5(serialize($url));
		if(isset($this->cache[$key]))
		{
			return $this->cache[$key];
		}
		
		$uri = new JURI();
		$uri->setQuery($url);
		
		//Process the uri information based on custom defined rules
		foreach($this->buildrules as $rule) {
			call_user_func_array($rule, array(&$this, &$uri));
		}
		
		// Get the path data
		$route = $uri->getPath();
		if(!$route)
		{
			$route = 'index.php';
		}
		
		//Add basepath to the uri
		$uri->setPath(JURI::base(true).'/'.$route);

		$this->cache[$key] = $uri;
		
		return $uri;
	}

	/**
	 * Get the router mode
	 */
	public function getOptions($key = null, $value = null)
	{
		if($key) {
			if(isset($this->options[$key])) {
				return $this->options[$key];
			} else {
				return $value;
			}
		}
		return $this->options;
	}

	/**
	 * Get the router mode
	 */
	public function setOption($key, $value)
	{
		$this->options[$key] = $value;
	}
	
	public function setVars($query)
	{
		$this->_vars = $query;
	}
	
	public function getVars()
	{
		return $this->_vars;
	}
	
	public function getMode()
	{
		if(defined(JROUTER_MODE)) {
			return JROUTER_MODE;
		}
		return 'undefined';
	}

	/**
	 * Attach a build rule
	 *
	 * @param	callback	The function to be called.
	 * @param	position	The position where this 
	 * 						function is supposed to be executed.
	 * 						Valid values: 'first', 'last'
	 */
	public function attachBuildRule($callback, $position = 'last')
	{
		if($position == 'last')	{
			$this->buildrules[] = $callback;
		} elseif ($position == 'first') {
			array_unshift($this->buildrules, $callback);
		}
	}

	/**
	 * Attach a parse rule
	 *
	 * @param	callback	The function to be called.
	 * @param	position	The position where this 
	 * 						function is supposed to be executed.
	 * 						Valid values: 'first', 'last'
	 */
	public function attachParseRule($callback, $position = 'last')
	{
		if($position == 'last')	{
			$this->parserules[] = $callback;
		} elseif ($position == 'first') {
			array_unshift($this->parserules, $callback);
		}
	}
}
