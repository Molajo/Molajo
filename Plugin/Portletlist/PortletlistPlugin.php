<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Portletlist;

use Molajo\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Portletlist
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class PortletlistPlugin extends ContentPlugin
{

	/**
	 * Retrieves list of data, according to parameters
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterReadall()
	{

		if (strtolower($this->get('template_view_path_node')) == 'portletlist') {
		} else {
			return true;
		}

		$model_name = $this->parameters['criteria_model_name'];
		if ($model_name == '') {
			$model_name = 'Articles';
		}

		$model_type = $this->parameters['criteria_model_type'];
		if ($model_type == '') {
			$model_type = 'Table';
		}

		$criteria_status_list = $this->parameters['criteria_status_list'];

		$ordering = $this->parameters['criteria_ordering'];

		if ($ordering == 'Popular') {
			$ordering = 'a.ordering'; //todo: hits
			$direction = 'ASC';

		} elseif ($ordering == 'Ordering') {
			$ordering = 'a.ordering';
			$direction = 'ASC';

		} elseif ($ordering == 'Stickied') {
			$ordering = 'a.stickied';
			$direction = 'ASC';

		} elseif ($ordering == 'Featured') {
			$ordering = 'a.featured';
			$direction = 'ASC';

		} else {
			$ordering = 'a.start_publishing_datetime';
			$direction = 'DESC';
		}

		$count = $this->parameters['criteria_count'];
		if ((int)$count == 0) {
			$count = 5;
		}

		$get_customfields = $this->parameters['criteria_get_customfields'];
		$use_special_joins = $this->parameters['criteria_use_special_joins'];
		$process_plugins = $this->parameters['criteria_process_plugins'];

		/** Retrieve Data */
		$controllerClass = 'Molajo\\Controller\\Controller';
		$connect = new $controllerClass();
		$results = $connect->connect($model_type, $model_name);
		if ($results == false) {
			return false;
		}

		$connect->set('get_customfields', $get_customfields);
		$connect->set('use_special_joins', $use_special_joins);
		$connect->set('process_plugins', $process_plugins);
		$prefix = $connect->get('primary_prefix', 'a');

		if ($criteria_status_list == '') {
		} else {
			$connect->model->query->where($connect->model->db->qn($prefix . '.' . 'status')
				. ' IN (' . $criteria_status_list . ')');
		}
		$connect->model->query->order($connect->model->db->qn($ordering) . ' ' . $direction);

		$connect->set('model_offset', 0);
		$connect->set('model_count', $count);

		$this->data = $connect->getData('list');

		return true;
	}
}
