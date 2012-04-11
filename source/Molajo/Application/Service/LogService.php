<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

defined('MOLAJO') or die;

/**
 * Log
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class LogService extends BaseService
{

    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

	/**
	 * Application responsible for log entry.
	 * @var    string
	 * @since  11.1
	 */
	public $category;

	/**
	 * The date the message was logged.
	 * @var    /Date
	 * @since  11.1
	 */
	public $date;

	/**
	 * Message to be logged.
	 * @var    string
	 * @since  11.1
	 */
	public $message;

	/**
	 * The priority of the message to be logged.
	 * @var    string
	 * @since  11.1
	 * @see    $priorities
	 */
	public $priority = LOG_TYPE_INFO;

	/**
	 * List of available log priority levels [Based on the SysLog default levels].
	 * @var    array
	 * @since  11.1
	 */
	protected $priorities = array(
		LOG_TYPE_EMERGENCY,
		LOG_TYPE_ALERT,
		LOG_TYPE_CRITICAL,
		LOG_TYPE_ERROR,
		LOG_TYPE_WARNING,
		LOG_TYPE_NOTICE,
		LOG_TYPE_INFO,
		LOG_TYPE_DEBUG
	);


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
     * __construct
     *
     * Class constructor.
     *
     * @since  1.0
     */
    public function __construct()
    {


// Initialise a basic logger with no options (once only).
		JLog::addLogger(array());

// Add a message.
JLog::add(&apos;Logged&apos;);



	/**
	 * Add a logger to the JLog instance.  Loggers route log entries to the correct files/systems to be logged.
	 *
	 * @param   array    $options     The object configuration array.
	 * @param   integer  $priorities  Message priority
	 * @param   array    $categories  Types of entry
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */


	}

	/**
	 * Method to add an entry to the log.
	 *
	 * @param   mixed    $entry     The JLogEntry object to add to the log or the message for a new JLogEntry object.
	 * @param   integer  $priority  Message priority.
	 * @param   string   $category  Type of entry
	 * @param   string   $date      Date of entry (defaults to now if not specified or blank)
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public static function add($entry, $priority = self::INFO, $category = '', $date = null)
	{

	}
}
