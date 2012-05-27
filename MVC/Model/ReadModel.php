<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\MVC\Model;

use Molajo\Application;
use Molajo\Service\Services;
use Molajo\Service\Services\Configuration\ConfigurationService;

defined('MOLAJO') or die;

/**
 * ReadModel
 *
 * Properties set in the ModelController used in query development:
 *
 * model_name
 *     Ex., Articles, used with custom fields to create registry with data, ex. ArticlesParameters
 *
 * table_name
 *     Ex., #__content, used in the physical database query
 *
 * primary_key
 *     Ex., id, used to indicate single item requests
 *
 * name_key
 *     Ex., title or username, used to retrieve single item by unique value, not primary key
 *
 * primary_prefix
 *     Ex. "a", used in query development
 *
 * Indicators:
 *
 * get_customfields
 *     0: do not retrieve custom fields
 *     1: retrieve fields
 *     2: retrieve and return as "normal" columns
 *
 * get_item_children
 *     0: no
 *     1: yes - executes a new read for additional data, query results return as column
 *
 * use_special_joins
 *     0: no
 *     1: yes - adds joins defined by model
 *
 * check_view_level_access
 *     0: no
 *     1: yes - adds joins to catalog and primary table, verifies view access
 *
 * check_published
 *     0: no
 *     1: yes - adds check for published dates and status field
 *
 * process_triggers
 *     0: no
 *     1: yes - list of specific database triggers for this data source
 *
 * db
 *     typically 'JDatabase', but can be other data sources, like Messages, Registry, and Assets
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class ReadModel extends Model
{
	/**
	 * Constructor.
	 *
	 * @since  1.0
	 */
	public function __construct()
	{
		return parent::__construct();
	}

	/**
	 * setBaseQuery
	 *
	 * Retrieve all elements of the specific table for a specific item
	 *
	 * @param  $columns
	 * @param  $table_name
	 * @param  $primary_prefix
	 * @param  $primary_key
	 * @param  $id
	 * @param  $name_key
	 * @param  $id_name
	 *
	 * @return ReadModel
	 * @since  1.0
	 */
	public function setBaseQuery($columns, $table_name, $primary_prefix, $primary_key, $id, $name_key, $id_name)
	{
		if ($this->query->select == null) {
			foreach ($columns as $column) {
				$this->query->select($this->db->qn($primary_prefix . '.' . $column['name']));
			}
		}

		if ($this->query->from == null) {
			$this->query->from($this->db->qn($table_name) . ' as ' . $this->db->qn($primary_prefix));
		}

		if ($this->query->where == null) {
			if ((int)$id > 0) {
				$this->query->where($this->db->qn($primary_prefix . '.' . $primary_key) . ' = ' . $this->db->q($id));
			} else if (trim($id_name) == '') {
			} else {
				$this->query->where($this->db->qn($primary_prefix . '.' . $name_key) . ' = ' . $this->db->q($id_name));
			}
		}

		return $this;
	}

	/**
	 * addACLCheck
	 *
	 * Add ACL checking to the Query
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function addACLCheck($primary_prefix, $primary_key)
	{
		Services::Authorisation()->setQueryViewAccess(
			$this->query,
			$this->db,
			array('join_to_prefix' => $primary_prefix,
				'join_to_primary_key' => $primary_key,
				'catalog_prefix' => 'acl_check_catalog',
				'select' => true
			)
		);

		return $this;
	}

	/**
	 *
	 * //todo move this into a trigger since it has specific column names
	 * // modify it so that ACL extends status codes that are visible
	 *
	 * addPublishedCheck
	 *
	 * Standard Publish check on primary content
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function addPublishedCheck($primary_prefix)
	{
		$this->query->where($this->db->qn($primary_prefix)
			. '.' . $this->db->qn('status')
			. ' > ' . STATUS_UNPUBLISHED);

		$this->query->where('(' . $this->db->qn($primary_prefix)
				. '.' . $this->db->qn('start_publishing_datetime')
				. ' = ' . $this->db->q($this->nullDate)
				. ' OR ' . $this->db->qn($primary_prefix) . '.' . $this->db->qn('start_publishing_datetime')
				. ' <= ' . $this->db->q($this->now) . ')'
		);

		$this->query->where('(' . $this->db->qn($primary_prefix)
				. '.' . $this->db->qn('stop_publishing_datetime')
				. ' = ' . $this->db->q($this->nullDate)
				. ' OR ' . $this->db->qn($primary_prefix) . '.' . $this->db->qn('stop_publishing_datetime')
				. ' >= ' . $this->db->q($this->now) . ')'
		);

		return $this;
	}

	/**
	 * useSpecialJoins
	 *
	 * Use joins defined in table xml to extend model
	 *
	 * @return  array
	 * @since   1.0
	 */
	public function useSpecialJoins($joins, $primary_prefix)
	{
		echo 'joins'.$joins;
		var_dump($joins);
		foreach ($joins as $join) {

			$join_table = $join['table'];
			$alias = $join['alias'];
			$select = $join['select'];
			$joinTo = $join['jointo'];
			$joinWith = $join['joinwith'];

//echo  'JOIN: ' . $join_table . ' ' . $alias . ' ' . $select . ' ' . $joinTo . ' ' . $joinWith . '<br />';

			/* Join to table */
			if (trim($alias) == '') {
				$alias = substr($join_table, 3, strlen($join_table));
			}

			$this->query->from($this->db->qn($join_table) . ' as ' . $this->db->qn($alias));

			/* Select fields */
			if (trim($select) == '') {
				$selectArray = array();
			} else {
				$selectArray = explode(',', $select);
			}

			if (count($selectArray) > 0) {

				foreach ($selectArray as $selectItem) {

					$this->query->select(
						$this->db->qn(trim($alias) . '.' . trim($selectItem))
							. ' as ' .
							$this->db->qn(trim($alias) . '_' . trim($selectItem))
					);
				}
			}

			/* joinTo and joinWith Fields */
			$joinToArray = explode(',', $joinTo);
			$joinWithArray = explode(',', $joinWith);

			if (count($joinToArray) > 0) {

				$i = 0;
				foreach ($joinToArray as $joinToItem) {

					/** join THIS to that */
					$to = $joinToItem;

					if ($to == 'APPLICATION_ID') {
						$whereLeft = APPLICATION_ID;

					} else if ($to == 'SITE_ID') {
						$whereLeft = SITE_ID;


					} else if (is_numeric($to)) {
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

					if ($with == 'APPLICATION_ID') {
						$whereRight = APPLICATION_ID;

					} else if ($with == 'SITE_ID') {
						$whereRight = SITE_ID;

					} else if (is_numeric($with)) {
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
					$this->query->where($whereLeft . ' = ' . $whereRight);

					$i++;
				}
			}
		}

		return $this;
	}

	/**
	 * getQueryResults
	 *
	 * Execute query and returns an associative array of data elements
	 *
	 * @return  array
	 * @since   1.0
	 */
	public function getQueryResults($columns)
	{
		echo '<br /><br />' . $this->query->__toString() . '<br /><br />';

		/**
		if ($id == 100) {
		echo '<br /><br />'.$this->query->__toString().'<br /><br />';
		die;
		};
		 */
		/** Run the query */
		$this->db->setQuery($this->query->__toString());

		$this->query_results = $this->db->loadObjectList();

		if (empty($this->query_results)) {

			$this->query_results = array();

			foreach ($columns as $column) {
				$this->query_results[$column['name']] = '';
			}
		}

		return $this;
	}

	/**
	 * addCustomFields
	 *
	 * Populate the custom fields defined by the Table xml with query results
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function addCustomFields($model_name, $customFieldName, $fields, $retrieval_method, $query_results)
	{
		//todo: get rid of array logic when result, item, and items work is done

		/** Prepare Registry Name */
		$customFieldName = strtolower($customFieldName);
		$useRegistryName = $model_name . ucfirst($customFieldName);

		/** See if there are query results for this Custom Field Group */
		if (is_object($query_results) && isset($query_results->$customFieldName)) {

			$jsonData = $query_results->$customFieldName;

			$data = json_decode($jsonData);

			/** test for application-specific values */
			if (count($data) > 0
				&& (defined('APPLICATION_ID'))
			) {
				foreach ($data as $key => $value) {

					if ($key == APPLICATION_ID) {
						$data = $value;
						break;
					}
				}
			}

			/** Place queryresults data for custom field group into named pair array */
			$lookup = array();

			if (count($data) > 0) {
				foreach ($data as $key => $value) {
					$lookup[$key] = $value;
				}
			}

			if (is_object($query_results) && isset($query_results->$customFieldName)) {
				unset($query_results->$customFieldName);
			}

		} else {

			$data = array();
			$lookup = array();
		}

		/** Process each of the Custom Field Group Definitions for Query Results or defaults */
		foreach ($fields as $f) {

			$name = $f['name'];
			$name = strtolower($name);

			$default = null;
			if (isset($f['default'])) {
				$default = $f['default'];
			}

			if ($default == '') {
				$default = null;
			}

			/** Use value, if exists, or defined default */
			if (isset($lookup[$name])) {
				$setValue = $lookup[$name];
			} else {
				$setValue = $default;
			}

			/** Filter Input and Save the Registry */
			//$set = $this->filterInput($name, $set, $dataType, $null, $default);

			if ($retrieval_method == 2) {
				$query_results->$name = $setValue;
			} else {
//echo $useRegistryName.' '. $name.' '.$setValue.'<br /> ';
				Services::Registry()->set($useRegistryName, $name, $setValue);
			}
		}

		return $query_results;
	}

	/**
	 * addItemChildren
	 *
	 * Method to append additional data elements needed to the standard
	 * array of elements provided by the data source
	 *
	 * @return array
	 * @since  1.0
	 */
	public function addItemChildren($children, $id, $query_results)
	{
		foreach ($children as $child) {

			$name = (string)$child['name'];
			$name = ucfirst(strtolower($name));

			$m = Application::Controller()->connect($name, 'Table');

			$join = (string)$child['join'];
			$this->query->where($this->db->qn($join) . ' = ' . (int)$id);

			$results = $m->getData('list');

			$query_results->$name = $results;
		}

		/** return array containing primary query and additional data elements */
		return $query_results;
	}

	/**
	 * store
	 *
	 * Method to store a row (insert: no PK; update: PK) in the database.
	 *
	 * @param   boolean True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 * @since   1.0
	 */
	public function store($id, $table_name, $primary_key)
	{
		/**
		echo '<pre>';
		var_dump($this->row);
		echo '</pre>';
		 */
		if ((int)$id == 0) {
			$stored = $this->db->insertObject(
				$table_name, $this->row, $primary_key);
		} else {
			$stored = $this->db->updateObject(
				$table_name, $this->row, $primary_key);
		}

		if ($stored) {

		} else {

//			throw new \Exception(
//				. ' '. $this->db->getErrorMsg()
//			);
		}
		/**
		if ($this->_locked) {
		$this->_unlock();
		}
		 */

		return true;
	}
}
