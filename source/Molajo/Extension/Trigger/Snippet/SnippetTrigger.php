<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Snippet;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Snippet
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class SnippetTrigger extends ContentTrigger
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
            self::$instance = new SnippetTrigger();
        }

        return self::$instance;
    }

    /**
     * After-read processing
     *
     * Parses the Content Text into a snippet, stripped of HTML tags
     *
     * @param   $this->query_results
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
   {
		$fields = $this->retrieveFieldsByType('text');

		if (is_array($fields) && count($fields) > 0) {

			/** @noinspection PhpWrongForeachArgumentTypeInspection */
			foreach ($fields as $field) {

				$name = $field->name;

				/** Retrieves the actual field value from the 'normal' or special field */
				$fieldValue = $this->getFieldValue($field);

				if ($fieldValue == false) {
				} else {

					$newField = Services::Text()->snippet($fieldValue);

					if ($newField == false) {
					} else {

						/** Creates the new 'normal' or special field and populates the value */
						$newFieldName = $name . '_' . 'snippet';
						$fieldValue = $this->saveField($field, $newFieldName, $newField);
					}
				}
			}
		}

		return true;
    }
}
