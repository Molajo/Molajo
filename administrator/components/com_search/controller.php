<?php
/**
 * @version		$Id: controller.php 21320 2011-05-11 01:01:37Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Search master display controller.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_search
 * * * @since		1.0
 */
class SearchController extends JController
{
	/**
	 * @var		string	The default view.
	 * @since	1.0
	 */
	protected $DefaultView = 'searches';

	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/search.php';

		// Load the submenu.
		SearchHelper::addSubmenu(JRequest::getCmd('view', 'searches'));

		parent::display();
	}
}