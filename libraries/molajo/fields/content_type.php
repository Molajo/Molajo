<?php
/**
 * @version     $id: filterContentType.php
 * @package     Molajo
 * @subpackage  Filter
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

/**
 *  MolajoFieldContentType
 *
 *  ContentType Filter Field Handling
 *
 *  @package    Molajo
 *  @subpackage Filter
 *  @since      1.6
 */
class MolajoFieldContent_type extends MolajoField
{
    /**
     *  __construct
     *
     *  Set Fieldname and Filter with parent
     */
    public function __construct() {
        parent::__construct();
        parent::setFieldname ('content_type');
        parent::setRequestFilter ('integer');
    }

    /**
     *  getOptions
     *
     *  Returns Option Values
     */
    public function getOptions ()
    {
        $contentTypeModel = JModel::getInstance('ModelConfiguration', 'Molajo', array('ignore_request' => true));
        return $contentTypeModel->getOptionList (MOLAJO_CONFIG_OPTION_ID_CONTENT_TYPES);
    }

    /**
     *  getSelectedValue
     *
     *  Returns Selected Value
     */
    public function getSelectedValue ()
    {
        /** retrieve and filter selected value **/
        parent::getSelectedValue ();

        if ($this->requestValue == null) {
            return false;
        }

        /** validate and return **/
        return MolajoFieldContent_type::validateRequestValue();
    }

    /**
     *  validateRequestValue
     *
     *  Returns Selected Value
     */
    public function validateRequestValue ()
    {
        $contentTypeModel = JModel::getInstance('ModelConfiguration', 'Molajo', array('ignore_request' => true));
        return $contentTypeModel->validateID (JRequest::getCmd('option'), $this->requestValue);
    }

    /**
    *  getQueryInformation
    *
    *  Returns Formatted Where clause for Query
    */
    public function getQueryInformation ($query, $value, $selectedState, $onlyWhereClause=false)
    {
        if ($onlyWhereClause) {
        } else {
//            $query->select('a.content_type, contentTypeConfig.option_value_literal AS content_type_name');
//            $query->join('LEFT', '#__configuration AS contentTypeConfig ON contentTypeConfig.option_value = a.content_type AND contentTypeConfig.option_id = '. (int) MOLAJO_CONFIG_OPTION_ID_CONTENT_TYPES .' AND contentTypeConfig.component_option = "'.JRequest::getCmd('option').'"');
            $query->select('a.content_type');
        }

        if ((int) $value == 0) {
            return;
        } 
        $query->where('a.content_type = '. (int) $value);
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
            $render['data_type'] = 'string';
            $render['column_name'] = 'content_type';
            if ($item->created == 0) {
                $render['print_value'] = '';
            } else {
                $render['print_value'] = $item->content_type_name;
            }

            return $render;
        }
    }
}