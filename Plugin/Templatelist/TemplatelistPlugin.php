<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Templatelist;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class TemplatelistPlugin extends Plugin
{
    /**
     * Prepares data for the Administrator Grid  - run TemplatelistPlugin after AdminmenuPlugin
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterReadAll()
    {
        if (strtolower($this->get('template_view_path_node')) == 'list') {
        } else {
            return true;
        }

		if (isset($this->parameters['list_model_name'])) {
		} else {
			return false;
		}
		$model_name = $this->parameters['list_model_name'];

		if (isset($this->parameters['list_model_type'])) {
			$model_type = $this->parameters['list_model_type'];
		} else {
			$model_type = 'Resource';
		}
		if ($model_type == '')  {
			$model_type = 'Resource';
		}

        $controllerClass = 'Molajo\\MVC\\Controller\\Controller';
        $connect = new $controllerClass();

        $results = $connect->connect($model_type, $model_name);
        if ($results === false) {
            return false;
        }

		$primary_prefix = $connect->get('primary_prefix', 'a');

		if (isset($this->parameters['list_ordering'])) {
			$ordering = $this->parameters['list_ordering'];
		} else {
			$ordering = '';
		}
		if (isset($this->parameters['list_model_ordering_direction'])) {
			$direction = $this->parameters['list_model_ordering_direction'];
		} else {
			$direction = 'ASC';
		}

        if ($ordering == '' || $ordering === null) {
        } else {
			if ($direction == '' || $direction === null) {
				$connect->model->query->order($connect->model->db->qn($ordering));
			} else {
				$connect->model->query->order($connect->model->db->qn($ordering)
					. ' ' . $connect->model->db->qn($direction));
			}
		}

		if (isset($this->parameters['list_model_offset'])) {
			$offset = $this->parameters['list_model_offset'];
		} else {
			$offset = 0;
		}

		if (isset($this->parameters['list_model_count'])) {
			$count = $this->parameters['list_model_count'];
		} else {
			$count = 0;
		}
		if ($count == 0) {
			if (isset($this->parameters['list_model_use_pagination'])) {
				$pagination = $this->parameters['list_model_use_pagination'];
			} else {
				$pagination = 0;
			}
		} else {
			$pagination = 1;
		}

		if ($pagination == 1) {
		} else {
			$pagination = 0;
		}

        $connect->set('model_offset', $offset);
        $connect->set('model_count', $count);
		$connect->set('use_pagination', $pagination);

        $this->data = $connect->getData('list');

        return true;
    }
}
