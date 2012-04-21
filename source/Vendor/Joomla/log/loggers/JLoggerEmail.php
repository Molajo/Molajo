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
	 * @var    string  Mailer Object
	 * @since  12.1
	 */
	protected $mailer;

	/**
	 * @var    string  Sender email
	 * @since  12.1
	 */
	protected $sender;

	/**
	 * @var    string  Recipient email list
	 * @since  12.1
	 */
	protected $recipient;

	/**
	 * @var    string  Email Subject
	 * @since  12.1
	 */
	protected $subject;

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
		if (empty($this->options['sender']))
		{
			$this->sender = array(JFactory::getConfig()->get('mailfrom'), JFactory::getConfig()->get('fromname'));
		}
		else
		{
			$this->sender = $this->options['sender'];
		}

		if (empty($this->options['recipient']))
		{
			$this->recipient = JFactory::getConfig()->get('mailfrom');
		}
		else
		{
			$this->recipient = $this->options['recipient'];
		}

		if (isset($this->options['subject']))
		{
			if (is_array($this->options['category']))
			{
				$this->subject = trim(implode(' ', $this->options['category']));
			}
			else
			{
				$this->subject = trim($this->options['category']);
			}
		}
		else
		{
			$this->subject = JFactory::getConfig()->get('sitename');
		}

		if (empty($this->options['recipient']))
		{
			$this->mailer = JFactory::getMailer();
		}
		else
		{
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
		$this->mailer->setSender($this->sender);
		$this->mailer->setRecipient($this->recipient);
		$this->mailer->setSubject($this->subject);
		$this->mailer->setBody(
				$this->priorities[$entry->priority]
				. ': '
				. $entry->message
				. (empty($entry->category) ? '' : ' [' . $entry->category . ']')
			);

		$results = $this->mailer->Send();

		if ($results == false)
		{
			throw new \RuntimeException('Email log entry not sent');
		}
	}
}
