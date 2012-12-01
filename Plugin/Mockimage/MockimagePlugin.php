<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Mockimage;

use Molajo\Plugin\Plugin\Plugin;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class MockimagePlugin extends Plugin
{

    /**
     * Adds mock images in text, where requested
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
                    $value = $this->search($fieldValue);

                    if ($value === false) {
                    } else {
                        $this->saveField($field, $name, $value);
                    }
                }

            }
        }

        return true;
    }

    /**
     * search for image requests
     *
     * @param  $text
     *
     * @return array
     * @since   1.0
     */
    protected function search($text)
    {
        $pattern = '/{mockimage}(.*){\/mockimage}/';

        preg_match_all($pattern, $text, $matches);

        $replaceThis = array();
        $withThis = array();

        if (count($matches) == 0) {

        } else {

            $i = 0;
            foreach ($matches[1] as $match) {

                $replaceThis[] = $matches[0][$i];

                $ImageRequest = explode(',', $match);

                $width = 0;
                $height = 0;
                $class = '';
                $type = '';

                $i = 0;
                foreach ($ImageRequest as $parameter) {
                    if ($width == 0) {
                        $width = (int) $parameter;

                    } elseif ($height == 0) {
                        $height = (int) $parameter;

                    } elseif ($class == '') {
                        if (in_array($parameter, array('right', 'left', 'center'))) {
                        } else {
                            $class = 'left';
                        }

                    } elseif ((trim($parameter) == 'cat')) {
                        $type = 'cat';

                    } else {
                        $type = 'box';
                    }
                }

                $withThis[] = $this->addImage($width, $height, $class, $type);
                $i++;
            }
        }

        $text = str_replace($replaceThis, $withThis, $text);

        return $text;
    }

    /**
     * Add images to text
     *
     * {mockimage}250,250,box{/mockimage}
     *
     * @param int $width
     * @param int $height
     * @param int $type   (box, cat)
     *
     * @param  color ffffff
     * @param  &text=Put+Text+Here
     *
     * @return string
     * @since   1.0
     */
    public function addImage($width = 250, $height = 250, $class = 'left', $type = 'box')
    {

        if (in_array($class, array('right', 'left', 'center'))) {
            $float = 'float: ' . $class;
        } else {
            $class = 'left';
            $float = 'float: left';
        }

        $imageclass = 'image' . $class;
        $spanclass = 'mockimage float' . $class;

        if ($type == 'cat') {
            $mockimage = '<span class="' . $spanclass . '"><img src="http://placekitten.com/' . (int) $width . '/' . (int) $height . '" class="' . $imageclass . '"/></span>';
        } else {
            $mockimage = '<span class="' . $spanclass . '"><img src="http://placehold.it/' . (int) $width . 'x' . (int) $height . '" class="' . $imageclass . '"/></span>';
        }

        return $mockimage;
    }
}
