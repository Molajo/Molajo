<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Plugin\Rownumber;

use Molajo\Extension\Plugin\Content\ContentPlugin;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class RownumberPlugin extends ContentPlugin
{
    /**
     * Before the Query results are injected into the View
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeViewRender()
    {

        if ((int) $this->parameters['total_rows'] == 0
            || $this->data == false
            || $this->data == null
        ) {
            return true;
        }

        if (is_object($this->data)) {
        } else {
            return true;
        }

        /** first row */
        if ($this->parameters['row_count'] == 1) {
            $value = 'first';
        } else {
            $value = '';
        }
        $this->saveField(null, 'first_row', $value);

        /** last row */
        if ($this->parameters['row_count'] == $this->parameters['total_rows']) {
            $value = 'last';
        } else {
            $value = '';
        }
        $this->saveField(null, 'last_row', $value);

        /** total_rows */
        $this->saveField(null, 'total_rows', $this->parameters['total_rows']);

        /** even_or_odd_row */
        $this->saveField(null, 'even_or_odd_row', $this->parameters['even_or_odd']);

        /** grid_row_class */
        $value = ' class="' .
            trim(trim($this->data->first_row)
                . ' ' . trim($this->data->even_or_odd_row)
                . ' ' . trim($this->data->last_row))
            . '"';

        $this->saveField(null, 'grid_row_class', $value);

        return true;
    }
}
