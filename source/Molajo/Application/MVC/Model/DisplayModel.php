<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\MVC\Model;

use Molajo\Extension\Helper;
use Molajo\Application\Molajo;
use Molajo\Application\Services;

defined('MOLAJO') or die;

/**
 * Display
 *
 * Abstracted class used as the parent class for common display views
 *
 * @package   Molajo
 * @subpackage  Model
 * @since       1.0
 */
class DisplayModel extends ItemModel
{
    /**
     * __construct
     *
     * Constructor.
     *
     * @param  $id
     * @since  1.0
     */
    public function __construct($table = null, $id = null, $path = null)
    {
        return parent::__construct($table, $id, $path);
    }

    /**
     * setQuery
     *
     * Method to create a query object
     *
     * @return  object
     * @since   1.0
     */
    protected function setQuery()
    {
        /**
         *  Model Helper: MolajoExtensionModelHelper extends ModelHelper
         */
        $extensionName = ExtensionHelper::formatNameForClass(
            $this->get('extension_instance_name')
        );

        $helperClass = 'Molajo\\Extension\\Component\\' . $extensionName . '\\Helper\\ModelHelper';
        if (class_exists($helperClass)) {
        } else {
            $helperClass = 'Molajo\\Extension\\Helper\\' . 'ModelHelper';
        }
        $h = new $helperClass();

        /** collect unique parameter defined query methods and run at end */
        $methods = array();

        /**
         *  Parameters: merged in Render
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
            $this->db->qn($this->table_name)
                . ' as '
                . $this->db->qn($this->primary_prefix)
        );

        if (isset($parameterArray['disable_view_access_check'])
            && $parameterArray['disable_view_access_check'] == 0
        ) {
            Services::Access()->setQueryViewAccess(
                $this->query,
                $this->db,
                array('join_to_prefix' => $this->primary_prefix,
                    'join_to_primary_key' => $this->primary_key,
                    'catalog_prefix' => $this->primary_prefix . '_catalog',
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
                            $dataType = explode(',', $this->fields[$field]);
                            if ($dataType[0] == 'int') {
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

        if (isset($parameterArray['catalog_type_id'])
            && (int)$parameterArray['catalog_type_id'] > 0
        ) {
            $this->query->where(
                $this->db->qn($this->primary_prefix)
                    . '.'
                    . $this->db->qn('catalog_type_id')
                    . ' = '
                    . (int)$parameterArray['catalog_type_id']
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
     * getAdditionalData
     *
     * Method to append additional data elements, as needed
     *
     * @param array $this->query_results
     *
     * @return array
     * @since  1.0
     */
    protected function getAdditionalData()
    {
        $rowCount = 1;
        if (count($this->query_results) == 0) {
            return $this->query_results;
        }

        /**
         *  Model Helper: MolajoExtensionModelHelper extends ModelHelper
         */
        $extensionName = ExtensionHelper::formatNameForClass(
            $this->get('extension_instance_name')
        );

        $helperClass = 'Molajo\\Extension\\Component\\' . $extensionName . 'ModelHelper';
        if (class_exists($helperClass)) {
        } else {
            $helperClass = 'ModelHelper';
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
        foreach ($this->query_results as $item) {
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

            /** process content triggers */
            //                $this->dispatcher->trigger('contentPrepare', array($this->context, &$item, &$this->parameters, $this->getState('list.start')));
            //$item->event = new \stdClass();

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
                $item->row_count = $rowCount++;
            } else {
                unset($item);
            }
        }
        return $this->query_results;
    }

    /**
     * getPagination
     *
     * Method to get a JPagination object for the data set.
     *
     * @return    object    A JPagination object for the data set.
     * @since    1.0
     */
    public function getPagination()
    {
        /** get pagination id **/
        $store = $this->getStoreId('getPagination');

        /** if available, load from cache **/
        if (empty($this->cache[$store])) {
        } else {
            return $this->cache[$store];
        }

        /** pagination object **/
        $limit = (int)$this->getState('list.limit') - (int)$this->getState('list.links');
//        $page = new JPagination($this->getTotal(), $this->getStart(), $limit);
        $page = '';
        /** load cache **/
        $this->cache[$store] = $page;

        /** return from cache **/
        return $this->cache[$store];
    }

    /**
     * getTotal
     *
     * Method to get the total number of items for the data set.
     *
     * @return    integer
     * @since    1.0
     */
    public function getTotal()
    {
        /** cache **/
        $store = $this->getStoreId('getTotal');
        if (empty($this->cache[$store])) {

        } else {
            return $this->cache[$store];
        }

        /** get total of items returned from the last query **/
        $this->db->setQuery($this->queryStatement);
        $this->db->query();

        $total = (int)$this->db->getNumRows();

        if ($this->db->getErrorNum()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }

        /** load cache **/
        $this->cache[$store] = $total;

        /** return from cache **/
        return $this->cache[$store];
    }

    /**
     * getStart
     *
     * Method to get the starting number of items for the data set.
     *
     * @return    integer
     * @since    1.0
     */
    public function getStart()
    {
        /** cache **/
        $store = $this->getStoreId('getStart');
        if (empty($this->cache[$store])) {

        } else {
            return $this->cache[$store];
        }

        /** get list object **/
        $start = $this->getState('list.start');
        $limit = $this->getState('list.limit');
        $total = $this->getTotal();
        if ($start > $total - $limit) {
            $start = max(0, (int)(ceil($total / $limit) - 1) * $limit);
        }

        /** load cache **/
        $this->cache[$store] = $start;

        /** return from cache **/
        return $this->cache[$store];
    }
}
