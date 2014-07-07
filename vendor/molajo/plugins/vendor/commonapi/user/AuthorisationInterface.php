<?php
/**
 * User Authorisation Interface
 *
 * @package    User
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\User;

/**
 * User Authorisation Interface
 *
 * @package    User
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
interface AuthorisationInterface
{
    /**
     * Verify User Permission to take Action on Resource
     *
     * @param   int    $action_id
     * @param   int    $resource_id
     * @param   string $type
     *
     * @return  bool
     * @since   1.0
     */
    public function isUserAuthorised($action_id, $resource_id, $type = 'Catalog');
}
