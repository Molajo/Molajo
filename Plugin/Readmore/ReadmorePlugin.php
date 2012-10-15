<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Readmore;

use Molajo\Plugin\Plugin\Plugin;

defined('MOLAJO') or die;

/**
 * Content Text
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class ReadmorePlugin extends Plugin
{

	/**
	 * After-read processing
	 *
	 * splits the content_text field into intro and full text on readmore
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{

		$fields = $this->retrieveFieldsByType('text');

		if (is_array($fields) && count($fields) > 0) {

			foreach ($fields as $field) {

				$name = $field->name;

				$fieldValue = $this->getFieldValue($field);

				if ($fieldValue === false) {
				} else {

					$newFields = $this->splitReadMoreText($fieldValue);

					if ($newFields === false) {
					} else {
						$introductory_name = $name . '_' . 'introductory';
						$this->saveField(null, $introductory_name, $newFields[0]);

						$fulltext_name = $name . '_' . 'fulltext';
						$this->saveField(null, $fulltext_name, $newFields[1]);

						$content_text = '<div class="introductory">' . trim($newFields[0]) . '</div>'
							. trim($newFields[1]);

						$this->saveField($name, $name, $content_text);
					}
				}
			}
		}

		return true;
	}

	/**
	 * splitReadMoreText - search for the system-readmore break and split the text at that point into two text fields
	 *
	 * @param  $text
	 *
	 * @return array
	 * @since   1.0
	 */
	public function splitReadMoreText($text)
	{
		$pattern = '#{readmore}#';

		$tagPos = preg_match($pattern, $text);

		$introductory_text = '';
		$fulltext = '';

		if ($tagPos == 0) {
			$introductory_text = $text;
		} else {
			list($introductory_text, $fulltext) = preg_split($pattern, $text, 2);
		}

		return (array($introductory_text, $fulltext));
	}
}
