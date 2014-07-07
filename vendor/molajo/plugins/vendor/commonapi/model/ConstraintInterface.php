<?php
/**
 * Constraint Interface
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\Model;

/**
 * Constraint Interface
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
interface ConstraintInterface
{
    /**
     * Validate
     *
     * @return  boolean
     * @since   1.0.0
     */
    public function validate();

    /**
     * Sanitize
     *
     * @return  mixed
     * @since   1.0.0
     */
    public function sanitize();

    /**
     * Format
     *
     * @return  mixed
     * @since   1.0.0
     */
    public function format();

    /**
     * Get Messages
     *
     * @return  array
     * @since   1.0.0
     */
    public function getValidateMessages();

    /**
     * Save Message Codes for Validation Failures
     *
     * @param   string $message_code
     *
     * @return  $this
     * @since   1.0.0
     */
    public function setValidateMessage($message_code);
}
