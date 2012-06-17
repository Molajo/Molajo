<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Admingrid;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class AdmingridTrigger extends ContentTrigger
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * getInstance
	 *
	 * @static
	 * @return bool|object
	 * @since  1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new AdmingridTrigger();
		}

		return self::$instance;
	}

	/**
	 * Before-read processing
	 *
	 * Prepares data for the Administrator Grid  - position AdmingridTrigger last
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onAfterRoute()
	{
		/** Is this an Administrative Grid Request?  */
		if (strtolower($this->get('template_view_path_node')) == 'admingrid') {
		} else {
			return true;
		}

		/** Data Source Connection */
		$model_type = $this->get('model_type');
		$model_name = $this->get('model_name');

		$controllerClass = 'Molajo\\MVC\\Controller\\ModelController';
		$connect = new $controllerClass();

		$results = $connect->connect($model_type, $model_name);
		if ($results == false) {
			return false;
		}

		$table_name = $connect->get('table_name');

		$primary_prefix = $connect->get('primary_prefix');
		$primary_key = $connect->get('primary_key');
		$name_key = $connect->get('name_key');

		/** URL */
		$url = Services::Registry()->get('Configuration', 'application_base_url');

		if (Services::Registry()->get('Configuration', 'url_sef') == 1) {
			$url .= '/' . $this->get('catalog_url_sef_request');
			$connector = '?';
		} else {
			$url .= '/' . $this->get('catalog_url_request');
			$connector = '&';
		}

		Services::Registry()->set('Trigger', 'PageURL', $url);
		Services::Registry()->set('Trigger', 'PageURLConnector', $connector);

		/**  Create Toolbar Registry, including links, button names, and ACL verification */
		$this->setToolbar($url, $connector);

		/**  Create Filter lists and store in Trigger registry */
		$this->setFilter($connect, $primary_prefix);

		/**  Create Grid Query and save results in Trigger registry */
		$this->setGrid($connect, $primary_prefix, $table_name);

		/**  Create Pagination data and store in Trigger registry */
		$this->setPagination($url, $connector);

		return true;
	}

	/**
	 * Create Toolbar Registry, including links and button names, based on User's Access Settings
	 *
	 * @return boolean
	 * @since  1.0
	 */
	protected function setToolbar($url, $connector)
	{
		$grid_toolbar_buttons = explode(',', $this->get('grid_toolbar_buttons',
				'new,edit,publish,feature,archive,checkin,restore,delete,trash,options')
		);

		$permissions = Services::Authorisation()->authoriseTaskList(
			$grid_toolbar_buttons,
			$this->get('extension_catalog_id')
		);

		$query_results = array();

		foreach ($grid_toolbar_buttons as $buttonname) {

			if ($permissions[$buttonname] == true) {

				$row = new \stdClass();
				$row->name = Services::Language()->translate(strtoupper('TASK_' . strtoupper($buttonname) . '_BUTTON'));
				$row->action = $buttonname;
				$row->link = $url . '&action=' . $row->action;

				$query_results[] = $row;
			}
		}

		if (Services::Registry()->get('Trigger', 'grid_search', 1) == 1) {
			$row = new \stdClass();
			$row->name = Services::Language()->translate(strtoupper('TASK_' . 'SEARCH' . '_BUTTON'));
			$row->action = 'search';
			$row->link = $url . '&action=search';

			$query_results[] = $row;
		}

		Services::Registry()->set('Trigger', 'AdminToolbar', $query_results);

		return true;
	}

	/**
	 * Creates and stores Filters in Trigger registry
	 * Sets where clauses for selected values for primary grid query
	 *
	 * @param   $connect
	 * @param   $primary_prefix
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	protected function setFilter($connect, $primary_prefix)
	{
		$grid_list = explode(',', $this->get(
				'grid_list', 'catalog_type_id,created_by,featured,status')
		);

		$lists = array();

		if (is_array($grid_list) && count($grid_list) > 0) {

			/** Build each list and store in registry along with current selection */
			foreach ($grid_list as $requested_listname) {

				$fieldValue = Services::Text()->getList($requested_listname, $this->parameters);

				if ($fieldValue == false) {
				} else {

					ksort($fieldValue);

					/** todo: Retrieve selected field from request */
					$selected = '';

					$query_results = array();
					foreach ($fieldValue as $item) {

						$row = new \stdClass();

						$row->listname = $requested_listname;
						$row->id = $item->id;
						$row->value = $item->value;

						if ($row->id == $selected) {
							$row->selected = ' selected="selected"';
						} else {
							$row->selected = '';
						}

						$query_results[] = $row;
					}

					Services::Registry()->set('Trigger', 'list_'. $requested_listname, $query_results);

					/** Store the name of each filter list in an array */
					$row = new \stdClass();
					$row->listname = $requested_listname;
					$lists[] = $row;
				}
			}
		}

		Services::Registry()->set('Trigger', 'GridFilters', $lists);

		return true;
	}

	/**
	 * Create Batch lists and store in Trigger registry, given ACL checks
	 *
	 * @return boolean
	 * @since  1.0
	 */
	protected function setBatch($connect, $primary_prefix)
	{
		$grid_list = Services::Registry()->set('Trigger', 'GridFilters');

		$batch_list = explode(',', 'status,categories,tags,access');

		$lists = array();

		if (is_array($batch_list) && count($batch_list) > 0) {

			/** Build each list and store in registry along with current selection */
			foreach ($batch_list as $list) {

				$fieldValue = Services::Text()->getList($list, $this->parameters);

				if ($fieldValue == false) {
				} else {

					ksort($fieldValue);

					Services::Registry()->set('Trigger', 'batch_' . $list, $fieldValue);

					/** todo: Retrieve selected field from request */
					$selectedValue = '';
					Services::Registry()->set('Trigger', 'batch_' . $list . '_selected', $selectedValue);

					if ($selectedValue == '') {
					} else {
						$connect->model->query->where($connect->model->db->qn($primary_prefix)
							. '.' . $connect->model->db->qn($list)
							. ' = ' . $connect->model->db->q($selectedValue));
					}

					/** Store the name of each filter list in an array */
					$lists[] = strtolower($list);
				}
			}
		}

		Services::Registry()->set('Trigger', 'GridBatch', $lists);

		return true;
	}

	/**
	 * Create Grid Query and save results in Trigger registry
	 *
	 * @param   $connect
	 * @param   $primary_prefix
	 * @param   $table_name
	 *
	 * @return  bool
	 * @since   1.0
	 */
	protected function setGrid($connect, $primary_prefix, $table_name)
	{
		/** Select */
		$grid_columns = explode(',', $this->get('grid_columns',
				'id,featured,title,created_by,start_publishing_datetime,ordering')
		);
		Services::Registry()->set('Trigger', 'GridTableColumns', $grid_columns);
		foreach ($grid_columns as $column) {
			$connect->model->query->select(
				$connect->model->db->qn($primary_prefix) . '.' . $connect->model->db->qn($column)
			);
		}

		/** From */
		$connect->model->query->from($connect->model->db->qn($table_name)
			. ' as ' . $connect->model->db->qn($primary_prefix));

		/** Where (filter values already set) */
		$connect->model->query->where($connect->model->db->qn('a.catalog_type_id')
			. ' = ' . $this->get('menuitem_source_catalog_type_id'));

		/** Ordering */
		$ordering = $this->get('grid_ordering', 'start_publishing_datetime');
		Services::Registry()->set('Trigger', 'GridTableOrdering', $ordering);
		$connect->model->query->order($connect->model->db->qn($ordering));

		/** Run the query and store results */
		$connect->model->db->setQuery(
			$connect->model->query->__toString(),
			$this->get('grid_offset', 0),
			$this->get('grid_count', 5)
		);

		$query_results = $connect->model->db->loadObjectList();

		Services::Registry()->set('Trigger', 'GridQueryResults', $query_results);

		/** Set Model Properties for use with Template View */
		Services::Registry()->set('Parameters', 'model_name', 'Triggerdata');
		Services::Registry()->set('Parameters', 'model_type', 'dbo');
		Services::Registry()->set('Parameters', 'model_query_object', 'getTriggerdata');

		Services::Registry()->set('Parameters', 'model_parameter', 'GridQueryResults');

		return true;
	}

	/**
	 * Create Pagination data and store in Trigger registry
	 *
	 * @return boolean
	 * @since  1.0
	 */
	protected function setPagination($url, $connector)
	{
		$query_results = array();
		$current = 0;

		$row = new \stdClass();
		$row->link = $url . $connector . '&start=' . $current + 5;
		$row->class = ' page-prev';
		$row->link_text = ' 1';

		$query_results[] = $row;

		$row = new \stdClass();
		$row->link = $url . $connector . '&start=' . $current + 10;
		$row->class = '';
		$row->link_text = ' 2';

		$query_results[] = $row;

		$row = new \stdClass();
		$row->link = $url . $connector . '&start=' . $current + 15;
		$row->class = ' page-next';
		$row->link_text = ' 3';

		$query_results[] = $row;

		Services::Registry()->set('Trigger', 'GridPagination', $query_results);
		Services::Registry()->set('Trigger', 'GridPaginationOffset', $this->get('grid_offset', 0));
		Services::Registry()->set('Trigger', 'GridPaginationCount', $this->get('grid_count', 5));

		return true;
	}
}
