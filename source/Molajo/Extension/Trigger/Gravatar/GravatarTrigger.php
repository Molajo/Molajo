<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Trigger\Gravatar;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Gravatar
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class GravatarTrigger extends ContentTrigger
{

	/**
	 * After-read processing
	 *
	 * Retrieves Author Information for Item
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		$fields = $this->retrieveFieldsByType('email');

		if (is_array($fields) && count($fields) > 0) {

			if ($this->get('gravatar', 1) == 1) {
				$size = $this->get('gravatar_size', 80);
				$type = $this->get('gravatar_type', 'mm');
				$rating = $this->get('gravatar_rating', 'pg');
				$image = $this->get('gravatar_image', 0);

			} else {
				return true;
			}

			/** @noinspection PhpWrongForeachArgumentTypeInspection */
			foreach ($fields as $field) {

				$name = $field->name;
				$new_name = $name . '_' . 'gravatar';

				/** Retrieves the actual field value from the 'normal' or special field */
				$fieldValue = $this->getFieldValue($field);

				if ($fieldValue == false) {
					return true;
				} else {
					$results = Services::Url()->getGravatar($fieldValue, $size, $type, $rating, $image);
				}

				if ($results == false) {
				} else {
					$fieldValue = $this->saveField($field, $new_name, $results);
				}
			}
		}

		return true;
	}
}
