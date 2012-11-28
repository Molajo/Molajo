<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Formbegin;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class FormbeginPlugin extends Plugin
{

    /**
     * Prepares form begin attributes
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterReadAll()
    {
        if (strtolower($this->get('template_view_path_node')) == 'formbegin') {
        } else {
            return true;
        }

        $pageUrl = Services::Registry()->get(STRUCTURE_LITERAL, 'page_url');

        $form_action = $this->get('form_action', '');
        if ($form_action == '' || $form_action === null) {
            $form_action = ' action="' . Services::Registry()->get(STRUCTURE_LITERAL, 'page_url') . '"';
        } else {
            $form_action = ' action="' . $form_action . '"';
        }

        $form_method = $this->get('form_method', '');
        if ($form_method == '' || $form_method === null) {
            $form_method = ' method="post"';
        } else {
            $form_method = ' method="' . $form_method . '"';
        }

        $form_name = $this->get('form_name');
        if ($form_name == '' || $form_name === null) {
            $form_name = ' name="' . Services::Registry()->get(ROUTE_PARAMETERS_LITERAL, 'template_view_path_node') . '"';
        } else {
            $form_name = ' name="' . $form_name . '"';
        }

        $form_id = $this->get('form_id', '');
        if ($form_id == '' || $form_id === null) {
            $temp = $this->get('form_name', '');
            if ($temp == '' || $temp === null) {
                $form_id = ' id="' . Services::Registry()->get(ROUTE_PARAMETERS_LITERAL, 'template_view_path_node') . '"';
            } else {
                $form_id = ' id="' . $temp . '"';
            }
        } else {
            $form_id = ' id="' . $form_id . '"';
        }

        $form_class = $this->get('form_class', '');
        if ($form_class == '' || $form_class === null) {
            $form_class = '';
        } else {
            $form_class = ' class="' . $form_class . '"';
        }

        $temp = $this->get('form_attributes', '');
        if ($temp == '' || $temp === null) {
            $formAttributes = '';
        } else {
            $formAttributes =  implode(' ', $temp);
        }

        /** Build Query Results for View */
        $query_results = array();

        $row = new \stdClass();

        $row->form_action = $form_action;
        $row->form_method = $form_method;
        $row->form_name = $form_name;
        $row->form_id = $form_id;
        $row->form_class = $form_class;
        $row->form_attributes = $formAttributes;

        $query_results[] = $row;

        $this->data = $query_results;

        return true;
    }
}
