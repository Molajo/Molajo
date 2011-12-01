<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen, Cristina Solano. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Supports an SQL select list of menu
 *
 * @package        Joomla.Framework
 * @subpackage    Form
 * @since        1.6
 */
class MolajoFormFieldSql extends MolajoFormFieldList
{
    /**
     * The form field type.
     *
     * @var        string
     * @since    1.6
     */
    public $type = 'SQL';

    /**
     * Method to get the field options.
     *
     * @return    array    The field option objects.
     * @since    1.6
     */
    protected function getOptions()
    {
        /** initialization **/
        $options = array();
        $db = MolajoFactory::getDBO();

        /** process field attributes **/
        $key = $this->element['key_field'] ? (string)$this->element['key_field'] : 'value';
        $value = $this->element['value_field'] ? (string)$this->element['value_field'] : (string)$this->element['name'];
        $translate = $this->element['translate'] ? (string)$this->element['translate'] : false;
        $query = (string)$this->element['query'];

        /** query **/
        $db->setQuery($query);
        $items = $db->loadObjectlist();
        if ($db->getErrorNum()) {
            MolajoError::raiseWarning(500, $db->getErrorMsg());
            return $options;
        }

        /** process results set **/
        if (!empty($items)) {

            /** translate values **/
            if ($translate == true) {
                $newitems = array();
                $i = 0;
                foreach ($items as $item) {
                    $newitems[$i]->$value = MolajoTextHelper::_($item->$value);
                    $newitems[$i]->$key = $item->$key;
                    $i++;
                }

                /** sort by translated value **/
                $items = array();
                sort($newitems);
                $items = $newitems;
            }

            /** load results into list **/
            foreach ($items as $item) {
                $options[] = MolajoHTML::_('select.option', $item->$key, $item->$value);
            }
        }

        /** merge in additional options **/
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
