<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Video;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Video
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class VideoTrigger extends ContentTrigger
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
            self::$instance = new VideoTrigger();
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
        $fields = $this->retrieveFieldsByType('text');

        if (is_array($fields) && count($fields) > 0) {

			/** @noinspection PhpWrongForeachArgumentTypeInspection */
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

						/** add wrap for each */
						foreach ($matches[0] as $iframe) {
							$video = '<include:wrap name=Video {'
								. $iframe
								. '} />';
							$fieldValue = str_replace($iframe, $video, $fieldValue);
                    	}

						/** Update field for all video replacements */
						$fieldValue = $this->saveField($field, $name, $fieldValue);
                	}
				}
            }
        }

        return true;
    }
}
