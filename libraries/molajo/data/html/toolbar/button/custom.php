<?php
/**
 * @package    Molajo
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('MOLAJO') or die;

/**
 * Renders a custom button
 *
 * @package    Molajo
 * @subpackage  HTML
 * @since       1.0
 */
class MolajoButtonCustom extends MolajoButton
{
	/**
	 * Button type
	 *
	 * @var    string
	 */
	protected $_name = 'Custom';

	public function fetchButton($type='Custom', $html = '', $id = 'custom')
	{
		return $html;
	}

	/**
	 * Get the button CSS Id
	 *
	 * @return  string  Button CSS Id
	 * @since   1.0
	 */
	public function fetchId($type='Custom', $html = '', $id = 'custom')
	{
		return $this->_parent->getName().'-'.$id;
	}
}
