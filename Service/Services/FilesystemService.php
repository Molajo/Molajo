<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Filesystem
 *
 * API for File, Folder, and Path Operations
 *
 * Usage:
 *
 * File
 * Services::Filesystem()->fileExists
 * Services::Filesystem()->fileName
 * Services::Filesystem()->fileRead
 * Services::Filesystem()->fileWrite
 * Services::Filesystem()->fileDelete
 * Services::Filesystem()->fileCopy
 * Services::Filesystem()->fileMove
 * Services::Filesystem()->fileExtension
 * Services::Filesystem()->fileNameNoExtension
 * Services::Filesystem()->fileUpload
 *
 * Folder
 * Services::Filesystem()->folderExists
 * Services::Filesystem()->folderName
 * Services::Filesystem()->folderCreate
 * Services::Filesystem()->folderDelete
 * Services::Filesystem()->folderCopy
 * Services::Filesystem()->folderMove
 * Services::Filesystem()->folderFiles
 * Services::Filesystem()->folderFolders
 * Services::Filesystem()->folderlistFolderTree
 *
 * Path
 * Services::Filesystem()->pathSetPermissions
 * Services::Filesystem()->pathGetPermissions
 * Services::Filesystem()->pathCheck
 * Services::Filesystem()->pathClean
 * Services::Filesystem()->pathIsOwner
 * Services::Filesystem()->pathFind
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
		return Services::Filesystem()->processCall($name, $arguments);
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
			Services::Debug()->set('Invalid Filesystem Class: ' . $name);
			return false;
		}

		$method = strtolower($method);
		if (method_exists($class, $method)) {
			return call_user_func_array(array($class, $method), $arguments);
		}

		Services::Debug()->set('Invalid Filesystem Method: ' . $name);

		return false;
	}
}
