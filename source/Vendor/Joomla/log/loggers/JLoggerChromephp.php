<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Log
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\log\loggers;

use Joomla\log\JLogger;

use Joomla\log\JLog;

use Joomla\log\JLogEntry;

use ChromePHP\ChromePHP;

defined('JPATH_PLATFORM') or die;

/**
 * Joomla Echo logger class.
 *
 * Works in concert with ChromePHP Class https://github.com/ccampbell/chromephp http://www.chromephp.com/
 *
 * Download the Chrome extension from: https://chrome.google.com/extensions/detail/noaneddfkdjfnfdakjjmocngnfkfehhd
 *
 * @package     Joomla.Platform
 * @subpackage  Log
 * @since       12.1
 */
class JLoggerChromephp extends JLogger
{
	/**
	 * Method to add an entry to the log.
	 *
	 * @param   JLogEntry  $entry  The log entry object to add to the log.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function addEntry(JLogEntry $entry)
	{
		$message = $this->priorities[$entry->priority]
			. ': '
			. $entry->message
			. (empty($entry->category) ? '' : ' [' . $entry->category . ']');

		if ($entry->priority == JLog::WARNING)
		{
			$method = 'warn';
		}
		elseif ($entry->priority == JLog::ERROR)
		{
			$method = 'error';
		}
		else
		{
			$method = 'log';
		}

		ChromePhp::$method($message);
	}
}
