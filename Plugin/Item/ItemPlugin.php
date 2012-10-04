<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Item;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;
use Molajo\Helpers;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class ItemPlugin extends Plugin
{
	/**
	 * Prepares data for Item
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeParse()
	{
		if (APPLICATION_ID == 2) {
		} else {
			return true;
		}

		if (strtolower($this->get('template_view_path_node')) == 'item') {
		} else {
			return true;
		}

		$resource_table_registry = ucfirst(strtolower($this->get('model_name')))
			. ucfirst(strtolower($this->get('model_type')));

		/** Get Actual Data for matching to Fields */
		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$connect = new $controllerClass();
		$results = $connect->connect($this->get('model_type'), $this->get('model_name'));
		if ($results === false) {
			return false;
		}

		$connect->set('get_customfields', 1);
		$connect->set('use_special_joins', 1);
		$connect->set('process_plugins', 1);
		$primary_prefix = $connect->get('primary_prefix');
		$primary_key = $connect->get('primary_key');
		$id = $this->get('content_id');

		$connect->model->query->where($connect->model->db->qn($primary_prefix)
				. '.' . $connect->model->db->qn($primary_key) . ' = ' . (int) $id);

		$item = $connect->getData('item');

		$this->set('model_name', 'Plugindata');
		$this->set('model_type', 'dbo');
		$this->set('model_query_object', 'getPlugindata');
		$this->set('model_parameter', 'PrimaryRequestQueryResults');

		$this->parameters['model_name'] = 'Plugindata';
		$this->parameters['model_type'] = 'dbo';

		return true;
	}
}
