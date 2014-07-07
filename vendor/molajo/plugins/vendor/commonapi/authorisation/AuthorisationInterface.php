<?php
/**
 * Authorisation Interface
 *
 * @package    Authorisation
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\Authorisation;

/**
 * Authorisation Interface
 *
 * @package    Authorisation
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
interface AuthorisationInterface
{
    /**
     * Verify User Authorisation to take Action on Resource
     *
     * @param   array $options
     *
     * @return  bool
     * @since   1.0.0
     */
    public function isUserAuthorised(array $options = array());
}
