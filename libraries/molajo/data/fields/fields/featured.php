<?php
/**
 * @version     $id: filterFeatured.php
 * @package     Molajo
 * @subpackage  Filter
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  MolajoFieldFeatured
 *
 *  Featured Filter Field Handling
 *
 * @package    Molajo
 * @subpackage Filter
 * @since      1.6
 */
class MolajoFieldFeatured extends MolajoField
{
    /**
     *  __construct
     *
     *  Set Fieldname and Filter with parent
     */
    public function __construct()
    {
        parent::__construct();
        parent::setFieldname('featured');
        parent::setRequestFilter('string');

        parent::setTableColumnSortable(true);
        parent::setTableColumnCheckbox(false);
        parent::setDisplayDataType('integer');
    }

    /**
     *  getOptions
     *
     *  Returns Option Values
     */
    public function getOptions()
    {
        $options = array();
        $options[] = MolajoHTML::_('select.option', '0', MolajoText::_('MOLAJO_OPTION_UNFEATURED'));
        $options[] = MolajoHTML::_('select.option', '1', MolajoText::_('MOLAJO_OPTION_FEATURED'));
        return $options;
    }

    /**
     *  getSelectedValue
     *
     *  Returns Selected Value
     */
    public function getSelectedValue()
    {
        /** retrieve and filter selected value **/
        parent::getSelectedValue();

        if ($this->requestValue == null) {
            return false;
        }

        /** validate to list **/
        $this->validateRequestValue();

        /** return filtered and validated value **/
        return $this->requestValue;
    }

    /**
     *  validateRequestValue
     *
     *  Returns Selected Value
     */
    public function validateRequestValue()
    {
        if ($this->requestValue == 1 || $this->requestValue == 0) {
            return $this->requestValue;
        } else {
            return false;
        }
    }

    /**
     *  getQueryInformation
     *
     *  Returns Formatted Where clause for Query
     */
    public function getQueryInformation($query, $value, $selectedState, $onlyWhereClause = false)
    {
        if ($onlyWhereClause) {
        } else {
            $query->select('a.featured');
        }

        if (trim($value) == '') {
            return;
        }
        $query->where('a.featured = ' . (int)$value);
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
            $render['link_value'] = false;
            $render['class'] = 'nowrap';
            $render['valign'] = 'top';
            $render['align'] = 'left';
            $render['sortable'] = true;
            $render['checkbox'] = false;
            $render['data_type'] = 'string';
            $render['column_name'] = 'author_name';
            $render['print_value'] = $item->featured; //MolajoHTML::_('Mgrid.featured', $item->featured, $itemCount, $item->canEditstate);

            return $render;
        }
    }
}