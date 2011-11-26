<?php
/**
 * @version     $id: filterAlias.php
 * @package     Molajo
 * @subpackage  Filter
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  MolajoFieldAlias
 *
 *  Alias Filter Field Handling
 *
 * @package    Molajo
 * @subpackage Filter
 * @since      1.6
 */
class MolajoFieldAlias extends MolajoField
{
    /**
     *  __construct
     *
     *  Set Fieldname and Filter with parent
     */
    public function __construct()
    {
        parent::__construct();
        parent::setFieldname('alias');
        parent::setFilter('string');

    }

    /**
     *  getOptions
     *
     *  Returns Option Values
     */
    public function getOptions()
    {
        $aliasModel = JModel::getInstance('Model'.ucfirst(JRequest::getCmd('DefaultView')), ucfirst(JRequest::getCmd('DefaultView')), array('ignore_request' => true));
        return $aliasModel->getOptionList('alias', 'alias', $showKey = false, $showKeyFirst = false, $table = '');
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

    /**
     *  validateRequestValue
     *
     *  Returns Selected Value
     */
    public function validateRequestValue()
    {
        //return MolajoModelDisplay::validateValue('alias', $this->value, 'string', $table = null);
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
            $query->select('a.alias');
        }

        if (trim($value) == '') {
            return;
        } else {
            $query->where('a.alias = "'.$value.'"');
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
                $render['link_value'] = 'index.php?option='.JRequest::getVar('option').'&task=edit&id='.$item->id;
            } else {
                $render['link_value'] = false;
            }
            $render['class'] = 'nowrap';
            $render['valign'] = 'top';
            $render['align'] = 'left';
            $render['sortable'] = true;
            $render['checkbox'] = false;
            $render['data_type'] = 'string';
            $render['column_name'] = 'alias';
            $render['print_value'] = $item->alias;

            return $render;
        }
    }
}