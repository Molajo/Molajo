<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service;

use Molajo\Service;

defined('MOLAJO') or die;

/**
 * Filesystem
 *
 * API for File, Folder, and Path Operations
 *
 * Usage:
 *
 * File
 * Service::Filesystem()->fileExists
 * Service::Filesystem()->fileName
 * Service::Filesystem()->fileRead
 * Service::Filesystem()->fileWrite
 * Service::Filesystem()->fileDelete
 * Service::Filesystem()->fileCopy
 * Service::Filesystem()->fileMove
 * Service::Filesystem()->fileExtension
 * Service::Filesystem()->fileNameNoExtension
 * Service::Filesystem()->fileUpload
 *
 * Folder
 * Service::Filesystem()->folderExists
 * Service::Filesystem()->folderName
 * Service::Filesystem()->folderCreate
 * Service::Filesystem()->folderDelete
 * Service::Filesystem()->folderCopy
 * Service::Filesystem()->folderMove
 * Service::Filesystem()->folderFiles
 * Service::Filesystem()->folderFolders
 * Service::Filesystem()->folderlistFolderTree
 *
 * Path
 * Service::Filesystem()->pathSetPermissions
 * Service::Filesystem()->pathGetPermissions
 * Service::Filesystem()->pathCheck
 * Service::Filesystem()->pathClean
 * Service::Filesystem()->pathIsOwner
 * Service::Filesystem()->pathFind
 *
 * @package   Molajo
 * @subpackage  Services
 * @since       1.0
 */
class FilesystemService
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * getInstance
	 *
	 * @static
	 * @return bool|object
	 * @since  1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new FilesystemService();
		}
		return self::$instance;
	}

	/**
	 * Magic method __call intercepts calls and act as a proxy to
	 * Joomla JFile, JFolder, and JPath Classes
	 *
	 * @param $name
	 * @param $arguments
	 *
	 * @return mixed
	 * @since 1.0
	 */
	public function __call($name, $arguments)
	{
		return $this->processCall($name, $arguments);
	}

	/**
	 * Magic method __callStatic intercepts calls and act as a proxy to
	 * Joomla JFile, JFolder, and JPath Classes
	 *
	 * @static
	 * @param $name
	 * @param $arguments
	 *
	 * @return mixed
	 * @since 1.0
	 */
	public static function __callStatic($name, $arguments)
	{
		return Service::Filesystem()->processCall($name, $arguments);
	}

	/**
	 * proxy to JFile, JFolder, and JPath Classes
	 *
	 * @param $name
	 * @param $arguments
	 * @return bool|mixed
	 */
	public function processCall($name, $arguments)
	{
		if (strtolower(substr($name, 0, 4)) == 'file') {
			$class = 'Joomla\\filesystem\\JFile';
			$method = substr($name, 4, strlen($name) - 4);

		}
		elseif (strtolower(substr($name, 0, 6)) == 'folder') {
			$class = 'Joomla\\filesystem\\JFolder';
			$method = substr($name, 6, strlen($name) - 6);

		}
		elseif (strtolower(substr($name, 0, 4)) == 'path') {
			$class = 'Joomla\\filesystem\\JPath';
			$method = substr($name, 4, strlen($name) - 4);

		} else {
			Service::Debug()->set('Invalid Filesystem Class: ' . $name);
			return false;
		}

		$method = strtolower($method);
		if (method_exists($class, $method)) {
			return call_user_func_array(array($class, $method), $arguments);
		}

		Service::Debug()->set('Invalid Filesystem Method: ' . $name);

		return false;
	}
}
