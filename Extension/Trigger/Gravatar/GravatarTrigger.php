<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
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
            self::$instance = new GravatarTrigger();
        }

        return self::$instance;
    }

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

			if (Services::Registry()->get('Parameters', 'criteria_use_gravatar', 1) == 1) {
				$size = Services::Registry()->get('Parameters', 'criteria_gravatar_size', 80);
				$type = Services::Registry()->get('Parameters', 'criteria_gravatar_type', 'mm');
				$rating = Services::Registry()->get('Parameters', 'criteria_gravatar_rating', 'pg');
				$image = Services::Registry()->get('Parameters', 'criteria_gravatar_image', 0);

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
