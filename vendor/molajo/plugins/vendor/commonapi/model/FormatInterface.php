<?php
/**
 * Format Interface
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\Model;

use CommonApi\Exception\UnexpectedValueException;
use CommonApi\Model\HandleResponseInterface;

/**
 * Format Interface
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
interface FormatInterface
{
    /**
     * Format
     *
     * @param   string  $field_name
     * @param   mixed   $field_value
     * @param   string  $constraint
     * @param   array   $options
     *
     * @return  \CommonApi\Model\HandleResponseInterface
     * @since   1.0.0
     * @throws  \CommonApi\Exception\UnexpectedValueException
     */
    public function format($field_name, $field_value, $constraint, array $options = array());
}
