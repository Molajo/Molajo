<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT, see License folder
 */
namespace Molajo\Plugin\login;

use Molajo\Plugin\Plugin\Plugin;

defined('NIAMBIE') or die;

/**
 * login
 *
 * @package     Niambie
 * @license     MIT
 * @since       1.0
 */
class LoginPlugin extends Plugin
{
    /**
     * Before Authenticating the Login Process
     *
     * @return  boolean
     * @since   1.0
     */
    public function onBeforeLogin()
    {
        return false;
    }

    /**
     * After Authenticating the Login Process
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterLogin()
    {
        return false;
    }
}
