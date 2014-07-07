<?php
/**
 * Url Interface
 *
 * @package    Controller
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\Controller;

/**
 * Url Interface
 *
 * @package    Controller
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
interface UrlInterface
{
    /**
     * Retrieves Catalog ID for the specified Catalog Type ID and Source ID
     *
     * @param   int      $request_type
     * @param   null|int $catalog_type_id
     * @param   null|int $source_id
     * @param   null|int $url_sef_request
     *
     * @return  mixed
     * @since   1.0.0
     */
    public function get($request_type = 1, $catalog_type_id = null, $source_id = null, $url_sef_request = null);

    /**
     * Add a Trailing Slash
     *
     * @param   string $url
     *
     * @return  string
     * @since   1.0.0
     */
    public function addTrailingSlash($url);

    /**
     * Remove the Trailing Slash
     *
     * @param   string $url
     *
     * @return  string
     * @since   1.0.0
     */
    public function removeTrailingSlash($url);

    /**
     * Add Site URL and application path to URL path
     *
     * @param   string $path
     *
     * @return  string
     * @since   1.0.0
     */
    public function getApplicationURL($path = '');

    /**
     * checkURLExternal - determines if it is a local site or external link
     *
     * @param   string $url
     *
     * @return  boolean
     * @since   1.0.0
     */
    public function checkURLExternal($url);

    /**
     * urlShortener
     *
     * @param  string $url
     * @param  int    $type
     *
     * 1 TinyURL
     * 2 is.gd
     * 3 Local
     *
     * @return  string
     * @since   1.0.0
     */
    public function urlShortener($url, $type = 2);
}
