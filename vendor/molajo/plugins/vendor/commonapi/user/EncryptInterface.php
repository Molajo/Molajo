<?php
/**
 * Encrypt Interface
 *
 * @package    User
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\User;

/**
 * Encrypt Interface
 *
 * @package    User
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
interface EncryptInterface
{
    /**
     * Create Hash from the input string
     *
     * For use with passwords, the hash is what is stored in the database
     *
     * @param   string $input
     *
     * @return  string
     * @since   1.0
     */
    public function createHashString($input);

    /**
     * Verify Input String to Hash
     *
     * For use with passwords, the input is the real password, but the hash is from the database
     *
     * @param   string $input
     * @param   string $hash
     *
     * @return  boolean
     * @since   1.0
     */
    public function verifyHashString($input, $hash);
}
