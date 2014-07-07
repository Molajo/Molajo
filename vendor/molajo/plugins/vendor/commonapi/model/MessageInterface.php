<?php
/**
 * Message Interface
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\Model;

/**
 * Message Interface
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
interface MessageInterface
{
    /**
     * Set Messages and inject tokens
     *
     * @param   array $message_codes
     * @param   array $tokens
     *
     * @return  $this
     * @since   1.0.0
     */
    public function setMessages(array $message_codes, array $tokens);

    /**
     * Get Messages
     *
     * @return  array
     * @since   1.0.0
     */
    public function getMessages();
}
