<?php
/**
 * @version     $id: filterChecked_out.php
 * @package     Molajo
 * @subpackage  Filter
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  MolajoFieldChecked_out
 *
 *  Checked_out Filter Field Handling
 *
 *  @package    Molajo
 *  @subpackage Filter
 *  @since      1.6
 */
class MolajoFieldChecked_out extends MolajoField
{
    /**
     *  __construct
     *
     *  Set Fieldname and Filter with parent
     */
    public function __construct() {
        parent::__construct();
        parent::setFieldname ('checked_out');
        parent::setRequestFilter ('integer');
    }

    /**
     *  getOptions
     *
     *  Returns Option Values
     */
    public function getOptions () {}
    /**
     *  getSelectedValue
     *
     *  Returns Selected Value
     */
    public function getSelectedValue () {}

    /**
     *  validateRequestValue
     *
     *  Returns Selected Value
     */
    public function validateRequestValue () {}

    /**
    *  getQueryInformation
    *
    *  Returns Formatted Where clause for Query
    */
    public function getQueryInformation ($query, $value, $selectedState, $onlyWhereClause=false)
    {
        if ($onlyWhereClause) {
        } else {
            $query->select('editor.name as currently_editing_name');
            $query->select('editor.id as currently_editing_id');
            $query->join('LEFT', '#__users AS editor ON editor.id = a.checked_out');
        }

        if ((int) $value == 0) {
            return;
        }

        $query->where('a.checked_out = '. (int) $value);
    }

    /**
     *  render
     *
     *  sets formatting and content parameters
     */
    public function render ($layout, $item, $itemCount)
    {
        if ($layout == 'admin') {
            $render = array();
            $render['link_value'] = false;
            $render['class'] = 'nowrap';
            $render['valign'] = 'top';
            $render['align'] = 'left';
            $render['sortable'] = true;
            $render['checkbox'] = false;
            $render['data_type'] = 'date';
            $render['column_name'] = 'checked_out';
            $render['print_value'] = $item->currently_editing_name;

            return $render;
        }
    }
}