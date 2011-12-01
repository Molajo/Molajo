<?php
/**
 * @package     Molajo
 * @subpackage  Attributes
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoAttributeAutocomplete
 *
 * Populate Autocomplete Attribute
 *
 * @package     Molajo
 * @subpackage  Attributes
 * @since       1.0
 */
class MolajoAttributeAutocomplete extends MolajoAttribute
{

    /**
     * __construct
     *
     * Method to instantiate the Autocomplete object.
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
        parent::__set('name', 'Autocomplete');
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
        $autocomplete = $this->element['autocomplete'];
        $value = $this->verifyValue($autocomplete);

        parent::__set('value', $value);

        /** $this->rowset */
        $this->rowset[0]['autocomplete'] = $this->value;

        /** return array of attributes */
        return $this->rowset;
    }

    /**
     * verifyValue
     *
     * Method to determine whether or not the Autocomplete exists
     *
     * @return  array   $rowset
     *
     * @since   1.1
     */
    protected function verifyValue($autocomplete)
    {
        if ((boolean)$autocomplete === true) {
            $value = 'autocomplete="autocomplete"';
        } else {
            $value = '';
        }
        return $value;
    }
}