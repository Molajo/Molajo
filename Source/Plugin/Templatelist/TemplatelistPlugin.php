<?php
/**
 * Templatelist Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Templatelist;

use CommonApi\Event\ReadInterface;
use Molajo\Plugin\ReadEventPlugin;

/**
 * Templatelist Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class TemplatelistPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * Prepares data for the Administrator Grid  - run TemplatelistPlugin after AdminmenuPlugin
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterReadAll()
    {
        if (isset($this->runtime_data->route)) {
        } else {
            return $this;
        }

        if (strtolower($this->get('template_view_path_node', '', 'runtime_data')) == 'list') {
        } else {
            return $this;
        }

        if (isset($this->runtime_data->list_model_name)) {
        } else {
            return $this;
        }
        $model_name = $this->runtime_data->list_model_name;

        if (isset($this->runtime_data->list_model_type)) {
            $model_type = $this->runtime_data->list_model_type;
        } else {
            $model_type = CATALOG_TYPE_RESOURCE_LITERAL;
        }
        if ($model_type == '') {
            $model_type = CATALOG_TYPE_RESOURCE_LITERAL;
        }

        $controller_class_namespace = $this->controller_namespace;
        $controller                 = new $controller_class_namespace();
        $controller->getModelRegistry($model_type, $model_name, 1);

        $primary_prefix = $controller->get('primary_prefix', 'a');

        if (isset($this->runtime_data->list_ordering)) {
            $ordering = $this->runtime_data->list_ordering;
        } else {
            $ordering = '';
        }
        if (isset($this->runtime_data->list_model_ordering_direction)) {
            $direction = $this->runtime_data->list_model_ordering_direction;
        } else {
            $direction = 'ASC';
        }

        if ($ordering == '' || $ordering === null) {
        } else {
            if ($direction == '' || $direction === null) {
                $controller->model->query->order($controller->model->database->qn($ordering));
            } else {
                $controller->model->query->order(
                    $controller->model->database->qn($ordering)
                    . ' ' . $controller->model->database->qn($direction)
                );
            }
        }

        if (isset($this->runtime_data->list_model_offset)) {
            $offset = $this->runtime_data->list_model_offset;
        } else {
            $offset = 0;
        }

        if (isset($this->runtime_data->list_model_count)) {
            $count = $this->runtime_data->list_model_count;
        } else {
            $count = 0;
        }
        if ($count == 0) {
            if (isset($this->runtime_data->list_model_use_pagination)) {
                $pagination = $this->runtime_data->list_model_use_pagination;
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

        $this->query_results = $controller->getData('list');

        return $this;
    }
}
