<?php
/**
 * Client Service
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Client;

use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Client Service
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 *
 * Usage Instructions:
 *
 * To retrieve a single value:
 * Services::Client()->get('property');
 *  where property = 'ajax', 'ip_address', 'browser', 'browser_version', 'user_agent', 'desktop', 'platform'
 *
 * To retrieve all values:
 * Services::Client()->get('*');
 *
 */
Class ClientService
{
    /**
     * Ajax
     *
     * @var    bool
     * @since  1.0
     */
    protected $ajax = null;

    /**
     * IP Address
     *
     * @var    string
     * @since  1.0
     */
    protected $ip_address = null;

    /**
     * Browser
     *
     * @var    string
     * @since  1.0
     */
    protected $browser = null;

    /**
     * Browser Version
     *
     * @var    string
     * @since  1.0
     */
    protected $browser_version = null;

    /**
     * User Agent
     *
     * @var    string
     * @since  1.0
     */
    protected $user_agent = null;

    /**
     * Desktop
     *
     * @var    string
     * @since  1.0
     */
    protected $desktop = null;

    /**
     * Platform
     *
     * @var    string
     * @since  1.0
     */
    protected $platform = null;

    /**
     * List of Properties
     *
     * @var    array
     * @since  1.0
     */
    protected $parameter_properties_array = array(
        'ajax',
        'ip_address',
        'browser',
        'browser_version',
        'user_agent',
        'desktop',
        'platform'
    );

    /**
     * __construct
     *
     * @return  object
     * @since   1.0
     */
    public function __construct()
    {
        $this->get_ip_address();
        $this->isAjax();
        $this->get_client();

        return $this;
    }

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  mixed
     * @since   1.0
     */
    public function get($key = null, $default = null)
    {
        $key = strtolower($key);

        if ($key == '*') {
            $results = array();
            foreach ($this->parameter_properties_array as $parameter) {
                $results[$key] = $this->$key;
            }
            return;
        }

        if (in_array($key, $this->parameter_properties_array)) {
        } else {
            throw new \OutOfRangeException('Client Service: is attempting to get value for unknown key: ' . $key);
        }

        if ($this->$key === null) {
            $this->$key = $default;
        }

        return $this->$key;
    }

    /**
     * Set the value of a specified key
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed
     * @since   1.0
     */
    public function set($key, $value = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->parameter_properties_array)) {
        } else {
            throw new \OutOfRangeException('Client Service: is attempting to set value for unknown key: ' . $key);
        }

        $this->$key = $value;

        return $this->$key;
    }

    /**
     * get (possible) ip_address for Client
     *
     * @return  object
     * @since   1.0
     */
    protected function get_ip_address()
    {
        if (empty($_SERVER['HTTP_CLIENT_IP'])) {

            if (empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip_address = $_SERVER['REMOTE_ADDR'];

            } else {
                $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }

        } else {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        }

        $this->set('ip_address', $ip_address);

        return $this;
    }

    /**
     * Tests to determine if Request is an Ajax call
     *
     * @return  void
     * @since   1.0
     */
    protected function isAjax()
    {
        $ajax = 0;

        if (empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        } else {
            if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $ajax = 1;
            }
        }

        $this->set('Ajax', $ajax);

        return;
    }

    /**
     * get client information using HTTP_USER_AGENT (very rough and not very reliable)
     *
     * - might be *somewhat* (maybe better than nothing) helpful for very high-level guess about desktop versus mobile
     *   in those cases where it's critical to handle the payload or interface differently on the server side
     *   if this disturbs you to even read this comment avoid using this data and nothing will rub off on you.
     *
     * @return  void
     * @since   1.0
     */
    protected function get_client()
    {
        $user_agent = '';

        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            $platform = 'unknown';
            $desktop = 0;
            $browser = 'unknown';
            $browser_version = 'unknown';

        } else {
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

            $this->set('platform', $platform);

            /** Desktop approximation */
            if ($platform == 'unknown') {
                $desktop = 0;
            } else {
                $desktop = 1;
            }

            $this->set('desktop', $desktop);

            /** Browser and Version Approximation */
            $browsers = array(
                'firefox',
                'msie',
                'opera',
                'chrome',
                'safari',
                'mozilla',
                'seamonkey',
                'konqueror',
                'netscape',
                'gecko',
                'navigator',
                'mosaic',
                'lynx',
                'amaya',
                'omniweb',
                'avant',
                'camino',
                'flock',
                'aol'
            );

            $browser = '';
            $browser_version = '';
            foreach ($browsers as $browser) {

                if (preg_match("#($browser)[/ ]?([0-9.]*)#", $user_agent, $match)) {
                    $browser = $match[1];
                    $browser_version = $match[2];
                    break;
                }
            }
        }

        $this->set('browser', $browser);

        $this->set('browser_version', $browser_version);

        $this->set('user_agent', $user_agent);

        return;
    }
}
