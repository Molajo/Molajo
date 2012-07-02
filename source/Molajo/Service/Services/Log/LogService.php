<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see license folder
 */
namespace Molajo\Service\Services\Log;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Log
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class LogService
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * Valid Priorities
     *
     * @var    object
     * @since  1.0
     */
    protected $priorities;

    /**
     * Options
     *
     * @var    array
     * @since  1.0
     */
    protected $options;

    /**
     * Valid Loggers
     *
     * @var    object
     * @since  1.0
     */
    protected $loggers;

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new LogService();
        }

        return self::$instance;
    }

    /**
     * Class constructor
     *
     * @return boolean
     * @since   1.0
     */
    public function __construct()
    {
        /** Valid Priorities */
        $this->priorities = array();

        $this->priorities[] = LOG_TYPE_EMERGENCY;
        $this->priorities[] = LOG_TYPE_ALERT;
        $this->priorities[] = LOG_TYPE_CRITICAL;
        $this->priorities[] = LOG_TYPE_ERROR;
        $this->priorities[] = LOG_TYPE_WARNING;
        $this->priorities[] = LOG_TYPE_NOTICE;
        $this->priorities[] = LOG_TYPE_INFO;
        $this->priorities[] = LOG_TYPE_DEBUG;
        $this->priorities[] = LOG_TYPE_ALL;

        /** Valid Loggers */
        $this->loggers = array();

        /** Provided with JPlatform */
        $this->loggers[] = LOG_FORMATTEDTEXT_LOGGER;
        $this->loggers[] = LOG_ECHO_LOGGER;
        $this->loggers[] = LOG_DATABASE_LOGGER;

        /** Custom Molajo loggers */
        $this->loggers[] = LOG_MESSAGES_LOGGER;
        $this->loggers[] = LOG_EMAIL_LOGGER;
        $this->loggers[] = LOG_CONSOLE_LOGGER;

		if (Services::Registry()->get('DebugService', 'CurrentPhase') == START_INITIALISE) {
			$response = Services::Debug()->setDebugLogger();
			if ($response == false) {
				Services::Debug()->setConfigurationComplete();
				return $this;
			}
			$this->setLog($response['options'], $response['priority'], $response['types']);
			Services::Debug()->setConfigurationComplete();
		}

        return $this;
    }

    /**
     * Initiate a logging activity and define logging options
     *
     * @param array   $options  Configuration array
     * @param integer $priority Valid priority for log
     * @param array   $types    Valid types for log
     *
     * $options array
     *
     * 0. logger is a required option
     *
     * $options['logger'] valid values include: console, echo (default), database, formattedtext, messages
     *
     * 1. Echo
     *
     * $options['line_separator'] <br /> or /n (default)
     *
     * 2. Text
     *
     * $options['text_file'] ex. error.php (default)
     * $options['text_file_path'] ex. /users/amystephen/sites/molajo/source/site/1/logs (default SITES_LOGS_FOLDER)
     * $options['text_file_no_php'] false - adds die('Forbidden') to top of file (true prevents the precaution)
     * $options['text_entry_format'] - can be used to specify a custom log format
     *
     * 3. Database
     *
     * $options['dbo'] - Services::JDatabase();
     * $options['db_table'] - #__log
     *
     * +++ Molajo custom loggers
     *
     * 4. Email
     * $this->options['sender'] = array(
     *             Services::Registry()->get('Configuration', 'mail_from'),
     *          Services::Registry()->get('Configuration', 'mail_from_name')
     *         };
     * $this->options['recipient'] = Services::Registry()->get('Configuration', 'mail_from_email_address');
     * $this->options['subject'] = Services::Language()->translate('LOG_ALERT_EMAIL_SUBJECT'));
     * $this->options['mailer'] = Services::Mail();
     *
     * 5. ChromePHP
     * No addition $option[] values. However, this option requires using Google Chrome and installing this
     * Google Chrome extension: https://chrome.google.com/webstore/detail/noaneddfkdjfnfdakjjmocngnfkfehhd
     * and https://github.com/ccampbell/chromephp
     *
     * @return boolean
     *
     * @since   1.0
     * @throws \RuntimeException
     */
    public function setLog($options = array(), $priority = LOG_TYPE_ALL, $types = array())
    {
        try {
            $class = 'Joomla\\log\\JLog';
            $class::addLogger($options, $priority, $types);

			} catch (\Exception $e) {
				throw new \RuntimeException('Unable to set Log: ' . $e->getMessage());
        }

        return true;
    }

    /**
     * Method to add an entry to a Log
     *
     * @param string  $message
     * @param integer $priority
     * @param array   $type
     * @param string  $date
     *
     * @return boolean
     *
     * @since   1.0
     * @throws \RuntimeException
     */
    public function addEntry($message, $priority = 0, $type = '', $date = '')
    {
        /** Message */
        $message = (string) $message;

        /** Priority */
        if (in_array($priority, $this->priorities)) {
        } else {
            $priority = LOG_TYPE_INFO;
        }

        /** Type */
        $type = (string) strtolower(preg_replace('/[^A-Z0-9_\.-]/i', '', $type));

        /** Date */
        $date = Services::Date()->getDate($date);

        /** Log it */
        try {
            $class = 'Joomla\\log\\JLog';
            $class::add($message, $priority, $type, $date);
        } catch (\Exception $e) {
			throw new \RuntimeException('Log entry failed for ' . $message . 'Error: ' . $e->getMessage());
		}

        return true;
    }
}
