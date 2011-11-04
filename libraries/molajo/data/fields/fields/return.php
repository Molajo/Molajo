<?php
/**
 * @version     $id: filterReturn.php
 * @package     Molajo
 * @subpackage  Filter
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  MolajoFieldReturn
 *
 *  Return Filter Field Handling
 *
 *  @package    Molajo
 *  @subpackage Filter
 *  @since      1.6
 */
class MolajoFieldReturn extends MolajoField
{
    /**
     *  __construct
     *
     *  Set Fieldname and Filter with parent
     */
    public function __construct() {
        parent::__construct();
        parent::setFieldname ('return');
        parent::setRequestFilter ('base64');
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
    public function getSelectedValue ()
    {
        /** retrieve and filter selected value **/
        parent::getSelectedValue ();

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
    public function validateRequestValue ()
    {
		if (empty($this->requestValue)) {
            return JURI::base();
        }

        if (JUri::isInternal(base64_decode($this->requestValue))) {
			return JURI::base();
		}

        return base64_decode($this->requestValue);
    }

    /**
    *  getQueryInformation
    *
    *  Returns Formatted Where clause for Query
    */
    public function getQueryInformation ($query, $value, $selectedState, $onlyWhereClause=false) {}

    /**
     *  render
     *
     *  sets formatting and content parameters
     */
    public function render ($layout, $item, $itemCount) {}

}