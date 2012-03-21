<?php
/**
 * @package     Molajo
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

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
    protected $_messages = array();

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
     * __construct
     *
     * Class constructor.
     *
     * @return boolean
     * @since  1.0
     */
    public function __construct()
    {
    }

    /**
     * set
     *
     * Set the system message.
     *
     * @param   string  $message
     * @param   string  $type      message, notice, warning, and error
     *
     * @return  bool
     * @since   1.0
     */
    public function set($message = null,
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

        /** load session messages into messages array */
        //$this->_getSessionMessages();

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
     * get
     *
     * @return  array  Application messages
     * @since   1.0
     */
    public function get()
    {
//        $this->_getSessionMessages();
        return $this->_messages;
    }

    /**
     *  _getSessionMessages
     *
     * Retrieve messages in _session and load into Application messages array
     *
     * @return  void
     * @since   1.0
     */
    private function _getSessionMessages()
    {
        $_session = $this->getSession();
        $_getSessionMessages = $_session->get('application.messages');

        if (count($_getSessionMessages) > 0) {
            $count = count($this->_messages);
            foreach ($_getSessionMessages as $_sessionMessage) {
                $this->_messages[$count] = $_sessionMessage;
                $count++;
            }
            $_session->set('application.messages', null);
        }
    }

    /**
     * _setSessionMessages
     *
     * Store system messages in Session variable
     *
     * @return  array  System messages
     * @since   1.0
     */
    private function _setSessionMessages()
    {
        $_session = $this->getSession();

        if (count($this->_messages) > 0) {
            $_session->set('application.messages', $this->_messages);
        } else {
            $_session->set('application.messages', null);

        }
    }
}
