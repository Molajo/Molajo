<?php
/**
 * FrontController Exception
 *
 * @package   Molajo
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Foundation\Exception;

defined('MOLAJO') or die;

use RuntimeException;

use Molajo\Foundation\Api\ExceptionInterface;

/**
 * FrontController Exception
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 */
class FrontControllerException extends RuntimeException implements ExceptionInterface
{
}
