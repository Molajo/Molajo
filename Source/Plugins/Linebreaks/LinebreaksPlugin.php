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
     * Changes line breaks to break tags
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRead()
    {
        $fields = $this->getFieldsByType('text');

        if (is_array($fields) && count($fields) > 0) {

            foreach ($fields as $field) {

                $name = $field['name'];

                $fieldValue = $this->getFieldValue($field);

                if ($fieldValue === null) {
                } else {

                    $newField = nl2br($fieldValue);

                    if ($newField === null) {
                    } else {
                        $this->setField($field, $field['name'], $newField);
                    }
                }
            }
        }

        return $this;
    }
}
