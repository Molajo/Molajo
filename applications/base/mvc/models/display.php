<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Display
 *
 * Abstracted class used as the parent class for common display views
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class MolajoDisplayModel extends MolajoModel
{
    /**
     * _setQuery
     *
     * Method to create a query object in preparation of running a query
     *
     * @return  object
     * @since   1.0
     */
    protected function _setQuery()
    {
        $this->query = $this->db->getQuery(true);

        /**
         *  Model Helper
         */
        $extensionName = ExtensionHelper::formatNameForClass(
            $this->get('extension_instance_name')
        );
        $helperClass = 'Molajo' . $extensionName . 'ModelHelper';
        if (class_exists($helperClass)) {
        } else {
            $helperClass = 'MolajoModelHelper';
        }
        $h = new $helperClass();

        /**
         *  Parameter arrays - select, criteria
         *  Task array - specific selection criteria
         */
        $taskRequestArray = $this->task_request->toArray();
        $parameterArray = Molajo::Request()->merged_parameters->toArray();

        /** Primary table field names and prefix */
        $fields = $this->getFieldnames();
        $primary_prefix = 'a';

        /**
         *  Select
         */
        $selectArray = array();
        if (isset($parameterArray['select'])) {
            $selectArray = explode(',', trim($parameterArray['select']));
        }
        if (count($selectArray) > 0) {
        } else {
            /** default to all primary table fields */
            $selectArray = $fields;
        }

        foreach ($selectArray as $select) {
            $select = trim($select);
            $method = 'query' . $select;
            if (method_exists($helperClass, $method)) {
                $this->query = $h->$method($this->query, $primary_prefix);
            } else {
                if (in_array($select, $fields)) {
                    $this->query->select(
                        $this->db->nq($primary_prefix)
                            . '.'
                            . $this->db->nq($select));
                }
            }
        }

        /**
         *  From
         */
        $this->query->from($this->db->nq($this->table)
                . ' as '
                . $this->db->nq($primary_prefix)
        );

        if (isset($parameterArray['disable_view_access_check'])
            && $parameterArray['disable_view_access_check'] == 0
        ) {

            MolajoAccessService::setQueryViewAccess(
                $this->query,
                array('join_to_prefix' => $primary_prefix,
                    'join_to_primary_key' => 'id',
                    'asset_prefix' => $primary_prefix . '_assets',
                    'select' => true
                )
            );
        }

        /**
         *  Where
         */
        while (list($name, $value) = each($parameterArray)) {
            if (substr($name, 0, strlen('criteria_')) == 'criteria_') {
                $field = trim(substr($name, strlen('criteria_'), 999));
                if (in_array(trim($field), $fields)) {
                    $method = 'query' . ucfirst(trim($field));
                    if (method_exists($helperClass, $method)) {
                        $this->query = $h->$method($this->query, $primary_prefix);
                    } else {

                        if (trim($value) == '') {
                            //todo: amy $value = user session field;
                        }

                        if (trim($value == '')) {
                        } else {
                            $this->query->where(
                                $this->db->nq($primary_prefix)
                                    . '.'
                                    . $this->db->nq($field)
                                    . ' = '
                                    . $this->db->q($value)
                            );
                        }
                    }
                }
            }
        }

        /** Task array: specific criteria */
        if (isset($parameterArray->id)
            && (int)$parameterArray->id > 0
        ) {
            $this->query->where(
                $this->db->nq($primary_prefix)
                    . '.'
                    . $this->db->nq('id')
                    . ' = '
                    . (int)$parameterArray->id
            );
        }

        if (isset($parameterArray->asset_type_id)
            && (int)$parameterArray->asset_type_id > 0
        ) {
            $this->query->where(
                $this->db->nq($primary_prefix)
                    . '.'
                    . $this->db->nq('asset_type_id')
                    . ' = '
                    . (int)$parameterArray->asset_type_id
            );
        }

        /**
         *  Ordering
         */
        if (isset($parameterArray['ordering'])) {
            $this->query->ordering(trim($parameterArray['ordering']));
        }

        /** set the query */
        echo $this->query->__toString();
        $this->db->setQuery($this->query->__toString());

        return;
    }

    /**
     * _runQuery
     *
     * Method to execute a prepared and set query statement,
     * returning the results
     *
     * @return  object
     * @since   1.0
     */
    protected function _runQuery()
    {
        $data = $this->db->loadObjectList();
        var_dump($data);
        if ($this->db->getErrorNum() == 0) {

        } else {
            Services::Message()
                ->set(
                $message = Services::Language()->_('ERROR_DATABASE_QUERY') . ' ' .
                    $this->db->getErrorNum() . ' ' .
                    $this->db->getErrorMsg(),
                $type = MOLAJO_MESSAGE_TYPE_ERROR,
                $code = 500,
                $debug_location = $this->name . ':' . 'getData',
                $debug_object = $this->db
            );
            return $this->request->set('status_found', false);
        }

        if (count($data) == 0) {
            return array();
        }

        return $data;
    }
}
