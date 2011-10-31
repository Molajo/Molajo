<?php
/**
 * @package     Molajo
 * @subpackage  Attributes
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoAttributeApplication
 *
 * Populate Application Attribute
 *
 * @package     Molajo
 * @subpackage  Attributes
 * @since       1.0
 */
class MolajoAttributeApplication extends MolajoAttribute
{

    /**
     * __construct
     * 
	 * Method to instantiate the attribute object.
     * 
     * @param array $input
     * @param array $rowset
     * 
	 * @return  void
	 *
	 * @since   1.0
     */
	public function __construct($input = array(), $rowset = array())
	{
        parent::__construct();
        parent::__set('name', 'Application');
        parent::__set('input', $input);        
        parent::__set('rowset', $rowset); 
	}

	/**
     * setValue
     *
	 * Method to set the Attribute Value
	 *
	 * @return  array   $rowset
     *
	 * @since   1.1
	 */
	protected function setValue()
	{
        $application = $this->element['application'];
        $ids = explode(',', $this->element['application']);
        $list = '';
        foreach ($ids as $id) {
            if ((int) $id == 0) {
            } else if ($list == '') {
            } else {
               $list .= ', ';
            }
            if ((int) $id == 0) {
            } else {
               $list .= $id;
            }
        }
        $results = $this->verifyValue($list);

        /** $this->value */
        if ($results === false) {
        } else {
            $value = 'application="'.$results.'"';
        }
        parent::__set('value', $value);
        
        /** $this->rowset */
        $this->rowset[0]['application'] = $this->value;

        /** return array of attributes */
        return $this->rowset;
     }

	/**
     * verifyValue
     *
	 * Method to determine whether or not the Application exists
	 *
	 * @return  array   $rowset
     *
	 * @since   1.1
	 */
	protected function verifyValue($id)
	{
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.id AS value');
        $query->from('#__applications AS a');
        $query->where('a.id IN ('.(int) $id.')');

        $db->setQuery($query);

        $results = $db->loadObjectList();

        if ($error = $db->getErrorMsg()) {
            MolajoError::raiseWarning(500, $error);
            return false;
        }

        $returnValue = '';
        foreach ($results as $result) {
            if ($returnValue == '') {
            } else {
               $returnValue .= ', ';
            }
            $returnValue .= $result->value;
        }

        return $returnValue;
     }
}