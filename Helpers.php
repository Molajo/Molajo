<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo;
use Molajo\Application;

defined('MOLAJO') or die;

/**
 * Helpers
 *
 * @package     Molajo
 * @subpackage  Helpers
 * @since       1.0
 */
Class Helpers
{

    /**
     * Used to connect to helpers
     *
     * @static
     * @param   string  $name
     * @param   string  $arguments
     *
     * @return  mixed
     * @since   1.0
     */
    public static function __callStatic($name, $arguments)
    {
        $app = new Application();
        return $app->Helpers()->get($name . 'Helper');
    }

    /**
     * Retrieves Helper
     *
     * @param   string $key
     *
     * @return  mixed
     * @since   1.0
     */
    protected function get($key)
    {
        return $this->getClassInstance('Molajo\\Helper\\' . $key);
    }

    /**
     * Get Class Instance
     *
     * @param   string  $entry
     * @param   $folder $entry
     *
     * @return  mixed
     * @since   1.0
     */
    private function getClassInstance($helperClass)
    {
        if (class_exists($helperClass)) {
        } else {
            $connectionSucceeded = false;
            $connection = $helperClass . ' Class does not exist';
            //throw error
        }

        return new $helperClass();
    }
}
