<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Comments;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class CommentsPlugin extends Plugin
{
	/**
	 * Retrieve Comments for Resource
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterReadall()
	{
		if (strtolower($this->get('template_view_path_node')) == 'comments') {
		} else {
			return true;
		}

		/** Identify Table Registry */
		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$connect = new $controllerClass();
		$results = $connect->connect('Table', 'CatalogTypes');
		if ($results === false) {
			return false;
		}

		$catalog_type = (int) $this->get('comment_for_catalog_type_id', 0);
		if ((int) $catalog_type === 0) {
			$catalog_type = (int) Services::Registry()->get('RouteParameters', 'catalog_type_id');
		}

		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$connect = new $controllerClass();
		$results = $connect->connect('Resource', 'Comments');
		if ($results === false) {
			return false;
		}

		$connect->set('get_customfields', 2);
		$connect->set('use_special_joins', 1);
		$connect->set('check_view_level_access', 1);

		$primary_prefix = $connect->get('primary_prefix');

		$source_id = (int) $this->get('comment_for_source_id', 0);
		if ((int) $source_id === 0) {
			$source_id = (int) Services::Registry()->get('RouteParameters', 'content_id');
		}

		$connect->set('root', (int) $source_id);

		$connect->model->query->where(
			$connect->model->db->qn($primary_prefix)
				. '.' . $connect->model->db->qn('root')
				. ' = ' . (int) $source_id
		);
		$connect->model->query->order(
			$connect->model->db->qn($primary_prefix)
				. '.' . $connect->model->db->qn('lft')
		);

		$this->data = $connect->getData('list');

		return true;
	}
}
