<?php
/**
 * @version		$Id: modules.php 20196 2011-01-09 02:40:25Z ian $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * * * @since		1.0
 */
abstract class MolajoHTMLModules
{
	/**
	 * @param	int $application_id	The application id
	 * @param	string $state 	The state of the template
	 */
	static public function templates($application_id = 0, $state = '')
	{
		$templates = ModulesHelper::getTemplates($application_id, $state);
		foreach ($templates as $template) {
			$options[]	= MolajoHTML::_('select.option', $template->element, $template->name);
		}
		return $options;
	}
	/**
	 */
	static public function types()
	{
		$options = array();
		$options[] = MolajoHTML::_('select.option', 'user', 'COM_MODULES_OPTION_POSITION_USER_DEFINED');
		$options[] = MolajoHTML::_('select.option', 'template', 'COM_MODULES_OPTION_POSITION_TEMPLATE_DEFINED');
		return $options;
	}

	/**
	 */
	static public function templateStates()
	{
		$options = array();
		$options[] = MolajoHTML::_('select.option', '1', 'JENABLED');
		$options[] = MolajoHTML::_('select.option', '0', 'JDISABLED');
		return $options;
	}
}
