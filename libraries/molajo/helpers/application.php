<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * MolajoApplicationHelper
 *
 * @package     Molajo
 * @subpackage  Application Helper
 * @since       1.0
 */
class MolajoApplicationHelper
{
	/**
     * @var null $_applications
     * @since 1.0
     */
	protected static $_applications = null;

	/**
	 * getApplicationInfo
     *
     * Retrieves Application info from database
	 *
	 * This method will return a application information array if called
	 * with no arguments which can be used to add custom application information.
	 *
	 * @param   integer  $id		A application identifier
	 * @param   boolean  $byName	If True, find the application by its name
	 *
	 * @return  boolean  True if the information is added. False on error
	 * @since   1.0
	 */
	public static function getApplicationInfo ($id = null, $byName = false)
	{
		// Only create the array if it does not exist
		if (self::$_applications === null)
        {
            $obj = new stdClass();

            if ($id == 2 || $id == 'installation') {
			    $obj->id	= 2;
			    $obj->name	= 'installation';
			    $obj->path	=  MOLAJO_PATH_ROOT.'/'.'installation';
			    self::$_applications[2] = clone $obj;

            } else {

                $db = MolajoFactory::getDbo();
                $query = $db->getQuery(true);

                $query->select('application_id as id');
                $query->select('name');
                $query->select('path');
                $query->from($db->namequote('#__applications'));

                $db->setQuery($query->__toString());

                if ($results = $db->loadObjectList()) {
                } else {
                    MolajoFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
                    return false;
                }

                if ($db->getErrorNum()) {
                    return new MolajoException($db->getErrorMsg());
                }

                foreach ($results as $result) {
                    $obj->id	= $result->id;
                    $obj->name	= $result->name;
                    $obj->path	= MOLAJO_PATH_ROOT.'/'.$result->path;
                    self::$_applications[$result->id] = clone $obj;
                }
            }
        }

		// If no application id has been passed return the whole array
		if (is_null($id)) {
			return self::$_applications;
		}

		// Are we looking for application information by id or by name?
		if ($byName) {

			foreach (self::$_applications as $application) {
				if ($application->name == strtolower($id)) {
					return $application;
				}
			}

		} else {

			if (isset(self::$_applications[$id])){
				return self::$_applications[$id];
			}
		}

		return null;
	}

	/**
	 * addApplicationInfo
     *
     * Adds information for a application.
	 *
	 * @param   mixed  A application identifier either an array or object
	 *
	 * @return  boolean  True if the information is added. False on error
	 * @since   1.0
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
     * getPath
     *
     * @static
     * @param string $varname
     * @param string $user_option
     *
     * @return null|string The requested path
     *
	 * @since   1.0
     */
	public static function getPath($varname, $user_option=null)
	{

        /** amy remove **/
        return;

	}

	/**
     * parseXMLInstallFile
     *
	 * Parse a XML install manifest file.
	 *
	 * XML Root tag should be 'install' except for languages which use meta file.

     * @param string $path Full path to XML file.
     * @return array|bool XML metadata.
     *
	 * @since   1.0
     */
	public static function parseXMLInstallFile($path)
	{
		// Read the file to see if it's a valid component XML file
		if($xml = MolajoFactory::getXML($path)) {
        } else {
			return false;
		}

		// Check for a valid XML root tag.

		// Should be 'install', but for backward compatability we will accept 'extension'.
		// Languages use 'metafile' instead

		if($xml->getName() != 'install'
            && $xml->getName() != 'extension'
            && $xml->getName() != 'metafile') {
			unset($xml);
			return false;
		}

		$data = array();

		$data['legacy'] = ($xml->getName() == 'mosinstall' || $xml->getName() == 'install');

		$data['name'] = (string)$xml->name;

		// Check if we're a language. If so use metafile.
		$data['type'] = $xml->getName() == 'metafile' ? 'language' : (string)$xml->attributes()->type;

		$data['creationDate'] =((string)$xml->creationDate) ? (string)$xml->creationDate : MolajoText::_('Unknown');
		$data['author'] =((string)$xml->author) ? (string)$xml->author : MolajoText::_('Unknown');

		$data['copyright'] = (string)$xml->copyright;
		$data['authorEmail'] = (string)$xml->authorEmail;
		$data['authorUrl'] = (string)$xml->authorUrl;
		$data['version'] = (string)$xml->version;
		$data['description'] = (string)$xml->description;
		$data['group'] = (string)$xml->group;

		return $data;
	}

	/**
	 * parseXMLLangMetaFile
     *
     * Parse an XML language meta file.
	 *
	 * XML Root tag  for languages which is meta file.
	 *
	 * @param   string   $path Full path to XML file.
	 *
	 * @return  array    XML metadata.
     *
	 * @since   1.0
     */
	public static function parseXMLLangMetaFile($path)
	{
		// Read the file to see if it's a valid component XML file
		$xml = MolajoFactory::getXML($path);

		if($xml) {
        } else {
			return false;
		}

		/*
		 * Check for a valid XML root tag.
		 *
		 * Should be 'langMetaData'.
		 */
		if ($xml->getName() == 'metafile') {
        } else {
			unset($xml);
			return false;
		}

		$data = array();

        /** Page Session Variables */

        $data = array();

		$data['name'] = (string)$xml->name;
		$data['type'] = $xml->attributes()->type;

		$data['creationDate'] =((string)$xml->creationDate) ? (string)$xml->creationDate : MolajoText::_('MOLAJO_UNKNOWN');
		$data['author'] =((string)$xml->author) ? (string)$xml->author : MolajoText::_('MOLAJO_UNKNOWN');

		$data['copyright'] = (string)$xml->copyright;
		$data['authorEmail'] = (string)$xml->authorEmail;
		$data['authorUrl'] = (string)$xml->authorUrl;
		$data['version'] = (string)$xml->version;
		$data['description'] = (string)$xml->description;
		$data['group'] = (string)$xml->group;

		return $data;
	}

	/**
	 * _checkPath
     *
     * Tries to find a file in the administrator or site areas
	 *
	 * @param   string   A file name
	 * @param   integer  0 to check site only, 1 to check site and admin, -1 to check admin only
	 *
	 * @return  string   File name or null
	 * @since   1.0
	 */
	protected static function _checkPath($path, $checkAdmin=1)
	{
		$file = MOLAJO_BASE_PATH.$path;
		if (file_exists($file)) {
			return $file;
		}

		$file = MOLAJO_PATH_SITE.$path;
		if ($checkAdmin > -1 && file_exists($file)) {
			return $file;

		} else if ($checkAdmin != 0) {
			$file = MOLAJO_PATH_ADMINISTRATOR.$path;
			if (file_exists($file)) {
				return $file;
			}
		}

		return null;
	}
}