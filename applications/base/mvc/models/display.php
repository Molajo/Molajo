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
                        $this->db->nq($this->primary_prefix)
                            . '.'
                            . $this->db->nq($select));
                }
            }
        }

        /**
         *  From
         */
        $this->query->from(
            $this->db->nq($this->table)
                . ' as '
                . $this->db->nq($this->primary_prefix)
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
                                $this->db->nq($this->primary_prefix)
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
        if (isset($parameterArray[$this->primary_key])
            && (int)$parameterArray[$this->primary_key] > 0
        ) {
            $this->query->where(
                $this->db->nq($this->primary_prefix)
                    . '.'
                    . $this->db->nq($this->primary_key)
                    . ' = '
                    . (int)$parameterArray[$this->primary_key]
            );
        }

        if (isset($parameterArray['asset_type_id'])
            && (int)$parameterArray['asset_type_id'] > 0
        ) {
            $this->query->where(
                $this->db->nq($this->primary_prefix)
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
         *  Helper Methods requested by parameters
         */
        foreach ($methods as $method) {
            $this->query = $h->$method($this->query, $this->primary_prefix);
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
        /** default to all fields for primary table */
        if ($this->query->select == null) {
            $this->fields = $this->getFieldDatatypes();
            while (list($name, $value) = each($this->fields)) {
                $this->query->select(
                    $this->db->nq($this->primary_prefix)
                        . '.'
                        . $this->db->nq($name));
            }
        }

        /** primary table from clause */
        if ($this->query->from == null) {
            $this->query->from(
                $this->db->nq($this->table)
                    . ' as '
                    . $this->db->nq($this->primary_prefix)
            );
        }


        /** set the query */
        //echo $this->query->__toString().'<br />';

        $this->db->setQuery($this->query->__toString());
        $data = $this->db->loadObjectList();

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

    /**
     * _getAdditionalData
     *
     * Method to append additional data elements, as needed
     *
     * @param array $data
     *
     * @return array
     * @since  1.0
     */
    protected function _getAdditionalData($data = array())
    {
        $rowCount = 1;
        if (count($data) == 0) {
            return $data;
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
        foreach ($data as $item) {
            $keep = true;

            if (count($methodArray) > 0) {
                foreach ($methodArray as $method) {
                    if (method_exists($helperClass, $method)) {
                        $item = $h->$method($item, $this->parameters);
                    }
                }
            }
            // $this->dispatcher->trigger('queryBeforeItem', array(&$this->status, &$item, &$this->parameters, &$keep));

            /** item-specific task permissions
            $results = Services::Access()
                ->getUserItemPermissions(
                $tasklist,
                $asset_id
            );
             */
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
        return $data;
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
