<?php
/**
 * Number to Text Interface
 *
 * @package    CommonApi
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\Controller;

/**
 * Number to Text Interface
 *
 * @package     CommonApi
 * @subpackage  Utilities
 * @since       0.1
 */
interface NumberToTextInterface
{
    /**
     * Converts a numeric value, with or without a decimal, up to a 999 quadrillion into words
     *
     * @param   string $number
     *
     * @return  string
     * @since   1.0.0
     */
    public function convert($number);
}
