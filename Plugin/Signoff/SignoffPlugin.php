<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Signout;

use Molajo\Plugin\Plugin\Plugin;

defined('MOLAJO') or die;

/**
 * Signout
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class SignoutPlugin extends Plugin
{

    /**
     * Before Authenticating the Signout Process
     *
     * @return  boolean
     * @since   1.0
     */
    public function onBeforeSignout()
    {
        return false;
    }

    /**
     * After Authenticating the Signout Process
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterSignout()
    {
        return false;
    }
}
