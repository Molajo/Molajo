<?php
/**
 * IFrame Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\IFrame;

use CommonApi\Event\ReadInterface;
use Molajo\Plugin\ReadEventPlugin;

/**
 * IFrame Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class IFramePlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * After-read processing
     *
     * Locates IFrame statements in text, replacing with an <include wrap statement for Responsive Treatment
     *
     * Primarily for treatment of Video, but useful for an IFrame embed
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRead()
    {
        $fields = $this->getFieldsByType('text');

        if (is_array($fields) && count($fields) > 0) {

            foreach ($fields as $field) {

                $name       = $field['name'];
                $fieldValue = $this->getFieldValue($field);

                if ($fieldValue === false) {
                } else {

                    preg_match_all('/<iframe.+?src="(.+?)".+?<\/iframe>/', $fieldValue, $matches);

                    if (count($matches) == 0) {
                    } else {

                        /** add wrap for each Iframe and saves data Plugin Registry */
                        $i = 0;

                        foreach ($matches[0] as $iframe) {
                            $element = 'IFrame' . $i ++;
                            $video   = '<include wrap=IFrame value=' . $element . '/>';
                            $this->registry->set('Template', $element, $iframe);
                            $fieldValue = str_replace($iframe, $video, $fieldValue);
                        }

                        /** Update field for all Iframe replacements */
                        $this->setField($field, $name, $fieldValue);
                    }
                }
            }
        }

        return $this;
    }
}
