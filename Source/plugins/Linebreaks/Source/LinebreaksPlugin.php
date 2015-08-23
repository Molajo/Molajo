<?php
/**
 * Linebreaks Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Linebreaks;

use CommonApi\Event\ReadEventInterface;
use Molajo\Plugins\ReadEvent;

/**
 * Linebreaks Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class LinebreaksPlugin extends ReadEvent implements ReadEventInterface
{
    /**
     * Executes after reading row
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterReadRow()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        $this->processFieldsByType('processLinebreak', $this->hold_fields);

        return $this;
    }

    /**
     * Process Plugin Determination
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
    {
        if (strtolower($this->runtime_data->route->page_type) === 'edit'
            || strtolower($this->runtime_data->route->page_type) === 'new'
        ) {
            return false;
        }

        return $this->existFields('html');
    }

    /**
     * Format Linebreaks Field
     *
     * @param   array $field
     *
     * @return  array
     * @since   1.0.0
     */
    protected function processLinebreak(array $field = array())
    {
        $value = $this->getFieldValue($field);

        if (trim($value) === '') {
            return $field;
        }

        $new_value = nl2br($value);

        if ($value === $new_value) {
            return $field;
        }

        $field['value'] = $new_value;

        return $field;
    }
}
