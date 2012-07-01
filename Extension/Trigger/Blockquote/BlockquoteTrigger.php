<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Blockquote;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Blockquote
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class BlockquoteTrigger extends ContentTrigger
{
	/**
	 * Blockquote extraction and formatting
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

					/** search for blockquote statements, remove from text */
					$results = Services::Text()->blockquote($fieldValue);

					if ($results == false || $results == '') {
					} else {
						/** Initialise */
						$query_results = array();

						/** Replace existing text */
						$fieldValue = $results[2];
						$this->saveField($field, $name, $fieldValue);

						/** Save new blockquote array */
						$blockquote = $results[0];
						$cite = $results[1];

						$blockQuoteName = $name . '_blockquote';
						$this->saveField($field, $blockQuoteName, $blockquote);

						$citeName = $name . '_cite';
						$this->saveField($field, $citeName, $cite);

						$i = 0;
						foreach ($blockquote as $quote) {
							$row = new \stdClass();
							$row->blockquote = $quote;
							$row->cite = $cite[$i];
							$query_results[] = $row;
							$i++;
						}

						Services::Registry()->set('Trigger', 'Blockquote', $query_results);
					}
				}
			}
		}

		return true;
	}
}
