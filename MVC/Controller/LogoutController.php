<?php
/**
 * Logout Controller
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\MVC\Controller;

use Molajo\Service\Services;
use Molajo\MVC\Controller\Controller;

defined('MOLAJO') or die;

/**
 * The logout controller manages system logout actions and schedules before and after logout events.
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
class LogoutController extends Controller
{
    /**
     * Method to logout a user from the site and application
     *
     * @return void
     */
    public function logout()
    {
    }

    /**
     * Schedule Before Logout Event
     *
     * @return void
     */
    public function onBeforeLogoutEvent()
    {
    }

    /**
     * Schedule After Logout Event
     *
     * @return void
     */
    public function onAfterLogoutEvent()
    {
    }
}
