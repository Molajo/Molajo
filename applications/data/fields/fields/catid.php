<?php
/**
 * @version     $id: filterCatid.php
 * @package     Molajo
 * @subpackage  Filter
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  MolajoFieldCatid
 *
 *  Catid Filter Field Handling
 *
 * @package    Molajo
 * @subpackage Filter
 * @since      1.6
 */
class MolajoFieldCatid extends MolajoField
{
    /**
     *  __construct
     *
     *  Set Fieldname and Filter with parent
     */
    public function __construct()
    {
        parent::__construct();
        parent::setName('category_id');
        parent::setFilter('integer');
    }

    /**
     *  getOptions
     *
     *  Returns Option Values
     */
    public function getOptions()
    {
        return MolajoHTML::_('category.options', JRequest::getCmd('option'));
    }

    /**
     *  getValue
     *
     *  Returns Selected Value
     */
    public function getValue()
    {
        parent::getValue();

        if ($this->value == null) {
            return false;
        }

        /** return filtered and validated value **/
        return $this->value;
    }

    public function validateRequestValue()
    {

    }

    /**
     *  getQueryInformation
     *
     *  Returns Formatted Where clause for Query
     */
    public function getQueryInformation($query, $value, $selectedState, $onlyWhereClause = false)
    {
        if ((int)$value == 0) {
            return;
        }

        if (is_numeric($value) && $value > 0) {
            $query->where('a.category_id = ' . $value);

        } else if (is_array($value)) {
            JArrayHelper::toInteger($value);
            $categoryId = implode(',', $value);
            $query->where('a.category_id IN (' . $categoryId . ')');
        }
    }

    /**
     *  render
     *
     *  sets formatting and content parameters
     */
    public function render($layout, $item, $itemCount)
    {
        if ($layout == 'admin') {
            $render = array();
            if ($item->canEdit === true) {
                $render['link_value'] = 'index.php?option=categories&extension=' . JRequest::getVar('option') . '&task=category.edit&id=' . $item->category_id;
            } else {
                $render['link_value'] = false;
            }
            $render['class'] = 'nowrap';
            $render['valign'] = 'top';
            $render['align'] = 'left';
            $render['sortable'] = true;
            $render['checkbox'] = false;
            $render['data_type'] = 'string';
            $render['column_name'] = 'category_id';
            $render['print_value'] = $item->category_id;

            return $render;
        }
    }
}