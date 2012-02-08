<?php
/**
 * @package     Molajo
 * @subpackage  Service
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Dispatcher
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
class MolajoMessageService
{

    /**
     * Messages
     *
     * @var    array
     * @since  1.0
     */
    protected $_messages = array();


    /**
     * setMessage
     *
     * Set the system message.
     *
     * @param   string  $message
     * @param   string  $type      message, notice, warning, and error
     *
     * @return  bool
     * @since   1.0
     */
    public static function setMessage($message = null,
                                      $type = 'message',
                                      $code = null,
                                      $debug_location = null,
                                      $debug_object = null)
    {
        if ($message == null
            && $code == null
        ) {
            return false;
        }

        $type = strtolower($type);
        if ($type == MOLAJO_MESSAGE_TYPE_NOTICE
            || $type == MOLAJO_MESSAGE_TYPE_WARNING
            || $type == MOLAJO_MESSAGE_TYPE_ERROR
        ) {
        } else {
            $type = MOLAJO_MESSAGE_TYPE_MESSAGE;
        }

        /** load _session messages into application messages array */
        $this->_sessionMessages();

        /** add new message */
        $count = count($this->_messages);

        $this->_messages[$count]['message'] = $message;
        $this->_messages[$count]['type'] = $type;
        $this->_messages[$count]['code'] = $code;
        $this->_messages[$count]['debug_location'] = $debug_location;
        $this->_messages[$count]['debug_object'] = $debug_object;

        return true;
    }

    /**
     * getMessages
     *
     * Get system messages
     *
     * @return  array  System messages
     * @since   1.0
     */
    public function getMessages()
    {
        $this->_sessionMessages();
        return $this->_messages;
    }

    /**
     *  _sessionMessages
     *
     * Retrieve messages in _session and load into Application messages array
     *
     * @return  void
     * @since   1.0
     */
    private function _sessionMessages()
    {
        $_session = $this->getSession();
        $_sessionMessages = $_session->get('application.messages');

        if (count($_sessionMessages) > 0) {
            $count = count($this->_messages);
            foreach ($_sessionMessages as $_sessionMessage) {
                $this->_messages[$count] = $_sessionMessage;
                $count++;
            }
            $_session->set('application.messages', null);
        }
    }
}
