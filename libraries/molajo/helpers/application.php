<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('MOLAJO_LIBRARY') or die;

/**
 * Application helper functions
 *
 * @package     Joomla.Platform
 * @subpackage  Application
 * @since       11.1
 */
class MolajoApplicationHelper
{
	/**
	 * Application information array
	 */
	protected static $_applications = null;

	/**
	 * Return the name of the request component [main component]
	 *
	 * @param   string  $default The default option
	 * @return  string  Option
	 * @since   11.1
	 */
	public static function getComponentName($default = NULL)
	{
		static $option;

		if ($option) {
			return $option;
		}

		$option = strtolower(JRequest::getCmd('option'));

		if (empty($option)) {
			$option = $default;
		}

		JRequest::setVar('option', $option);
		return $option;
	}

	/**
	 * Gets information on a specific application id.  This method will be useful in
	 * future versions when we start mapping applications in the database.
	 *
	 * This method will return a application information array if called
	 * with no arguments which can be used to add custom application information.
	 *
	 * @param   integer  $id		A application identifier
	 * @param   boolean  $byName	If True, find the application by its name
	 *
	 * @return  mixed  Object describing the application or false if not known
	 * @since   11.1
	 */
	public static function getApplicationInfo($id = null, $byName = false)
	{
		// Only create the array if it does not exist
		if (self::$_applications === null)
		{
			$obj = new stdClass();

			// Site Application
			$obj->id	= 0;
			$obj->name	= 'site';
			$obj->path	= MOLAJO_PATH_SITE;
			self::$_applications[0] = clone $obj;

			// Administrator Application
			$obj->id	= 1;
			$obj->name	= 'administrator';
			$obj->path	= MOLAJO_PATH_ADMINISTRATOR;
			self::$_applications[1] = clone $obj;

			// Installation Application
			$obj->id	= 2;
			$obj->name	= 'installation';
			$obj->path	= MOLAJO_PATH_INSTALLATION;
			self::$_applications[2] = clone $obj;
		}

		// If no application id has been passed return the whole array
		if (is_null($id)) {
			return self::$_applications;
		}

		// Are we looking for application information by id or by name?
		if (!$byName)
		{
			if (isset(self::$_applications[$id])){
				return self::$_applications[$id];
			}
		}
		else
		{
			foreach (self::$_applications as $application)
			{
				if ($application->name == strtolower($id)) {
					return $application;
				}
			}
		}

		return null;
	}

	/**
	 * Retrieves Application info from database
	 *
	 * This method will return a application information array if called
	 * with no arguments which can be used to add custom application information.
	 *
	 * @param   integer  $id		A application identifier
	 * @param   boolean  $byName	If True, find the application by its name
	 *
	 * @return  boolean  True if the information is added. False on error
	 * @since   11.1
	 */
	public static function getApplicationInfoDB ($id = null, $byName = false)
	{
        // if even this single next statement is run
        $db = JFactory::getDbo();
        // Warning: mysqli::ping() [mysqli.ping]: Couldn't fetch mysqli in /Users/amystephen/Sites/molajo/libraries/joomla/database/database/mysqli.php on line 188
        $query = $db->getQuery(true);

        /** validation query **/
        $query->select('application_id as id');
        $query->select('name');
        $query->select('path');
        $query->from($db->namequote('#__applications'));

        if ($byName === true) {
            $query->where('name = '.$db->quote(trim($id)));
        } else {
            $query->where('id = '. (int) $id);
        }

        $db->setQuery($query->__toString());

        if ($results = $db->loadObjectList()) {
        } else {
            JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
        }

        if ($db->getErrorNum()) {
            return new JException($db->getErrorMsg());
        }
    }

	/**
	 * Adds information for a application.
	 *
	 * @param   mixed  A application identifier either an array or object
	 *
	 * @return  boolean  True if the information is added. False on error
	 * @since   11.1
	 */
	public static function addApplicationInfo($application)
	{
		if (is_array($application)) {
			$application = (object) $application;
		}

		if (!is_object($application)) {
			return false;
		}

		$info = self::getApplicationInfo();

		if (!isset($application->id)) {
			$application->id = count($info);
		}

		self::$_applications[$application->id] = clone $application;

		return true;
	}

	/**
	* Get a path
	*
	* @param   string  $varname
	* @param   string  $user_option
	*
	* @return  string  The requested path
	* @since   11.1
	*/
	public static function getPath($varname, $user_option=null)
	{
		// Check needed for handling of custom/new module XML file loading
		$check = (($varname == 'mod0_xml') || ($varname == 'mod1_xml'));

		if (!$user_option && !$check) {
			$user_option = JRequest::getCmd('option');
		} else {
			$user_option = JFilterInput::getInstance()->clean($user_option, 'path');
		}

		$result = null;
		$name	= substr($user_option, 4);

		switch ($varname) {
			case 'front':
				$result = self::_checkPath(DS.'components'.DS. $user_option .DS. $name .'.php', 0);
				break;

			case 'html':
			case 'front_html':
				if (!($result = self::_checkPath(DS.'templates'.DS. MolajoApplication::getTemplate() .DS.'components'.DS. $name .'.html.php', 0))) {
					$result = self::_checkPath(DS.'components'.DS. $user_option .DS. $name .'.html.php', 0);
				}
				break;

			case 'toolbar':
				$result = self::_checkPath(DS.'components'.DS. $user_option .DS.'toolbar.'. $name .'.php', -1);
				break;

			case 'toolbar_html':
				$result = self::_checkPath(DS.'components'.DS. $user_option .DS.'toolbar.'. $name .'.html.php', -1);
				break;

			case 'toolbar_default':
			case 'toolbar_front':
				$result = self::_checkPath(DS.'includes'.DS.'HTML_toolbar.php', 0);
				break;

			case 'admin':
				$path	= DS.'components'.DS. $user_option .DS.'admin.'. $name .'.php';
				$result = self::_checkPath($path, -1);
				if ($result == null) {
					$path = DS.'components'.DS. $user_option .DS. $name .'.php';
					$result = self::_checkPath($path, -1);
				}
				break;

			case 'admin_html':
				$path	= DS.'components'.DS. $user_option .DS.'admin.'. $name .'.html.php';
				$result = self::_checkPath($path, -1);
				break;

			case 'admin_functions':
				$path	= DS.'components'.DS. $user_option .DS. $name .'.functions.php';
				$result = self::_checkPath($path, -1);
				break;

			case 'class':
				if (!($result = self::_checkPath(DS.'components'.DS. $user_option .DS. $name .'.class.php'))) {
					$result = self::_checkPath(DS.'includes'.DS. $name .'.php');
				}
				break;

			case 'helper':
				$path	= DS.'components'.DS. $user_option .DS. $name .'.helper.php';
				$result = self::_checkPath($path);
				break;

			case 'com_xml':
				$path	= DS.'components'.DS. $user_option .DS. $name .'.xml';
				$result = self::_checkPath($path, 1);
				break;

			case 'mod0_xml':
				$path = DS.'modules'.DS. $user_option .DS. $user_option. '.xml';
				$result = self::_checkPath($path);
				break;

			case 'mod1_xml':
				// Admin modules
				$path = DS.'modules'.DS. $user_option .DS. $user_option. '.xml';
				$result = self::_checkPath($path, -1);
				break;

			case 'plg_xml':
				// Site plugins
				$j15path	= DS.'plugins'.DS. $user_option .'.xml';
				$parts = explode(DS, $user_option);
				$j16path = DS.'plugins'.DS. $user_option.DS.$parts[1].'.xml';
				$j15 = self::_checkPath($j15path, 0);
				$j16 = self::_checkPath( $j16path, 0);
				// Return 1.6 if working otherwise default to whatever 1.5 gives us
				$result = $j16 ? $j16 : $j15;
				break;

			case 'menu_xml':
				$path	= DS.'components'.DS.'com_menus'.DS. $user_option .DS. $user_option .'.xml';
				$result = self::_checkPath($path, -1);
				break;
		}

		return $result;
	}

	/**
	 * Parse a XML install manifest file.
	 *
	 * XML Root tag should be 'install' except for languages which use meta file.
	 *
	 * @param   string  $path Full path to XML file.
	 *
	 * @return  array  XML metadata.
	 */
	public static function parseXMLInstallFile($path)
	{
		// Read the file to see if it's a valid component XML file
		if( ! $xml = JFactory::getXML($path))
		{
			return false;
		}

		// Check for a valid XML root tag.

		// Should be 'install', but for backward compatability we will accept 'extension'.
		// Languages use 'metafile' instead

		if($xml->getName() != 'install'
		&& $xml->getName() != 'extension'
		&& $xml->getName() != 'metafile')
		{
			unset($xml);
			return false;
		}

		$data = array();

		$data['legacy'] = ($xml->getName() == 'mosinstall' || $xml->getName() == 'install');

		$data['name'] = (string)$xml->name;

		// Check if we're a language. If so use metafile.
		$data['type'] = $xml->getName() == 'metafile' ? 'language' : (string)$xml->attributes()->type;

		$data['creationDate'] =((string)$xml->creationDate) ? (string)$xml->creationDate : JText::_('Unknown');
		$data['author'] =((string)$xml->author) ? (string)$xml->author : JText::_('Unknown');

		$data['copyright'] = (string)$xml->copyright;
		$data['authorEmail'] = (string)$xml->authorEmail;
		$data['authorUrl'] = (string)$xml->authorUrl;
		$data['version'] = (string)$xml->version;
		$data['description'] = (string)$xml->description;
		$data['group'] = (string)$xml->group;

		return $data;
	}

	/**
	 * Parse a XML language meta file.
	 *
	 * XML Root tag  for languages which is meta file.
	 *
	 * @param   string   $path Full path to XML file.
	 *
	 * @return  array    XML metadata.
	 */
	public static function parseXMLLangMetaFile($path)
	{
		// Read the file to see if it's a valid component XML file
		$xml = JFactory::getXML($path);

		if( ! $xml)
		{
			return false;
		}

		/*
		 * Check for a valid XML root tag.
		 *
		 * Should be 'langMetaData'.
		 */
		if ($xml->getName() != 'metafile') {
			unset($xml);
			return false;
		}

		$data = array();

		$data['name'] = (string)$xml->name;
		$data['type'] = $xml->attributes()->type;

		$data['creationDate'] =((string)$xml->creationDate) ? (string)$xml->creationDate : JText::_('JLIB_UNKNOWN');
		$data['author'] =((string)$xml->author) ? (string)$xml->author : JText::_('JLIB_UNKNOWN');

		$data['copyright'] = (string)$xml->copyright;
		$data['authorEmail'] = (string)$xml->authorEmail;
		$data['authorUrl'] = (string)$xml->authorUrl;
		$data['version'] = (string)$xml->version;
		$data['description'] = (string)$xml->description;
		$data['group'] = (string)$xml->group;

		return $data;
	}

	/**
	 * Tries to find a file in the administrator or site areas
	 *
	 * @param   string   A file name
	 * @param   integer  0 to check site only, 1 to check site and admin, -1 to check admin only
	 *
	 * @return  string   File name or null
	 * @since   11.1
	 */
	protected static function _checkPath($path, $checkAdmin=1)
	{
		$file = MOLAJO_PATH_SITE . $path;
		if ($checkAdmin > -1 && file_exists($file)) {
			return $file;
		}
		else if ($checkAdmin != 0)
		{
			$file = MOLAJO_PATH_ADMINISTRATOR . $path;
			if (file_exists($file)) {
				return $file;
			}
		}

		return null;
	}
}