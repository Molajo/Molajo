<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Status;

use Molajo\Service\Services;
use Molajo\Plugin\Content\ContentPlugin;

defined('MOLAJO') or die;

/**
 * Status Url
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class StatusPlugin extends ContentPlugin
{

	/**
	 * After-read processing
	 *
	 * Provides the Url for any catalog_id field in the recordset
	 *
	 * @param   $this->data
	 * @param   $model
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		$status = $this->get('status');
		if ($status === null) {
			return true;
		}

		if ($status == '2') {
			$status_name = Services::Language()->translate('Archived');
		} elseif ($status == '1') {
			$status_name = Services::Language()->translate('Published');
		} elseif ($status == '0') {
			$status_name = Services::Language()->translate('Unpublished');
		} elseif ($status == '-1') {
			$status_name = Services::Language()->translate('Trashed');
		} elseif ($status == '-2') {
			$status_name = Services::Language()->translate('Spammed');
		} elseif ($status == '-5') {
			$status_name = Services::Language()->translate('Draft');
		} elseif ($status == '-10') {
			$status_name = Services::Language()->translate('Version');
		} else {
			return true;
		}

		$this->saveField(null, 'status_name', $status_name);

		return true;
	}
}
