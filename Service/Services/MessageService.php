<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */

namespace Molajo\Service;

defined('MOLAJO') or die;

/**
 * Message
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class MessageService
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * Messages
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $messages = array();

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
			self::$instance = new MessageService();
		}
		return self::$instance;
	}

	/**
	 * Class constructor.
	 *
	 * @return boolean
	 * @since  1.0
	 */
	public function __construct()
	{
	}

	/**
	 * get application messages
	 *
	 * @return  array  Application messages
	 *
	 * @since   1.0
	 */
	public function get()
	{
		return $this->messages;
	}

	/**
	 * Set the system message.
	 *
	 * @param  string  $message
	 * @param  string  $type      message, notice, warning, and error
	 * @param  integer $code
	 *
	 * @return  bool
	 * @since   1.0
	 */
	public function set($message = null, $type = 'message', $code = null)
	{
		if ($message == null && $code == null) {
			return false;
		}

		$type = strtolower($type);

		if ($type == MESSAGE_TYPE_NOTICE
			|| $type == MESSAGE_TYPE_WARNING
			|| $type == MESSAGE_TYPE_ERROR
		) {
		} else {
			$type = MESSAGE_TYPE_MESSAGE;
		}

		$count = count($this->messages);

		$this->messages[$count]['message'] = $message;
		$this->messages[$count]['type'] = $type;
		$this->messages[$count]['code'] = $code;

		return true;
	}
}
