<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see license folder
 */
namespace Molajo\Extension\Trigger\IFrame;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * IFrame
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class IFrameTrigger extends ContentTrigger
{

	/**
	 * After-read processing
	 *
	 * Locates IFrame statements in text, replacing with an <include:wrap statement for Responsive Treatment
	 *
	 * Primarily for treatment of Video, but useful for an IFrame embed
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		$fields = $this->retrieveFieldsByType('text');

		if (is_array($fields) && count($fields) > 0) {

			foreach ($fields as $field) {

				/** retrieve each text field */
				$name = $field->name;
				$fieldValue = $this->getFieldValue($field);

				if ($fieldValue == false) {
				} else {

					/** search for iframe statements */
					preg_match_all('/<iframe.+?src="(.+?)".+?<\/iframe>/', $fieldValue, $matches);

					if (count($matches) == 0) {
					} else {

						/** add wrap for each Iframe and saves data Trigger Registry */
						$i = 0;

						foreach ($matches[0] as $iframe) {
							$element = 'IFrame' . $i++;
							$video = '<include:wrap name=IFrame value=' . $element . '/>';
							Services::Registry()->set('Triggerdata', $element, $iframe);
							$fieldValue = str_replace($iframe, $video, $fieldValue);
						}

						/** Update field for all Iframe replacements */
						$fieldValue = $this->saveField($field, $name, $fieldValue);
					}
				}
			}
		}

		return true;
	}
}
