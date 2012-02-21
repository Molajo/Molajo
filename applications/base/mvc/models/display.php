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
class MolajoDisplayModel extends MolajoCrudModel
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
        if (is_object(Molajo::Request()->parameters)) {
            $parameterArray = Molajo::Request()->parameters->toArray();
        } else {
            $parameterArray = array();
        }

        /** Primary table field names and prefix */
        $this->fields = $this->getFieldDatatypes();

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
            while (list($name, $value) = each($this->fields)) {
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
                if (isset($this->fields[$select])) {
                    $this->query->select(
                        $this->db->qn($this->primary_prefix)
                            . '.'
                            . $this->db->qn($select));
                }
            }
        }

        /**
         *  From
         */
        $this->query->from(
            $this->db->qn($this->table)
                . ' as '
                . $this->db->qn($this->primary_prefix)
        );

        if (isset($parameterArray['disable_view_access_check'])
            && $parameterArray['disable_view_access_check'] == 0
        ) {
            MolajoAccessService::setQueryViewAccess(
                $this->query,
                array('join_to_prefix' => $this->primary_prefix,
                    'join_to_primary_key' => $this->primary_key,
                    'asset_prefix' => $this->primary_prefix . '_assets',
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

                if (isset($this->fields[$field])) {
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
                            $datatype = explode(',', $this->fields[$field]);
                            if ($datatype[0] == 'int') {
                                $v = (int)$value;
                            } else {
                                $v = $this->db->q($value);
                            }
                            $this->query->where(
                                $this->db->qn($this->primary_prefix)
                                    . '.'
                                    . $this->db->qn($field)
                                    . ' = '
                                    . $v
                            );
                        }
                    }
                }
            }
        }

        /** Specific criteria */
        if (isset($parameterArray[$this->primary_key])
            && (int)$parameterArray[$this->primary_key] > 0
        ) {
            $this->query->where(
                $this->db->qn($this->primary_prefix)
                    . '.'
                    . $this->db->qn($this->primary_key)
                    . ' = '
                    . (int)$parameterArray[$this->primary_key]
            );
        }

        if (isset($parameterArray['asset_type_id'])
            && (int)$parameterArray['asset_type_id'] > 0
        ) {
            $this->query->where(
                $this->db->qn($this->primary_prefix)
                    . '.'
                    . $this->db->qn('asset_type_id')
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
         *  Helper Methods requested by parameters
         */
        foreach ($methods as $method) {
            $this->query = $h->$method(
                $this->query,
                $this->primary_prefix,
                $this->db
            );
        }

        /**
        $this->db->setQuery(
        $query,
        $this->getStart(),
        $this->getState('list.limit')
        );
         */
        return;
    }

    /**
     * runQuery
     *
     * Method to execute a prepared and set query statement,
     * returning the results
     *
     * @return  object
     * @since   1.0
     */
    public function runQuery()
    {
        return $this->loadObjectList();
    }

    /**
     * _getAdditionalData
     *
     * Method to append additional data elements, as needed
     *
     * @param array $this->data
     *
     * @return array
     * @since  1.0
     */
    protected function _getAdditionalData()
    {
        $rowCount = 1;
        if (count($this->data) == 0) {
            return $this->data;
        }

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

        if (is_object(Molajo::Request()->parameters)) {
            $parameterArray = Molajo::Request()->parameters->toArray();
        } else {
            $parameterArray = array();
        }

        $methodArray = array();
        if (isset($parameterArray['item_methods'])) {
            $temp = explode(',', $parameterArray['item_methods']);
            foreach ($temp as $method) {
                $methodArray[] = trim($method);
            }
        }

        /**
         *  Loop through query results
         */
        foreach ($this->data as $item) {
            $keep = true;

            if (count($methodArray) > 0) {
                foreach ($methodArray as $method) {
                    if (method_exists($helperClass, $method)) {
                        $item = $h->$method($item, $this->parameters);
                    }
                }
            }
            // $this->dispatcher->trigger('queryBeforeItem', array(&$this->status, &$item, &$this->parameters, &$keep));

            // $this->dispatcher->trigger('queryAfterItem', array(&$this->status, &$item, &$this->parameters, &$keep));

            /** process content plugins */
            //                $this->dispatcher->trigger('contentPrepare', array($this->context, &$item, &$this->parameters, $this->getState('list.start')));
            //$item->event = new stdClass();

            //                $results = $this->dispatcher->trigger(
            //                    'contentBeforeDisplay',
            //                    array($this->context,
            //                        &$item,
            //                        &$this->parameters,
            //                        $this->getState('list.start')
            //                   )
            //                );
            //$item->event->beforeDisplayContent = trim(implode("\n", $results));

            //                $results = $this->dispatcher->trigger('contentAfterDisplay', array($this->context, &$item, &$this->parameters, $this->getState('list.start')));
            //$item->event->afterDisplayContent = trim(implode("\n", $results));

            /** remove items so marked **/
            if ($keep === true) {
                $item->rowCount = $rowCount++;
            } else {
                unset($item);
            }
        }
        return $this->data;
    }

    /**
     * getPagination
     *
     * @return    array
     * @since    1.0
     */
    public function getPagination()
    {
        return $this->pagination;
    }
}
