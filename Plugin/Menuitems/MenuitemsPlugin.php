<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Menuitems;

use Molajo\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class MenuitemsPlugin extends ContentPlugin
{
	/**
	 * Generates list of Menus and Menuitems for use in Datalists
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRoute()
	{
		if (APPLICATION_ID == 2) {
		} else {
			return true;
		}

		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$connect = new $controllerClass();

		$results = $connect->connect('System', 'Menuitems');
		if ($results === false) {
			return false;
		}

		$connect->set('get_customfields', 0);
		$connect->set('use_special_joins', 0);
		$connect->set('process_plugins', 0);
		$connect->set('check_view_level_access', 0);

		$connect->model->query->select(
			$connect->model->db->qn($connect->get('primary_prefix'))
				. '.' . $connect->model->db->qn('title')
		);
		$connect->model->query->select(
			$connect->model->db->qn($connect->get('primary_prefix'))
				. '.' . $connect->model->db->qn('id')
		);
		$connect->model->query->select(
			$connect->model->db->qn($connect->get('primary_prefix'))
				. '.' . $connect->model->db->qn('lvl')
		);

		$connect->model->query->where(
			$connect->model->db->qn($connect->get('primary_prefix'))
				. '.' . $connect->model->db->qn('status')
				. ' IN (0,1,2)'
		);

		$connect->model->query->order(
			$connect->model->db->qn($connect->get('primary_prefix'))
				. '.' . $connect->model->db->qn('root') . ', '
				. $connect->model->db->qn($connect->get('primary_prefix'))
				. '.' . $connect->model->db->qn('lft')
		);

		$connect->set('model_offset', 0);
		$connect->set('model_count', 99999);

		$query_results = $connect->getData('list');

		$menuitems = array();
		foreach ($query_results as $item) {
			$row = new \stdClass();

			$name = $item->title;
			$lvl = (int) $item->lvl - 1;

			if ($lvl > 0) {
				for($i = 0; $i < $lvl; $i++) {
					$name = ' ..' . $name;
				}
			}

			$row->id = $item->id;
			$row->value = trim($name);

			$menuitems[] = $row;
		}

		Services::Registry()->set('Datalist', 'Menuitems', $menuitems);

		return true;
	}
}
