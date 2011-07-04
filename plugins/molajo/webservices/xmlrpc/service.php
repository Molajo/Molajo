<?php
/**
 * @version		$Id: service.php 253 2009-01-07 23:56:26Z louis $
 * @package		JXtended.Libraries
 * @subpackage	XMLRPC
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

/**
 * XMLRPC Service Router Class
 *
 * @package		JXtended.Libraries
 * @subpackage	XMLRPC
 * @version		1.0
 */
class JXServices extends JObject
{
	/**
	 * Methods array
	 * @access	protected
	 * @var		array
	 */
	var $_methods;

	/**
	 * Constructor
	 *
	 * @access	protected
	 * @return	void
	 * @since	1.0
	 */
	function __construct()
	{
		$this->_methods = array();
	}

	/**
	 * Get a singleton instance of the JXServices object
	 *
	 * <code>
	 *	<?php
	 *	jximport('jxtended.xmlrpc.service');
	 *	$services = &JXServices::getInstance();
	 *	?>
	 * </code>
	 *
	 * @static
	 * @return	object	JXServices object
	 * @since	1.0
	 */
	function &getInstance()
	{
		static $instance;

		if ($instance == null)
		{
			$instance = new JXServices();
		}
		return $instance;
	}

	/**
	 * Registers all xml-rpc services in a given folder
	 *
	 * <code>
	 *	<?php
	 *	jximport('jxtended.xmlrpc.service');
	 *	$services = &JXServices::getInstance();
	 *
	 *	// Register all the methods in a given folder
	 *	$services->register({PATH_TO_METHODS});
	 *	?>
	 * </code>
	 *
	 * @access	public
	 * @param	string	$path	The path to the root folder
	 * @return	void
	 * @since	1.0
	 */
	function register( $path )
	{
		$d = dir($path);
		while (false !== ($file = $d->read()))
		{
			$fileName = $path.DS.$file;
			if (file_exists($fileName) && is_file($fileName))
			{
				require_once( $path.DS.$file );
			}
		}
		$d->close();
	}

	/**
	 * Registers a JXService method
	 *
	 * <code>
	 *	<?php
	 *	jximport('jxtended.xmlrpc.service');
	 *	$services = &JXServices::getInstance();
	 *
	 *	// Instantiate a JXService based method
	 *	$method = new myWebService();
	 *
	 *	// Register the method
	 *	$services->registerMethod($method);
	 *	?>
	 * </code>
	 *
	 * @access	public
	 * @param	object	$method	A JXService derived method
	 * @return	void
	 * @since	1.0
	 */
	function registerMethod( $method )
	{
		$this->_methods[] = $method;
	}

	/**
	 * Gets information strings about all methods
	 *
	 * <code>
	 *	<?php
	 *	jximport('jxtended.xmlrpc.service');
	 *	$services = &JXServices::getInstance();
	 *
	 *	// Register all the methods in a given folder
	 *	$services->register({PATH_TO_METHODS});
	 *
	 *	// Instantiate a JXService based method
	 *	$info = $services->getInfo();
	 *	?>
	 * </code>
	 *
	 * @access	public
	 * @return	array	Information data for the registered methods
	 * @since	1.0
	 */
	function getInfo()
	{
		$result = array();

		for ($i = 0, $n = count($this->_methods); $i < $n; $i++)
		{
			$result = array_merge( $result, $this->_methods[$i]->getInfo() );
		}
		return $result;
	}
}

/**
 * XMLRPC Service Class
 *
 * @abstract
 * @package		JXtended.Libraries
 * @subpackage	XMLRPC
 * @version		1.0
 */
class JXService
{
	/**
	 * The name of the service method
	 * @access	protected
	 * @var		string
	 */
	var $_name;

	/**
	 * Register a method with the JXServices
	 *
	 * @access	public
	 * @param	object	$method	A JXService derived object
	 * @return	void
	 * @since	1.0
	 */
	function registerMethod( $method )
	{
		$services = JXServices::getInstance();
		$services->registerMethod( $method );
	}

	/**
	 * Runs the method
	 *
	 * @access	public
	 * @param	object	$xmlrpcmsg	The XMLRPC message object
	 * @return	mixed	Method return value
	 * @since	1.0
	 */
	function run( $xmlrpcmsg )
	{
		return null;
	}

	/**
	 * Gets info about the method
	 *
	 * @access	public
	 * @return	array	Information for the registered method
	 * @since	1.0
	 */
	function getInfo()
	{
		$result = array(
			$this->_name => array(
				'function' => get_class( $this ) . '::run',
				'signature' => $this->getSignature(),
				'docstring' => $this->getDocString()
			)
		);
		return $result;
	}

	/**
	 * Gets the signature of the method
	 * - eg: array( array( <return type>[, <arg1 type>[,<arg2 type> ... ]]))
	 *
	 * @access	public
	 * @return	array	Signature for the registered method
	 * @since	1.0
	 */
	function getSignature()
	{
		return null;
	}

	/**
	 * Help text for the method
	 *
	 * @access	public
	 * @return	string	Help text for the registered method
	 * @since	1.0
	 */
	function getDocString()
	{
		return 'No help available';
	}
}
