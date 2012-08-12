<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Plugin\Cssclassandids;

use Molajo\Extension\Plugin\Content\ContentPlugin;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class CssclassandidsPlugin extends ContentPlugin
{

    /**
     * Add CSS Class and ID to each row
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeViewRender()
    {

        $count = count($this->data);

        if ((int) $count == 0
            || $this->data == false
            || $this->data == null
        ) {
            return true;
        }

        /** class */
        $class = '';

        $class_field = $this->getField('css_class');
        if ($class_field === false) {
            $class_field_value = '';
        } else {
            $class_field_value = $this->getFieldValue($class_field);
        }

        $view_css_class = $this->parameters['template_view_css_class'];

        $current = '';
        if ((int) $this->getField('current', 0) == 1) {
            $current = 'active';
        }

        $class .= ' ' . trim($class_field_value) . ' ' . trim($view_css_class) . ' ' . trim($current);

        if (trim($class) == '') {
            $class = '';
        } else {
            $class = ' class="' . htmlspecialchars(trim($class), ENT_NOQUOTES, 'UTF-8') . '"';
        }

        $this->saveField(null, 'css_class', $class);

        /** id */
        $id = '';

        $id_field = $this->getField('css_id');
        if ($id_field === false) {
            $id_field_value = '';
        } else {
            $id_field_value = $this->getFieldValue($id_field);
        }

        $view_css_id = $this->parameters['template_view_css_id'];

        $id .= ' ' . trim($id_field_value) . ' ' . trim($view_css_id);

        if (trim($id) == '') {
            $id = '';
        } else {
            $id = ' id="' . htmlspecialchars(trim($id), ENT_NOQUOTES, 'UTF-8') . '"';
        }

        $this->saveField(null, 'css_id', $id);

        return true;
    }
}
