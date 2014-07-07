<?php
/**
 * FieldhandlerInterface Interface
 *
 * @package    Model
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\Model;

use CommonApi\Exception\UnexpectedValueException;
use CommonApi\Model\ValidateResponseInterface;

/**
 * FieldhandlerInterface Interface
 *
 * @package    Model
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
interface FieldhandlerInterface extends ValidateInterface, SanitizeInterface, FormatInterface
{

}
