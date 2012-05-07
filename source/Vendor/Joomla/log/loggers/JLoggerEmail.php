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

use Joomla\JFactory;

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
	 * @var    string  From email address
	 * @since  12.1
	 */
	protected $from;

	/**
	 * @var    string  To email address list
	 * @since  12.1
	 */
	protected $to;

	/**
	 * @var    string  Subject
	 * @since  12.1
	 */
	protected $subject;

	/**
	 * @var    string  Mailer Object
	 * @since  12.1
	 */
	protected $mailer;

	/**
	 * Constructor.
	 *
	 * @param   array  $options  Log object options.
	 *
	 * @since   11.1
	 */
	public function __construct(array $options)
	{
		// Call the parent constructor.
		parent::__construct($options);

		// If both the database object and driver options are empty we want to use the system database connection.
		if (empty($this->options['from'])) {
			$this->from = JFactory::getConfig()->get('mailfrom');
		}
		else {
			$this->from = $this->options['from'];
		}

		if (empty($this->options['to'])) {
			$this->to = JFactory::getConfig()->get('mailfrom');
		}
		else {
			$this->to = $this->options['to'];
		}

		if (empty($this->options['subject'])) {
			$this->subject = JFactory::getConfig()->get('sitename');
		}
		else {
			$this->subject = $this->options['subject'];
		}

		if (empty($this->options['mailer'])) {
			$this->mailer = JFactory::getMailer();
		}
		else {
			$this->mailer = $this->options['mailer'];
		}
	}

	/**
	 * Method to email log entry.
	 *
	 * @param   JLogEntry  $entry  The log entry object to add to the log.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  /RuntimeException
	 */
	public function addEntry(JLogEntry $entry)
	{
		$this->mailer->setSender($this->from);
		$this->mailer->setRecipient($this->to);
		$this->mailer->setSubject($this->subject);
		$this->mailer->setBody(
			$this->priorities[$entry->priority]
				. ': '
				. $entry->message
				. (empty($entry->category) ? '' : ' [' . $entry->category . ']')
		);

		$results = $this->mailer->Send();

		if ($results == false) {
			throw new \RuntimeException('Email log entry not sent');
		}
	}
}
