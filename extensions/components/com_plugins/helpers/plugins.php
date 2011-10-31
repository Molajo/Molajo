<?php
/**
 * @version		$Id: plugins.php 21358 2011-05-14 16:49:50Z infograf768 $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Plugins component helper.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_plugins
 * * * @since		1.0
 */
class PluginsHelper
{
	public static $extension = 'com_plugins';

	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 */
	public static function addSubmenu($vName)
	{
		// No submenu for this component.
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 */
	public static function getActions()
	{
		$user		= MolajoFactory::getUser();
		$result		= new JObject;
		$assetName	= 'com_plugins';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return	string			The HTML code for the select tag
	 */
	public static function stateOptions()
	{
		// Build the active state filter options.
		$options	= array();
		$options[]	= MolajoHTML::_('select.option', '1', 'JENABLED');
		$options[]	= MolajoHTML::_('select.option', '0', 'JDISABLED');

		return $options;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return	string			The HTML code for the select tag
	 */
	public static function folderOptions()
	{
		$db		= MolajoFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('DISTINCT(folder) AS value, folder AS text');
		$query->from('#__extensions');
		$query->where('`type` = '.$db->quote('plugin'));
		$query->order('folder');

		$db->setQuery($query);
		$options = $db->loadObjectList();

		if ($error = $db->getErrorMsg()) {
			JError::raiseWarning(500, $error);
		}

		return $options;
	}
	function parseXMLTemplateFile($templateBaseDir, $templateDir)
	{
		$data = new JObject;

		// Check of the xml file exists
		$filePath = JPath::clean($templateBaseDir.'/templates/'.$templateDir.'/templateDetails.xml');
		if (is_file($filePath)) {
			$xml = JApplicationHelper::parseXMLInstallFile($filePath);

			if ($xml['type'] != 'template') {
				return false;
			}

			foreach ($xml as $key => $value) {
				$data->set($key, $value);
			}
		}

		return $data;
	}
}