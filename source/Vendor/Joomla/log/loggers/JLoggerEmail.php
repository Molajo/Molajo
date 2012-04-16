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
	 * @var    string  Name of the sender
	 * @since  12.1
	 */
	protected $sender;

	/**
	 * @var    string  Name of the recipient
	 * @since  12.1
	 */
	protected $recipient;

	/**
	 * @var    string  Email subject
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
		if (empty($this->options['sender']) && empty($this->options['recipient']) && empty($this->options['mailer']))
		{
			$this->sender = array(JFactory::getConfig()->get('mailfrom'), JFactory::getConfig()->get('fromname'));
			$this->recipient = JFactory::getConfig()->get('mailfrom');
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
				$this->subject = JFactory::getConfig()->get('sitename') . ' Alert';
			}
			$this->mailer = JFactory::getMailer();
		}
		else
		{
			$this->sender = $this->options['sender'];
			$this->recipient = $this->options['recipient'];
			$this->subject = $this->options['subject'];
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

		if ($results == true)
		{
			throw new \RuntimeException('Email log entry not sent');
		}
	}
}
