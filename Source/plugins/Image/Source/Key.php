<?php
/**
 * Image Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Image;

use Molajo\Plugins\ReadEvent;
use stdClass;

/**
 * Image Plugin
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Key extends ReadEvent
{
    /**
     * Add Image Key
     *
     * @param   array $field
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function addImageKey(array $field = array())
    {
        // Save name
        $field['value'] = md5($this->getFieldValue($field));

        // Create Image Data and Model Registry
        $model_registry = $this->createImageModelRegistry($field);
        $row            = $this->createImageRow($field);

        // Set Plugin Data
        $this->plugin_data->{$field['value']}                 = new stdClass();
        $this->plugin_data->{$field['value']}->data           = array($row);
        $this->plugin_data->{$field['value']}->model_registry = $model_registry;

        // Add Image Key to Primary Fieldset
        $field['name'] .= '_key';
        $field['type']   = 'string';
        $field['source'] = 'fields';

        return $field;
    }

    /**
     * Create Image Model Registry
     *
     * @param   array $field
     *
     * @return  array
     * @since   1.0.0
     */
    protected function createImageModelRegistry(array $field = array())
    {
        $field_name = $field['name'];
        $source     = $field['source'];

        $model_registry           = array();
        $model_registry['fields'] = array();

        $model_registry['fields']['image']
            = $this->controller['model_registry'][$source][$field_name];

        $model_registry['fields']['image_caption']
            = $this->controller['model_registry'][$source][$field_name . '_caption'];

        $model_registry['fields']['align']
            = $this->controller['model_registry'][$source][$field_name . '_caption'];
        $model_registry['fields']['align']['name']
            = 'align';

        $model_registry['fields']['size']
            = $this->controller['model_registry'][$source][$field_name . '_caption'];
        $model_registry['fields']['size']['name']
            = 'size';

        return $model_registry;
    }

    /**
     * Create Image Row
     *
     * @param   array $field
     *
     * @return  object
     * @since   1.0.0
     */
    protected function createImageRow(array $field = array())
    {
        $field_name = $field['name'];
        $source     = strtolower($field['source']);

        $row = new stdClass();

        if ($source === 'fields') {
            $row->image         = $this->controller['row']->$field_name;
            $row->image_caption = $this->controller['row']->{$field_name . '_caption'};
        } else {
            $row->image         = $this->controller['row']->$source->$field_name;
            $row->image_caption = $this->controller['row']->$source->{$field_name . '_caption'};
        }

        return $row;
    }
}
