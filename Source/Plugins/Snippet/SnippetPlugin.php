<?php
/**
 * Snippet Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Snippet;

use CommonApi\Event\ReadInterface;
use Molajo\Plugins\ReadEventPlugin;

/**
 * Snippet Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class SnippetPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * Parses field types of Text into snippets, stripped of HTML tags, forced to a specific length
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRead()
    {
        if (isset($this->runtime_data->route)) {
        } else {
            return $this;
        }

        $fields = $this->getFieldsByType('text');
//todo criteria_snippet_length
        $snippetLength = 200;

        if (count($fields) > 0) {

            foreach ($fields as $field) {

                $name = $field['name'];

                $fieldValue = $this->getFieldValue($field);

                if ($fieldValue === false || $fieldValue === null) {
                } else {

                    $newFieldValue = substr(strip_tags($fieldValue), 0, $snippetLength);

                    if (trim($newFieldValue) == trim(strip_tags($fieldValue))) {
                    } else {
                        $newFieldValue .= '...';
                    }

                    if ($newFieldValue === false || $newFieldValue === null) {
                    } else {

                        $newFieldName = $name . '_' . 'snippet';
                        $this->setField(null, $newFieldName, $newFieldValue);
                    }
                }
            }
        }

        return $this;
    }
}
