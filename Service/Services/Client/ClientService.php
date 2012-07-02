<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see license folder
 */
namespace Molajo\Service\Services\Client;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Client
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class ClientService
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new ClientService();
        }

        return self::$instance;
    }

    /**
     * __construct
     *
     * @param integer $identifier
     *
     * @return object
     * @since   1.0
     */
    protected function __construct()
    {
        Services::Registry()->createRegistry('Client');

        $this->get_ip_address();

        $this->get_client();

        return $this;
    }

    /**
     * get (possible) ip_address for Client
     *
     * @return object
     * @since   1.0
     */
    public function get_ip_address()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];

        /** Check to see if IP comes from Proxy */
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];

            /** Last priority */
        } else {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }

        Services::Registry()->set('Client', 'ip_address', $ip_address);

        return $this;
    }

    /**
     * get (very rough and not very reliable) client information
     *
     * - might be useful for very high-level guess about desktop versus mobile
     *   in those cases where it's critical to handle the payload or interface differently
     *
     * @return object
     * @since   1.0
     */
    public function get_client()
    {
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);

        /** Platform approximations */
        if (preg_match('/linux/i', $user_agent)) {
            $platform = 'linux';

        } elseif (preg_match('/macintosh|mac os x/i', $user_agent)) {
            $platform = 'mac';

        } elseif (preg_match('/windows|win32/i', $user_agent)) {
            $platform = 'windows';

        } else {
            $platform = 'unknown';
        }

        Services::Registry()->set('Client', 'platform', $platform);

        /** Desktop approximation */
        if ($platform == 'unknown') {
            $desktop = 0;
        } else {
            $desktop = 1;
        }

        Services::Registry()->set('Client', 'desktop', $desktop);

        /** Browser and Version Approximation */
        $browsers = array('firefox', 'msie', 'opera', 'chrome', 'safari',
            'mozilla', 'seamonkey',    'konqueror', 'netscape',
            'gecko', 'navigator', 'mosaic', 'lynx', 'amaya',
            'omniweb', 'avant', 'camino', 'flock', 'aol');

		$browser = '';
		$browser_version = '';
        foreach ($browsers as $browser) {

            if (preg_match("#($browser)[/ ]?([0-9.]*)#", $user_agent, $match)) {
                $browser = $match[1] ;
                $browser_version = $match[2] ;
                break ;
            }
        }

        Services::Registry()->set('Client', 'browser', $browser);

        Services::Registry()->set('Client', 'browser_version', $browser_version);

        Services::Registry()->set('Client', 'user_agent', $_SERVER['HTTP_USER_AGENT']);

        return $this;
    }
}
