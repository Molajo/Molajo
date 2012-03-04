<?php
/**
 * @package     Molajo
 * @subpackage  Includer
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * MolajoTagIncluder
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
Class TagIncluder extends Includer
{
	/**
	 * Renders multiple modules script and returns the results as a string
	 *
	 * @param   string  $position  The position of the modules to render
	 * @param   array   $params    Associative array of values
	 * @param   string  $content   Module content
	 *
	 * @return  string  The output of the script
	 *
	 * @since   11.1
	 */
	public function render($position, $params = array(), $content = null)
	{
		$renderer = $this->_doc->loadIncluder('module');
		$buffer = '';

		foreach (JModuleHelper::getModules($position) as $mod)
		{
			$buffer .= $renderer->render($mod, $params, $content);
		}
		return $buffer;
	}
}
