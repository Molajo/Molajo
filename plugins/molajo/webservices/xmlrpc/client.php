<?php
/**
 * @version		$Id: client.php 253 2009-01-07 23:56:26Z louis $
 * @package		JXtended.Libraries
 * @subpackage	XMLRPC
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

jximport('joomla.utilities.error');

/**
 * XML-RPC Helper class
 *
 * @static
 * @package		JXtended.Libraries
 * @subpackage	XMLRPC
 * @version		1.0
 */
class JXXMLRPCHelper
{
	/**
	 * Cleans up a text string
	 *
	 * <code>
	 *	<?php
	 *	$string = JXMLRPCHelper::cleanup({STRING_TO_CLEAN});
	 *	?>
	 * </code>
	 *
	 * @static
	 * @param	string	$text	The string to cleanup and properly encode
	 * @return	string	Cleaned up text string
	 * @since	1.0
	 */
	function cleanup( $text )
	{
		// fix junk word chars
		// TODO: Find a better UTF8 method
		$text = str_replace( chr( 28 ), '', $text );

		$text = utf8_encode( trim( $text ) );
		return $text;
	}

	/**
	 * Get the XML-RPC client object
	 *
	 * <code>
	 *	<?php
	 *	$client = &JXMLRPCHelper::getClient({SERVER_HOST}, {SERVER_PATH}, {SERVER_PORT} [, {REQUEST_METHOD}] [, {DEBUG_MODE}]);
	 *	?>
	 * </code>
	 *
	 * @static
	 * @param	string	$host	The server host
	 * @param	string	$path	The server path
	 * @param	int		$port	The server port
	 * @param	string	$method	The request method [http or https]
	 * @param	boolean	$debug	Client debugging mode
	 * @return	object	XML-RPC client object
	 * @since	1.0
	 */
	function &getClient($host, $path, $port, $method=null, $debug=false)
	{
		static $client;

		if (is_object($client)) {
			return $client;
		}

		// Import and instantiate the client object
		jximport('phpxmlrpc.xmlrpc');
		if ($path) {
			$client = new xmlrpc_client($path, $host, $port, $method);
		} else {
			$client = new xmlrpc_client($host);
		}
		$client->return_type = 'phpvals';
		$client->setDebug($debug);

		// Temporary fix
		$client->verifypeer = false;
		$client->verifyhost = false;

		return $client;
	}

	/**
	 * Get the transaction ID number
	 *
	 * <code>
	 *	<?php
	 *	$trans_id = JXMLRPCHelper::getTransId();
	 *	?>
	 * </code>
	 *
	 * @static
	 * @return	int	Transaction ID
	 * @since	1.0
	 */
	function getTransId()
	{
		return time();
	}

	/**
	 * Handle the XML-RPC response object and return a native php type.
	 *
	 * <code>
	 *	<?php
	 *	// Get the client object
	 *	$client = &JXMLRPCHelper::getClient({SERVER_HOST}, {SERVER_PATH}, {SERVER_PORT} [, {REQUEST_METHOD}] [, {DEBUG_MODE}]);
	 *
	 *	// Create the XML-RPC message object to send
	 *	$message = new xmlrpcmsg({METHOD_NAME});
	 *
	 *	// Send the XML-RPC request and get the response
	 *	$response = $client->send($message);
	 *	$response = &JXMLRPCHelper::handleResponse($response [, {DUMP_DATA}] [, {METHOD_NAME}]);
	 *	if (JError::isError($response)) {
	 *		echo 'There was an error with our request';
	 *	} else {
	 *		echo 'We now have a response value of: '.var_export($response);
	 *	}
	 *	?>
	 * </code>
	 *
	 * @static
	 * @param	object	XML-RPC Response Object
	 * @param	boolean	Dump the payload for an error
	 * @param	string	The name of the method
	 * @return	mixed	Mixed response or boolean false on error
	 * @since	1.0
	 */
	function handleResponse(&$response, $dump=false, $method='')
	{
		if ($response->faultCode() == 0) {
			// Successful request
			$return = $response->value();
		} else {
			// Error request
			$return = JError::raiseWarning(400, $response->faultCode() . '-' . $response->faultString() );

			if ($dump) {
				$file	= JPATH_BASE.DS.'logs'.DS.'dump.'.$method.'.'.date( 'Ymd' ).'.txt';
				$buffer = "\n\n@".date( 'Y-m-d g:i:s' )."----------\n";
				$buffer .= 'Code: '.$response->faultCode().', Message: '.$response->faultString()."\n";
				$buffer .= strip_tags( $response->value() );
				file_put_contents( $file, $buffer, FILE_APPEND );
			}
		}
		return $return;
	}
}
