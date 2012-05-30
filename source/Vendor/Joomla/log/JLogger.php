<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Log
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\log;

defined('JPATH_PLATFORM') or die;

/**
 * Joomla! Logger Base Class
 *
 * This class is used to be the basis of logger classes to allow for defined functions
 * to exist regardless of the child class.
 *
 * @package     Joomla.Platform
 * @subpackage  Log
 * @since       11.1
 */
abstract class JLogger
{
	/**
	 * Options array for the JLog instance.
	 * @var    array
	 * @since  11.1
	 */
	protected $options = array();

	/**
	 * @var    array  Translation array for JLogEntry priorities to text strings.
	 * @since  11.1
	 */
	protected $priorities = array(
		JLog::EMERGENCY => 'EMERGENCY',
		JLog::ALERT => 'ALERT',
		JLog::CRITICAL => 'CRITICAL',
		JLog::ERROR => 'ERROR',
		JLog::WARNING => 'WARNING',
		JLog::NOTICE => 'NOTICE',
		JLog::INFO => 'INFO',
		JLog::DEBUG => 'DEBUG');

	/**
	 * Constructor.
	 *
	 * @param   array  &$options  Log object options.
	 *
	 * @since   11.1
	 */
	public function __construct(array &$options)
	{
		// Set the options for the class.
		$this->options = & $options;
	}

	/**
	 * Method to add an entry to the log.
	 *
	 * @param   JLogEntry  $entry  The log entry object to add to the log.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	abstract public function addEntry(JLogEntry $entry);
}