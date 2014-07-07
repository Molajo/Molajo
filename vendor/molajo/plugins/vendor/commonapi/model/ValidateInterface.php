<?php
/**
 * Validate Interface
 *
 * @package    Model
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\Model;

use CommonApi\Exception\UnexpectedValueException;
use CommonApi\Model\ValidateResponseInterface;

/**
 * Validate Interface
 *
 * @package    Model
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
interface ValidateInterface
{
    /**
     * Validate
     *
     * @param   string  $field_name
     * @param   mixed   $field_value
     * @param   string  $constraint
     * @param   array   $options
     *
     * @return  \CommonApi\Model\ValidateResponseInterface
     * @since   1.0.0
     * @throws  \CommonApi\Exception\UnexpectedValueException
     */
    public function validate($field_name, $field_value, $constraint, array $options = array());
}
