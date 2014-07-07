<?php
/**
 * Mailer Interface
 *
 * @package    User
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\User;

/**
 * Mailer Interface
 *
 * @package    User
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
interface MailerInterface
{
    /**
     * Set the Option Values, Initiate Rendering, Send
     *
     * @param   string    $template
     * @param   object    $input_data
     *
     * @return  $this
     * @since   1.0
     */
    public function send($template, $input_data);
}
