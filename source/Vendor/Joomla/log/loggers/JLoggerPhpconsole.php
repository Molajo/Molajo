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
use Joomla\log\JLogEntry;

defined('JPATH_PLATFORM') or die;

/**
 * Joomla Email logger class.
 *
 * @package     Joomla.Platform
 * @subpackage  Log
 * @since       11.1
 */
class JLoggerEmail extends JLogger
{
	/**
	 * Method to add an entry to the log.
	 *
	 * @param   JLogEntry  $entry  The log entry object to add to the log.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function addEntry(JLogEntry $entry)
	{
		echo $this->priorities[$entry->priority] . ': ' . $entry->message . (empty($entry->category) ? '' : ' [' . $entry->category . ']') . "<br />";
	}
}
