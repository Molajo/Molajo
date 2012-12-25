<?php
/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Plugin\Formbegin;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * @package     Niambie
 * @license     MIT
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
        if (strtolower($this->get('template_view_path_node', '', 'parameters')) == 'formbegin') {
        } else {
            return true;
        }

        $pageUrl = Services::Registry()->get(PAGE_LITERAL, 'page_url');

        $form_action = $this->get('form_action', '');
        if ($form_action == '' || $form_action === null) {
            $form_action = ' action="' . Services::Registry()->get(PAGE_LITERAL, 'page_url') . '"';
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
        $temp_query_results = array();

        $temp_row = new \stdClass();

        $temp_row->form_action = $form_action;
        $temp_row->form_method = $form_method;
        $temp_row->form_name = $form_name;
        $temp_row->form_id = $form_id;
        $temp_row->form_class = $form_class;
        $temp_row->form_attributes = $formAttributes;

        $temp_query_results[] = $temp_row;

        $this->row = $temp_query_results;

        return true;
    }
}
