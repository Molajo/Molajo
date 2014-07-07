<?php
/**
 * Random String Interface
 *
 * @package    Controller
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\Controller;

/**
 * Random String Interface
 *
 * @package    Controller
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
interface RandomStringInterface
{
    /**
     * Generate a Random String of an optional specified length
     *
     * @param   null|int     $length
     * @param   null|string  $characters
     *
     * @return  string
     * @since   1.0.0
     */
    public function generateString($length = null, $characters = null);
}
