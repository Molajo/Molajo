<?php
/**
 * Error Thrown as Exception
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Foundation\Exception;

defined('MOLAJO') or die;

use RuntimeException;

use Molajo\Foundation\Api\ExceptionInterface;

/**
 * Error Thrown as Exception
 *
 * @author    Amy Stephen
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 */
class ErrorThrownAsException extends RuntimeException implements ExceptionInterface
{
}
