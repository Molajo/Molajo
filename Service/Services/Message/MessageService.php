<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Message;

use Molajo\Service\Services;

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
     * Set the system message.
     *
     * @param string  $message
     * @param string  $type    message, notice, warning, and error
     * @param integer $code
     *
     * @return bool
     * @since   1.0
     */
    public function set($message = null, $type = 'message', $code = null)
    {
        if ($message == null && $code == null) {
            return false;
        }

        $type = strtolower($type);

        if ($type == MESSAGE_TYPE_INFORMATION
            || $type == MESSAGE_TYPE_WARNING
            || $type == MESSAGE_TYPE_ERROR
) {
        } else {
            $type = MESSAGE_TYPE_SUCCESS;
        }

        $count = count($this->messages);

        $this->messages[$count]['message'] = $message;
        $this->messages[$count]['type'] = $type;
        $this->messages[$count]['code'] = $code;

        return true;
    }

    /**
     * get application messages
     *
     * @return array Application messages
     *
     * @since   1.0
     */
    public function get($option = null)
    {
        if ($option == 'db') {
            return $this;

        } elseif ($option == 'count') {
            return count($this->messages);

        } else {
            return $this->messages;
        }

    }

    /**
     *     Dummy functions to pass service off as a DBO to interact with model
     */
    public function getNullDate()
    {
        return $this;
    }

    public function getQuery()
    {
        return $this;
    }

    public function toSql()
    {
        return $this;
    }

    public function clear()
    {
        return $this;
    }

    /**
     * getMessages is called out of the ReadModel to simulate a database query for Messages
     *
     * @return array
     *
     * @since    1.0
     */
    public function getMessages()
    {
        $query_results = array();

        $messages = $this->get();
        if (count($messages) == 0) {
            return array();
        }

        foreach ($messages as $message) {

            $row = new \stdClass();

            $row->message = $message['message'];
            $row->type = $message['type'];
            $row->code = $message['code'];

            $query_results[] = $row;
        }

        return $query_results;
    }
}
