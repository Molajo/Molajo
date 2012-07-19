<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Trigger\Pagination;

use Molajo\Service\Services;
use Molajo\Extension\Trigger\Content\ContentTrigger;

defined('MOLAJO') or die;

/**
 * Pagination
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class PaginationTrigger extends ContentTrigger
{
    /**
     * After reading, calculate pagination data
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
		if ($this->get('first') == true) {
		} else {
			return true;
		}

		//todo: move model_use_pagination into model - work around for now - grab just read parameter
		if (Services::Registry()->get('Parameters', 'model_use_pagination') == 1) {
		} else {
			return true;
		}

		echo '<pre>';
		var_dump($this->parameters);
		echo '</pre>';
		die;
	}

	/**
	 * Pagination for List Pages
	 *
	 * @return bool
	 */
	protected function listPagination()
	{
		if ((int) $this->get('pagination_total') > 1) {
		} else {
			return true;
		}

		if ((int) $this->get('model_count') > 0) {
		} else {
			$this->set('model_count', 5);
		}

		if ((int) $this->get('model_offset') > 1) {
		} else {
			$this->set('model_offset', 0);
		}

		if ($this->get('model_offset') + $this->get('model_count') >= $this->get('pagination_total')) {
			return true;
		}

		/** Next offset */
		if ($this->get('model_offset') + $this->get('model_count') > $this->get('pagination_total')) {
			$next_offset = 0;
		} else {
			$next_offset = $this->get('model_offset') + $this->get('model_count');
		}

		$current_offset = $this->get('model_offset');

		/** Prev offset */
		if ($this->get('model_offset') - $this->get('model_count') < 0) {
			$prev_offset = null;
		} else {
			$prev_offset = $this->get('model_offset') - $this->get('model_count');
		}

		/** Pages */
		$url = Services::Registry()->get('Triggerdata', 'full_page_url');

		$connector = '/';
		$query_results = array();
		$offset = 0;

		$iteration = round($this->get('pagination_total') / $this->get('model_count', 5), 0);

		for ($i = 0; $i < $iteration; $i++) {

			$row = new \stdClass();

			$row->link = $url . $connector . 'offset=' . $offset;

			if ($offset < $this->get('model_offset')) {
				$row->class = ' page-prev';
			} elseif ($offset == $this->get('model_offset')) {
				$row->class = ' page-current';
			} else {
				$row->class = ' page-next';
			}

			$row->link_text = ' ' . (int) $i + 1;

			$row->prev_link = $url . '/offset=' . $prev_offset;
			$row->next_link = $url . '/offset=' . $next_offset;

			$offset = $offset + $this->get('model_count');

			$query_results[] = $row;
		}

		Services::Registry()->set('Triggerdata', 'AdminGridPagination', $query_results);

		return true;
	}
}
