<?php
/**
 * @version     $id: filterFeatured.php
 * @package     Molajo
 * @subpackage  Filter
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
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
ClassFieldFeatured extends MolajoField
{
    /**
     *  __construct
     *
     *  Set Fieldname and Filter with parent
     */
    public function __construct()
    {
        parent::__construct();
        parent::setName('featured');
        parent::setFilter('string');

        parent::setSortable(true);
        parent::setCheckbox(false);
        parent::setDisplayType('integer');
    }

    /**
     *  getOptions
     *
     *  Returns Option Values
     */
    public function getOptions()
    {
        $options = array();
        $options[] = MolajoHTML::_('select.option', '0', Services::Language()->translate('MOLAJO_OPTION_UNFEATURED'));
        $options[] = MolajoHTML::_('select.option', '1', Services::Language()->translate('MOLAJO_OPTION_FEATURED'));
        return $options;
    }

    /**
     *  getValue
     *
     *  Returns Selected Value
     */
    public function getValue()
    {
        /** retrieve and filter selected value **/
        parent::getValue();

        if ($this->value == null) {
            return false;
        }

        /** validate to list **/
        $this->validateRequestValue();

        /** return filtered and validated value **/
        return $this->value;
    }

    /**
     *  validateRequestValue
     *
     *  Returns Selected Value
     */
    public function validateRequestValue()
    {
        if ($this->value == 1 || $this->value == 0) {
            return $this->value;
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
    public function render($view, $item, $itemCount)
    {
        if ($view == 'admin') {
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
