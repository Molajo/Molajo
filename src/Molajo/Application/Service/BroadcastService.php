<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

use Molajo\Application\Services;

defined('MOLAJO') or die;

/**
 * Broadcast
 *
 * Establish and utilize broadcast agents
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class BroadcastService extends BaseService
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    // Register broadcasting agents: email, call, text, ping, tweet, etc.

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
    }
}
