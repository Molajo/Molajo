<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Dataobject;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class DataobjectPlugin extends Plugin
{
	/**
	 * Prepares list of Dataobject Types
	 *
	 * This can be moved to onBeforeParse when Plugin ordering is in place
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onAfterRoute()
	{
		if (APPLICATION_ID == 2) {
		} else {
			return true;
		}

		$files = Services::Filesystem()->folderFiles(
			PLATFORM_FOLDER . '/Dataobject'
		);

		if (count($files) === 0 || $files === false) {
            $dataobjectLists = array();
		} else {
			$dataobjectLists = $this->processFiles($files);
		}

		$resourceFiles = Services::Filesystem()->folderFiles(
			Services::Registry()->get('Parameters', 'extension_path') . '/Dataobject'
		);

		if (count($resourceFiles) == 0 || $resourceFiles === false) {
			$resourceLists = array();
		} else {
			$resourceLists = $this->processFiles($resourceFiles);
		}

		$new = array_merge($dataobjectLists, $resourceLists);
		$newer = array_unique($new);
		sort($newer);

		$dataobject = array();

		foreach ($newer as $file) {
			$row = new \stdClass();
			$row->value = $file;
			$row->id = $file;
			$dataobject[] = $row;
		}

		Services::Registry()->set('Dataobject', 'Dataobjects', $dataobject);

		return true;
	}

	/**
	 * Prepares list of Dataobject Lists
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	protected function processFiles($files)
	{
		$fileList = array();

		foreach ($files as $file) {

			$length = strlen($file) - strlen('.xml');
			$value = substr($file, 0, $length);

			$fileList[] = $value;
		}

		return $fileList;
	}
}
