<?php
/**
 * @version     $id: filterTitle.php
 * @package     Molajo
 * @subpackage  Filter
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  MolajoFieldTitle
 *
 *  Title Filter Field Handling
 *
 *  @package    Molajo
 *  @subpackage Filter
 *  @since      1.6
 */
class MolajoFieldTitle extends MolajoField
{
    /**
     *  __construct
     *
     *  Set Fieldname and Filter with parent
     */
    public function __construct() {
        parent::__construct();
        parent::setFieldname ('title');
        parent::setRequestFilter ('integer');

    }

    /**
     *  getOptions
     *
     *  Returns Option Values
     */
    public function getOptions ()
    {
        $titleModel = JModel::getInstance('Model'.ucfirst(JRequest::getCmd('DefaultView')), ucfirst(JRequest::getCmd('DefaultView')), array('ignore_request' => true));
        return $titleModel->getOptionList('id', 'title', $showKey = true, $showKeyFirst = false, $table = '');
    }

    /**
     *  getSelectedValue
     *
     *  Returns Selected Value
     */
    public function getSelectedValue ()
    {
        parent::getSelectedValue ();

        if ($this->requestValue == null) {
            return false;
        }

        /** validate to list **/
        $results = MolajoFieldTitle::validateRequestValue();
        if ($results === false) {
            return false;
        }  else {
            return $this->requestValue;
        }
    }

    /**
     *  validateRequestValue
     *
     *  Returns Selected Value
     */
    public function validateRequestValue ()
    {
        $titleModel = JModel::getInstance('Model'.ucfirst(JRequest::getCmd('DefaultView')), ucfirst(JRequest::getCmd('DefaultView')), array('ignore_request' => true));
        return $titleModel->validateValue('id', $this->requestValue, 'numeric', null);
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
            $query->select('a.title');
        }

        if ((int) $value == 0) {
            return;
        }
        if ($selectedState == '*' || $selectedState == MOLAJO_STATE_VERSION) {
            $query->where('(a.id = '. (int) $value.' OR a.version_of_id = '. (int) $value.')');
        } else {
            $query->where('a.id = '. (int) $value);
        }
        
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
            $render['class'] = 'nowrap';
            $render['valign'] = 'top';
            $render['align'] = 'left';
            $render['sortable'] = true;
            $render['checkbox'] = false;
            $render['data_type'] = 'string';
            $render['column_name'] = 'title';

            if ($item->canEdit === true) {
                $render['link_value'] = 'index.php?option='.JRequest::getVar('option').'&task=edit&id='.$item->id;
                $render['print_value'] = $item->title;
            } else {
                $render['link_value'] = false;
                $render['print_value'] = $item->title;
            }

            return $render;
        }
    }
}