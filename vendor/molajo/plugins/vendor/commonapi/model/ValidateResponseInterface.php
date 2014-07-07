<?php
/**
 * Validate Response Interface
 *
 * @package    Model
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\Model;

/**
 * Validate Response Interface
 *
 * @package    Model
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
interface ValidateResponseInterface
{
    /**
     * Get Validation Response
     *
     * @return  Boolean
     * @since   1.0.0
     */
    public function getValidateResponse();

    /**
     * Get Validation Messages
     *
     * @return  array
     * @since   1.0.0
     */
    public function getValidateMessages();
}
