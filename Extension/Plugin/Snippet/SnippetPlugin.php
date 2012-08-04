<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Plugin\Snippet;

use Molajo\Extension\Plugin\Content\ContentPlugin;

defined('MOLAJO') or die;

/**
 * Snippet
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class SnippetPlugin extends ContentPlugin
{

    /**
     * Parses the Content Text into a snippet, stripped of HTML tags
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        $fields = $this->retrieveFieldsByType('text');

        $snippetLength = $this->get('criteria_snippet_length', 200);

        if (is_array($fields) && count($fields) > 0) {

            foreach ($fields as $field) {

                $name = $field->name;

                $fieldValue = $this->getFieldValue($field);

                if ($fieldValue == false) {
                } else {

                    $newFieldValue = substr(strip_tags($fieldValue), 0, $snippetLength);
                    if (trim($newFieldValue) == trim(strip_tags($fieldValue))) {
                    } else {
                        $newFieldValue .= '...';
                    }

                    if ($newFieldValue == false) {
                    } else {

                        $newFieldName = $name . '_' . 'snippet';
                        $this->saveField(null, $newFieldName, $newFieldValue);
                    }
                }
            }
        }

        return true;
    }
}
