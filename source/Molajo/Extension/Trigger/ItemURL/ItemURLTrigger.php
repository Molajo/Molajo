<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\ItemURL;

use Molajo\Extension\Trigger\Content\ContentTrigger;

defined('MOLAJO') or die;

/**
 * Item URL
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class ItemURLTrigger extends ContentTrigger
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
            self::$instance = new ItemURLTrigger();
        }

        return self::$instance;
    }

	/**
	 * After-read processing
	 *
	 * Provides the URL for any catalog_id field in the recordset
	 *
	 * @param   $this->query_results
	 * @param   $model
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		$fields = $this->retrieveFieldsByType('catalog_id');

		if (is_array($fields) && count($fields) > 0) {

			foreach ($fields as $field) {

				$name = $field->name;

				/** Retrieves the actual field value from the 'normal' or special field */
				$fieldValue = $this->getFieldValue($field);

				if ($fieldValue == false) {
				} else {

					$newField = Services::Url()->getURL($fieldValue);

					if ($newField == false) {
					} else {

						/** Creates the new 'normal' or special field and populates the value */
						$newFieldName = $name . '_' . 'url';
						$fieldValue = $this->saveField($field, $newFieldName, $newField);
					}
				}
			}
		}

		return true;
	}
}
