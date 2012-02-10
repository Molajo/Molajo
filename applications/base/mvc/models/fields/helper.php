<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  Field Helper
 *
 * @package     Molajo
 * @subpackage  Filter
 * @since       1.0
 */
abstract class MolajoFieldHelper
{

    /**
     *  getValue
     *
     *  Returns a single value for the field
     */
    public function getValue()
    {
        $results = MolajoFieldHelper::_validateValue();
    }

    /**
     *  validateValue
     *
     *  Validates a single value for the field
     */
    protected function _validateValue()
    {

    }

    /**
     * getList
     *
     * Returns a list of values
     *
     * @param $listType
     * @param null $selected
     *
     * @return  mixed
     * @since   1.0
     */
    public function getList($listType, $parameters)
    {

        if ($listType == 'simplequery') {

            $results = MolajoFieldHelper::getSimpleQuery(
                $parameters['id'],
                $parameters['value'],
                $parameters['table']
            );
        }

    }

    /**
     * _getSimpleQuery
     *
     * @param $idField
     * @param $displayField
     * @param $tableName
     *
     * @return MolajoException
     * @since  1.0
     */
    protected function _getSimpleQuery($idField, $displayField, $tableName)
    {
        $db = Molajo::Services()->connect('jdb');
        $query = $db->getQuery(true);

        $query->select($db->namequote($idField));
        $query->select($db->namequote($displayField));
        $query->from($db->namequote($tableName));

        $db->setQuery($query->__toString());

        $results = $db->loadObjectList();

        if ($db->getErrorNum()) {
            return new MolajoException($db->getErrorMsg());
        }
        if (count($results) == 0) {
            //amy error;
        } else {
            return $results;
        }
    }
}
