<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Mockdata;

use Molajo\Plugin\Plugin\Plugin;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class MockdataPlugin extends Plugin
{

    /**
     * Creates text, adds images, video, smilies, assigns created_by
     *
     * {image}250,250,box{/image}
     * {blockquote}{cite:xYZ}*.*{/blockquote}
     * <iframe.+?src="(.+?)".+?<\/iframe>
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
}
