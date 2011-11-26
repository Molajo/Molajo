<?php
/**
 * @version     $id: filterState.php
 * @package     Molajo
 * @subpackage  Filter
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  MolajoFieldState
 *
 *  State Filter Field Handling
 *
 * @package    Molajo
 * @subpackage Filter
 * @since      1.6
 */
class MolajoFieldState extends MolajoField
{
    /**
     *  __construct
     *
     *  Set Fieldname and Filter with parent
     */
    public function __construct()
    {
        parent::__construct();
        parent::setFieldname('state');
        parent::setFilter('string');
    }

    /**
     *  getOptions
     *
     *  Returns Option Values
     */
    public function getOptions()
    {
        $this->parameters = MolajoApplicationComponent::getParameters(JRequest::getVar('option'));
        $this->config_component_state_spam = JRequest::getCmd('config_component_state_spam', '0');

        $options = array();
        $options[] = MolajoHTML::_('select.option', MOLAJO_STATUS_ARCHIVED, MolajoText::_('MOLAJO_OPTION_STATUS_ARCHIVED'));
        $options[] = MolajoHTML::_('select.option', MOLAJO_STATUS_PUBLISHED, MolajoText::_('MOLAJO_OPTION_STATUS_PUBLISHED'));
        $options[] = MolajoHTML::_('select.option', MOLAJO_STATUS_UNPUBLISHED, MolajoText::_('MOLAJO_OPTION_STATUS_UNPUBLISHED'));
        $options[] = MolajoHTML::_('select.option', MOLAJO_STATUS_TRASHED, MolajoText::_('MOLAJO_OPTION_STATUS_TRASHED'));
        if ($this->parameters->def('config_component_state_spam', '0') == 1) {
            $options[] = MolajoHTML::_('select.option', MOLAJO_STATUS_SPAMMED, MolajoText::_('MOLAJO_OPTION_STATUS_SPAMMED'));
        }
        if ($this->parameters->def('config_component_version_management', '1') == 1) {
            $options[] = MolajoHTML::_('select.option', MOLAJO_STATUS_VERSION, MolajoText::_('MOLAJO_OPTION_STATUS_VERSION'));
        }
        $options[] = MolajoHTML::_('select.option', '*', MolajoText::_('MOLAJO_OPTION_ALL'));

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
        $validItems = $this->getOptions();

        /** loop thru **/
        $found = false;
        foreach ($validItems as $count => $validItem) {

            if ($this->value == $validItem->value) {
                $found = true;
                break;
            }

        }
        return $found;
    }

    /**
     * getQueryInformation
     * @param sstring $value
     * @return array
     */
    public function getQueryInformation($query, $value, $selectedState, $onlyWhereClause = false)
    {
        $query->select('a.state');
        if (is_numeric($value)) {
            $query->where('a.state = '.(int)$value);
        } else if ($value == '*') {

        } else {

            $query->where('a.state > -1');
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
            $render['link_value'] = false;
            $render['class'] = 'nowrap';
            $render['valign'] = 'top';
            $render['align'] = 'left';
            $render['sortable'] = true;
            $render['checkbox'] = false;
            $render['data_type'] = 'string';
            $render['column_name'] = 'state';
            $render['print_value'] = $item->state; //MolajoHTML::_('Mgrid.state', $item->state, $itemCount, $item->canEditstate);

            return $render;
        }
    }
}