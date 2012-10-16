<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Comment;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class CommentPlugin extends Plugin
{
	/**
	 * Retrieve Comment for Resource
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterReadAll()
	{
		if (strtolower($this->get('template_view_path_node')) == 'comment') {
		} else {
			return true;
		}

		/** Retrieve Parent Parameter Data */
		$model_type = $this->get('parent_model_type');
		$model_name = $this->get('parent_model_name');
		$source_id = $this->get('parent_source_id');

		/** Identify Table Registry */
		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$connect = new $controllerClass();
		$results = $connect->connect('Resource', 'Comments');
		if ($results === false) {
			return false;
		}

		$connect->set('get_customfields', 0);
		$connect->set('use_special_joins', 0);
		$connect->set('check_view_level_access', 0);

		$primary_prefix = $connect->get('primary_prefix');

		$connect->model->query->select('count(*)');
		$connect->model->query->where(
			$connect->model->db->qn($primary_prefix)
				. '.' . $connect->model->db->qn('root')
				. ' = ' . (int) $source_id
		);

		$count = $connect->getData('result');

		$results = array();
		$row = new \stdClass();
		$row->count_of_comments = $count;

//todo: add comments for comments closed

		if ($count == 0) {
			$row->title = Services::Language()->translate('COMMENTS_TITLE_NO_COMMENTS');
			$row->content_text = Services::Language()->translate('COMMENTS_TEXT_NO_COMMENTS');
		} else {
			$row->title = Services::Language()->translate('COMMENTS_TITLE_HAS_COMMENTS');
			$row->content_text = Services::Language()->translate('COMMENTS_TEXT_HAS_COMMENTS');
		}

		$results[] = $row;

		$this->data = $results;

		return true;
	}
}
