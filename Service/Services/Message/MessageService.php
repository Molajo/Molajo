<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
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
     * Initialize registry for storing System Messages
     *
     * @return  mixed
     * @since   1.0
     */
    public function initialise()
    {
        return Services::Registry()->createRegistry(SYSTEM_MESSAGES_LITERAL);
    }

    /**
     * getMessages simulates a database list query for Messages
     *
     * @return  array
     * @since   1.0
     */
    public function getMessages()
    {
        $messages = $this->get();

        if (count($messages) == 0) {
            return array();
        }

        return $messages;
    }

    /**
     * get application messages
     *
     * @param   null  $option
     *
     * @return  int|array|MessageService
     * @since   1.0
     */
    public function get()
    {
        return Services::Registry()->getArray(SYSTEM_MESSAGES_LITERAL, SYSTEM_MESSAGES_LITERAL);
    }

    /**
     * Set the system message.
     *
     * @param   string   $message
     * @param   string   $type    message, notice, warning, and error
     * @param   integer  $code
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

        if ($type == MESSAGE_TYPE_INFORMATION
            || $type == MESSAGE_TYPE_WARNING
            || $type == MESSAGE_TYPE_ERROR
        ) {
        } else {
            $type = MESSAGE_TYPE_SUCCESS;
        }

        $messageArray = Services::Registry()->getArray(SYSTEM_MESSAGES_LITERAL, SYSTEM_MESSAGES_LITERAL);

        $temp_row = new \stdClass();

        $temp_row->message = $message;
        $temp_row->type = $type;
        $temp_row->code = $code;

        $messageArray[] = $temp_row;

        Services::Registry()->set(SYSTEM_MESSAGES_LITERAL, SYSTEM_MESSAGES_LITERAL, $messageArray);

        return true;
    }
}
