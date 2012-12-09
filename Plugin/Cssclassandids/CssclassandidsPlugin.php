<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Cssclassandids;

use Molajo\Plugin\Plugin\Plugin;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class CssclassandidsPlugin extends Plugin
{

    /**
     * Add CSS Class and ID to each row
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
    {

        if ((int) count($this->row) == 0
            || $this->row === false
            || $this->row == null
        ) {
            return true;
        }

        /** class */
        $class = '';

        $class_field = $this->get('css_class', '', 'parameters');

        $view_css_class = $this->get('template_view_css_class', '', 'parameters');

        $current_field = $this->get('current', '', 'parameters');
        if ($current_field === false) {
            $current_field_value = '';
        } else {
            $current_field_value = $this->get($current_field, '', 'parameters');
        }

        $class .= ' ' . trim($class_field) . ' ' . trim($view_css_class) . ' ' . trim($current_field_value);

        if (trim($class) == '') {
            $class = '';
        } else {
            $class = htmlspecialchars(trim($class), ENT_NOQUOTES, 'UTF-8');
        }

        $this->saveField(null, 'css_class', $class);

        /** id */
        $id = '';

        $id_field = $this->get('css_id', '', 'parameters');
        if ($id_field === '') {
            $id_field_value = '';
        } else {
            $id_field_value = $this->getFieldValue($id_field);
        }

        $view_css_id = $this->get('template_view_css_id', '', 'parameters');

        $id .= ' ' . trim($id_field_value) . ' ' . trim($view_css_id);

        if (trim($id) == '') {
            $id = '';
        } else {
            $id = htmlspecialchars(trim($id), ENT_NOQUOTES, 'UTF-8');
        }

        $this->saveField(null, 'css_id', $id);

        return true;
    }
}
