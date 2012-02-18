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
     * Method to create a query object
     *
     * @return  object
     * @since   1.0
     */
    protected function _setQuery()
    {
        $this->query = $this->db->getQuery(true);

        /**
         *  Model Helper: MolajoExtensionModelHelper extends MolajoModelHelper
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

        /** collect unique parameter defined query methods and run at end */
        $methods = array();

        /**
         *  Parameters: merged in MolajoRenderer
         */
        $parameterArray = Molajo::Request()->parameters->toArray();

        /** Primary table field names and prefix */
        $fields = $this->getFieldDatatypes();
        $primary_prefix = 'a';

        /**
         *  Select
         */
        $selectArray = array();
        if (isset($parameterArray['select'])) {
            $temp = explode(',', $parameterArray['select']);
            foreach ($temp as $select) {
                $selectArray[] = trim($select);
            }
        }
        if (count($selectArray) > 0) {
        } else {
            /** default to all primary table fields */
            while (list($name, $value) = each($fields)) {
                $selectArray[] = $name;
            }
        }

        foreach ($selectArray as $select) {
            $method = 'query' . ucfirst(strtolower($select));
            if (method_exists($helperClass, $method)) {
                if (in_array($method, $methods)) {
                } else {
                    $methods[] = $method;
                }
            } else {
                if (isset($fields[$select])) {
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
                if (isset($fields[$field])) {
                    $method = 'query' . ucfirst(strtolower($field));
                    if (method_exists($helperClass, $method)) {
                        if (in_array($method, $methods)) {
                        } else {
                            $methods[] = $method;
                        }
                    } else {

                        if (trim($value) == '') {
                            //todo: amy $value = user session field;
                        }

                        if (trim($value == '')) {
                        } else {
                            $datatype = explode(',', $fields[$field]);
                            if ($datatype[0] == 'int') {
                                $v = (int) $value;
                            } else {
                                $v = $this->db->q($value);
                            }
                            $this->query->where(
                                $this->db->nq($primary_prefix)
                                    . '.'
                                    . $this->db->nq($field)
                                    . ' = '
                                    . $v
                            );
                        }
                    }
                }
            }
        }

        /** Specific criteria */
        if (isset($parameterArray['id'])
            && (int)$parameterArray['id'] > 0
        ) {
            $this->query->where(
                $this->db->nq($primary_prefix)
                    . '.'
                    . $this->db->nq('id')
                    . ' = '
                    . (int)$parameterArray['id']
            );
        }

        if (isset($parameterArray['asset_type_id'])
            && (int)$parameterArray['asset_type_id'] > 0
        ) {
            $this->query->where(
                $this->db->nq($primary_prefix)
                    . '.'
                    . $this->db->nq('asset_type_id')
                    . ' = '
                    . (int)$parameterArray['asset_type_id']
            );
        }

        //todo: amy category id

        /**
         *  Ordering
         */
        if (isset($parameterArray['ordering'])) {
            $this->query->ordering(trim($parameterArray['ordering']));
        }

        /**
         *  process parameter requested query helper methods
         */
        foreach ($methods as $method) {
            $this->query = $h->$method($this->query, $primary_prefix);
        }

        /** set the query */
//echo $this->query->__toString();
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
        //var_dump($data);
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
