<?php
/**
 * @version		$Id: view.html.php 20196 2011-01-09 02:40:25Z ian $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

include_once dirname(__FILE__) . '/../default/view.php';

/**
 * Extension Manager Templates View
 *
 * @package		Joomla.Administrator
 * @subpackage	installer
 * * * @since		1.0
 */
class InstallerViewWarnings extends InstallerViewDefault
{
	/**
	 * @since	1.0
	 */
	function display($tpl=null)
	{
		$items		= $this->get('Items');
		$this->assignRef('messages', $items);
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.0
	 */
	protected function addToolbar()
	{
		parent::addToolbar();
		MolajoToolbarHelper::help('JHELP_EXTENSIONS_EXTENSION_MANAGER_WARNINGS');
	}
}
