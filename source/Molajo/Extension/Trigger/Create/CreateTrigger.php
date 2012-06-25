<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Create;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Create
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class CreateTrigger extends ContentTrigger
{

	/**
	 * Post-create processing for new extension
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterCreate()
	{
		if ($this->query_results->catalog_type_id >= CATALOG_TYPE_EXTENSION_BEGIN
		AND $this->query_results->catalog_type_id <= CATALOG_TYPE_EXTENSION_END
		) {
		} else {
		return true;
		}


		//if ($this->parameters->create_extension == 1) {

		$results = $this->createExtension();
		if ($results === false) {
			return false;
		}
		/** Sample Data */
		//if ($this->parameters->create_sample_data == 1) {
		$results = $this->createSampleContent();
		if ($results === false) {
			return false;
		}
		//}
		//}

		return true;
	}

	/**
	 * Create extension folders and files using samples
	 *
	 * @return bool
	 * @since  1.0
	 */
	protected function createExtension()
	{
		/**
		$id = $this->query_results->id;
		$extension_name = ucfirst(strtolower($this->query_results->title));
		$catalog_type = Helpers::Extension()->getType($this->query_results->catalog_type_id);
		 */
		$sourceFolder = 'Samples';

		$id = 5;
		$extension_name = 'Test';
		$catalog_type = 'Component';

		/** Determine Source Folder for files to copy */
		$sourceFolder = $this->getSourceFolder($catalog_type, $sourceFolder);
		if ($sourceFolder === false) {
			return false;
		}

		/** Determine Destination Folder for target location */
		$destinationFolder = $this->getDestinationFolder($catalog_type, $extension_name);
		if ($destinationFolder === false) {
			return false;
		}

		/** Copy Source to Destination */
		$results = $this->copy($sourceFolder, $destinationFolder);
		if ($results === false) {
			return false;
		}

		/** Traverse Folders */
		$replace = 'Samples';
		$with = ucfirst(strtolower($extension_name));

		$results = $this->traverseFolders($destinationFolder, $replace, $with);
		if ($results === false) {
			return false;
		}

		return true;
	}

	/**
	 * determine source path
	 *
	 * @param $source
	 *
	 * @return mixed
	 * @since  1.0
	 */
	protected function getSourceFolder($catalog_type, $sourceFolder)
	{
		if (Services::Filesystem()->folderExists(dirname(__FILE__) . '/' . $catalog_type . '/' . $sourceFolder)) {
			return dirname(__FILE__) . '/' . $catalog_type . '/' . $sourceFolder;
		}

		//error - no source folder available to copy
		return false;
	}

	/**
	 * Get destination folder
	 *
	 * @param $catalog_type
	 * @param $extension_name
	 *
	 * @return mixed
	 * @since  1.0
	 */
	protected function getDestinationFolder($catalog_type, $extension_name)
	{
		if (Services::Filesystem()->folderExists(EXTENSIONS . '/' . $catalog_type)) {
			if (Services::Filesystem()->folderExists(EXTENSIONS . '/' . $catalog_type . '/' . $extension_name)) {
				// error extension already exists
				return false;
			} else {
				return EXTENSIONS . '/' . $catalog_type . '/' . $extension_name;
			}
		} elseif (Services::Filesystem()->folderExists(EXTENSIONS . '/' . 'Views' . '/' . $catalog_type)) {
			if (Services::Filesystem()->folderExists(EXTENSIONS . '/' . 'Views' . '/' . $catalog_type . '/' . $extension_name)) {
				// error extension already exists
				return false;
			} else {
				return EXTENSIONS . '/' . 'Views' . '/' . $catalog_type . '/' . $extension_name;
			}
		}
	}

	/**
	 * copy files and folders from source to destination for new extension
	 *
	 * @param $source
	 * @param $destination
	 * @return bool
	 */
	protected function copy($source, $destination)
	{
		$results = Services::Filesystem()->folderCopy($source, $destination);
		if ($results == false) {
			//error copying source to destination
			return false;
		}
	}

	/**
	 * copy files and folders from source to destination for new extension
	 *
	 * @param $source
	 * @param $destination
	 * @return bool
	 */
	protected function traverseFolders($destination, $replace, $with)
	{
		/** retrieve all folder names for destination **/
		$folders = Services::Filesystem()->folderFolders($destination, $filter = '',
			$recurse = true, $fullpath = true, $exclude = array('.git'));
		$folders[] = $destination;

		/** process files in each folder **/
		foreach ($folders as $folder) {

			/** retrieve all file names in folder **/
			$files = Services::Filesystem()->folderFiles($folder);

			/** process each file **/
			foreach ($files as $file) {

				/** retrieve current file extension **/
				$file_extension = Services::Filesystem()->fileExtension($file);

				/** rename files, if needed **/
				if (strtolower($file) == $replace . '.' . $file_extension) {

					$this->renameFile(
						$existingName = $replace . '.' . $file_extension,
						$newName = $with . '.' . $file_extension,
						$folder);

					$this->changeWords($folder . '/' . $newName, $replace, $with);

				} else {
					$this->changeWords($folder . '/' . $file, $replace, $with);
				}
			}
		}

		/** process each folder for renames last **/
		foreach ($folders as $folder) {

			/** rename folders, as needed **/
			if (basename($folder) == $replace) {
				/** see if the parent folders have been renamed **/
				$parentPath = dirname($folder);
				if (Services::Filesystem()->folderExists(dirname($parentPath))) {
				} else {
					$parentPath = str_replace($replace, strtolower($with), $parentPath);
				}
				/** rename folder **/
				$this->renameFolder(
					$existingName = $replace,
					$newName = $with,
					$parentPath);
			}
		}
	}


	/**
	 * renameFolder
	 *
	 * @param string $folder
	 *
	 * @return boolean
	 * @since  1.0
	 */
	protected function renameFolder($existingName, $newName, $path)
	{
		if (Services::Filesystem()->folderExists($path)) {
		} else {
			return false;
		}
		if (Services::Filesystem()->folderExists($path . '/' . $existingName)) {
		} else {
			return false;
		}

		$results = Services::Filesystem()->folderMove($existingName, $newName, $path);
		if ($results == false) {
			return false;
		}

		return true;
	}

	/**
	 * renameFile
	 *
	 * Rename file
	 *
	 * @param string $file
	 * @return boolean
	 */
	protected function renameFile($existingName, $newName, $path)
	{
		if (Services::Filesystem()->folderExists($path)) {
		} else {
			return false;
		}
		if (Services::Filesystem()->fileExists($path . '/' . $existingName)) {
		} else {
			return false;
		}
		if (Services::Filesystem()->fileExists($path . '/' . $newName)) {
			return false;
		}

		$results = Services::Filesystem()->fileMove($existingName, $newName, $path);
		if ($results == false) {
			return false;
		}

		return true;
	}

	/**
	 * changeWords
	 *
	 * Changes words in file for plural and singular
	 *
	 * @string  $file
	 * @return  boolean
	 */
	protected function changeWords($file, $replace, $with)
	{
		if (Services::Filesystem()->fileExists($file)) {
		} else {
			return false;
		}

		$body = Services::Filesystem()->fileRead($file);

		$body = str_replace($replace, strtolower($with), $body);
		$body = str_replace(strtoupper($replace), strtoupper($with), $body);
		$body = str_replace(ucfirst($replace), ucfirst($with), $body);

		return Services::Filesystem()->fileWrite($file, &$body);
	}

	/**
	 * Create extension folders and files using samples
	 *
	 * @param  $type
	 *
	 * @return bool
	 * @since  1.0
	 */
	protected function createSampleContent($type = null)
	{
		return true;
		//add admin menu items
		//add sample data

		//$results = Services::Text()->getPlaceHolderText(4, 20, 'html', 1);

		//$results = Services::Text()->addImage(200, 300, 'cat');
	}
}
