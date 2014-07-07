<?php
/**
 * Site Interface
 *
 * @package    Controller
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\Controller;

/**
 * Site Interface
 *
 * @package    Controller
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
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
     * @since   1.0.0
     */
    public function get($key = null, $default = null);

    /**
     * Identifies the specific site and sets site paths for use in the application
     *
     * @return  $this
     * @since   1.0.0
     */
    public function identifySite();

    /**
     * Define Site URL and Folder using scheme, host, and base URL
     *
     * @param   string $base_url
     * @param   string $base_path
     *
     * @return  $this
     * @since   1.0.0
     */
    public function setBase($base_url, $base_path);

    /**
     * Determine if the site has already been installed
     *
     * @param   $this
     *
     * @return  $this
     * @since   1.0.0
     */
    public function installCheck();
}
