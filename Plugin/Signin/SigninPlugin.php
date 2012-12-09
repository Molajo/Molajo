<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\signin;

use Molajo\Plugin\Plugin\Plugin;

defined('MOLAJO') or die;

/**
 * signin
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class SigninPlugin extends Plugin
{
    /**
     * Before Authenticating the Signin Process
     *
     * @return  boolean
     * @since   1.0
     */
    public function onBeforeSignin()
    {
        return false;
    }

    /**
     * After Authenticating the Signin Process
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterSignin()
    {
        return false;
    }
}
