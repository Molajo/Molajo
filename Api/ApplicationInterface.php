<?php
/**
 * Application Interface
 *
 * @package   Molajo
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Application\Api;

defined('MOLAJO') or die;

use Molajo\Application\Exception\ApplicationException;

/**
 * Application Interface
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 * @api
 */
interface ApplicationInterface
{
    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string $key
     * @param   null   $default
     *
     * @return  mixed
     * @since   1.0
     * @throws  ApplicationException
     */
    public function get($key, $default = null);

    /**
     * Set the value of the specified key
     *
     * Parameters are set in the Application file or by updating the entire $parameters array
     *  and passing it back in - as a full array
     *
     * @param   string $key
     * @param   mixed  $value
     *
     * @return  mixed
     * @since   1.0
     * @throws  ApplicationException
     */
    public function set($key, $value);

    /**
     * Using Request URI, identify current application and page request
     *
     * @return  $this
     * @since   1.0
     */
    public function setApplication();

    /**
     * Append Application Node to Scheme + Base URL for use creating URLs for the Application
     *
     * @return  $this
     * @since   1.0
     */
    public function setBaseUrlPath();

    /**
     * Determine if the Application must use SSL, according to Application Data
     * If so, determine if SSL is already in use
     * If not, redirect using HTTPS
     *
     * @return  bool|string
     * @since   1.0
     */
    public function sslCheck();

    /**
     * Retrieve Application Data
     *
     * @return  $this
     * @since   1.0
     * @throws  ApplicationException
     */
    public function getApplication();

    /**
     * Establish Site paths for media, cache, log, etc., locations as configured for this Application
     *
     * @return  $this
     * @since   1.0
     */
    public function setApplicationSitePaths();
}
