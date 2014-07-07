<?php
/**
 * Linebreaks Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Linebreaks;

use CommonApi\Event\ReadInterface;
use Molajo\Plugins\ReadEventPlugin;

/**
 * Linebreaks Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class LinebreaksPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * Creates Linebreaks in Text Fields
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterRead()
    {
        if ($this->countGetFields() === false) {
            return $this;
        }

        return $this->setLinkbreaks();
    }

    /**
     * Changes line breaks to break tags
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setLinkbreaks()
    {
        $this->processFieldsByType($this->hold_fields, 'processLineBreak');

        return $this;
    }

    /**
     * Changes line breaks to break tags
     *
     * @param   object $field
     *
     * @return  object
     * @since   1.0.0
     */
    protected function processLineBreak($field)
    {
        $field_value = $this->getFieldValue($field);

        if ($field_value === null) {
        } else {
            $field['value'] = nl2br($field_value);
        }

        return $field;
    }
}
