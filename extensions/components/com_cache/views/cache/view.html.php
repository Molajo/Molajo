<?php
/**
 * @version		$Id: view.html.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @package		Joomla.Administrator
 * @subpackage	com_cache
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML View class for the Cache component
 *
 * @static
 * @package		Joomla.Administrator
 * @subpackage	com_cache
 * @since 1.6
 */
class CacheViewCache extends JView
{
	protected $application;
	protected $data;
	protected $pagination;
	protected $state;

	public function display($tpl = null)
	{
		$this->data			= $this->get('Data');
		$this->application	= $this->get('Application');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			MolajoError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.0
	 */
	protected function addToolbar()
	{
		$user = MolajoFactory::getUser();
		$condition = ($this->application->name == 'site');

		MolajoToolbarHelper::title(MolajoText::_('COM_CACHE_CLEAR_CACHE'), 'clear.png');
		MolajoToolbarHelper::custom('delete', 'delete.png', 'delete_f2.png', 'JTOOLBAR_DELETE', true);
		MolajoToolbarHelper::divider();
		if (MolajoFactory::getUser()->authorise('core.admin', 'com_cache')) {
			MolajoToolbarHelper::preferences('com_cache');
		}
		MolajoToolbarHelper::divider();
		MolajoToolbarHelper::help('JHELP_SITE_MAINTENANCE_CLEAR_CACHE');
	}
}
