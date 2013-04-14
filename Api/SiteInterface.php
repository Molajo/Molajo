<?php
/**
 * Site Interface
 *
 * @package   Molajo
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Site\Api;

defined('MOLAJO') or die;

use Molajo\Site\Exception\SiteException;

/**
 * Site Interface
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 */
interface SiteInterface
{
    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0
     * @throws  SiteException
     */
    public function get($key, $default);

    /**
     * Set the value of the specified key
     *
     * @param   string $key
     * @param   mixed  $value
     *
     * @return  mixed
     * @since   1.0
     * @throws  SiteException
     */
    public function set($key, $value = null);

    /**
     * Populate BASE_URL using scheme, host, and base URL
     *
     * @return  void
     * @since   1.0
     */
    public function setBaseURL();

    /**
     * Identifies the specific site and sets site paths for use in the application
     *
     * @return  void
     * @since   1.0
     * @throws  SiteInterface
     */
    public function identifySite();

    /**
     * Custom set of defines for consistency in Application
     *
     * @return void
     * @since   1.0
     */
    public function setCustomDefines();

    /**
     * Determine if the site has already been installed
     *
     * return  boolean
     *
     * @since  1.0
     */
    public function installCheck();
}
