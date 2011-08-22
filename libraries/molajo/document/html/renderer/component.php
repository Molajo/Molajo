<?php
/**
 * @package    Molajo
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('MOLAJO') or die;

/**
 * Component renderer
 *
 * @package    Molajo
 * @subpackage  Document
 * @since       1.0
 */
class MolajoDocumentRendererComponent extends MolajoDocumentRenderer
{
	/**
	 * Renders a component script and returns the results as a string
	 *
	 * @param   string  $component  The name of the component to render
	 * @param   array   $params     Associative array of values
	 * @param   string  $content    Content script
	 *
	 * @return  string  The output of the script
	 *
	 * @since   11.1
	 */
	public function render($component = null, $params = array(), $content = null)
	{
		return $content;
	}
}
