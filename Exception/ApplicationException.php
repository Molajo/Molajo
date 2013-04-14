<?php
/**
 * Application Exception
 *
 * @package   Molajo
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Application\Exception;

defined('MOLAJO') or die;

use Exception;

use Molajo\Application\Api\ExceptionInterface;

/**
 * Application Exception
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 */
class ApplicationException extends Exception implements ExceptionInterface
{

}
