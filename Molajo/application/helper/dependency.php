<?php
/**
 * @package     Molajo
 * @subpackage  Helpers
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Application\Helper;

defined('MOLAJO') or die;

/**
 * Dependencies
 *
 * Used to mockup classes and methods for unneeded Joomla dependencies
 *
 * @package     Molajo
 * @subpackage  Helpers
 * @since       1.0
 */
abstract class DependencyHelper
{
    public static function add()
    {
    }
}

abstract class JText
{

    public static function _($string, $jsSafe = false, $interpretBackSlashes = true, $script = false)
    {
        return Services::Language()->translate($string, $jsSafe, $interpretBackSlashes, $script);
    }

    public static function sprintf($string)
    {
        sprintf($string);
    }
}

abstract class JLog extends DependencyHelper
{
    /**
     * Used by JDatabase for deprecation warnings
     *  JLog::WARNING
     *  JLog::add
     */

    const WARNING = 16;
}
// JError::isError($error)
// JError::raiseWarning

// if (JError::$legacy)
// if (JError::$legacy)

