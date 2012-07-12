<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Trigger\Application;

use Molajo\Extension\Trigger\Trigger\Trigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Application Base
 *
 * Application events are scheduled one, and only one time, per page load
 * Standard system variables, like Molajo::Registry()->('parameters') should
 * be used in Application Triggers since there is no danger of collusion
 * with other instances
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class ApplicationTrigger extends Trigger
{

    /**
     * Runs before Route and after Services and Helpers have been instantiated
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterInitialise()
    {
        return true;
    }

    /**
     * Fires after Route has run - Parameters contain all instruction
     *
     * Services::Registry('Parameters', '*') lists all available
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRoute()
    {
        return true;
    }

    /**
     * Follows Authorise and can used to override a failed authorisation or a successful one
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterAuthorise()
    {
        return true;
    }

    /**
     * Event fires after execute for both display and non-display task
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterExecute()
    {
        return true;
    }

    /**
     * Trigger that fires after all views are rendered
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterResponse()
    {
        return true;
    }

}
