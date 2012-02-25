<?php
/**
 * @package     Molajo
 * @subpackage  Helpers
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Dependencies
 *
 * Used to mockup classes and methods for unneeded Joomla dependencies
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
abstract class MolajoDependenciesHelper
{
    /**
     * add
     *
     * Retrieve a list of classes and methods
     *
     * @results  object
     * @since    1.0
     */
    public static function add () {}
}

abstract class JLog extends MolajoDependenciesHelper
{
    /**
     * Used by JDatabase for deprecation warnings
     *  JLog::WARNING
     *  JLog::add
     */

    /**
   	 * Warning conditions.
   	 * @var    integer
   	 * @since  11.1
   	 */
   	const WARNING = 16;
}
// JError::isError($error)
// JError::raiseWarning
// JText::sprintf(
// JText::_('JLIB_CL')
// if (JError::$legacy)
// if (JError::$legacy)
