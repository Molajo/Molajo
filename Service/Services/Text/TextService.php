<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Text;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Text
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class TextService
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
			self::$instance = new TextService();
		}

		return self::$instance;
	}

	/**
	 * Add rows to model
	 *
	 * @param $extension_name
	 * @param $model_name
	 * @param $source_path
	 * @param $destination_path
	 *
	 * @return bool
	 * @since  1.0
	 */
	public function extension($model_name, $source_path = null, $destination_path = null)
	{
		$controller = new CreateController();

		$table_registry_name = ucfirst(strtolower($model_name)) . 'Table';

		$data = new \stdClass();
		$data->title = $model_name;
		$data->model_name = $model_name;

		$controller->data = $data;

		$id = $controller->execute();
		if ($id === false) {
			//install failed
			return false;
		}
	}

	/**
	 * Retrieves Lorem Ipsum text
	 *
	 * Usage:
	 * Services::Text()->getPlaceHolderText(4, 20, 'html', 1);
	 *
	 * @param int  $paragraph_word_count - number of words per paragraph
	 * @param int  $paragraph_count
	 * @param char $format               txt, plain, html
	 * @param  $start_with_lorem_ipsum 0 or 1
	 *
	 * @return string
	 * @since   1.0
	 */
	public function getPlaceHolderText($paragraph_word_count, $paragraph_count, $format,
									   $start_with_lorem_ipsum)
	{
		/**
		$generator = new LoremIpsumGenerator($paragraph_word_count);

		return ucfirst(
			$generator->getContent(
				$paragraph_word_count * $paragraph_count,
				$format,
				$start_with_lorem_ipsum
			)
		);
		 */
	}

	/**
	 * getList retrieves values called from listsPlugin
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function getList($filter, $parameters)
	{
		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$m = new $controllerClass();

		$results = $m->connect('Datalist', $filter);
		if ($results == false) {
			return false;
		}

		$values = Services::Registry()->get('StatusDatalist', 'values');
		if (is_array($values) && count($values) > 0) {
			$query_results = $values;

		} else {
			$query_results = $this->getQueryResults($m, $filter, $parameters);
		}

		return $query_results;
	}

	/**
	 * getQueryResults for list
	 *
	 * @param   $m
	 * @param   $filter
	 * @param   $parameters
	 *
	 * @return  object
	 * @since   1.0
	 */
	protected function getQueryResults($m, $filter, $parameters)
	{
		$primary_prefix = $m->get('primary_prefix');
		$primary_key = $m->get('primary_key');
		$name_key = $m->get('name_key');

		$m->model->set('model_offset', 0);
		$m->model->set('model_count', 999999);

		/** Select */
		$fields = Services::Registry()->get($filter . 'Datalist', 'Fields');

		$first = true;

		if (count($fields) < 2) {

			$m->model->query->select('DISTINCT '
				. $m->model->db->qn($primary_prefix . '.' . $primary_key) . ' as id');
			$m->model->query->select($m->model->db->qn($primary_prefix
				. '.' . $name_key) . ' as value');
			$m->model->query->order($m->model->db->qn($primary_prefix
				. '.' . $name_key) . ' ASC');

		} else {

			$ordering = '';
			foreach ($fields as $field) {

				if (isset($field['alias'])) {
					$alias = $field['alias'];
				} else {
					$alias = $primary_prefix;
				}

				$name = $field['name'];

				if ($first) {
					$first = false;
					$as = 'id';
					$distinct = 'DISTINCT';
				} else {
					$as = 'value';
					$distinct = '';
					$ordering = $alias . '.' . $name;
				}

				$m->model->query->select($distinct . ' ' . $m->model->db->qn($alias . '.' . $name) . ' as ' . $as);
			}

			$m->model->query->order($m->model->db->qn($ordering) . ' ASC');
		}

		/** Where */
		if (strtolower($parameters['template_view_path_node']) == 'admindashboard') {
			$m->model->query->where($m->model->db->qn('extensions.name')
				. ' LIKE ' . $m->model->db->q('Portlet%'));
		}

		$this->setWhereCriteria('catalog_type_id',
			$parameters['criteria_catalog_type_id'],
			$primary_prefix,
			$m
		);

		$this->setWhereCriteria('status',
			$parameters['criteria_status'],
			$primary_prefix,
			$m
		);

		$this->setWhereCriteria('extension_instance_id',
			$m->get('criteria_extension_instance_id'),
			$primary_prefix,
			$m
		);

		$menu_id = null;
		if (isset($parameters['criteria_catalog_type_id'])
			&& (int)$parameters['criteria_catalog_type_id'] == 1300
		) {
			$this->setWhereCriteria('menu_id',
				$m->get('item_parent_menu_id'),
				$primary_prefix,
				$m
			);
		}  else {

			$catalog_type_id = $m->get('criteria_catalog_type_id');
			if ((int)$catalog_type_id > 0
				|| strrpos($catalog_type_id, ',') > 0
			) {
				$this->setWhereCriteria(
					'catalog_type_id',
					$catalog_type_id,
					$primary_prefix,
					$m
				);
			}
		}

		$query_object = 'distinct';

		$offset = $m->set('model_offset', 0);
		$count = $m->set('model_count', 9999999);

		return $m->getData($query_object);
	}

	/**
	 * setWhereCriteria
	 *
	 * @param $field
	 * @param $value
	 * @param $alias
	 * @param $connection
	 *
	 * @return void
	 * @since  1.0
	 */
	protected function setWhereCriteria($field, $value, $alias, $connection)
	{

		if (strrpos($value, ',') > 0) {
			$connection->model->query->where(
				$connection->model->db->qn($alias . '.' . $field)
					. ' IN (' . $value . ')'
			);

		} elseif ((int)$value == 0) {

		} else {
			$connection->model->query->where(
				$connection->model->db->qn($alias . '.' . $field) . ' = ' . (int)$value
			);
		}

		return;
	}

	/**
	 * add publishedStatus information to list query
	 *
	 * @return void
	 * @since   1.0
	 */
	protected function publishedStatus($m)
	{
		$primary_prefix = Services::Registry()->get($m->table_registry_name, 'primary_prefix', 'a');

		$m->model->query->where($m->model->db->qn($primary_prefix)
			. '.' . $m->model->db->qn('status')
			. ' > ' . STATUS_UNPUBLISHED);

		$m->model->query->where('(' . $m->model->db->qn($primary_prefix)
				. '.' . $m->model->db->qn('start_publishing_datetime')
				. ' = ' . $m->model->db->q($m->model->null_date)
				. ' OR ' . $m->model->db->qn($primary_prefix) . '.' . $m->model->db->qn('start_publishing_datetime')
				. ' <= ' . $m->model->db->q($m->model->now) . ')'
		);

		$m->model->query->where('(' . $m->model->db->qn($primary_prefix)
				. '.' . $m->model->db->qn('stop_publishing_datetime')
				. ' = ' . $m->model->db->q($m->model->null_date)
				. ' OR ' . $m->model->db->qn($primary_prefix) . '.' . $m->model->db->qn('stop_publishing_datetime')
				. ' >= ' . $m->model->db->q($m->model->now) . ')'
		);

		return;
	}

	/**
	 * buildSelectlist - build select list for insertion into webpage
	 *
	 * @param  $listname
	 * @param  $items
	 * @param int $multiple
	 * @param int $size
	 *
	 * @return array
	 * @since   1.0
	 */
	public function buildSelectlist($listname, $items, $multiple = 0, $size = 5)
	{
		ksort($items);

		/** todo: Retrieve selected field from request */
		$selected = '';

		$query_results = array();
		foreach ($items as $item) {

			$row = new \stdClass();

			$row->listname = $listname;

			$row->id = $item->id;
			$row->value = $item->value;

			if ($row->id == $selected) {
				$row->selected = ' selected ';
			} else {
				$row->selected = '';
			}

			$row->multiple = '';

			if ($multiple == 1) {
				$row->multiple = ' multiple ';
				if ((int)$size == 0) {
					$row->multiple .= 'size=5 ';
				} else {
					$row->multiple .= 'size=' . (int)$size;
				}
			}

			$query_results[] = $row;
		}

		return $query_results;
	}

	/**
	 *     Dummy functions to pass service off as a DBO to interact with model
	 */
	public function get($option = null)
	{
		if ($option == 'db') {
			return $this;
		}
	}

	public function getNullDate()
	{
		return $this;
	}

	public function getQuery()
	{
		return $this;
	}

	public function toSql()
	{
		return $this;
	}

	public function clear()
	{
		return $this;
	}

	/**
	 * getData - simulates DBO - interacts with the Model getTextlist method
	 *
	 * @param $registry
	 * @param $element
	 * @param $single_result
	 *
	 * @return array
	 * @since    1.0
	 */
	public function getData($list)
	{
		$query_results = array();

		/** Return results to Model */

		return $query_results;
	}
}
