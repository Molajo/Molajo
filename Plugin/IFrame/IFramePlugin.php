<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\IFrame;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * IFrame
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class IFramePlugin extends Plugin
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

                $name = $field['name'];
                $fieldValue = $this->getFieldValue($field);

                if ($fieldValue === false) {
                } else {

                    preg_match_all('/<iframe.+?src="(.+?)".+?<\/iframe>/', $fieldValue, $matches);

                    if (count($matches) == 0) {
                    } else {

                        /** add wrap for each Iframe and saves data Plugin Registry */
                        $i = 0;

                        foreach ($matches[0] as $iframe) {
                            $element = 'IFrame' . $i++;
                            $video = '<include:wrap name=IFrame value=' . $element . '/>';
                            Services::Registry()->set(TEMPLATE_LITERAL, $element, $iframe);
                            $fieldValue = str_replace($iframe, $video, $fieldValue);
                        }

                        /** Update field for all Iframe replacements */
                        $this->saveField($field, $name, $fieldValue);
                    }
                }
            }
        }

        return true;
    }
}
