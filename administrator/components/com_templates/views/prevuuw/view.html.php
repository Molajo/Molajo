<?php
/**
 * @version		$Id: view.html.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML View class for the Templates component
 *
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @since		1.6
 */
class TemplatesViewPrevuuw extends JView
{
	protected $application;
	protected $id;
	protected $option;
	protected $template;
	protected $tp;
	protected $url;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{

		require_once JPATH_COMPONENT.'/helpers/templates.php';

		// Initialise some variables
		$this->application	= JApplicationHelper::getApplicationInfo(JRequest::getVar('application', '0', '', 'int'));
		$this->id		= JRequest::getVar('id', '', 'method', 'int');
		$this->option	= JRequest::getCmd('option');
		$this->template	= TemplatesHelper::getTemplateName($this->id);
		$this->tp		= true;
		$this->url		= $application->id ? JURI::base() : JURI::root();

		if (!$this->template) {
			return JError::raiseWarning(500, JText::_('COM_TEMPLATES_TEMPLATE_NOT_SPECIFIED'));
		}

		// Set FTP credentials, if given
		jimport('joomla.application.helper');
		JApplicationHelper::setCredentialsFromRequest('ftp');

		parent::display($tpl);
		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_TEMPLATES_MANAGER'), 'thememanager');
		JToolBarHelper::custom('edit', 'back.png', 'back_f2.png', 'Back', false, false);
	}
}
