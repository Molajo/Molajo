<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Pagination;

use Molajo\Service\Services;
use Molajo\Plugin\Plugin\Plugin;

defined('MOLAJO') or die;

/**
 * Pagination
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class PaginationPlugin extends Plugin
{
	/**
	 * After reading, calculate pagination data
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterReadall()
	{

		if ((int)$this->get('use_pagination') > 0) {
		} else {
			return true;
		}

		/** initialise */
		$url = Services::Registry()->get('Plugindata', 'page_url');
		$query_results = array();

		/** pagination_total: number of items */
		if ((int)$this->get('pagination_total') > 1) {
		} else {
			return true;
		}

		/** model_count: max number of rows to display per page */
		if ((int)$this->get('model_count') > 0) {
		} else {
			$this->set('model_count', 10);
		}

		/** model_offset: offset of 0 means skip 0 rows, then start with row 1 */
		if ((int)$this->get('model_offset') > 1) {
		} else {
			$this->set('model_offset', 0);
		}

		/** current_page */
		$current_page = ($this->get('model_offset') / $this->get('model_count')) + 1;
		if ($this->get('model_offset') % $this->get('model_count')) {
			$current_page++;
		}

		/** previous page */
		if ((int) $current_page > 1) {
			$previous_page = (int) $current_page - 1;
			$prev_link = $url . '/page/' . (int) $previous_page;
		} else {
			$previous_page = 0;
			$prev_link = '';
		}

		/** total pages */
		$total_pages = (int)$this->get('pagination_total') / (int) $this->get('model_count');

		if ((int)$this->get('pagination_total') % $this->get('model_count') > 0) {
			$total_pages++;
		}

		/** next page */
		if ((int) $total_pages > (int) $current_page) {
			$next_page = $current_page + 1;
			$next_link = $url . '/page/' . $next_page;
		} else {
			$next_page = 0;
			$next_link = '';
		}

		/** first and last pages */
		$first_page = 1;
		$first_link = $url . '/page/' . 1;

		$last_page = (int) $total_pages;
		$last_link = $url . '/page/' . (int) $total_pages;

		/** Paging */
		$row = new \stdClass();

		$row->total_items = (int)$this->get('pagination_total');
		$row->total_items_per_page = (int)$this->get('model_count');

		$row->first_page = $first_page;
		$row->first_link = $first_link;

		$row->previous_page = $previous_page;
		$row->prev_link = $prev_link;

		$row->next_page = $next_page;
		$row->next_link = $next_link;

		$row->last_page = $last_page;
		$row->last_link = $last_link;

		$query_results[] = $row;

		Services::Registry()->set('Plugindata', 'Paging', $query_results);

		/** Paging */
		$query_results = array();
		if ($total_pages > 10) {
			$total_pages = 10;
		}
		for ($i = 1; $i < $total_pages; $i++) {

			$row = new \stdClass();

			$row->total_items = (int)$this->get('pagination_total');
			$row->total_items_per_page = (int)$this->get('model_count');

			$row->first_page = $first_page;
			$row->first_link = $first_link;

			$row->previous_page = $previous_page;
			$row->prev_link = $prev_link;

			if ($i == $current_page) {
				$row->current = 1;
			} else {
				$row->current = 0;
			}

			$row->link = $url . '/page/' . $i;
			$row->link_text = ' ' . (int) $i;

			$row->next_page = $next_page;
			$row->next_link = $next_link;

			$row->last_page = $last_page;
			$row->last_link = $last_link;

			$query_results[] = $row;
		}

		Services::Registry()->set('Plugindata', 'Pagination', $query_results);

		return true;
	}

	/**
	 * Prev and Next Pagination for Item Pages
	 *
	 * @return bool
	 */
	protected function itemPaging()
	{
		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$connect = new $controllerClass();

		$results = $connect->connect(
			$this->get('model_type', 'Table'),
			$this->get('model_name')
		);
		if ($results === false) {
			return false;
		}

		$connect->set('get_customfields', 0);
		$connect->set('use_special_joins', 0);
		$connect->set('process_plugins', 0);
		$connect->set('get_item_children', 0);

		$connect->model->query->select($connect->model->db->qn('a')
			. '.' . $connect->model->db->qn($connect->get('primary_key', 'id')));

		$connect->model->query->select($connect->model->db->qn('a')
			. '.' . $connect->model->db->qn($connect->get('name_key', 'title')));

		$connect->model->query->where($connect->model->db->qn('a')
			. '.' . $connect->model->db->qn($connect->get('primary_key', 'id')
			. ' = ' . (int)$this->parameters['catalog_source_id']));

//todo ordering
		$item = $connect->getData('item');

		$this->table_registry_name = ucfirst(strtolower($this->get('model_name')))
			. ucfirst(strtolower($this->get('model_type', 'Table')));

		if ($item === false || count($item) == 0) {
			return false;
		}
	}
}
