<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Exception Handler
 *
 * @package     Molajo
 * @subpackage  Application
 * @since       1.0
 */
class Exceptions extends \Exception
{
    /**
     * Application::Services
     *
     * @var    Object Services
     * @since  1.0
     */
    protected static $services = null;

    public function __construct($message ='', $code = 0, \Exception $previous = null)
    {
        // some code

        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    // custom string representation of object
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function customFunction()
    {
        echo "A custom function for this type of exception\n";
    }
}
