<?php
/**
 * @version		$Id: log.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('MOLAJO') or die;


/**
 * Molajo System Logging Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	System.log
 */
class  plgSystemLog extends MolajoApplicationPlugin
{
	function onUserLoginFailure($response)
	{
		jimport('joomla.error.log');

		$log = JLog::getInstance();
		$errorlog = array();

		switch($response['status'])
		{
			case MOLAJO_AUTHENTICATE_STATUS_CANCEL :
			{
				$errorlog['status']  = $response['type']." CANCELED: ";
				$errorlog['comment'] = $response['error_message'];
				$log->addEntry($errorlog);
			} break;

			case MOLAJO_AUTHENTICATE_STATUS_FAILURE :
			{
				$errorlog['status']  = $response['type']." FAILURE: ";
				$errorlog['comment'] = $response['error_message'];
				$log->addEntry($errorlog);
			}	break;

			default :
			{
				$errorlog['status']  = $response['type']." UNKNOWN ERROR: ";
				$errorlog['comment'] = $response['error_message'];
				$log->addEntry($errorlog);
			}	break;
		}
	}
}
