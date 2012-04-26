<?php
/**
 * @package    Joomla.Platform
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla;

use Molajo\Service;

defined('JPATH_PLATFORM') or die;

/**
 * JText shell for aliasing to Molajo Language Service
 */
abstract class JText
{

	public static function _($string, $jsSafe = false, $interpretBackSlashes = true, $script = false)
	{
		return Service::Language()->translate($string, $jsSafe, $interpretBackSlashes, $script);
	}

	public static function sprintf($string)
	{
		return sprintf($string);
	}
}
