<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Version;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Item Author
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class VersionTrigger extends ContentTrigger
{

	/**
	 * Pre-create processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeCreate()
	{
		$field = $this->getField('version');
		$name = $field->name;
		$fieldValue = $this->getFieldValue($field);
		if ($fieldValue == false
			|| $fieldValue == ''
		) {
			$newFieldValue = 1;
			$this->saveField($field, $name, $newFieldValue);
		}

		$field = $this->getField('version_of_id');
		$name = $field->name;
		$fieldValue = $this->getFieldValue($field);
		if ($fieldValue == false
			|| $fieldValue == ''
		) {
			$newFieldValue = 0;
			$this->saveField($field, $name, $newFieldValue);
		}

		$field = $this->getField('status_prior_to_version');
		$name = $field->name;
		$fieldValue = $this->getFieldValue($field);
		if ($fieldValue == false
			|| $fieldValue == ''
		) {
			$newFieldValue = 0;
			$this->saveField($field, $name, $newFieldValue);
		}

		return true;
	}

	/**
	 * Pre-update processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeUpdate()
	{
		$field = $this->getField('version');
		$name = $field->name;
		$fieldValue = $this->getFieldValue($field);
		if ($fieldValue == false
			|| $fieldValue == ''
		) {
			$newFieldValue = 1 + 1;
			$this->saveField($field, $name, $newFieldValue);
		}

		$field = $this->getField('status_prior_to_version');
		$name = $field->name;
		$fieldValue = $this->getFieldValue($field);
		if ($fieldValue == false
			|| $fieldValue == ''
		) {
			$newFieldValue = $this->query_results->status;
			$this->saveField($field, $name, $newFieldValue);
		}

		return true;
	}

	/**
	 * Post-update processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterUpdate()
	{
		return true;
	}
}
