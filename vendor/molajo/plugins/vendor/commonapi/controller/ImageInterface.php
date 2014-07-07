<?php
/**
 * Image Interface
 *
 * @package    Controller
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\Controller;

/**
 * Image Interface
 *
 * @package    Controller
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
interface ImageInterface
{
    /**
     * Retrieve and optionally resize requested image
     *
     * @param   string      $filename
     * @param   null|string $type
     * @param   null|string $size
     *
     * @return  string
     * @since   1.0.0
     */
    public function getImage($filename, $type = null, $size = null);

    /**
     * Get Placeholder Image
     *
     * @param   string $size
     * @param   string $color
     *
     * @return  object
     * @since   1.0.0
     */
    public function getImagePlaceholder($size, $color);
}
