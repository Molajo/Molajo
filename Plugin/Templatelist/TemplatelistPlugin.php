<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
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
     * @return  boolean
     * @since   1.0
     */
    public function onAfterReadAll()
    {
        if (strtolower($this->get('template_view_path_node', '', 'parameters')) == QUERY_OBJECT_LIST) {
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
            $model_type = CATALOG_TYPE_RESOURCE_LITERAL;
        }
        if ($model_type == '') {
            $model_type = CATALOG_TYPE_RESOURCE_LITERAL;
        }

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry($model_type, $model_name);
        $controller->setDataobject();
        $controller->connectDatabase();

        $primary_prefix = $controller->get('primary_prefix', 'a', 'model_registry');

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
                $controller->model->query->order($controller->model->db->qn($ordering));
            } else {
                $controller->model->query->order(
                    $controller->model->db->qn($ordering)
                        . ' ' . $controller->model->db->qn($direction)
                );
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

        $controller->set('model_offset', $offset);
        $controller->set('model_count', $count);
        $controller->set('use_pagination', $pagination);

        $this->data = $controller->getData(QUERY_OBJECT_LIST);

        return true;
    }
}
