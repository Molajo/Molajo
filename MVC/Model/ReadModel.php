<?php
/**
 * Read Model
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\MVC\Model;

use Molajo\Frontcontroller;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Read Model is driven by the primary Controller to use with model registries, to create and execute DB queries
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
class ReadModel extends Model
{
    /**
     * setBaseQuery
     *
     * Retrieve all elements of the specific table for a specific item
     *
     * @param   array   $columns
     * @param   string  $table_name
     * @param   string  $primary_prefix
     * @param   string  $primary_key
     * @param   int     $id
     * @param   string  $name_key
     * @param   int     $name_key_value
     * @param   string  $query_object - item, list, result, distinct
     * @param   array   $criteria_array
     *
     * @return  ReadModel
     * @since   1.0
     */
    public function setBaseQuery(
        $columns,
        $table_name,
        $primary_prefix,
        $primary_key,
        $id,
        $name_key,
        $name_key_value,
        $query_object,
        $criteria_array = array()
    ) {

        if ($this->query->select == null) {

            if ($query_object == QUERY_OBJECT_RESULT) {

                if ((int)$id > 0) {

                    $this->query->select($this->db->qn($primary_prefix . '.' . $name_key));

                    $this->query->where(
                        $this->db->qn($primary_prefix . '.' . $primary_key)
                            . ' = ' . $this->db->q($id)
                    );

                } else {

                    $this->query->select($this->db->qn($primary_prefix . '.' . $primary_key));

                    $this->query->where(
                        $this->db->qn($primary_prefix . '.' . $name_key)
                            . ' = ' . $this->db->q($name_key_value)
                    );
                }

            } else {

                $first = true;

                if (count($columns) == 0) {

                    $this->query->select($this->db->qn($primary_prefix) . '.' . '*');

                } else {
                    foreach ($columns as $column) {

                        if ($first === true && strtolower(trim($query_object)) == QUERY_OBJECT_DISTINCT) {

                            $first = false;
                            $this->query->select('DISTINCT ' . $this->db->qn($primary_prefix . '.' . $column['name']));

                        } else {
                            $this->query->select($this->db->qn($primary_prefix . '.' . $column['name']));
                        }
                    }
                }
            }
        }

        if ($this->query->from == null) {
            $this->query->from(
                $this->db->qn($table_name)
                    . ' as '
                    . $this->db->qn($primary_prefix)
            );
        }

        if ($this->query->where == null) {
            if ((int)$id > 0) {
                $this->query->where(
                    $this->db->qn($primary_prefix . '.' . $primary_key)
                        . ' = ' . $this->db->q($id)
                );

            } elseif (trim($name_key_value) == '') {

            } else {
                $this->query->where(
                    $this->db->qn($primary_prefix . '.' . $name_key)
                        . ' = ' . $this->db->q($name_key_value)
                );
            }
        }

        if (is_array($criteria_array) && count($criteria_array) > 0) {

            foreach ($criteria_array as $item) {
                if (isset($item['value'])) {
                    $this->query->where(
                        $this->db->qn($item['name'])
                            . ' ' . $item['connector'] . ' '
                            . $this->db->q($item['value'])
                    );

                } elseif (isset($item['name2'])) {
                    $this->query->where(
                        $this->db->qn($item['name'])
                            . ' ' . $item['connector'] . ' '
                            . $this->db->qn($item['name2'])
                    );
                }
            }
        }

        return $this;
    }

    /**
     * Add View Permission Verification to the Query
     *
     * Note: When Language query runs, Permissions Service is not yet available.
     *
     * @param   $primary_prefix
     * @param   $primary_key
     * @param   $query_object
     *
     * @return  ReadModel
     * @since   1.0
     */
    public function checkPermissions($primary_prefix, $primary_key, $query_object)
    {
        if ($query_object == QUERY_OBJECT_RESULT) {
            $select = false;
        } else {
            $select = true;
        }

// when language query runs, Services is not yet defined
        Services::Permissions()->setQueryViewAccess(
            $this->query,
            $this->db,
            array(
                'join_to_prefix'      => $primary_prefix,
                'join_to_primary_key' => $primary_key,
                'catalog_prefix'      => 'check_permissions_catalog',
                'select'              => $select
            )
        );

        return $this;
    }

    /**
     * Uses joins defined in model registry to build SQL statements
     *
     * @param   array   $joins
     * @param   string  $primary_prefix
     * @param   string  $query_object - result, item, list, or distinct
     *
     * @return  ReadModel
     * @since   1.0
     */
    public function useSpecialJoins($joins, $primary_prefix, $query_object)
    {
        $menu_id         = (int)$this->get('menu_id', 0);
        $catalog_type_id = (int)$this->get('catalog_type_id', 0);

        foreach ($joins as $join) {

            $join_table = $join['table_name'];
            $alias      = $join['alias'];
            $select     = $join['select'];
            $joinTo     = $join['jointo'];
            $joinWith   = $join['joinwith'];

            $this->query->from(
                $this->db->qn($join_table)
                    . ' as '
                    . $this->db->qn($alias)
            );

            /* Select fields */
            if (trim($select) == '') {
                $selectArray = array();
            } else {
                $selectArray = explode(',', $select);
            }

            if ($query_object == QUERY_OBJECT_RESULT) {
            } else {

                if (count($selectArray) > 0) {

                    foreach ($selectArray as $selectItem) {

                        $this->query->select(
                            $this->db->qn(trim($alias) . '.' . trim($selectItem))
                                . ' as ' .
                                $this->db->qn(trim($alias) . '_' . trim($selectItem))
                        );
                    }
                }
            }

            /* joinTo and joinWith Fields */
            $joinToArray   = explode(',', $joinTo);
            $joinWithArray = explode(',', $joinWith);

            if (count($joinToArray) > 0) {

                $i = 0;
                foreach ($joinToArray as $joinToItem) {

                    /** join THIS to that */
                    $to = $joinToItem;

                    if (defined('APPLICATION_ID') && $to == 'APPLICATION_ID') {
                        $whereLeft = APPLICATION_ID;

                    } elseif ($to == 'SITE_ID') {
                        $whereLeft = SITE_ID;

                    } elseif ($to == 'MENU_ID') {
                        $whereLeft = (int)$menu_id;

                    } elseif ($to == 'CATALOG_TYPE_ID') {
                        $whereLeft = (int)$catalog_type_id;

                    } elseif (is_numeric($to)) {
                        $whereLeft = (int)$to;

                    } else {

                        $hasAlias = explode('.', $to);

                        if (count($hasAlias) > 1) {
                            $toJoin = trim($hasAlias[0]) . '.' . trim($hasAlias[1]);
                        } else {
                            $toJoin = trim($alias) . '.' . trim($to);
                        }

                        $whereLeft = $this->db->qn($toJoin);
                    }

                    /** join this to THAT */
                    $with = $joinWithArray[$i];

                    $operator = '=';
                    if (substr($with, 0, 2) == '>=') {
                        $operator = '>=';
                        $with     = substr($with, 2, strlen($with) - 2);

                    } elseif (substr($with, 0, 1) == '>') {
                        $operator = '>';
                        $with     = substr($with, 0, strlen($with) - 1);

                    } elseif (substr($with, 0, 2) == '<=') {
                        $operator = '<=';
                        $with     = substr($with, 2, strlen($with) - 2);

                    } elseif (substr($with, 0, 1) == '<') {
                        $operator = '<';
                        $with     = substr($with, 0, strlen($with) - 1);
                    }

                    if (defined('APPLICATION_ID') && $with == 'APPLICATION_ID') {
                        $whereRight = APPLICATION_ID;

                    } elseif ($with == 'SITE_ID') {
                        $whereRight = SITE_ID;

                    } elseif ($with == 'MENU_ID') {
                        $whereLeft = (int)$menu_id;

                    } elseif ($with == 'CATALOG_TYPE_ID') {
                        $whereLeft = (int)$catalog_type_id;

                    } elseif (is_numeric($with)) {
                        $whereRight = (int)$with;

                    } else {

                        $hasAlias = explode('.', $with);

                        if (count($hasAlias) > 1) {
                            $withJoin = trim($hasAlias[0]) . '.' . trim($hasAlias[1]);
                        } else {
                            $withJoin = trim($primary_prefix) . '.' . trim($with);
                        }

                        $whereRight = $this->db->qn($withJoin);
                    }

                    /** put the where together */
                    $this->query->where($whereLeft . $operator . $whereRight);

                    $i++;
                }
            }
        }

        return $this;
    }

    /**
     * Add Model Criteria to Query
     *
     * @param   $catalog_type_id
     * @param   $extension_instance_id
     * @param   $primary_prefix
     * @param   $status
     *
     * @return  ReadModel
     * @since   1.0
     */
    public function setModelCriteria($catalog_type_id, $extension_instance_id, $primary_prefix, $status = null)
    {
        if ((int)$catalog_type_id == 0) {
        } else {
            $this->query->where(
                $this->db->qn($primary_prefix . '.' . 'catalog_type_id')
                    . ' = ' . (int)$catalog_type_id
            );
        }

        /**
        if ((int) $extension_instance_id == 0) {
        } else {
        $this->query->where($this->db->qn($primary_prefix . '.' . 'extension_instance_id')
        . ' = ' . (int) $extension_instance_id);
        }

        if ($status === null) {
        } else {
        $this->query->where($this->db->qn($primary_prefix . '.' . 'status')
        . ' IN ' .
        ' (' . $status . ') ');
        }
         */

        return $this;
    }

    /**
     * getQueryResults - Execute query and returns an associative array of data elements
     *
     * @param   string   $query_object - four valid values: result, item, list, distinct
     * @param   int      $offset
     * @param   int      $count
     * @param   int      $use_pagination
     *
     * @return  int      count of total rows for pagination
     * @since   1.0
     */
    public function getQueryResults($query_object, $offset = 0, $count = 5, $use_pagination = 0)
    {
        $this->query_results = array();

        if ($query_object == QUERY_OBJECT_RESULT ||
            $query_object == QUERY_OBJECT_ITEM ||
            $query_object == QUERY_OBJECT_LIST ||
            $query_object == QUERY_OBJECT_DISTINCT
        ) {
        } else {
            $query_object = QUERY_OBJECT_LIST;
        }

        if ($offset == 0 && $count == 0) {
            if ($query_object == QUERY_OBJECT_RESULT) {
                $offset         = 0;
                $count          = 1;
                $use_pagination = 0;

            } elseif ($query_object == QUERY_OBJECT_DISTINCT) {
                $offset         = 0;
                $count          = 999999;
                $use_pagination = 0;

            } else {
                $offset         = 0;
                $count          = 15;
                $use_pagination = 1;
            }
        }
        /**
        echo  'Offset ' . $offset . ' Count ' . $count . ' Use Pagination ' . $use_pagination . '<br />';
        echo '<br /><br /><pre>';
        $string = $this->query->__toString();
        echo str_replace('#__', 'molajo_', $string);
        echo '</pre><br /><br />';
         */
        $cache_key = $this->query->__toString();

        $cached_output = Services::Cache()->get('Query', $cache_key);

        if ($query_object == QUERY_OBJECT_LIST) {
        } else {
            $use_pagination = 0;
        }

        if ($cached_output === false) {

            if ((int)$use_pagination === 0) {
                $query_offset = $offset;
                $query_count  = $count;

            } else {
                $query_offset = 0;
                $query_count  = 99999999;
            }

            $this->db->setQuery($this->query->__toString(), $query_offset, $query_count);

            if ($query_object == QUERY_OBJECT_RESULT) {
                $results = $this->db->loadResult();
            } else {
                $results = $this->db->loadObjectList();
            }
//echo '<pre>';
//echo 'Query results ';
//var_dump($results);
//echo '</pre>';

            Services::Cache()->set('Query', $cache_key, $results);

        } else {

            $results = $cached_output;
        }

        $total = count($results);

        if ($offset > $total) {
            $offset = 0;
        }

        if ($use_pagination === 0
            || (int)$total === 0
        ) {
            $this->query_results = $results;

            return $total;
        }

        $countOfOffset  = 0;
        $countOfResults = 0;

        foreach ($results as $item) {

            /** Read past offset */
            if ($countOfOffset < $offset) {
                $countOfOffset++;

                /** Collect next set for pagination */
            } elseif ($countOfResults < $count) {
                $this->query_results[] = $item;
                $countOfResults++;

                /** Offset and Results set collected. Exit. */
            } else {
                break;
            }

        }

        return $total;
    }

    /**
     * Populate query results with custom fields and values
     *
     * @param   string  $model_registry_name
     * @param   string  $customFieldName
     * @param   array   $fields
     * @param   string  $retrieval_method
     * @param   object  $query_results
     * @param   string  $query_object
     *
     * @return  mixed
     * @since   1.0
     */
    public function addCustomFields(
        $model_registry_name,
        $customFieldName,
        $fields,
        $retrieval_method,
        $query_results,
        $query_object
    ) {

        $customFieldName  = strtolower($customFieldName);
        $useModelRegistry = $model_registry_name . ucfirst($customFieldName);

        /** See if there are query results for this Custom Field Group */
        if (is_object($query_results) && isset($query_results->$customFieldName)) {
            $jsonData = $query_results->$customFieldName;
            $data     = json_decode($jsonData);

            /** test for application-specific values */
            if (count($data) > 0 && (defined('APPLICATION_ID'))) {

                foreach ($data as $key => $value) {
                    if ($key == APPLICATION_ID) {
                        $data = $value;
                        break;
                    }
                }
            }

            /** Inject data for custom field group into named pairs array */
            $lookup = array();

            if (count($data) > 0) {
                foreach ($data as $key => $value) {
                    $lookup[$key] = $value;
                }
            }

        } else {
            /** No data in query results for this specific custom field */
            $data   = array();
            $lookup = array();
        }

        /** Process each of the Custom Field Group Definitions for Query Results or defaults */
        foreach ($fields as $f) {

            //@todo remove hack testing name - addition elements added on in controller incorrectly show in this array
            if (isset($f['name'])) {
                $name = $f['name'];
                $name = strtolower($name);

                $default = null;
                if (isset($f['default'])) {
                    $default = $f['default'];
                }

                if ($default == '') {
                    $default = null;
                }

                if (isset($lookup[$name])) {
                    $setValue = $lookup[$name];
                } else {
                    $setValue = $default;
                }

                if ($retrieval_method == 1 && $query_object == QUERY_OBJECT_ITEM) {
                    Services::Registry()->set($useModelRegistry, $name, $setValue);

                } else {
                    if (strtolower($customFieldName) == PARAMETERS_LITERAL
                        || strtolower($customFieldName) == 'Metadata'
                    ) {
                        $name = strtolower($customFieldName) . '_' . $name;
                    }
                    $query_results->$name = $setValue;
                }
            }
        }

        return $query_results;
    }

    /**
     * Append additional rows of child data as defined by the Model Registry
     *
     * @param   bool     $children
     * @param   integer  $id
     * @param   array    $query_results
     *
     * @return  mixed
     * @since   1.0
     *
     * @return  object
     */
    public function addItemChildren($children, $id, $query_results)
    {
        $controllerClass = CONTROLLER_CLASS_NAMESPACE;

        foreach ($children as $child) {

            $model_name = (string)$child['name'];
            $model_name = ucfirst(strtolower($model_name));

            $model_type = (string)$child['type'];
            $model_type = ucfirst(strtolower($model_type));

            $controller = new $controllerClass();
            $controller->getModelRegistry($model_type, $model_name, 1);

            $join              = (string)$child['join'];
            $joinPrimaryPrefix = $controller->get('primary_prefix', 'a', 'model_registry');

            $controller->model->query->where(
                $controller->model->db->qn($joinPrimaryPrefix . '.' . $join)
                    . ' = ' . (int)$id
            );

            $results                    = $controller->getData(QUERY_OBJECT_LIST);
            $query_results->$model_name = $results;

            unset ($controller);
        }

        return $query_results;
    }
}
