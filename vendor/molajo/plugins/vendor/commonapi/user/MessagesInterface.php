<?php
/**
 * Messages Interface
 *
 * @package    User
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\User;

/**
 * Messages Interface
 *
 * @package    User
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
interface MessagesInterface
{
    /**
     * Set Message
     *
     * @param   int    $message_id
     * @param   string $message
     *
     * @since   1.0
     * @return  $this
     */
    public function setMessage($message_id, $message);

    /**
     * Store Flash (User) Messages in Flashmessage for presentation after redirect
     *
     * @param   int    $message_id
     * @param   array  $values
     * @param   string $type (Success, Notice, Warning, Error)
     *
     * @return  $this
     * @since   1.0
     */
    public function setFlashmessage($message_id, array $values = array(), $type = 'Error');

    /**
     * Get Message
     *
     * @param   int   $message_id
     * @param   array $values
     *
     * @since   1.0
     * @return  string
     */
    public function getMessage($message_id = 0, array $values = array());
}
